<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['detsuid']==0)) {
  header('location:logout.php');
  } else {

// Initialize variables
$fdate = '';
$tdate = '';
$rtype = '';
$msg = '';
$showReport = false;

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    if(isset($_POST['fromdate']) && !empty($_POST['fromdate'])) {
        $fdate = mysqli_real_escape_string($con, $_POST['fromdate']);
    } else {
        $msg = "Please select From Date";
    }
    
    if(isset($_POST['todate']) && !empty($_POST['todate'])) {
        $tdate = mysqli_real_escape_string($con, $_POST['todate']);
    } else {
        $msg = "Please select To Date";
    }
    
    $rtype = isset($_POST['requesttype']) ? mysqli_real_escape_string($con, $_POST['requesttype']) : 'daily';
    
    // Only show report if dates are selected
    if(!empty($fdate) && !empty($tdate)) {
        $showReport = true;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Daily Expense Tracker || Datewise Expense Report</title>
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/font-awesome.min.css" rel="stylesheet">
	<link href="css/datepicker3.css" rel="stylesheet">
	<link href="css/styles.css" rel="stylesheet">
	
	<!--Custom Font-->
	<link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
	
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
				<li class="active">Datewise Expense Report</li>
			</ol>
		</div><!--/.row-->
		
		
				
		
		<div class="row">
			<div class="col-lg-12">
			
				
				
				<div class="panel panel-default">
					<div class="panel-heading">Datewise Expense Report</div>
					<div class="panel-body">

						<div class="col-md-12">
							<?php if(!empty($msg)): ?>
							<div class="alert alert-warning"><?php echo $msg; ?></div>
							<?php endif; ?>

							<!-- Report Form -->
							<form method="post" action="">
								<div class="form-group">
									<label>From Date</label>
									<input type="date" name="fromdate" class="form-control" required>
								</div>
								<div class="form-group">
									<label>To Date</label>
									<input type="date" name="todate" class="form-control" required>
								</div>
								<div class="form-group">
									<button type="submit" class="btn btn-primary">Generate Report</button>
								</div>
							</form>

							<?php if($showReport): ?>
							<h5 align="center" style="color:blue">Datewise Expense Report from <?php echo $fdate?> to <?php echo $tdate?></h5>
							<hr />
							<table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
								<thead>
									<tr>
										<th>S.NO</th>
										<th>Date</th>
										<th>Expense Amount</th>
									</tr>
								</thead>
								<tbody>
								<?php
								$userid = $_SESSION['detsuid'];
								$stmt = mysqli_prepare($con, "SELECT ExpenseDate, SUM(ExpenseCost) as totaldaily 
														   FROM tblexpense 
														   WHERE ExpenseDate BETWEEN ? AND ? 
														   AND UserId = ? 
														   GROUP BY ExpenseDate");
								mysqli_stmt_bind_param($stmt, "ssi", $fdate, $tdate, $userid);
								mysqli_stmt_execute($stmt);
								$result = mysqli_stmt_get_result($stmt);
								
								$cnt = 1;
								$totalsexp = 0;
								while ($row = mysqli_fetch_array($result)) {
									?>
									<tr>
										<td><?php echo $cnt;?></td>
										<td><?php echo date('d-m-Y', strtotime($row['ExpenseDate']));?></td>
										<td>₹<?php echo number_format($row['totaldaily'], 2);?></td>
									</tr>
									<?php
									$totalsexp += $row['totaldaily']; 
									$cnt++;
								}
								mysqli_stmt_close($stmt);
								?>
								<tr>
									<th colspan="2" style="text-align:center">Grand Total</th>     
									<td>₹<?php echo number_format($totalsexp, 2);?></td>
								</tr>     
								</tbody>
							</table>
							<?php endif; ?>
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
<?php } ?>