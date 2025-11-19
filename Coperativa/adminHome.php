<?php



include 'php/conexion.php';


session_start();



if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: index.html");
    exit();
}



$sql_usuarios = "SELECT * FROM usuarios WHERE rol = 'socio' OR estado = 'pendiente'";
$stmt_usuarios = $conexion->prepare($sql_usuarios);
$stmt_usuarios->execute();
$resultado = $stmt_usuarios->get_result();

$usuarios = [];

while ($fila = $resultado->fetch_assoc()) {
    $usuarios[] = $fila;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/fonts.css">
    <link rel="stylesheet" href="style/style.css">
    <title>Admin Home</title>
</head>

<body class="BodyHome">
    <header class="HeaderHomes">
        <a href="api/Logout.php">⮜ Cerrar Sesión</a>
        <h2>Admin</h2>
    </header>

    <main>
        <div class="HomeAdmin">
            <h2>Gestión de Socios y Pendientes</h2>

            <?php if (count($usuarios) > 0): ?>
                <table class="tabla-usuarios">
                    <thead>
                        <tr>
                            <th>ID Usuario</th>
                            <th>Nombre</th>
                            <th>Estado Actual</th>
                            <th>Horas Trabajadas (Semana Actual)</th>
                            <th>Comprobantes</th>
                            <th>Gestión</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr class="DatosUsuarios">
                                <td><?php echo htmlspecialchars($usuario['id_usuario']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['usuario']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['estado']); ?></td>
                                <td>
                                    <?php
                                    $sql_horas = "SELECT SUM(TIME_TO_SEC(TIMEDIFF(final_horario, inicio_horario))) as total_segundos 
                                                  FROM horas_trabajadas 
                                                  WHERE id_usuario = ? AND YEARWEEK(fecha_trabajo, 1) = YEARWEEK(CURDATE(), 1)";
                                    $stmt_horas = $conexion->prepare($sql_horas);
                                    $stmt_horas->bind_param("i", $usuario['id_usuario']);
                                    $stmt_horas->execute();
                                    $resultado_horas = $stmt_horas->get_result();
                                    $horas_data = $resultado_horas->fetch_assoc();

                                    if ($horas_data && $horas_data['total_segundos'] > 0) {
                                        $horas_total = floor($horas_data['total_segundos'] / 3600);
                                        $minutos_total = floor(($horas_data['total_segundos'] % 3600) / 60);
                                        echo "{$horas_total}h {$minutos_total}m";
                                    } else {
                                        echo "0h 0m";
                                    }
                                    $stmt_horas->close();
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $sql_count = "SELECT COUNT(*) AS total FROM comprobantes WHERE id_usuario = ?";
                                    $stmt_count = $conexion->prepare($sql_count);
                                    $stmt_count->bind_param("i", $usuario['id_usuario']);
                                    $stmt_count->execute();
                                    $resultado_count = $stmt_count->get_result();
                                    $data_count = $resultado_count->fetch_assoc();
                                    $total_comprobantes = $data_count['total'];
                                    $stmt_count->close();

                                    $id_usuario_actual = htmlspecialchars($usuario['id_usuario']);

                                    if ($total_comprobantes > 0) {

                                        echo "<a href='ver_comprobantes.php?id_usuario={$id_usuario_actual}' class='btn-ver-lista'>";
                                        echo "Ver Lista ({$total_comprobantes})";
                                        echo "</a>";
                                    } else {
                                        echo "Sin Comprobante";
                                    }
                                    ?>
                                </td>
                                <td>
                                <?php if ($usuario['estado'] === 'pendiente'): ?>
                                    <form action="cambiar_estado.php" method="post" style="display:inline;">
                                        <input type="hidden" name="id_usuario" value="<?php echo $usuario['id_usuario']; ?>">
                                        <input type="hidden" name="accion" value="aprobar">
                                        <button type="submit" class="btn-aprobar">Convertir a Socio</button>
                                    </form>
                                <?php else: ?>
                                    <span class="estado-activo">Socio</span>
                                <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No hay usuarios registrados como socios ni pendientes.</p>
            <?php endif; ?>
        </div>
        </div>
    </main>
</body>

</html>