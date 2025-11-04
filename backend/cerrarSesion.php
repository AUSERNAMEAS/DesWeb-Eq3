<?php
session_start(); // Inicia la sesión
session_destroy(); // borra los datos de la sesión
header("Location: ../FakeShop.php"); // Redirige a la página principal
exit();
?>