<?php
session_start();
include 'db.php';

// Get form data
$playerId = $_POST['playerId'];
$name = $_POST['name'];
$role = $_POST['role'];
$phone = $_POST['phone'];
$age = $_POST['age'];
$base_price = $_POST['base_price'];

// Check if player exists
$check_query = "SELECT id FROM players WHERE id = ?";
$stmt = $con->prepare($check_query);
$stmt->bind_param("i", $playerId);
$stmt->execute();
if($stmt->get_result()->num_rows == 0) {
    header("Location: ../update_player.html?msg=Player not found");
    exit();
}

// Handle file upload if new image is provided
if(isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
    $target_dir = "../assits/players/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
    $image_path = uniqid() . '.' . $imageFileType;
    $target_file = $target_dir . $image_path;

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if($check === false) {
        header("Location: ../update_player.html?msg=File is not an image");
        exit();
    }

    // Check file size (5MB max)
    if ($_FILES["image"]["size"] > 5000000) {
        header("Location: ../update_player.html?msg=File is too large");
        exit();
    }

    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        header("Location: ../update_player.html?msg=Only JPG, JPEG & PNG files are allowed");
        exit();
    }

    // Upload file
    if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        header("Location: ../update_player.html?msg=Error uploading file");
        exit();
    }

    // Update with new image
    $sql = "UPDATE players SET name=?, role=?, phone_number=?, age=?, base_price=?, image_path=? WHERE id=?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("sssiisi", $name, $role, $phone, $age, $base_price, $image_path, $playerId);
} else {
    // Update without changing image
    $sql = "UPDATE players SET name=?, role=?, phone_number=?, age=?, base_price=? WHERE id=?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("sssiii", $name, $role, $phone, $age, $base_price, $playerId);
}

if ($stmt->execute()) {
    header("Location: ../viewPlayer.html?msg=Player updated successfully");
} else {
    header("Location: ../update_player.html?msg=Error updating player");
}

$stmt->close();
$con->close();
?> 