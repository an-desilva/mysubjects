<?php

require_once __DIR__ . '/../../config/database.php';

class Quiz {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function createQuiz(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO quizzes (course_id, created_by, title, description, duration_minutes, pass_mark, is_active)
            VALUES (:course_id, :created_by, :title, :description, :duration_minutes, :pass_mark, 1)
        ");
        $stmt->execute([
            'course_id'        => $data['course_id'],
            'created_by'       => $data['created_by'],
            'title'            => $data['title'],
            'description'      => $data['description'] ?? '',
            'duration_minutes' => $data['duration_minutes'] ?? 15,
            'pass_mark'        => $data['pass_mark'] ?? 50
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function addQuestion(int $quizId, string $questionText, int $marks, array $options): bool {
        try {
            $this->db->beginTransaction();

            $stmtQ = $this->db->prepare("
                INSERT INTO quiz_questions (quiz_id, question_text, marks)
                VALUES (:quiz_id, :question_text, :marks)
            ");
            $stmtQ->execute([
                'quiz_id'       => $quizId,
                'question_text' => $questionText,
                'marks'         => $marks
            ]);
            $questionId = (int) $this->db->lastInsertId();

            $stmtO = $this->db->prepare("
                INSERT INTO quiz_options (question_id, option_text, is_correct)
                VALUES (:question_id, :option_text, :is_correct)
            ");
            foreach ($options as $opt) {
                if (empty(trim($opt['text'] ?? ''))) continue;
                $stmtO->execute([
                    'question_id' => $questionId,
                    'option_text' => trim($opt['text']),
                    'is_correct'  => !empty($opt['is_correct']) ? 1 : 0
                ]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Add Question Error: " . $e->getMessage());
            return false;
        }
    }

    public function getAll(): array {
        $stmt = $this->db->prepare("
            SELECT q.*, c.title as course_title, u.name as creator_name,
            (SELECT COUNT(*) FROM quiz_questions qq WHERE qq.quiz_id = q.id) as total_questions
            FROM quizzes q
            JOIN courses c ON q.course_id = c.id
            JOIN users u ON q.created_by = u.id
            ORDER BY q.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByTeacher(int $teacherId): array {
        $stmt = $this->db->prepare("
            SELECT q.*, c.title as course_title,
            (SELECT COUNT(*) FROM quiz_questions qq WHERE qq.quiz_id = q.id) as total_questions
            FROM quizzes q
            JOIN courses c ON q.course_id = c.id
            WHERE q.created_by = :teacher_id
            ORDER BY q.created_at DESC
        ");
        $stmt->execute(['teacher_id' => $teacherId]);
        return $stmt->fetchAll();
    }

    public function getForStudent(int $studentId): array {
        $stmt = $this->db->prepare("
            SELECT q.*, c.title as course_title, c.course_code,
            (SELECT COUNT(*) FROM quiz_questions qq WHERE qq.quiz_id = q.id) as total_questions,
            (SELECT score FROM quiz_attempts qa WHERE qa.quiz_id = q.id AND qa.student_id = :student_id1 AND qa.status = 'completed' ORDER BY qa.id DESC LIMIT 1) as last_score,
            (SELECT status FROM quiz_attempts qa WHERE qa.quiz_id = q.id AND qa.student_id = :student_id2 ORDER BY qa.id DESC LIMIT 1) as attempt_status
            FROM quizzes q
            JOIN courses c ON q.course_id = c.id
            JOIN course_enrollments ce ON c.id = ce.course_id
            WHERE ce.student_id = :student_id3 AND q.is_active = 1
            ORDER BY q.created_at DESC
        ");
        $stmt->execute([
            'student_id1' => $studentId,
            'student_id2' => $studentId,
            'student_id3' => $studentId,
        ]);
        return $stmt->fetchAll();
    }

    public function findByIdWithQuestions(int $quizId): ?array {
        $stmt = $this->db->prepare("
            SELECT q.*, c.title as course_title
            FROM quizzes q
            JOIN courses c ON q.course_id = c.id
            WHERE q.id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $quizId]);
        $quiz = $stmt->fetch();
        if (!$quiz) return null;

        $stmtQ = $this->db->prepare("
            SELECT * FROM quiz_questions WHERE quiz_id = :quiz_id ORDER BY id ASC
        ");
        $stmtQ->execute(['quiz_id' => $quizId]);
        $questions = $stmtQ->fetchAll();

        foreach ($questions as &$q) {
            $stmtO = $this->db->prepare("
                SELECT id, question_id, option_text, is_correct FROM quiz_options WHERE question_id = :q_id ORDER BY id ASC
            ");
            $stmtO->execute(['q_id' => $q['id']]);
            $q['options'] = $stmtO->fetchAll();
        }

        $quiz['questions'] = $questions;
        return $quiz;
    }

    public function getOrCreateAttempt(int $quizId, int $studentId): array {
        $stmt = $this->db->prepare("
            SELECT * FROM quiz_attempts
            WHERE quiz_id = :quiz_id AND student_id = :student_id AND status = 'in_progress'
            ORDER BY id DESC LIMIT 1
        ");
        $stmt->execute(['quiz_id' => $quizId, 'student_id' => $studentId]);
        $attempt = $stmt->fetch();

        if ($attempt) {
            return $attempt;
        }

        // Calculate total marks available
        $stmtM = $this->db->prepare("SELECT COALESCE(SUM(marks), 0) FROM quiz_questions WHERE quiz_id = :quiz_id");
        $stmtM->execute(['quiz_id' => $quizId]);
        $totalMarks = (int) $stmtM->fetchColumn();

        $stmtInsert = $this->db->prepare("
            INSERT INTO quiz_attempts (quiz_id, student_id, total_marks, started_at, status)
            VALUES (:quiz_id, :student_id, :total_marks, NOW(), 'in_progress')
        ");
        $stmtInsert->execute([
            'quiz_id'     => $quizId,
            'student_id'  => $studentId,
            'total_marks' => $totalMarks
        ]);
        $attemptId = (int) $this->db->lastInsertId();

        return $this->getAttemptById($attemptId);
    }

    public function getAttemptById(int $attemptId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM quiz_attempts WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $attemptId]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function submitQuiz(int $attemptId, array $submittedAnswers): array {
        $attempt = $this->getAttemptById($attemptId);
        if (!$attempt || $attempt['status'] === 'completed') {
            return ['success' => false, 'message' => 'Attempt already submitted or invalid.'];
        }

        $quiz = $this->findByIdWithQuestions($attempt['quiz_id']);
        if (!$quiz) {
            return ['success' => false, 'message' => 'Quiz not found.'];
        }

        $totalScore = 0;
        $totalPossible = 0;

        $this->db->beginTransaction();
        try {
            $stmtAns = $this->db->prepare("
                INSERT INTO quiz_answers (attempt_id, question_id, selected_option_id, is_correct)
                VALUES (:attempt_id, :question_id, :selected_option_id, :is_correct)
            ");

            foreach ($quiz['questions'] as $question) {
                $qId = $question['id'];
                $qMarks = (int) $question['marks'];
                $totalPossible += $qMarks;

                $selectedOptionId = isset($submittedAnswers[$qId]) ? (int) $submittedAnswers[$qId] : null;
                $isCorrect = 0;

                if ($selectedOptionId) {
                    $stmtOpt = $this->db->prepare("SELECT is_correct FROM quiz_options WHERE id = :opt_id AND question_id = :q_id");
                    $stmtOpt->execute(['opt_id' => $selectedOptionId, 'q_id' => $qId]);
                    $opt = $stmtOpt->fetch();
                    if ($opt && $opt['is_correct'] == 1) {
                        $isCorrect = 1;
                        $totalScore += $qMarks;
                    }
                }

                $stmtAns->execute([
                    'attempt_id'         => $attemptId,
                    'question_id'        => $qId,
                    'selected_option_id' => $selectedOptionId,
                    'is_correct'         => $isCorrect
                ]);
            }

            $percentage = $totalPossible > 0 ? round(($totalScore / $totalPossible) * 100, 2) : 0;

            $stmtUpd = $this->db->prepare("
                UPDATE quiz_attempts
                SET score = :score, total_marks = :total_marks, submitted_at = NOW(), status = 'completed'
                WHERE id = :id
            ");
            $stmtUpd->execute([
                'score'       => $percentage,
                'total_marks' => $totalPossible,
                'id'          => $attemptId
            ]);

            $this->db->commit();
            return [
                'success'     => true,
                'attempt_id'  => $attemptId,
                'score'       => $percentage,
                'total_marks' => $totalPossible,
                'passed'      => $percentage >= $quiz['pass_mark']
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Quiz Submit Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error auto-grading quiz.'];
        }
    }
}
