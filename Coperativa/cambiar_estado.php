<?php
session_start();
include('php/conexion.php'); 


if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: index.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_usuario']) && $_POST['accion'] === 'aprobar') {
    
    $id_usuario = (int)$_POST['id_usuario'];
    $nuevo_estado = 'socio';

    $sql_update = "UPDATE usuarios SET estado = ?, rol = 'socio' WHERE id_usuario = ? AND estado = 'pendiente'";
    
    if ($stmt = $conexion->prepare($sql_update)) {
        
        $stmt->bind_param("si", $nuevo_estado, $id_usuario); 
        
        if ($stmt->execute()) {
            header("Location: adminhome.php?status=success");
            exit();
        } else {
            header("Location: adminhome.php?status=error&msg=Error%20al%20ejecutar%20actualizacion");
            exit();
        }

        $stmt->close();
    } else {
        header("Location: adminhome.php?status=error&msg=Error%20al%20preparar%20consulta");
        exit();
    }

} 

else {
    header("Location: adminhome.php");
    exit();
}

$conexion->close();
?>