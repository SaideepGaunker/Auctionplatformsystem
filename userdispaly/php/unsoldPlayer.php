<?php
session_start();
include '../../Admin/php/db.php';

header('Content-Type: application/json');
$error = null;
$message = null;
$next_player = false;

if (isset($_POST['playerId']) && isset($_POST['playerName'])) {
    $playerId = $_POST['playerId'];
    $playerName = $_POST['playerName'];

    // First check if player exists
    $check_sql = "SELECT id FROM players WHERE id = '$playerId'";
    $check_result = mysqli_query($con, $check_sql);

    if (mysqli_num_rows($check_result) > 0) {
        // Check if player is already marked as unsold
        $check_unsold = "SELECT playerId FROM unsold_player WHERE playerId = '$playerId'";
        $unsold_result = mysqli_query($con, $check_unsold);

        if (mysqli_num_rows($unsold_result) == 0) {
            // Player not already marked as unsold, proceed with insertion
            $sql = "INSERT INTO unsold_player (playerId) VALUES ('$playerId')";
            $result = mysqli_query($con, $sql);
            
            if ($result) {
                $message = "Player " . htmlspecialchars($playerName) . " marked as unsold";
            } else {
                $error = "Error marking player as unsold: " . mysqli_error($con);
            }
        } else {
            $message = "Player " . htmlspecialchars($playerName) . " is already marked as unsold";
        }
    } else {
        $error = "Player not found in database";
    }
} else {
    $error = "Missing player information";
}

// Get total players count for navigation
$total_query = "SELECT COUNT(*) as total FROM players";
$total_result = mysqli_query($con, $total_query);
$total_players = mysqli_fetch_assoc($total_result)['total'];
$next_player = (isset($_SESSION['current_player_index']) && $_SESSION['current_player_index'] < $total_players - 1);

$response = [];
if ($error) {
    $response['status'] = 'error';
    $response['message'] = $error;
    $response['next_player'] = false;
} else {
    $response['status'] = 'success';
    $response['message'] = $message;
    $response['next_player'] = $next_player;
}
echo json_encode($response);
exit();