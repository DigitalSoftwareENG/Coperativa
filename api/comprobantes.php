<?php

session_start();


if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../index.html");
    exit();
}


$id_del_usuario_actual = $_SESSION['id_usuario'];


include('../php/conexion.php');


$numero_referencia = $_POST['referencia'];



$upload_dir_abs = realpath(__DIR__ . '/../uploads/');
$file_name = $id_del_usuario_actual . '_' . uniqid() . '_' . basename($_FILES["comprobante"]["name"]);
$target_file = $upload_dir_abs . '/' . $file_name;
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));


if ($_FILES["comprobante"]["size"] > 5000000) {
    echo "Error: El archivo es demasiado grande.";
    $uploadOk = 0;
}
if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "pdf") {
    echo "Error: Solo se permiten archivos JPG, JPEG, PNG y PDF.";
    $uploadOk = 0;
}

if ($uploadOk == 1) {
    if (move_uploaded_file($_FILES["comprobante"]["tmp_name"], $target_file)) {

        $ruta_relativa_db = "uploads/" . $file_name;

        $sql = "INSERT INTO comprobantes (id_usuario, numero_referencia, ruta_archivo, estado) VALUES (?, ?, ?, 'pendiente')";
        $stmt = mysqli_prepare($conexion, $sql);

        mysqli_stmt_bind_param($stmt, "iss", $id_del_usuario_actual, $numero_referencia, $ruta_relativa_db);

        if (mysqli_stmt_execute($stmt)) {
            echo "El comprobante ha sido subido y guardado exitosamente.";
        } else {
            echo "Error al guardar los datos del comprobante: " . mysqli_error($conexion);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Lo sentimos, hubo un error al subir tu archivo.";
    }
} else {
    echo "No se subió el archivo.";
}

mysqli_close($conexion);
