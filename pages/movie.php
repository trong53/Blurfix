<?php
session_start();

require_once '../vendor/autoload.php';
$loader = new \Twig\Loader\FilesystemLoader('../views');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
]);
require_once '../config.php';

        // Mettre ou Enlever le favori
if (!empty($_GET['favori'])) {
    $favori = (INT)$_GET['favori'];
    
    $favorite = $connect->prepare("SELECT favorite FROM movie WHERE id =".$favori);
    $favorite->execute();
    $favorite = $favorite->fetchAll(PDO::FETCH_ASSOC);
    
    if ($favorite[0]['favorite'] == 'true') {
        $favorite_update = $connect->prepare("UPDATE movie SET favorite='false' WHERE id=".$favori);
    } elseif ($favorite[0]['favorite'] == 'false') {
        $favorite_update = $connect->prepare("UPDATE movie SET favorite='true' WHERE id=".$favori);
    }
    $favorite_update->execute();

    // Résultat du tableau movie
    $movie = $connect->prepare("SELECT * FROM movie WHERE id=".$favori);
    $movie->execute();
    $movie = $movie->fetchAll(PDO::FETCH_ASSOC);

    // Afficher la catégorie de la video en cours de la lecture
    $category = $connect->prepare("SELECT c.name AS category 
            FROM movie AS m, category AS c, category_movie AS cm
            WHERE m.id = cm.id_movie  AND c.id = cm.id_category AND m.id = ?");
    $category->bindParam(1, $favori);     
    $category->execute();
    $category = $category->fetchAll(PDO::FETCH_ASSOC);

    $catr='';
    for ($i = 0; $i < count($category); $i++) :
        $catr.=$category[$i]['category'].' et ';
    endfor;
    $catr = rtrim($catr, ' et ');
    $movie[0]['category'] = $catr; 
}

// Compteur de 'vu'
if (!empty($_GET['id'])) {

    $lecture_movie = $_GET['id'];

    $viewed = $connect->prepare("UPDATE movie SET views_number=views_number+1 WHERE id=?");
    $viewed->bindParam(1,$lecture_movie);
    $viewed->execute();

    // Résultat du tableau movie
    $movie = $connect->prepare("SELECT * FROM movie WHERE id=".$lecture_movie);
    $movie->execute();
    $movie = $movie->fetchAll(PDO::FETCH_ASSOC);

    // Afficher la catégorie de la video en cours de la lecture
    $category = $connect->prepare("SELECT c.name AS category 
                    FROM movie AS m, category AS c, category_movie AS cm
                    WHERE m.id = cm.id_movie  AND c.id = cm.id_category AND m.id=".$lecture_movie);
    $category->execute();
    $category = $category->fetchAll(PDO::FETCH_ASSOC);

    $catr='';
    for ($i = 0; $i < count($category); $i++) :
        $catr.=$category[$i]['category'].' et ';
    endfor;
    $catr = rtrim($catr, ' et ');
    $movie[0]['category'] = $catr;
}

$coloration = [
    'background' => 'text-white',
    'hover' => 'hover:bg-[#13121b] hover:text-white'
];

$template = $twig->load('movie.html.twig');
echo $template->render([
    'movie' => $movie[0],
    'coloration0' => $coloration,
    'coloration1' => $coloration,
    'coloration2' => $coloration
]);