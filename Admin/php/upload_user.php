<?php
session_start();
include 'db.php';

header('Content-Type: application/json');
$response = [];

// Get form data
$name = $_POST['name'];
$role = $_POST['role'];
$phone = $_POST['phone'];
$age = $_POST['age'];
$base_price = $_POST['base_price'];

// Handle file upload
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
    $response['status'] = 'error';
    $response['message'] = 'File is not an image.';
    echo json_encode($response); exit();
}

// Check file size (5MB max)
if ($_FILES["image"]["size"] > 5000000) {
    $response['status'] = 'error';
    $response['message'] = 'File is too large.';
    echo json_encode($response); exit();
}

// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
    $response['status'] = 'error';
    $response['message'] = 'Only JPG, JPEG & PNG files are allowed.';
    echo json_encode($response); exit();
}

// Check if phone number already exists
$check_phone = "SELECT id FROM players WHERE phone_number = ?";
$stmt = $con->prepare($check_phone);
$stmt->bind_param("s", $phone);
$stmt->execute();
if($stmt->get_result()->num_rows > 0) {
    $response['status'] = 'error';
    $response['message'] = 'Phone number already exists';
    echo json_encode($response); exit();
}

// Upload file
if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
    // Insert into database
    $sql = "INSERT INTO players (name, role, phone_number, age, base_price, image_path) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("sssiis", $name, $role, $phone, $age, $base_price, $image_path);
    
    if ($stmt->execute()) {
        $response['status'] = 'success';
        $response['message'] = 'Player added successfully';
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Error adding player to database';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Error uploading file';
}

$stmt->close();
$con->close();
echo json_encode($response); exit();
?>