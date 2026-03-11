<?php
include 'db.php';
include 'header.php';

// Get the ID from the URL
$doc_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch document details
$stmt = $conn->prepare("SELECT * FROM documents WHERE id = ?");
$stmt->bind_param("i", $doc_id);
$stmt->execute();
$result = $stmt->get_result();
$doc = $result->fetch_assoc();

if (!$doc) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Document not found.</div></div>";
    exit;
}

// Logic to approve/move to archive (optional action button)
if (isset($_POST['approve_doc'])) {
    $update = $conn->prepare("UPDATE documents SET status='Approved', current_location='Archive' WHERE id=?");
    $update->bind_param("i", $doc_id);
    if ($update->execute()) {
        header("Location: incoming.php?msg=approved");
        exit();
    }
}
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="incoming.php">Incoming</a></li>
                    <li class="breadcrumb-item active">View Document</li>
                </ol>
            </nav>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2">Document Details: <?php echo htmlspecialchars($doc['tracking_number']); ?></h1>
                <form method="POST">
                    <button type="submit" name="approve_doc" class="btn btn-success shadow-sm">
                        <i class="bi bi-check-lg"></i> Approve & Archive
                    </button>
                </form>
            </div>

            <div class="row">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white fw-bold">Information</div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr><th class="text-muted">Student ID:</th><td><?php echo htmlspecialchars($doc['student_id']); ?></td></tr>
                                <tr><th class="text-muted">Full Name:</th><td><?php echo htmlspecialchars($doc['student_name']); ?></td></tr>
                                <tr><th class="text-muted">Doc Type:</th><td><?php echo htmlspecialchars($doc['doc_type']); ?></td></tr>
                                <tr><th class="text-muted">Grade/Section:</th><td><?php echo htmlspecialchars($doc['grade_section']); ?></td></tr>
                                <tr><th class="text-muted">Contact:</th><td><?php echo htmlspecialchars($doc['contact']); ?></td></tr>
                                <tr><th class="text-muted">Email:</th><td><?php echo htmlspecialchars($doc['email']); ?></td></tr>
                                <tr><th class="text-muted">From School:</th><td><?php echo htmlspecialchars($doc['from_school']); ?></td></tr>
                                <tr><th class="text-muted">Date Filed:</th><td><?php echo date('F j, Y, g:i a', strtotime($doc['date_created'])); ?></td></tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white fw-bold">Digital Copy / Attachment</div>
                        <div class="card-body text-center">
                            <?php 
                            $file_name = $doc['file_path'];
                            $file_path = "uploads/" . $file_name;

                            if (!empty($file_name) && file_exists($file_path)): 
                                $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
                                
                                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                    <a href="<?php echo $file_path; ?>" target="_blank">
                                        <img src="<?php echo $file_path; ?>" class="img-fluid rounded border shadow-sm" alt="Document Image">
                                    </a>
                                    <p class="mt-2 small text-muted">Click image to view full size</p>

                                <?php elseif ($ext === 'pdf'): ?>
                                    <div class="py-5">
                                        <i class="bi bi-file-earmark-pdf text-danger" style="font-size: 4rem;"></i>
                                        <h5 class="mt-3">PDF Document</h5>
                                        <a href="<?php echo $file_path; ?>" target="_blank" class="btn btn-primary">
                                            Open PDF in New Tab
                                        </a>
                                    </div>

                                <?php else: ?>
                                    <p class="text-muted">Unsupported file format (<?php echo $ext; ?>).</p>
                                    <a href="<?php echo $file_path; ?>" download class="btn btn-secondary">Download File</a>
                                <?php endif; ?>

                            <?php else: ?>
                                <div class="py-5 text-muted">
                                    <i class="bi bi-file-earmark-x" style="font-size: 3rem;"></i>
                                    <p>No file uploaded or file not found on server.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'footer.php'; ?>