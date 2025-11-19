<?php
session_start();

include('php/conexion.php');

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: index.html");
    exit();
}


if (!isset($_GET['id_usuario']) || empty($_GET['id_usuario'])) {
    die("Error: ID de usuario no especificado.");
}

$id_usuario_a_revisar = $_GET['id_usuario'];


$sql = "
    SELECT 
        c.id, 
        c.numero_referencia, 
        c.ruta_archivo, 
        c.estado 
    FROM comprobantes c
    WHERE c.id_usuario = ?
    ORDER BY c.id DESC; 
";

$stmt = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_usuario_a_revisar);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$comprobantes = mysqli_fetch_all($resultado, MYSQLI_ASSOC);

mysqli_stmt_close($stmt);
mysqli_close($conexion);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobantes del Usuario #<?php echo htmlspecialchars($id_usuario_a_revisar); ?></title>
    <link rel="stylesheet" href="style/fonts.css">
    <link rel="stylesheet" href="style/style.css">
</head>

<body>
    <div class="ContainerVer">
        <a class="btn-volver" href="adminhome.php">⮜</a>
        <h2>Comprobantes del Usuario #<?php echo htmlspecialchars($id_usuario_a_revisar); ?></h2>
        

        <?php if (empty($comprobantes)): ?>
            <p>Este usuario no tiene comprobantes registrados.</p>
        <?php else: ?>
            <table class="tablaVer">
                <thead>
                    <tr>
                        <th>ID Comprobante</th>
                        <th>Referencia</th>
                        <th>Estado</th>
                        <th>Acción (Ver Archivo)</th>
                        <th>Gestión</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($comprobantes as $comp): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($comp['id']); ?></td>
                            <td><?php echo htmlspecialchars($comp['numero_referencia']); ?></td>
                            <td>
                                <span class="status-<?php echo htmlspecialchars($comp['estado']); ?>">
                                    <?php echo ucfirst(htmlspecialchars($comp['estado'])); ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?php echo htmlspecialchars($comp['ruta_archivo']); ?>"
                                    target="_blank" class="btn-view">Ver Archivo</a>
                            </td>
                            <td>
                                <?php if ($comp['estado'] === 'pendiente'): ?>
                                    <form method="POST" action="procesar_comprobante.php" style="display:inline;">
                                        <input type="hidden" name="id_comprobante" value="<?php echo htmlspecialchars($comp['id']); ?>">
                                        <input type="hidden" name="nuevo_estado" value="verificado">
                                        <button type="submit" class="btn-aprobar">Verificar</button>
                                    </form>
                                <?php else: ?>
                                    <span class="estado-verificado">Verificado</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>

</html>