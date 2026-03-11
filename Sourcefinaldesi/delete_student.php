<?php
include 'db.php';

if (isset($_GET['id'])) {
    $student_id = $_GET['id'];

    // 1. Delete all physical files belonging to this student
    $files = $conn->query("SELECT document_file FROM documents WHERE student_id = $student_id");
    while ($f = $files->fetch_assoc()) {
        if (!empty($f['document_file'])) {
            @unlink("uploads/" . $f['document_file']);
        }
    }

    // 2. Delete all document records
    $conn->query("DELETE FROM documents WHERE student_id = $student_id");

    // 3. Delete the student profile
    $conn->query("DELETE FROM students WHERE student_id = $student_id");

    header("Location: Students.php?msg=student_removed");
    exit();
}