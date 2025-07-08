<?php
session_start();
require "db.php";

$playerId = $_GET['playerId'];

// Check if player exists
$check_query = "SELECT id FROM players WHERE id = ?";
$stmt = $con->prepare($check_query);
$stmt->bind_param("i", $playerId);
$stmt->execute();

if($stmt->get_result()->num_rows === 0) {
    $error = "Player not found";
    header("Location: http://localhost/auction/Admin/viewPlayer.html?msg=" . $error);
    exit();
}

// Delete the player
$delete_query = "DELETE FROM players WHERE id = ?";
$stmt = $con->prepare($delete_query);
$stmt->bind_param("i", $playerId);

if($stmt->execute()) {
    $error = "Player deleted successfully";
} else {
    $error = "Error: " . $stmt->error;
}

header("Location: http://localhost/auction/Admin/viewPlayer.html?msg=" . $error);
$stmt->close();
$con->close();
?> 