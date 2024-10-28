<?php

	$router->group(['prefix' => 'admin'], function($router){	

		$router->group(['prefix' => 'users'], function($router){		
			
			$ctrl = new App\Controller\Admin\Users();
			include(PATH_ROUTES . "/type/crud.php");
	
		});

	});
