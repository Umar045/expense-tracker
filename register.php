<?php 
session_start();
error_reporting(0);
$msg = ""; // Initialize $msg variable
require_once('includes/dbconnection.php');

if(isset($_POST['submit']))
{
    $fname = mysqli_real_escape_string($con, $_POST['name']);
    $mobno = mysqli_real_escape_string($con, $_POST['mobilenumber']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email exists
    $stmt = mysqli_prepare($con, "SELECT Email FROM tbluser WHERE Email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    
    if(mysqli_stmt_num_rows($stmt) > 0){
        $msg = "This email is associated with another account";
    } else {
        // Insert new user
        $stmt = mysqli_prepare($con, "INSERT INTO tbluser(FullName, MobileNumber, Email, Password) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $fname, $mobno, $email, $password);
        
        if (mysqli_stmt_execute($stmt)) {
            $msg = "You have successfully registered";
        } else {
            $msg = "Something Went Wrong. Please try again";
        }
    }
    mysqli_stmt_close($stmt);
}

 ?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Daily Expense Tracker - Signup</title>
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/datepicker3.css" rel="stylesheet">
	<link href="css/styles.css" rel="stylesheet">
	<link href="css/auth-styles.css" rel="stylesheet">
	<link href="css/font-awesome.min.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
	<script type="text/javascript">
function checkpass()
{
if(document.signup.password.value!=document.signup.repeatpassword.value)
{
alert('Password and Repeat Password field does not match');
document.signup.repeatpassword.focus();
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
				<h4>Create Account</h4>
				<p>Fill in your details to get started</p>
			</div>
			<div class="auth-card-body">
				<?php if($msg){ ?>
				<div class="auth-alert <?php echo (strpos($msg, 'successfully') !== false) ? 'auth-alert-success' : 'auth-alert-danger'; ?>">
					<?php echo $msg; ?>
				</div>
				<?php } ?>
				
				<form role="form" action="" method="post" id="" name="signup" onsubmit="return checkpass();">
					<div class="auth-form-group">
						<i class="fa fa-user auth-form-icon"></i>
						<input class="auth-form-control" placeholder="Full Name" name="name" type="text" required>
					</div>
					
					<div class="auth-form-group">
						<i class="fa fa-envelope auth-form-icon"></i>
						<input class="auth-form-control" placeholder="Email Address" name="email" type="email" required>
					</div>
					
					<div class="auth-form-group">
						<i class="fa fa-phone auth-form-icon"></i>
						<input type="text" class="auth-form-control" id="mobilenumber" name="mobilenumber" placeholder="Mobile Number" maxlength="10" pattern="[0-9]{10}" required>
					</div>
					
					<div class="auth-form-group">
						<i class="fa fa-lock auth-form-icon"></i>
						<input class="auth-form-control" placeholder="Password" name="password" type="password" required>
					</div>
					
					<div class="auth-form-group">
						<i class="fa fa-check-circle auth-form-icon"></i>
						<input type="password" class="auth-form-control" id="repeatpassword" name="repeatpassword" placeholder="Confirm Password" required>
					</div>
					
					<button type="submit" name="submit" class="auth-btn auth-btn-primary">Create Account</button>
					
					<div class="auth-divider">
						<hr>
						<span>Already have an account?</span>
						<hr>
					</div>
					
					<div class="text-center">
						<a href="index.php" class="auth-link">Login to your account</a>
					</div>
				</form>
			</div>
		</div>
	</div>
	

<script src="js/jquery-1.11.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
</body>
</html>
