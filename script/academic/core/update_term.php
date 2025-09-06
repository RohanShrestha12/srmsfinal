<?php
chdir('../../');
session_start();
require_once('db/config.php');
require_once('const/school.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

$name = ucfirst($_POST['name']);
$status = $_POST['status'];
$id = $_POST['id'];

try {

$stmt = $conn->prepare("SELECT * FROM tbl_terms WHERE name = ? AND id != ?");
$stmt->execute([$name, $id]);
$result = $stmt->fetchAll();

if (count($result) < 1) {
$stmt = $conn->prepare("UPDATE tbl_terms SET name=?, status=? WHERE id = ?");
$stmt->execute([$name, $status, $id]);

$_SESSION['reply'] = array (array("success",'Academic term updated successfully'));
header("location:../terms.php");

}else{

$_SESSION['reply'] = array (array("danger",'Academic term is already registered'));
header("location:../terms.php");

}

}catch(PDOException $e)
{
echo "Connection failed: " . $e->getMessage();
}


}else{
header("location:../");
}
?>
