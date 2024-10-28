<?php
namespace App\Engine;

trait TraitCrud {

    function index() {
        if(method_exists($this, 'search')) {
            $this->search();
        } else {
            $records = null;
            if(property_exists($this, 'orderSearch')) {
                if(is_array($this->orderSearch)) {
                    foreach ($this->orderSearch as $key => $value) {
                        if(is_null($records)) {
                            $records = $this->model::orderBy($key, $value);
                        } else {
                            $records = $records->orderBy($key, $value);
                        }
                    }
                } else {
                    $records = $this->model::orderBy($this->orderSearch);
                }
                $records = $records->get();
            } else {
                $records = $this->model::all();
            }

            $this->setVar('records', $records);
        }

        if(method_exists($this, 'indexAdditional')) {
            $this->indexAdditional();
        }

		$this->setVar('route', URL_SITE . $this->route);
    	$this->setResponse($this->path . '/list.html');
    }

    function insert() {
    	$this->setResponse($this->path . '/form.html');
    }

    function edit($id) {
		$this->setVar('record', GeneralContent::get($this->type, $id));
    	$this->setResponse($this->path . '/form.html');
    }

    function delete($id) {
		GeneralContent::remove($this->type, $id);

		$this->flash(URL_SITE . '/' . $this->getVar('route'), $this->getVar('lang')['msg']['success'], 'success');
    }

}
