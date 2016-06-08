<?php

use maw\core\Autoloader,
    maw\core\DataProcessor,
    maw\core\Router;

require_once 'core/Autoloader.php';

$autoloader = new Autoloader('maw');
$autoloader->register();

$router = new Router();

$pages = DataProcessor::getJSONAsArray(PAGES);
foreach ($pages as $page) {
    $router->add($page->urlSegment, $page->pageType, $page->id);
}
$router->route();
