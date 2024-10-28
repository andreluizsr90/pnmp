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

	$router->get('/{id}', function($id = null) use ($ctrl) {
        $ctrl->edit($id);
        return $ctrl->getResponse();
    });

	$router->post('/{id}', function($id = null) use ($ctrl) {
        $ctrl->save($id);
        return $ctrl->getResponse();
    });

	$router->get('/{id}/delete', function($id = null) use ($ctrl) {
        $ctrl->delete($id);
        return $ctrl->getResponse();
    });
