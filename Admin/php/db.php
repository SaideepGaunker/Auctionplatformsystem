<?php
    $con = mysqli_connect("localhost","root","","auction");
    if(!$con) {
    die("Connection failed: " . mysqli_connect_error());
    } 
?>
