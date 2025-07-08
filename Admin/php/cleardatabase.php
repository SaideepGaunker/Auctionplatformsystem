<?php
include 'db.php';

// List all tables you want to clear
$tables = [
    'sold_player',
    'players',
    'teams',
    'unsold_player'
];

foreach ($tables as $table) {
    $sql = "DELETE FROM $table";
    mysqli_query($con, $sql);
}

// Optionally, reset AUTO_INCREMENT counters
foreach ($tables as $table) {
    $sql = "ALTER TABLE $table AUTO_INCREMENT = 1";
    mysqli_query($con, $sql);
}

echo "All data deleted from tables.";
?>
