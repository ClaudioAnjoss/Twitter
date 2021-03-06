<?php

namespace App\Controllers;

//os recursos do miniframework

use App\Models\Usuario;
use MF\Controller\Action;
use MF\Model\Container;
use App\Controllers\AuthController;

class IndexController extends Action {

	public function index() {

		$this->view->login = isset($_GET['login']) ? $_GET['login'] : '';
		$this->view->auth = isset($_GET['auth']) ? $_GET['auth'] : '';

		$this->render('index');
	}

	public function inscreverse() {
		$this->view->valueCampos = array(
			'nome' => $_POST['nome'] = '',
			'email' => $_POST['email'] = '',
			'senha' => $_POST['senha'] = ''
		);
		
		$this->render('inscreverse');
	}

	public function entrar() {

		$this->view->login = isset($_GET['login']) ? $_GET['login'] : '';
		$this->view->auth = isset($_GET['auth']) ? $_GET['auth'] : '';

		$this->render('entrar');
	}

	public function registrar() {
		// sucesso
		// print_r($_POST);
		

		$usuario = Container::getModel('Usuario');
		$usuario->__set('nome', $_POST['nome']);
		$usuario->__set('email', $_POST['email']);
		$usuario->__set('senha', md5($_POST['senha']));

		

		if($usuario->validar()) {
			if (count($usuario->getUsuarioPorEmail()) == 0) {
				$usuario->salvar();
				// $this->render('cadastro');
				$usuario->autenticar();

        		if($usuario->__get('id') != '' && $usuario->__get('nome') != '') {
            	session_start();
            	$_SESSION['id'] = $usuario->__get('id');
            	$_SESSION['nome'] = $usuario->__get('nome');

            	header('Location: /timeline?cadastro=true');
        	}
			} else {
				$this->view->usuarioExiste = true;
				// $this->render('inscreverse');
				header("Location: /inscreverse?usuario_existe=true");
			}
		} else {
			$this->view->camposInvalidos = true;
			$this->view->valueCampos = array(
				'nome' => $_POST['nome'],
				'email' => $_POST['email'],
				'senha' => $_POST['senha']
			);
			$this->render('inscreverse');
		}
	}

}


?>