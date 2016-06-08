<?php

namespace maw\controller;

class Error_Controller extends Controller {
    private $code;

    public function __construct($code, $id = 0)
    {
        parent::__construct($id);
        $this->code = $code;
    }
}