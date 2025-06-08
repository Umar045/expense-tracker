<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection only if not already included
if (!defined('DB_CONNECT_INCLUDED')) {
    include_once('dbconnection.php');
}

// Get database connection
$con = verify_connection();

// Check if user is logged in
if (!isset($_SESSION['detsuid']) || empty($_SESSION['detsuid'])) {
    header('location: login.php');
    exit();
}

// Handle profile picture upload
if(isset($_FILES['profile_picture'])) {
    $userid = $_SESSION['detsuid'];
    $target_dir = "../uploads/profile_pictures/";
    
    // Create directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION));
    $new_filename = "user_" . $userid . "." . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // Check if image file is a actual image or fake image
    if(getimagesize($_FILES["profile_picture"]["tmp_name"]) !== false) {
        // Allow certain file formats
        if($file_extension == "jpg" || $file_extension == "jpeg" || $file_extension == "png") {
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                // Update user profile picture in database
                if($stmt = mysqli_prepare($con, "UPDATE tbluser SET ProfilePicture = ? WHERE ID = ?")) {
                    mysqli_stmt_bind_param($stmt, "si", $new_filename, $userid);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
            }
        }
    }
}

// Get user information
$uid = $_SESSION['detsuid'];
$name = "User";
$profile_picture = null;

try {
    if($stmt = mysqli_prepare($con, "SELECT FullName, ProfilePicture FROM tbluser WHERE ID = ?")) {
        mysqli_stmt_bind_param($stmt, "i", $uid);
        if(mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if($row = mysqli_fetch_assoc($result)) {
                $name = htmlspecialchars($row['FullName']);
                $profile_picture = $row['ProfilePicture'];
            }
        }
        mysqli_stmt_close($stmt);
    } else {
        error_log("Failed to prepare statement: " . mysqli_error($con));
    }
} catch (Exception $e) {
    error_log("Error in sidebar.php: " . $e->getMessage());
}
?>

<div id="sidebar-collapse" class="col-sm-3 col-lg-2 sidebar">
    <div class="profile-sidebar">
        <div class="profile-userpic">
            <?php
            // Fix: Use absolute path from project root for default avatar
            $profile_path = "uploads/profile_pictures/" . $profile_picture;
            if($profile_picture && file_exists($profile_path)) {
                $img_src = $profile_path;
            } else {
                $img_src = "/Expense/Expense-Management-System/assets/images/default-avatar.png";
            }
            ?>
            <img src="<?php echo htmlspecialchars($img_src); ?>" class="img-responsive" alt="">
            <div class="edit-avatar" onclick="document.getElementById('profile_picture_input').click();">
                <i class="fa fa-camera"></i>
            </div>
            <form id="profile_picture_form" method="post" enctype="multipart/form-data" style="display: none;">
                <input type="file" id="profile_picture_input" name="profile_picture" accept="image/jpeg,image/png" onchange="this.form.submit()">
            </form>
        </div>
        <div class="profile-usertitle">
            <div class="profile-usertitle-name"><?php echo $name; ?></div>
            <div class="profile-usertitle-status"><span class="indicator label-success"></span>Online</div>
        </div>
        <div class="clear"></div>
    </div>
    <div class="divider"></div>
    
    <ul class="nav menu">
        <li class="<?php if($page=='dashboard') { echo 'active'; }?>">
            <a href="dashboard.php"><em class="fa fa-dashboard">&nbsp;</em> Dashboard</a>
        </li>
        
        <li class="parent">
            <a data-toggle="collapse" href="#sub-item-1">
                <em class="fa fa-navicon">&nbsp;</em> Expenses <span data-toggle="collapse" href="#sub-item-1" class="icon pull-right"><em class="fa fa-plus"></em></span>
            </a>
            <ul class="children collapse" id="sub-item-1">
                <li><a class="" href="add-expense.php">
                    <span class="fa fa-arrow-right">&nbsp;</span> Add Expenses
                </a></li>
                <li><a class="" href="manage-expense.php">
                    <span class="fa fa-arrow-right">&nbsp;</span> Manage Expenses
                </a></li>
            </ul>
        </li>
        
        <li class="parent">
            <a data-toggle="collapse" href="#sub-item-2">
                <em class="fa fa-navicon">&nbsp;</em> Expense Report <span data-toggle="collapse" href="#sub-item-2" class="icon pull-right"><em class="fa fa-plus"></em></span>
            </a>
            <ul class="children collapse" id="sub-item-2">
                <li><a class="" href="expense-datewise-reports.php">
                    <span class="fa fa-arrow-right">&nbsp;</span> Daywise Expenses
                </a></li>
                <li><a class="" href="expense-monthwise-reports.php">
                    <span class="fa fa-arrow-right">&nbsp;</span> Monthwise Expenses
                </a></li>
                <li><a class="" href="expense-yearwise-reports.php">
                    <span class="fa fa-arrow-right">&nbsp;</span> Yearwise Expenses
                </a></li>
            </ul>
        </li>
        
        <li class="parent">
            <a data-toggle="collapse" href="#sub-item-category">
                <em class="fa fa-folder-open">&nbsp;</em> Category <span data-toggle="collapse" href="#sub-item-category" class="icon pull-right"><em class="fa fa-plus"></em></span>
            </a>
            <ul class="children collapse" id="sub-item-category">
                <li><a class="" href="add-category.php">
                    <span class="fa fa-arrow-right">&nbsp;</span> Add Category
                </a></li>
                <li><a class="" href="manage-category.php">
                    <span class="fa fa-arrow-right">&nbsp;</span> Manage Category
                </a></li>
            </ul>
        </li>
        
        <li><a href="user-profile.php"><em class="fa fa-user">&nbsp;</em> Profile</a></li>
        <li><a href="change-password.php"><em class="fa fa-clone">&nbsp;</em> Change Password</a></li>
        <li><a href="logout.php"><em class="fa fa-power-off">&nbsp;</em> Logout</a></li>
    </ul>
</div>

<script>
// Add active class to current page
document.addEventListener('DOMContentLoaded', function() {
    var currentPage = window.location.pathname.split('/').pop();
    var menuItems = document.querySelectorAll('.nav.menu li a');
    
    menuItems.forEach(function(item) {
        if(item.getAttribute('href') === currentPage) {
            item.parentElement.classList.add('active');
            // If it's a child menu item, expand the parent
            var parent = item.closest('.children');
            if(parent) {
                parent.classList.add('in');
                parent.previousElementSibling.setAttribute('aria-expanded', 'true');
            }
        }
    });

    // Handle collapse toggles independently
    document.querySelectorAll('[data-toggle="collapse"]').forEach(function(toggle) {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            var target = document.querySelector(this.getAttribute('href'));
            if(target) {
                target.classList.toggle('in');
                var icon = this.querySelector('.fa');
                if(icon) {
                    icon.classList.toggle('fa-plus');
                    icon.classList.toggle('fa-minus');
                }
                this.setAttribute('aria-expanded', target.classList.contains('in'));
            }
        });
    });
});
</script>