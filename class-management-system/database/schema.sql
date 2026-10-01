-- Class & Tuition Management System Schema
-- MySQL database schema and initial seed data

CREATE DATABASE IF NOT EXISTS `class_management_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `class_management_db`;

-- 1. Users Table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'teacher', 'student') NOT NULL DEFAULT 'student',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Students Table
CREATE TABLE IF NOT EXISTS `students` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL UNIQUE,
    `student_code` VARCHAR(50) NOT NULL UNIQUE,
    `phone` VARCHAR(20) NULL,
    `parent_phone` VARCHAR(20) NULL,
    `address` TEXT NULL,
    `grade_level` VARCHAR(50) NOT NULL,
    `barcode_path` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Courses Table (Tuition Classes)
CREATE TABLE IF NOT EXISTS `courses` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `teacher_id` INT NOT NULL,
    `course_code` VARCHAR(50) NOT NULL UNIQUE,
    `title` VARCHAR(150) NOT NULL,
    `description` TEXT NULL,
    `monthly_fee` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `schedule_day` VARCHAR(20) NOT NULL,
    `schedule_time` TIME NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`teacher_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Course Enrollments Table
CREATE TABLE IF NOT EXISTS `course_enrollments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT NOT NULL,
    `course_id` INT NOT NULL,
    `enrolled_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_student_course` (`student_id`, `course_id`),
    FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Attendance Table
CREATE TABLE IF NOT EXISTS `attendance` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT NOT NULL,
    `course_id` INT NOT NULL,
    `date` DATE NOT NULL,
    `status` ENUM('present', 'absent', 'late') NOT NULL DEFAULT 'present',
    `marked_by` INT NOT NULL,
    `scanned_code` VARCHAR(50) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_student_course_date` (`student_id`, `course_id`, `date`),
    FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`marked_by`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Payments (Tuition Fees) Table
CREATE TABLE IF NOT EXISTS `payments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `receipt_number` VARCHAR(50) NOT NULL UNIQUE,
    `student_id` INT NOT NULL,
    `course_id` INT NOT NULL,
    `month` VARCHAR(20) NOT NULL, -- Format YYYY-MM
    `amount` DECIMAL(10,2) NOT NULL,
    `payment_date` DATE NOT NULL,
    `payment_method` ENUM('cash', 'card', 'online', 'bank_transfer') NOT NULL DEFAULT 'cash',
    `status` ENUM('paid', 'pending', 'overdue') NOT NULL DEFAULT 'paid',
    `notes` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Materials Table (PDF Notes & Past Papers)
CREATE TABLE IF NOT EXISTS `materials` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `course_id` INT NOT NULL,
    `uploaded_by` INT NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `description` TEXT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `file_size` INT NOT NULL DEFAULT 0,
    `type` ENUM('pdf', 'past_paper', 'notes', 'assignment') NOT NULL DEFAULT 'pdf',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`uploaded_by`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Quizzes Table
CREATE TABLE IF NOT EXISTS `quizzes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `course_id` INT NOT NULL,
    `created_by` INT NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `description` TEXT NULL,
    `duration_minutes` INT NOT NULL DEFAULT 15,
    `pass_mark` INT NOT NULL DEFAULT 50,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Quiz Questions Table
CREATE TABLE IF NOT EXISTS `quiz_questions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `quiz_id` INT NOT NULL,
    `question_text` TEXT NOT NULL,
    `marks` INT NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`quiz_id`) REFERENCES `quizzes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Quiz Options Table
CREATE TABLE IF NOT EXISTS `quiz_options` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `question_id` INT NOT NULL,
    `option_text` TEXT NOT NULL,
    `is_correct` TINYINT(1) NOT NULL DEFAULT 0,
    FOREIGN KEY (`question_id`) REFERENCES `quiz_questions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Quiz Attempts Table
CREATE TABLE IF NOT EXISTS `quiz_attempts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `quiz_id` INT NOT NULL,
    `student_id` INT NOT NULL,
    `score` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `total_marks` INT NOT NULL DEFAULT 0,
    `started_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `submitted_at` TIMESTAMP NULL,
    `status` ENUM('in_progress', 'completed', 'timed_out') NOT NULL DEFAULT 'in_progress',
    FOREIGN KEY (`quiz_id`) REFERENCES `quizzes`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Quiz Answers Table
CREATE TABLE IF NOT EXISTS `quiz_answers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `attempt_id` INT NOT NULL,
    `question_id` INT NOT NULL,
    `selected_option_id` INT NULL,
    `is_correct` TINYINT(1) NOT NULL DEFAULT 0,
    FOREIGN KEY (`attempt_id`) REFERENCES `quiz_attempts`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`question_id`) REFERENCES `quiz_questions`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`selected_option_id`) REFERENCES `quiz_options`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEED DATA
-- Default password for all seed users: password123

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`) VALUES
(1, 'System Administrator', 'admin@tuition.com', '$2y$10$4.TlyS8Vj6W6d7OqJ.cW/.y16H0aP2J7mG/3rT0N/7tQ4X2B2aP1O', 'admin'),
(2, 'Dr. Robert Vance', 'john.doe@tuition.com', '$2y$10$4.TlyS8Vj6W6d7OqJ.cW/.y16H0aP2J7mG/3rT0N/7tQ4X2B2aP1O', 'teacher'),
(3, 'Prof. Sarah Jenkins', 'sarah.maths@tuition.com', '$2y$10$4.TlyS8Vj6W6d7OqJ.cW/.y16H0aP2J7mG/3rT0N/7tQ4X2B2aP1O', 'teacher'),
(4, 'Alex Smith', 'alex.smith@student.com', '$2y$10$4.TlyS8Vj6W6d7OqJ.cW/.y16H0aP2J7mG/3rT0N/7tQ4X2B2aP1O', 'student'),
(5, 'Emma Watson', 'emma.watson@student.com', '$2y$10$4.TlyS8Vj6W6d7OqJ.cW/.y16H0aP2J7mG/3rT0N/7tQ4X2B2aP1O', 'student'),
(6, 'Michael Brown', 'michael.brown@student.com', '$2y$10$4.TlyS8Vj6W6d7OqJ.cW/.y16H0aP2J7mG/3rT0N/7tQ4X2B2aP1O', 'student')
ON DUPLICATE KEY UPDATE `email`=`email`;

INSERT INTO `students` (`id`, `user_id`, `student_code`, `phone`, `parent_phone`, `address`, `grade_level`) VALUES
(1, 4, 'STU-2026-001', '+1 555-0192', '+1 555-0193', '742 Evergreen Terrace, Springfield', 'Grade 11'),
(2, 5, 'STU-2026-002', '+1 555-0284', '+1 555-0285', '123 Baker Street, London', 'Grade 11'),
(3, 6, 'STU-2026-003', '+1 555-0371', '+1 555-0372', '456 Elm Street, Metropolis', 'Grade 12')
ON DUPLICATE KEY UPDATE `student_code`=`student_code`;

INSERT INTO `courses` (`id`, `teacher_id`, `course_code`, `title`, `description`, `monthly_fee`, `schedule_day`, `schedule_time`) VALUES
(1, 2, 'PHY-101', 'Advanced Physics Mechanics & Electricity', 'Comprehensive theory, revision and practical problem solving for Grade 11 & A/L.', 45.00, 'Saturday', '09:00:00'),
(2, 3, 'MAT-201', 'Higher Pure Mathematics & Calculus', 'Advanced calculus, vectors, algebra, and past paper review.', 50.00, 'Sunday', '14:00:00'),
(3, 2, 'SCI-102', 'General Science & Chemistry Fundamentals', 'Foundational concepts in physical science, chemical bonding, and reactions.', 40.00, 'Wednesday', '16:30:00')
ON DUPLICATE KEY UPDATE `course_code`=`course_code`;

INSERT INTO `course_enrollments` (`id`, `student_id`, `course_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 2, 1),
(4, 2, 3),
(5, 3, 2)
ON DUPLICATE KEY UPDATE `student_id`=`student_id`;

INSERT INTO `payments` (`id`, `receipt_number`, `student_id`, `course_id`, `month`, `amount`, `payment_date`, `payment_method`, `status`, `notes`) VALUES
(1, 'REC-202610-001', 1, 1, '2026-10', 45.00, CURRENT_DATE(), 'cash', 'paid', 'October fee paid at counter'),
(2, 'REC-202610-002', 1, 2, '2026-10', 50.00, CURRENT_DATE(), 'online', 'paid', 'Paid via online portal'),
(3, 'REC-202610-003', 2, 1, '2026-10', 45.00, CURRENT_DATE(), 'card', 'paid', 'Counter POS payment'),
(4, 'REC-202609-004', 3, 2, '2026-09', 50.00, '2026-09-15', 'cash', 'paid', 'September fee')
ON DUPLICATE KEY UPDATE `receipt_number`=`receipt_number`;

INSERT INTO `attendance` (`id`, `student_id`, `course_id`, `date`, `status`, `marked_by`, `scanned_code`) VALUES
(1, 1, 1, CURRENT_DATE(), 'present', 1, 'STU-2026-001'),
(2, 2, 1, CURRENT_DATE(), 'present', 1, 'STU-2026-002'),
(3, 1, 2, CURRENT_DATE(), 'late', 2, 'STU-2026-001')
ON DUPLICATE KEY UPDATE `date`=`date`;

INSERT INTO `materials` (`id`, `course_id`, `uploaded_by`, `title`, `description`, `file_path`, `file_size`, `type`) VALUES
(1, 1, 2, 'Physics Mechanics Revision Guide 2026', 'Complete summary notes on kinematics, Newton laws, and work-energy theorem.', 'physics_mechanics_summary.pdf', 1048576, 'notes'),
(2, 1, 2, 'Physics Model Paper 2025 with Answers', 'Model question paper with detailed step-by-step solutions.', 'physics_model_paper_2025.pdf', 2097152, 'past_paper'),
(3, 2, 3, 'Calculus Integration Techniques Cheat Sheet', 'Essential integration formulas, substitution rules, and integration by parts.', 'calculus_cheat_sheet.pdf', 524288, 'pdf')
ON DUPLICATE KEY UPDATE `title`=`title`;

INSERT INTO `quizzes` (`id`, `course_id`, `created_by`, `title`, `description`, `duration_minutes`, `pass_mark`, `is_active`) VALUES
(1, 1, 2, 'Physics Mechanics Quick Check', 'Test your knowledge on Newton laws, acceleration, and friction.', 10, 60, 1),
(2, 2, 3, 'Calculus & Derivatives Assessment', '15 minute quiz covering differentiation rules and chain rule.', 15, 50, 1)
ON DUPLICATE KEY UPDATE `title`=`title`;

INSERT INTO `quiz_questions` (`id`, `quiz_id`, `question_text`, `marks`) VALUES
(1, 1, 'What is the SI unit of force?', 1),
(2, 1, 'Which of Newton\'s laws of motion states that for every action there is an equal and opposite reaction?', 1),
(3, 1, 'What is the acceleration due to gravity on Earth near sea level approximately?', 1),
(4, 2, 'What is the derivative of f(x) = x^3 with respect to x?', 1),
(5, 2, 'What is the derivative of sin(x)?', 1)
ON DUPLICATE KEY UPDATE `question_text`=`question_text`;

INSERT INTO `quiz_options` (`id`, `question_id`, `option_text`, `is_correct`) VALUES
(1, 1, 'Joule (J)', 0),
(2, 1, 'Newton (N)', 1),
(3, 1, 'Watt (W)', 0),
(4, 1, 'Pascal (Pa)', 0),

(5, 2, 'First Law', 0),
(6, 2, 'Second Law', 0),
(7, 2, 'Third Law', 1),
(8, 2, 'Law of Gravitation', 0),

(9, 3, '9.81 m/s²', 1),
(10, 3, '5.0 m/s²', 0),
(11, 3, '12.4 m/s²', 0),
(12, 3, '1.62 m/s²', 0),

(13, 4, '3x', 0),
(14, 4, '3x²', 1),
(15, 4, 'x²', 0),
(16, 4, '6x', 0),

(17, 5, 'cos(x)', 1),
(18, 5, '-cos(x)', 0),
(19, 5, 'tan(x)', 0),
(20, 5, '-sin(x)', 0)
ON DUPLICATE KEY UPDATE `option_text`=`option_text`;
