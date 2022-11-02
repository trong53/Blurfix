<?php

require_once '../vendor/autoload.php';

require_once '../loadTwig.php';

$categories = ['Titre', 'Vu', 'Date'];
try {
    include '../config.php';

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
    }

    // Filtrer par catégories
    $movies = $connect->prepare("SELECT * FROM movie ORDER BY title");
    $movies->execute();
    $movies = $movies->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($_GET['filter_category']) && $_GET['filter_category'] == 'Vu') {
        $movies = $connect->prepare("SELECT * FROM movie ORDER BY views_number DESC");
        $movies->execute();
        $movies = $movies->fetchAll(PDO::FETCH_ASSOC);
        
        include '../arrange_array.php';
        $categories = arrange_array($categories, $_GET['filter_category']);

    } elseif (!empty($_GET['filter_category']) && $_GET['filter_category'] == 'Date') {
        $movies = $connect->prepare("SELECT * FROM movie ORDER BY created_at DESC");
        $movies->execute();
        $movies = $movies->fetchAll(PDO::FETCH_ASSOC);

        include '../arrange_array.php';
        $categories = arrange_array($categories, $_GET['filter_category']);
    }

} catch (PDOException $e) {
        echo $e -> getMessage();
}

$coloration = [
    'background' => 'text-white bg-[#13121b]',
    'hover' => null
];
$coloration12 = [
    'background' => 'text-[#999]',
    'hover' => 'hover:bg-[#13121b] hover:text-white'
];

$template = $twig->load('trending.html.twig');
echo $template->render([
    'coloration0' => $coloration12,
    'coloration1' => $coloration,
    'coloration2' => $coloration12,
    'movies' => $movies,
    'categories' => $categories
]);
