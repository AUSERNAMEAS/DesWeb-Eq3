<?php
include("conexion.php");

// Recibir datos del formulario
$nombre = $_POST['nombre'];
$email = $_POST['email'];
$contrasenia = $_POST['contrasenia'];

// Encriptar contraseña
$contrasenia_encriptada = password_hash($contrasenia, PASSWORD_DEFAULT);

// Consulta con parámetros
$sql = "INSERT INTO cliente (nombre, correo, contrasenia_hash) VALUES (?, ?, ?)";
$params = array($nombre, $email, $contrasenia_encriptada);

// Ejecutar consulta
$busqueda = sqlsrv_query($conexion, $sql, $params);

if ($busqueda) {
    echo "<script>alert('✅ Cuenta creada correctamente'); window.location.href='../FakeShop.php';</script>";
     session_start();
    $_SESSION['usuario'] = $email; //para guardar la sesion del usuario que se registro
} else {
    echo " Error al registrar: " . print_r(sqlsrv_errors(), true);
}

// Cerrar conexión
sqlsrv_close($conexion);
?>
