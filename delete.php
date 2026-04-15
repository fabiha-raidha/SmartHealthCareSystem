<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
include "db.php";


if (isset($_GET['id'])) {
    $user_id = (int)$_GET['id'];

    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        header("Location: view.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
} else {
    header("Location: view.php");
    exit();
}
?>