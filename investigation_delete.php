<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
include "db.php";

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    $stmt = $conn->prepare("DELETE FROM investigations WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: investigation_view.php");
        exit();
    } else {
        echo "Failed to delete investigation record.";
    }

    $stmt->close();
} else {
    header("Location: investigation_view.php");
    exit();
}
?>