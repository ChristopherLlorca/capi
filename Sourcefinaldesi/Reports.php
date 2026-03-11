<?php
include 'db.php';
include 'header.php';

// --- ADDED: Filter Logic ---
$where_clause = "";
$selected_month = "";
if (isset($_GET['filter_month']) && !empty($_GET['filter_month'])) {
    $selected_month = $conn->real_escape_string($_GET['filter_month']);
    $where_clause = " WHERE DATE_FORMAT(date_created, '%Y-%m') = '$selected_month'";
}

// --- Get statistics from the database ---
// Added $where_clause to the existing queries to support the reset/filter functionality
$total_docs = $conn->query("SELECT COUNT(*) AS total FROM documents" . $where_clause)->fetch_assoc()['total'];
$pending = $conn->query("SELECT COUNT(*) AS total FROM documents WHERE status='Pending'" . str_replace('WHERE', 'AND', $where_clause))->fetch_assoc()['total'];
$approved = $conn->query("SELECT COUNT(*) AS total FROM documents WHERE status='Approved'" . str_replace('WHERE', 'AND', $where_clause))->fetch_assoc()['total'];
$completed = $conn->query("SELECT COUNT(*) AS total FROM documents WHERE status='Completed'" . str_replace('WHERE', 'AND', $where_clause))->fetch_assoc()['total'];
$rejected = $conn->query("SELECT COUNT(*) AS total FROM documents WHERE status='Rejected'" . str_replace('WHERE', 'AND', $where_clause))->fetch_assoc()['total'];

// Get document count by type
$type_data = $conn->query("SELECT doc_type, COUNT(*) AS count FROM documents" . $where_clause . " GROUP BY doc_type");

// Monthly document data (for the line chart)
$monthly_data = $conn->query("
    SELECT DATE_FORMAT(date_created, '%Y-%m') AS month, COUNT(*) AS count 
    FROM documents 
    GROUP BY month 
    ORDER BY month ASC
");
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Reports & Analytics</h1>
                
                <div class="btn-toolbar mb-2 mb-md-0">
                    <form method="GET" action="Reports.php" class="d-flex me-2">
                        <input type="month" name="filter_month" class="form-control form-control-sm me-2" value="<?php echo $selected_month; ?>">
                        <button type="submit" class="btn btn-sm btn-primary me-2">Filter</button>
                        <a href="Reports.php" class="btn btn-sm btn-outline-danger">Reset</a>
                    </form>
                    <button type="button" class="btn btn-sm btn-success" onclick="window.print()">
                        <i class="bi bi-download"></i> Export
                    </button>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5>Total Documents</h5>
                            <h2 class="text-primary"><?php echo $total_docs; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5>Pending</h5>
                            <h2 class="text-warning"><?php echo $pending; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5>Approved</h5>
                            <h2 class="text-success"><?php echo $approved; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5>Completed</h5>
                            <h2 class="text-info"><?php echo $completed; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5>Rejected</h5>
                            <h2 class="text-danger"><?php echo $rejected; ?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header"><h5 class="mb-0">Documents by Type</h5></div>
                        <div class="card-body">
                            <canvas id="typeChart" height="250"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header"><h5 class="mb-0">Monthly Document Activity</h5></div>
                        <div class="card-body">
                            <canvas id="monthChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">All Document Records <?php echo $selected_month ? "($selected_month)" : ""; ?></h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Tracking #</th>
                                    <th>Type</th>
                                    <th>Student Name</th>
                                    <th>From School</th>
                                    <th>Date Created</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Added $where_clause here to filter the table results too
                                $docs = $conn->query("SELECT * FROM documents " . $where_clause . " ORDER BY date_created DESC");
                                if ($docs->num_rows > 0) {
                                    while ($d = $docs->fetch_assoc()) {
                                        $status_class = 'status-pending';
                                        if ($d['status'] == 'Approved') $status_class = 'status-approved';
                                        elseif ($d['status'] == 'Completed') $status_class = 'status-completed';
                                        elseif ($d['status'] == 'Rejected') $status_class = 'status-rejected';

                                        echo "
                                        <tr>
                                            <td>{$d['tracking_number']}</td>
                                            <td>{$d['doc_type']}</td>
                                            <td>{$d['student_name']}</td>
                                            <td>{$d['from_school']}</td>
                                            <td>{$d['date_created']}</td>
                                            <td><span class='status-badge $status_class'>{$d['status']}</span></td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center text-muted'>No records found</td></tr>";
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const typeCtx = document.getElementById('typeChart');
    const typeChart = new Chart(typeCtx, {
        type: 'pie',
        data: {
            labels: [
                <?php
                $labels = [];
                $values = [];
                $type_data->data_seek(0);
                while ($row = $type_data->fetch_assoc()) {
                    $labels[] = '"' . $row['doc_type'] . '"';
                    $values[] = $row['count'];
                }
                echo implode(',', $labels);
                ?>
            ],
            datasets: [{
                data: [<?php echo implode(',', $values); ?>],
                backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#0dcaf0'],
            }]
        },
        options: {
            plugins: { legend: { position: 'bottom' } }
        }
    });

    const monthCtx = document.getElementById('monthChart');
    const monthChart = new Chart(monthCtx, {
        type: 'line',
        data: {
            labels: [
                <?php
                $months = [];
                $counts = [];
                $monthly_data->data_seek(0);
                while ($row = $monthly_data->fetch_assoc()) {
                    $months[] = '"' . $row['month'] . '"';
                    $counts[] = $row['count'];
                }
                echo implode(',', $months);
                ?>
            ],
            datasets: [{
                label: 'Documents Created',
                data: [<?php echo implode(',', $counts); ?>],
                borderColor: '#0d6efd',
                fill: false,
                tension: 0.3
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
});
</script>