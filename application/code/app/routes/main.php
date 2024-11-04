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


	$router->group(['prefix' => 'api'], function($router){

		$router->group(['prefix' => 'administrative-units'], function($router){
			$ctrl = new App\Controller\Api\AdministrativeUnits();

			$router->get('/by-parent-id', function() use ($ctrl) {
				$ctrl->getByParentId();
				return $ctrl->getResponse();
			});

			$router->get('/parents-by-id', function() use ($ctrl) {
				$ctrl->getParentsById();
				return $ctrl->getResponse();
			});

			$router->get('/by-id', function() use ($ctrl) {
				$ctrl->getById();
				return $ctrl->getResponse();
			});
		});

		$router->group(['prefix' => 'institutions'], function($router){
			$ctrl = new App\Controller\Api\Institutions();

			$router->get('/by-filter', function() use ($ctrl) {
				$ctrl->getByFilter();
				return $ctrl->getResponse();
			});
		});
	});

	$router->group(['before' => 'authUserAccount'], function($router){
		$router->get('/', function() {
			$ctrl = new App\Controller\Dashboard();
			$ctrl->init();
			return $ctrl->getResponse();
		});

		require(PATH_ROUTES . '/admin.php');  

	});
