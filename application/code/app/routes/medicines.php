<?php

	$router->group(['prefix' => 'medicines'], function($router){

		$router->group(['prefix' => 'inventory'], function($router){
			$ctrl = new App\Controller\Medicines\Inventory();

			$router->get('/', function() use ($ctrl) {
				$ctrl->overview();
				return $ctrl->getResponse();
			});

			$router->get('/new-batch', function() use ($ctrl) {
				$ctrl->newBatch();
				return $ctrl->getResponse();
			});

			$router->post('/new-batch', function() use ($ctrl) {
				$ctrl->saveNewBatch();
				return $ctrl->getResponse();
			});
            
		});

	});
