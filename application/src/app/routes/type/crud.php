<?php


	$router->get('/', function() use ($ctrl) {
        $ctrl->index();
        return $ctrl->getResponse();
    });

	$router->get('/insert', function() use ($ctrl) {
        $ctrl->insert();
        return $ctrl->getResponse();
    });

	$router->post('/insert', function() use ($ctrl) {
        $ctrl->save(null);
        return $ctrl->getResponse();
    });

	$router->get('/delete/{id}', function($id = null) use ($ctrl) {
        $ctrl->delete((int) $id);
        return $ctrl->getResponse();
    });

	$router->get('/{id}', function($id = null) use ($ctrl) {
        $ctrl->edit((int) $id);
        return $ctrl->getResponse();
    });

	$router->post('/{id}', function($id = null) use ($ctrl) {
        $ctrl->save((int) $id);
        return $ctrl->getResponse();
    });
