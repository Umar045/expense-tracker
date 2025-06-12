<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
$msg = ""; // Initialize $msg variable

if(isset($_POST['submit']))
  {
    $contactno = mysqli_real_escape_string($con, $_POST['contactno']);
    $email = mysqli_real_escape_string($con, $_POST['email']);

    $stmt = mysqli_prepare($con, "SELECT ID FROM tbluser WHERE Email=? AND MobileNumber=?");
    mysqli_stmt_bind_param($stmt, "ss", $email, $contactno);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    
    if(mysqli_stmt_num_rows($stmt) > 0){
      $_SESSION['contactno'] = $contactno;
      $_SESSION['email'] = $email;
      header('location:reset-password.php');
      exit();
    } else {
      $msg = "Invalid Details. Please try again.";
    }
    mysqli_stmt_close($stmt);
  }
  ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Daily Expense Tracker - Forgot Password</title>
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/datepicker3.css" rel="stylesheet">
	<link href="css/styles.css" rel="stylesheet">
	<link href="css/auth-styles.css" rel="stylesheet">
	<link href="css/font-awesome.min.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="auth-page">
	<div class="container">
		<div class="auth-title">
			<h2>Daily Expense Tracker</h2>
			<hr>
		</div>
		
		<div class="auth-card">
			<div class="auth-card-header">
				<h4>Forgot Password</h4>
				<p>Enter your email and mobile number to reset your password</p>
			</div>
			<div class="auth-card-body">
				<?php if($msg){ ?>
				<div class="auth-alert auth-alert-danger">
					<?php echo $msg; ?>
				</div>
				<?php } ?>
				
				<form role="form" action="" method="post" id="" name="login">
					<div class="auth-form-group">
						<i class="fa fa-envelope auth-form-icon"></i>
						<input class="auth-form-control" placeholder="Email Address" name="email" type="email" autofocus required>
					</div>
					
					<div class="auth-form-group">
						<i class="fa fa-phone auth-form-icon"></i>
						<input class="auth-form-control" placeholder="Mobile Number" name="contactno" type="text" required>
					</div>
					
					<button type="submit" name="submit" class="auth-btn auth-btn-primary">Reset Password</button>
					
					<div class="auth-divider">
						<hr>
						<span>Remember your password?</span>
						<hr>
					</div>
					
					<div class="text-center">
						<a href="index.php" class="auth-link">Back to Login</a>
					</div>
				</form>
			</div>
		</div>
	</div>
	

<script src="js/jquery-1.11.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
</body>
</html>
