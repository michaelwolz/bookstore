<?php

namespace maw\controller;

use \maw\core\Renderer;

class Controller
{
    private $pageID;

    public function __construct($pageID)
    {
        $this->pageID = $pageID;
    }

    public function render() {
        $renderer = new Renderer($this);
        $renderer->render();
    }

    public function forTemplate($skeleton) {
        return $skeleton;
    }

    public function getCurrentID() {
        return $this->pageID;
    }
}