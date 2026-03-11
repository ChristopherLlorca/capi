<?
include 'header.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Document Tracker | LHS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .hero-section {
            background: linear-gradient(135deg, #0d6efd 0%, #004085 100%);
            color: white;
            padding: 60px 0;
            margin-bottom: 30px;
        }

        .search-card {
            margin-top: -60px;
            border: none;
            border-radius: 15px;
        }

        .status-badge {
            font-size: 0.9rem;
            padding: 8px 12px;
        }

        .instruction-step {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .step-number {
            background: #0d6efd;
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 12px;
            flex-shrink: 0;
        }

        :root {
            --primary-color: #0056b3;
            --secondary-color: #e9ecef;
        }

        .navbar-brand img {
            height: 40px;
        }

        .sidebar {
            background-color: white;
            min-height: calc(100vh - 56px);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar .nav-link {
            color: #495057;
            border-radius: 5px;
            margin: 2px 0;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: var(--primary-color);
            color: white;
        }

        .results-container {
            max-height: 800px;
            /* Adjust this height as needed */
            overflow-y: auto;
            padding-right: 10px;
            /* Space for the scrollbar */
            scrollbar-width: thin;
            /* For Firefox */
        }

        /* Custom scrollbar for Chrome/Safari */
        .results-container::-webkit-scrollbar {
            width: 6px;
        }

        .results-container::-webkit-scrollbar-thumb {
            background-color: #dee2e6;
            border-radius: 10px;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="Dashboard.php">
                <img src="lhs-reglogo.png" alt="LHS Logo">
                <span class="ms-2">LHS - Document Tracking</span>
            </a>
        </div>
    </nav>

    <div class="hero-section text-center">
        <div class="container">
            <h1 class="display-5 fw-bold">Track Your Request</h1>
            <p class="lead">Enter your tracking number or student ID below to see your document status.</p>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-4 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title fw-bold mb-4"><i
                                class="bi bi-info-circle text-primary me-2"></i>Instructions</h5>

                        <div class="instruction-step">
                            <div class="step-number">1</div>
                            <p class="mb-0 text-muted">Locate your <strong>Tracking Number</strong> or use your
                                <strong>Student ID</strong>.</p>
                        </div>
                        <div class="instruction-step">
                            <div class="step-number">2</div>
                            <p class="mb-0 text-muted">Type the code into the search field below.</p>
                        </div>
                        <div class="instruction-step">
                            <div class="step-number">3</div>
                            <p class="mb-0 text-muted">Click <strong>"Track"</strong> to view all matching records.</p>
                        </div>

                        <hr class="my-4">

                        <h6 class="fw-bold mb-3">Status Guide:</h6>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-warning text-dark">Pending</span>
                            <span class="badge bg-success">Approved</span>
                            <span class="badge bg-info">Out Going</span>
                            <span class="badge bg-primary">Completed</span>
                            <span class="badge bg-danger">Rejected</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <form method="POST" action="">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-9">
                                    <label for="searchQuery" class="form-label fw-semibold">Tracking Number or Student
                                        ID</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                                        <input type="text" id="searchQuery" name="searchQuery"
                                            class="form-control form-control-lg" placeholder="Enter ID or Tracking No."
                                            value="<?php echo isset($_POST['searchQuery']) ? htmlspecialchars($_POST['searchQuery']) : ''; ?>"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-primary btn-lg w-100" type="submit">
                                        Track
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <?php
                include 'db.php';

                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['searchQuery'])) {
                    $query = trim($_POST['searchQuery']);

                    if (empty($query)) {
                        echo '<div class="alert alert-danger border-0 shadow-sm"><i class="bi bi-exclamation-triangle-fill me-2"></i>Please enter a search term.</div>';
                    } else {
                        // Updated SQL to search both tracking_number OR student_id
                        $stmt = $conn->prepare("SELECT * FROM documents WHERE tracking_number = ? OR student_id = ? ORDER BY date_created DESC");
                        $stmt->bind_param("ss", $query, $query);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            echo '<h5 class="mb-3 text-secondary">Found ' . $result->num_rows . ' record(s) for "' . htmlspecialchars($query) . '"</h5>';

                            // START: Added scrollable container wrapper
                            echo '<div class="results-container">';

                            // Loop through all results
                            while ($doc = $result->fetch_assoc()) {
                                ?>
                                <div class="card shadow-sm border-0 overflow-hidden mb-4">
                                    <div class="card-header bg-success text-white py-2">
                                        <small class="fw-bold text-uppercase">Tracking No:
                                            <?php echo htmlspecialchars($doc['tracking_number']); ?></small>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0 align-middle">
                                            <tbody>
                                                <tr>
                                                    <td class="ps-4 fw-bold text-muted" style="width: 30%;">Student</td>
                                                    <td>
                                                        <div class="fw-bold"><?php echo htmlspecialchars($doc['student_name']); ?></div>
                                                        <small class="text-muted">ID:
                                                            <?php echo htmlspecialchars($doc['student_id']); ?></small>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-4 fw-bold text-muted">Document Type</td>
                                                    <td><?php echo htmlspecialchars($doc['doc_type']); ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-4 fw-bold text-muted">Current Status</td>
                                                    <td>
                                                        <span class="badge status-badge" style="background-color: 
                                        <?php
                                        switch ($doc['status']) {
                                            case 'Completed':
                                                echo '#28a745';
                                                break;
                                            case 'Pending':
                                                echo '#ffc107';
                                                break;
                                            case 'Rejected':
                                                echo '#dc3545';
                                                break;
                                            case 'Approved':
                                                echo '#0d6efd';
                                                break;
                                            default:
                                                echo '#0DCAF0';
                                        }
                                        ?>;">
                                                            <?php echo htmlspecialchars($doc['status']); ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-4 fw-bold text-muted">Location</td>
                                                    <td><i
                                                            class="bi bi-geo-alt-fill text-danger me-1"></i><?php echo htmlspecialchars($doc['current_location']); ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-4 fw-bold text-muted">Date Requested</td>
                                                    <td>
                                                        <i class="bi bi-calendar3 me-1"></i>
                                                        <?php echo date('F j, Y', strtotime($doc['date_created'])); ?>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <?php
                            }
                            // END: Close scrollable container
                            echo '</div>';

                        } else {
                            echo '
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-search text-muted mb-3" style="font-size: 3rem;"></i>
                    <h5 class="text-muted">No records found</h5>
                    <p class="text-secondary">No document found for: <strong>' . htmlspecialchars($query) . '</strong></p>
                </div>
            </div>';
                        }
                        $stmt->close();
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <footer class="footer mt-auto py-3 bg-light">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <span class="text-muted">© 2025 Lagro High School - Document Tracking System</span>
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="text-muted">Version 1.0.0</span>
                </div>
            </div>
        </div>
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>