<?php
require('core/Main.php');
$main = new Main();
$mainTemplate = $main->template;
$database = $main->db;
$functions = $main->functions;

$articleData = null;

// Check to see if the article exists
if(isset($_GET['year']) && isset($_GET['month']) && isset($_GET['day']) && isset($_GET['articleurl'])) {
    // Query to check
    $query = $database->executeQuery("SELECT * FROM articles INNER JOIN authors a on articles.AuthorID = a.AuthorID INNER JOIN articles_category ac on articles.ArticleID = ac.ArticleID INNER JOIN categories c on ac.CategoryID = c.CategoryID WHERE ArticleDate = '{$_GET['year']}-{$_GET['month']}-{$_GET['day']}' AND ArticleURL LIKE '{$_GET['articleurl']}'");
    if($query->num_rows <= 0) {
        $themeFile = "site.404";
        $mainTemplate->setVariable("pageName", "404");
    } else {
        $themeFile = "site.article";
        $articleData = $query->fetch_array();
        $mainTemplate->setVariable("pageName", $articleData['ArticleTitle']);
    }

} else {
    $themeFile = "site.404";
    $mainTemplate->setVariable("pageName", "404");
}

$page = new TemplateHandler($themeFile);

if($articleData != null) {
    $articleDate = strtotime($articleData['ArticleDate']);
    $page->setVariable("articleTitle", $articleData['ArticleTitle']);
    $page->setVariable("author","{$articleData['AuthorFirstName']} {$articleData['AuthorLastName']}");
    $page->setVariable("date", date('F jS Y',$articleDate));
    $page->setVariable("category", $articleData['CategoryName']);
    $page->setVariable("content", nl2br($articleData['ArticleContent']));
}

$mainTemplate->setVariable("content", $page->getTemplate());
$mainTemplate->render();