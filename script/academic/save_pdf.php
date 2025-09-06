<?php
// Start output buffering immediately to prevent any output
ob_start();

chdir('../');
session_start();
require_once('db/config.php');
require_once('const/school.php');
require_once('const/check_session.php');
require_once('tcpdf/tcpdf.php');
require_once('const/calculations.php');

if ($res == "1" && $level == "1" && isset($_GET['term'])) {} else { 
    ob_end_clean();
    header("location:../"); 
    exit();
}

$term = $_GET['term'];
$std = $_GET['std'];

try {
    // Get student information
    $stmt = $conn->prepare("SELECT * FROM tbl_students WHERE id = ?");
    $stmt->execute([$std]);
    $result = $stmt->fetchAll();

    foreach ($result as $value) {
        $dob_bs = $value[5] ?? '';
        $symbol_no = $value[6] ?? '';
        $student_name = $value[1] . ' ' . ($value[2] ? $value[2] . ' ' : '') . $value[3];
    }

    $stmt = $conn->prepare("SELECT * FROM tbl_terms WHERE id = ?");
    $stmt->execute([$term]);
    $result = $stmt->fetchAll();

    if (count($result) < 1) {
        header("location:./");
    }

    $title = $result[0][1] . ' Examination Result';
    $exam_year = date('Y');

    $stmt = $conn->prepare("SELECT * FROM tbl_exam_results LEFT JOIN tbl_classes ON tbl_exam_results.class = tbl_classes.id WHERE tbl_exam_results.term = ? AND tbl_exam_results.student = ?");
    $stmt->execute([$term, $std]);
    $result2 = $stmt->fetchAll();

    if (count($result2) < 1) {
        ob_end_clean();
        header("location:./");
        exit();
    }
} catch (PDOException $e) {
    ob_end_clean();
    die("Connection failed: " . $e->getMessage());
}

$pdf = new TCPDF('P', 'mm', array(210, 297), true, 'UTF-8', false);
$pdf->SetMargins(19.3, 5.08, 22.35);
$pdf->SetAutoPageBreak(TRUE, 5.84);
$pdf->SetCellHeightRatio(1.5);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->setFontSubsetting(true);
$pdf->SetFont('helvetica', '', 11, '', true);

$pdf->AddPage();

// Registration No.
$html = '<div style="text-align:right; font-family:calibri; font-size:11px; font-weight:bold; width:170.43mm; padding-right:50px; margin-bottom:6px;">
<u>REGISTRATION NO.: ' . $std . '</u> <br> <br> <br> <br>
</div>';
$pdf->writeHTMLCell(170.43, 0, '', '', $html, 0, 1, 0, true, 'C', true);
$pdf->Ln(3);

// Grade Sheet Header with student name
$html = '
<div style="font-size:11px; font-weight:bold; font-family:helvetica; text-align:left; width:0cm;">
  THE FOLLOWING ARE THE GRADE(S) SECURED BY: <u>' . strtoupper($student_name) . '</u>
  <div style=""></div>
  DATE OF BIRTH: <u>' . $dob_bs . '</u> B.S. SYMBOL NO: <u>' . $symbol_no . '</u> <br><br> GRADE XI IN THE 
  ANNUAL EXAMINATION CONDUCTED IN ' . $exam_year . ' A.D. ARE GIVEN BELOW.
</div>';

$pdf->writeHTMLCell(170.43, 0, '', '', $html, 0, 1, 0, true, 'L', true);
$pdf->Ln(2);

// Updated Table Header with new format
$html = '<table border="1" cellpadding="1.02" cellspacing="0" style="font-size:10px; border-collapse:collapse;" width="170.43mm">
<tr>
<th width="20mm" style="text-align:center; font-weight:bold; vertical-align:middle;">SUBJECT<br>CODE</th>
<th width="60mm" style="text-align:center; font-weight:bold; vertical-align:middle;">SUBJECTS</th>
<th width="15mm" style="text-align:center; font-weight:bold; vertical-align:middle;">CREDIT<br>HOUR</th>
<th width="15mm" style="text-align:center; font-weight:bold; vertical-align:middle;">GRADE</th>
<th width="15mm" style="text-align:center; font-weight:bold; vertical-align:middle;">GRADE<br>POINT</th>
<th width="15mm" style="text-align:center; font-weight:bold; vertical-align:middle;">FINAL<br>GRADE</th>
<th width="20mm" style="text-align:center; font-weight:bold; vertical-align:middle;">REMARKS</th>
</tr>';

// Get subject combinations for the student's class
$stmt = $conn->prepare("
    SELECT sc.id, sc.class, sc.subject, s.name, COALESCE(s.has_practical, 0) as has_practical 
    FROM tbl_subject_combinations sc
    LEFT JOIN tbl_subjects s ON sc.subject = s.id
");
$stmt->execute();
$subject_combinations = $stmt->fetchAll();

$total_gpa = 0;
$subject_count = 0;
$all_passed = true;

// Get student's class first
$stmt_student = $conn->prepare("SELECT class FROM tbl_students WHERE id = ?");
$stmt_student->execute([$std]);
$student_data = $stmt_student->fetch();
$student_class = $student_data[0];

// Simple approach: Get all exam results for this student and term
$stmt = $conn->prepare("
    SELECT 
        er.theory_marks,
        er.internal_marks, 
        er.total_marks,
        er.grade,
        er.gpa,
        er.remarks,
        er.result_status,
        er.score,
        sc.id as subject_combination_id,
        s.name as subject_name,
        s.has_practical
    FROM tbl_exam_results er
    JOIN tbl_subject_combinations sc ON er.subject_combination = sc.id
    JOIN tbl_subjects s ON sc.subject = s.id
    WHERE er.student = ? AND er.term = ?
    ORDER BY s.id
");
$stmt->execute([$std, $term]);
$exam_results = $stmt->fetchAll();

$total_gpa = 0;
$subject_count = 0;
$all_passed = true;

// Process each exam result
foreach ($exam_results as $row) {
    $subject_combination_id = $row['subject_combination_id'];
    $subject_name = $row['subject_name'];
    $has_practical = $row['has_practical'] ?? 0;
    $credit_hour = 3;
    
    $theory_marks = $row['theory_marks'] ?? 0;
    $internal_marks = $row['internal_marks'] ?? 0;
    $total_marks = $row['total_marks'] ?? 0;
    $grade = $row['grade'] ?? 'NG';
    $gpa_value = $row['gpa'] ?? 0;
    $remark = $row['remarks'] ?? 'Not Graded';
    $status = $row['result_status'] ?? 'FAIL';
    $final_grade = $grade;
    
    // If total_marks is 0 but score exists, use score for display
    if ($total_marks == 0 && $row['score'] > 0) {
        $total_marks = $row['score'];
    }
    
    if ($status == 'PASS' && $gpa_value > 0) {
        $total_gpa += $gpa_value;
        $subject_count++;
    } else {
        $all_passed = false;
    }

    // Add row to table
    $html .= '<tr>
    <td style="text-align:center; vertical-align:middle; height:20px">' . $subject_combination_id . '</td>
    <td style="text-align:left; vertical-align:middle; height:20px; padding-left:3px;">' . htmlspecialchars($subject_name) . '</td>
    <td style="text-align:center; vertical-align:middle; height:20px">' . $credit_hour . '</td>
    <td style="text-align:center; vertical-align:middle; height:20px">' . $grade . '</td>
    <td style="text-align:center; vertical-align:middle; height:20px">' . $gpa_value . '</td>
    <td style="text-align:center; vertical-align:middle; height:20px">' . $final_grade . '</td>
    <td style="text-align:center; vertical-align:middle; height:20px">' . htmlspecialchars($remark) . '</td>
    </tr>';
}

// Calculate CGPA
$cgpa = $subject_count > 0 ? round($total_gpa / $subject_count, 2) : 0;
$overall_result = $all_passed && $cgpa >= 2.0 ? "PROMOTED" : "NOT PROMOTED";

// Add CGPA and result summary row
$html .= '<tr>
<td colspan="4" style="text-align:right; font-weight:bold; height:20px; padding-right:5px;">CGPA:</td>
<td style="text-align:center; font-weight:bold; height:20px">' . $cgpa . '</td>
<td style="text-align:center; font-weight:bold; height:20px">' . $overall_result . '</td>
<td style="text-align:center; height:20px"></td>
</tr>';

$html .= '<tr><td colspan="7" style="text-align:center; height:20px"><b>EXTRA CREDIT SUBJECT</b></td></tr>';
$html .= '</table>';

$pdf->writeHTMLCell(170.43, 0, '', '', $html, 0, 1, 0, true, 'L', true);
$pdf->Ln(5);

$date_of_issue = date('g:i A +0545, l, F j, Y');
$html = '<table width="100%">
<tr>
<td width="33%" style="text-align:center; font-size:11px; font-weight:bold;">PREPARED BY: _____________________</td>
<td width="33%" style="text-align:center; font-size:11px; font-weight:bold;">CHECKED BY: _____________________</td>
<td width="33%" style="text-align:center; font-size:11px; font-weight:bold;">DATE OF ISSUE: ' . $date_of_issue . '</td>
</tr>
<tr>
<td colspan="2"></td>
<td style="text-align:center; font-size:11px; font-weight:bold;">PRINCIPAL: _____________________</td>
</tr>
</table>';

$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, 'C', true);
$pdf->Ln(5);

// Updated notes for NEB system
$html = '<p style="text-align:center; font-size:10px;">NOTE: ONE CREDIT HOUR EQUALS TO 32 WORKING HOURS</p>
<p style="text-align:center; font-size:10px;">GRADE POINT AVERAGE (GPA) SCALE: A+ = 4.0, A = 3.6, B+ = 3.2, B = 2.8, C+ = 2.4, C = 2.0, D+ = 1.6, NG = 0.0</p>
<p style="text-align:center; font-size:10px;">CUMULATIVE GRADE POINT AVERAGE (CGPA) IS THE AVERAGE OF ALL GRADE POINTS</p>
<p style="text-align:center; font-size:10px;">MINIMUM CGPA REQUIRED FOR PROMOTION: 2.0</p>
<p style="text-align:center; font-size:10px;">ABS = ABSENT  NG = NOT GRADED  CGPA = CUMULATIVE GRADE POINT AVERAGE</p>';

$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, 'C', true);

// Clean any remaining output buffer content
ob_end_clean();

// Output the PDF
$pdf->Output($title . '.pdf', 'I');
exit();
?>