<?php
session_start();

include('php/conexion.php'); 

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: index.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_comprobante']) && isset($_POST['nuevo_estado'])) {
    
    $id_comprobante = (int)$_POST['id_comprobante'];
    $nuevo_estado = $_POST['nuevo_estado'];
    

    if ($nuevo_estado !== 'verificado' && $nuevo_estado !== 'comprobado') {
        $msg = "Estado no permitido.";
        goto_error($msg);
    }

    $sql_user = "SELECT id_usuario FROM comprobantes WHERE id = ?";
    $stmt_user = $conexion->prepare($sql_user);
    $stmt_user->bind_param("i", $id_comprobante);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    $user_data = $result_user->fetch_assoc();
    $id_usuario = $user_data['id_usuario'];
    $stmt_user->close();

    $sql_update = "UPDATE comprobantes SET estado = ? WHERE id = ?";
    
    if ($stmt = $conexion->prepare($sql_update)) {
        
        $stmt->bind_param("si", $nuevo_estado, $id_comprobante);
        
        if ($stmt->execute()) {
            header("Location: ver_comprobantes.php?id_usuario=" . $id_usuario . "&status=success");
            exit();
        } else {

            goto_error("Error al actualizar la DB: " . $stmt->error);
        }

        $stmt->close();
    } else {

        goto_error("Error al preparar la consulta: " . $conexion->error);
    }
} else {

    header("Location: adminhome.php");
    exit();
}

$conexion->close();

function goto_error($msg) {

    header("Location: adminhome.php?status=error&msg=" . urlencode($msg));
    exit();
}
?>