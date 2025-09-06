<?php
chdir('../../');
session_start();
require_once('db/config.php');
require_once('const/school.php');
require_once('const/check_session.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['term']) && isset($_POST['class']) && isset($_POST['subject'])) {
    $_SESSION['result__data'] = $_POST;
    header("location:../results.php");
    exit;
} else {
    header("location:../manage_results.php");
    exit;
}
?>
