<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
include "db.php";


$message = "";
$messageType = "";

if (!isset($_GET['id']) && !isset($_POST['prescription_id'])) {
    header("Location: prescription_view.php");
    exit();
}

$prescription_id = isset($_GET['id']) ? (int)$_GET['id'] : (int)$_POST['prescription_id'];

if (isset($_POST['update'])) {
    $patient_id = (int)($_POST['patient_id'] ?? 0);
    $doctor_id = (int)($_POST['doctor_id'] ?? 0);
    $medicine = trim($_POST['medicine'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    $prescription_date = trim($_POST['prescription_date'] ?? '');

    if ($patient_id <= 0 || $doctor_id <= 0 || $medicine === "" || $notes === "" || $prescription_date === "") {
        $message = "All fields are required.";
        $messageType = "danger";
    } else {
        $stmt = $conn->prepare("UPDATE prescriptions SET patient_id=?, doctor_id=?, medicine=?, notes=?, prescription_date=? WHERE id=?");
        $stmt->bind_param("iisssi", $patient_id, $doctor_id, $medicine, $notes, $prescription_date, $prescription_id);

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

$patients = $conn->query("SELECT id, firstname, lastname FROM users ORDER BY firstname ASC");
$doctors = $conn->query("SELECT id, name FROM doctors ORDER BY name ASC");

$stmt = $conn->prepare("SELECT * FROM prescriptions WHERE id=?");
$stmt->bind_param("i", $prescription_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: prescription_view.php");
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
    <title>Update Prescription - SHMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color:#f4f8fb;">

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <div class="col-md-9 col-lg-10 p-4">
            <div class="card shadow-sm border-0 mx-auto" style="max-width: 850px;">
                <div class="card-body p-4">
                    <h2 class="mb-1">Update Prescription</h2>
                    <p class="text-muted">Smart Healthcare Management System</p>

                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <input type="hidden" name="prescription_id" value="<?php echo (int)$row['id']; ?>">

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
                            <label class="form-label">Medicine</label>
                            <textarea name="medicine" class="form-control" rows="3" required><?php echo htmlspecialchars($row['medicine']); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes / Advice</label>
                            <textarea name="notes" class="form-control" rows="3" required><?php echo htmlspecialchars($row['notes']); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Prescription Date</label>
                            <input type="date" name="prescription_date" class="form-control" value="<?php echo htmlspecialchars($row['prescription_date']); ?>" required>
                        </div>

                        <button type="submit" name="update" class="btn btn-primary">Update Prescription</button>
                        <a href="prescription_view.php" class="btn btn-secondary">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>