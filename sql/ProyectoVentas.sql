CREATE DATABASE Ventas

create table cliente(
id_cliente int primary key identity(1,1) not null,
nombre varchar(40) not null,
contrasenia_hash varchar(250) not null,
apellido char(20),
correo char(40) unique not null,
telefono char(10),
direccion char(40),
ciudad char(15),
codigo_postal char(7),
pais char(15),
fecha_Compra date,
rol varchar(20) DEFAULT 'cliente')

DROP DATABASE Ventas

select * from cliente

--insert into cliente(nombre,correo) values('Phoenix','pelos@gmail.com')

--update cliente set fecha_Compra= GETDATE() where id_cliente = 1;

alter table cliente alter column nombre varchar(40) not null

insert into cliente(nombre,correo,contrasenia_hash,rol) values ('cu','pelos@gmail.com', 'pelos1234', 'admin');

delete from cliente where id_cliente = 14


update cliente set rol = 'admin' where correo = 'pelos@gmail.com  '   

SELECT correo, contrasenia_hash, rol 
FROM cliente 
WHERE correo = 'pelos@gmail.com';


truncate table cliente

delete from cliente where id_cliente = 1

--añadir campo imagenes
create table producto(
id_producto int primary key identity(1,1) not null,
nombre varchar(30),
descripcion varchar(60),
stock int,
categoria varchar(20),
peso_kg varchar(10),
estado_producto varchar(20)
)

alter table producto add precio_unitario money
ALTER TABLE producto ADD imagen VARCHAR(100);

select * from producto
DELETE FROM producto

-- INSERT INTO: Consulta para insertar los 10 productos con todos los campos.
INSERT INTO producto (nombre, descripcion, stock, categoria, peso_kg, estado_producto, precio_unitario, imagen) 
VALUES
('Taza Jin', 'Contenedor de bebida temático.', 8, 'termo/taza', '0.4kg', 'Activo', 150.00, 'imagenes/taza.jpg'),
('Camisa Golden', 'Prenda de vestir de colección.', 7, 'prenda', '0.7kg', 'Activo', 250.00, 'imagenes/camisanegra.jpg'),
('Photocaras', 'Accesorio coleccionable.', 10, 'freebe', '0.05kg', 'Activo', 30.00, 'imagenes/photocaras.jpg'),
('Camisa Indigo', 'Prenda de vestir de colección.', 6, 'prenda', '0.7kg', 'Activo', 200.00, 'imagenes/camisaazul.jpg'),
('Suéter Navideño BTS', 'Prenda de vestir de colección.', 5, 'prenda', '0.7kg', 'Activo', 300.00, 'imagenes/sudadera.jpg'),
('Llavero', 'Accesorio coleccionable.', 9, 'freebe', '0.05kg', 'Activo', 50.00, 'imagenes/llavero.jpg'),
('Termo I AM STILL', 'Contenedor de bebida temático.', 8, 'termo/taza', '0.4kg', 'Activo', 195.00, 'imagenes/termo.jpg'),
('Frazada Viajera', 'Prenda de vestir o manta de colección.', 6, 'prenda', '1.0kg', 'Activo', 140.00, 'imagenes/Frazada.jpg'),
('Sudadera Jungkook Tattoo', 'Prenda de vestir de colección.', 7, 'prenda', '0.7kg', 'Activo', 560.00, 'imagenes/sudaderablanca1.jpg'),
('Sudadera Jimin Tattoo', 'Prenda de vestir de colección.', 5, 'prenda', '0.7kg', 'Activo', 400.00, 'imagenes/sudaderablanca2.jpg');

select id_producto,nombre,precio_unitario,imagen from producto

-- *Ajusta la descripción, stock y categoría según necesites*

create table pedido(
id_pedido int primary key identity(1,1) not null,
id_cliente int,
fecha_pedido date,
estado_pedido varchar(20),
metodo_pago varchar(20),
total money,
 foreign key(id_cliente) references cliente(id_cliente)

)

select * from pedido
delete from pedido


--insert into pedido(id_cliente,estado_pedido) values(1,'detenido')

truncate table pedido

create table detalle_pedido(
id_pedido int,
id_producto int,
id_cliente int,
cantidad int,
precio_unitario money,
subtotal money

foreign key (id_pedido) references pedido(id_pedido),
foreign key (id_cliente) references cliente(id_cliente),
foreign key (id_producto) references producto(id_producto) 




)

select * from detalle_pedido
delete from detalle_pedido


create table envio(
id_envio int primary key identity(1,1) not null,
id_pedido int,
direccion_envio char(40),
empresa_envio char(20),
costo_envio money,
numero_guia varchar(30),
fecha_envio date,
fecha_entrega date,
estado_envio varchar(30)
foreign key (id_pedido) references pedido(id_pedido)

)

select * from envio

delete from envio

SELECT SUM(total) AS MontoMes FROM pedido 
                        WHERE MONTH(fecha_pedido) = MONTH(GETDATE()) 
                        AND YEAR(fecha_pedido) = YEAR(GETDATE())

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

sELECT TOP 5
            p.id_pedido,
            CONVERT(VARCHAR(10), p.fecha_pedido, 120) AS FechaPedido,
            p.total,
            p.estado_pedido,
            c.nombre AS NombreCliente
        FROM pedido p
        JOIN cliente c ON p.id_cliente = c.id_cliente
        ORDER BY p.fecha_pedido DESC

CREATE TABLE solicitud_personalizacion (
    id_solicitud INT PRIMARY KEY IDENTITY(1,1) NOT NULL,
    tipo_producto VARCHAR(50) NOT NULL,
    instrucciones TEXT,
    imagen_url VARCHAR(250),
    fecha_solicitud DATETIME DEFAULT GETDATE(),
    estado VARCHAR(20) DEFAULT 'Pendiente' -- (Pendiente, Revisado, Aprobado)
);
ALTER TABLE solicitud_personalizacion
ADD imagen_nombre VARCHAR(150);
select * from solicitud_personalizacion