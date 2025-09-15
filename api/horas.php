<?php

session_start();


if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../index.html");
    exit();
}

$id_del_usuario_actual = $_SESSION['id_usuario'];


include('../php/conexion.php'); 


$inicio_horario = $_POST['RegHoraInicio'];
$final_horario = $_POST['RegHoraFinal'];
$fecha = $_POST['RegFecha'];


$sql = "INSERT INTO horas_trabajadas (id_usuario, inicio_horario, final_horario, fecha_trabajo) VALUES (?, ?, ?, ?)";
$stmt = mysqli_prepare($conexion, $sql);


mysqli_stmt_bind_param($stmt, "isss", $id_del_usuario_actual, $inicio_horario, $final_horario, $fecha);

if (mysqli_stmt_execute($stmt)) {
    
    echo "Horas de trabajo guardadas correctamente.";
} else {
    
    echo "Error al guardar las horas: " . mysqli_error($conexion);
}

mysqli_stmt_close($stmt);
mysqli_close($conexion);
?>