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

if (isset($_POST['submit'])) {
    $patient_id       = (int)($_POST['patient_id'] ?? 0);
    $recommendation   = trim($_POST['recommendation'] ?? '');
    $preventive_care  = trim($_POST['preventive_care'] ?? '');
    $follow_up_advice = trim($_POST['follow_up_advice'] ?? '');
    $record_date      = trim($_POST['record_date'] ?? '');

    if ($patient_id <= 0 || $recommendation === "" || $preventive_care === "" || $record_date === "") {
        $message = "Please fill in all required fields.";
        $messageType = "danger";
    } else {
        $stmt = $conn->prepare("INSERT INTO recommendations (patient_id, recommendation, preventive_care, follow_up_advice, record_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $patient_id, $recommendation, $preventive_care, $follow_up_advice, $record_date);

        if ($stmt->execute()) {
            header("Location: recommendation_view.php");
            exit();
        } else {
            $message = "Failed to save recommendation record.";
            $messageType = "danger";
        }

        $stmt->close();
    }
}

$patients = $conn->query("SELECT id, firstname, lastname FROM users ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Recommendation - SHMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color:#f4f8fb;">
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <div class="col-md-9 col-lg-10 p-4">
            <div class="card shadow-sm border-0 mx-auto" style="max-width: 900px;">
                <div class="card-body p-4">
                    <h2 class="mb-1">Add Recommendation & Preventive Care</h2>
                    <p class="text-muted">Smart Healthcare Management System</p>

                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $messageType; ?>">
                            <?php echo e($message); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <fieldset>
                            <legend class="fs-5 mb-3">Recommendation Information</legend>

                            <div class="mb-3">
                                <label class="form-label">Select Patient</label>
                                <select name="patient_id" class="form-select" required>
                                    <option value="">Select Patient</option>
                                    <?php while ($patient = $patients->fetch_assoc()): ?>
                                        <option value="<?php echo (int)$patient['id']; ?>">
                                            <?php echo e($patient['firstname'] . ' ' . $patient['lastname']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Recommendation</label>
                                <textarea name="recommendation" class="form-control" rows="4" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Preventive Care</label>
                                <textarea name="preventive_care" class="form-control" rows="4" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Follow-up Advice</label>
                                <textarea name="follow_up_advice" class="form-control" rows="3"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Record Date</label>
                                <input type="date" name="record_date" class="form-control" required>
                            </div>

                            <button type="submit" name="submit" class="btn btn-primary">Save Recommendation</button>
                            <a href="recommendation_view.php" class="btn btn-secondary">Back</a>
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