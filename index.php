<?php
require_once './vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('./views');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
]);

try {
    include 'config.php';

    $promote_movie = $connect->prepare("SELECT * FROM movie ORDER by created_at DESC");    // SELECT * FROM movie WHERE created_at = (SELECT MAX(created_at) FROM movie)");

    $promote_movie->execute();
    $promote_movie = $promote_movie->fetchAll(PDO::FETCH_ASSOC);
    $promote_movie[0]['picture']= substr_replace($promote_movie[0]['picture'], '../', 0, 0);

} catch (PDOException $e) {
    echo $e -> getMessage();
}

// Coloration du layout
$coloration = [
    'background' => 'text-white bg-[#13121b]',
    'hover' => null
];
$coloration12 = [
    'background' => 'text-[#999]',
    'hover' => 'hover:bg-[#13121b] hover:text-white'
];

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

//Trier la catégorie de Streaming
$categories = ['Tous', 'Dessins animés', 'Suspens', 'Autres'];

if (!empty($_GET['filter']) && $_GET['filter'] != 'Tous') {
    $movies = $connect->prepare("SELECT * FROM movie AS m, category AS c, category_movie AS cm 
                        WHERE m.id = cm.id_movie AND c.id = cm.id_category AND c.name = '".$_GET['filter']."'");
    $movies->execute();
    $movies = $movies->fetchAll(PDO::FETCH_ASSOC);

    include 'arrange_array.php';
    $categories = arrange_array($categories, $_GET['filter']);

} else {
    $movies = $connect->prepare("SELECT * FROM movie");
    $movies->execute();
    $movies = $movies->fetchAll(PDO::FETCH_ASSOC);
    
    // $movies = array_unique($movies, SORT_REGULAR);   // c'est important SORT_REGULAR sinon il ne sais pas comment afficher après avoir supprimé les éléments répétés
    /*include 'supprime_elementDouble.php';
    $movies = supprime_elementDouble($movies);*/
}

// Rechercher un filme
if (!empty($_GET['search'])):    // chaque fois on recharge la page, la variable GET est reset. C'est bonne chose.
    $search = trim ($_GET['search']);
    require_once 'config.php';
    $movies = $connect->prepare("SELECT * FROM movie WHERE movie.title LIKE '%".$search."%'");
    $movies->execute();
    $movies = $movies->fetchAll(PDO::FETCH_ASSOC);
endif;

$pagination = [
    'previous' => 'index.php?page=1',
    'next' => 'index.php?page=2',
    'total' => 80,
    'from' => 1,
    'to' => 4,
];
$pagination['to'] = count($movies);
$pagination['total'] = count($movies);

$template = $twig->load('home.html.twig');
echo $template->render([
    'coloration0' => $coloration,
    'coloration1' => $coloration12,
    'coloration2' => $coloration12,
    'movies' => $movies,
    'categories' => $categories,
    'promote_movie' => $promote_movie,
    'pagination' => $pagination
]);

