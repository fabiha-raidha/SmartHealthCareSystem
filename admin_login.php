<?php
session_start();
include "db.php";

$message = "";
$messageType = "";

if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

if (isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === "" || $password === "") {
        $message = "Email and password are required.";
        $messageType = "danger";
    } else {
        $stmt = $conn->prepare("SELECT * FROM admins WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();

            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['full_name'];
                $_SESSION['admin_email'] = $admin['email'];

                header("Location: dashboard.php");
                exit();
            } else {
                $message = "Invalid password.";
                $messageType = "danger";
            }
        } else {
            $message = "Admin account not found.";
            $messageType = "danger";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background: linear-gradient(135deg, #dff6ff, #f4f8fb); min-height: 100vh;">

<div class="container py-5">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-7 col-lg-5">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-4">
                    <h2 class="mb-1">Admin Login</h2>
                    <p class="text-muted">Smart Healthcare Management System</p>

                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $messageType; ?>">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                    </form>

                    <p class="mt-3 mb-0 text-center">
                        Don't have an account? <a href="admin_register.php">Create one</a>
                    </p>
                    <p class="mt-2 mb-0 text-center">
                        <a href="index.php">Back to Home</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>