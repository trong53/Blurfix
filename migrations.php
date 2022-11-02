<?php
try {

$connect_bd = new PDO ("mysql:host=localhost;dbname=projet_blurfix;charset=utf8", 'root', 'Juncceun');  // Eviter tous les espaces dans sql:
$connect_bd -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql_bd = "CREATE TABLE IF NOT EXISTS movie 
    (
        id INT(4) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        title TEXT NOT NULL,
        description TEXT,
        picture TEXT NOT NULL,
        exclusivity VARCHAR(50),
        favorite VARCHAR(50),
        views_number INT(11),
        created_at DATE NOT NULL
    ) ";
$connect_bd->exec($sql_bd);

$sql_bd = "CREATE TABLE IF NOT EXISTS category_movie 
    (
        id_movie INT(4) NOT NULL,
        id_category INT(4) NOT NULL
    ) ";
$connect_bd->exec($sql_bd);

$sql_bd = "CREATE TABLE IF NOT EXISTS category 
    (
        id INT(4) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        name TEXT NOT NULL
    ) ";
$connect_bd->exec($sql_bd);

$insert_bd = "INSERT INTO category_movie (id_movie, id_category) 
                VALUES (1, 1)";
$connect_bd->exec($insert_bd);
$insert_bd = "INSERT INTO category_movie (id_movie, id_category) 
                VALUES (1, 2)";
$connect_bd->exec($insert_bd);

$insert_bd = "INSERT INTO category_movie (id_movie, id_category) 
                VALUES (2, 1)";
$connect_bd->exec($insert_bd);
$insert_bd = "INSERT INTO category_movie (id_movie, id_category) 
                VALUES (3, 1)";
$connect_bd->exec($insert_bd);
$insert_bd = "INSERT INTO category_movie (id_movie, id_category) 
                VALUES (3, 3)";
$connect_bd->exec($insert_bd);
$insert_bd = "INSERT INTO category_movie (id_movie, id_category) 
                VALUES (4, 1)";
$connect_bd->exec($insert_bd);
$insert_bd = "INSERT INTO category_movie (id_movie, id_category) 
                VALUES (4, 2)";
$connect_bd->exec($insert_bd);

}
catch (PDOException $e) {
    echo $e -> getMessage();
}
