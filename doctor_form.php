<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
include "db.php";


$message = "";
$messageType = "";

if (isset($_POST['submit'])) {
    $name           = trim($_POST['name'] ?? '');
    $specialization = trim($_POST['specialization'] ?? '');
    $email          = trim($_POST['email'] ?? '');
    

    if ($name === "" || $specialization === "" || $email === "" ) {
        $message = "All fields are required.";
        $messageType = "danger";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $messageType = "danger";
    } else {
    

        $stmt = $conn->prepare("INSERT INTO doctors (name, specialization, email ) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $specialization, $email);

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Doctor - SHMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color:#f4f8fb;">

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <div class="col-md-9 col-lg-10 p-4">
            <div class="card shadow-sm border-0 mx-auto" style="max-width: 800px;">
                <div class="card-body p-4">
                    <h2 class="mb-1">Add New Doctor</h2>
                    <p class="text-muted">Smart Healthcare Management System</p>

                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $messageType; ?>">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Doctor Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Specialization</label>
                            <input type="text" class="form-control" name="specialization" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>

                        <!--<div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>--> 

                        <button type="submit" name="submit" class="btn btn-primary">Save Doctor</button>
                        <a href="doctor_view.php" class="btn btn-secondary">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>