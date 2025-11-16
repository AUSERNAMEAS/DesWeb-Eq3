<?php
//ahora obtenemos los datos desde la base de datos
header('Content-Type: application/json; charset=utf-8');

// Incluye la conexión a la base de datos
include("conexion.php"); 

// 1. Consulta SQL para obtener los datos necesarios
// Selecciona los campos de la tabla que coinciden con lo que necesita el frontend.
$sql = "select id_producto,nombre,precio_unitario, imagen FROM producto";
$query = sqlsrv_query($conexion, $sql);

if ($query === false) //si la consulta falla
{
    http_response_code(500);
    echo json_encode(['error' => 'Error al consultar productos: ' . print_r(sqlsrv_errors(), true)]); //envia mensaje de error
    sqlsrv_close($conexion);
    exit();
}

$productos_DataBase = [];
// 2. Recorrer los resultados y los hace JSON
while ($row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)) //guarda cada fila en un array,osea en eow
{
    $id = (int)$row['id_producto'];
    
    // lo pasa al array final:
    $productos_DataBase[] = [
        'id' => $id,
        'name' => trim($row['nombre']), 
        'price' => (float)$row['precio_unitario'],
        'image' => trim($row['imagen']) 
    ];
}

sqlsrv_free_stmt($query);
sqlsrv_close($conexion);

// 3. Devolver el array final codificado en formato JSON
echo json_encode($productos_bd);

?>