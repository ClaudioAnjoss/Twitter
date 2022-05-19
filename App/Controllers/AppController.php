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

    public function quemSeguir() {

        $this->validarAuth();

        $pesquisarPor = isset($_GET['pesquisarPor']) ? $_GET['pesquisarPor'] : '';

        $usuarios = array();

        if ($pesquisarPor != '') {
            $usuario = Container::getModel('Usuario');
            $usuario->__set('nome' , $pesquisarPor);
            $usuario->__set('id' , $_SESSION['id']);

            $usuarios = $usuario->getAll();

            // echo '<pre>';
            // print_r($usuarios);
            // echo '</pre>';

        }

        $this->view->usuarioPesquisado = $usuarios;

        $this->render('quemSeguir');
    }

    public function acao() {
        $this->validarAuth();

        $acao = isset($_GET['acao']) ? $_GET['acao'] : '';
        $id_usuario = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';

        $usuario = Container::getModel('Usuario');
        $usuario->__set('id' , $_SESSION['id']);

        if($acao == 'seguir') {
            $usuario->seguirUsuario($id_usuario);
            header('Location: /quem_seguir');
        } else if($acao == 'deixar_seguir') {
            $usuario->deixarseguirUsuario($id_usuario);
            header('Location: /quem_seguir');
        }
        
    }
}

?>