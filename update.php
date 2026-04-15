<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
include "db.php";


$message = "";
$messageType = "";

if (isset($_POST['update'])) {
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname  = trim($_POST['lastname'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = trim($_POST['password'] ?? '');
    $gender    = trim($_POST['gender'] ?? '');
    $user_id   = (int)($_POST['user_id'] ?? 0);

    if ($firstname === "" || $lastname === "" || $email === "" || $gender === "") {
        $message = "First name, last name, email, and gender are required.";
        $messageType = "danger";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $messageType = "danger";
    } else {
        if ($password !== "") {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET firstname=?, lastname=?, email=?, password=?, gender=? WHERE id=?");
            $stmt->bind_param("sssssi", $firstname, $lastname, $email, $hashed_password, $gender, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET firstname=?, lastname=?, email=?, gender=? WHERE id=?");
            $stmt->bind_param("ssssi", $firstname, $lastname, $email, $gender, $user_id);
        }

        if ($stmt->execute()) {
            header("Location: view.php");
            exit();
        } else {
            $message = "Error: " . $conn->error;
            $messageType = "danger";
        }

        $stmt->close();
    }
}

if (!isset($_GET['id']) && !isset($_POST['user_id'])) {
    header("Location: view.php");
    exit();
}

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : (int)$_POST['user_id'];

$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: view.php");
    exit();
}

$row = $result->fetch_assoc();
$first_name = $row['firstname'];
$lastname   = $row['lastname'];
$email      = $row['email'];
$gender     = $row['gender'];
$id         = $row['id'];

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Patient</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color:#f4f8fb;">

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <div class="col-md-9 col-lg-10 p-4">
            <div class="card shadow-sm border-0 mx-auto" style="max-width: 800px;">
                <div class="card-body p-4">
                    <h2 class="mb-1">Update Patient Record</h2>
                    <p class="text-muted">Smart Healthcare Management System</p>

                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $messageType; ?>">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <form action="" method="post">
                        <input type="hidden" name="user_id" value="<?php echo (int)$id; ?>">

                        <div class="mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" name="firstname" value="<?php echo htmlspecialchars($first_name); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" name="lastname" value="<?php echo htmlspecialchars($lastname); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control" name="password" placeholder="Leave blank if you do not want to change password">
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block">Gender</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" value="Male" <?php if ($gender === 'Male') echo 'checked'; ?> required>
                                <label class="form-check-label">Male</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" value="Female" <?php if ($gender === 'Female') echo 'checked'; ?> required>
                                <label class="form-check-label">Female</label>
                            </div>
                        </div>

                        <button type="submit" name="update" class="btn btn-primary">Update Patient</button>
                        <a href="view.php" class="btn btn-secondary">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>