<?php
include 'db.php';
include 'header.php';

// Handle Delete Request (Kept original logic)
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    
    // 1. Get the file name to delete it from the server folder
    $file_query = $conn->query("SELECT file_path FROM documents WHERE id = '$delete_id'"); // Fixed column name to match previous files
    $file_data = $file_query->fetch_assoc();
    
    if ($file_data && !empty($file_data['file_path'])) {
        $file_path = "uploads/" . $file_data['file_path'];
        if (file_exists($file_path)) {
            unlink($file_path); // Physically delete the file
        }
    }

    // 2. Delete the record from the database
    $conn->query("DELETE FROM documents WHERE id = '$delete_id'");
    header("Location: Rejected.php?msg=deleted");
    exit();
}

/**
 * NOTE: To actually send Gmails, you should ideally use PHPMailer.
 * Below is the UI and database logic to handle the rejection feedback.
 */
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2 text-danger">Rejected Documents</h1>
            </div>

            <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Document record and file permanently deleted.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-dark">List of Rejected Documents</h5>
                        <form method="GET" class="input-group" style="width: 300px;">
                            <input type="text" name="search" class="form-control" placeholder="Search rejected..." 
                                value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-danger">
                                <tr>
                                    <th>Tracking #</th>
                                    <th>Student Name</th>
                                    <th>Document Type</th>
                                    <th>Rejection Reason</th> <th>Date Rejected</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
                                $sql = "SELECT * FROM documents WHERE status='Rejected'";

                                if (!empty($search)) {
                                    $sql .= " AND (tracking_number LIKE '%$search%' OR student_name LIKE '%$search%')";
                                }

                                $sql .= " ORDER BY date_created DESC";
                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        // Display "No reason provided" if the feedback column is empty
                                        $reason = !empty($row['rejection_feedback']) ? htmlspecialchars($row['rejection_feedback']) : '<i class="text-muted small">No reason provided</i>';
                                        
                                        echo "
                                        <tr>
                                            <td><span class='fw-bold'>{$row['tracking_number']}</span></td>
                                            <td>{$row['student_name']}</td>
                                            <td>{$row['doc_type']}</td>
                                            <td><span class='text-danger'>$reason</span></td> 
                                            <td>{$row['date_created']}</td>
                                            <td>
                                                <div class='btn-group'>
                                                    <a href='DocumentView.php?id={$row['id']}' class='btn btn-sm btn-outline-primary'>
                                                        <i class='bi bi-eye'></i> View
                                                    </a>
                                                    <a href='Rejected.php?delete_id={$row['id']}' 
                                                       class='btn btn-sm btn-outline-danger' 
                                                       onclick=\"return confirm('Delete this record permanently?')\">
                                                        <i class='bi bi-trash'></i> Delete
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center text-muted py-4'>No rejected documents found.</td></tr>";
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

<?php include 'footer.php'; ?>