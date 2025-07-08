<?php
require 'db.php';

//Calculate total spent by each team and update their budget
$teams_query = "SELECT id, budget FROM teams";
$teams_result = mysqli_query($con, $teams_query);

while ($team = mysqli_fetch_assoc($teams_result)) {
    $teamId = $team['id'];
    $budget = $team['budget'];
    $spent_query = "SELECT SUM(soldPrice) as total_spent FROM sold_player WHERE teamId = ?";
    $stmt = $con->prepare($spent_query);
    $stmt->bind_param("i", $teamId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $total_spent = $row['total_spent'] ?? 0;
    $stmt->close();

    // Add total_spent to team's budget
    $new_budget = $budget + $total_spent;
    $update_query = "UPDATE teams SET budget = ? WHERE id = ?";
    $stmt2 = $con->prepare($update_query);
    $stmt2->bind_param("ii", $new_budget, $teamId);
    $stmt2->execute();
    $stmt2->close();
}

//Reset the auction 
$tables = [
    'sold_player',
    'unsold_player'
];

foreach ($tables as $table) {
    $sql = "DELETE FROM $table";
    mysqli_query($con, $sql);
}

foreach ($tables as $table) {
    $sql = "ALTER TABLE $table AUTO_INCREMENT = 1";
    mysqli_query($con, $sql);
}

echo "Reset complete: Budgets updated and player tables cleared.";
?>