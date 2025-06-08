<?php
session_start();
include('includes/dbconnection.php');

$userId = $_SESSION['detsuid'];
$query = mysqli_query($con, "SELECT ExpenseItem, SUM(ExpenseCost) as total FROM tblexpense 
    WHERE UserId='$userId' 
    GROUP BY ExpenseItem 
    ORDER BY total DESC");

$labels = [];
$values = [];

while($row = mysqli_fetch_assoc($query)) {
    $labels[] = $row['ExpenseItem'];
    $values[] = $row['total'];
}

header('Content-Type: application/json');
echo json_encode(['labels' => $labels, 'values' => $values]);