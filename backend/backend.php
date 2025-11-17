<?php/*
include("conexion.php");
//ahora obtenemos los datos desde la base de datos
header('Content-Type: application/json; charset=utf-8');
// Consulta SQL para obtener los datos necesarios
$sql = "select * FROM producto";
$query = sqlsrv_query($conexion, $sql);
if ($query === false) //si la consulta falla
{
    http_response_code(500);
    header('Content-Type: text/plain');
    print_r(sqlsrv_errors());  // ⬅️ AQUÍ vemos el error real
    exit();
}
$productos_DataBase=[];
// 2. Recorrer los resultados y los hace JSON
while ($row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)) //mientras haya filas que leer
{
    $productos_DataBase[]=[
        "id"=> $row["id_producto"],
        "name"=>$row["nombre"],
        "price"=>floatval($row["precio_unitario"]),
        "image"=>$row["imagen"]
    ];
}
echo json_encode($productos_DataBase);*/
include("conexion.php");

// Verificar si hay conexión
if (!$conexion) {
    http_response_code(500);
    echo json_encode(["error" => "No hay conexión a la base de datos"]);
    exit();
}

header('Content-Type: application/json; charset=utf-8');

$sql = "select * from producto";
$query = sqlsrv_query($conexion, $sql);

if ($query === false) {
    http_response_code(500);
    echo json_encode(["error" => "Error en la consulta: " . print_r(sqlsrv_errors(), true)]);
    exit();
}

$productos_DataBase = [];

while ($row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)) {
    $productos_DataBase[] = [
        "id" => $row["id_producto"] ?? null,
        "name" => $row["nombre"] ?? '',
        "price" => floatval($row["precio_unitario"] ?? 0),
        "image" => $row["imagen"] ?? ''
    ];
}

// Cerrar conexión
sqlsrv_free_stmt($query);
sqlsrv_close($conexion);

echo json_encode($productos_DataBase, JSON_UNESCAPED_UNICODE);
