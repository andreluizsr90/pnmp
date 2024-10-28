<?php
namespace App\Engine;

trait TraitCrud {
    
    function index() {
        if(property_exists($this, 'rolesCrud') && !empty($this->rolesCrud['view'])) {
            $this->checkRole($this->rolesCrud['view']);
        }

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
        if(property_exists($this, 'rolesCrud') && !empty($this->rolesCrud['ins'])) {
            $this->checkRole($this->rolesCrud['ins']);
        }

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
        if(property_exists($this, 'rolesCrud') && !empty($this->rolesCrud['edt'])) {
            $this->checkRole($this->rolesCrud['edt']);
        }

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
        if(property_exists($this, 'rolesCrud') && !empty($this->rolesCrud['del'])) {
            $this->checkRole($this->rolesCrud['del']);
        }

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
            $message = sprintf($this->getVar('lang')['action_generic_error'], $th->getMessage());
            $this->flashDataPost($message);
        }
		
	}

}
