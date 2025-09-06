<?php
chdir('../');
session_start();
require_once('db/config.php');
require_once('const/school.php');
require_once('const/check_session.php');
if ($res == "1" && $level == "1") {}else{header("location:../");}

// Initialize variables
$tit = 'Student Results';
$page_title = 'Student Results';
$std_data = [];
$term_data = [];
$class_data = [];

if (isset($_SESSION['student_result'])) {
$std = $_SESSION['student_result']['student'];
$term = $_SESSION['student_result']['term'];

try {
// $conn is already available from school.php
// No need to create a new connection

$stmt = $conn->prepare("SELECT * FROM tbl_students WHERE id = ?");
$stmt->execute([$std]);
$std_data = $stmt->fetchAll();

if (empty($std_data)) {
    throw new Exception("Student not found");
}

$stmt = $conn->prepare("SELECT * FROM tbl_terms WHERE id = ?");
$stmt->execute([$term]);
$term_data = $stmt->fetchAll();

if (empty($term_data)) {
    throw new Exception("Term not found");
}

$stmt = $conn->prepare("SELECT * FROM tbl_classes WHERE id = ?");
$stmt->execute([$std_data[0][6]]);
$class_data = $stmt->fetchAll();

if (empty($class_data)) {
    throw new Exception("Class not found");
}

$tit = ''.$std_data[0][1].' '.$std_data[0][2].' '.$std_data[0][3].' ('.$term_data[0][1].' Results)';

// Set page title for header
$page_title = $tit;

}catch(PDOException $e)
{
$error_message = "Database connection failed: " . $e->getMessage();
$tit = 'Error - Database Connection Failed';
$page_title = 'Error';
}catch(Exception $e) {
$error_message = "Error: " . $e->getMessage();
$tit = 'Error - ' . $e->getMessage();
$page_title = 'Error';
}

}else{
header("location:./");
exit();
}

// Include the academic header
require_once('academic/academic-header.php');
?>

<?php if (isset($error_message)): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>Error!</strong> <?php echo htmlspecialchars($error_message); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<?php if (empty($std_data) || empty($term_data) || empty($class_data)): ?>
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Warning!</strong> Required data is missing. Please check your session and try again.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php else: ?>

<div class="app-title">
<div>
<h1><?php echo htmlspecialchars($tit); ?></h1>
<p class="text-muted">Enter Theory and Internal marks for NEB system (Theory: 75/100, Internal: 25/0)</p>
</div>
</div>

<div class="row">
<div class="col-md-12 ">
<div class="tile">
<div class="tile-body">

<form enctype="multipart/form-data" action="academic/core/update_results.php" class="app_frm row" method="POST" autocomplete="OFF">

<?php
$tscore = 0;
$total_theory = 0;
$total_internal = 0;

// Get subject combinations for the student's class
$stmt = $conn->prepare("
    SELECT sc.id, sc.class, sc.subject, s.name, COALESCE(s.has_practical, 0) as has_practical 
    FROM tbl_subject_combinations sc
    LEFT JOIN tbl_subjects s ON sc.subject = s.id
");
$stmt->execute();
$subject_combinations = $stmt->fetchAll();

foreach ($subject_combinations as $key => $row) {
    // Handle serialized class data safely
    $class_data_str = $row[1];
    $class_list = [];
    
    if (!empty($class_data_str)) {
        // Try to unserialize, handle errors gracefully
        $unserialized = @unserialize($class_data_str);
        if ($unserialized !== false && is_array($unserialized)) {
            $class_list = $unserialized;
        } else {
            // If unserialize fails, try to extract class ID manually
            if (preg_match('/s:\d+:"(\d+)"/', $class_data_str, $matches)) {
                $class_list = [$matches[1]];
            }
        }
    }
    
    // Convert student's class to string for comparison
    $student_class = (string)$std_data[0][6];
    
    // Check if this subject combination applies to student's class
    if (in_array($student_class, $class_list)) {
        $subject_code = $row[0];
        $subject_name = $row[3];
        $has_practical = $row[4] ?? 0;
        
        // Default values
        $theory_marks = 0;
        $internal_marks = 0;
        $total_marks = 0;
        $grade = '';
        $gpa = 0;
        $remarks = '';

        // Get existing results
        $stmt = $conn->prepare("
            SELECT theory_marks, internal_marks, total_marks, grade, gpa, remarks, result_status 
            FROM tbl_exam_results 
            WHERE class = ? AND subject_combination = ? AND term = ? AND student = ?
        ");
        $stmt->execute([$std_data[0][6], $row[0], $term, $std]);
        $ex_result = $stmt->fetchAll();

        if (!empty($ex_result)) {
            $theory_marks = $ex_result[0][0] ?? 0;
            $internal_marks = $ex_result[0][1] ?? 0;
            $total_marks = $ex_result[0][2] ?? 0;
            $grade = $ex_result[0][3] ?? '';
            $gpa = $ex_result[0][4] ?? 0;
            $remarks = $ex_result[0][5] ?? '';
            $result_status = $ex_result[0][6] ?? 'FAIL';
        } else {
            // Check for legacy score data
            $stmt = $conn->prepare("SELECT score FROM tbl_exam_results WHERE class = ? AND subject_combination = ? AND term = ? AND student = ?");
            $stmt->execute([$std_data[0][6], $row[0], $term, $std]);
            $legacy_result = $stmt->fetchAll();
            
            if (!empty($legacy_result) && $legacy_result[0][0] > 0) {
                $legacy_score = $legacy_result[0][0];
                // Convert legacy score based on subject type
                if ($has_practical == 1) {
                    $theory_marks = round($legacy_score * 0.75, 1);
                    $internal_marks = round($legacy_score * 0.25, 1);
                } else {
                    $theory_marks = $legacy_score;
                    $internal_marks = 0;
                }
            }
        }

        $tscore += ($theory_marks + $internal_marks);
        $total_theory += $theory_marks;
        $total_internal += $internal_marks;

        // Determine max marks for this subject
        $theory_max = $has_practical == 1 ? 75 : 100;
        $internal_max = $has_practical == 1 ? 25 : 0;
        $theory_pass = $has_practical == 1 ? 26.25 : 35;
        $internal_pass = $has_practical == 1 ? 8.75 : 0;

?>

<div class="mb-3 col-md-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0"><?php echo htmlspecialchars($subject_name); ?></h6>
            <small>
                <?php if ($has_practical == 1): ?>
                    Theory: <?php echo $theory_max; ?> marks, Internal: <?php echo $internal_max; ?> marks
                <?php else: ?>
                    Theory Only: <?php echo $theory_max; ?> marks
                <?php endif; ?>
            </small>
        </div>
        <div class="card-body">
            <!-- Theory Marks -->
            <div class="row mb-2">
                <div class="col-6">
                    <label class="form-label small">Theory Marks</label>
                    <input value="<?php echo htmlspecialchars($theory_marks); ?>" 
                           name="theory_<?php echo htmlspecialchars($row[0]);?>" 
                           class="form-control theory-input" 
                           required 
                           type="number" 
                           min="0" 
                           max="<?php echo $theory_max; ?>" 
                           step="0.25"
                           placeholder="Max: <?php echo $theory_max; ?>"
                           data-subject-id="<?php echo $row[0]; ?>"
                           data-max="<?php echo $theory_max; ?>"
                           data-pass="<?php echo $theory_pass; ?>"
                           oninput="validateTheoryMark(this)">
                    <div class="invalid-feedback theory-error"></div>
                    <small class="text-muted">Pass: <?php echo $theory_pass; ?></small>
                </div>
                
                <!-- Internal/Practical Marks -->
                <div class="col-6">
                    <label class="form-label small">
                        <?php echo $has_practical == 1 ? 'Internal/Practical' : 'Internal'; ?>
                    </label>
                    <?php if ($has_practical == 1): ?>
                        <input value="<?php echo htmlspecialchars($internal_marks); ?>" 
                               name="internal_<?php echo htmlspecialchars($row[0]);?>" 
                               class="form-control internal-input" 
                               required 
                               type="number" 
                               min="0" 
                               max="<?php echo $internal_max; ?>" 
                               step="0.25"
                               placeholder="Max: <?php echo $internal_max; ?>"
                               data-subject-id="<?php echo $row[0]; ?>"
                               data-max="<?php echo $internal_max; ?>"
                               data-pass="<?php echo $internal_pass; ?>"
                               oninput="validateInternalMark(this)">
                        <div class="invalid-feedback internal-error"></div>
                        <small class="text-muted">Pass: <?php echo $internal_pass; ?></small>
                    <?php else: ?>
                        <input value="0" 
                               name="internal_<?php echo htmlspecialchars($row[0]);?>" 
                               class="form-control" 
                               type="number" 
                               readonly
                               style="background-color: #f8f9fa;">
                        <small class="text-muted">No practical marks</small>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Total and Grade Display -->
            <div class="row">
                <div class="col-6">
                    <div class="text-center p-2 border rounded" id="total_<?php echo $row[0]; ?>">
                        <strong>Total: <?php echo $theory_marks + $internal_marks; ?>/100</strong>
                    </div>
                </div>
                <div class="col-6">
                    <div class="text-center p-2 border rounded" id="grade_<?php echo $row[0]; ?>">
                        <?php if (!empty($grade)): ?>
                            <strong><?php echo $grade; ?> (<?php echo $gpa; ?>)</strong><br>
                            <small><?php echo $remarks; ?></small>
                        <?php else: ?>
                            <small class="text-muted">Grade will auto-calculate</small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    }
}
?>

<!-- Summary Card -->
<div class="col-md-12 mb-3">
    <div class="card bg-light">
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-3">
                    <h5 class="text-primary"><?php echo $total_theory; ?></h5>
                    <small>Total Theory Marks</small>
                </div>
                <div class="col-md-3">
                    <h5 class="text-success"><?php echo $total_internal; ?></h5>
                    <small>Total Internal Marks</small>
                </div>
                <div class="col-md-3">
                    <h5 class="text-info"><?php echo $tscore; ?></h5>
                    <small>Grand Total</small>
                </div>
                <div class="col-md-3">
                    <h5 class="text-warning" id="overall-cgpa">-</h5>
                    <small>Estimated CGPA</small>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" name="student" value="<?php echo htmlspecialchars($std); ?>">
<input type="hidden" name="term" value="<?php echo htmlspecialchars($term); ?>">
<input type="hidden" name="class" value="<?php echo htmlspecialchars($std_data[0][6]); ?>">

<div class="col-md-12">
    <div class="d-flex gap-2">
        <button class="btn btn-primary app_btn" type="submit">
            <i class="fa fa-save"></i> Save Results
        </button>
        <?php if ($tscore > 0): ?>
            <a onclick="del('academic/core/drop_results?src=single_results&std=<?php echo htmlspecialchars($std); ?>&class=<?php echo htmlspecialchars($std_data[0][6]); ?>&term=<?php echo htmlspecialchars($term); ?>', 'Delete Results?');" 
               href="javascript:void(0);" 
               class="btn btn-danger">
                <i class="fa fa-trash"></i> Delete Results
            </a>
        <?php endif; ?>
        <a href="academic/save_pdf.php?term=<?php echo htmlspecialchars($term); ?>&std=<?php echo htmlspecialchars($std); ?>" 
           class="btn btn-success" 
           target="_blank">
            <i class="fa fa-download"></i> Download Result PDF
        </a>
    </div>
</div>

</form>

</div>
</div>
</div>
</div>

<?php endif; ?>

<style>
/* Enhanced styling for the new form */
.card-header {
    padding: 0.5rem 1rem;
}

.card-body {
    padding: 1rem;
}

.theory-input, .internal-input {
    font-size: 0.9rem;
}

.theory-input.is-invalid, .internal-input.is-invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.theory-input.is-valid, .internal-input.is-valid {
    border-color: #198754;
    box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
}

.theory-error, .internal-error {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.75em;
    color: #dc3545;
}

/* Mark type indicators */
.theory-input {
    border-left: 4px solid #0d6efd;
}

.internal-input {
    border-left: 4px solid #198754;
}

/* Total and grade display */
#total_display, #grade_display {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 0.375rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .col-md-4 {
        flex: 0 0 100%;
        max-width: 100%;
        margin-bottom: 1rem;
    }
}

/* Animation for updates */
.mark-updated {
    animation: highlight 0.5s ease-in-out;
}

@keyframes highlight {
    0% { background-color: #fff3cd; }
    100% { background-color: transparent; }
}
</style>

<script>
// Validation functions for theory marks
function validateTheoryMark(input) {
    const mark = parseFloat(input.value);
    const max = parseFloat(input.dataset.max);
    const pass = parseFloat(input.dataset.pass);
    const subjectId = input.dataset.subjectId;
    const errorDiv = input.parentNode.querySelector('.theory-error');
    
    // Clear previous errors
    input.classList.remove('is-invalid', 'is-valid');
    errorDiv.textContent = '';
    
    if (input.value === '') {
        return true;
    }
    
    if (isNaN(mark)) {
        input.classList.add('is-invalid');
        errorDiv.textContent = 'Please enter a valid number';
        return false;
    }
    
    if (mark < 0 || mark > max) {
        input.classList.add('is-invalid');
        errorDiv.textContent = `Theory marks must be between 0 and ${max}`;
        return false;
    }
    
    if (mark < pass) {
        input.classList.add('is-invalid');
        errorDiv.textContent = `Below pass marks (${pass})`;
    } else {
        input.classList.add('is-valid');
    }
    
    updateTotalAndGrade(subjectId);
    return true;
}

// Validation functions for internal marks
function validateInternalMark(input) {
    const mark = parseFloat(input.value);
    const max = parseFloat(input.dataset.max);
    const pass = parseFloat(input.dataset.pass);
    const subjectId = input.dataset.subjectId;
    const errorDiv = input.parentNode.querySelector('.internal-error');
    
    // Clear previous errors
    input.classList.remove('is-invalid', 'is-valid');
    errorDiv.textContent = '';
    
    if (input.value === '') {
        return true;
    }
    
    if (isNaN(mark)) {
        input.classList.add('is-invalid');
        errorDiv.textContent = 'Please enter a valid number';
        return false;
    }
    
    if (mark < 0 || mark > max) {
        input.classList.add('is-invalid');
        errorDiv.textContent = `Internal marks must be between 0 and ${max}`;
        return false;
    }
    
    if (mark < pass) {
        input.classList.add('is-invalid');
        errorDiv.textContent = `Below pass marks (${pass})`;
    } else {
        input.classList.add('is-valid');
    }
    
    updateTotalAndGrade(subjectId);
    return true;
}

// Update total marks and grade display
function updateTotalAndGrade(subjectId) {
    const theoryInput = document.querySelector(`input[name="theory_${subjectId}"]`);
    const internalInput = document.querySelector(`input[name="internal_${subjectId}"]`);
    const totalDiv = document.getElementById(`total_${subjectId}`);
    const gradeDiv = document.getElementById(`grade_${subjectId}`);
    
    if (!theoryInput || !internalInput || !totalDiv || !gradeDiv) return;
    
    const theory = parseFloat(theoryInput.value) || 0;
    const internal = parseFloat(internalInput.value) || 0;
    const total = theory + internal;
    
    // Update total display
    totalDiv.innerHTML = `<strong>Total: ${total}/100</strong>`;
    totalDiv.classList.add('mark-updated');
    setTimeout(() => totalDiv.classList.remove('mark-updated'), 500);
    
    // Calculate and display grade
    let grade = 'NG';
    let gpa = 0;
    let remarks = 'Not Graded';
    
    // Check pass conditions
    const theoryMax = parseFloat(theoryInput.dataset.max);
    const theoryPass = parseFloat(theoryInput.dataset.pass);
    const internalPass = parseFloat(internalInput.dataset.pass || 0);
    
    if (theory >= theoryPass && internal >= internalPass && total >= 35) {
        if (total >= 90) { grade = 'A+'; gpa = 4.0; remarks = 'Outstanding'; }
        else if (total >= 80) { grade = 'A'; gpa = 3.6; remarks = 'Excellent'; }
        else if (total >= 70) { grade = 'B+'; gpa = 3.2; remarks = 'Very Good'; }
        else if (total >= 60) { grade = 'B'; gpa = 2.8; remarks = 'Good'; }
        else if (total >= 50) { grade = 'C+'; gpa = 2.4; remarks = 'Satisfactory'; }
        else if (total >= 40) { grade = 'C'; gpa = 2.0; remarks = 'Acceptable'; }
        else if (total >= 35) { grade = 'D+'; gpa = 1.6; remarks = 'Partially Acceptable'; }
    }
    
    // Update grade display
    gradeDiv.innerHTML = `<strong>${grade} (${gpa})</strong><br><small>${remarks}</small>`;
    gradeDiv.className = `text-center p-2 border rounded ${grade === 'NG' ? 'bg-danger text-white' : 'bg-success text-white'}`;
    
    updateOverallCGPA();
}

// Calculate overall CGPA
function updateOverallCGPA() {
    const theoryInputs = document.querySelectorAll('.theory-input');
    const internalInputs = document.querySelectorAll('.internal-input');
    
    let totalGPA = 0;
    let subjectCount = 0;
    
    theoryInputs.forEach((theoryInput, index) => {
        const internalInput = internalInputs[index];
        const theory = parseFloat(theoryInput.value) || 0;
        const internal = parseFloat(internalInput.value) || 0;
        const total = theory + internal;
        
        const theoryPass = parseFloat(theoryInput.dataset.pass);
        const internalPass = parseFloat(internalInput.dataset.pass || 0);
        
        if (theory >= theoryPass && internal >= internalPass && total >= 35) {
            let gpa = 0;
            if (total >= 90) gpa = 4.0;
            else if (total >= 80) gpa = 3.6;
            else if (total >= 70) gpa = 3.2;
            else if (total >= 60) gpa = 2.8;
            else if (total >= 50) gpa = 2.4;
            else if (total >= 40) gpa = 2.0;
            else if (total >= 35) gpa = 1.6;
            
            totalGPA += gpa;
            subjectCount++;
        }
    });
    
    const cgpa = subjectCount > 0 ? (totalGPA / subjectCount).toFixed(2) : 0;
    document.getElementById('overall-cgpa').textContent = cgpa;
}

// Form validation before submission
document.querySelector('.app_frm').addEventListener('submit', function(e) {
    const theoryInputs = document.querySelectorAll('.theory-input');
    const internalInputs = document.querySelectorAll('.internal-input');
    let isValid = true;
    
    theoryInputs.forEach(input => {
        if (!validateTheoryMark(input)) {
            isValid = false;
        }
    });
    
    internalInputs.forEach(input => {
        if (!validateInternalMark(input)) {
            isValid = false;
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Please fix the errors before submitting the form.',
            confirmButtonColor: '#3085d6'
        });
        return false;
    }
    
    // Show loading state
    Swal.fire({
        title: 'Saving Results...',
        text: 'Please wait while we save the results.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners
    document.querySelectorAll('.theory-input').forEach(input => {
        input.addEventListener('input', function() { validateTheoryMark(this); });
        input.addEventListener('blur', function() { validateTheoryMark(this); });
    });
    
    document.querySelectorAll('.internal-input').forEach(input => {
        input.addEventListener('input', function() { validateInternalMark(this); });
        input.addEventListener('blur', function() { validateInternalMark(this); });
    });
    
    // Initial CGPA calculation
    updateOverallCGPA();
});
</script>

<?php
// Include the academic footer
require_once('academic/academic-footer.php');
?>