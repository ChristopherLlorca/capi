<?php
include 'db.php';
include 'header.php'; 

$user_id = $_SESSION['user_id'];

// --- 1. HANDLE IMAGE UPLOAD ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_image'])) {
    $target_dir = "profile_pics/";
    
    // Create folder if it doesn't exist
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_extension = strtolower(pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION));
    $new_filename = "user_" . $user_id . "_" . time() . "." . $file_extension;
    $target_file = $target_dir . $new_filename;

    // Validate if it's an image
    $check = getimagesize($_FILES["profile_image"]["tmp_name"]);
    if($check !== false) {
        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            // Update database with new image path
            $update_stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
            $update_stmt->bind_param("si", $target_file, $user_id);
            $update_stmt->execute();
            $msg = "success";
        }
    }
}

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

// Initials for fallback
$name_parts = explode(' ', $userData['full_name']);
$initials = strtoupper(substr($name_parts[0], 0, 1) . (isset($name_parts[1]) ? substr($name_parts[1], 0, 1) : ''));
?>

<main class="px-md-4 py-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">User Profile</h1>
    </div>

    <?php if(isset($msg)): ?>
        <div class="alert alert-success">Profile picture updated successfully!</div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-4">
            <div class="card mb-4 text-center shadow-sm">
                <div class="card-body">
                    <div class="position-relative mx-auto mb-3" style="width: 120px;">
                        <?php if (!empty($userData['profile_image']) && file_exists($userData['profile_image'])): ?>
                            <img src="<?php echo $userData['profile_image']; ?>" class="rounded-circle img-thumbnail" style="width: 120px; height: 120px; object-fit: cover;">
                        <?php else: ?>
                            <div class="user-avatar rounded-circle d-flex align-items-center justify-content-center bg-primary text-white fs-1 mx-auto" style="width: 120px; height: 120px;">
                                <?php echo $initials; ?>
                            </div>
                        <?php endif; ?>
                        
                        <button type="button" class="btn btn-sm btn-dark position-absolute bottom-0 end-0 rounded-circle" onclick="document.getElementById('fileInput').click()">
                            <i class="bi bi-camera"></i>
                        </button>
                    </div>

                    <form id="uploadForm" method="POST" enctype="multipart/form-data" class="d-none">
                        <input type="file" id="fileInput" name="profile_image" accept="image/*" onchange="document.getElementById('uploadForm').submit()">
                    </form>

                    <h4 class="mb-0"><?php echo htmlspecialchars($userData['full_name']); ?></h4>
                    <p class="text-muted"><?php echo ucfirst($userData['role']); ?></p>
                    
                    <div class="text-start mt-3">
                        <p class="small mb-1"><strong>Username:</strong> <?php echo htmlspecialchars($userData['username']); ?></p>
                        <p class="small mb-1"><strong>Status:</strong> <span class="badge bg-success"><?php echo ucfirst($userData['status']); ?></span></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Personal & Professional Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Full Name</div>
                        <div class="col-sm-8 fw-bold"><?php echo htmlspecialchars($userData['full_name']); ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Role</div>
                        <div class="col-sm-8"><?php echo ucfirst($userData['role']); ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Member Since</div>
                        <div class="col-sm-8"><?php echo date('F j, Y', strtotime($userData['created_at'])); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
    .user-avatar { font-weight: bold; }
    .btn-dark i { font-size: 0.8rem; }
    .img-thumbnail { border: 2px solid #0d6efd; }
</style>

<?php include 'footer.php'; ?>