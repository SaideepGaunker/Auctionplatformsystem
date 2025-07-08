<?php
session_start();
include 'db.php';

header('Content-Type: application/json');
$response = [];

// Get form data
$teamId = $_POST['teamId'];
$name = $_POST['name'];
$owner = $_POST['owner'];
$budget = $_POST['budget'];

// Check if team exists
$check_query = "SELECT id FROM teams WHERE id = ?";
$stmt = $con->prepare($check_query);
$stmt->bind_param("i", $teamId);
$stmt->execute();
if($stmt->get_result()->num_rows == 0) {
    $response['status'] = 'error';
    $response['message'] = 'Team not found';
    echo json_encode($response); exit();
}

// Handle file upload if new image is provided
if(isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
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
        $response['message'] = 'File is not an image';
        echo json_encode($response); exit();
    }

    // Check file size (5MB max)
    if ($_FILES["image"]["size"] > 5000000) {
        $response['status'] = 'error';
        $response['message'] = 'File is too large';
        echo json_encode($response); exit();
    }

    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        $response['status'] = 'error';
        $response['message'] = 'Only JPG, JPEG & PNG files are allowed';
        echo json_encode($response); exit();
    }

    // Upload file
    if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $response['status'] = 'error';
        $response['message'] = 'Error uploading file';
        echo json_encode($response); exit();
    }

    // Update with new image
    $sql = "UPDATE teams SET name=?, owner=?, budget=?, image_path=? WHERE id=?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ssisi", $name, $owner, $budget, $image_path, $teamId);
} else {
    // Update without changing image
    $sql = "UPDATE teams SET name=?, owner=?, budget=? WHERE id=?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ssii", $name, $owner, $budget, $teamId);
}

if ($stmt->execute()) {
    $response['status'] = 'success';
    $response['message'] = 'Team updated successfully';
} else {
    $response['status'] = 'error';
    $response['message'] = 'Error updating team';
}

$stmt->close();
$con->close();
echo json_encode($response); exit();
?> 