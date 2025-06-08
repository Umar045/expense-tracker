<?php
session_start();
include('includes/dbconnection.php');
if (!isset($_GET['id'])) { die('No category selected.'); }
$id = intval($_GET['id']);
$q = mysqli_query($con, "DELETE FROM tblexpensecategory WHERE ID=$id");
header('Location: manage-category.php');
exit();
