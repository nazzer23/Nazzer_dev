<?php
require('core/Main.php');
$main = new Main();
$mainTemplate = $main->template;
$database = $main->db;
$functions = $main->functions;

$themeFile = "site.404";
$page = new TemplateHandler($themeFile);
$page->setVariable("siteName", Configuration::siteName);

$mainTemplate->setVariable("content", $page->getTemplate());
$mainTemplate->render();
