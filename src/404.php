<?php
require('core/Main.php');
$main = new Main();
$mainTemplate = $main->template;
$database = $main->db;
$functions = $main->functions;

$themeFile = "site.404";
$mainTemplate->setVariable("pageName", "404");

$page = new TemplateHandler($themeFile);

$mainTemplate->setVariable("content", $page->getTemplate());
$mainTemplate->render();