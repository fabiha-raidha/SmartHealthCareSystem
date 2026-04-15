<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
include "db.php";


$message = "";
$messageType = "";

$patients = $conn->query("SELECT id, firstname, lastname FROM users ORDER BY firstname ASC");
$doctors = $conn->query("SELECT id, name FROM doctors ORDER BY name ASC");

if (isset($_POST['submit'])) {
    $patient_id = (int)($_POST['patient_id'] ?? 0);
    $doctor_id = (int)($_POST['doctor_id'] ?? 0);
    $medicine = trim($_POST['medicine'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    $prescription_date = trim($_POST['prescription_date'] ?? '');

    if ($patient_id <= 0 || $doctor_id <= 0 || $medicine === "" || $notes === "" || $prescription_date === "") {
        $message = "All fields are required.";
        $messageType = "danger";
    } else {
        $stmt = $conn->prepare("INSERT INTO prescriptions (patient_id, doctor_id, medicine, notes, prescription_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $patient_id, $doctor_id, $medicine, $notes, $prescription_date);

        if ($stmt->execute()) {
            header("Location: prescription_view.php");
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
    <title>Add Prescription - SHMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color:#f4f8fb;">

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <div class="col-md-9 col-lg-10 p-4">
            <div class="card shadow-sm border-0 mx-auto" style="max-width: 850px;">
                <div class="card-body p-4">
                    <h2 class="mb-1">Add New Prescription</h2>
                    <p class="text-muted">Smart Healthcare Management System</p>

                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Patient</label>
                            <select name="patient_id" class="form-select" required>
                                <option value="">Select Patient</option>
                                <?php while ($patient = $patients->fetch_assoc()): ?>
                                    <option value="<?php echo $patient['id']; ?>">
                                        <?php echo htmlspecialchars($patient['firstname'] . ' ' . $patient['lastname']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Doctor</label>
                            <select name="doctor_id" class="form-select" required>
                                <option value="">Select Doctor</option>
                                <?php while ($doctor = $doctors->fetch_assoc()): ?>
                                    <option value="<?php echo $doctor['id']; ?>">
                                        <?php echo htmlspecialchars($doctor['name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Medicine</label>
                            <textarea name="medicine" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes / Advice</label>
                            <textarea name="notes" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Prescription Date</label>
                            <input type="date" name="prescription_date" class="form-control" required>
                        </div>

                        <button type="submit" name="submit" class="btn btn-primary">Save Prescription</button>
                        <a href="prescription_view.php" class="btn btn-secondary">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>