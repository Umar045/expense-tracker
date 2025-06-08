<?php  
session_start();
error_reporting(0);
include('includes/dbconnection.php');
$msg = ""; // Initialize $msg variable

if (strlen($_SESSION['detsuid']==0)) {
  header('location:logout.php');
  exit();
} else {
    //code deletion
    if(isset($_GET['delid'])) {
        $rowid = intval($_GET['delid']);
        $userid = $_SESSION['detsuid'];
        
        // First verify that the expense belongs to the logged-in user
        $stmt = mysqli_prepare($con, "SELECT ID FROM tblexpense WHERE ID=? AND UserId=?");
        mysqli_stmt_bind_param($stmt, "ii", $rowid, $userid);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if(mysqli_stmt_num_rows($stmt) > 0) {
            // If expense belongs to user, delete it
            $stmt = mysqli_prepare($con, "DELETE FROM tblexpense WHERE ID=? AND UserId=?");
            mysqli_stmt_bind_param($stmt, "ii", $rowid, $userid);
            
            if(mysqli_stmt_execute($stmt)){
                $msg = "Record successfully deleted";
                echo "<script>window.location.href='manage-expense.php'</script>";
            } else {
                $msg = "Something went wrong. Please try again";
            }
        } else {
            $msg = "Invalid request or unauthorized access";
        }
        mysqli_stmt_close($stmt);
    }
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Daily Expense Tracker || Manage Expense</title>
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
                                <div class="table-responsive">
                                    <table class="table table-bordered mg-b-0">
                                        <thead>
                                            <tr>
                                                <th>S.NO</th>
                                                <th>Expense Item</th>
                                                <th>Expense Cost</th>
                                                <th>Expense Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $userid = $_SESSION['detsuid'];
                                            $stmt = mysqli_prepare($con, "SELECT * FROM tblexpense WHERE UserId=? ORDER BY ExpenseDate DESC");
                                            mysqli_stmt_bind_param($stmt, "i", $userid);
                                            mysqli_stmt_execute($stmt);
                                            $result = mysqli_stmt_get_result($stmt);
                                            $cnt=1;
                                            while ($row = mysqli_fetch_array($result)) {
                                            ?>
                                            <tr>
                                                <td><?php echo $cnt;?></td>
                                                <td><?php echo htmlspecialchars($row['ExpenseItem']);?></td>
                                                <td><?php echo htmlspecialchars($row['ExpenseCost']);?></td>
                                                <td><?php echo htmlspecialchars($row['ExpenseDate']);?></td>
                                                <td>
                                                    <a href="./view-expense.php?id=<?php echo $row['ID']; ?>" class="btn btn-info btn-xs">View</a>
                                                    <a href="./update-expense.php?id=<?php echo $row['ID']; ?>" class="btn btn-warning btn-xs">Update</a>
                                                    <a href="./delete-expense.php?id=<?php echo $row['ID']; ?>" class="btn btn-danger btn-xs" onclick="return confirm('Are you sure you want to delete this expense?');">Delete</a>
                                                </td>
                                            </tr>
                                            <?php 
                                            $cnt=$cnt+1;
                                            }
                                            mysqli_stmt_close($stmt);
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
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