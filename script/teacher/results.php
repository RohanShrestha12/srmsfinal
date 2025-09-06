<?php
// Set page title and include DataTables
chdir('../');
session_start();
require_once 'db/config.php';
require_once 'db/connection.php';

$page_title = "Examination Results";
$include_datatables = true;

// Check for result data in session
if (
    isset($_SESSION['result__data']['term'], $_SESSION['result__data']['class'], $_SESSION['result__data']['subject']) &&
    !empty($_SESSION['result__data']['term']) &&
    !empty($_SESSION['result__data']['class']) &&
    !empty($_SESSION['result__data']['subject'])
) {
    $term = $_SESSION['result__data']['term'];
    $class = $_SESSION['result__data']['class'];
    $subject = $_SESSION['result__data']['subject'];

    try {
        // Fetch term
        $stmt = $conn->prepare("SELECT * FROM tbl_terms WHERE id = ?");
        $stmt->execute([$term]);
        $term_data = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fetch class
        $stmt = $conn->prepare("SELECT * FROM tbl_classes WHERE id = ?");
        $stmt->execute([$class]);
        $class_data = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fetch subject combination and subject
        $stmt = $conn->prepare("
    SELECT sc.*, s.name AS subject_name
    FROM tbl_subject_combinations sc
    LEFT JOIN tbl_subjects s ON sc.subject = s.id 
    WHERE sc.id = ?
");
        $stmt->execute([$subject]);
        $sub_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$term_data || !$class_data || !$sub_data) {
        unset($_SESSION['result__data']);
        header("location:manage_results.php");
        exit;
    }
     $tit = $sub_data['subject_name'] . ' - ' . $term_data['name'] . ' - ' . $class_data['name'] . ' Examination Results';

    } catch (PDOException $e) {
        echo "Connection failed: " . htmlspecialchars($e->getMessage());
        exit;
    }
} else {
    // Clear any invalid session data
    unset($_SESSION['result__data']);
    header("location:manage_results.php");
    exit;
}
?>

<?php include 'teacher-header.php'; ?>

<div class="app-title">
    <div>
        <h1><i class="bi bi-file-earmark-text me-2"></i><?php echo htmlspecialchars($tit); ?></h1>
        <p>View examination results for the selected criteria.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <div class="tile-title-w-btn">
                <h3 class="title"><i class="bi bi-table me-2"></i>Results Table</h3>
                <p>Student examination results with grades and remarks</p>
            </div>
            <div class="tile-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="srmsTable">
                        <thead>
                            <tr>
                                <th><i class="bi bi-hash me-2"></i>Registration Number</th>
                                <th><i class="bi bi-person me-2"></i>Student Name</th>
                                <th><i class="bi bi-percent me-2"></i>Score</th>
                                <th><i class="bi bi-award me-2"></i>Grade</th>
                                <th><i class="bi bi-chat-text me-2"></i>Remark</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                $stmt = $conn->prepare("
                                    SELECT 
                                        r.student, 
                                        s.fname, s.mname, s.lname,
                                        r.score, 
                                        r.grade, 
                                        r.gpa, 
                                        r.remarks
                                    FROM tbl_exam_results r
                                    LEFT JOIN tbl_students s ON r.student = s.id
                                    WHERE r.class = ? AND r.subject_combination = ? AND r.term = ?
                                    ORDER BY s.fname, s.lname
                                ");
                                $stmt->execute([$class, $subject, $term]);
                                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                if (empty($results)) {
                                    echo '<tr><td colspan="5" class="text-center py-5">
                                            <div class="alert alert-info mb-0">
                                                <i class="bi bi-info-circle me-2"></i>
                                                <strong>No results found</strong>
                                                <br>
                                                <small>No examination results available for the selected criteria.</small>
                                            </div>
                                          </td></tr>';
                                } else {
                                    foreach ($results as $row) {
                                        $fullname = trim($row['fname'] . ' ' . $row['mname'] . ' ' . $row['lname']);
                                        echo '<tr>
                                            <td><strong>' . htmlspecialchars($row['student']) . '</strong></td>
                                            <td>' . htmlspecialchars($fullname) . '</td>
                                            <td><span class="badge bg-primary">' . htmlspecialchars($row['score']) . '%</span></td>
                                            <td><span class="badge bg-success">' . htmlspecialchars($row['grade']) . '</span></td>
                                            <td>' . htmlspecialchars($row['remarks']) . ' (GPA: ' . htmlspecialchars($row['gpa']) . ')</td>
                                        </tr>';
                                    }
                                }

                            } catch (PDOException $e) {
                                echo '<tr><td colspan="5" class="text-center py-5">
                                        <div class="alert alert-danger mb-0">
                                            <i class="bi bi-exclamation-triangle me-2"></i>
                                            <strong>Database Error</strong>
                                            <br>
                                            <small>' . htmlspecialchars($e->getMessage()) . '</small>
                                        </div>
                                      </td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'teacher-footer.php'; ?>
