<?php
session_start();
require "db.php";

$teamId = $_GET['teamId'];

// Check if team exists
$check_query = "SELECT id FROM teams WHERE id = ?";
$stmt = $con->prepare($check_query);
$stmt->bind_param("i", $teamId);
$stmt->execute();

if($stmt->get_result()->num_rows === 0) {
    $error = "Team not found";
    header("Location: http://localhost/auction/Admin/viewTeam.html?msg=" . $error);
    exit();
}

// Delete the team
$delete_query = "DELETE FROM teams WHERE id = ?";
$stmt = $con->prepare($delete_query);
$stmt->bind_param("i", $teamId);

if($stmt->execute()) {
    $error = "Team deleted successfully";
} else {
    $error = "Error: " . $stmt->error;
}

header("Location: http://localhost/auction/Admin/viewTeam.html?msg=" . $error);
$stmt->close();
$con->close();
?> 