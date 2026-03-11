<?php
include 'db.php';
include 'header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <?php
            if (!isset($_GET['id'])) {
                echo "<div class='alert alert-danger'>No document ID provided.</div>";
                include 'footer.php';
                exit;
            }

            $doc_id = intval($_GET['id']);
            $query = "SELECT * FROM documents WHERE id = $doc_id";
            $result = $conn->query($query);

            if ($result->num_rows === 0) {
                echo "<div class='alert alert-warning'>Document not found.</div>";
                include 'footer.php';
                exit;
            }

            $doc = $result->fetch_assoc();
            ?>

            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center border-bottom pb-2 mb-3">
                <h1 class="h2">Tracking Document</h1>
                <a href="Outgoing.php" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">Tracking Number: <strong><?php echo htmlspecialchars($doc['tracking_number']); ?></strong></h5>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Document Type:</strong> <?php echo htmlspecialchars($doc['doc_type']); ?></p>
                            <p><strong>Student Name:</strong> <?php echo htmlspecialchars($doc['student_name']); ?></p>
                            <p><strong>From School:</strong> <?php echo htmlspecialchars($doc['from_school']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Grade & Section:</strong> <?php echo htmlspecialchars($doc['grade_section']); ?></p>
                            <p><strong>Contact:</strong> <?php echo htmlspecialchars($doc['contact']); ?></p>
                            <p><strong>Date Created:</strong> <?php echo htmlspecialchars($doc['date_created']); ?></p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h6>Document Status: 
                            <span class="badge 
                                <?php 
                                    if ($doc['status'] == 'Pending') echo 'bg-warning text-dark';
                                    elseif ($doc['status'] == 'Approved') echo 'bg-success';
                                    elseif ($doc['status'] == 'Completed') echo 'bg-info text-dark';
                                    elseif ($doc['status'] == 'Rejected') echo 'bg-danger';
                                ?>">
                                <?php echo htmlspecialchars($doc['status']); ?>
                            </span>
                        </h6>
                    </div>

                    <!-- Tracking Progress Bar -->
                    <div class="progress" style="height: 25px;">
                        <?php
                        $progress = 0;
                        switch ($doc['status']) {
                            case 'Pending': $progress = 25; break;
                            case 'Approved': $progress = 75; break;
                            case 'Completed': $progress = 100; break;
                            case 'Rejected': $progress = 100; break;
                        }
                        ?>
                        <div class="progress-bar 
                            <?php echo ($doc['status'] == 'Rejected') ? 'bg-danger' : 'bg-success'; ?>" 
                            role="progressbar" 
                            style="width: <?php echo $progress; ?>%;" 
                            aria-valuenow="<?php echo $progress; ?>" 
                            aria-valuemin="0" 
                            aria-valuemax="100">
                            <?php echo $progress; ?>%
                        </div>
                    </div>
                </div>
            </div>

            <!-- Optional: Timeline Steps -->
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3">Tracking Timeline</h6>
                    <ul class="list-group">
                        <li class="list-group-item <?php echo ($doc['status'] == 'Pending' || $doc['status'] == 'Approved' || $doc['status'] == 'Completed' || $doc['status'] == 'Rejected') ? 'list-group-item-success' : ''; ?>">📥 Document Submitted</li>
                        <li class="list-group-item <?php echo ($doc['status'] == 'Approved' || $doc['status'] == 'Completed') ? 'list-group-item-success' : ''; ?>">✅ Approved by Registrar</li>
                        <li class="list-group-item <?php echo ($doc['status'] == 'Completed') ? 'list-group-item-success' : ''; ?>">📤 Completed / Sent Out</li>
                        <?php if ($doc['status'] == 'Rejected') : ?>
                            <li class="list-group-item list-group-item-danger">❌ Rejected</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'footer.php'; ?>
