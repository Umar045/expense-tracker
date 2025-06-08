<?php
session_start();
include('includes/dbconnection.php');
if (!isset($_GET['id'])) { die('No category selected.'); }
$id = intval($_GET['id']);
$msg = "";
if (isset($_POST['update'])) {
    $name = trim($_POST['name']);
    if($name != "") {
        $stmt = mysqli_prepare($con, "UPDATE tblexpensecategory SET CategoryName=? WHERE ID=?");
        mysqli_stmt_bind_param($stmt, "si", $name, $id);
        if(mysqli_stmt_execute($stmt)) {
            $msg = '<div class="alert alert-success">Category updated successfully! <a href="manage-category.php" class="btn btn-primary btn-sm float-right">Back to Categories</a></div>';
        } else {
            $msg = '<div class="alert alert-danger">Update failed.</div>';
        }
        mysqli_stmt_close($stmt);
    } else {
        $msg = '<div class="alert alert-warning">Category name cannot be empty.</div>';
    }
}
$q = mysqli_query($con, "SELECT * FROM tblexpensecategory WHERE ID=$id");
$row = mysqli_fetch_assoc($q);
if (!$row) { die('Category not found.'); }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Update Category</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        .float-right { float: right; }
    </style>
</head>
<body>
<div class="container" style="max-width:400px;margin:40px auto;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Update Category</h2>
        <a href="manage-category.php" class="btn btn-secondary">Back</a>
    </div>
    <?php echo $msg; ?>
    <form method="post">
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($row['CategoryName']); ?>" required maxlength="200">
        </div>
        <button type="submit" name="update" class="btn btn-success">Update</button>
        <a href="manage-category.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>