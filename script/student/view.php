<?php
chdir('../');
session_start();
require_once('db/config.php');
require_once('const/school.php');
require_once('const/check_session.php');
if ($res == "1" && $level == "3") {
} else {
    header("location:../");
}

// Set page title
$page_title = "My Profile";

// Include the student header
include('student-header.php');
?>

<div class="app-title">
    <div>
        <h1><i class="bi bi-person-circle me-2"></i>My Profile</h1>
        <p>View and manage your student profile information</p>
    </div>
</div>

<div class="row">
    <!-- Profile Card -->
    <div class="col-md-4 mb-4">
        <div class="dashboard-widget">
            <div class="widget-header text-center">
                <h5><i class="bi bi-person-circle me-2"></i>Profile Photo</h5>
            </div>
            <div class="widget-content text-center">
                <div class="profile-photo-container mb-3">
                    <?php
                    if ($display_image == "DEFAULT" || empty($display_image)) {
                        echo '<img src="images/students/' . $gender . '.png" class="profile-photo-large" alt="Profile Photo">';
                    } else {
                        echo '<img src="images/students/' . $display_image . '" class="profile-photo-large" alt="Profile Photo">';
                    }
                    ?>
                </div>
                <h5 class="mb-1"><?php echo $fname . ' ' . $lname; ?></h5>
                <p class="text-muted mb-0">Student ID: <?php echo $account_id; ?></p>
                <div class="mt-3">
                    <?php
                    try {
                        $stmt = $conn->prepare("SELECT name FROM tbl_classes WHERE id = ?");
                        $stmt->execute([$class]);
                        $class_result = $stmt->fetch();
                        $class_name = $class_result ? $class_result['name'] : 'Class ' . $class;
                    } catch (PDOException $e) {
                        $class_name = 'Class ' . $class;
                    }
                    ?>
                    <span class="badge bg-primary"><?php echo $class_name; ?></span>
                    <span class="badge bg-secondary"><?php echo ucfirst($gender); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Details -->
    <div class="col-md-8">
        <div class="dashboard-widget">
            <div class="widget-header">
                <h5><i class="bi bi-card-text me-2"></i>Personal Information</h5>
            </div>
            <div class="widget-content">
                <table class="table table-borderless profile-table">
                    <tbody>
                        <tr>
                            <td class="profile-label">
                                <i class="bi bi-card-text me-2"></i>Registration Number
                            </td>
                            <td class="profile-data"><?php echo $account_id; ?></td>
                        </tr>
                        <tr>
                            <td class="profile-label">
                                <i class="bi bi-person me-2"></i>First Name
                            </td>
                            <td class="profile-data"><?php echo $fname; ?></td>
                        </tr>
                        <tr>
                            <td class="profile-label">
                                <i class="bi bi-person me-2"></i>Middle Name
                            </td>
                            <td class="profile-data"><?php echo $mname ?: 'N/A'; ?></td>
                        </tr>
                        <tr>
                            <td class="profile-label">
                                <i class="bi bi-person me-2"></i>Last Name
                            </td>
                            <td class="profile-data"><?php echo $lname; ?></td>
                        </tr>
                        <tr>
                            <td class="profile-label">
                                <i class="bi bi-gender-ambiguous me-2"></i>Gender
                            </td>
                            <td class="profile-data"><?php echo ucfirst($gender); ?></td>
                        </tr>
                        <tr>
                            <td class="profile-label">
                                <i class="bi bi-envelope me-2"></i>Email Address
                            </td>
                            <td class="profile-data"><?php echo $email; ?></td>
                        </tr>
                        <tr>
                            <td class="profile-label">
                                <i class="bi bi-mortarboard me-2"></i>Current Class
                            </td>
                            <td class="profile-data">
                                <?php
                                try {
                                    $stmt = $conn->prepare("SELECT name FROM tbl_classes WHERE id = ?");
                                    $stmt->execute([$class]);
                                    $class_result = $stmt->fetch();
                                    echo $class_result ? $class_result['name'] : 'Class ' . $class;
                                } catch (PDOException $e) {
                                    echo 'Class ' . $class;
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="profile-label">
                                <i class="bi bi-calendar me-2"></i>Registration Date
                            </td>
                            <td class="profile-data"><?php echo date('F j, Y'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<style>
/* Profile Photo Styling */
.profile-photo-large {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #fff;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Simple Profile Table Styling */
.profile-table {
    margin: 0;
}

.profile-table td {
    padding: 12px 0;
    border: none;
    vertical-align: middle;
}

.profile-table tr {
    border-bottom: 1px solid #f0f0f0;
}

.profile-table tr:last-child {
    border-bottom: none;
}

.profile-table tr:hover {
    background-color: #f8f9fa;
}

.profile-label {
    font-weight: 500;
    color: #6c757d;
    width: 40%;
    padding-right: 20px !important;
}

.profile-label i {
    color: #007bff;
    width: 16px;
}

.profile-data {
    font-weight: 600;
    color: #2c3e50;
    text-align: left;
}

/* Info Card Styling */
.info-card {
    display: flex;
    align-items: center;
    padding: 20px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.info-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.info-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    flex-shrink: 0;
    color: white;
    font-size: 20px;
}

.info-content h6 {
    margin: 0 0 5px 0;
    font-weight: 600;
    color: #2c3e50;
}

.info-value {
    font-size: 24px;
    font-weight: 700;
    margin: 0 0 5px 0;
    color: #007bff;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .profile-table td {
        display: block;
        width: 100%;
        padding: 8px 0;
    }
    
    .profile-label {
        width: 100%;
        font-size: 14px;
        margin-bottom: 4px;
    }
    
    .profile-data {
        font-size: 16px;
        padding-left: 20px;
    }
    
    .info-card {
        margin-bottom: 15px;
    }
    
    .profile-photo-large {
        width: 100px;
        height: 100px;
    }
}
</style>

<?php include('student-footer.php'); ?>