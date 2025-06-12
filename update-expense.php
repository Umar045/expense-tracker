<?php
session_start();
include('includes/dbconnection.php');
if (!isset($_GET['id'])) { die('No expense selected.'); }
$id = intval($_GET['id']);
$uid = $_SESSION['detsuid'];

if (isset($_POST['update'])) {
    $date = $_POST['date'];
    $item = $_POST['item'];
    $cost = $_POST['cost'];
    $note = $_POST['note'];
    $q = mysqli_query($con, "UPDATE tblexpense SET ExpenseDate='$date', ExpenseItem='$item', ExpenseCost='$cost', Note='$note' WHERE ID=$id AND UserId=$uid");
    if ($q) {
        header('Location: manage-expense.php');
        exit();
    } else {
        $msg = 'Update failed.';
    }
}
$q = mysqli_query($con, "SELECT * FROM tblexpense WHERE ID=$id AND UserId=$uid");
$row = mysqli_fetch_assoc($q);
if (!$row) { die('Expense not found.'); }
?>
<!DOCTYPE html>
<html>
<head>
<title>Update Expense</title>
<link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
  <h2>Update Expense</h2>
  <?php if(isset($msg)) echo '<div class="alert alert-danger">'.$msg.'</div>'; ?>
  <form method="post">
    <div class="form-group">
      <label>Date</label>
      <input type="date" name="date" class="form-control" value="<?php echo htmlspecialchars($row['ExpenseDate']); ?>" required>
    </div>
    <div class="form-group">
      <label>Item</label>
      <input type="text" name="item" class="form-control" value="<?php echo htmlspecialchars($row['ExpenseItem']); ?>" required>
    </div>
    <div class="form-group">
      <label>Cost</label>
      <input type="number" step="0.01" name="cost" class="form-control" value="<?php echo htmlspecialchars($row['ExpenseCost']); ?>" required>
    </div>
    
    <button type="submit" name="update" class="btn btn-success">Update</button>
    <a href="manage-expense.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>
</body>
</html>