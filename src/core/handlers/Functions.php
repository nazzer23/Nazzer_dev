<?php

class Functions
{
    private $global;

    public function __construct($global)
    {
        $this->global = $global;
    }

    public function urlClean($string)
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        return strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', $string)); // Removes special chars.
    }

    public function generateArticleTemplate($articleData) {
        $articleDate = strtotime($articleData['ArticleDate']);
        $article = new TemplateHandler("site.home.article");
        $article->setVariable("articleID", $articleData['ArticleID']);
        $article->setVariable("articleTitle", $articleData['ArticleTitle']);
        $article->setVariable("authorName","{$articleData['AuthorFirstName']} {$articleData['AuthorLastName']}");
        $article->setVariable("postedDate", date('F jS Y',$articleDate));
        $article->setVariable("categoryName",$articleData['CategoryName']);
        $articleContent = (strlen(trim($articleData['ArticleContent'])) >= 254 ? trim(substr($articleData['ArticleContent'], 0, 254)) . "..." : trim($articleData['ArticleContent']));
        $article->setVariable("articleContent",$articleContent);

        $year = date('Y',$articleDate);
        $postMonth = date('m',$articleDate);
        $postDay = date('d',$articleDate);
        $articleURLTitle = $articleData['ArticleURL'];

        $article->setVariable("articleURL","/article/{$year}/{$postMonth}/{$postDay}/{$articleURLTitle}/");
        return $article;
    }
}