-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 06, 2025 at 07:38 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `srms_bishal_rohan`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_announcements`
--

CREATE TABLE `tbl_announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(90) NOT NULL,
  `announcement` longtext NOT NULL,
  `create_date` datetime NOT NULL,
  `level` int(11) NOT NULL COMMENT '0 = Teachers, 1 = Student, 2 = Both'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_announcements`
--

INSERT INTO `tbl_announcements` (`id`, `title`, `announcement`, `create_date`, `level`) VALUES
(1, 'Results are out now', 'Results are out now', '2025-07-22 12:38:54', 2);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_classes`
--

CREATE TABLE `tbl_classes` (
  `id` int(11) NOT NULL,
  `name` varchar(90) NOT NULL,
  `registration_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_classes`
--

INSERT INTO `tbl_classes` (`id`, `name`, `registration_date`) VALUES
(1, 'Twelve (Management)', '2025-07-22 07:56:42'),
(2, 'Eleven(Management)', '2025-08-06 17:34:19');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_exam_results`
--

CREATE TABLE `tbl_exam_results` (
  `id` int(11) NOT NULL,
  `student` varchar(20) NOT NULL,
  `class` int(11) NOT NULL,
  `subject_combination` int(11) NOT NULL,
  `term` int(11) NOT NULL,
  `score` double NOT NULL DEFAULT 0,
  `theory_marks` double NOT NULL DEFAULT 0 COMMENT 'Theory/External marks (75 for practical subjects, 100 for theory-only)',
  `internal_marks` double NOT NULL DEFAULT 0 COMMENT 'Internal/Practical marks (25 for practical subjects, 0 for theory-only)',
  `total_marks` double GENERATED ALWAYS AS (`theory_marks` + `internal_marks`) STORED COMMENT 'Auto-calculated total marks',
  `grade` varchar(5) DEFAULT NULL COMMENT 'Letter grade (A+, A, B+, B, C+, C, D+, D, NG)',
  `gpa` double DEFAULT NULL COMMENT 'Grade Point Average (0.0 to 4.0)',
  `remarks` varchar(90) DEFAULT NULL COMMENT 'Grade remarks (Outstanding, Excellent, etc.)',
  `result_status` enum('PASS','FAIL') DEFAULT 'FAIL' COMMENT 'Overall pass/fail status',
  `entry_date` datetime DEFAULT current_timestamp(),
  `updated_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_exam_results`
--

INSERT INTO `tbl_exam_results` (`id`, `student`, `class`, `subject_combination`, `term`, `score`, `theory_marks`, `internal_marks`, `grade`, `gpa`, `remarks`, `result_status`, `entry_date`, `updated_date`) VALUES
(1, '22061002', 2, 1, 1, 68, 51, 17, 'B', 2.8, 'Good', 'PASS', '2025-09-01 01:20:35', '2025-09-01 01:22:38'),
(2, '22061002', 2, 1, 2, 66, 49.5, 16.5, 'B', 2.8, 'Good', 'PASS', '2025-09-01 01:20:35', '2025-09-01 01:22:38'),
(3, '50230091', 1, 1, 1, 80, 60, 20, 'A', 3.6, 'Excellent', 'PASS', '2025-09-01 01:20:35', '2025-09-01 01:22:38'),
(4, '50230091', 1, 1, 4, 60, 40, 20, 'B', 2.8, 'Good', 'PASS', '2025-09-01 01:24:12', '2025-09-01 01:59:50'),
(7, '50230091', 1, 3, 4, 60, 40, 20, 'B', 2.8, 'Good', 'PASS', '2025-09-01 01:59:50', '2025-09-01 01:59:50'),
(8, '50230091', 1, 4, 4, 40, 40, 0, 'C', 2, 'Acceptable', 'PASS', '2025-09-01 01:59:50', '2025-09-01 01:59:50'),
(9, '50230091', 1, 5, 4, 40, 40, 0, 'C', 2, 'Acceptable', 'PASS', '2025-09-01 01:59:50', '2025-09-01 01:59:50'),
(10, '50230091', 1, 6, 4, 60, 40, 20, 'B', 2.8, 'Good', 'PASS', '2025-09-01 01:59:50', '2025-09-01 01:59:50'),
(11, '50230091', 1, 7, 4, 40, 40, 0, 'C', 2, 'Acceptable', 'PASS', '2025-09-01 01:59:50', '2025-09-01 01:59:50'),
(12, '50230092', 1, 1, 4, 75, 50, 25, 'B+', 3.2, 'Very Good', 'PASS', '2025-09-05 20:01:45', '2025-09-05 20:01:45'),
(13, '50230092', 1, 3, 4, 75, 50, 25, 'B+', 3.2, 'Very Good', 'PASS', '2025-09-05 20:01:45', '2025-09-05 20:01:45'),
(14, '50230092', 1, 4, 4, 50, 50, 0, 'C+', 2.4, 'Satisfactory', 'PASS', '2025-09-05 20:01:45', '2025-09-05 20:01:45'),
(15, '50230092', 1, 5, 4, 50, 50, 0, 'C+', 2.4, 'Satisfactory', 'PASS', '2025-09-05 20:01:45', '2025-09-05 20:01:45'),
(16, '50230092', 1, 6, 4, 75, 50, 25, 'B+', 3.2, 'Very Good', 'PASS', '2025-09-05 20:01:45', '2025-09-05 20:01:45'),
(17, '50230092', 1, 7, 4, 50, 50, 0, 'C+', 2.4, 'Satisfactory', 'PASS', '2025-09-05 20:01:45', '2025-09-05 20:01:45'),
(18, '50230092', 1, 1, 1, 75, 50, 25, 'B+', 3.2, 'Very Good', 'PASS', '2025-09-05 20:02:25', '2025-09-05 20:02:25'),
(19, '50230092', 1, 3, 1, 75, 50, 25, 'B+', 3.2, 'Very Good', 'PASS', '2025-09-05 20:02:25', '2025-09-05 20:02:25'),
(20, '50230092', 1, 4, 1, 50, 50, 0, 'C+', 2.4, 'Satisfactory', 'PASS', '2025-09-05 20:02:25', '2025-09-05 20:02:25'),
(21, '50230092', 1, 5, 1, 50, 50, 0, 'C+', 2.4, 'Satisfactory', 'PASS', '2025-09-05 20:02:25', '2025-09-05 20:02:25'),
(22, '50230092', 1, 6, 1, 75, 50, 25, 'B+', 3.2, 'Very Good', 'PASS', '2025-09-05 20:02:25', '2025-09-05 20:02:25'),
(23, '50230092', 1, 7, 1, 50, 50, 0, 'C+', 2.4, 'Satisfactory', 'PASS', '2025-09-05 20:02:25', '2025-09-05 20:02:25'),
(24, '50230092', 1, 1, 3, 75, 50, 25, 'B+', 3.2, 'Very Good', 'PASS', '2025-09-05 20:02:56', '2025-09-05 20:02:56'),
(25, '50230092', 1, 3, 3, 75, 50, 25, 'B+', 3.2, 'Very Good', 'PASS', '2025-09-05 20:02:56', '2025-09-05 20:02:56'),
(26, '50230092', 1, 4, 3, 50, 50, 0, 'C+', 2.4, 'Satisfactory', 'PASS', '2025-09-05 20:02:56', '2025-09-05 20:02:56'),
(27, '50230092', 1, 5, 3, 50, 50, 0, 'C+', 2.4, 'Satisfactory', 'PASS', '2025-09-05 20:02:56', '2025-09-05 20:02:56'),
(28, '50230092', 1, 6, 3, 75, 50, 25, 'B+', 3.2, 'Very Good', 'PASS', '2025-09-05 20:02:56', '2025-09-05 20:02:56'),
(29, '50230092', 1, 7, 3, 50, 50, 0, 'C+', 2.4, 'Satisfactory', 'PASS', '2025-09-05 20:02:56', '2025-09-05 20:02:56'),
(30, '50230092', 1, 1, 2, 75, 50, 25, 'B+', 3.2, 'Very Good', 'PASS', '2025-09-05 20:03:25', '2025-09-05 20:03:25'),
(31, '50230092', 1, 3, 2, 75, 50, 25, 'B+', 3.2, 'Very Good', 'PASS', '2025-09-05 20:03:25', '2025-09-05 20:03:25'),
(32, '50230092', 1, 4, 2, 50, 50, 0, 'C+', 2.4, 'Satisfactory', 'PASS', '2025-09-05 20:03:25', '2025-09-05 20:03:25'),
(33, '50230092', 1, 5, 2, 50, 50, 0, 'C+', 2.4, 'Satisfactory', 'PASS', '2025-09-05 20:03:25', '2025-09-05 20:03:25'),
(34, '50230092', 1, 6, 2, 75, 50, 25, 'B+', 3.2, 'Very Good', 'PASS', '2025-09-05 20:03:25', '2025-09-05 20:03:25'),
(35, '50230092', 1, 7, 2, 50, 50, 0, 'C+', 2.4, 'Satisfactory', 'PASS', '2025-09-05 20:03:25', '2025-09-05 20:03:25'),
(36, '50230002', 1, 1, 1, 75, 50, 25, 'B+', 3.2, 'Very Good', 'PASS', '2025-09-06 22:59:38', '2025-09-06 22:59:38'),
(37, '50230002', 1, 3, 1, 75, 50, 25, 'B+', 3.2, 'Very Good', 'PASS', '2025-09-06 22:59:38', '2025-09-06 22:59:38'),
(38, '50230002', 1, 4, 1, 50, 50, 0, 'C+', 2.4, 'Satisfactory', 'PASS', '2025-09-06 22:59:38', '2025-09-06 22:59:38'),
(39, '50230002', 1, 5, 1, 50, 50, 0, 'C+', 2.4, 'Satisfactory', 'PASS', '2025-09-06 22:59:38', '2025-09-06 22:59:38'),
(40, '50230002', 1, 6, 1, 75, 50, 25, 'B+', 3.2, 'Very Good', 'PASS', '2025-09-06 22:59:38', '2025-09-06 22:59:38'),
(41, '50230002', 1, 7, 1, 50, 50, 0, 'C+', 2.4, 'Satisfactory', 'PASS', '2025-09-06 22:59:38', '2025-09-06 22:59:38');

--
-- Triggers `tbl_exam_results`
--
DELIMITER $$
CREATE TRIGGER `tr_auto_calculate_grades` BEFORE INSERT ON `tbl_exam_results` FOR EACH ROW BEGIN
    DECLARE v_has_practical TINYINT(1) DEFAULT 0;
    DECLARE v_theory_pass DOUBLE DEFAULT 35;
    DECLARE v_practical_pass DOUBLE DEFAULT 0;
    DECLARE v_total_marks DOUBLE;
    DECLARE v_grade VARCHAR(5);
    DECLARE v_gpa DOUBLE;
    DECLARE v_remarks VARCHAR(90);
    
    -- Get subject information
    SELECT 
        COALESCE(s.has_practical, 0),
        CASE WHEN COALESCE(s.has_practical, 0) = 1 THEN 26.25 ELSE 35 END,
        CASE WHEN COALESCE(s.has_practical, 0) = 1 THEN 8.75 ELSE 0 END
    INTO v_has_practical, v_theory_pass, v_practical_pass
    FROM tbl_subject_combinations sc
    LEFT JOIN tbl_subjects s ON sc.subject = s.id
    WHERE sc.id = NEW.subject_combination;
    
    -- Calculate total marks
    SET v_total_marks = NEW.theory_marks + NEW.internal_marks;
    
    -- Check pass/fail conditions
    IF (NEW.theory_marks < v_theory_pass) OR 
       (v_has_practical = 1 AND NEW.internal_marks < v_practical_pass) THEN
        SET NEW.grade = 'NG';
        SET NEW.gpa = 0.0;
        SET NEW.remarks = 'Not Graded';
        SET NEW.result_status = 'FAIL';
    ELSE
        -- Get grade based on total marks
        SELECT name, gpa, remark
        INTO v_grade, v_gpa, v_remarks
        FROM tbl_grade_system
        WHERE v_total_marks >= min AND v_total_marks <= max
        ORDER BY min DESC
        LIMIT 1;
        
        IF v_grade IS NULL THEN
            SET NEW.grade = 'NG';
            SET NEW.gpa = 0.0;
            SET NEW.remarks = 'Not Graded';
            SET NEW.result_status = 'FAIL';
        ELSE
            SET NEW.grade = v_grade;
            SET NEW.gpa = v_gpa;
            SET NEW.remarks = v_remarks;
            SET NEW.result_status = 'PASS';
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_auto_calculate_grades_update` BEFORE UPDATE ON `tbl_exam_results` FOR EACH ROW BEGIN
    DECLARE v_has_practical TINYINT(1) DEFAULT 0;
    DECLARE v_theory_pass DOUBLE DEFAULT 35;
    DECLARE v_practical_pass DOUBLE DEFAULT 0;
    DECLARE v_total_marks DOUBLE;
    DECLARE v_grade VARCHAR(5);
    DECLARE v_gpa DOUBLE;
    DECLARE v_remarks VARCHAR(90);
    
    -- Get subject information
    SELECT 
        COALESCE(s.has_practical, 0),
        CASE WHEN COALESCE(s.has_practical, 0) = 1 THEN 26.25 ELSE 35 END,
        CASE WHEN COALESCE(s.has_practical, 0) = 1 THEN 8.75 ELSE 0 END
    INTO v_has_practical, v_theory_pass, v_practical_pass
    FROM tbl_subject_combinations sc
    LEFT JOIN tbl_subjects s ON sc.subject = s.id
    WHERE sc.id = NEW.subject_combination;
    
    -- Calculate total marks
    SET v_total_marks = NEW.theory_marks + NEW.internal_marks;
    
    -- Check pass/fail conditions
    IF (NEW.theory_marks < v_theory_pass) OR 
       (v_has_practical = 1 AND NEW.internal_marks < v_practical_pass) THEN
        SET NEW.grade = 'NG';
        SET NEW.gpa = 0.0;
        SET NEW.remarks = 'Not Graded';
        SET NEW.result_status = 'FAIL';
    ELSE
        -- Get grade based on total marks
        SELECT name, gpa, remark
        INTO v_grade, v_gpa, v_remarks
        FROM tbl_grade_system
        WHERE v_total_marks >= min AND v_total_marks <= max
        ORDER BY min DESC
        LIMIT 1;
        
        IF v_grade IS NULL THEN
            SET NEW.grade = 'NG';
            SET NEW.gpa = 0.0;
            SET NEW.remarks = 'Not Graded';
            SET NEW.result_status = 'FAIL';
        ELSE
            SET NEW.grade = v_grade;
            SET NEW.gpa = v_gpa;
            SET NEW.remarks = v_remarks;
            SET NEW.result_status = 'PASS';
        END IF;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_exam_results_backup`
--

CREATE TABLE `tbl_exam_results_backup` (
  `id` int(11) NOT NULL DEFAULT 0,
  `student` varchar(20) NOT NULL,
  `class` int(11) NOT NULL,
  `subject_combination` int(11) NOT NULL,
  `term` int(11) NOT NULL,
  `score` double NOT NULL DEFAULT 0,
  `theory_marks` double NOT NULL DEFAULT 0 COMMENT 'Theory/External marks (75 for practical subjects, 100 for theory-only)',
  `internal_marks` double NOT NULL DEFAULT 0 COMMENT 'Internal/Practical marks (25 for practical subjects, 0 for theory-only)',
  `total_marks` double DEFAULT NULL COMMENT 'Auto-calculated total marks',
  `grade` varchar(5) DEFAULT NULL COMMENT 'Letter grade (A+, A, B+, B, C+, C, D+, D, NG)',
  `gpa` double DEFAULT NULL COMMENT 'Grade Point Average (0.0 to 4.0)',
  `remarks` varchar(90) DEFAULT NULL COMMENT 'Grade remarks (Outstanding, Excellent, etc.)',
  `result_status` enum('PASS','FAIL') DEFAULT 'FAIL' COMMENT 'Overall pass/fail status',
  `entry_date` datetime DEFAULT current_timestamp(),
  `updated_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_exam_results_backup`
--

INSERT INTO `tbl_exam_results_backup` (`id`, `student`, `class`, `subject_combination`, `term`, `score`, `theory_marks`, `internal_marks`, `total_marks`, `grade`, `gpa`, `remarks`, `result_status`, `entry_date`, `updated_date`) VALUES
(1, '22061002', 2, 1, 1, 68, 0, 0, 0, NULL, NULL, NULL, 'FAIL', '2025-09-01 01:20:35', '2025-09-01 01:20:35'),
(2, '22061002', 2, 1, 2, 66, 0, 0, 0, NULL, NULL, NULL, 'FAIL', '2025-09-01 01:20:35', '2025-09-01 01:20:35'),
(3, '50230091', 1, 1, 1, 80, 0, 0, 0, NULL, NULL, NULL, 'FAIL', '2025-09-01 01:20:35', '2025-09-01 01:20:35');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_grade_system`
--

CREATE TABLE `tbl_grade_system` (
  `id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `min` double NOT NULL,
  `max` double NOT NULL,
  `gpa` double NOT NULL DEFAULT 0,
  `remark` varchar(90) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_grade_system`
--

INSERT INTO `tbl_grade_system` (`id`, `name`, `min`, `max`, `gpa`, `remark`) VALUES
(1, 'A+', 90, 100, 4, 'Outstanding'),
(2, 'A', 80, 89, 3.6, 'Excellent'),
(3, 'B+', 70, 79, 3.2, 'Very Good'),
(4, 'B', 60, 69, 2.8, 'Good'),
(5, 'C+', 50, 59, 2.4, 'Satisfactory'),
(6, 'C', 40, 49, 2, 'Acceptable'),
(7, 'D', 30, 39, 1.6, 'Partially Acceptable'),
(8, 'NG', 0, 29, 0, 'Failed');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_login_sessions`
--

CREATE TABLE `tbl_login_sessions` (
  `session_key` varchar(90) NOT NULL,
  `staff` int(11) DEFAULT NULL,
  `student` varchar(20) DEFAULT NULL,
  `ip_address` varchar(90) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_school`
--

CREATE TABLE `tbl_school` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `logo` varchar(50) NOT NULL,
  `result_system` int(11) NOT NULL COMMENT '0 = Average, 1 = Division',
  `allow_results` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_school`
--

INSERT INTO `tbl_school` (`id`, `name`, `logo`, `result_system`, `allow_results`) VALUES
(1, 'ACHS COLLEGE', 'school_logo1754365748.png', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_smtp`
--

CREATE TABLE `tbl_smtp` (
  `id` int(11) NOT NULL,
  `server` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `port` varchar(255) NOT NULL,
  `encryption` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_smtp`
--

INSERT INTO `tbl_smtp` (`id`, `server`, `username`, `password`, `port`, `encryption`, `status`) VALUES
(1, 'smtp.gmail.com', 'vriasuhn@gmail.com', 'smtp.gmail.com', '587', 'tls', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_staff`
--

CREATE TABLE `tbl_staff` (
  `id` int(11) NOT NULL,
  `fname` varchar(20) NOT NULL,
  `lname` varchar(20) NOT NULL,
  `gender` varchar(6) NOT NULL,
  `email` varchar(90) NOT NULL,
  `password` varchar(90) NOT NULL,
  `level` int(11) NOT NULL COMMENT '0 = Admin, 1 = Academic, 2 = Teacher',
  `status` int(11) NOT NULL DEFAULT 1 COMMENT '0 = Blocked, 1 = Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_staff`
--

INSERT INTO `tbl_staff` (`id`, `fname`, `lname`, `gender`, `email`, `password`, `level`, `status`) VALUES
(1, 'Govinda', 'Gautam', 'Male', 'govinda@srms.test', '$2y$10$l8XYJDrBHTyeZkpupiRhwey6jJihzku0bYXiVtBM5kDRz3sZvSpgC', 2, 1),
(2, 'DENIS', 'MWAMBUNGU', 'Male', 'denis@srms.test', '$2y$10$l8XYJDrBHTyeZkpupiRhwey6jJihzku0bYXiVtBM5kDRz3sZvSpgC', 2, 1),
(3, 'FRANCIS', 'MASANJA', 'Male', 'francis@srms.test', '$2y$10$l8XYJDrBHTyeZkpupiRhwey6jJihzku0bYXiVtBM5kDRz3sZvSpgC', 2, 1),
(4, 'HANS', 'UISSO', 'Male', 'hans@srms.test', '$2y$10$l8XYJDrBHTyeZkpupiRhwey6jJihzku0bYXiVtBM5kDRz3sZvSpgC', 2, 1),
(5, 'HANSON', 'MAITA', 'Male', 'hanson@srms.test', '$2y$10$l8XYJDrBHTyeZkpupiRhwey6jJihzku0bYXiVtBM5kDRz3sZvSpgC', 2, 1),
(6, 'HENRY', 'GOWELLE', 'Male', 'henry@srms.test', '$2y$10$l8XYJDrBHTyeZkpupiRhwey6jJihzku0bYXiVtBM5kDRz3sZvSpgC', 2, 1),
(7, 'HILDA', 'KANDAUMA', 'Female', 'hilda@srms.test', '$2y$10$l8XYJDrBHTyeZkpupiRhwey6jJihzku0bYXiVtBM5kDRz3sZvSpgC', 2, 1),
(8, 'INNOCENT', 'MBAWALA', 'Male', 'innocent@srms.test', '$2y$10$l8XYJDrBHTyeZkpupiRhwey6jJihzku0bYXiVtBM5kDRz3sZvSpgC', 2, 1),
(9, 'JAMALI', 'NZOTA', 'Male', 'jamali@srms.test', '$2y$10$l8XYJDrBHTyeZkpupiRhwey6jJihzku0bYXiVtBM5kDRz3sZvSpgC', 2, 1),
(10, 'JAMIL', 'ABDALLAH', 'Male', 'jamil@srms.test', '$2y$10$l8XYJDrBHTyeZkpupiRhwey6jJihzku0bYXiVtBM5kDRz3sZvSpgC', 2, 1),
(11, 'JOAN', 'NKYA', 'Female', 'joan@srms.test', '$2y$10$l8XYJDrBHTyeZkpupiRhwey6jJihzku0bYXiVtBM5kDRz3sZvSpgC', 2, 1),
(12, 'JOSEPH', 'HAMISI', 'Male', 'joseph@srms.test', '$2y$10$l8XYJDrBHTyeZkpupiRhwey6jJihzku0bYXiVtBM5kDRz3sZvSpgC', 2, 1),
(13, 'Saugat', 'Thapa', 'Male', 'saugat@gmail.com', '$2y$10$ZNGU9aRLjyY5ZQKebFMGc.6qyoBLuOM0o4lLJR6Bc3haFhOrEpSL6', 2, 1),
(14, 'Rohan', 'Shrestha', 'Male', 'bca210604_rohan@achsnepal.edu.np', '$2y$10$h8Yc0PG/yofw8P3mJ6KcteP426cSfWSPk1WFw1BGOWr4O9yf9m6K.', 0, 1),
(15, 'test', 'test', 'Male', 'bishal@gmail.com', '$2y$10$/xCONkg1oTHsiuMm8qOUrObymLCTdoGR.GTdOGORVOBn4GMM6upD2', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_students`
--

CREATE TABLE `tbl_students` (
  `id` varchar(20) NOT NULL,
  `fname` varchar(70) NOT NULL,
  `mname` varchar(70) NOT NULL,
  `lname` varchar(70) NOT NULL,
  `gender` varchar(7) NOT NULL,
  `email` varchar(90) NOT NULL,
  `class` int(11) NOT NULL,
  `password` varchar(90) NOT NULL,
  `level` int(11) NOT NULL DEFAULT 3 COMMENT '3 = student',
  `display_image` varchar(50) NOT NULL DEFAULT 'Blank',
  `status` int(11) NOT NULL DEFAULT 1 COMMENT '0 = Disabled, 1 = Enabled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_students`
--

INSERT INTO `tbl_students` (`id`, `fname`, `mname`, `lname`, `gender`, `email`, `class`, `password`, `level`, `display_image`, `status`) VALUES
('', 'Rohan', 'Kumar', 'Shrestha', 'Male', 'rohan2@gmail.com', 1, 'Rohan@123', 3, 'Blank', 1),
('12345678', 'Jarbis', '', 'Singh', 'Male', 'jarbis@gmail.com', 1, '$2y$10$niJD1.msP8bY5tfmAHTtke/yjlM3tmv/YgRSSvk3VhRgSHfLaSOc2', 3, 'avator_1754469743.jpg', 1),
('123456789', 'Andy', '', 'Ruth', 'Male', 'andy28@gmail.com', 1, '$2y$10$82lNtM7.z3QC/eSO.nep9eSIXkpPkQuS7NywcJp9tfMtgJ3F/T3sy', 3, 'avator_1754469636.png', 1),
('22061002', 'Laila', '', 'Majhnu', 'Female', 'laila@gmail.com', 1, '$2y$10$.l5nQxbDz8CD.c9/9M0BgO1e9sSmNg14B0QRqEROeont8GD9e0toO', 3, 'avator_1754469849.png', 1),
('50230002', 'Alisha', '', 'Maharjan', 'Female', 'alisha.maharjan@demo.com', 1, '$2y$10$t5vUVW9tsDzbZlkrGJQyV.ve4zBusZkEB/om1eo5rIgmLEHv.O9ZW', 3, 'DEFAULT', 1),
('50230003', 'Anit', '', 'Sigdel', 'Female', 'anit.sigdel@demo.com', 1, '$2y$10$v7SUZCZr6rd.a86ktMgymeM9L27anCG1LRnbYh8n/eyZHXTbbAmDG', 3, 'DEFAULT', 1),
('50230004', 'Bijina', '', 'Tandukar', 'Male', 'bijina.tandukar@demo.com', 1, '$2y$10$sdzgSJkB.YhkM./b3G0Rau9bBWjn30i26tLzQFZp9Ap7ZnrPyN0ui', 3, 'DEFAULT', 1),
('50230005', 'Carlos', 'Tamang', 'Lama', 'Female', 'carlos.lama@demo.com', 1, '$2y$10$CU1p46NqEMLVx5.cCjsD9u1ZvMNTNGyB.JuC3zO9fZtDmVvNEl7nu', 3, 'DEFAULT', 1),
('50230006', 'Creation', '', 'Ghalan', 'Female', 'creation.ghalan@demo.com', 1, '$2y$10$u0I4OP/Y6F1.0yEPqN8ZTepenNYQY7lHnXjSO20wJNyiyahpyGvne', 3, 'DEFAULT', 1),
('50230007', 'Diben', '', 'Maharjan', 'Female', 'diben.maharjan@demo.com', 1, '$2y$10$CS4.1FGOk7cXwvRToBYLquvOSIFCZ/j4MYgChUjllBiPHBsVWXzmS', 3, 'DEFAULT', 1),
('50230008', 'Gaurab', '', 'Maharjan', 'Female', 'gaurab.maharjan@demo.com', 2, '$2y$10$tUvxlHZnoC18.dJ/wC/LD.FbFg.jsjXBCexP5PrL6IIqSNbmGTe7i', 3, 'DEFAULT', 1),
('50230009', 'Jenisha', '', 'Shrestha', 'Male', 'jenisha.shrestha@demo.com', 1, '$2y$10$cUml/.EiVphS6d//rlmYre.8c60M1qBVXLa.E.IQeVCzkXk2UOrs6', 3, 'DEFAULT', 1),
('50230010', 'Julian', '', 'Maharjan', 'Male', 'julian.maharjan@demo.com', 2, '$2y$10$GT4kZ.l/H2Vilo.WQ/oIfe3HKt06BOkaO1AQsJLkuUbGJloJOVRWG', 3, 'DEFAULT', 1),
('50230011', 'Kalasha', '', 'Maharjan', 'Male', 'kalasha.maharjan@demo.com', 1, '$2y$10$0n3LRtzTQBY9xLVRHwfNoe/uqvlmhDJaEXYJx/dSImWzjtAcPkuP6', 3, 'DEFAULT', 1),
('50230012', 'Krisha', '', 'Khatiwada', 'Male', 'krisha.khatiwada@demo.com', 1, '$2y$10$JzGuucsTjcFxM8/HOYG.7OYBFm6sLO1pZTlFo.xG1d5xoD.9rNoyO', 3, 'DEFAULT', 1),
('50230013', 'Lasta', '', 'Tuladhar', 'Male', 'lasta.tuladhar@demo.com', 1, '$2y$10$J2.bdJ5NwIvauYN/NCCBDe2DiC9GkDndOn0ExCS4pQrjvtRgFvedm', 3, 'DEFAULT', 1),
('50230014', 'Lemek', '', 'Maharjan', 'Female', 'lemek.maharjan@demo.com', 1, '$2y$10$Txh9ih.OX4J6NYniOjVbCONt1CIwYwmL7GA09VSEXiH8iSn9WOvym', 3, 'DEFAULT', 1),
('50230015', 'Maitri', 'Ratna', 'Bajracharya', 'Male', 'maitri.bajracharya@demo.com', 1, '$2y$10$6UN3VunEMSbKVq9jBmS4HOP1f4wqfpoE49vNfePhtVrO3aoB8GCri', 3, 'DEFAULT', 1),
('50230016', 'Manish', '', 'Gautam', 'Female', 'manish.gautam@demo.com', 2, '$2y$10$dLLgzM8zjXQvJ24Rh.LVI.ZRDL3Db3L8Sry5WWPc8kYs2Y8PDeu6S', 3, 'DEFAULT', 1),
('50230017', 'Manthan', '', 'Maharjan', 'Male', 'manthan.maharjan@demo.com', 1, '$2y$10$StghZASDkAwCFc/nnIvXje.VRHt19qgBFZRP5DFFstMhYLkBz3iLS', 3, 'DEFAULT', 1),
('50230018', 'Maikal', '', 'Garbuja', 'Male', 'maikal.garbuja@demo.com', 2, '$2y$10$XzokZh3tFOtAb5/jRboZNeBp1hlD6UYOU/Y29.ul53uQTrQBDkWGC', 3, 'DEFAULT', 1),
('50230019', 'Muskan', '', 'Malakar', 'Female', 'muskan.malakar@demo.com', 2, '$2y$10$ooud2iwXWqwZE6X1MPDl8ewmwWeofSlqMPChITHvi5zDKQGkXiLYe', 3, 'DEFAULT', 1),
('50230020', 'Niraj', '', 'Kumar', 'Male', 'niraj.kumar@demo.com', 1, '$2y$10$oTuE2wkMMRY8NjYGaai6cuqkuyYwCXa/jvZKFMYX0LzaJFaPHT6dW', 3, 'DEFAULT', 1),
('50230021', 'Palistha', '', 'Nakarmi', 'Female', 'palistha.nakarmi@demo.com', 1, '$2y$10$N9FPgzwSmt1pSp/BEsykW.c/ORvq7wyq14D/WCmwDrIC8z4APFriu', 3, 'DEFAULT', 1),
('50230022', 'Palpasa', 'Thapa', 'Magar', 'Male', 'palpasa.magar@demo.com', 1, '$2y$10$Emt3t6ypjNbKobbT3YLdRePDDWVuRYC4y9MCqGCeKzFCKb3F6fT3W', 3, 'DEFAULT', 1),
('50230023', 'Prachita', 'Thapa', 'Magar', 'Male', 'prachita.magar@demo.com', 1, '$2y$10$HGb2Jv3cva5jiOv9IUa9/OaKj53XhWNJyT4.liiBP61YPiXnITQgS', 3, 'DEFAULT', 1),
('50230024', 'Pratik', '', 'Chaudhary', 'Female', 'pratik.chaudhary@demo.com', 1, '$2y$10$sQhhKIfOsrUSjtSbZ01tFOSsbN32naeafy2zteUZ2uWMcH.f2eUDi', 3, 'DEFAULT', 1),
('50230025', 'Prinsha', '', 'Karki', 'Male', 'prinsha.karki@demo.com', 1, '$2y$10$SywXelO9MZcaC8.QLtGZuuOCmRzk8LElcFmJ.9Ed9dVNS51K6BgF.', 3, 'DEFAULT', 1),
('50230026', 'Rakesh', '', 'Maharjan', 'Female', 'rakesh.maharjan@demo.com', 1, '$2y$10$1.VwRIQAYzz.RIsGiXCiteUxe4eXNn9Fz/tGwvG2XYJSK8IkVjCSu', 3, 'DEFAULT', 1),
('50230027', 'Rashik', '', 'Maharjan', 'Male', 'rashik.maharjan@demo.com', 1, '$2y$10$ijkDdwHOK6uJLEZ5snDpHu89o1RwTFZXYgnEEhJKPehB/7Vwm6EcC', 3, 'DEFAULT', 1),
('50230028', 'Robin', '', 'KC', 'Female', 'robin.kc@demo.com', 1, '$2y$10$vgdA2zzvGzOXvRjQRh1dxO7TBx3JYLoHSK9N97qjtx.jegaOtloam', 3, 'DEFAULT', 1),
('50230029', 'Roxan', '', 'Maharjan', 'Female', 'roxan.maharjan@demo.com', 1, '$2y$10$uS1ETYUlje5BLS0ejdOcp.RnCHB4aMHzVCH97sxCgkkXB.jYbmHwi', 3, 'DEFAULT', 1),
('50230030', 'Sakshyam', '', 'Basnet', 'Male', 'sakshyam.basnet@demo.com', 1, '$2y$10$azwK3xrmkBD2sl6eN9JwQuyXR0Qbh6X5Y9rFJWLmN840hD0tL9EnO', 3, 'DEFAULT', 1),
('50230031', 'Samayan', '', 'Pariyar', 'Female', 'samayan.pariyar@demo.com', 1, '$2y$10$fWPB.1bKNbE7a2Oqwb1YO.ENvZKhUwa.Hswq7x5rn77G74mIAVpPK', 3, 'DEFAULT', 1),
('50230032', 'Saugat', '', 'Thapa', 'Female', 'saugat.thapa@demo.com', 1, '$2y$10$.80fBg/OUHErec1WPMgKBOmjwDxerlGkcYWYcIJScETT2FPmyKJri', 3, 'DEFAULT', 1),
('50230033', 'Sohan', '', 'Tamang', 'Female', 'sohan.tamang@demo.com', 1, '$2y$10$zyN6e89sRz8Ch1KGdwOEjOQYX4fP.pL7Hzu3GeE7/LKZKZog9pnmC', 3, 'DEFAULT', 1),
('50230034', 'Sonish', '', 'Maharjan', 'Female', 'sonish.maharjan@demo.com', 1, '$2y$10$JzjGqm5qnRU6Tc0EZXC1TeQsVzDQdHURAjSlNiFak9/LHSLZ8p1te', 3, 'DEFAULT', 1),
('50230035', 'Subha', '', 'Maharjan', 'Female', 'subha.maharjan@demo.com', 1, '$2y$10$tF8I07CzUebaWO1f7eiSReOEfs2LrSxrO3I0laPz9tWYRyC.oqvbm', 3, 'DEFAULT', 1),
('50230036', 'Subham', '', 'Maharjan', 'Female', 'subham.maharjan@demo.com', 1, '$2y$10$4P5c.6kVlRRuy2N8.Q6tpOW1RtsX8LwpUZzKF8IBJH47XeSlqzgWC', 3, 'DEFAULT', 1),
('50230037', 'Subham', '', 'Shrestha', 'Male', 'subham.shrestha@demo.com', 1, '$2y$10$Wgo0FhBWJIQpK36u2vqDjOrezdZnymiNDijEpdhScW/GabUJceOGG', 3, 'DEFAULT', 1),
('50230038', 'Suhan', '', 'Budhathoki', 'Male', 'suhan.budhathoki@demo.com', 1, '$2y$10$A2aJMjkiN0oR9n2k1hej1O4cF98rDB53.i1pqrDick184mY/a9UpC', 3, 'DEFAULT', 1),
('50230039', 'Sujal', '', 'Maharjan', 'Female', 'sujal.maharjan@demo.com', 1, '$2y$10$HFTcj36Ccr4jkc4ZCPReI.XIs4N4Nz89vKPpjNJONFB9D/fo/dRVa', 3, 'DEFAULT', 1),
('50230040', 'Swastika', '', 'Maharjan', 'Female', 'swastika.maharjan@demo.com', 1, '$2y$10$7N6glvrIL4YclGBEcj0CB.XfpqFrhJpUA55DqhvKEKZHRmD5WMs/e', 3, 'DEFAULT', 1),
('50230041', 'Ujeni', '', 'Shrestha', 'Male', 'ujeni.shrestha@demo.com', 1, '$2y$10$rWgfz7vT6F7KvBzatPmEOuAQockLM9ZpYUdUq69ET/WTEYSoM5YAC', 3, 'DEFAULT', 1),
('50230091', 'Aarav', '', 'Sharma', 'Male', 'aarav.sharma@demo.com', 1, '$2y$10$t5vUVW9tsDzbZlkrGJQyV.ve4zBusZkEB/om1eo5rIgmLEHv.O9ZW', 3, 'DEFAULT', 1),
('50230092', 'Aisha', '', 'Patel', 'Female', 'aisha.patel@demo.com', 1, '$2y$10$v7SUZCZr6rd.a86ktMgymeM9L27anCG1LRnbYh8n/eyZHXTbbAmDG', 3, 'DEFAULT', 1),
('50230093', 'Arjun', '', 'Kumar', 'Male', 'arjun.kumar@demo.com', 1, '$2y$10$sdzgSJkB.YhkM./b3G0Rau9bBWjn30i26tLzQFZp9Ap7ZnrPyN0ui', 3, 'DEFAULT', 1),
('50230094', 'Diya', '', 'Singh', 'Female', 'diya.singh@demo.com', 1, '$2y$10$CU1p46NqEMLVx5.cCjsD9u1ZvMNTNGyB.JuC3zO9fZtDmVvNEl7nu', 3, 'DEFAULT', 1),
('50230095', 'Esha', '', 'Verma', 'Female', 'esha.verma@demo.com', 1, '$2y$10$u0I4OP/Y6F1.0yEPqN8ZTepenNYQY7lHnXjSO20wJNyiyahpyGvne', 3, 'DEFAULT', 1),
('50230096', 'Ishaan', '', 'Gupta', 'Male', 'ishaan.gupta@demo.com', 1, '$2y$10$CS4.1FGOk7cXwvRToBYLquvOSIFCZ/j4MYgChUjllBiPHBsVWXzmS', 3, 'DEFAULT', 1),
('50230097', 'Kavya', '', 'Joshi', 'Female', 'kavya.joshi@demo.com', 1, '$2y$10$tUvxlHZnoC18.dJ/wC/LD.FbFg.jsjXBCexP5PrL6IIqSNbmGTe7i', 3, 'DEFAULT', 1),
('50230098', 'Lakshay', '', 'Malhotra', 'Male', 'lakshay.malhotra@demo.com', 1, '$2y$10$cUml/.EiVphS6d//rlmYre.8c60M1qBVXLa.E.IQeVCzkXk2UOrs6', 3, 'DEFAULT', 1),
('50230099', 'Mira', '', 'Chopra', 'Female', 'mira.chopra@demo.com', 1, '$2y$10$GT4kZ.l/H2Vilo.WQ/oIfe3HKt06BOkaO1AQsJLkuUbGJloJOVRWG', 3, 'DEFAULT', 1),
('50230100', 'Neel', '', 'Reddy', 'Male', 'neel.reddy@demo.com', 1, '$2y$10$0n3LRtzTQBY9xLVRHwfNoe/uqvlmhDJaEXYJx/dSImWzjtAcPkuP6', 3, 'DEFAULT', 1),
('50230101', 'Priya', '', 'Iyer', 'Female', 'priya.iyer@demo.com', 1, '$2y$10$JzGuucsTjcFxM8/HOYG.7OYBFm6sLO1pZTlFo.xG1d5xoD.9rNoyO', 3, 'DEFAULT', 1),
('50230102', 'Rohan', '', 'Mehta', 'Male', 'rohan.mehta@demo.com', 1, '$2y$10$J2.bdJ5NwIvauYN/NCCBDe2DiC9GkDndOn0ExCS4pQrjvtRgFvedm', 3, 'DEFAULT', 1),
('50230103', 'Saanvi', '', 'Kapoor', 'Female', 'saanvi.kapoor@demo.com', 1, '$2y$10$Txh9ih.OX4J6NYniOjVbCONt1CIwYwmL7GA09VSEXiH8iSn9WOvym', 3, 'DEFAULT', 1),
('50230104', 'Tanish', '', 'Bhatt', 'Male', 'tanish.bhatt@demo.com', 1, '$2y$10$6UN3VunEMSbKVq9jBmS4HOP1f4wqfpoE49vNfePhtVrO3aoB8GCri', 3, 'DEFAULT', 1),
('50230105', 'Vanya', '', 'Saxena', 'Female', 'vanya.saxena@demo.com', 1, '$2y$10$dLLgzM8zjXQvJ24Rh.LVI.ZRDL3Db3L8Sry5WWPc8kYs2Y8PDeu6S', 3, 'DEFAULT', 1),
('50230111', 'Aditya', '', 'Rajput', 'Male', 'aditya.rajput@demo.com', 2, '$2y$10$t5vUVW9tsDzbZlkrGJQyV.ve4zBusZkEB/om1eo5rIgmLEHv.O9ZW', 3, 'DEFAULT', 1),
('50230112', 'Ananya', '', 'Desai', 'Female', 'ananya.desai@demo.com', 2, '$2y$10$v7SUZCZr6rd.a86ktMgymeM9L27anCG1LRnbYh8n/eyZHXTbbAmDG', 3, 'DEFAULT', 1),
('50230113', 'Dhruv', '', 'Tiwari', 'Male', 'dhruv.tiwari@demo.com', 2, '$2y$10$sdzgSJkB.YhkM./b3G0Rau9bBWjn30i26tLzQFZp9Ap7ZnrPyN0ui', 3, 'DEFAULT', 1),
('50230114', 'Ira', '', 'Nair', 'Female', 'ira.nair@demo.com', 2, '$2y$10$CU1p46NqEMLVx5.cCjsD9u1ZvMNTNGyB.JuC3zO9fZtDmVvNEl7nu', 3, 'DEFAULT', 1),
('50230115', 'Kabir', '', 'Chauhan', 'Male', 'kabir.chauhan@demo.com', 2, '$2y$10$u0I4OP/Y6F1.0yEPqN8ZTepenNYQY7lHnXjSO20wJNyiyahpyGvne', 3, 'DEFAULT', 1),
('50230116', 'Kiara', '', 'Mishra', 'Female', 'kiara.mishra@demo.com', 2, '$2y$10$CS4.1FGOk7cXwvRToBYLquvOSIFCZ/j4MYgChUjllBiPHBsVWXzmS', 3, 'DEFAULT', 1),
('50230117', 'Laksh', '', 'Yadav', 'Male', 'laksh.yadav@demo.com', 2, '$2y$10$tUvxlHZnoC18.dJ/wC/LD.FbFg.jsjXBCexP5PrL6IIqSNbmGTe7i', 3, 'DEFAULT', 1),
('50230118', 'Maya', '', 'Pandey', 'Female', 'maya.pandey@demo.com', 2, '$2y$10$cUml/.EiVphS6d//rlmYre.8c60M1qBVXLa.E.IQeVCzkXk2UOrs6', 3, 'DEFAULT', 1),
('50230119', 'Nikhil', '', 'Sinha', 'Male', 'nikhil.sinha@demo.com', 2, '$2y$10$GT4kZ.l/H2Vilo.WQ/oIfe3HKt06BOkaO1AQsJLkuUbGJloJOVRWG', 3, 'DEFAULT', 1),
('50230120', 'Pari', '', 'Trivedi', 'Female', 'pari.trivedi@demo.com', 2, '$2y$10$0n3LRtzTQBY9xLVRHwfNoe/uqvlmhDJaEXYJx/dSImWzjtAcPkuP6', 3, 'DEFAULT', 1),
('50230121', 'Rudra', '', 'Verma', 'Male', 'rudra.verma@demo.com', 2, '$2y$10$JzGuucsTjcFxM8/HOYG.7OYBFm6sLO1pZTlFo.xG1d5xoD.9rNoyO', 3, 'DEFAULT', 1),
('50230122', 'Sia', '', 'Kaur', 'Female', 'sia.kaur@demo.com', 2, '$2y$10$J2.bdJ5NwIvauYN/NCCBDe2DiC9GkDndOn0ExCS4pQrjvtRgFvedm', 3, 'DEFAULT', 1),
('50230123', 'Ved', '', 'Shah', 'Male', 'ved.shah@demo.com', 2, '$2y$10$Txh9ih.OX4J6NYniOjVbCONt1CIwYwmL7GA09VSEXiH8iSn9WOvym', 3, 'DEFAULT', 1),
('50230124', 'Zara', '', 'Khan', 'Female', 'zara.khan@demo.com', 2, '$2y$10$6UN3VunEMSbKVq9jBmS4HOP1f4wqfpoE49vNfePhtVrO3aoB8GCri', 3, 'DEFAULT', 1),
('50230125', 'Aryan', '', 'Chopra', 'Male', 'aryan.chopra@demo.com', 2, '$2y$10$dLLgzM8zjXQvJ24Rh.LVI.ZRDL3Db3L8Sry5WWPc8kYs2Y8PDeu6S', 3, 'DEFAULT', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_subjects`
--

CREATE TABLE `tbl_subjects` (
  `id` int(11) NOT NULL,
  `name` varchar(90) NOT NULL,
  `has_practical` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = Theory Only (100 marks), 1 = Theory + Practical (75+25)',
  `theory_full_marks` int(11) NOT NULL DEFAULT 100,
  `practical_full_marks` int(11) NOT NULL DEFAULT 0,
  `theory_pass_marks` double NOT NULL DEFAULT 35,
  `practical_pass_marks` double NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_subjects`
--

INSERT INTO `tbl_subjects` (`id`, `name`, `has_practical`, `theory_full_marks`, `practical_full_marks`, `theory_pass_marks`, `practical_pass_marks`) VALUES
(1, 'Nepali', 0, 100, 0, 35, 0),
(2, 'Mathematics', 1, 75, 25, 26.25, 8.75),
(3, 'Accounting', 1, 75, 25, 26.25, 8.75),
(4, 'Economics', 0, 100, 0, 35, 0),
(5, 'Computer Sciences', 1, 75, 25, 26.25, 8.75),
(6, 'English-II', 0, 100, 0, 35, 0),
(7, 'Nepali-II', 0, 100, 0, 35, 0),
(8, 'Mathematics-II', 1, 75, 25, 26.25, 8.75),
(9, 'Accounting-II', 1, 75, 25, 26.25, 8.75),
(10, 'Economics-II', 0, 100, 0, 35, 0),
(11, 'Computer Science-II', 1, 75, 25, 26.25, 8.75);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_subject_combinations`
--

CREATE TABLE `tbl_subject_combinations` (
  `id` int(11) NOT NULL,
  `class` varchar(100) NOT NULL,
  `subject` int(11) NOT NULL,
  `teacher` int(11) NOT NULL,
  `reg_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_subject_combinations`
--

INSERT INTO `tbl_subject_combinations` (`id`, `class`, `subject`, `teacher`, `reg_date`) VALUES
(1, 'a:1:{i:0;s:1:\"1\";}', 11, 13, '2025-07-23 10:01:02'),
(2, 'a:1:{i:0;s:1:\"2\";}', 3, 2, '2025-08-06 18:19:52'),
(3, 'a:1:{i:0;s:1:\"1\";}', 9, 12, '2025-09-01 01:31:47'),
(4, 'a:1:{i:0;s:1:\"1\";}', 10, 9, '2025-09-01 01:31:58'),
(5, 'a:1:{i:0;s:1:\"1\";}', 6, 10, '2025-09-01 01:32:14'),
(6, 'a:1:{i:0;s:1:\"1\";}', 8, 5, '2025-09-01 01:32:22'),
(7, 'a:1:{i:0;s:1:\"1\";}', 7, 11, '2025-09-01 01:32:31');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_terms`
--

CREATE TABLE `tbl_terms` (
  `id` int(11) NOT NULL,
  `name` varchar(90) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1 COMMENT '0 = Disabled , 1 = Enabled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_terms`
--

INSERT INTO `tbl_terms` (`id`, `name`, `status`) VALUES
(1, 'First Term March 2025', 1),
(2, 'Second Terminal June 2024', 1),
(3, 'Pre Boards September 2024', 1),
(4, 'Final', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_announcements`
--
ALTER TABLE `tbl_announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_classes`
--
ALTER TABLE `tbl_classes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_exam_results`
--
ALTER TABLE `tbl_exam_results`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_student_subject_term` (`student`,`subject_combination`,`term`),
  ADD KEY `student` (`student`),
  ADD KEY `class` (`class`),
  ADD KEY `subject_combination` (`subject_combination`),
  ADD KEY `term` (`term`),
  ADD KEY `idx_grade` (`grade`),
  ADD KEY `idx_result_status` (`result_status`);

--
-- Indexes for table `tbl_grade_system`
--
ALTER TABLE `tbl_grade_system`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_login_sessions`
--
ALTER TABLE `tbl_login_sessions`
  ADD PRIMARY KEY (`session_key`),
  ADD KEY `staff` (`staff`),
  ADD KEY `student` (`student`);

--
-- Indexes for table `tbl_school`
--
ALTER TABLE `tbl_school`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_smtp`
--
ALTER TABLE `tbl_smtp`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_staff`
--
ALTER TABLE `tbl_staff`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_students`
--
ALTER TABLE `tbl_students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class` (`class`);

--
-- Indexes for table `tbl_subjects`
--
ALTER TABLE `tbl_subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_subject_combinations`
--
ALTER TABLE `tbl_subject_combinations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject` (`subject`),
  ADD KEY `teacher` (`teacher`);

--
-- Indexes for table `tbl_terms`
--
ALTER TABLE `tbl_terms`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_announcements`
--
ALTER TABLE `tbl_announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_classes`
--
ALTER TABLE `tbl_classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_exam_results`
--
ALTER TABLE `tbl_exam_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `tbl_grade_system`
--
ALTER TABLE `tbl_grade_system`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tbl_school`
--
ALTER TABLE `tbl_school`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_smtp`
--
ALTER TABLE `tbl_smtp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_staff`
--
ALTER TABLE `tbl_staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tbl_subjects`
--
ALTER TABLE `tbl_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tbl_subject_combinations`
--
ALTER TABLE `tbl_subject_combinations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_terms`
--
ALTER TABLE `tbl_terms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_exam_results`
--
ALTER TABLE `tbl_exam_results`
  ADD CONSTRAINT `tbl_exam_results_ibfk_1` FOREIGN KEY (`student`) REFERENCES `tbl_students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_exam_results_ibfk_2` FOREIGN KEY (`class`) REFERENCES `tbl_classes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_exam_results_ibfk_3` FOREIGN KEY (`subject_combination`) REFERENCES `tbl_subject_combinations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_exam_results_ibfk_4` FOREIGN KEY (`term`) REFERENCES `tbl_terms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_login_sessions`
--
ALTER TABLE `tbl_login_sessions`
  ADD CONSTRAINT `tbl_login_sessions_ibfk_1` FOREIGN KEY (`staff`) REFERENCES `tbl_staff` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_login_sessions_ibfk_2` FOREIGN KEY (`student`) REFERENCES `tbl_students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_students`
--
ALTER TABLE `tbl_students`
  ADD CONSTRAINT `tbl_students_ibfk_1` FOREIGN KEY (`class`) REFERENCES `tbl_classes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_subject_combinations`
--
ALTER TABLE `tbl_subject_combinations`
  ADD CONSTRAINT `tbl_subject_combinations_ibfk_2` FOREIGN KEY (`subject`) REFERENCES `tbl_subjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_subject_combinations_ibfk_3` FOREIGN KEY (`teacher`) REFERENCES `tbl_staff` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
