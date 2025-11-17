<?php
$serverName = "LAPTOP\\SQLEXPRESS"; // <- cambia por tu nombre de servidor
$connectionInfo = array(
    "Database" => "Ventas", // nombre de la base de datos en SQL Server
    "UID" => "",            // usuario (vacío si usas autenticación de Windows)
    "PWD" => ""             // contraseña (vacía si usas autenticación de Windows)
);
$conexion = sqlsrv_connect($serverName, $connectionInfo);
if (!$conexion) 
{
    echo "Error al conectar a la base de datos: " . print_r(sqlsrv_errors(), true);
    exit();
}

