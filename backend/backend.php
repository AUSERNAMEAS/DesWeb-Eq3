<?php
// productos.php
header('Content-Type: application/json; charset=utf-8');

$productos = [
    [ 'id' => 1, 'name' => 'Taza Jin', 'price' => 150, 'image' => 'imagenes/taza.jpg' ],
    [ 'id' => 2, 'name' => 'Camisa Golden', 'price' => 250, 'image' => 'imagenes/camisanegra.jpg' ],
    [ 'id' => 3, 'name' => 'Photocaras', 'price' => 30, 'image' => 'imagenes/photocaras.jpg' ],
    [ 'id' => 4, 'name' => 'Camisa Indigo', 'price' => 200, 'image' => 'imagenes/camisaazul.jpg' ],
    [ 'id' => 5, 'name' => 'Suéter Navideño BTS', 'price' => 300, 'image' => 'imagenes/sudadera.jpg' ],
    [ 'id' => 6, 'name' => 'Llavero', 'price' => 50, 'image' => 'imagenes/llavero.jpg' ],
    [ 'id' => 7, 'name' => 'Termo I AM STILL', 'price' => 195, 'image' => 'imagenes/termo.jpg' ],
    [ 'id' => 8, 'name' => 'Frazada Viajera', 'price' => 140, 'image' => 'imagenes/frazada.jpg' ],
    [ 'id' => 9, 'name' => 'Sudadera Jungkook Tattoo', 'price' => 560, 'image' => 'imagenes/sudaderablanca1.jpg' ],
    [ 'id' => 10, 'name' => 'Sudadera Jimin Tattoo', 'price' => 400, 'image' => 'imagenes/sudaderablanca2.jpg' ]
];

echo json_encode($productos);
