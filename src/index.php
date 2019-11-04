<?php
require('core/Main.php');
$main = new Main();
$mainTemplate = $main->template;
$database = $main->db;
$functions = $main->functions;

$themeFile = "site.home";

$page = new TemplateHandler($themeFile);
$page->setVariable("articles", null);

// Right Bar
generateSidebarContent($page);
// Center Column
populateArticles($page);

$mainTemplate->setVariable("pageName", "Home");
$mainTemplate->setVariable("content", $page->getTemplate());
$mainTemplate->render();

function generateSidebarContent($page) {
    global $database, $functions;

    // Generate Categories
    $category = new TemplateHandler("site.home.sidebar");
    $category->setVariable("panelID", 0);
    $category->setVariable("panelName", "Categories");

    $query = "SELECT * FROM categories ORDER BY CategoryID DESC";
    $query = $database->executeQuery($query);
    while($row = $query->fetch_array()) {
        $item = new TemplateHandler("site.home.sidebar.item");
        $item->setVariable("itemName", $row['CategoryName']);
        $item->setVariable("itemDesc", "See all articles under this category.");
        $item->setVariable("itemBio",$row['CategoryDescription']);
        $item->setVariable("itemURL", "/category/".$functions->urlClean($row['CategoryName']));
        $category->appendVariable("panelItems", $item->getTemplate());
    }
    $page->appendVariable("sidebarContent", $category->getTemplate());

    // Generate Recent Articles
    $recentArticles = new TemplateHandler("site.home.sidebar");
    $recentArticles->setVariable("panelID", 1);
    $recentArticles->setVariable("panelName", "Recent Articles");

    $query = "SELECT articles.*, a.AuthorFirstName, a.AuthorLastName FROM articles INNER JOIN authors a on articles.AuthorID = a.AuthorID ORDER BY ArticleID DESC LIMIT 4";
    $query = $database->executeQuery($query);
    while($row = $query->fetch_array()) {
        $articleDate = strtotime($row['ArticleDate']);
        $item = new TemplateHandler("site.home.sidebar.item");
        $item->setVariable("itemName", $row['ArticleTitle']);
        $item->setVariable("itemDesc", "Read More about &quot;{$row['ArticleTitle']}&quot;");
        $item->setVariable("itemBio", "");

        $year = date('Y',$articleDate);
        $postMonth = date('m',$articleDate);
        $postDay = date('d',$articleDate);
        $articleURLTitle = $row['ArticleURL'];

        $item->setVariable("itemURL","/article/{$year}/{$postMonth}/{$postDay}/{$articleURLTitle}/");

        $recentArticles->appendVariable("panelItems", $item->getTemplate());
    }
    $page->appendVariable("sidebarContent", $recentArticles->getTemplate());

}
function populateArticles($page) {
    global $database, $functions;
    $mainQuery = "SELECT articles.*, CategoryName, AuthorLastName, AuthorFirstName, a.AuthorID FROM articles INNER JOIN authors a on articles.AuthorID = a.AuthorID INNER JOIN articles_category ac on articles.ArticleID = ac.ArticleID INNER JOIN categories c2 on ac.CategoryID = c2.CategoryID ORDER BY ArticleID DESC";

    $articlesToLimitBy = 4;
    $currentPage = 1;
    if(isset($_GET['pageID'])) {
        if(is_numeric($_GET['pageID'])) {
            $currentPage = floor($_GET['pageID']);
        }
    }

    $totalArticleCount = $database->getNumberOfRows($mainQuery);
    $maxPages = ceil($totalArticleCount / $articlesToLimitBy);
    if($currentPage > $maxPages) {
        header('Location: /');
    }
    $articleStart = ($currentPage-1) * $articlesToLimitBy;

    $articleQuery = $mainQuery . " LIMIT {$articleStart}, {$articlesToLimitBy}";
    $articleQuery = $database->executeQuery($articleQuery);

    if($articleQuery->num_rows <= 0) {
        if($currentPage == 1) {
            $article = new TemplateHandler("site.home.article");
            $page->appendVariable("articles", $article->getTemplate());
        }
    } else {
        while($articleData = $articleQuery->fetch_array()) {
            $article = $functions->generateArticleTemplate($articleData);
            $page->appendVariable("articles", $article->getTemplate());
        }
    }

    $previousPage = $currentPage - 1;
    $nextPage = $currentPage + 1;
    if($currentPage > 1) {
        $page->appendVariable("currentPage", "<a href='/?pageID={$previousPage}'>Previous Page</a><br>");
    }
    if($currentPage < $maxPages) {
        $page->appendVariable("currentPage", "<a href='/?pageID={$nextPage}'>Next Page</a><br>");
    }

    $page->appendVariable("currentPage", "Page {$currentPage} of {$maxPages}");

}