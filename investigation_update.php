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
    $id          = (int)($_POST['id'] ?? 0);
    $patient_id  = (int)($_POST['patient_id'] ?? 0);
    $subject     = trim($_POST['subject'] ?? '');
    $report      = trim($_POST['report'] ?? '');
    $result      = trim($_POST['result'] ?? '');
    $report_date = trim($_POST['report_date'] ?? '');

    if ($patient_id <= 0 || $subject === "" || $report === "" || $result === "" || $report_date === "") {
        $message = "Please fill in all required fields.";
        $messageType = "danger";
    } else {
        $stmt = $conn->prepare("UPDATE investigations SET patient_id=?, subject=?, report=?, result=?, report_date=? WHERE id=?");
        $stmt->bind_param("issssi", $patient_id, $subject, $report, $result, $report_date, $id);

        if ($stmt->execute()) {
            header("Location: investigation_view.php");
            exit();
        } else {
            $message = "Failed to update investigation record.";
            $messageType = "danger";
        }

        $stmt->close();
    }
}

if (!isset($_GET['id']) && !isset($_POST['id'])) {
    header("Location: investigation_view.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : (int)$_POST['id'];

$stmt = $conn->prepare("SELECT * FROM investigations WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultData = $stmt->get_result();

if ($resultData->num_rows !== 1) {
    header("Location: investigation_view.php");
    exit();
}

$row = $resultData->fetch_assoc();
$stmt->close();

$patients = $conn->query("SELECT id, firstname, lastname FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Investigation - SHMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color:#f4f8fb;">

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <div class="col-md-9 col-lg-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Update Investigation Record</h2>
                    <p class="text-muted mb-0">Smart Healthcare Management System</p>
                </div>
                <!--<a class="btn btn-secondary" href="investigation_view.php">Back</a>-->
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h4 class="mb-3">Edit Investigation Information</h4>

                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $messageType; ?>">
                            <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>">

                        <div class="mb-3">
                            <label class="form-label">Select Patient</label>
                            <select name="patient_id" class="form-select" required>
                                <option value="">Select Patient</option>
                                <?php while ($p = $patients->fetch_assoc()): ?>
                                    <option value="<?php echo (int)$p['id']; ?>" <?php if ($p['id'] == $row['patient_id']) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($p['firstname'] . ' ' . $p['lastname'], ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Subject</label>
                            <input type="text" name="subject" class="form-control" value="<?php echo htmlspecialchars($row['subject'], ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Report</label>
                            <textarea name="report" class="form-control" rows="4" required><?php echo htmlspecialchars($row['report'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Result</label>
                            <select name="result" class="form-select" required>
                                <option value="Open" <?php if ($row['result'] == 'Open') echo 'selected'; ?>>Open</option>
                                <option value="Under Review" <?php if ($row['result'] == 'Under Review') echo 'selected'; ?>>Under Review</option>
                                <option value="Resolved" <?php if ($row['result'] == 'Resolved') echo 'selected'; ?>>Resolved</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Report Date</label>
                            <input type="date" name="report_date" class="form-control" value="<?php echo htmlspecialchars($row['report_date'], ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>

                        <button type="submit" name="update" class="btn btn-info text-white">Update Investigation</button>
                        <a href="investigation_view.php" class="btn btn-secondary">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>