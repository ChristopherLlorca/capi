<?php
// Ensure database connection
include_once 'db.php';

// Query document counts by status for badges
$pendingCount   = $conn->query("SELECT COUNT(*) AS total FROM documents WHERE status = 'Pending'")->fetch_assoc()['total'];
$approvedCount  = $conn->query("SELECT COUNT(*) AS total FROM documents WHERE status = 'Approved'")->fetch_assoc()['total'];
$outgoingCount  = $conn->query("SELECT COUNT(*) AS total FROM documents WHERE status = 'Out Going'")->fetch_assoc()['total'];
$completedCount = $conn->query("SELECT COUNT(*) AS total FROM documents WHERE status = 'Completed'")->fetch_assoc()['total'];
$rejectedCount  = $conn->query("SELECT COUNT(*) AS total FROM documents WHERE status = 'Rejected'")->fetch_assoc()['total'];

// Query total student count for the Students badge
$studentCount   = $conn->query("SELECT COUNT(*) AS total FROM students")->fetch_assoc()['total'];
?>

<div class="col-md-3 col-lg-2 d-md-block sidebar collapse py-3 bg-white" id="sidebarMenu">
    <ul class="nav flex-column">

        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'Dashboard.php' ? 'active' : ''; ?>" href="Dashboard.php">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'Search.php' ? 'active' : ''; ?>" href="Search.php">
                <i class="bi bi-search"></i> Advanced Search
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'NewDocument.php' ? 'active' : ''; ?>" href="NewDocument.php">
                <i class="bi bi-file-earmark-plus"></i> New Document
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'incoming.php' ? 'active' : ''; ?>" href="incoming.php">
                <i class="bi bi-download"></i> Incoming
                <?php if ($pendingCount > 0): ?>
                    <span class="badge bg-warning text-dark float-end"><?php echo $pendingCount; ?></span>
                <?php endif; ?>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'Outgoing.php' ? 'active' : ''; ?>" href="Outgoing.php">
                <i class="bi bi-send"></i> Outgoing
                <?php if ($outgoingCount > 0): ?>
                    <span class="badge bg-info text-dark float-end"><?php echo $outgoingCount; ?></span>
                <?php endif; ?>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'Archive.php' ? 'active' : ''; ?>" href="Archive.php">
                <i class="bi bi-archive"></i> Archive
                <?php if ($approvedCount > 0): ?>
                    <span class="badge bg-success float-end"><?php echo $approvedCount; ?></span>
                <?php endif; ?>
            </a>
        </li>

        <li class="nav-item border-top mt-2 pt-2">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'Students.php' ? 'active' : ''; ?>" href="Students.php">
                <i class="bi bi-person-badge"></i> Students
                <?php if ($studentCount > 0): ?>
                    <span class="badge bg-primary float-end"><?php echo $studentCount; ?></span>
                <?php endif; ?>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link text-danger <?php echo basename($_SERVER['PHP_SELF']) == 'Rejected.php' ? 'active' : ''; ?>" href="Rejected.php">
                <i class="bi bi-x-circle"></i> Rejected
                <?php if ($rejectedCount > 0): ?>
                    <span class="badge bg-danger float-end"><?php echo $rejectedCount; ?></span>
                <?php endif; ?>
            </a>
        </li>

        <li class="nav-item mt-3">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'Reports.php' ? 'active' : ''; ?>" href="Reports.php">
                <i class="bi bi-bar-chart"></i> Reports
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'User.php' ? 'active' : ''; ?>" href="User.php">
                <i class="bi bi-people"></i> Users
            </a>
        </li>

        <li class="nav-item mt-4">
            <a class="nav-link text-muted" href="logout.php">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </li>
    </ul>
</div>

<style>
    .sidebar .nav-link {
        font-weight: 500;
        color: #333;
        padding: 10px 15px;
        border-radius: 5px;
        margin-bottom: 2px;
    }
    .sidebar .nav-link:hover {
        background-color: #f8f9fa;
        color: #0d6efd;
    }
    .sidebar .nav-link.active {
        background-color: #e7f1ff;
        color: #0d6efd;
    }
    .badge {
        font-size: 0.75rem;
        padding: 4px 8px;
    }
</style>