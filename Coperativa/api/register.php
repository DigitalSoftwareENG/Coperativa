<?php

require '../php/conexion.php';

$usuario = $_POST['Usuario'];
$email = $_POST['Email'];
$telefono = $_POST['Telefono'];
$contrasena = $_POST['Contrasena'];
$direccion = $_POST['Direccion'];
$ci = $_POST['Cedula'];
$estado_inicial = 'pendiente'; 


if (empty($usuario) || empty($email) || empty($contrasena) || empty($ci)) {
    echo '<h1>Error: Faltan campos obligatorios.</h1>';
    exit();
}

$contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

$stmt = $conexion->prepare("INSERT INTO Usuarios (usuario, ci, direccion, email, contrasena, telefono, estado) VALUES (?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("sssssss", $usuario, $ci, $direccion, $email, $contrasena_hash, $telefono, $estado_inicial);

if ($stmt->execute()) {
    
    // Si el registro es exitoso, redirige al login
    header('Location: ../login.html');
    exit();
} else {
    
    echo '<h1>Error al registrar el usuario. Por favor, inténtelo de nuevo.</h1>';
}

$stmt->close();
// Asumo que la conexión se cierra al final del script si no hay más código.
?>