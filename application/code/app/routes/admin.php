<?php

	$router->group(['prefix' => 'admin'], function($router){	

		$router->group(['prefix' => 'users'], function($router){
			$ctrl = new App\Controller\Admin\Users();
			include(PATH_ROUTES . "/type/crud.php");
		});

		$router->group(['prefix' => 'profiles'], function($router){
			$ctrl = new App\Controller\Admin\Profiles();
			include(PATH_ROUTES . "/type/crud.php");
		});

		$router->group(['prefix' => 'medicines'], function($router){
			$ctrl = new App\Controller\Admin\Medicines();
			include(PATH_ROUTES . "/type/crud.php");
		});

		$router->group(['prefix' => 'institutions'], function($router){
			$ctrl = new App\Controller\Admin\Institutions();
			include(PATH_ROUTES . "/type/crud.php");
		});

		$router->group(['prefix' => 'notice'], function($router){
			$ctrl = new App\Controller\Admin\Notice();
			include(PATH_ROUTES . "/type/crud.php");
		});

		$router->group(['prefix' => 'administrative-units'], function($router){
			$ctrl = new App\Controller\Admin\AdministrativeUnits();

			$router->get('/tree-view', function() use ($ctrl) {
				$ctrl->indexTree();
				return $ctrl->getResponse();
			});

			$router->group(['prefix' => 'import'], function($router) use ($ctrl){
				$router->get('/', function() use ($ctrl) {
					$ctrl->importForm();
					return $ctrl->getResponse();
				});
				$router->get('/download', function() use ($ctrl) {
					$ctrl->importDownload();
					return $ctrl->getResponse();
				});
			
				$router->post('/', function() use ($ctrl) {
					$ctrl->import();
					return $ctrl->getResponse();
				});
			});
			
			include(PATH_ROUTES . "/type/crud.php");
		});

	});
