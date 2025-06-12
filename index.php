<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
$msg = ""; // Initialize $msg variable

if(isset($_POST['login']))
  {
    $email=$_POST['email'];
    $password=md5($_POST['password']);
    $query=mysqli_query($con,"select ID from tbluser where  Email='$email' && Password='$password' ");
    $ret=mysqli_fetch_array($query);
    if($ret>0){
      $_SESSION['detsuid']=$ret['ID'];
     header('location:dashboard.php');
    }
    else{
    $msg="Invalid Details.";
    }
  }
  ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Daily Expense Tracker - Login</title>
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
				<h4>Welcome Back</h4>
				<p>Please login to your account</p>
			</div>
			<div class="auth-card-body">
				<?php if($msg){ ?>
				<div class="auth-alert auth-alert-danger">
					<?php echo $msg; ?>
				</div>
				<?php } ?>
				
				<div class="social-login">
					<button class="social-btn social-btn-github" disabled>
						<i class="fa fa-github"></i> Login with GitHub
					</button>
				</div>
				
				<div class="auth-divider">
					<hr>
					<span>or with Email</span>
					<hr>
				</div>
				
				<form role="form" action="" method="post" id="" name="login">
					<div class="auth-form-group">
						<i class="fa fa-envelope auth-form-icon"></i>
						<input class="auth-form-control" placeholder="Email Address" name="email" type="email" autofocus required>
					</div>
					
					<div class="auth-form-group">
						<i class="fa fa-lock auth-form-icon"></i>
						<input class="auth-form-control" placeholder="Password" name="password" type="password" required>
					</div>
					
					<button type="submit" name="login" class="auth-btn auth-btn-primary">Login</button>
					
					<div class="auth-links">
						<a href="forgot-password.php" class="auth-link">Forgot Password?</a>
						<a href="register.php" class="auth-link">Create Account</a>
					</div>
				</form>
			</div>
		</div>
	</div>
	

<script src="js/jquery-1.11.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
</body>
</html>
