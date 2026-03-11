<?php
include 'db.php';
include 'header.php'; // header.php contains session_start()

// RESTRICTION: If the role is NOT admin, kick them out
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>
            alert('ACCESS DENIED: Only Administrators can access the User Management page.');
            window.location.href='Dashboard.php';
          </script>";
    exit();
}
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">User Management</h1>
                <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#newUserModal">
                    <i class="bi bi-person-plus-fill"></i> Add New User
                </button>
            </div>

            <?php if(isset($_GET['msg'])): ?>
                <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="bi i-info-circle-fill me-2"></i> <?php echo htmlspecialchars($_GET['msg']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php
            // Check for pending requests
            $req_query = $conn->query("SELECT * FROM password_requests WHERE status = 'pending' ORDER BY request_date DESC");
            if ($req_query && $req_query->num_rows > 0): 
            ?>
            <div class="card border-0 shadow-sm mb-4" style="border-left: 5px solid #ffc107 !important;">
                <div class="card-body">
                    <h6 class="fw-bold text-warning"><i class="bi bi-exclamation-triangle-fill"></i> Password Reset Requests</h6>
                    <div class="list-group list-group-flush">
                        <?php while($req = $req_query->fetch_assoc()): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0 px-0">
                                <div>
                                    <span class="fw-bold">@<?php echo htmlspecialchars($req['username']); ?></span> 
                                    <small class="text-muted ms-2"><?php echo date('M d, h:i A', strtotime($req['request_date'])); ?></small>
                                </div>
                                <a href="reset_to_default.php?id=<?php echo $req['id']; ?>&user=<?php echo $req['username']; ?>" class="btn btn-sm btn-warning fw-bold">
                                    Reset to Default
                                </a>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-primary">System Accounts</h5>
                    <form method="GET" class="input-group" style="width: 300px;">
                        <input type="text" name="search" class="form-control bg-light border-0" placeholder="Search name..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                    </form>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">ID</th>
                                    <th>User Info</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
                                $sql = "SELECT * FROM users WHERE full_name LIKE '%$search%' OR username LIKE '%$search%' ORDER BY id DESC";
                                $result = $conn->query($sql);

                                // Buffer for modals to be printed after the table
                                $editModals = "";

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $statusClass = ($row['status'] == 'active') ? 'bg-success' : 'bg-danger';
                                        $toggleText = ($row['status'] == 'active') ? 'Deactivate' : 'Activate';
                                        ?>
                                        <tr>
                                            <td class="ps-4 text-muted"><?php echo $row['id']; ?></td>
                                            <td>
                                                <div class="fw-bold"><?php echo htmlspecialchars($row['full_name']); ?></div>
                                                <small class="text-muted">@<?php echo htmlspecialchars($row['username']); ?></small>
                                            </td>
                                            <td><span class="badge rounded-pill bg-primary px-3"><?php echo strtoupper($row['role']); ?></span></td>
                                            <td><span class="badge <?php echo $statusClass; ?>"><?php echo strtoupper($row['status']); ?></span></td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-light border" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['id']; ?>" title="Edit">
                                                    <i class="bi bi-pencil-square text-primary"></i>
                                                </button>
                                                
                                                <a href="deactivate_user.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-light border" title="<?php echo $toggleText; ?>">
                                                    <i class="bi bi-power <?php echo ($row['status'] == 'active') ? 'text-warning' : 'text-success'; ?>"></i>
                                                </a>

                                                <a href="delete_user.php?id=<?php echo $row['id']; ?>" 
                                                   class="btn btn-sm btn-light border" 
                                                   onclick="return confirm('PERMANENT ACTION: Delete this user?');" title="Delete">
                                                    <i class="bi bi-trash text-danger"></i>
                                                </a>
                                            </td>
                                        </tr>

                                        <?php
                                        // Store Modal HTML in a variable
                                        ob_start(); ?>
                                        <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <form action="edit_user.php" method="POST" class="modal-content border-0 shadow">
                                                    <div class="modal-header bg-white border-bottom-0 pt-4 px-4">
                                                        <h5 class="modal-title fw-bold">Edit User Details</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body bg-white px-4">
                                                        <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-bold text-muted">Full Name</label>
                                                            <input type="text" name="full_name" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($row['full_name']); ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-bold text-muted">Username</label>
                                                            <input type="text" name="username" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($row['username']); ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-bold text-muted">Role</label>
                                                            <select name="role" class="form-select bg-light border-0">
                                                                <option value="staff" <?php if($row['role'] == 'staff') echo 'selected'; ?>>Staff</option>
                                                                <option value="admin" <?php if($row['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer bg-white border-top-0 pb-4 px-4">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <?php
                                        $editModals .= ob_get_clean();
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='text-center py-5 text-muted'>No accounts found.</td></tr>";
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

<?php echo $editModals; ?>

<div class="modal fade" id="newUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="add_user.php" class="modal-content border-0 shadow">
            <div class="modal-header bg-white border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Create New Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body bg-white px-4">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Full Name</label>
                    <input type="text" name="full_name" class="form-control bg-light border-0" placeholder="Full Name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Username</label>
                    <input type="text" name="username" class="form-control bg-light border-0" placeholder="Username" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Password</label>
                    <input type="password" name="password" class="form-control bg-light border-0" placeholder="Password" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Account Role</label>
                    <select name="role" class="form-select bg-light border-0" required>
                        <option value="staff">Staff (Standard)</option>
                        <option value="admin">Admin (Full Control)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer bg-white border-top-0 pb-4 px-4">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary px-4">Create Account</button>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>