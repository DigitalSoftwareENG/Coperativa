<?php

session_start();

require_once '../php/conexion.php';

$adminName = $_POST['Administrador'];
$adminPass = $_POST['Contrasena'];

$sql = "SELECT id, contrasena FROM administradores WHERE nombre = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $adminName);
$stmt->execute();

$stmt->bind_result($adminId, $hashedPassword);
$stmt->fetch();
$stmt->close();

if ($hashedPassword && password_verify($adminPass, $hashedPassword)) {

    $_SESSION['usuario_id'] = $adminId;
    $_SESSION['rol'] = 'admin';
    
    header("Location: ../adminHome.php");
    exit();
} else {
    
    header("Location: ../admin.html?error=1");
    exit();
}

$conexion->close();
?>
