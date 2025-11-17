<?php
header('Content-Type: application/json; charset=utf-8');
include("conexion.php");


// Verificar si hay conexión
if (!$conexion) {
    http_response_code(500);
    echo json_encode(["error" => "No hay conexión a la base de datos", "details" => sqlsrv_errors()]);
    exit();
}

//$sql = "select id_producto,nombre,descripción AS descripcion,categoría AS categoria,peso_kg,estado_producto,precio_unitario,stock,imagen FROM producto";
$sql = "select id_producto,nombre,precio_unitario,imagen from producto";
$query = sqlsrv_query($conexion, $sql);
$productos = [];
if ($query === false) {
    http_response_code(500);
    echo json_encode(["error" => "Error en la consulta: " . print_r(sqlsrv_errors(), true)]);
    exit();
}

while ($row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)) {
    $nameFixed = iconv("UTF-8", "UTF-8//IGNORE", $row["nombre"]); //no funciona sin esto por acentos o no se
    $productos[] = [
        "id" => $row["id_producto"],
        "name"=>$nameFixed,
        "price" => floatval($row["precio_unitario"]),
        "image" => $row["imagen"],
        //"descripcion" => $row["descripcion"],
        //"stock" => intval($row["stock"]),
        //"categoria" => $row["categoria"],
        //"estado_producto" => $row["estado_producto"]
    ];
}
sqlsrv_free_stmt($query);
sqlsrv_close($conexion);
echo json_encode($productos);

