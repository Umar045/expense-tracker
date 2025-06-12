<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection only if not already included
if (!defined('DB_CONNECT_INCLUDED')) {
    include_once('includes/dbconnection.php');
}

// Get database connection
$con = verify_connection();

if (strlen($_SESSION['detsuid']==0)) {
  header('location:logout.php');
    exit();
}

// Get Today's Expense
$userid = $_SESSION['detsuid'];
$today = date('Y-m-d');
$stmt = mysqli_prepare($con, "SELECT COALESCE(SUM(ExpenseCost), 0) as TodayExpense FROM tblexpense WHERE UserId=? AND ExpenseDate=?");
mysqli_stmt_bind_param($stmt, "is", $userid, $today);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
$today_expense = $row['TodayExpense'];
mysqli_stmt_close($stmt);

// Get Yesterday's Expense
$yesterday = date('Y-m-d', strtotime('-1 day'));
$stmt = mysqli_prepare($con, "SELECT COALESCE(SUM(ExpenseCost), 0) as YesterdayExpense FROM tblexpense WHERE UserId=? AND ExpenseDate=?");
mysqli_stmt_bind_param($stmt, "is", $userid, $yesterday);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
$yesterday_expense = $row['YesterdayExpense'];
mysqli_stmt_close($stmt);

// Get Last 7 Days Expense
$last_7_days = date('Y-m-d', strtotime('-7 days'));
$stmt = mysqli_prepare($con, "SELECT COALESCE(SUM(ExpenseCost), 0) as WeekExpense FROM tblexpense WHERE UserId=? AND ExpenseDate BETWEEN ? AND ?");
mysqli_stmt_bind_param($stmt, "iss", $userid, $last_7_days, $today);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
$week_expense = $row['WeekExpense'];
mysqli_stmt_close($stmt);

// Get Last 30 Days Expense
$last_30_days = date('Y-m-d', strtotime('-30 days'));
$stmt = mysqli_prepare($con, "SELECT COALESCE(SUM(ExpenseCost), 0) as MonthExpense FROM tblexpense WHERE UserId=? AND ExpenseDate BETWEEN ? AND ?");
mysqli_stmt_bind_param($stmt, "iss", $userid, $last_30_days, $today);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
$month_expense = $row['MonthExpense'];
mysqli_stmt_close($stmt);

// Get Year to Date Expense
$year_start = date('Y-01-01');
$stmt = mysqli_prepare($con, "SELECT COALESCE(SUM(ExpenseCost), 0) as YearExpense FROM tblexpense WHERE UserId=? AND ExpenseDate BETWEEN ? AND ?");
mysqli_stmt_bind_param($stmt, "iss", $userid, $year_start, $today);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
$year_expense = $row['YearExpense'];
mysqli_stmt_close($stmt);

// Get Category-wise Expenses for Chart
$stmt = mysqli_prepare($con, "SELECT c.CategoryName, COALESCE(SUM(e.ExpenseCost), 0) as Amount 
                            FROM tblexpensecategory c 
                            LEFT JOIN tblexpense e ON e.CategoryId = c.ID AND e.UserId = ? AND e.ExpenseDate BETWEEN ? AND ?
                            GROUP BY c.CategoryName 
                            ORDER BY Amount DESC");
mysqli_stmt_bind_param($stmt, "iss", $userid, $last_30_days, $today);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$categories = [];
$amounts = [];
while($row = mysqli_fetch_assoc($result)) {
    $categories[] = $row['CategoryName'];
    $amounts[] = floatval($row['Amount']);
}
mysqli_stmt_close($stmt);

// Get trend data for the last 7 days
$trend_dates = array();
$trend_amounts = array();
for($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $stmt = mysqli_prepare($con, "SELECT COALESCE(SUM(ExpenseCost), 0) as Amount FROM tblexpense WHERE UserId=? AND ExpenseDate=?");
    mysqli_stmt_bind_param($stmt, "is", $userid, $date);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $trend_dates[] = date('d M', strtotime($date));
    $trend_amounts[] = floatval($row['Amount']);
    mysqli_stmt_close($stmt);
}

// Recent Expenses
$recent_stmt = mysqli_prepare($con, "SELECT e.ExpenseDate, e.ExpenseItem, e.ExpenseCost, c.CategoryName 
                            FROM tblexpense e 
                            JOIN tblexpensecategory c ON e.CategoryId = c.ID 
                            WHERE e.UserId = ? 
                            ORDER BY e.ExpenseDate DESC LIMIT 5");
mysqli_stmt_bind_param($recent_stmt, "i", $userid);
mysqli_stmt_execute($recent_stmt);
$recent_result = mysqli_stmt_get_result($recent_stmt);
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Daily Expense Tracker - Dashboard</title>
	
	<!-- CSS -->
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/font-awesome.min.css" rel="stylesheet">
	<link href="css/datepicker3.css" rel="stylesheet">
	<link href="css/styles.css" rel="stylesheet">
	<link href="css/layout-styles.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
	
	<!-- JavaScript -->
	<script src="js/jquery-1.11.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/chart.min.js"></script>
	<script src="js/chart-data.js"></script>
	<script src="js/easypiechart.js"></script>
	<script src="js/easypiechart-data.js"></script>
	<script src="js/bootstrap-datepicker.js"></script>
	<script src="js/custom.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	
	<!--[if lt IE 9]>
	<script src="js/html5shiv.js"></script>
	<script src="js/respond.min.js"></script>
	<![endif]-->
	<style>
    .card {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .bg-primary { background-color: #4e73df; color: white; }
    .bg-success { background-color: #1cc88a; color: white; }
    .bg-warning { background-color: #f6c23e; color: white; }
    .bg-info { background-color: #36b9cc; color: white; }
    .card h4 { margin-top: 0; }
    .card h3 { margin-bottom: 0; }
    .panel-body { height: 300px; }
    canvas { max-height: 280px !important; }
</style>
</head>
<body class="dashboard-page">
	<!-- Header Section -->
	<?php include_once('includes/header.php');?>
	
	<div class="app-container">
		<!-- Sidebar Section -->
		<?php include_once('includes/sidebar.php');?>

		<!-- Main Content Section -->
		<div class="main-content col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
			<div class="content-wrapper">
				<div class="row">
					<div class="col-lg-12">
						<h1 class="page-header">Dashboard</h1>
					</div>
				</div><!--/.row-->
		
		<!-- Expense Summary Cards -->
		<div class="row">
			<div class="col-xs-6 col-md-3">
				<div class="card bg-primary">
					<div class="card-body">
						<h4>Today's Expense</h4>
						<h3><?php echo number_format($today_expense, 2); ?></h3>
					</div>
				</div>
			</div>
			<div class="col-xs-6 col-md-3">
				<div class="card bg-success">
					<div class="card-body">
						<h4>Last 7 Days</h4>
						<h3><?php echo number_format($week_expense, 2); ?></h3>
					</div>
				</div>
			</div>
			<div class="col-xs-6 col-md-3">
				<div class="card bg-warning">
					<div class="card-body">
						<h4>Last 30 Days</h4>
						<h3><?php echo number_format($month_expense, 2); ?></h3>
					</div>
				</div>
			</div>
			<div class="col-xs-6 col-md-3">
				<div class="card bg-info">
					<div class="card-body">
						<h4>Year to Date</h4>
						<h3><?php echo number_format($year_expense, 2); ?></h3>
					</div>
				</div>
			</div>
		</div>

		<!-- Charts -->
		<div class="row">
			<div class="col-md-6">
				<div class="panel panel-default">
					<div class="panel-heading">Expense by Category</div>
					<div class="panel-body">
						<canvas id="categoryChart"></canvas>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="panel panel-default">
					<div class="panel-heading">7 Day Expense Trend</div>
					<div class="panel-body">
						<canvas id="trendChart"></canvas>
					</div>
				</div>
			</div>
		</div>

		<!-- Recent Expenses -->
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						Recent Expenses
						<span class="pull-right clickable panel-toggle"><em class="fa fa-toggle-up"></em></span>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table class="table table-hover">
								<thead>
									<tr>
										<th>Date</th>
										<th>Category</th>
										<th>Item</th>
										<th>Amount</th>
									</tr>
								</thead>
								<tbody>
					<?php
									while($row = mysqli_fetch_assoc($recent_result)) {
										echo "<tr>";
										echo "<td>" . date('d M Y', strtotime($row['ExpenseDate'])) . "</td>";
										echo "<td>" . htmlspecialchars($row['CategoryName']) . "</td>";
										echo "<td>" . htmlspecialchars($row['ExpenseItem']) . "</td>";
										echo "<td>₹" . number_format($row['ExpenseCost'], 2) . "</td>";
										echo "</tr>";
									}
									mysqli_stmt_close($recent_stmt);
									?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
				</div>
			</div> <!--/.content-wrapper-->

			<!-- Footer Section -->
			<div class="footer-container">
				<?php include_once('includes/footer.php');?>
			</div>
		</div> <!--/.main-content-->
	</div> <!--/.app-container-->
	
	<script>
	// Initialize Charts after DOM is ready
	$(document).ready(function() {
	    // Debug output
	    console.log('Category Data:', <?php echo json_encode($categories); ?>);
	    console.log('Amount Data:', <?php echo json_encode($amounts); ?>);
	    console.log('Today Expense:', <?php echo json_encode($today_expense); ?>);

	    // Category Chart
	    var ctxCategory = document.getElementById('categoryChart').getContext('2d');
	    new Chart(ctxCategory, {
	        type: 'pie',
	        data: {
	            labels: <?php echo json_encode($categories); ?>,
	            datasets: [{
	                data: <?php echo json_encode($amounts); ?>,
	                backgroundColor: <?php echo json_encode($categories); ?>.map(() => 
                    '#' + Math.floor(Math.random()*16777215).toString(16).padStart(6, '0')
                )
	            }]
	        },
	        options: {
	            responsive: true,
	            maintainAspectRatio: false,
	            plugins: {
	                legend: {
	                    position: 'bottom'
	                }
	            }
	        }
	    });

	    // Trend Chart
	    var ctxTrend = document.getElementById('trendChart').getContext('2d');
	    new Chart(ctxTrend, {
	        type: 'line',
	        data: {
	            labels: <?php echo json_encode($trend_dates); ?>,
	            datasets: [{
	                label: 'Daily Expenses',
	                data: <?php echo json_encode($trend_amounts); ?>,
	                borderColor: '#4e73df',
	                tension: 0.1
	            }]
	        },
	        options: {
	            responsive: true,
	            maintainAspectRatio: false,
	            scales: {
	                y: {
	                    beginAtZero: true
	                }
	            }
	        }
	    });
	});
	</script>
</body>
</html>