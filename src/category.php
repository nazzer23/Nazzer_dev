<?php
require('core/Main.php');
$main = new Main();
$mainTemplate = $main->template;
$database = $main->db;
$functions = $main->functions;

$articleData = null;

// Check to see if the article exists
if(isset($_GET['category'])) {
    // Query to check
    $category = str_replace("-", " ", $_GET['category']);
    $query = $database->executeQuery("SELECT * FROM categories WHERE CategoryName LIKE '{$category}'");
    if($query->num_rows <= 0) {
        $themeFile = "site.404";
        $mainTemplate->setVariable("pageName", "404");
    } else {
        $themeFile = "site.categories";
        $articleData = $query->fetch_array();
        $mainTemplate->setVariable("pageName", $articleData['CategoryName']);
    }
} else {
    $themeFile = "site.404";
    $mainTemplate->setVariable("pageName", "404");
}

$page = new TemplateHandler($themeFile);



$mainTemplate->setVariable("content", $page->getTemplate());
$mainTemplate->render();