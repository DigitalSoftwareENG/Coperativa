<?php

session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.html");
    exit();
}

$id_del_usuario_actual = $_SESSION['id_usuario'];

include('php/conexion.php');

$sql_horas = "
    SELECT
        SUM(TIME_TO_SEC(TIMEDIFF(final_horario, inicio_horario))) / 3600 AS horas_trabajadas_semana
    FROM horas_trabajadas
    WHERE
        id_usuario = ?
        AND YEARWEEK(fecha_trabajo, 1) = YEARWEEK(CURDATE(), 1);
";

$stmt_horas = mysqli_prepare($conexion, $sql_horas);
mysqli_stmt_bind_param($stmt_horas, "i", $id_del_usuario_actual);
mysqli_stmt_execute($stmt_horas);
$resultado = mysqli_stmt_get_result($stmt_horas);
$fila = mysqli_fetch_assoc($resultado);

$horas_trabajadas_esta_semana = round($fila['horas_trabajadas_semana'], 1) ?? 0;
$horas_requeridas = 27.0;

$saldo_a_favor_a_mostrar = max(0, $horas_trabajadas_esta_semana - $horas_requeridas);
$faltan_a_mostrar = max(0, $horas_requeridas - $horas_trabajadas_esta_semana);

mysqli_stmt_close($stmt_horas);

$mensaje = '';
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'comprobante_success') {
        $mensaje = ' El comprobante ha sido subido exitosamente.';
    } elseif ($_GET['status'] == 'comprobante_error' && isset($_GET['msg'])) {
        $mensaje = ' Error al subir el comprobante: ' . htmlspecialchars($_GET['msg']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/fonts.css">
    <link rel="stylesheet" href="style/style.css">
    <title>Home</title>
    <style>
        .mensaje-estado {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            font-weight: bold;
        }
        .mensaje-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .mensaje-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>

<body class="BodyHome">
    <header class="HeaderHomes">
        <a href="api/Logout.php">⮜ Cerrar Sesión</a>
        <h2>Home</h2>
    </header>

    <main>
        <div class="DivisorHome">
            
            <?php if ($mensaje): ?>
                <div class="mensaje-estado <?php echo strpos($mensaje, '✅') !== false ? 'mensaje-success' : 'mensaje-error'; ?>">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <div>
                <div style="margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; border: 1px solid #ccc; padding: 10px; border-radius: 8px;">
                    <span style="font-size: 14px; font-weight: bold; color: #155724; background-color: #d4edda; padding: 5px 10px; border-radius: 5px;">
                        Esta Semana: <strong><?php echo $horas_trabajadas_esta_semana; ?></strong> Hrs trabajadas
                    </span>

                    <?php if ($saldo_a_favor_a_mostrar > 0): ?>
                        <span style="font-size: 14px; font-weight: bold; color: #0c5460; background-color: #d1ecf1; padding: 5px 10px; border-radius: 5px;">
                            Saldo a Favor: <strong><?php echo $saldo_a_favor_a_mostrar; ?></strong> Hrs
                        </span>
                    <?php elseif ($faltan_a_mostrar > 0): ?>
                        <span style="font-size: 14px; font-weight: bold; color: #856404; background-color: #fff3cd; padding: 5px 10px; border-radius: 5px;">
                            Faltan: <strong><?php echo $faltan_a_mostrar; ?></strong> Hrs para las 27
                        </span>
                    <?php else: ?>
                        <span style="font-size: 14px; font-weight: bold; color: #155724; background-color: #d4edda; padding: 5px 10px; border-radius: 5px;">
                            Meta Semanal Cumplida.
                        </span>
                    <?php endif; ?>

                </div>

                <a class="HorasTrabajo" href="#" onclick="toggleMenu(event, 'menuHoras')">Marcar Horas de trabajo</a>
                <form method="post" class="menu-horas" id="menuHoras" action="api/horas.php">
                    <label for="HoraInicio">Inicio de Horario</label>
                    <input type="time" name="RegHoraInicio" id="HoraInicio" required>

                    <label for="HoraFinal">Final de Horario</label>
                    <input type="time" name="RegHoraFinal" id="HoraFinal" required>

                    <label for="Fecha">Fecha de su Horario</label>
                    <input type="date" name="RegFecha" id="Fecha" required>

                    <input type="submit" value="Enviar">
                </form>
            </div>
            <div>
                <a class="ComprobantePago" href="#" onclick="toggleMenu(event, 'menuComprobante')">Subir Comprobante</a>

                <form class="menu-comprobante" id="menuComprobante" action="api/comprobantes.php" method="post" enctype="multipart/form-data">
                    <label for="referencia">Numero de referencia:</label>
                    <input type="text" id="referencia" name="referencia" placeholder="Ej: 123456789" required>

                    <label for="comprobante">Selecciona tu comprobante:</label>
                    <input type="file" id="comprobante" name="comprobante" accept=".jpg,.jpeg,.png,.pdf" required>
                    <p class="nota">Formatos permitidos: JPG, PNG o PDF (max. 5 MB)</p>

                    <button type="submit">Enviar</button>
                </form>
            </div>
        </div>
    </main>

    <script>
    function toggleMenu(event, menuId) {
        event.preventDefault(); 
        const menu = document.getElementById(menuId);
        
        if (menu.style.display === 'block') {
            menu.style.display = 'none';
        } else {
            menu.style.display = 'block';
        }
    }
    </script>
</body>

</html>