<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
$msg = ""; // Initialize $msg variable
error_reporting(0);

if(isset($_POST['submit']))
  {
    $contactno=$_SESSION['contactno'];
    $email=$_SESSION['email'];
    $password=md5($_POST['newpassword']);

        $query=mysqli_query($con,"update tbluser set Password='$password'  where  Email='$email' && MobileNumber='$contactno' ");
   if($query)
   {
echo "<script>alert('Password successfully changed');</script>";
session_destroy();
   }
  
  }
  ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Daily Expense Tracker - Reset Password</title>
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/datepicker3.css" rel="stylesheet">
	<link href="css/styles.css" rel="stylesheet">
	<link href="css/auth-styles.css" rel="stylesheet">
	<link href="css/font-awesome.min.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
	<script type="text/javascript">
function checkpass()
{
if(document.changepassword.newpassword.value!=document.changepassword.confirmpassword.value)
{
alert('New Password and Confirm Password field does not match');
document.changepassword.confirmpassword.focus();
return false;
}
return true;
} 

</script>
</head>
<body class="auth-page">
	<div class="container">
		<div class="auth-title">
			<h2>Daily Expense Tracker</h2>
			<hr>
		</div>
		
		<div class="auth-card">
			<div class="auth-card-header">
				<h4>Reset Password</h4>
				<p>Create a new secure password</p>
			</div>
			<div class="auth-card-body">
				<?php if($msg){ ?>
				<div class="auth-alert <?php echo (strpos($msg, 'successfully') !== false) ? 'auth-alert-success' : 'auth-alert-danger'; ?>">
					<?php echo $msg; ?>
				</div>
				<?php } ?>
				
				<form role="form" action="" method="post" name="changepassword" onsubmit="return checkpass()">
					<div class="auth-form-group">
						<i class="fa fa-lock auth-form-icon"></i>
						<input class="auth-form-control" placeholder="New Password" name="newpassword" type="password" required>
					</div>
					
					<div class="auth-form-group">
						<i class="fa fa-check-circle auth-form-icon"></i>
						<input class="auth-form-control" placeholder="Confirm New Password" name="confirmpassword" type="password" required>
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
