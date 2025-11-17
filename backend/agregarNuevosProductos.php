<?php
header('Content-Type: application/json; charset=utf-8');
include("conexion.php"); // Incluye la conexión a la base de datos

// 1. Recibir y decodificar los datos JSON enviados por JavaScript
$datos = json_decode(file_get_contents('php://input'), true);

// Verificar conexión
if (!$conexion) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos.']);
    exit();
}

// 2. Extracción y validación básica de datos // se uso ternarios por que a veces me daban errores por que estavan 0 o null
$nombre = $datos['nombre'] ?? '';
$precio = $datos['precio'] ?? 0.00;
$stock = $datos['stock'] ?? 0;
$categoria = $datos['categoria'] ?? '';
$descripcion = $datos['descripcion'] ?? '';
$imagen = $datos['imagen'] ?? '';
$peso_kg = $datos['peso_kg'] ?? '0.0kg';

// Validación simple de campos obligatorios
if (empty($nombre) || empty($precio) || $stock === null || empty($categoria)) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios.']);
    sqlsrv_close($conexion);
    exit();
}

$estado_producto = 'Activo';

// 3. Consulta SQL para INSERT
$sql = "INSERT INTO producto (nombre, descripcion, stock, categoria, peso_kg, estado_producto, precio_unitario, imagen) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
$params = array(
    $nombre,
    $descripcion,
    (int)$stock,
    $categoria,
    $peso_kg,
    $estado_producto,
    (float)$precio,
    $imagen
);

// 4. Ejecutar consulta
$busqueda = sqlsrv_query($conexion, $sql, $params);

if ($busqueda) {
    echo json_encode(['success' => true, 'message' => ' Producto agregado correctamente.']);
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => 'Error al insertar producto: ' . print_r(sqlsrv_errors(), true)]);
}

// 5. Cerrar conexión
sqlsrv_close($conexion);
?>