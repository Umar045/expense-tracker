<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once('includes/dbconnection.php');

// Get user information for the header
$userid = isset($_SESSION['detsuid']) ? $_SESSION['detsuid'] : 0;
$username = '';
$profile_picture = '';

if ($userid > 0) {
    $con = verify_connection();
    $stmt = mysqli_prepare($con, "SELECT FullName, ProfilePicture FROM tbluser WHERE ID = ?");
    mysqli_stmt_bind_param($stmt, "i", $userid);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $username = $row['FullName'];
        $profile_picture = $row['ProfilePicture'];
    }
    mysqli_stmt_close($stmt);
}
?>
<link href="css/footer-styles.css" rel="stylesheet">
<link href="css/header-styles.css" rel="stylesheet">

<div class="header-animation">
    <div class="typewriter">
        <h1 id="typewriter-text">Welcome to Daily Expense Tracker</h1>
    </div>
</div>

<script>
// Typewriter effect
const text = document.getElementById('typewriter-text').innerHTML;
const typewriterText = document.getElementById('typewriter-text');
typewriterText.innerHTML = '';
let i = 0;

function typeWriter() {
  if (i < text.length) {
    typewriterText.innerHTML += text.charAt(i);
    i++;
    setTimeout(typeWriter, 100);
  }
}

typeWriter();

// Toggle dropdown menu
document.addEventListener('DOMContentLoaded', function() {
    const profileTrigger = document.querySelector('.profile-trigger');
    if (profileTrigger) {
        profileTrigger.addEventListener('click', function() {
            const dropdownMenu = document.querySelector('.dropdown-menu');
            dropdownMenu.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.querySelector('.profile-dropdown');
            if (dropdown && !dropdown.contains(event.target)) {
                const dropdownMenu = document.querySelector('.dropdown-menu');
                if (dropdownMenu.classList.contains('show')) {
                    dropdownMenu.classList.remove('show');
                }
            }
        });
    }
});
</script>