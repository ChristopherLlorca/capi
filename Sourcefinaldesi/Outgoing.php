<?php
include 'db.php';
include 'header.php';

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
// Get sort order from URL, default to DESC (Newest)
$sort = isset($_GET['sort']) && $_GET['sort'] == 'ASC' ? 'ASC' : 'DESC';

// Get specific count for the badge
$count_query = $conn->query("SELECT COUNT(*) AS total FROM documents WHERE status = 'Out Going'");
$page_count = $count_query->fetch_assoc()['total'];
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <h1 class="h2 border-bottom pb-2">
                Outgoing Documents <span class="badge rounded-pill bg-info text-dark"><?php echo $page_count; ?></span>
            </h1>

            <div class="card shadow-sm border-0 mt-3">
                <div class="card-body">
                    <form method="GET" action="Outgoing.php" class="row g-2 mb-3">
                        <div class="col-md-8">
                            <input type="text" name="search" class="form-control" placeholder="Search by Tracking # or Student ID..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Search</button>
                        </div>
                        <div class="col-md-2">
                            <a href="Outgoing.php?search=<?php echo urlencode($search); ?>&sort=<?php echo ($sort == 'DESC' ? 'ASC' : 'DESC'); ?>" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-sort-<?php echo ($sort == 'DESC' ? 'down' : 'up'); ?>"></i> 
                                <?php echo ($sort == 'DESC' ? 'Newest' : 'Oldest'); ?>
                            </a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-info">
                                <tr>
                                    <th>Tracking #</th>
                                    <th>Student ID</th>
                                    <th>Student Name</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM documents WHERE status = 'Out Going'";
                                if (!empty($search)) {
                                    $sql .= " AND (tracking_number LIKE '%$search%' OR student_id LIKE '%$search%')";
                                }
                                
                                // Apply the sort order to the query
                                $sql .= " ORDER BY date_created $sort";
                                
                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>
                                            <td class='fw-bold text-primary'>{$row['tracking_number']}</td>
                                            <td>{$row['student_id']}</td>
                                            <td>{$row['student_name']}</td>
                                            <td><span class='badge bg-info text-dark'>Out Going</span></td>
                                            <td>
                                                <a href='DocumentView.php?id={$row['id']}' class='btn btn-sm btn-outline-primary'>
                                                    <i class='bi bi-eye'></i> View & Complete
                                                </a>
                                            </td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='text-center py-4 text-muted'>No outgoing documents found.</td></tr>";
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