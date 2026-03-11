<?php
include 'db.php';

if (isset($_POST['register'])) {
    $fname = $_POST['firstname'];
    $lname = $_POST['lastname'];
    $mi    = $_POST['middle_initial'];
    $age   = $_POST['age'];

    $sql = "INSERT INTO students (firstname, lastname, middle_initial, age) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $fname, $lname, $mi, $age);

    if ($stmt->execute()) {
        header("Location: Students.php?msg=StudentRegistered");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>