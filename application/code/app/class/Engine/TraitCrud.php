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

        if(method_exists($this, 'allAdditional')) {
            $this->allAdditional();
        }

		$this->setVar('route', URL_SITE . $this->route);
    	$this->setResponse($this->path . '/list.html');
    }

    function insert() {

        if(method_exists($this, 'insertAdditional')) {
            $this->insertAdditional();
        }

        if(method_exists($this, 'allAdditional')) {
            $this->allAdditional();
        }

		$this->setVar('route', URL_SITE . $this->route);
    	$this->setResponse($this->path . '/form.html');
    }

    function edit($id) {

        if(method_exists($this, 'editAdditional')) {
            $this->editAdditional();
        }

        if(method_exists($this, 'allAdditional')) {
            $this->allAdditional();
        }
        
		$this->setVar('record', $this->model::where('id', $id)->first());
		$this->setVar('route', URL_SITE . $this->route);
    	$this->setResponse($this->path . '/form.html');
    }

    function delete($id) {
		$this->model::where('id', $id)->first()->delete();
		$this->flash(URL_SITE . '/' . $this->route, $this->getVar('lang')['action_success'], 'success');
    }

	public function save($id) {
		if(is_null($id)) {
			$record = new $this->model;
		} else {
			$record = $this->model::where('id', $id)->first();
		}

        if(property_exists($this, 'formFields')) {
            foreach ($this->formFields as $value) {
                if(array_key_exists($value, $_POST)) {
                    $record->$value = $_POST[$value];
                }
            }
        }

        if(method_exists($this, 'saveAdditional')) {
            $this->saveAdditional($record, is_null($id));
        }

        try {
            $record->save();
            $this->flash(URL_SITE . '/' . $this->route, $this->getVar('lang')['action_success'], 'success');
        } catch (\Throwable $th) {
            $message = strtr($this->getVar('lang')['action_generic_error'], ['{$1}' => $th->getMessage()]);
            $this->flash($_SERVER['REQUEST_URI'], $message, 'error', $_POST, true);
            exit;
        }
		
	}

}
