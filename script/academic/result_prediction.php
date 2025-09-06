<?php
/**
 * FIXED Result Prediction using Linear Regression Algorithm
 * This file contains corrected functions to predict student results based on historical data
 */

/**
 * Calculate grade from percentage score
 * @param float $percentage
 * @return array ['grade', 'remark']
 */
function calculateGrade($percentage) {
    $grade_ranges = [
        ['grade' => 'A+', 'min' => 90, 'max' => 100, 'remark' => 'Outstanding'],
        ['grade' => 'A', 'min' => 80, 'max' => 89, 'remark' => 'Excellent'],
        ['grade' => 'B+', 'min' => 70, 'max' => 79, 'remark' => 'Very Good'],
        ['grade' => 'B', 'min' => 60, 'max' => 69, 'remark' => 'Good'],
        ['grade' => 'C+', 'min' => 50, 'max' => 59, 'remark' => 'Satisfactory'],
        ['grade' => 'C', 'min' => 40, 'max' => 49, 'remark' => 'Acceptable'],
        ['grade' => 'D', 'min' => 30, 'max' => 39, 'remark' => 'Partially Acceptable'],
        ['grade' => 'NG', 'min' => 0, 'max' => 29, 'remark' => 'Failed']
    ];
    
    foreach ($grade_ranges as $range) {
        if ($percentage >= $range['min'] && $percentage <= $range['max']) {
            return ['grade' => $range['grade'], 'remark' => $range['remark']];
        }
    }
    
    return ['grade' => 'NG', 'remark' => 'Failed'];
}

/**
 * Simple Linear Regression Algorithm
 * @param array $x_values - Independent variable (time periods/terms)
 * @param array $y_values - Dependent variable (scores)
 * @return array ['slope', 'intercept']
 */
function linearRegression($x_values, $y_values) {
    $n = count($x_values);
    
    if ($n < 2) {
        return ['slope' => 0, 'intercept' => 0];
    }
    
    // Calculate means
    $x_mean = array_sum($x_values) / $n;
    $y_mean = array_sum($y_values) / $n;
    
    // Calculate slope and intercept
    $numerator = 0;
    $denominator = 0;
    
    for ($i = 0; $i < $n; $i++) {
        $numerator += ($x_values[$i] - $x_mean) * ($y_values[$i] - $y_mean);
        $denominator += ($x_values[$i] - $x_mean) * ($x_values[$i] - $x_mean);
    }
    
    if ($denominator == 0) {
        return ['slope' => 0, 'intercept' => $y_mean];
    }
    
    $slope = $numerator / $denominator;
    $intercept = $y_mean - ($slope * $x_mean);
    
    return [
        'slope' => $slope,
        'intercept' => $intercept
    ];
}

/**
 * Predict final result for Class 11 student (FIXED)
 * @param PDO $conn - Database connection
 * @param string $student_id
 * @return array
 */
function predictClass11FinalResult($conn, $student_id) {
    try {
        // FIXED: Get student's class 11 results (class ID = 2 for Eleven Management)
        $stmt = $conn->prepare("
            SELECT 
                er.score, 
                t.name as term_name, 
                t.id as term_id,
                s.name as subject_name
            FROM tbl_exam_results er
            JOIN tbl_terms t ON er.term = t.id
            JOIN tbl_subject_combinations sc ON er.subject_combination = sc.id
            JOIN tbl_subjects s ON sc.subject = s.id
            WHERE er.student = ? AND er.class = 2
            ORDER BY t.id ASC
        ");
        $stmt->execute([$student_id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($results)) {
            return [
                'available' => false,
                'message' => 'No Class 11 results available for prediction',
                'prediction' => null
            ];
        }
        
        // Group results by term and calculate averages
        $term_results = [];
        foreach ($results as $result) {
            $term_id = $result['term_id'];
            if (!isset($term_results[$term_id])) {
                $term_results[$term_id] = [];
            }
            $term_results[$term_id][] = $result['score'];
        }
        
        // Calculate average for each term
        $term_averages = [];
        $term_numbers = [];
        $counter = 1;
        
        foreach ($term_results as $term_id => $scores) {
            $average = array_sum($scores) / count($scores);
            $term_averages[] = $average;
            $term_numbers[] = $counter;
            $counter++;
        }
        
        // If only first term is available, use trend prediction
        if (count($term_averages) == 1) {
            $first_term_avg = $term_averages[0];
            
            // Conservative prediction: assume slight improvement or stability
            $predicted_final = min(100, max(0, $first_term_avg + rand(-3, 5)));
            
            $grade_info = calculateGrade($predicted_final);
            
            return [
                'available' => true,
                'message' => 'Prediction based on first term performance with trend analysis',
                'prediction' => [
                    'percentage' => round($predicted_final, 2),
                    'grade' => $grade_info['grade'],
                    'remark' => $grade_info['remark'],
                    'confidence' => 'Medium',
                    'method' => 'Single term extrapolation'
                ]
            ];
        }
        
        // If multiple terms available, use linear regression
        $regression = linearRegression($term_numbers, $term_averages);
        
        // Predict final result (next term)
        $next_term = count($term_averages) + 1;
        $predicted_final = $regression['slope'] * $next_term + $regression['intercept'];
        $predicted_final = max(0, min(100, $predicted_final)); // Clamp between 0-100
        
        $grade_info = calculateGrade($predicted_final);
        
        // Determine confidence based on trend consistency
        $confidence = 'High';
        if (abs($regression['slope']) > 10) {
            $confidence = 'Medium'; // Large changes might be less predictable
        }
        
        return [
            'available' => true,
            'message' => 'Prediction based on Class 11 performance trend using ' . count($term_averages) . ' terms',
            'prediction' => [
                'percentage' => round($predicted_final, 2),
                'grade' => $grade_info['grade'],
                'remark' => $grade_info['remark'],
                'confidence' => $confidence,
                'method' => 'Linear regression on ' . count($term_averages) . ' terms'
            ]
        ];
        
    } catch (PDOException $e) {
        return [
            'available' => false,
            'message' => 'Error calculating prediction: ' . $e->getMessage(),
            'prediction' => null
        ];
    }
}

/**
 * Predict final result for Class 12 student (FIXED)
 * @param PDO $conn - Database connection
 * @param string $student_id
 * @return array
 */
function predictClass12FinalResult($conn, $student_id) {
    try {
        // Get student's class 11 results first (class ID = 2)
        $stmt = $conn->prepare("
            SELECT 
                er.score, 
                t.name as term_name, 
                t.id as term_id
            FROM tbl_exam_results er
            JOIN tbl_terms t ON er.term = t.id
            JOIN tbl_subject_combinations sc ON er.subject_combination = sc.id
            WHERE er.student = ? AND er.class = 2
            ORDER BY t.id ASC
        ");
        $stmt->execute([$student_id]);
        $class11_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get student's class 12 results (class ID = 1)
        $stmt = $conn->prepare("
            SELECT 
                er.score, 
                t.name as term_name, 
                t.id as term_id
            FROM tbl_exam_results er
            JOIN tbl_terms t ON er.term = t.id
            JOIN tbl_subject_combinations sc ON er.subject_combination = sc.id
            WHERE er.student = ? AND er.class = 1
            ORDER BY t.id ASC
        ");
        $stmt->execute([$student_id]);
        $class12_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate class 11 final average
        $class11_final = 0;
        if (!empty($class11_results)) {
            $class11_scores = array_column($class11_results, 'score');
            $class11_final = array_sum($class11_scores) / count($class11_scores);
        }
        
        // If no class 12 results yet, predict based on class 11
        if (empty($class12_results)) {
            if ($class11_final == 0) {
                return [
                    'available' => false,
                    'message' => 'No results available for prediction',
                    'prediction' => null
                ];
            }
            
            // Predict based on class 11 performance with slight variation
            $predicted_final = min(100, max(0, $class11_final + rand(-2, 4)));
            
            $grade_info = calculateGrade($predicted_final);
            
            return [
                'available' => true,
                'message' => 'Prediction based on Class 11 performance',
                'prediction' => [
                    'percentage' => round($predicted_final, 2),
                    'grade' => $grade_info['grade'],
                    'remark' => $grade_info['remark'],
                    'confidence' => 'Medium',
                    'method' => 'Class 11 performance extrapolation'
                ]
            ];
        }
        
        // Calculate class 12 term averages
        $class12_term_results = [];
        foreach ($class12_results as $result) {
            $term_id = $result['term_id'];
            if (!isset($class12_term_results[$term_id])) {
                $class12_term_results[$term_id] = [];
            }
            $class12_term_results[$term_id][] = $result['score'];
        }
        
        $class12_averages = [];
        $term_numbers = [];
        $counter = 1;
        
        foreach ($class12_term_results as $term_id => $scores) {
            $average = array_sum($scores) / count($scores);
            $class12_averages[] = $average;
            $term_numbers[] = $counter;
            $counter++;
        }
        
        // If only one class 12 term available
        if (count($class12_averages) == 1) {
            $current_term_avg = $class12_averages[0];
            
            // Weighted prediction: 30% class 11 + 70% current class 12 term
            $predicted_final = ($class11_final * 0.3) + ($current_term_avg * 0.7);
            
            $grade_info = calculateGrade($predicted_final);
            
            return [
                'available' => true,
                'message' => 'Prediction based on Class 11 and current Class 12 term',
                'prediction' => [
                    'percentage' => round($predicted_final, 2),
                    'grade' => $grade_info['grade'],
                    'remark' => $grade_info['remark'],
                    'confidence' => 'Medium',
                    'method' => 'Weighted average (Class 11 + Current term)'
                ]
            ];
        }
        
        // Multiple class 12 terms available, use linear regression
        $regression = linearRegression($term_numbers, $class12_averages);
        
        // Predict next term
        $next_term = count($class12_averages) + 1;
        $predicted_final = $regression['slope'] * $next_term + $regression['intercept'];
        $predicted_final = max(0, min(100, $predicted_final)); // Clamp between 0-100
        
        $grade_info = calculateGrade($predicted_final);
        
        // Determine confidence
        $confidence = 'High';
        if (abs($regression['slope']) > 15) {
            $confidence = 'Medium';
        }
        
        return [
            'available' => true,
            'message' => 'Prediction based on Class 12 performance trend using ' . count($class12_averages) . ' terms',
            'prediction' => [
                'percentage' => round($predicted_final, 2),
                'grade' => $grade_info['grade'],
                'remark' => $grade_info['remark'],
                'confidence' => $confidence,
                'method' => 'Linear regression on Class 12 data'
            ]
        ];
        
    } catch (PDOException $e) {
        return [
            'available' => false,
            'message' => 'Error calculating prediction: ' . $e->getMessage(),
            'prediction' => null
        ];
    }
}

/**
 * Get student's available results for display (FIXED)
 * @param PDO $conn - Database connection
 * @param string $student_id
 * @return array
 */
function getStudentResults($conn, $student_id) {
    try {
        $stmt = $conn->prepare("
            SELECT 
                er.score,
                t.name as term_name,
                t.id as term_id,
                c.name as class_name,
                c.id as class_id,
                s.name as subject_name
            FROM tbl_exam_results er
            JOIN tbl_terms t ON er.term = t.id
            JOIN tbl_classes c ON er.class = c.id
            JOIN tbl_subject_combinations sc ON er.subject_combination = sc.id
            JOIN tbl_subjects s ON sc.subject = s.id
            WHERE er.student = ?
            ORDER BY c.id DESC, t.id ASC, s.name ASC
        ");
        $stmt->execute([$student_id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Group results by class and term
        $organized_results = [];
        foreach ($results as $result) {
            $class_id = $result['class_id'];
            $term_id = $result['term_id'];
            
            if (!isset($organized_results[$class_id])) {
                $organized_results[$class_id] = [
                    'class_name' => $result['class_name'],
                    'terms' => []
                ];
            }
            
            if (!isset($organized_results[$class_id]['terms'][$term_id])) {
                $organized_results[$class_id]['terms'][$term_id] = [
                    'term_name' => $result['term_name'],
                    'subjects' => []
                ];
            }
            
            $organized_results[$class_id]['terms'][$term_id]['subjects'][] = [
                'subject' => $result['subject_name'],
                'score' => $result['score']
            ];
        }
        
        // Calculate averages for each term
        foreach ($organized_results as $class_id => &$class_data) {
            foreach ($class_data['terms'] as $term_id => &$term_data) {
                $scores = array_column($term_data['subjects'], 'score');
                if (!empty($scores)) {
                    $term_data['average'] = round(array_sum($scores) / count($scores), 2);
                    $grade_info = calculateGrade($term_data['average']);
                    $term_data['grade'] = $grade_info['grade'];
                    $term_data['remark'] = $grade_info['remark'];
                } else {
                    $term_data['average'] = 0;
                    $term_data['grade'] = 'NG';
                    $term_data['remark'] = 'No data';
                }
            }
        }
        
        return $organized_results;
        
    } catch (PDOException $e) {
        error_log("Error in getStudentResults: " . $e->getMessage());
        return [];
    }
}

/**
 * Debug function to check what data is available
 * @param PDO $conn - Database connection
 * @param string $student_id
 * @return array
 */
function debugStudentData($conn, $student_id) {
    try {
        // Check student exists
        $stmt = $conn->prepare("SELECT id, fname, lname, class FROM tbl_students WHERE id = ?");
        $stmt->execute([$student_id]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$student) {
            return ['error' => 'Student not found'];
        }
        
        // Check all exam results
        $stmt = $conn->prepare("
            SELECT 
                er.id,
                er.student,
                er.class as class_id,
                c.name as class_name,
                er.term as term_id,
                t.name as term_name,
                er.score,
                er.subject_combination,
                s.name as subject_name
            FROM tbl_exam_results er
            LEFT JOIN tbl_classes c ON er.class = c.id
            LEFT JOIN tbl_terms t ON er.term = t.id
            LEFT JOIN tbl_subject_combinations sc ON er.subject_combination = sc.id
            LEFT JOIN tbl_subjects s ON sc.subject = s.id
            WHERE er.student = ?
            ORDER BY er.class DESC, er.term ASC
        ");
        $stmt->execute([$student_id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'student' => $student,
            'total_results' => count($results),
            'results' => $results
        ];
        
    } catch (PDOException $e) {
        return ['error' => $e->getMessage()];
    }
}
?>