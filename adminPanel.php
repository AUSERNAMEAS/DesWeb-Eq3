<?php
//session_start();
// Verificar si el usuario ha iniciado sesión y tiene el rol de administrador
// Esto es importante para la seguridad


/*if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    // Si no es admin o no ha iniciado sesión, redirigir a la página principal
    header("Location: FakeShop.php");
    exit();
}
// El correo del administrador está en $_SESSION['usuario']
$admin_email = $_SESSION['usuario']; */
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
                <p class="big-number">125</p>
                <p>Órdenes en el último mes</p>
            </div>
            <div class="card">
                <h2>Ventas del Mes</h2>
                <p class="big-number">$15,450 MXN</p>
                <p>Meta: $20,000 MXN</p>
            </div>
            <div class="card">
                <h2>Solicitudes Pendientes</h2>
                <p class="big-number">4</p>
                <p>Pedidos a Empacar</p>
            </div>
            
            <div class="card full-width">
                <h2>Calendario de Envíos Importantes</h2>
                <p>Aqui planeamos poner un calendario con los pedidos por haber, por ahora esta vacío</p>
                <div class="calendar-placeholder">
                    <p>📅 Próximos 5 días con envíos/pedidos:</p>
                    <ul>
                        <li>25/11/2025: 3 Pedidos (Urgente)</li>
                        <li>26/11/2025: 1 Pedido</li>
                        <li>28/11/2025: 5 Pedidos (Black Friday)</li>
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
                        <tr>
                            <td>#00101</td>
                            <td>Ana López</td>
                            <td>2025-11-08</td>
                            <td>$500.00</td>
                            <td><span class="status pending">Pendiente</span></td>
                            <td><a href="#">Ver</a></td>
                        </tr>
                        <tr>
                            <td>#00100</td>
                            <td>Carlos Ruiz</td>
                            <td>2025-11-07</td>
                            <td>$250.00</td>
                            <td><span class="status sent">Enviado</span></td>
                            <td><a href="#">Ver</a></td>
                        </tr>
                        <tr>
                            <td>#00099</td>
                            <td>Elena M.</td>
                            <td>2025-11-07</td>
                            <td>$1,200.00</td>
                            <td><span class="status completed">Completado</span></td>
                            <td><a href="#">Ver</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section id="custom-requests">
            <h2>Solicitudes de Personalización Pendientes</h2>
            <p>Sección para gestionar las solicitudes del formulario 'Personaliza tu Producto'.</p>
            <ul>
                <li>**ID: P001**: Playera, Imagen de "Jimin", Nota: "Letra pequeña en la manga". - <a href="#">Ver/Aprobar</a></li>
                <li>**ID: P002**: Termo, Imagen subida, Nota: "Fondo morado y nombre 'Andrea'". - <a href="#">Ver/Aprobar</a></li>
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
                        <tr>
                            <td>1</td>
                            <td>Taza Jin</td>
                            <td>$150.00</td>
                            <td class="stock-value">50</td>
                            <td>
                                <button class="stock-btn decrease-stock">-</button>
                                <button class="stock-btn increase-stock">+</button>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Camisa Golden</td>
                            <td>$250.00</td>
                            <td class="stock-value">25</td>
                            <td>
                                <button class="stock-btn decrease-stock">-</button>
                                <button class="stock-btn increase-stock">+</button>
                            </td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>Suéter Navideño BTS</td>
                            <td>$300.00</td>
                            <td class="stock-value">10</td>
                            <td>
                                <button class="stock-btn decrease-stock">-</button>
                                <button class="stock-btn increase-stock">+</button>
                            </td>
                        </tr>
                        </tbody>
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

</body>
</html>