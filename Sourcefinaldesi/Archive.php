<?php
include 'db.php';
include 'header.php';

if (isset($_POST['move_outgoing'])) {
    $doc_id = intval($_POST['doc_id']);
    $conn->query("UPDATE documents SET status='Out Going' WHERE id=$doc_id");
    header("Location: Archive.php?msg=sent");
    exit();
}

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Get specific count for the badge (All Approved documents)
$count_query = $conn->query("SELECT COUNT(*) AS total FROM documents WHERE status = 'Approved'");
$page_count = $count_query->fetch_assoc()['total'];
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <h1 class="h2 border-bottom pb-2">
                Document Archive <span class="badge rounded-pill bg-success"><?php echo $page_count; ?></span>
            </h1>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-2">
                        <div class="col-md-10">
                            <input type="text" name="search" class="form-control" placeholder="Search Tracking # or Student ID..." value="<?php echo htmlspecialchars($search); ?>">
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
                                <th>Tracking #</th>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM documents WHERE status='Approved'";
                            if (!empty($search)) {
                                $sql .= " AND (tracking_number LIKE '%$search%' OR student_id LIKE '%$search%')";
                            }
                            $result = $conn->query($sql);

                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                    <td>{$row['tracking_number']}</td>
                                    <td>{$row['student_id']}</td>
                                    <td>{$row['student_name']}</td>
                                    <td><span class='badge bg-success'>Approved</span></td>
                                    <td class='text-center'>
                                        <form method='POST'>
                                            <input type='hidden' name='doc_id' value='{$row['id']}'>
                                            <button type='submit' name='move_outgoing' class='btn btn-sm btn-primary'>Out Going</button>
                                        </form>
                                    </td>
                                </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>
<?php include 'footer.php'; ?>