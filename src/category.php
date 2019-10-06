<?php
require('core/Main.php');
$main = new Main();
$mainTemplate = $main->template;
$database = $main->db;
$functions = $main->functions;

$categoryData = null;

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
        $categoryData = $query->fetch_array();
        $mainTemplate->setVariable("pageName", $categoryData['CategoryName']);
    }
} else {
    $themeFile = "site.404";
    $mainTemplate->setVariable("pageName", "404");
}

$query = $database->executeQuery("SELECT * FROM articles INNER JOIN articles_category ac on articles.ArticleID = ac.ArticleID INNER JOIN categories c2 on ac.CategoryID = c2.CategoryID INNER JOIN authors a on articles.AuthorID = a.AuthorID WHERE CategoryName = '{$categoryData['CategoryName']}' ORDER BY id DESC");
$page = new TemplateHandler($themeFile);
if($categoryData != null) {
// This aspect of code, determines if any articles can be found that are linked to the category.
    if ($query->num_rows <= 0) {
        $themeFile = "site.customerror";
        $page = new TemplateHandler($themeFile);

        $page->setVariable("title", "Oh No!");
        $page->setVariable("message", "There are currently no articles posted under this category. Please check back in the future!");

    } else {
        // Found Articles.
        $page->setVariable("categoryName", $categoryData['CategoryName']);
        $page->setVariable("articlesUnderCategory", null);
        while ($articleData = $query->fetch_array()) {
            $article = $functions->generateArticleTemplate($articleData);
            $page->appendVariable("articlesUnderCategory", $article->getTemplate());
        }

    }
}
$mainTemplate->setVariable("content", $page->getTemplate());
$mainTemplate->render();