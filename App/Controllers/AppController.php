<?php

namespace App\Controllers;

//os recursos do miniframework

use App\Models\Usuario;
use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action {
    public function timeline() {

        $this->validarAuth();

        $tweet = Container::getModel('Tweet');
        $tweet->__set('id_usuario' , $_SESSION['id']);

        $tweets = $tweet->getAll();

        $this->view->tweets = $tweets;

        $this->render('timeline');
        
    }

    public function tweet() {

        $this->validarAuth();

        $tweet = Container::getModel('Tweet');
        $tweet->__set('tweet' , $_POST['tweet']);
        $tweet->__set('id_usuario' , $_SESSION['id']);

        $tweet->salvar();

        $this->timeline();
        
    }

    public function validarAuth() {
        session_start();

        if((!isset($_SESSION['id']) || $_SESSION['id'] == '') && (!isset($_SESSION['nome']) || $_SESSION['nome'] == '')) {
            header('Location: /?auth=false'); 
        }
    }
}

?>