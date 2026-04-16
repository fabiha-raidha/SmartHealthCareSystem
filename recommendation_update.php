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
    $id               = (int)($_POST['id'] ?? 0);
    $patient_id       = (int)($_POST['patient_id'] ?? 0);
    $recommendation   = trim($_POST['recommendation'] ?? '');
    $preventive_care  = trim($_POST['preventive_care'] ?? '');
    $follow_up_advice = trim($_POST['follow_up_advice'] ?? '');
    $record_date      = trim($_POST['record_date'] ?? '');

    if ($patient_id <= 0 || $recommendation === "" || $preventive_care === "" || $record_date === "") {
        $message = "Please fill in all required fields.";
        $messageType = "danger";
    } else {
        $stmt = $conn->prepare("UPDATE recommendations SET patient_id=?, recommendation=?, preventive_care=?, follow_up_advice=?, record_date=? WHERE id=?");
        $stmt->bind_param("issssi", $patient_id, $recommendation, $preventive_care, $follow_up_advice, $record_date, $id);

        if ($stmt->execute()) {
            header("Location: recommendation_view.php");
            exit();
        } else {
            $message = "Failed to update recommendation record.";
            $messageType = "danger";
        }

        $stmt->close();
    }
}

if (!isset($_GET['id']) && !isset($_POST['id'])) {
    header("Location: recommendation_view.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : (int)($_POST['id'] ?? 0);

$stmt = $conn->prepare("SELECT * FROM recommendations WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: recommendation_view.php");
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
    <title>Update Recommendation - SHMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color:#f4f8fb;">
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <div class="col-md-9 col-lg-10 p-4">
            <div class="card shadow-sm border-0 mx-auto" style="max-width: 900px;">
                <div class="card-body p-4">
                    <h2 class="mb-1">Update Recommendation & Preventive Care</h2>
                    <p class="text-muted">Smart Healthcare Management System</p>

                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $messageType; ?>">
                            <?php echo e($message); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <fieldset>
                            <legend class="fs-5 mb-3">Edit Recommendation Information</legend>

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
                                <label class="form-label">Recommendation</label>
                                <textarea name="recommendation" class="form-control" rows="4" required><?php echo e($row['recommendation']); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Preventive Care</label>
                                <textarea name="preventive_care" class="form-control" rows="4" required><?php echo e($row['preventive_care']); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Follow-up Advice</label>
                                <textarea name="follow_up_advice" class="form-control" rows="3"><?php echo e($row['follow_up_advice']); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Record Date</label>
                                <input type="date" name="record_date" class="form-control" value="<?php echo e($row['record_date']); ?>" required>
                            </div>

                            <button type="submit" name="update" class="btn btn-info text-white">Update Recommendation</button>
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