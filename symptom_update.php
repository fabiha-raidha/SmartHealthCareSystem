<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
include "db.php";

$message = "";
$messageType = "";

if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}

if (isset($_POST['update'])) {
    $id          = (int)($_POST['id'] ?? 0);
    $patient_id  = (int)($_POST['patient_id'] ?? 0);
    $symptom     = trim($_POST['symptom'] ?? '');
    $disease     = trim($_POST['disease'] ?? '');
    $doctor_note = trim($_POST['doctor_note'] ?? '');
    $record_date = trim($_POST['record_date'] ?? '');

    if ($patient_id <= 0 || $symptom === "" || $disease === "" || $record_date === "") {
        $message = "Please fill in all required fields.";
        $messageType = "danger";
    } else {
        $stmt = $conn->prepare("UPDATE symptom_disease SET patient_id=?, symptom=?, disease=?, doctor_note=?, record_date=? WHERE id=?");
        $stmt->bind_param("issssi", $patient_id, $symptom, $disease, $doctor_note, $record_date, $id);

        if ($stmt->execute()) {
            header("Location: symptom_view.php");
            exit();
        } else {
            $message = "Failed to update record.";
            $messageType = "danger";
        }

        $stmt->close();
    }
}

if (!isset($_GET['id']) && !isset($_POST['id'])) {
    header("Location: symptom_view.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : (int)($_POST['id'] ?? 0);

$stmt = $conn->prepare("SELECT * FROM symptom_disease WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: symptom_view.php");
    exit();
}

$row = $result->fetch_assoc();
$stmt->close();

$patients = $conn->query("SELECT id, firstname, lastname FROM users ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Symptom & Disease - SHMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color:#f4f8fb;">

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <div class="col-md-9 col-lg-10 p-4">
            <div class="card shadow-sm border-0 mx-auto" style="max-width: 900px;">
                <div class="card-body p-4">
                    <h2 class="mb-1">Update Symptom & Disease Record</h2>
                    <p class="text-muted">Smart Healthcare Management System</p>

                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $messageType; ?>">
                            <?php echo e($message); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <fieldset>
                            <legend class="fs-5 mb-3">Edit Symptom & Disease Information</legend>

                            <input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>">

                            <div class="mb-3">
                                <label class="form-label">Select Patient</label>
                                <select name="patient_id" class="form-select" required>
                                    <option value="">Select Patient</option>
                                    <?php while ($patient = $patients->fetch_assoc()): ?>
                                        <option value="<?php echo (int)$patient['id']; ?>" <?php echo ((int)$patient['id'] === (int)$row['patient_id']) ? 'selected' : ''; ?>>
                                            <?php echo e($patient['firstname'] . ' ' . $patient['lastname']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Symptom</label>
                                <input type="text" name="symptom" class="form-control" value="<?php echo e($row['symptom']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Disease</label>
                                <input type="text" name="disease" class="form-control" value="<?php echo e($row['disease']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Doctor Note</label>
                                <textarea name="doctor_note" class="form-control" rows="4"><?php echo e($row['doctor_note']); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Record Date</label>
                                <input type="date" name="record_date" class="form-control" value="<?php echo e($row['record_date']); ?>" required>
                            </div>

                            <button type="submit" name="update" class="btn btn-info text-white">Update Record</button>
                            <a href="symptom_view.php" class="btn btn-secondary">Back</a>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>

<?php $conn->close(); ?>