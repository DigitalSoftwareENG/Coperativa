<?php
session_start();


if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../index.html");
    exit();
}

$id_del_usuario_actual = $_SESSION['id_usuario'];
include('../php/conexion.php');

$numero_referencia = $_POST['referencia'] ?? null;


function redirect_error($conexion, $msg) {
    mysqli_close($conexion);

    header("Location: ../home.php?status=comprobante_error&msg=" . urlencode($msg));
    exit();
}

function redirect_success() {
    header("Location: ../home.php?status=comprobante_success");
    exit();
}


if (!$numero_referencia) {
    redirect_error($conexion, 'Falta el número de referencia.');
}

$upload_dir_abs = realpath(__DIR__ . '/../uploads/');

if (!$upload_dir_abs || !is_dir($upload_dir_abs)) {
    redirect_error($conexion, 'Error de configuración del servidor (ruta uploads).');
}

$file_name = $id_del_usuario_actual . '_' . uniqid() . '_' . basename($_FILES["comprobante"]["name"]);
$target_file = $upload_dir_abs . '/' . $file_name;
$uploadOk = true;
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
$message = ''; 


if ($_FILES["comprobante"]["size"] > 5000000) {
    $uploadOk = false;
    $message = "El archivo es demasiado grande (máx. 5MB).";
}
if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "pdf") {
    $uploadOk = false;
    $message = "Solo se permiten archivos JPG, JPEG, PNG y PDF.";
}

if (!$uploadOk) {
    redirect_error($conexion, $message);
}


if (move_uploaded_file($_FILES["comprobante"]["tmp_name"], $target_file)) {
    $ruta_relativa_db = "uploads/" . $file_name;

    $sql = "INSERT INTO comprobantes (id_usuario, numero_referencia, ruta_archivo, estado) VALUES (?, ?, ?, 'pendiente')";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "iss", $id_del_usuario_actual, $numero_referencia, $ruta_relativa_db);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        mysqli_close($conexion);
        redirect_success();
    } else {

        mysqli_stmt_close($stmt);
        redirect_error($conexion, 'Error al guardar los datos del comprobante.');
    }
} else {

    redirect_error($conexion, 'Lo sentimos, hubo un error al subir tu archivo.');
}

mysqli_close($conexion);