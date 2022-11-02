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
    $movies = $connect->prepare("SELECT * FROM movie WHERE favorite = 'true' ORDER BY title");
    $movies->execute();
    $movies = $movies->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($_GET['filter_favorite']) && $_GET['filter_favorite'] == 'Vu') {
        $movies = $connect->prepare("SELECT * FROM movie WHERE favorite = 'true' ORDER BY views_number DESC");
        $movies->execute();
        $movies = $movies->fetchAll(PDO::FETCH_ASSOC);
        
        include '../arrange_array.php';
        $categories = arrange_array($categories, $_GET['filter_favorite']);

    } elseif (!empty($_GET['filter_favorite']) && $_GET['filter_favorite'] == 'Date') {
        $movies = $connect->prepare("SELECT * FROM movie WHERE favorite = 'true' ORDER BY created_at DESC");
        $movies->execute();
        $movies = $movies->fetchAll(PDO::FETCH_ASSOC);

        include '../arrange_array.php';
        $categories = arrange_array($categories, $_GET['filter_favorite']);
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

$template = $twig->load('favorite.html.twig');
echo $template->render([
    'coloration2' => $coloration,
    'coloration0' => $coloration12,
    'coloration1' => $coloration12,
    'movies' => $movies,
    'categories' => $categories
]);
