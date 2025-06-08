<?php
session_start();
include('includes/dbconnection.php');
if (!isset($_GET['id'])) { die('No category selected.'); }
$id = intval($_GET['id']);
$q = mysqli_query($con, "SELECT * FROM tblexpensecategory WHERE ID=$id");
$row = mysqli_fetch_assoc($q);
if (!$row) { die('Category not found.'); }
?>
<!DOCTYPE html>
<html>
<head>
<title>View Category</title>
<link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container" style="max-width:400px;margin:40px auto;">
  <h2>Category Details</h2>
  <table class="table table-bordered">
    <tr><th>Name</th><td><?php echo htmlspecialchars($row['CategoryName']); ?></td></tr>
    <tr><th>Created</th><td><?php echo htmlspecialchars($row['CreationDate']); ?></td></tr>
  </table>
  <a href="manage-category.php" class="btn btn-primary">Back</a>
</div>
</body>
</html>