<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

// Agar logged in nahi hai AND admin nahi hai to dashboard redirect nahi hoga
// Admin "Add User" ke liye is page ko open kar sakta hai
$isAdminAdding = isLoggedIn() && isAdmin();

// Agar normal logged-in user (non-admin) hai to dashboard pe bhejo
if (isLoggedIn() && !isAdmin()) {
    header('Location: /dashboard/index.php');
    exit;
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';
    $role     = $_POST['role'] ?? 'member';

    if (!$name || !$email || !$password || !$confirm) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (!in_array($role, ['admin', 'member'])) {
        $error = 'Invalid role selected.';
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param('s', $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = 'An account with this email already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('ssss', $name, $email, $hash, $role);
            if ($stmt->execute()) {
                $success = $isAdminAdding
                    ? 'User "' . htmlspecialchars($name) . '" successfully created!'
                    : 'Account created! You can now sign in.';
                // Form reset karo success ke baad
                $_POST = [];
            } else {
                $error = 'Registration failed. Please try again.';
            }
            $stmt->close();
        }
        $check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isAdminAdding ? 'Add User' : 'Register' ?> — TaskNest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>

<?php if ($isAdminAdding): ?>
<!-- Admin view: sidebar layout ke saath -->
<body>
<div class="app-layout">
    <?php include __DIR__ . '/../includes/navbar.php'; ?>
    <div class="main-content">
        <div class="topbar">
            <div class="topbar-title">Add New User</div>
            <a href="/dashboard/index.php" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
            </a>
        </div>
        <div class="page-body">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="content-card">
                        <div class="card-header">
                            <span><i class="bi bi-person-plus me-2" style="color:var(--teal-400)"></i>Create New User</span>
                        </div>
                        <div class="card-body">

                            <?php if ($error): ?>
                                <div class="alert alert-danger d-flex align-items-center gap-2 mb-3">
                                    <i class="bi bi-exclamation-circle-fill"></i> <?= htmlspecialchars($error) ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($success): ?>
                                <div class="alert alert-success d-flex align-items-center gap-2 mb-3">
                                    <i class="bi bi-check-circle-fill"></i> <?= $success ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST" novalidate>
                                <div class="mb-3">
                                    <label class="form-label">Full Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input type="text" name="name" class="form-control" placeholder="John Doe"
                                               value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                        <input type="email" name="email" class="form-control" placeholder="user@example.com"
                                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Role</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                                        <select name="role" class="form-select">
                                            <option value="member" <?= (($_POST['role'] ?? 'member') === 'member') ? 'selected' : '' ?>>Member</option>
                                            <option value="admin"  <?= (($_POST['role'] ?? '') === 'admin')  ? 'selected' : '' ?>>Admin</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                        <input type="password" name="password" class="form-control" placeholder="Min. 6 characters" required>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Confirm Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                        <input type="password" name="confirm_password" class="form-control" placeholder="Repeat password" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 py-2">
                                    <i class="bi bi-person-plus me-1"></i> Create User
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php else: ?>
<!-- Guest view: auth page layout -->
<body class="auth-page">
<div class="auth-wrapper">
    <div class="auth-card card p-4">
        <div class="text-center mb-4">
            <div class="brand-icon mb-3">
                <i class="bi bi-hexagon-fill"></i>
            </div>
            <h2 class="fw-bold">Create Account</h2>
            <p class="text-muted">Join Task<span style="color:var(--teal-400)">Nest</span> today</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-circle-fill"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($success) ?>
                <a href="/auth/login.php" class="ms-auto fw-semibold" style="color:var(--teal-400)">Sign In →</a>
            </div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="mb-3">
                <label class="form-label fw-semibold">Full Name</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="name" class="form-control" placeholder="John Doe"
                           value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Email address</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="you@example.com"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Min. 6 characters" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Confirm Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" name="confirm_password" class="form-control" placeholder="Repeat password" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                <i class="bi bi-person-plus me-1"></i> Create Account
            </button>
        </form>

        <hr class="my-4">
        <p class="text-center text-muted mb-0">
            Already have an account?
            <a href="/auth/login.php" class="fw-semibold" style="color:var(--teal-400)!important">Sign in</a>
        </p>
    </div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
