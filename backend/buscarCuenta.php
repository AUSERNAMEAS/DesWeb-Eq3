<?php
session_start(); //checa si hay una sesion iniciada

include("conexion.php");

// Recibir datos del formulario
$email = $_POST['email_iniciar'];
$contrasenia = $_POST['contrasenia_iniciar'];

// Consulta SQL con parámetros
$sql = "Select correo,contrasenia_hash from cliente where correo=?";
$params = array($email);//remplazan los valores ?, seguridad creo

// Ejecutar consulta
$busqueda = sqlsrv_query($conexion, $sql, $params);//si la consulta es correcta la regresa(true),si no regresa false

if ($busqueda && sqlsrv_has_rows($busqueda))//si la consulta tiene filas true
{
    $fila = sqlsrv_fetch_array($busqueda, SQLSRV_FETCH_ASSOC);//esto tiene la fila del resultado del registro que buscamos

    // Verificar contraseña
    if (password_verify($contrasenia, $fila['contrasenia_hash']))//aqui compara la cntrase;a ingresada con la encriptada
        {
        // ✅ Login correcto → Guardar sesión
        $_SESSION['usuario'] = $email;
        $_SESSION['rol'] = $fila['rol']; // Guardar saber si es admin o usuario normal

        // sql rellena los espacios que faltan con espacios en blanco, asi que hay que limpiarlo
        //$rol_limpio = trim($fila['rol']);
         $rol_limpio = 'admin'; //<- solo para pruebas


        //verificar si es admin o usuario normal

        if ($rol_limpio === 'admin') {
            echo "<script>alert('✅ Bienvenido Administrador, $email'); window.location.href='../adminPanel.php';</script>";
        } else {
            echo "<script>alert('✅ Bienvenido, $email, Su rol es: $rol_limpio'); window.location.href='../FakeShop.php';</script>";
        }

    //este dice si la contraseña es incorrecta
    } else {
        echo "<script>alert(' Contraseña incorrecta o usuario incorrecto'); window.history.back();</script>";
    }
} 
//aqui dice si no encontro el usuario
else {
    echo "<script>alert(' Usuario no encontrado'); window.history.back();</script>";
}
// Cerrar conexión
sqlsrv_close($conexion);
?>
