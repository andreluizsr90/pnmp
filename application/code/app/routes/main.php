<?php

	$router->filter('authUserAccount', function(){    
		if(!isset($_SESSION['user_account'])) {
			header('Location: ' . URL_SITE . "/login");
			return false;
		}
	});

	$router->get('/login', function(){
		$ctrl = new App\Controller\Auth();
		$ctrl->logIn();
		return $ctrl->getResponse();
	});

	$router->post('/login', function(){
		$ctrl = new App\Controller\Auth();
		$ctrl->logInAction();
		return $ctrl->getResponse();
	});

	$router->get('/logoff', function(){
		$ctrl = new App\Controller\Auth();
		$ctrl->logOff();
		return $ctrl->getResponse();
	});


	$router->group(['before' => 'authUserAccount'], function($router){
		$router->get('/', function() {
			$ctrl = new App\Controller\Dashboard();
			$ctrl->init();
			return $ctrl->getResponse();
		});

		require(PATH_ROUTES . '/admin.php');  

	});
