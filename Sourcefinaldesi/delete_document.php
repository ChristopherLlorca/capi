<?php
include 'db.php';

if (isset($_GET['id']) && isset($_GET['student_id'])) {
    $id = $_GET['id'];
    $student_id = $_GET['student_id'];

    // 1. Find the file name to delete it from the physical 'uploads' folder
    $query = $conn->query("SELECT document_file FROM documents WHERE id = $id");
    $file_data = $query->fetch_assoc();
    
    if ($file_data && !empty($file_data['document_file'])) {
        $file_path = "uploads/" . $file_data['document_file'];
        if (file_exists($file_path)) {
            unlink($file_path); // Physically deletes the image file
        }
    }

    // 2. Delete the database record
    $conn->query("DELETE FROM documents WHERE id = $id");

    header("Location: StudentProfile.php?id=$student_id&msg=deleted");
    exit();
}