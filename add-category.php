<?php
session_start();
include('includes/dbconnection.php');
$msg = "";
if(isset($_POST['add'])) {
    $category = trim($_POST['category']);
    if($category != "") {
        $stmt = mysqli_prepare($con, "INSERT INTO tblexpensecategory (CategoryName) VALUES (?)");
        mysqli_stmt_bind_param($stmt, "s", $category);
        if(mysqli_stmt_execute($stmt)) {
            $msg = '<div class="alert alert-success">Category added successfully!</div>';
        } else {
            $msg = '<div class="alert alert-danger">Failed to add category.</div>';
        }
        mysqli_stmt_close($stmt);
    } else {
        $msg = '<div class="alert alert-warning">Category name cannot be empty.</div>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Category</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body>
<?php include('includes/header.php'); ?>
<?php include('includes/sidebar.php'); ?>
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="panel panel-default" style="max-width:400px;margin:40px auto;">
        <div class="panel-heading">Add New Category</div>
        <div class="panel-body">
            <?php echo $msg; ?>
            <form method="post">
                <div class="form-group">
                    <label for="category">Category Name</label>
                    <input type="text" name="category" id="category" class="form-control" required maxlength="200">
                </div>
                <button type="submit" name="add" class="btn btn-primary">Add Category</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>