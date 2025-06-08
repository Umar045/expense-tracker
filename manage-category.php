<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('includes/dbconnection.php');

// Debug connection
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Check which database we're connected to
$current_db = mysqli_query($con, "SELECT DATABASE()");
$db_name = mysqli_fetch_row($current_db)[0];
echo "<div style='background:#fff3cd;padding:10px;margin:10px;'>";
echo "Connected to database: " . $db_name . "<br>";

// Test the categories table
$result = mysqli_query($con, "SELECT COUNT(*) as count FROM tblexpensecategory");
if (!$result) {
    echo "Error checking categories: " . mysqli_error($con);
} else {
    $count = mysqli_fetch_assoc($result)['count'];
    echo "Number of categories in database: " . $count;
}
echo "</div>";

// Fetch all categories
$categories = mysqli_query($con, "SELECT * FROM tblexpensecategory ORDER BY ID DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Categories</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <style>
        .main { padding: 20px; }
        .panel { margin-top: 20px; }
    </style>
</head>
<body>
<?php include('includes/header.php'); ?>
<?php include('includes/sidebar.php'); ?>
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="panel panel-default">
        <div class="panel-heading">Manage Categories</div>
        <div class="panel-body">
            <?php if ($categories && mysqli_num_rows($categories) > 0): ?>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Category Name</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    while($row = mysqli_fetch_assoc($categories)): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo htmlspecialchars($row['CategoryName']); ?></td>
                        <td><?php echo htmlspecialchars($row['CreationDate']); ?></td>
                        <td>
                            <a href="view-category.php?id=<?php echo $row['ID']; ?>" class="btn btn-info btn-xs">View</a>
                            <a href="update-category.php?id=<?php echo $row['ID']; ?>" class="btn btn-warning btn-xs">Update</a>
                            <a href="delete-category.php?id=<?php echo $row['ID']; ?>" class="btn btn-danger btn-xs" onclick="return confirm('Are you sure?');">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
                <div class="alert alert-info">No categories found.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>