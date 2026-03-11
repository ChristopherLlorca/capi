<?php
include 'db.php';
include 'header.php';

// Fetch user info for the display
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$userData = $stmt->get_result()->fetch_assoc();

// --- LOGIC FROM PROFILE PAGE ---
// Initials for fallback if no image exists
$name_parts = explode(' ', $userData['full_name']);
$initials = strtoupper(substr($name_parts[0], 0, 1) . (isset($name_parts[1]) ? substr($name_parts[1], 0, 1) : ''));

$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
$err = isset($_GET['err']) ? $_GET['err'] : '';
?>

<main class="px-md-4 py-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="bi bi-gear-fill me-2"></i>Account Settings</h1>
    </div>

    <?php if($msg): ?>
        <div class="alert alert-success border-0 shadow-sm"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>
    <?php if($err): ?>
        <div class="alert alert-danger border-0 shadow-sm"><?php echo htmlspecialchars($err); ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-4">
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="mx-auto mb-3" style="width: 80px; height: 80px; position: relative;">
                        <?php if (!empty($userData['profile_image']) && file_exists($userData['profile_image'])): ?>
                            <img src="<?php echo $userData['profile_image']; ?>" 
                                 class="rounded-circle border" 
                                 style="width: 80px; height: 80px; object-fit: cover;">
                        <?php else: ?>
                            <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white fw-bold" 
                                 style="width: 80px; height: 80px; font-size: 1.5rem;">
                                <?php echo $initials; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <h5 class="fw-bold mb-0"><?php echo htmlspecialchars($userData['full_name']); ?></h5>
                    <p class="text-muted small mb-3">@<?php echo htmlspecialchars($userData['username']); ?></p>
                    <span class="badge bg-primary rounded-pill px-3"><?php echo strtoupper($userData['role']); ?></span>
                    
                    <div class="mt-3">
                        <a href="Profile.php" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-camera me-1"></i> Change Photo
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border-0 bg-white">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <h5 class="mb-0 fw-bold">Update Password</h5>
                    <p class="text-muted small">Keep your account secure by changing your password regularly.</p>
                </div>
                <div class="card-body px-4 pb-4">
                    <form action="update_password.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Current Password</label>
                            <input type="password" name="current_password" class="form-control bg-light border-0" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">New Password</label>
                                <input type="password" name="new_password" class="form-control bg-light border-0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Confirm New Password</label>
                                <input type="password" name="confirm_password" class="form-control bg-light border-0" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="bi bi-shield-lock me-2"></i>Save Security Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>