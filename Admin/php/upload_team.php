<?php
session_start();
include 'db.php';

header('Content-Type: application/json');
$response = [];

// Get form data
$name = $_POST['name'];
$owner = $_POST['owner'];
$budget = $_POST['budget'];

// Check if team already exists
$check_sql = "SELECT id FROM teams WHERE name = ?";
$check_stmt = $con->prepare($check_sql);
$check_stmt->bind_param("s", $name);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows > 0) {
    $response['status'] = 'error';
    $response['message'] = 'Team with this name already exists';
    echo json_encode($response); exit();
}
$check_stmt->close();

// Handle file upload
$target_dir = "../assits/teams/";
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

// Upload file
if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
    // Insert into database
    $sql = "INSERT INTO teams (name, owner, budget, image_path) VALUES (?, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ssis", $name, $owner, $budget, $image_path);
    
    if ($stmt->execute()) {
        $response['status'] = 'success';
        $response['message'] = 'Team added successfully';
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Error adding team to database';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Error uploading file';
}

$stmt->close();
$con->close();
echo json_encode($response); exit();
?>