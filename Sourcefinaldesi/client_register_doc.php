<?php
include 'db.php';

$message = '';

/**
 * Generates a unique tracking number by checking the database 
 * for existing entries before returning the result.
 */
function generateUniqueTracking($conn)
{
    $exists = true;
    $new_id = "";

    while ($exists) {
        // Generate a random 8-character hex string prefixed with LHS_
        $new_id = "LHS_" . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));

        // Check if it already exists in the 'documents' table
        $check = $conn->prepare("SELECT tracking_number FROM documents WHERE tracking_number = ?");
        $check->bind_param("s", $new_id);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows == 0) {
            $exists = false; // ID is unique, exit loop
        }
        $check->close();
    }
    return $new_id;
}

// Initial tracking number for the form display
$auto_tracking = generateUniqueTracking($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tracking_number = $_POST['tracking_number'];
    $doc_type = $_POST['doc_type'];
    $student_id = $_POST['student_id'];
    $student_name = $_POST['student_name'];
    $grade_section = $_POST['grade_section'];
    $contact = $_POST['contact'];
    $email = $_POST['email']; // Added email field
    $from_school = $_POST['from_school'];
    $current_location = "Registrar (Pending)";

    $date_created = date('Y-m-d H:i:s');
    $status = "Pending";

    // --- STEP 1: AUTOMATIC STUDENT REGISTRATION OR UPDATE ---
    $stmtCheck = $conn->prepare("SELECT student_id FROM students WHERE student_id = ?");
    $stmtCheck->bind_param("s", $student_id);
    $stmtCheck->execute();
    $checkStudent = $stmtCheck->get_result();

    if ($checkStudent->num_rows == 0) {
        $name_parts = explode(' ', $student_name, 2);
        $fname = $name_parts[0];
        $lname = isset($name_parts[1]) ? $name_parts[1] : 'N/A';

        // Added email to student registration
        $stmtIns = $conn->prepare("INSERT INTO students (student_id, firstname, lastname, age, date_created, email) VALUES (?, ?, ?, '0', ?, ?)");
        $stmtIns->bind_param("sssss", $student_id, $fname, $lname, $date_created, $email);
        $stmtIns->execute();
        $stmtIns->close();
    }
    $stmtCheck->close();

    // --- STEP 2: HANDLE FILE UPLOAD ---
    $file_name = "";
    if (isset($_FILES['document_file']) && $_FILES['document_file']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_name = time() . "_" . basename($_FILES["document_file"]["name"]);
        move_uploaded_file($_FILES["document_file"]["tmp_name"], $target_dir . $file_name);
    }

    // --- STEP 3: SAVE DOCUMENT WITH ERROR HANDLING ---
    try {
        // Added email to the documents table insert
        $sql = "INSERT INTO documents (tracking_number, doc_type, student_id, student_name, grade_section, contact, email, from_school, date_created, status, current_location, file_path) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssssss", $tracking_number, $doc_type, $student_id, $student_name, $grade_section, $contact, $email, $from_school, $date_created, $status, $current_location, $file_name);

        if ($stmt->execute()) {
            $message = "success";
            // Refresh the auto_tracking for the next form load
            $auto_tracking = generateUniqueTracking($conn);
        }
        $stmt->close();
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) { // Duplicate entry error code
            $message = "duplicate";
        } else {
            $message = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LHS - Submit Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .hero-section { background: linear-gradient(135deg, #0d6efd 0%, #004085 100%); color: white; padding: 60px 0; margin-bottom: 30px; }
        .form-card { margin-top: -30px; border-radius: 15px; border: none; }
        :root { --primary-color: #0056b3; --secondary-color: #e9ecef; }
        .navbar-brand img { height: 40px; }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="Dashboard.php">
                <img src="lhs-reglogo.png" alt="LHS Logo">
                <span class="ms-2">LHS - Document Tracking</span>
            </a>
        </div>
    </nav>

    <div class="hero-section text-center">
        <div class="container">
            <h1 class="display-5 fw-bold">Submit a New Document</h1>
            <p class="lead">Fill out the form below to register your document for processing.</p>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <?php if ($message == 'success'): ?>
                    <div class="alert alert-success border-0 shadow-sm mb-4">
                        <h4 class="alert-heading"><i class="bi bi-check-circle-fill"></i> Submission Successful!</h4>
                        <p>Your document has been filed. Please save your Tracking Number:</p>
                        <h3 class="fw-bold mb-0"><?php echo $tracking_number; ?></h3>
                    </div>
                <?php elseif ($message == 'duplicate'): ?>
                    <div class="alert alert-warning border-0 shadow-sm mb-4">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> This tracking number was already taken. A new
                        one has been generated. Please try submitting again.
                    </div>
                <?php elseif ($message == 'error'): ?>
                    <div class="alert alert-danger border-0 shadow-sm mb-4">Error saving document. Please try again.</div>
                <?php endif; ?>

                <div class="card shadow-sm p-4 form-card">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Student ID / LRN</label>
                                <input type="text" name="student_id" class="form-control" placeholder="Enter ID Number" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Full Name</label>
                                <input type="text" name="student_name" class="form-control" placeholder="Firstname Lastname" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Contact Number</label>
                                <input type="text" name="contact" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="lhs_student@gmail.com" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Grade & Section</label>
                                <input type="text" name="grade_section" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">From School (If transferee)</label>
                                <input type="text" name="from_school" class="form-control" value="N/A">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Document Type</label>
                            <select name="doc_type" class="form-select" required>
                                <option value="" disabled selected>Select Document</option>
                                <option value="Birth Certificate">Birth Certificate</option>
                                <option value="Form 137">Form 137</option>
                                <option value="Report Card">Report Card</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tracking Number</label>
                                <input type="text" name="tracking_number" class="form-control bg-light" value="<?php echo $auto_tracking; ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-primary">Upload Digital Copy (PDF/Image)</label>
                                <input type="file" name="document_file" class="form-control" accept="image/*,application/pdf" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100 mt-3 fw-bold shadow-sm">
                            Submit Document to Registrar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer mt-auto py-3 bg-light">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <span class="text-muted">© 2025 Lagro High School - Document Tracking System</span>
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="text-muted">Version 1.0.0</span>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>