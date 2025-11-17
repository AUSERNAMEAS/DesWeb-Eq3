<?php
// adminPanel.php

// 1. INCLUIR CONEXIÓN A LA BASE DE DATOS
// Se asume que 'conexion.php' está configurado y contiene la función sqlsrv_connect().
include("backend/conexion.php"); 
session_start();

// Lógica de seguridad para verificar el rol de administrador (descomentar y usar)
/*
if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol']) || trim($_SESSION['rol']) !== 'admin') {
    header("Location: FakeShop.php");
    exit();
}
$admin_email = $_SESSION['usuario'];
*/

// --- INICIO DE LÓGICA DE RECUPERACIÓN DE DATOS PARA EL DASHBOARD Y PRODUCTOS ---

// Variables para el Dashboard (Valores por defecto si la conexión falla)
$total_pedidos = 0;
$pedidos_pendientes = 0;
$ventas_del_mes = 0.00; // El cálculo de ventas se deja simple/fijo por ahora.
$productos_stock = []; // Array para almacenar los productos

if (isset($conexion) && $conexion) { //Verifica que la conexión se haya establecido correctamente
    // 2. RECUPERAR DATOS DEL DASHBOARD

    // Consulta 1: Total de Pedidos
    $sql_total_pedidos = "SELECT COUNT(id_pedido) AS TotalPedidos FROM pedido";
    $query_total_pedidos = sqlsrv_query($conexion, $sql_total_pedidos);
    if ($query_total_pedidos && sqlsrv_fetch($query_total_pedidos)) 
    {
        //obtiene cuantos pedidos hay (columna 0)
        $total_pedidos = sqlsrv_get_field($query_total_pedidos, 0);
    }
    
    // Consulta 2: Pedidos Pendientes (Basado en el estado de la tabla 'envio')
    $sql_pedidos_pendientes = "SELECT COUNT(id_envio) AS Pendientes FROM envio WHERE estado_envio = 'Pendiente de Empaque'";
    $query_pedidos_pendientes = sqlsrv_query($conexion, $sql_pedidos_pendientes);
    if ($query_pedidos_pendientes && sqlsrv_fetch($query_pedidos_pendientes)) {
        // Obtiene el campo del conteo (columna 0)
        $pedidos_pendientes = sqlsrv_get_field($query_pedidos_pendientes, 0);
    }

    // Consulta 3: Productos para la tabla de Gestión (ID, Nombre, Precio, Stock)
    $sql_productos = "SELECT id_producto, nombre, precio_unitario, stock FROM producto";
    $query_productos = sqlsrv_query($conexion, $sql_productos);
    if ($query_productos) {
        while ($row = sqlsrv_fetch_array($query_productos, SQLSRV_FETCH_ASSOC)) 
        {//probable error de nombre por acentos
            $productos_stock[] = $row;
        }
    }

    //consulta 4: Ventas del Mes ahora que s e mueva
    $sql_montoDelMes = "SELECT SUM(total) AS MontoMes FROM pedido WHERE MONTH(fecha_pedido) = MONTH(GETDATE()) AND YEAR(fecha_pedido) = YEAR(GETDATE())";
    $query_montoDelMes = sqlsrv_query($conexion, $sql_montoDelMes);
    if ($query_montoDelMes && sqlsrv_fetch($query_montoDelMes)) {
        $monto_mes = sqlsrv_get_field($query_montoDelMes, 0);
    }
    // Consulta 5: Próximos Envíos (para el Calendario)
    $proximos_envios = [];
    $sql_proximos_envios = "
       -- Selecciona los 5 días de envío más recientes, contando los pedidos de cada día.
        -- Se usa fecha_envio porque es la que se inserta en el momento de procesar el pedido.
        SELECT TOP 5 
            CONVERT(VARCHAR(10), fecha_envio, 120) AS FechaBase, 
            COUNT(id_envio) AS Cantidad
        FROM envio
        -- Solo incluye registros que tienen una fecha de envío.
        WHERE fecha_envio IS NOT NULL
        GROUP BY fecha_envio
        -- Muestra los más recientes primero.
        ORDER BY fecha_envio DESC
    ";
    $query_proximos_envios = sqlsrv_query($conexion, $sql_proximos_envios);

    if ($query_proximos_envios) 
    {
        while ($row = sqlsrv_fetch_array($query_proximos_envios, SQLSRV_FETCH_ASSOC)) {
            $proximos_envios[] = [
                'Fecha' => $row['FechaBase'], 
                'Cantidad' => $row['Cantidad']
            ];
        }
    }
    

    // Consulta 6: Pedidos Recientes (Top 5)
    $pedidos_recientes = [];
    $sql_pedidos_recientes = "
        SELECT TOP 5
            p.id_pedido,
            CONVERT(VARCHAR(10), p.fecha_pedido, 120) AS FechaPedido,
            p.total,
            p.estado_pedido,
            c.nombre AS NombreCliente
        FROM pedido p
        JOIN cliente c ON p.id_cliente = c.id_cliente
        ORDER BY p.fecha_pedido DESC
    ";
    $query_pedidos_recientes = sqlsrv_query($conexion, $sql_pedidos_recientes);

    if ($query_pedidos_recientes) {
        while ($row = sqlsrv_fetch_array($query_pedidos_recientes, SQLSRV_FETCH_ASSOC)) {
            $pedidos_recientes[] = [
                'id_pedido' => $row['id_pedido'],
                'fecha_pedido' => $row['FechaPedido'],
                'total' => $row['total'], 
                'estado_pedido' => $row['estado_pedido'],
                'cliente' => trim($row['NombreCliente']) // Limpiar espacios del nombre
            ];
        }
    }

    // Consulta 7: Solicitudes de Personalización Pendientes
    $solicitudes_pendientes = [];
    
    $sql_solicitudes = "
        SELECT TOP 5 id_solicitud, tipo_producto, instrucciones, CONVERT(VARCHAR(10), fecha_solicitud, 120) AS FechaSolicitud
        FROM solicitud_personalizacion 
        WHERE estado = 'Pendiente' 
        ORDER BY fecha_solicitud ASC";

    $query_solicitudes = sqlsrv_query($conexion, $sql_solicitudes);

    if ($query_solicitudes) {
        while ($row = sqlsrv_fetch_array($query_solicitudes, SQLSRV_FETCH_ASSOC)) {
            $solicitudes_pendientes[] = [
                'id_solicitud' => $row['id_solicitud'],
                'tipo_producto' => $row['tipo_producto'],
                'instrucciones' => $row['instrucciones'],
                'fecha_solicitud' => $row['FechaSolicitud']
            ];
        }
    }

    sqlsrv_close($conexion); // Se puede cerrar la conexión aquí o al final del script.
}


// Variables de la sesión, usando un valor por defecto si no está iniciada (solo para pruebas)
$admin_email = $_SESSION['usuario'] ?? 'admin@example.com';
// Se usan placeholders si no se pudo conectar a la BD, para que la vista no se rompa
if ($total_pedidos === 0) $total_pedidos = 'N/A';
if ($pedidos_pendientes === 0) $pedidos_pendientes = 'N/A';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Fake Shop</title>
    <link rel="stylesheet" href="estilos/main.css">
    <link rel="stylesheet" href="estilos/admin.css"> 
</head>
<body>

    <header>
        <img src="imagenes/logo.jpg" alt="Logo Fake Shop" style="height:48px;vertical-align:middle;">
        <span style="margin-left:20px;">Panel de Administrador: <?php echo $admin_email; ?></span>
        <a href="backend/cerrarSesion.php" style="margin-left:12px;">Cerrar sesión</a>
    </header>

    <nav>
        <a href="#dashboard">Dashboard</a>
        <a href="#orders">Pedidos</a>
        <a href="#custom-requests">Personalización</a>
        <a href="#products-manage">Productos</a>
    </nav>

    <div class="container">
        <h1>Bienvenido al Panel de Administración</h1>

        <section id="dashboard" class="dashboard-grid">
            <div class="card">
                <h2>Total de Pedidos</h2>
                <p class="big-number"><?php echo $total_pedidos; ?></p>
                <p>Órdenes totales en la base de datos</p>
            </div>
            <div class="card">
                <h2>Ventas del Mes</h2>
                <p class="big-number">$<?php echo $monto_mes ?? 'N/A'; ?></p>
                <p>Monto de ventas actual</p>
            </div>
            <div class="card">
                <h2>Solicitudes Pendientes</h2>
                <p class="big-number"><?php echo $pedidos_pendientes; ?></p>
                <p>Pedidos listos para ser empacados</p>
            </div>
            
            <div class="card full-width">
                <h2>Calendario de Envíos Importantes</h2>
                <p>Pedidos con fecha de entrega programada (próximos 5)</p>
                <div class="calendar-placeholder">
                <p>📅 Próximos envíos/pedidos:</p>
                <ul>
                    <?php if (!empty($proximos_envios)): ?>
                        <?php foreach ($proximos_envios as $envio): ?>
                            <li><?php echo $envio['Fecha']; ?>: **<?php echo $envio['Cantidad']; ?>** Pedido(s)</li>
                        <?php endforeach; ?>
                        <?php else: ?>
                            <li>No hay envíos programados próximamente.</li>
                        <?php endif; ?>
                </ul>
    </div>
            </div>
        </section>
        <section id="orders">
        <h2>Pedidos Recientes</h2>
        <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID Pedido</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Detalles</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($pedidos_recientes)): ?>
                    <?php foreach ($pedidos_recientes as $pedido): ?>
                        <tr>
                            <td>#<?php echo $pedido['id_pedido']; ?></td>
                            <td><?php echo $pedido['cliente']; ?></td>
                            <td><?php echo $pedido['fecha_pedido']; ?></td>
                            <td>$<?php echo number_format((float)$pedido['total'], 2); ?></td>
                            <td>
                                <?php
                                    // 1. Limpia el estado y lo prepara
                                    $estado = trim($pedido['estado_pedido']);
                                    // 2. Asigna la clase CSS basándose en el estado
                                    $status_class = 'pending'; // Default
                                    if (strpos($estado, 'Enviado') !== false || strpos($estado, 'sent') !== false) {
                                        $status_class = 'sent';
                                    } elseif (strpos($estado, 'Completado') !== false) {
                                        $status_class = 'completed';
                                    }
                                ?>
                                <span class="status <?php echo $status_class; ?>"><?php echo $estado; ?></span>
                            </td>
                            <td><a href="#">Ver</a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No hay pedidos recientes registrados en la base de datos.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

        <section id="custom-requests">
            <h2>Solicitudes de Personalización Pendientes</h2>
            <p>Sección para gestionar las solicitudes del formulario 'Personaliza tu Producto'.</p>
            <ul>
                <?php if (!empty($solicitudes_pendientes)): ?>
                    <?php foreach ($solicitudes_pendientes as $solicitud): ?>
                        <li>
                            <strong>ID:<?php echo $solicitud['id_solicitud']; ?></strong>
                            (Fecha: <?php echo $solicitud['fecha_solicitud']; ?>) 
                            <?php echo $solicitud['tipo_producto']; ?>:
                            <?php echo $solicitud['instrucciones']; ?> 
                            - <a href="#">Ver/Aprobar</a>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>No hay solicitudes de personalización pendientes.</li>
                <?php endif; ?>
    </ul>
        </section>

        <section id="products-manage">
            <h2>Gestión de Productos</h2>

            <h3>Stock de Productos Existentes</h3>
            <div class="table-responsive">
                <table id="product-stock-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Stock Actual</th>
                            <th>Modificar Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos_stock as $producto): ?>
                        <tr data-product-id="<?php echo $producto['id_producto']; ?>">
                            <td><?php echo $producto['id_producto']; ?></td>
                            <td><?php echo $producto['nombre']; ?></td>
                            <td>$<?php echo number_format($producto['precio_unitario'], 2); ?></td>
                            <td class="stock-value" id="stock-<?php echo $producto['id_producto']; ?>"><?php echo $producto['stock']; ?></td>
                            <td>
                                <button class="stock-btn decrease-stock" data-action="decrease" data-id="<?php echo $producto['id_producto']; ?>">-</button>
                                <button class="stock-btn increase-stock" data-action="increase" data-id="<?php echo $producto['id_producto']; ?>">+</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <button id="save-stock-btn" class="btn-product-action" style="background-color: #28a745; margin-top: 1rem; width: 100%;">
                        Guardar Cambios de Stock
                        </button>

<hr style="margin: 2rem 0; border-color: #eee;">
                </table>
            </div>

            <hr style="margin: 2rem 0; border-color: #eee;">

            <h3>Agregar Nuevo Producto</h3>
            <form id="add-product-form" class="product-form-grid">
                <div class="form-group">
                    <label for="new-name">Nombre del Producto:</label>
                    <input type="text" id="new-name" required>
                </div>
                <div class="form-group">
                    <label for="new-price">Precio (MXN):</label>
                    <input type="number" id="new-price" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label for="new-stock">Stock Inicial:</label>
                    <input type="number" id="new-stock" min="0" required>
                </div>
                <div class="form-group">
                    <label for="new-category">Categoría:</label>
                    <input type="text" id="new-category" placeholder="Ej. Playera, Taza" required>
                </div>
                <div class="form-group full-row">
                    <label for="new-description">Descripción:</label>
                    <textarea id="new-description" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label for="new-image">Imagen del Producto (URL o Archivo):</label>
                    <input type="text" id="new-image" placeholder="ruta/o/url/imagen.jpg">
                    </div>
                <div class="form-group">
                    <label for="new-weight">Peso (kg):</label>
                    <input type="text" id="new-weight" placeholder="Ej. 0.5kg">
                </div>

                <div class="form-group full-row">
                    <button type="submit" id="btn-add-product" class="btn-product-action">Añadir Producto</button>
                </div>
            </form>
        </section>

    </div>
    <script src="scripts/adminPanel.js"></script>
    </body>
</html>