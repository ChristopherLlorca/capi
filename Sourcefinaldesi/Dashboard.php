<?php
include 'db.php';
include 'header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <!-- Dashboard Overview -->
            <div class="border-bottom mb-4 pb-2">
                <h1 class="h2">Dashboard</h1>
            </div>

            <?php
            // Fetch document counts
            $pending = $conn->query("SELECT COUNT(*) AS total FROM documents WHERE status='Pending'")->fetch_assoc()['total'];
            $approved = $conn->query("SELECT COUNT(*) AS total FROM documents WHERE status='Approved'")->fetch_assoc()['total'];
            $rejected = $conn->query("SELECT COUNT(*) AS total FROM documents WHERE status='Rejected'")->fetch_assoc()['total'];
            $completed = $conn->query("SELECT COUNT(*) AS total FROM documents WHERE status='Completed'")->fetch_assoc()['total'];
            $total = $conn->query("SELECT COUNT(*) AS total FROM documents")->fetch_assoc()['total'];
            ?>

            <!-- Stat Boxes -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card text-white bg-warning shadow-sm border-0">
                        <div class="card-body text-center">
                            <h5 class="card-title">Pending</h5>
                            <h2 class="fw-bold"><?php echo $pending; ?></h2>
                            <p class="mb-0 small">Documents waiting for review</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success shadow-sm border-0">
                        <div class="card-body text-center">
                            <h5 class="card-title">Approved</h5>
                            <h2 class="fw-bold"><?php echo $approved; ?></h2>
                            <p class="mb-0 small">Documents approved</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-danger shadow-sm border-0">
                        <div class="card-body text-center">
                            <h5 class="card-title">Rejected</h5>
                            <h2 class="fw-bold"><?php echo $rejected; ?></h2>
                            <p class="mb-0 small">Documents rejected</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-info shadow-sm border-0">
                        <div class="card-body text-center">
                            <h5 class="card-title">Completed</h5>
                            <h2 class="fw-bold"><?php echo $completed; ?></h2>
                            <p class="mb-0 small">Documents fully processed</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Table -->
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Documents</h5>
                    <a href="NewDocument.php" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-circle"></i> Add New
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Tracking #</th>
                                    <th>Student Name</th>
                                    <th>Document Type</th>
                                    <th>Status</th>
                                    <th>Date Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM documents ORDER BY date_created DESC LIMIT 5";
                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $status_class = 'status-pending';
                                        if ($row['status'] == 'Approved') $status_class = 'status-approved';
                                        elseif ($row['status'] == 'Rejected') $status_class = 'status-rejected';
                                        elseif ($row['status'] == 'Completed') $status_class = 'status-completed';

                                        echo "
                                        <tr>
                                            <td>{$row['tracking_number']}</td>
                                            <td>{$row['student_name']}</td>
                                            <td>{$row['doc_type']}</td>
                                            <td><span class='status-badge $status_class'>{$row['status']}</span></td>
                                            <td>{$row['date_created']}</td>
                                            <td>
                                                <a href='DocumentView.php?id={$row['id']}' class='btn btn-sm btn-outline-primary'>
                                                    <i class='bi bi-eye'></i> View
                                                </a>
                                            </td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center text-muted'>No documents found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Total Documents -->
            <div class="alert alert-secondary text-center shadow-sm">
                <strong>Total Documents in System:</strong> <?php echo $total; ?>
            </div>
        </main>
    </div>
</div>

<?php include 'footer.php'; ?>
