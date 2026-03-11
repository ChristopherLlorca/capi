

<?php

// filename: index.php

// Define data file and upload directory
$dataFile = 'students.json';
$uploadDir = 'uploads/';

// Ensure upload directory exists
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Load existing student data
$students = [];
if (file_exists($dataFile)) {
    $json = file_get_contents($dataFile);
    $students = json_decode($json, true) ?? [];
}

// Handle form submission for adding new student
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $name = trim($_POST['name']);
    $grade = trim($_POST['grade']);
    $id = trim($_POST['id']);

    // Handle file uploads
    $sf10Path = '';
    $moralPath = '';
    $reportPath = '';

    // SF10 PDF
    if (isset($_FILES['sf10File']) && $_FILES['sf10File']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['sf10File']['tmp_name'];
        $fileName = basename($_FILES['sf10File']['name']);
        $sf10Path = $uploadDir . uniqid() . '_' . $fileName;
        move_uploaded_file($fileTmp, $sf10Path);
    }

    // Good Moral Image
    if (isset($_FILES['moralFile']) && $_FILES['moralFile']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['moralFile']['tmp_name'];
        $fileName = basename($_FILES['moralFile']['name']);
        $moralPath = $uploadDir . uniqid() . '_' . $fileName;
        move_uploaded_file($fileTmp, $moralPath);
    }

    // Report Card PDF
    if (isset($_FILES['reportFile']) && $_FILES['reportFile']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['reportFile']['tmp_name'];
        $fileName = basename($_FILES['reportFile']['name']);
        $reportPath = $uploadDir . uniqid() . '_' . $fileName;
        move_uploaded_file($fileTmp, $reportPath);
    }

    // Create new student record
    $newStudent = [
        'name' => $name,
        'grade' => $grade,
        'id' => $id,
        'sf10' => $sf10Path,
        'moral' => $moralPath,
        'report' => $reportPath
    ];

    // Add to students array
    $students[] = $newStudent;

    // Save to JSON file
    file_put_contents($dataFile, json_encode($students, JSON_PRETTY_PRINT));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Student Records Management</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<style>
    body { background-color: #f8f9fa; }
    .record-card { margin: 10px; }
</style>
</head>
<body>
<div class="container mt-5">
<h1 class="text-center mb-4">Student Records Management</h1>

<!-- Search Bar -->
<input type="text" id="search" class="form-control mb-3" placeholder="Search students...">

<!-- Add New Student Button -->
<button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">Add New Student</button>

<!-- Student List -->
<div id="studentList" class="row">
<?php foreach ($students as $student): ?>
<div class="col-md-4 record-card">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars(strtoupper($student['name'])); ?></h5>
            <p class="card-text">Grade: <?php echo htmlspecialchars($student['grade']); ?> | ID: <?php echo htmlspecialchars($student['id']); ?></p>
            <?php if ($student['sf10']): ?>
                <a href="<?php echo htmlspecialchars($student['sf10']); ?>" class="btn btn-secondary" target="_blank">View SF10</a>
            <?php endif; ?>
            <?php if ($student['moral']): ?>
                <a href="<?php echo htmlspecialchars($student['moral']); ?>" class="btn btn-secondary" target="_blank">View Good Moral</a>
            <?php endif; ?>
            <?php if ($student['report']): ?>
                <a href="<?php echo htmlspecialchars($student['report']); ?>" class="btn btn-secondary" target="_blank">View Report Card</a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>
</div>

<!-- Add Student Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title">Add New Student</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<form id="addForm" method="POST" enctype="multipart/form-data">
<input type="text" name="name" class="form-control mb-2" placeholder="Name" required>
<input type="text" name="grade" class="form-control mb-2" placeholder="Grade" required>
<input type="text" name="id" class="form-control mb-2" placeholder="ID" required>
<label class="form-label">SF10 PDF</label>
<input type="file" name="sf10File" class="form-control mb-2" accept=".pdf">
<label class="form-label">Good Moral Image</label>
<input type="file" name="moralFile" class="form-control mb-2" accept="image/*">
<label class="form-label">Report Card PDF</label>
<input type="file" name="reportFile" class="form-control mb-2" accept=".pdf">
<button type="submit" class="btn btn-primary">Add</button>
</form>
</div>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Search functionality
document.getElementById('search').addEventListener('input', function() {
    const query = this.value.toLowerCase();
    const cards = document.querySelectorAll('.record-card');
    cards.forEach(card => {
        const name = card.querySelector('.card-title').textContent.toLowerCase();
        card.style.display = name.includes(query) ? '' : 'none';
    });
});
</script>
</body>
</html>