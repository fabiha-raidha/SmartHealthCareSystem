<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
include "db.php";


$message = "";
$messageType = "";

if (!isset($_GET['id']) && !isset($_POST['appointment_id'])) {
    header("Location: appointment_view.php");
    exit();
}

$appointment_id = isset($_GET['id']) ? (int)$_GET['id'] : (int)$_POST['appointment_id'];

if (isset($_POST['update'])) {
    $patient_id = (int)($_POST['patient_id'] ?? 0);
    $doctor_id = (int)($_POST['doctor_id'] ?? 0);
    $appointment_date = trim($_POST['appointment_date'] ?? '');
    $appointment_time = trim($_POST['appointment_time'] ?? '');
    $status = trim($_POST['status'] ?? '');

    if ($patient_id <= 0 || $doctor_id <= 0 || $appointment_date === "" || $appointment_time === "" || $status === "") {
        $message = "All fields are required.";
        $messageType = "danger";
    } else {
        $stmt = $conn->prepare("UPDATE appointments SET patient_id=?, doctor_id=?, appointment_date=?, appointment_time=?, status=? WHERE id=?");
        $stmt->bind_param("iisssi", $patient_id, $doctor_id, $appointment_date, $appointment_time, $status, $appointment_id);

        if ($stmt->execute()) {
            header("Location: appointment_view.php");
            exit();
        } else {
            $message = "Error: " . $conn->error;
            $messageType = "danger";
        }

        $stmt->close();
    }
}

$patients = $conn->query("SELECT id, firstname, lastname FROM users ORDER BY firstname ASC");
$doctors = $conn->query("SELECT id, name FROM doctors ORDER BY name ASC");

$stmt = $conn->prepare("SELECT * FROM appointments WHERE id=?");
$stmt->bind_param("i", $appointment_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: appointment_view.php");
    exit();
}

$row = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Appointment - SHMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color:#f4f8fb;">

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <div class="col-md-9 col-lg-10 p-4">
            <div class="card shadow-sm border-0 mx-auto" style="max-width: 850px;">
                <div class="card-body p-4">
                    <h2 class="mb-1">Update Appointment</h2>
                    <p class="text-muted">Smart Healthcare Management System</p>

                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <input type="hidden" name="appointment_id" value="<?php echo (int)$row['id']; ?>">

                        <div class="mb-3">
                            <label class="form-label">Patient</label>
                            <select name="patient_id" class="form-select" required>
                                <?php while ($patient = $patients->fetch_assoc()): ?>
                                    <option value="<?php echo $patient['id']; ?>" <?php if ($row['patient_id'] == $patient['id']) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($patient['firstname'] . ' ' . $patient['lastname']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Doctor</label>
                            <select name="doctor_id" class="form-select" required>
                                <?php while ($doctor = $doctors->fetch_assoc()): ?>
                                    <option value="<?php echo $doctor['id']; ?>" <?php if ($row['doctor_id'] == $doctor['id']) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($doctor['name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Appointment Date</label>
                            <input type="date" name="appointment_date" class="form-control" value="<?php echo htmlspecialchars($row['appointment_date']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Appointment Time</label>
                            <input type="time" name="appointment_time" class="form-control" value="<?php echo htmlspecialchars($row['appointment_time']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="Pending" <?php if ($row['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                <option value="Confirmed" <?php if ($row['status'] == 'Confirmed') echo 'selected'; ?>>Confirmed</option>
                                <option value="Completed" <?php if ($row['status'] == 'Completed') echo 'selected'; ?>>Completed</option>
                            </select>
                        </div>

                        <button type="submit" name="update" class="btn btn-primary">Update Appointment</button>
                        <a href="appointment_view.php" class="btn btn-secondary">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>