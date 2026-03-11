<?php
session_start();
include 'db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $now = new DateTime();

        if ($user['lockout_until'] && new DateTime($user['lockout_until']) > $now) {
            $diff = $now->diff(new DateTime($user['lockout_until']));
            $error = "LOCKED|" . $diff->format('%h hours, %i minutes');
        } else {
            if (password_verify($password, $user['password'])) {
                if ($user['status'] !== 'active') {
                    $error = "ACCOUNT_INACTIVE";
                } else {
                    $reset = $conn->prepare("UPDATE users SET login_attempts = 0, lockout_until = NULL WHERE id = ?");
                    $reset->bind_param("i", $user['id']);
                    $reset->execute();

                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    header("Location: Dashboard.php");
                    exit();
                }
            } else {
                $attempts = $user['login_attempts'] + 1;
                if ($attempts >= 3) {
                    $until = (new DateTime())->add(new DateInterval('PT12H'))->format('Y-m-d H:i:s');
                    $upd = $conn->prepare("UPDATE users SET login_attempts = ?, lockout_until = ? WHERE id = ?");
                    $upd->bind_param("isi", $attempts, $until, $user['id']);
                    $error = "TOO_MANY_ATTEMPTS";
                } else {
                    $upd = $conn->prepare("UPDATE users SET login_attempts = ? WHERE id = ?");
                    $upd->bind_param("ii", $attempts, $user['id']);
                    $remaining = 3 - $attempts;
                    $error = "Invalid password. trials left: $remaining";
                }
                $upd->execute();
            }
        }
    } else {
        $error = "Username not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LHS - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #0056b3;
            --secondary-color: #e9ecef;
        }

        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        input::-ms-reveal,
        input::-ms-clear {
            display: none !important;
        }

       .navbar-brand img { height: 40px; }
        .sidebar {
            background-color: white;
            min-height: calc(100vh - 56px);
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .sidebar .nav-link {
            color: #495057;
            border-radius: 5px;
            margin: 2px 0;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: var(--primary-color);
            color: white;
        }

        .login-container {
            position: relative;
            height: calc(100vh - 56px);
            width: 100%;
            display: flex;
        }

        .background-slideshow {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            z-index: 1;
            filter: brightness(0.5);
            transition: opacity 1.5s ease-in-out;
        }

        .left-content {
            flex: 1.5;
            z-index: 2;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding-left: 5%;
            color: white;
        }

        @media (max-width: 991px) {
            .left-content {
                display: none;
            }
        }

        .right-panel {
            flex: 1;
            z-index: 5;
            display: flex;
            justify-content: center;
            align-items: center;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-left: 1px solid rgba(255, 255, 255, 0.2);
        }

        .form-card {
            width: 100%;
            max-width: 380px;
            padding: 40px;
            color: white;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.9);
            border: none;
        }

        .btn-login {
            background-color: var(--primary-color);
            border: none;
            font-weight: 700;
            padding: 0.8rem;
            transition: transform 0.2s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            background-color: #004494;
        }

        .hidden {
            opacity: 0;
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
         <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link fw-bold text-white me-3" href="clientTrackingPage.php">
                        <i class="bi bi-geo-alt-fill"></i> Tracking Portal
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-bold text-white" href="client_register_doc.php">
                        <i class="bi bi-file-earmark-plus-fill"></i> Register Document
                    </a>
                </li>
            </ul>
    </div>
</nav>

    <div class="login-container">
        <div class="background-slideshow" id="bg1"></div>
        <div class="background-slideshow hidden" id="bg2"></div>

        <div class="left-content">
            <h1 class="display-4 fw-bold">Registrar's Office Portal</h1>
            <p class="fs-4 opacity-75">Your reliable gateway for academic records, <br>enrollment status, and official
                documents.</p>
        </div>

        <div class="right-panel">
            <div class="form-card">
                <div class="text-center mb-4">
                    <img src="lhs-reglogo.png" alt="Logo"
                        style="height: 100px; filter: drop-shadow(0px 4px 10px rgba(0,0,0,0.3));">
                    <h2 class="fw-bold mt-3 letter-spacing-2">LOGIN</h2>
                </div>
                <form method="POST" autocomplete="off">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-white">Username</label>
                        <input type="text" name="username" class="form-control form-control-lg"
                            placeholder="Enter username" required
                            value="<?= isset($username) ? htmlspecialchars($username) : '' ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-white">Password</label>
                        <input type="password" name="password" class="form-control form-control-lg"
                            placeholder="Enter password" required>
                    </div>
                    <div class="text-center mb-4">
                        <a href="forgot_password.php" class="text-decoration-none small text-white-50">Forgot
                            Password?</a>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 btn-login text-white">LOG IN</button>
                </form>
                <?php if (!empty($error)): ?>
                    <?php if (strpos($error, 'LOCKED|') !== false):
                        $time_left = explode('|', $error)[1]; ?>
                        <div class="alert alert-warning mt-4 text-center small"
                            style="background: rgba(255, 193, 7, 0.9); color: #000; border: none;">
                            <i class="bi bi-clock-fill"></i> Locked for <strong><?= $time_left ?></strong>. <br>
                            <a href="forgot_password.php?user=<?= urlencode($username) ?>" class="fw-bold text-dark">Notify
                                Admin to Unlock</a>
                        </div>
                    <?php elseif ($error !== "ACCOUNT_INACTIVE"): ?>
                        <div class="alert alert-danger mt-4 text-center py-2 small"
                            style="background: rgba(220, 53, 69, 0.8); color: white; border: none;">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <?php if ($error === "ACCOUNT_INACTIVE"): ?>
        <script>alert("ACCESS DENIED: Your account is currently INACTIVE.");</script>
    <?php elseif ($error === "TOO_MANY_ATTEMPTS"): ?>
        <script>alert("Security Alert: 3 failed trials. Account locked for 12 hours."); location.reload();</script>
    <?php endif; ?>

    <script>
        const images = ['lhs1.webp', 'lhs2.webp', 'lhs3.webp'];
        let currentIndex = 0;
        const bg1 = document.getElementById('bg1');
        const bg2 = document.getElementById('bg2');
        let activeBg = bg1;
        function updateBackground() {
            const nextBg = (activeBg === bg1) ? bg2 : bg1;
            nextBg.style.backgroundImage = `url(${images[currentIndex]})`;
            nextBg.classList.remove('hidden');
            activeBg.classList.add('hidden');
            activeBg = nextBg;
            currentIndex = (currentIndex + 1) % images.length;
        }
        bg1.style.backgroundImage = `url(${images[0]})`;
        currentIndex = 1;
        setInterval(updateBackground, 5000);
    </script>
</body>

</html>