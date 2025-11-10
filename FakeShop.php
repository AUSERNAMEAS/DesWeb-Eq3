<!DOCTYPE html>
<?php
session_start();
// Inicia la sesión para verificar si el usuario ha iniciado sesión,osea que muestre el nombre oo correo
?>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fake Shop - Productos Personalizados</title>
    <link rel="stylesheet" href="estilos/main.css">
</head>
<body>
    <!-- dependiendo ,si tiene sesion muestra el nombre y en la esquina cerrar seesion,si no no cambia nada -->

    <header>
    <img src="imagenes/logo.jpg" alt="Logo Fake Shop" style="height:48px;vertical-align:middle;">

    <?php if (isset($_SESSION['usuario'])): ?> 
        <span style="margin-left:20px;">👋 Hola, <?php echo $_SESSION['usuario']; ?></span>
        <a href="backend/cerrarSesion.php" style="margin-left:12px;">Cerrar sesión</a>
    <?php else: ?>
        <a href="login.html" style="margin-left:12px;">Iniciar sesión</a>
    <?php endif; ?> 25
</header>


    <nav>
        <a href="#about">Inicio</a>
        <a href="#products">Productos Ya Hechos</a>
        <a href="#custom">Personaliza el Tuyo</a>
        <a href="#cart">Carrito de Compras</a>
    </nav>

    <div class="container">

        <section id="about">
            <h2>Acerca de Nosotros</h2>
            <p>En <strong>Fake Shop</strong>, transformamos tus ideas en productos únicos. Nos especializamos en la creación de artículos personalizados como **tazas, termos, sudaderas y camisas**, utilizando materiales de alta calidad y diseños vibrantes. Desde un regalo especial hasta un pedido para tu evento, ¡hacemos que cada pieza sea tan única como tú!</p>
        </section>

        <section id="products">
            <h2>Productos Ya Hechos</h2>
            <p>Explora nuestra colección de diseños pre-diseñados. ¡Listos para ser tuyos!</p>
            <div id="product-grid" class="product-grid">
                </div>
            <button id="load-more-btn">Ver más productos</button>
        </section>

        <section id="custom">
            <h2>Personaliza tu Producto</h2>
            <p>¡Sube la imagen que quieras y dinos qué producto deseas! Nuestro equipo se encargará del resto.</p>
            <form class="custom-form">
                <label for="product-type">Tipo de Producto:</label>
                <input type="text" id="product-type" placeholder="Ej. Taza, Playera, Sudadera, Termo" required>

                <label for="custom-image">Sube tu Imagen:</label>
                <input type="file" id="custom-image" accept="image/*" required>

                <label for="instructions">Instrucciones Adicionales:</label>
                <textarea id="instructions" rows="4" placeholder="Ej. 'Quiero que el diseño vaya en la espalda'"></textarea>

                <button type="button" id="btn-personalized">Enviar Solicitud de Personalización</button>
            </form>
        </section>

        <section id="cart">
            <h2>Carrito de Compras</h2>
            <div id="cart-items">
                <p>Tu carrito está vacío.</p>
            </div>
            <div class="cart-details">
                <p>Subtotal: <span id="cart-subtotal">$0.00 MXN</span></p>
                <p>Envío: <span id="cart-shipping">$0.00 MXN</span></p>
                <p><strong>Total: <span id="cart-total">$0.00 MXN</span></strong></p>
            </div>
            <button id="checkout-btn">Finalizar Compra</button>
        </section>

    </div>

    <script src="scripts/diseño.js"></script>

</body>
</html>


