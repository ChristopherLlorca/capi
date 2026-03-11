<?php
include 'db.php';
include 'header.php';

// Handle Search Query
$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Student Management</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="NewDocument.php" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-circle"></i> Add New Record
                    </a>
                </div>
            </div>

            <?php if(isset($_GET['msg']) && $_GET['msg'] == 'student_deleted'): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Student and all associated documents have been permanently removed.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="Students.php" class="row g-2">
                        <div class="col-md-10">
                            <input type="text" name="search" class="form-control" placeholder="Search by Student ID, Name, or Grade..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-dark w-100">Search</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Student ID</th>
                                <th>Full Name</th>
                                <th>Grade & Section</th> 
                                <th>Status</th> <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT s.*, d.grade_section 
                                      FROM students s
                                      LEFT JOIN (
                                          SELECT student_id, grade_section 
                                          FROM documents 
                                          WHERE id IN (SELECT MAX(id) FROM documents GROUP BY student_id)
                                      ) d ON s.student_id = d.student_id";
                            
                            if (!empty($search)) {
                                $query .= " WHERE s.student_id LIKE '%$search%' 
                                            OR s.firstname LIKE '%$search%' 
                                            OR s.lastname LIKE '%$search%'
                                            OR d.grade_section LIKE '%$search%'";
                            }
                            $query .= " ORDER BY s.lastname ASC";
                            
                            $result = $conn->query($query);

                            if ($result->num_rows > 0):
                                while ($row = $result->fetch_assoc()):
                            ?>
                                <tr>
                                    <td class="fw-bold text-primary"><?php echo $row['student_id']; ?></td>
                                    <td><?php echo $row['lastname'] . ", " . $row['firstname']; ?></td>
                                    <td><?php echo !empty($row['grade_section']) ? htmlspecialchars($row['grade_section']) : '<span class="text-muted">Not Set</span>'; ?></td>
                                    <td><span class="badge rounded-pill bg-primary"><i class="bi bi-person-check"></i> Active Record</span></td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="StudentProfile.php?id=<?php echo $row['student_id']; ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-folder2-open"></i> View Folder
                                            </a>
                                            <a href="delete_student.php?id=<?php echo $row['student_id']; ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('WARNING: Deleting this student will permanently remove ALL their uploaded documents. Continue?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No student records found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<style>
    .table thead th {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700;
    }
    .card {
        border-radius: 12px;
    }
    .btn-group .btn {
        margin: 0 2px;
        border-radius: 4px !important;
    }
</style>

<?php include 'footer.php'; ?>