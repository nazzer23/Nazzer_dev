<?php
require('core/Main.php');
$main = new Main();
$mainTemplate = $main->template;
$database = $main->db;
$functions = $main->functions;

$themeFile = "site.home";

$page = new TemplateHandler($themeFile);
$page->setVariable("siteName", Configuration::siteName);
$page->setVariable("articles", null);

$articleQuery = "SELECT articles.*, CategoryName, AuthorLastName, AuthorFirstName, a.AuthorID FROM articles INNER JOIN authors a on articles.AuthorID = a.AuthorID INNER JOIN articles_category ac on articles.ArticleID = ac.ArticleID INNER JOIN categories c2 on ac.CategoryID = c2.CategoryID";
$articleQuery = $database->executeQuery($articleQuery);

while($articleData = $articleQuery->fetch_array()) {
    $articleDate = strtotime($articleData['ArticleDate']);
    $article = new TemplateHandler("site.home.article");
    $article->setVariable("articleTitle",$articleData['ArticleTitle']);
    $article->setVariable("authorName","{$articleData['AuthorFirstName']} {$articleData['AuthorLastName']}");
    $article->setVariable("postedDate", date('F jS Y',$articleDate));
    $article->setVariable("categoryName",$articleData['CategoryName']);
    $articleContent = (strlen($articleData['ArticleContent']) >= 254 ? substr($articleData['ArticleContent'], 0, 254) . "..." : $articleData['ArticleContent']);
    $article->setVariable("articleContent",$articleContent);

    $year = date('Y',$articleDate);
    $postMonth = date('m',$articleDate);
    $postDay = date('d',$articleDate);
    $articleURLTitle = $functions->urlClean($articleData['ArticleTitle']);

    $article->setVariable("articleURL","/article/{$year}/{$postMonth}/{$postDay}/{$articleURLTitle}/");
    $page->appendVariable("articles", $article->getTemplate());
}

$mainTemplate->setVariable("pageName", "Home");
$mainTemplate->setVariable("content", $page->getTemplate());
$mainTemplate->render();