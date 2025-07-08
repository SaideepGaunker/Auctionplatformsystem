<?php
session_start();

// Check if it's an exit request
if(isset($_GET['next']) && $_GET['next'] == 0) {
    // Reset the session
    unset($_SESSION['current_player_index']);
    header("Location: ../../Admin/index.html");
    exit();
}

// Increment the current player index for next player
$_SESSION['current_player_index']++;
header("Location: ../display.html");
exit();
?> 