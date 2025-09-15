<?php

include 'php/conexion.php';


session_start();


if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: index.html");
    exit();
}


$sql_usuarios = "SELECT * FROM usuarios WHERE rol = 'socio'";
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
            <h2>Gestión de Usuarios</h2>

            <?php if (count($usuarios) > 0): ?>
                <table class="tabla-usuarios">
                    <thead>
                        <tr class="DatosUsuarios">
                            <th>ID Usuario</th>
                            <th>Nombre</th>
                            <th>Estado</th>
                            <th>Horas Trabajadas</th>
                            <th>Comprobante de Pago</th>
                            <th>Acción</th>
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

                                    $sql_horas = "SELECT SUM(TIME_TO_SEC(TIMEDIFF(final_horario, inicio_horario))) as total_segundos FROM horas_trabajadas WHERE id_usuario = ?";
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
                                        echo "N/A";
                                    }
                                    $stmt_horas->close();
                                    ?>
                                </td>
                                <td>
                                    <?php

                                    $sql_comprobante = "SELECT * FROM comprobantes WHERE id_usuario = ?";
                                    $stmt_comprobante = $conexion->prepare($sql_comprobante);
                                    $stmt_comprobante->bind_param("i", $usuario['id_usuario']);
                                    $stmt_comprobante->execute();
                                    $resultado_comprobante = $stmt_comprobante->get_result();
                                    $comprobante = $resultado_comprobante->fetch_assoc();

                                    if ($comprobante && !empty($comprobante['ruta_archivo'])) {
                                        $ruta_completa = htmlspecialchars($comprobante['ruta_archivo']);
                                        echo "<a href='{$ruta_completa}' target='_blank'>Ver Comprobante</a>";
                                    } else {
                                        echo "Sin Comprobante";
                                    }
                                    $stmt_comprobante->close();
                                    ?>
                                </td>
                                <td>
                                    <?php if ($usuario['estado'] === 'pendiente' || $usuario['estado'] === 'inactivo'): ?>
                                        <form action="api/actualizar_estado.php" method="post" style="display:inline;">
                                            <input type="hidden" name="id_usuario" value="<?php echo $usuario['id_usuario']; ?>">
                                            <input type="hidden" name="estado" value="activo">
                                            <button type="submit" class="btn-activar">Activar</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="estado-activo">Activo</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No hay usuarios registrados como socios.</p>
            <?php endif; ?>
        </div>
        </div>
    </main>
</body>

</html>