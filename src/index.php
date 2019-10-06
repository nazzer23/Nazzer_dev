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
    $recentArticles->setVariable("panelName", "Recent Articles");

    $query = "SELECT articles.*, a.AuthorFirstName, a.AuthorLastName FROM articles INNER JOIN authors a on articles.AuthorID = a.AuthorID ORDER BY ArticleID DESC LIMIT 6";
    $query = $database->executeQuery($query);
    while($row = $query->fetch_array()) {
        $articleDate = strtotime($row['ArticleDate']);
        $item = new TemplateHandler("site.home.sidebar.item");
        $item->setVariable("itemName", $row['ArticleTitle']);
        $item->setVariable("itemDesc", "Read More about " . $row['ArticleTitle']);
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
    $articleQuery = "SELECT articles.*, CategoryName, AuthorLastName, AuthorFirstName, a.AuthorID FROM articles INNER JOIN authors a on articles.AuthorID = a.AuthorID INNER JOIN articles_category ac on articles.ArticleID = ac.ArticleID INNER JOIN categories c2 on ac.CategoryID = c2.CategoryID ORDER BY ArticleID DESC";
    $articleQuery = $database->executeQuery($articleQuery);

    while($articleData = $articleQuery->fetch_array()) {
        $articleDate = strtotime($articleData['ArticleDate']);
        $article = new TemplateHandler("site.home.article");
        $article->setVariable("articleTitle", $articleData['ArticleTitle']);
        $article->setVariable("authorName","{$articleData['AuthorFirstName']} {$articleData['AuthorLastName']}");
        $article->setVariable("postedDate", date('F jS Y',$articleDate));
        $article->setVariable("categoryName",$articleData['CategoryName']);
        $articleContent = (strlen($articleData['ArticleContent']) >= 254 ? substr($articleData['ArticleContent'], 0, 254) . "..." : $articleData['ArticleContent']);
        $article->setVariable("articleContent",$articleContent);

        $year = date('Y',$articleDate);
        $postMonth = date('m',$articleDate);
        $postDay = date('d',$articleDate);
        $articleURLTitle = $articleData['ArticleURL'];

        $article->setVariable("articleURL","/article/{$year}/{$postMonth}/{$postDay}/{$articleURLTitle}/");
        $page->appendVariable("articles", $article->getTemplate());
    }
}