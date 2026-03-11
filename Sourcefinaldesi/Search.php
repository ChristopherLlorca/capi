<?php
include 'db.php';
include 'header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Document Search</h1>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Search by Student ID</h5>
                    <form method="GET" action="Search.php">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="searchStudentID" class="form-label">Student ID</label>
                                <input type="text" class="form-control" id="searchStudentID" name="student_id" placeholder="Enter Student ID / LRN " 
                                       value="<?php echo isset($_GET['student_id']) ? htmlspecialchars($_GET['student_id']) : ''; ?>">
                            </div>

                            <div class="col-md-4">
                                <label for="searchType" class="form-label">Document Type</label>
                                <select class="form-select" id="searchType" name="type">
                                    <option value="">All Types</option>
                                    <option value="Birth Certificate" <?php echo (isset($_GET['type']) && $_GET['type'] == 'Birth Certificate') ? 'selected' : ''; ?>>Birth Certificate</option>
                                    <option value="Form 137" <?php echo (isset($_GET['type']) && $_GET['type'] == 'Form 137') ? 'selected' : ''; ?>>Form 137</option>
                                    <option value="Report Card" <?php echo (isset($_GET['type']) && $_GET['type'] == 'Report Card') ? 'selected' : ''; ?>>Report Card</option>
                                </select>
                            </div>

                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Search Document</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <?php
                    // Logic to handle the specific Student ID search
                    if (isset($_GET['student_id']) || isset($_GET['type'])) {
                        $student_id = $conn->real_escape_string($_GET['student_id']);
                        $type = $conn->real_escape_string($_GET['type']);

                        // Build the SQL query
                        $sql = "SELECT * FROM documents WHERE 1=1";
                        
                        if (!empty($student_id)) {
                            // Focus search specifically on student_id column
                            $sql .= " AND student_id LIKE '%$student_id%'";
                        }
                        
                        if (!empty($type)) {
                            $sql .= " AND doc_type = '$type'";
                        }

                        $sql .= " ORDER BY date_created DESC";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            echo '<div class="table-responsive"><table class="table table-hover align-middle">';
                            echo '<thead class="table-light"><tr>
                                    <th>Tracking #</th>
                                    <th>Student Name</th>
                                    <th>Type</th>
                                    <th>From School</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                  </tr></thead><tbody>';

                            while ($row = $result->fetch_assoc()) {
                                $status_class = ($row['status'] == 'Pending') ? 'bg-warning text-dark' : (($row['status'] == 'Approved') ? 'bg-success' : 'bg-danger');
                                echo "<tr>
                                    <td>{$row['tracking_number']}</td>
                                    <td>{$row['student_name']}</td>
                                    <td>{$row['doc_type']}</td>
                                    <td>{$row['from_school']}</td>
                                    <td>" . date('M d, Y', strtotime($row['date_created'])) . "</td>
                                    <td><span class='badge $status_class'>{$row['status']}</span></td>
                                    <td>
                                        <a href='DocumentView.php?id={$row['id']}' class='btn btn-sm btn-outline-primary'>
                                            <i class='bi bi-eye'></i> View
                                        </a>
                                    </td>
                                </tr>";
                            }
                            echo '</tbody></table></div>';
                        } else {
                            echo "<div class='alert alert-warning'><i class='bi bi-exclamation-circle'></i> No documents found for Student ID: " . htmlspecialchars($student_id) . "</div>";
                        }
                    } else {
                        echo "<div class='alert alert-info'><i class='bi bi-info-circle'></i> Enter a Student ID above to find documents.</div>";
                    }
                    ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'footer.php'; ?>