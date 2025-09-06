<?php
chdir('../../');
session_start();
require_once('db/config.php');
require_once('const/school.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

$std = $_POST['student'];
$term = $_POST['term'];
$class = $_POST['class'];

try {
    // Group theory and internal marks by subject
    $subject_marks = [];
    
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'theory_') === 0) {
            $subject_id = str_replace('theory_', '', $key);
            $subject_marks[$subject_id]['theory'] = floatval($value);
        } elseif (strpos($key, 'internal_') === 0) {
            $subject_id = str_replace('internal_', '', $key);
            $subject_marks[$subject_id]['internal'] = floatval($value);
        }
    }
    
    // Process each subject's marks
    foreach ($subject_marks as $subject_combination_id => $marks) {
        $theory_marks = $marks['theory'] ?? 0;
        $internal_marks = $marks['internal'] ?? 0;
        $total_score = $theory_marks + $internal_marks;
        
        // Validate that this subject_combination exists
        $stmt = $conn->prepare("SELECT id FROM tbl_subject_combinations WHERE id = ?");
        $stmt->execute([$subject_combination_id]);
        $combination_exists = $stmt->fetch();
        
        if (!$combination_exists) {
            error_log("Subject combination ID not found: $subject_combination_id");
            continue;
        }
        
        // Check if result already exists
        $stmt = $conn->prepare("SELECT id FROM tbl_exam_results WHERE student = ? AND class = ? AND subject_combination = ? AND term = ?");
        $stmt->execute([$std, $class, $subject_combination_id, $term]);
        $existing_result = $stmt->fetch();
        
        if (!$existing_result) {
            // Insert new result
            $stmt = $conn->prepare("
                INSERT INTO tbl_exam_results (student, class, subject_combination, term, score, theory_marks, internal_marks) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$std, $class, $subject_combination_id, $term, $total_score, $theory_marks, $internal_marks]);
        } else {
            // Update existing result
            $stmt = $conn->prepare("
                UPDATE tbl_exam_results 
                SET score = ?, theory_marks = ?, internal_marks = ? 
                WHERE student = ? AND class = ? AND subject_combination = ? AND term = ?
            ");
            $stmt->execute([$total_score, $theory_marks, $internal_marks, $std, $class, $subject_combination_id, $term]);
        }
    }
    
    $_SESSION['reply'] = array(array("success", 'Results updated successfully'));
    header("location:../single_results.php");

} catch(PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $_SESSION['reply'] = array(array("error", 'Database error occurred: ' . $e->getMessage()));
    header("location:../single_results.php");
} catch(Exception $e) {
    error_log("General error: " . $e->getMessage());
    $_SESSION['reply'] = array(array("error", 'An error occurred: ' . $e->getMessage()));
    header("location:../single_results.php");
}

} else {
    header("location:../");
}
?>