<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../Middleware/RoleMiddleware.php';
require_once __DIR__ . '/../Models/Quiz.php';
require_once __DIR__ . '/../Models/Course.php';
require_once __DIR__ . '/../Models/Student.php';

class QuizController {
    private Quiz $quizModel;
    private Course $courseModel;
    private Student $studentModel;

    public function __construct() {
        $this->quizModel = new Quiz();
        $this->courseModel = new Course();
        $this->studentModel = new Student();
    }

    public function teacherIndex(): void {
        RoleMiddleware::handle('teacher', 'admin');
        $user = auth_user();

        if ($user['role'] === 'teacher') {
            $courses = $this->courseModel->getByTeacher($user['id']);
            $quizzes = $this->quizModel->getByTeacher($user['id']);
        } else {
            $courses = $this->courseModel->getAll();
            $quizzes = $this->quizModel->getAll();
        }

        // Selected quiz to add questions
        $selectedQuiz = null;
        if (isset($_GET['id'])) {
            $selectedQuiz = $this->quizModel->findByIdWithQuestions((int) $_GET['id']);
        }

        view('teacher/quizzes', [
            'courses'      => $courses,
            'quizzes'      => $quizzes,
            'selectedQuiz' => $selectedQuiz
        ]);
    }

    public function storeQuiz(): void {
        RoleMiddleware::handle('teacher', 'admin');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
            redirect('/teacher/quizzes');
        }

        $title = sanitize($_POST['title'] ?? '');
        $courseId = (int) ($_POST['course_id'] ?? 0);
        $duration = (int) ($_POST['duration_minutes'] ?? 15);
        $passMark = (int) ($_POST['pass_mark'] ?? 50);
        $description = sanitize($_POST['description'] ?? '');

        if (empty($title) || $courseId <= 0) {
            set_flash('error', 'Quiz title and course selection are required.');
            redirect('/teacher/quizzes');
        }

        $user = auth_user();
        $quizId = $this->quizModel->createQuiz([
            'course_id'        => $courseId,
            'created_by'       => $user['id'],
            'title'            => $title,
            'description'      => $description,
            'duration_minutes' => $duration,
            'pass_mark'        => $passMark
        ]);

        set_flash('success', 'Quiz created successfully! Now add MCQ questions.');
        redirect('/teacher/quizzes?id=' . $quizId);
    }

    public function addQuestion(): void {
        RoleMiddleware::handle('teacher', 'admin');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
            redirect('/teacher/quizzes');
        }

        $quizId = (int) ($_POST['quiz_id'] ?? 0);
        $questionText = sanitize($_POST['question_text'] ?? '');
        $marks = (int) ($_POST['marks'] ?? 1);
        $correctIndex = (int) ($_POST['correct_option'] ?? 0);
        $rawOptions = $_POST['options'] ?? [];

        if ($quizId <= 0 || empty($questionText) || count($rawOptions) < 2) {
            set_flash('error', 'Question text and at least 2 options are required.');
            redirect('/teacher/quizzes?id=' . $quizId);
        }

        $formattedOptions = [];
        foreach ($rawOptions as $idx => $optText) {
            $formattedOptions[] = [
                'text'       => sanitize($optText),
                'is_correct' => ($idx === $correctIndex) ? 1 : 0
            ];
        }

        $ok = $this->quizModel->addQuestion($quizId, $questionText, $marks, $formattedOptions);
        if ($ok) {
            set_flash('success', 'Question added to quiz.');
        } else {
            set_flash('error', 'Failed to add question.');
        }

        redirect('/teacher/quizzes?id=' . $quizId);
    }

    public function studentTake(): void {
        RoleMiddleware::handle('student');
        $user = auth_user();

        $student = $this->studentModel->findByUserId($user['id']);
        if (!$student) {
            set_flash('error', 'Student record not found.');
            redirect('/login');
        }

        $quizId = (int) ($_GET['quiz_id'] ?? 0);

        if ($quizId <= 0) {
            // Show list of quizzes available for student
            $quizzes = $this->quizModel->getForStudent($student['id']);
            view('student/quizzes', ['quizzes' => $quizzes]);
            return;
        }

        $quiz = $this->quizModel->findByIdWithQuestions($quizId);
        if (!$quiz || empty($quiz['questions'])) {
            set_flash('error', 'Quiz not found or contains no questions.');
            redirect('/student/take-quiz');
        }

        $attempt = $this->quizModel->getOrCreateAttempt($quizId, $student['id']);

        view('student/take_quiz', [
            'quiz'    => $quiz,
            'attempt' => $attempt
        ]);
    }

    public function submitQuiz(): void {
        RoleMiddleware::handle('student');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
            redirect('/student/take-quiz');
        }

        $attemptId = (int) ($_POST['attempt_id'] ?? 0);
        $answers = $_POST['answers'] ?? [];

        if ($attemptId <= 0) {
            set_flash('error', 'Invalid quiz attempt.');
            redirect('/student/take-quiz');
        }

        $result = $this->quizModel->submitQuiz($attemptId, $answers);
        if ($result['success']) {
            set_flash('success', "Quiz submitted! Your Score: {$result['score']}%");
            redirect('/student/take-quiz');
        } else {
            set_flash('error', $result['message']);
            redirect('/student/take-quiz');
        }
    }
}
