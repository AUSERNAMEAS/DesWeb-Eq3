<?php
// backend/saveCustomRequest.php
header('Content-Type: application/json; charset=utf-8');
session_start();
include("conexion.php"); // Incluye la conexión a la base de datos

// Verificar conexión
if (!$conexion) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos.']);
    exit();
}


// 2. Recibir datos JSON
$datos = json_decode(file_get_contents('php://input'), true);

$tipo_producto = $datos['productType'] ?? '';
$instructions = $datos['instructions'] ?? '';
$image_file_name = $datos['imageFileName'] ?? ''; // Nombre del archivo enviado

if (empty($tipo_producto)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Debe especificar un tipo de producto.']);
    sqlsrv_close($conexion);
    exit();
}

// 3. Consulta INSERT
$sql = "INSERT INTO solicitud_personalizacion (tipo_producto, instrucciones, imagen_nombre, estado, fecha_solicitud) 
        VALUES (?, ?, ?, 'Pendiente', GETDATE())";
        
$params = array(
    $tipo_producto,
    $instructions,
    $image_file_name
);

$query = sqlsrv_query($conexion, $sql, $params);

if ($query) {
    echo json_encode(['success' => true, 'message' => ' Solicitud enviada correctamente. ¡Pronto te contactaremos!']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al guardar la solicitud: ' . print_r(sqlsrv_errors(), true)]);
}

sqlsrv_close($conexion);
?>