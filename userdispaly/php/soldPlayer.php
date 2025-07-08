<?php
session_start();
include '../../admin/php/db.php';

header('Content-Type: application/json');
$error = null;
$message = null;
$next_player = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $player_id = $_POST['player_id'];
    $team_id = $_POST['team_id'];
    $sold_price = $_POST['sold_price'];

    // First check if player exists
    $check_sql = "SELECT id FROM players WHERE id = '$player_id'";
    $check_result = mysqli_query($con, $check_sql);

    if (mysqli_num_rows($check_result) > 0) {
        // Check if player is already sold
        $check_sold = "SELECT playerId FROM sold_player WHERE playerId = '$player_id'";
        $sold_result = mysqli_query($con, $check_sold);

        if (mysqli_num_rows($sold_result) == 0) {
            // Check team budget before sale
            $budget_query = "SELECT budget FROM teams WHERE id = $team_id";
            $budget_result = mysqli_query($con, $budget_query);
            $team_budget = mysqli_fetch_assoc($budget_result)['budget'];
            if ($team_budget < $sold_price) {
                $error = "Team does not have enough budget to purchase this player.";
            } else {
                // Get player and team names for the message
                $player_query = "SELECT name FROM players WHERE id = $player_id";
                $team_query = "SELECT name FROM teams WHERE id = $team_id";
                
                $player_result = mysqli_query($con, $player_query);
                $team_result = mysqli_query($con, $team_query);
                
                $player_name = mysqli_fetch_assoc($player_result)['name'];
                $team_name = mysqli_fetch_assoc($team_result)['name'];

                // Insert into sold_players table with timestamp
                $insert_query = "INSERT INTO sold_player (playerId, teamId, soldPrice, time) 
                               VALUES ($player_id, $team_id, $sold_price, NOW())";
                
                if (mysqli_query($con, $insert_query)) {
                    // Update team budget
                    $update_budget_query = "UPDATE teams SET budget = budget - $sold_price WHERE id = $team_id";
                    mysqli_query($con, $update_budget_query);

                    $message = "Player " . htmlspecialchars($player_name) . " sold to " . htmlspecialchars($team_name) . " for ₹" . $sold_price;
                    // Only move to next player if sale is successful
                    $total_query = "SELECT COUNT(*) as total FROM players";
                    $total_result = mysqli_query($con, $total_query);
                    $total_players = mysqli_fetch_assoc($total_result)['total'];
                    $next_player = (isset($_SESSION['current_player_index']) && $_SESSION['current_player_index'] < $total_players - 1);
                } else {
                    $error = "Error marking player as sold: " . mysqli_error($con);
                }
            }
        } else {
            $message = "Player is already sold";
            $total_query = "SELECT COUNT(*) as total FROM players";
            $total_result = mysqli_query($con, $total_query);
            $total_players = mysqli_fetch_assoc($total_result)['total'];
            $next_player = (isset($_SESSION['current_player_index']) && $_SESSION['current_player_index'] < $total_players - 1); 
        }
    } else {
        $error = "Player not found in database";
    }
} else {
    $error = "Invalid request method";
}

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