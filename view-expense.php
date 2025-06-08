<?php
session_start();
include('includes/dbconnection.php');
if (!isset($_GET['id'])) { die('No expense selected.'); }
$id = intval($_GET['id']);
$uid = $_SESSION['detsuid'];
$q = mysqli_query($con, "SELECT * FROM tblexpense WHERE ID=$id AND UserId=$uid");
$row = mysqli_fetch_assoc($q);
if (!$row) { die('Expense not found.'); }
?>
<!DOCTYPE html>
<html>
<head>
<title>View Expense</title>
<link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
  <h2>Expense Details</h2>
  <table class="table table-bordered">
    <tr><th>Date</th><td><?php echo htmlspecialchars($row['ExpenseDate']); ?></td></tr>
    <tr><th>Item</th><td><?php echo htmlspecialchars($row['ExpenseItem']); ?></td></tr>
    <tr><th>Cost</th><td><?php echo htmlspecialchars($row['ExpenseCost']); ?></td></tr>
    <tr><th>Note</th><td><?php echo htmlspecialchars($row['Note']); ?></td></tr>
  </table>
  <a href="manage-expense.php" class="btn btn-primary">Back</a>
</div>
</body>
</html>