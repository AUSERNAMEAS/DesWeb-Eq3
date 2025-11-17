<?php
// backend/updateStock.php
header('Content-Type: application/json; charset=utf-8');
include("conexion.php");

// 1. Recibir y decodificar los datos JSON (esperamos un array de productos con id y stock)
$datos = json_decode(file_get_contents('php://input'), true);

if (!$conexion) {
    http_response_code(500); 
    echo json_encode(['success' => false, 'message' => 'Error crítico de conexión DB.']);
    exit();
}

if (empty($datos) || !is_array($datos)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos inválidos o vacíos recibidos.']);
    sqlsrv_close($conexion);
    exit();
}

// 2. INICIO DE TRANSACCIÓN
// Esto asegura que si una actualización falla, ninguna se guarda.
sqlsrv_begin_transaction($conexion);

try {
    $sql_update = "UPDATE producto SET stock = ? WHERE id_producto = ?";
    foreach ($datos as $item) 
    {
        $id = (int)($item['id'] ?? 0);
        $stock = (int)($item['stock'] ?? 0);

        if ($id > 0) 
        {
            $params = array($stock, $id);
            $query = sqlsrv_query($conexion, $sql_update, $params);

            if ($query === false) {
                // Si la consulta falla, lanza una excepción para forzar el rollback
                throw new Exception("Error al actualizar stock para ID: " . $id);
            }
        }
    }

    // 3. ÉXITO: Confirmar todos los cambios
    sqlsrv_commit($conexion);
    echo json_encode(['success' => true, 'message' => " Se actualizaron los productos correctamente."]);
    
} catch (Exception $e) {
    // 4. FALLO: Revertir todos los cambios
    sqlsrv_rollback($conexion);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al guardar cambios: ' . $e->getMessage()]);
}

sqlsrv_close($conexion);
?>