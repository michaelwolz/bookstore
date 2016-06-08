<?php

namespace maw\core;

use maw\controller\Cart_Controller;

class Renderer
{
    private $tplFile;
    private $callerClass;

    public function __construct($callerClass)
    {
        $this->callerClass = $callerClass;
        $this->tplFile = TEMPLATE_DIR . '/templates/layout/' . strtolower(str_replace('_Controller', '', (new \ReflectionClass($callerClass))->getShortName())) . '.maw';
        if (!file_exists($this->tplFile))
            $this->tplFile = TEMPLATE_DIR . '/templates/layout/page.maw';
    }

    protected function buildHTML()
    {
        $classTpl = file_get_contents($this->tplFile);

        $html = $this->callerClass->forTemplate(str_replace("{{Layout}}", $classTpl, $this->getSkeleton()));

        return $this->replaceDefaultTags($html);
    }

    protected function replaceDefaultTags($html) {
        $pages = DataProcessor::getJSONAsArray(PAGES);
        $page = null;

        foreach ($pages as $page)
            if ($page->id == $this->callerClass->getCurrentID())
                break;

        $defaultTplTags = [
            '{{BASEHREF}}',
            '{{TITLE}}',
            '{{CONTENT}}',
            '{{METATITLE}}',
            '{{METADESCRIPTION}}',
            '{{URLSEGMENT}}',
            '{{CURRENTITEMS}}'
        ];

        $replaceTags = [
            BASEURL,
            $page->title,
            $page->content,
            $page->metaTitle,
            $page->metaDescription,
            $page->urlSegment,
            Cart_Controller::countCurrentObjects()
        ];

        return str_replace($defaultTplTags, $replaceTags, $html);
    }

    protected function getSkeleton()
    {
        if (file_exists(TEMPLATE_DIR . '/templates/page.maw')) {
            $html = file_get_contents(TEMPLATE_DIR . '/templates/page.maw');
            return $html;
        } else {
            return "<h1>Main Template File not found.</h1>";
        }
    }

    public function render()
    {
        header('Content-Type: text/html; charset=utf-8');
        echo $this->buildHTML();
    }
}