<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
include "db.php";

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    $stmt = $conn->prepare("DELETE FROM insurance WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: insurance_view.php");
        exit();
    } else {
        echo "Failed to delete insurance record.";
    }

    $stmt->close();
} else {
    header("Location: insurance_view.php");
    exit();
}
?>