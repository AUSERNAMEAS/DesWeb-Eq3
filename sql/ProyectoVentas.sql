CREATE DATABASE Ventas

create table cliente(
id_cliente int primary key identity(1,1) not null,
nombre nvarchar(20),
apellido char(20),
correo char(40),
telefono char(10),
direccion char(40),
ciudad char(15),
codigo_postal char(7),
pais char(15),
fecha_Compra date)

select * from cliente

--insert into cliente(nombre,correo) values('Phoenix','pelos@gmail.com')

--update cliente set fecha_Compra= GETDATE() where id_cliente = 1;

--alter table cliente alter column telefono char(10)

truncate table cliente

delete from cliente where id_cliente = 1


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

select * from producto

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