<?php

session_start();

include('../php/conexion.php');

if (isset($_POST['usuario']) && isset($_POST['contrasena'])) {
    function validate($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $usuario = validate($_POST['usuario']);
    $contrasena = validate($_POST['contrasena']);

    if (empty($usuario) || empty($contrasena)) {
        header("Location: ../index.html");
        exit();
    } else {

        
        $sql = "SELECT * FROM usuarios WHERE usuario = ?";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "s", $usuario);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        
        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            
            
            if (password_verify($contrasena, $row['contrasena'])) { 
                $_SESSION['usuario'] = $row['usuario'];
                $_SESSION['id_usuario'] = $row['id_usuario'];
                header("Location: ../home.html");
                exit();
            } else {
                
                header("Location: ../login.html?error=1");
                exit();
            }
        } else {
            
            header("Location: ../login.html?error=1");
            exit();
        }
    }
} else {
    
    header("Location: ../index.html");
    exit();
}
?>