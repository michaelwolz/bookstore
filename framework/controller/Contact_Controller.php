<?php

namespace maw\controller;

use maw\core\Renderer;

class Contact_Controller extends Controller
{
    private $success = "";

    public function forTemplate($skeleton) {
        return str_replace("{{SUCCESS}}", $this->success, $skeleton);
    }

    public function send()
    {
        $from = $_POST['email'];
        $name = $_POST['name'];
        $subject = $_POST['subject'];
        $message = $_POST['message'];

        $headers = "From: $name <$from>" . "\r\n";
        $headers .= "X-Mailer: PHP/".phpversion();

        $to = "s4miwolz@uni-trier.de,s4aawinz@uni-trier.de";

        if (mail($to, $subject, $message, $headers))
            $this->success = "success";
        else
            $this->success = "failure";

        $this->render();
    }
}