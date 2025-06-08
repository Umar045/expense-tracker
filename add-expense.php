<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
$msg = ""; // Initialize $msg variable

if (strlen($_SESSION['detsuid']==0)) {
  header('location:logout.php');
  exit();
} else {
  // Verify user exists
  $userid = $_SESSION['detsuid'];
  $verify_stmt = mysqli_prepare($con, "SELECT ID FROM tbluser WHERE ID = ?");
  mysqli_stmt_bind_param($verify_stmt, "i", $userid);
  mysqli_stmt_execute($verify_stmt);
  $verify_result = mysqli_stmt_get_result($verify_stmt);
  
  if(mysqli_num_rows($verify_result) == 0) {
    // User doesn't exist
    session_destroy();
    header('location:logout.php');
    exit();
  }
  mysqli_stmt_close($verify_stmt);

  if(isset($_POST['submit'])) {
    try {
      $dateexpense = mysqli_real_escape_string($con, $_POST['dateexpense']);
      $item = mysqli_real_escape_string($con, $_POST['item']);
      $costitem = mysqli_real_escape_string($con, $_POST['costitem']);
      $category = mysqli_real_escape_string($con, $_POST['category']);
      
      // Validate cost is a valid number
      if(!is_numeric($costitem)) {
        throw new Exception("Cost must be a valid number");
      }
      
      // Verify category exists
      $cat_stmt = mysqli_prepare($con, "SELECT ID FROM tblexpensecategory WHERE ID = ?");
      mysqli_stmt_bind_param($cat_stmt, "i", $category);
      mysqli_stmt_execute($cat_stmt);
      $cat_result = mysqli_stmt_get_result($cat_stmt);
      if(mysqli_num_rows($cat_result) == 0) {
        throw new Exception("Invalid category selected");
      }
      mysqli_stmt_close($cat_stmt);
      
      $stmt = mysqli_prepare($con, "INSERT INTO tblexpense(UserId, ExpenseDate, ExpenseItem, ExpenseCost, CategoryId) VALUES (?, ?, ?, ?, ?)");
      mysqli_stmt_bind_param($stmt, "isssi", $userid, $dateexpense, $item, $costitem, $category);
      
      if(mysqli_stmt_execute($stmt)){
        $msg = "Expense has been added successfully";
        echo "<script>window.location.href='manage-expense.php'</script>";
      } else {
        throw new Exception(mysqli_error($con));
      }
      mysqli_stmt_close($stmt);
    } catch(Exception $e) {
      $msg = "Error: " . $e->getMessage();
    }
  }
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Daily Expense Tracker || Add Expense</title>
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/font-awesome.min.css" rel="stylesheet">
	<link href="css/datepicker3.css" rel="stylesheet">
	<link href="css/styles.css" rel="stylesheet">
	
	<!--Custom Font-->
	<link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
	<!--[if lt IE 9]>
	<script src="js/html5shiv.js"></script>
	<script src="js/respond.min.js"></script>
	<![endif]-->
</head>
<body>
	<?php include_once('includes/header.php');?>
	<?php include_once('includes/sidebar.php');?>
		
	<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
		<div class="row">
			<ol class="breadcrumb">
				<li><a href="#">
					<em class="fa fa-home"></em>
				</a></li>
				<li class="active">Expense</li>
			</ol>
		</div><!--/.row-->
		
		
				
		
		<div class="row">
			<div class="col-lg-12">
			
				
				
				<div class="panel panel-default">
					<div class="panel-heading">Expense</div>
					<div class="panel-body">
						<p style="font-size:16px; color:red" align="center"> <?php if($msg){
    echo $msg;
  }  ?> </p>
						<div class="col-md-12">
							
							<form role="form" method="post" action="">
								<div class="form-group">
									<label>Date of Expense</label>
									<input class="form-control" type="date" value="" name="dateexpense" required="true">
								</div>
								<div class="form-group">
									<label>Category</label>
									<select class="form-control" name="category" required="true">
										<option value="">Select Category</option>
										<?php
										$stmt = mysqli_prepare($con, "SELECT ID, CategoryName FROM tblexpensecategory ORDER BY CategoryName");
										mysqli_stmt_execute($stmt);
										$result = mysqli_stmt_get_result($stmt);
										while($row = mysqli_fetch_assoc($result)) {
											echo '<option value="' . $row['ID'] . '">' . htmlspecialchars($row['CategoryName']) . '</option>';
										}
										mysqli_stmt_close($stmt);
										?>
									</select>
								</div>
								<div class="form-group">
									<label>Item</label>
									<input type="text" class="form-control" name="item" value="" required="true">
								</div>
								
								<div class="form-group">
									<label>Cost of Item</label>
									<input class="form-control" type="text" value="" required="true" name="costitem">
								</div>
																
								<div class="form-group has-success">
									<button type="submit" class="btn btn-primary" name="submit">Add</button>
								</div>
								
								
								</div>
								
							</form>
						</div>
					</div>
				</div><!-- /.panel-->
			</div><!-- /.col-->
			<?php include_once('includes/footer.php');?>
		</div><!-- /.row -->
	</div><!--/.main-->
	
<script src="js/jquery-1.11.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/chart.min.js"></script>
	<script src="js/chart-data.js"></script>
	<script src="js/easypiechart.js"></script>
	<script src="js/easypiechart-data.js"></script>
	<script src="js/bootstrap-datepicker.js"></script>
	<script src="js/custom.js"></script>
	
</body>
</html>