<?php
// procesarPedido.php
session_start();
header('Content-Type: application/json');
include("conexion.php");

// 1. Verificar sesión de usuario
if (!isset($_SESSION['usuario'])) {
    http_response_code(401); 
    echo json_encode(['success' => false, 'message' => 'Debe iniciar sesión.']);
    sqlsrv_close($conexion);
    exit();
}

$email_cliente = trim($_SESSION['usuario']);
$datos = json_decode(file_get_contents('php://input'), true);

//para saber si llego la info
if (empty($datos) || empty($datos['carrito'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No hay datos de pedido.']); //explixa
    sqlsrv_close($conexion);//cierra la conexion a la base de datos
    exit();
}

// Obtener ID del cliente
$sql_cliente = "select id_cliente FROM cliente WHERE correo = ?";
$query_cliente = sqlsrv_query($conexion, $sql_cliente, array($email_cliente));
$fila_cliente = sqlsrv_fetch_array($query_cliente, SQLSRV_FETCH_ASSOC); // explixae
$id_cliente = $fila_cliente['id_cliente'];

// INICIO DE TRANSACCIÓN: Asegura que si una parte falla, se cancela todo.
sqlsrv_begin_transaction($conexion);
$id_pedido = null; 

try {
    // 2. INSERTAR en la tabla 'pedido'
    $sql_pedido = "insert INTO pedido (id_cliente, fecha_pedido, estado_pedido, metodo_pago, total) 
                   OUTPUT INSERTED.id_pedido 
                   VALUES (?, GETDATE(), 'Pendiente', ?, ?)";
    
    $params_pedido = array($id_cliente, $datos['metodo_pago'], (float)$datos['total_final']);
    $query_pedido = sqlsrv_query($conexion, $sql_pedido, $params_pedido);
    
    if (!$query_pedido || !sqlsrv_fetch($query_pedido)) //si sale mal o esta vacia avienta un error
    {
        throw new Exception("Error al insertar el pedido.");
    }
    $id_pedido = sqlsrv_get_field($query_pedido, 0); //aqui busca el primer campo osea el id del pedido

    // 3. INSERTAR en 'detalle_pedido' y ACTUALIZAR 'producto' (Stock)
    $sql_detalle = "insert INTO detalle_pedido (id_pedido, id_producto, id_cliente, cantidad, precio_unitario, subtotal) 
                    VALUES (?, ?, ?, ?, ?, ?)";
    //$sql_stock = "update producto SET stock = stock - ? WHERE id_producto = ?"; //todavia no implementado el stock

    foreach ($datos['carrito'] as $item) {
        $cantidad = (int)$item['quantity'];
        $precio = (float)$item['price'];
        $subtotal_item = $precio * $cantidad;

        // Insertar Detalle
        $params_detalle = array($id_pedido, (int)$item['id'], $id_cliente, $cantidad, $precio, $subtotal_item);// los valores para agregar en el detalle pedido;
        if (sqlsrv_query($conexion, $sql_detalle, $params_detalle) === false)//si falla 
        {
            throw new Exception("Error al insertar detalle para producto ID: " . $item['id']);
        }



        // Actualizar Stock // todavia no implementado el stock
        /*$params_stock = array($cantidad, (int)$item['id']);
        if (sqlsrv_query($conexion, $sql_stock, $params_stock) === false) {
            throw new Exception("Error al actualizar stock para producto ID: " . $item['id']);
        }*/


    }

    // 4. INSERTAR en la tabla 'envio'
    $direccion_completa = $datos['datos_envio']['direccion'] . ', ' . $datos['datos_envio']['ciudad'];
    
    $sql_envio = "insert INTO envio (id_pedido, direccion_envio, empresa_envio, costo_envio, estado_envio, fecha_envio) 
                  VALUES (?, ?, 'FicticiaExpress', ?, 'Pendiente de Empaque', GETDATE())";
    
    $params_envio = array($id_pedido, $direccion_completa, (float)$datos['costo_envio']);// los valores para agregar en envio
    
    if (sqlsrv_query($conexion, $sql_envio, $params_envio) === false) //si sale mal
    {
        throw new Exception("Error al insertar datos de envío.");
    }

    // 5. ÉXITO: Confirmar todos los cambios
    sqlsrv_commit($conexion);
    echo json_encode(['success' => true, 'message' => '¡Pedido procesado correctamente! Su número de pedido es #' . $id_pedido]);
    
} 

catch (Exception $e) //si sale algom mal en el try revierte todo
{
    sqlsrv_rollback($conexion);
    http_response_code(500); 
    echo json_encode(['success' => false, 'message' => 'Error al guardar el pedido. ' . $e->getMessage()]);
}

sqlsrv_close($conexion);
?>