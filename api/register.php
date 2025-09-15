<?php

require '../php/conexion.php';

$usuario = $_POST['Usuario'];
$email = $_POST['Email'];
$telefono = $_POST['Telefono'];
$contrasena = $_POST['Contrasena'];
$direccion = $_POST['Direccion'];
$ci = $_POST['Cedula'];


if (empty($usuario) || empty($email) || empty($contrasena) || empty($ci)) {
    echo '<h1>Error: Faltan campos obligatorios.</h1>';
    exit();
}

$contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

$stmt = $conexion->prepare("INSERT INTO Usuarios (usuario, ci, direccion, email, contrasena, telefono) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $usuario, $ci, $direccion, $email, $contrasena_hash, $telefono);

if ($stmt->execute()) {
   
    header('Location: ../login.html');
    exit();
} else {
    
    echo '<h1>Error al registrar el usuario. Por favor, inténtelo de nuevo.</h1>';
}

$stmt->close();
?>