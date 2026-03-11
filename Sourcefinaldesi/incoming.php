<?php
include 'db.php';
include 'header.php';

// --- UPDATED REJECTION LOGIC WITH FEEDBACK ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_reject'])) {
    $reject_id = intval($_POST['reject_id']);
    $feedback = $_POST['rejection_feedback'];
    $status = 'Rejected';

    // Update status and save the feedback message
    $stmt = $conn->prepare("UPDATE documents SET status = ?, rejection_feedback = ? WHERE id = ?");
    $stmt->bind_param("ssi", $status, $feedback, $reject_id);
    
    if ($stmt->execute()) {
        // Here is where you would trigger the Gmail notification logic
        echo "<script>alert('Document rejected and feedback saved.'); window.location.href='Rejected.php';</script>";
        exit();
    }
    $stmt->close();
}

$sql = "SELECT * FROM documents WHERE status = 'Pending' ORDER BY date_created DESC";
$result = $conn->query($sql);
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Incoming Documents</h1>
                <a href="NewDocument.php" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle"></i> Add New
                </a>
            </div>

            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-dark">Pending Document Requests</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Tracking #</th>
                                    <th>Document Type</th>
                                    <th>Student Name</th>
                                    <th>Date Received</th>
                                    <th>Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "
                                        <tr>
                                            <td><span class='fw-bold text-primary'>{$row['tracking_number']}</span></td>
                                            <td>{$row['doc_type']}</td>
                                            <td>{$row['student_name']}</td>
                                            <td>
                                                <div class='fw-bold'>" . date('M d, Y', strtotime($row['date_created'])) . "</div>
                                                <small class='text-muted'><i class='bi bi-clock'></i> " . date('h:i A', strtotime($row['date_created'])) . "</small>
                                            </td>
                                            <td><span class='badge bg-warning text-dark'>{$row['status']}</span></td>
                                            <td class='text-center'>
                                                <div class='btn-group'>
                                                    <a href='DocumentView.php?id={$row['id']}' class='btn btn-sm btn-outline-primary'>
                                                        <i class='bi bi-eye'></i> View Details
                                                    </a>
                                                    <button type='button' 
                                                        class='btn btn-sm btn-outline-danger' 
                                                        data-bs-toggle='modal' 
                                                        data-bs-target='#rejectModal' 
                                                        data-id='{$row['id']}' 
                                                        data-name='{$row['student_name']}'>
                                                        <i class='bi bi-x-circle'></i> Reject
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center text-muted py-4'>No pending documents found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="rejectModalLabel">Confirm Rejection</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="reject_id" id="modal_reject_id">
                    <p>You are rejecting the document for: <strong id="modal_student_name"></strong></p>
                    
                    <div class="mb-3">
                        <label for="rejection_feedback" class="form-label fw-bold">Reason for Rejection (Feedback)</label>
                        <textarea class="form-control" name="rejection_feedback" rows="4" placeholder="Type the message to be sent via email..." required></textarea>
                        <div class="form-text text-muted">This message will be sent to the student's registered email.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="confirm_reject" class="btn btn-danger">Confirm Reject & Send Email</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Script to pass data to the modal
var rejectModal = document.getElementById('rejectModal')
rejectModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget
    var id = button.getAttribute('data-id')
    var name = button.getAttribute('data-name')
    
    document.getElementById('modal_reject_id').value = id
    document.getElementById('modal_student_name').textContent = name
})
</script>

<?php include 'footer.php'; ?>