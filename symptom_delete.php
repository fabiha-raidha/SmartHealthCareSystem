<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
include "db.php";

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    $stmt = $conn->prepare("DELETE FROM symptom_disease WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: symptom_view.php");
        exit();
    } else {
        echo "Failed to delete symptom and disease record.";
    }

    $stmt->close();
} else {
    header("Location: symptom_view.php");
    exit();
}
?>