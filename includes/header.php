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
?>

<nav class="navbar navbar-custom navbar-fixed-top" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#sidebar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <div class="header-animated">
                <h1 id="typewriter-header"></h1>
            </div>
        </div>
    </div>
</nav>
<script>
// Typewriter animation for header (continuous loop)
const word = "Daily Expense Tracker";
const header = document.getElementById('typewriter-header');
let charIndex = 0;
function typeWord() {
  if (charIndex <= word.length) {
    header.textContent = word.substring(0, charIndex);
    charIndex++;
    setTimeout(typeWord, 100);
  } else {
    setTimeout(() => {
      charIndex = 0;
      typeWord();
    }, 1200); // Pause before restarting
  }
}
typeWord();
</script>