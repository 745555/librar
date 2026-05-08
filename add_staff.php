<?php
// add_staff.php
session_start();
require_once 'database.php';

// فقط admin يمكنه إضافة موظفين
if(!isset($_SESSION['staff_username']) || $_SESSION['staff_username'] != 'admin') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO library_staff (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $password]);
        header("Location: index.php?msg=added");
    } catch(PDOException $e) {
        header("Location: index.php?msg=error");
    }
} else {
    header("Location: index.php");
}
?>