<?php
define('DEVELOPER_PHONE', '0719957871');
define('DEVELOPER_EMAIL', 's.d.randiwela@gmail.com');
$host = "localhost";
$user = "root";
$password = "";
$dbname = "sdr_ims";

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function clean($value) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($value));
}

function h($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function money($value) {
    return "Rs. " . number_format((float)$value, 2);
}

function activePage($page) {
    $current = basename($_SERVER['PHP_SELF']);
    return $current === $page ? 'active' : '';
}

function require_admin() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header("Location: dashboard.php");
        exit();
    }
}

function upload_profile_picture($fieldName) {
    if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    $tmp = $_FILES[$fieldName]['tmp_name'];
    $mime = mime_content_type($tmp);

    if (!isset($allowed[$mime])) {
        return null;
    }

    $dir = __DIR__ . "/uploads/profiles";
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $name = "profile_" . time() . "_" . rand(1000,9999) . "." . $allowed[$mime];
    $path = $dir . "/" . $name;

    if (move_uploaded_file($tmp, $path)) {
        return "uploads/profiles/" . $name;
    }

    return null;
}


function valid_phone($phone) {
    return preg_match('/^[0-9]{0,10}$/', $phone) === 1;
}

function valid_password_rule($password) {
    // 5-8 characters, at least one letter, one number, and one symbol
    return preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z0-9]).{5,8}$/', $password) === 1;
}

function password_rule_message() {
    return "Password must be 5-8 characters and contain letters, numbers and symbols.";
}
