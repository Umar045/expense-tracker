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
    
    // Set up upload directory paths
    $base_upload_dir = dirname(__DIR__) . "/uploads";
    $profile_pictures_dir = $base_upload_dir . "/profile_pictures";
    
    // Create base uploads directory if it doesn't exist
    if (!file_exists($base_upload_dir)) {
        if (!mkdir($base_upload_dir, 0777, true)) {
            error_log("Failed to create uploads directory");
            die('Failed to create uploads directory');
        }
        chmod($base_upload_dir, 0777);
    }
    
    // Create profile pictures directory if it doesn't exist
    if (!file_exists($profile_pictures_dir)) {
        if (!mkdir($profile_pictures_dir, 0777, true)) {
            error_log("Failed to create profile pictures directory");
            die('Failed to create profile pictures directory');
        }
        chmod($profile_pictures_dir, 0777);
    }
      $target_dir = $profile_pictures_dir . "/";
    
    $file_extension = strtolower(pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION));
    $new_filename = "user_" . $userid . "." . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // Delete old profile picture if exists
    $old_files = glob($target_dir . "user_" . $userid . ".*");
    foreach($old_files as $old_file) {
        if(is_file($old_file)) {
            unlink($old_file);
        }
    }
    
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
    <div class="profile-sidebar">        <div class="profile-userpic">
            <?php            $profile_path = dirname(__DIR__) . "/uploads/profile_pictures/" . $profile_picture;
            if($profile_picture && file_exists($profile_path)) {
                $img_src = "../uploads/profile_pictures/" . $profile_picture;
            } else {
                // Ensure uploads directory exists with proper permissions
                $upload_dir = dirname(__DIR__) . "/uploads";
                $profile_dir = $upload_dir . "/profile_pictures";
                
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                if (!file_exists($profile_dir)) {
                    mkdir($profile_dir, 0777, true);
                }
                
                // Set permissions
                chmod($upload_dir, 0777);
                chmod($profile_dir, 0777);
                
                $img_src = "../assets/images/default-avatar.png";
            }
            ?>
            <img src="<?php echo htmlspecialchars($img_src); ?>" alt="User Avatar">
            <div class="edit-avatar">
                <i class="fa fa-pencil"></i>
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
    // Profile picture edit functionality
    document.querySelector('.edit-avatar').addEventListener('click', function() {
        document.getElementById('profile_picture_input').click();
    });
    
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
            e.stopPropagation();
            var target = document.querySelector(this.getAttribute('href'));
            if(target) {
                // Close other open menus
                document.querySelectorAll('.children.in').forEach(function(menu) {
                    if(menu !== target) {
                        menu.classList.remove('in');
                        var menuToggle = menu.previousElementSibling;
                        if(menuToggle) {
                            menuToggle.setAttribute('aria-expanded', 'false');
                            var menuIcon = menuToggle.querySelector('.fa');
                            if(menuIcon) {
                                menuIcon.classList.add('fa-plus');
                                menuIcon.classList.remove('fa-minus');
                            }
                        }
                    }
                });

                // Toggle current menu
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