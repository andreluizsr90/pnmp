<?php

	$router->get('/', function(){
		$ctrl = new App\Controller\Example();
		$ctrl->home();
		return $ctrl->getResponse();
	});