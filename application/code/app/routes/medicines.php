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
	
			$router->get('/history', function() {
				$ctrl = new App\Controller\Medicines\InventoryHistory();
				$ctrl->overview();
				return $ctrl->getResponse();
			});


			$router->group(['prefix' => 'order'], function($router){
				$ctrl = new App\Controller\Medicines\InventoryOrder();
	
				$router->get('/', function() use ($ctrl) {
					$ctrl->newOrder();
					return $ctrl->getResponse();
				});
	
				$router->post('/', function() use ($ctrl) {
					$ctrl->saveNewOrder();
					return $ctrl->getResponse();
				});
	
				$router->get('/overview', function() use ($ctrl) {
					$ctrl->overview();
					return $ctrl->getResponse();
				});
	
				$router->get('/overview-pending', function() use ($ctrl) {
					$ctrl->overviewPendingOrder();
					return $ctrl->getResponse();
				});
	
				$router->get('/handle-pending/{id}', function(int $id) use ($ctrl) {
					$ctrl->handlePendingOrder($id);
					return $ctrl->getResponse();
				});
	
				$router->post('/handle-pending/{id}', function(int $id) use ($ctrl) {
					$ctrl->savePendingOrder($id);
					return $ctrl->getResponse();
				});
	
				$router->get('/overview-receiving', function() use ($ctrl) {
					$ctrl->overviewReceivingOrder();
					return $ctrl->getResponse();
				});
	
				$router->get('/handle-receiving/{id}', function(int $id) use ($ctrl) {
					$ctrl->handleReceivingOrder($id);
					return $ctrl->getResponse();
				});
			});
            
		});

	});
