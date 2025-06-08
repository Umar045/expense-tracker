<?php
session_start();
include('includes/dbconnection.php');
if (!isset($_GET['id'])) { die('No expense selected.'); }
$id = intval($_GET['id']);
$uid = $_SESSION['detsuid'];
$q = mysqli_query($con, "DELETE FROM tblexpense WHERE ID=$id AND UserId=$uid");
header('Location: manage-expense.php');
exit();
