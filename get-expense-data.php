<?php
session_start();
include('includes/dbconnection.php');

$userId = $_SESSION['detsuid'];
$query = mysqli_query($con, "SELECT ExpenseDate, SUM(ExpenseCost) as total FROM tblexpense 
    WHERE UserId='$userId' 
    GROUP BY ExpenseDate 
    ORDER BY ExpenseDate DESC 
    LIMIT 7");

$labels = [];
$values = [];

while($row = mysqli_fetch_assoc($query)) {
    $labels[] = date('d M', strtotime($row['ExpenseDate']));
    $values[] = $row['total'];
}

// Reverse arrays to show oldest to newest
$labels = array_reverse($labels);
$values = array_reverse($values);

header('Content-Type: application/json');
echo json_encode(['labels' => $labels, 'values' => $values]);