<?php
include 'db.php';
include 'header.php';

if (!isset($_GET['id'])) {
    header("Location: Students.php");
    exit();
}

$student_id = $conn->real_escape_string($_GET['id']);

// 1. Fetch student details 
$student_query = $conn->query("SELECT * FROM students WHERE student_id = '$student_id'");
$student = $student_query->fetch_assoc();

if (!$student) {
    echo "Student not found.";
    exit();
}

// 2. Fetch latest document info (Includes date_created for the registration status)
$extra_info_query = $conn->query("SELECT contact, grade_section, date_created FROM documents WHERE student_id = '$student_id' ORDER BY date_created DESC LIMIT 1");
$extra_info = $extra_info_query->fetch_assoc();

$contact = isset($extra_info['contact']) ? $extra_info['contact'] : 'N/A';
$grade_section = isset($extra_info['grade_section']) ? $extra_info['grade_section'] : 'N/A';

// Pull the date from the latest document ($extra_info) instead of the student table
$registration_date = isset($extra_info['date_created']) ? date('M d, Y', strtotime($extra_info['date_created'])) : 'No Documents Registered';

$full_name = $student['firstname'] . ' ' . $student['lastname'];
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="card shadow-sm border-0 mb-4 bg-primary text-white p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-0"><?php echo htmlspecialchars($full_name); ?></h2>
                        <div class="mt-1">
                            <span class="me-3"><i class="bi bi-fingerprint"></i> ID: <?php echo htmlspecialchars($student['student_id']); ?></span>
                            <span class="me-3"><i class="bi bi-telephone"></i> <?php echo htmlspecialchars($contact); ?></span>
                            <span class="me-3"><i class="bi bi-book"></i> <?php echo htmlspecialchars($grade_section); ?></span>
                            <span class="me-3"><i class="bi bi-calendar-check"></i> Registered: <?php echo $registration_date; ?></span>
                        </div>
                    </div>
                    <a href="Students.php" class="btn btn-light btn-sm">Back to List</a>
                </div>
            </div>

            <h4 class="mb-3 text-secondary"><i class="bi bi-folder-fill"></i> Document Folder</h4>
            
            <div class="row">
                <?php
                $docs = $conn->query("SELECT * FROM documents WHERE student_id = '$student_id' ORDER BY date_created DESC");
                if ($docs->num_rows > 0):
                    while ($doc = $docs->fetch_assoc()):
                        $file_path = "uploads/" . $doc['file_path']; 
                ?>
                    <div class="col-md-3 mb-4">
                        <div class="card h-100 record-card border-0 shadow-sm">
                            <div class="card-img-top position-relative" style="height: 180px; overflow: hidden; cursor: pointer;" 
                                 onclick="openFull('<?php echo $file_path; ?>', '<?php echo addslashes($doc['doc_type']); ?>', '<?php echo addslashes($doc['tracking_number']); ?>')">
                                <img src="<?php echo $file_path; ?>" class="w-100 h-100 object-fit-cover" alt="Document">
                                <div class="overlay">
                                    <i class="bi bi-zoom-in text-white fs-2"></i>
                                </div>
                            </div>
                            <div class="card-body p-2">
                                <h6 class="card-title mb-0"><?php echo htmlspecialchars($doc['doc_type']); ?></h6>
                                <p class="small text-muted mb-0">Ref: <?php echo htmlspecialchars($doc['tracking_number']); ?></p>
                                
                                <p class="text-muted mb-0" style="font-size: 0.75rem;">
                                    <i class="bi bi-clock-history"></i> Uploaded: <?php echo date('M d, Y', strtotime($doc['date_created'])); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endwhile; else: ?>
                    <div class="col-12 text-center py-5">
                        <p class="text-muted">No documents found.</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<div class="modal fade" id="fullModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-dark border-0">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white" id="mTitle"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 text-center">
                <img src="" id="mImg" class="img-fluid" style="max-height: 85vh; width: auto;">
            </div>
        </div>
    </div>
</div>

<script>
/**
 * Opens the document in a full-screen modal.
 * @param {string} src - The path to the image.
 * @param {string} title - The document type.
 * @param {string} ref - The tracking reference code.
 */
function openFull(src, title, ref) {
    document.getElementById('mImg').src = src;
    // UPDATED: Displays the title and the reference code together
    document.getElementById('mTitle').innerText = title + " (Ref: " + ref + ")";
    var myModal = new bootstrap.Modal(document.getElementById('fullModal'));
    myModal.show();
}
</script>

<style>
    .record-card { transition: transform 0.2s ease; border-radius: 10px; overflow: hidden; border: 1px solid #eee; }
    .record-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
    .object-fit-cover { object-fit: cover; }
    .overlay {
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(13, 110, 253, 0.4);
        display: flex; align-items: center; justify-content: center;
        opacity: 0; transition: opacity 0.3s;
    }
    .card-img-top:hover .overlay { opacity: 1; }
</style>

<?php include 'footer.php'; ?>