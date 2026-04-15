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
    $doctor_id      = (int)($_POST['doctor_id'] ?? 0);
    $name           = trim($_POST['name'] ?? '');
    $specialization = trim($_POST['specialization'] ?? '');
    $email          = trim($_POST['email'] ?? '');
    $password       = trim($_POST['password'] ?? '');

    if ($name === "" || $specialization === "" || $email === "") {
        $message = "Name, specialization, and email are required.";
        $messageType = "danger";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $messageType = "danger";
    } else {
        if ($password !== "") {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE doctors SET name=?, specialization=?, email=?, password=? WHERE id=?");
            $stmt->bind_param("ssssi", $name, $specialization, $email, $hashed_password, $doctor_id);
        } else {
            $stmt = $conn->prepare("UPDATE doctors SET name=?, specialization=?, email=? WHERE id=?");
            $stmt->bind_param("sssi", $name, $specialization, $email, $doctor_id);
        }

        if ($stmt->execute()) {
            header("Location: doctor_view.php");
            exit();
        } else {
            $message = "Error: " . $conn->error;
            $messageType = "danger";
        }

        $stmt->close();
    }
}

if (!isset($_GET['id']) && !isset($_POST['doctor_id'])) {
    header("Location: doctor_view.php");
    exit();
}

$doctor_id = isset($_GET['id']) ? (int)$_GET['id'] : (int)$_POST['doctor_id'];

$stmt = $conn->prepare("SELECT * FROM doctors WHERE id=?");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: doctor_view.php");
    exit();
}

$row = $result->fetch_assoc();
$id             = $row['id'];
$name           = $row['name'];
$specialization = $row['specialization'];
$email          = $row['email'];

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Doctor - SHMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color:#f4f8fb;">

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <div class="col-md-9 col-lg-10 p-4">
            <div class="card shadow-sm border-0 mx-auto" style="max-width: 800px;">
                <div class="card-body p-4">
                    <h2 class="mb-1">Update Doctor Record</h2>
                    <p class="text-muted">Smart Healthcare Management System</p>

                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $messageType; ?>">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <input type="hidden" name="doctor_id" value="<?php echo (int)$id; ?>">

                        <div class="mb-3">
                            <label class="form-label">Doctor Name</label>
                            <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Specialization</label>
                            <input type="text" class="form-control" name="specialization" value="<?php echo htmlspecialchars($specialization); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control" name="password" placeholder="Leave blank if you do not want to change password">
                        </div>

                        <button type="submit" name="update" class="btn btn-primary">Update Doctor</button>
                        <a href="doctor_view.php" class="btn btn-secondary">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>