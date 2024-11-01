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
            $records = $this->model::all();
            if(property_exists($this, 'orderSearch')) {
                if(is_array($this->orderSearch)) {
                    $records->sort($this->orderSearch);
                } else {
                    $records->sort([$this->orderSearch => 1]);
                }
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
		$this->setVar('record', $this->model::first($id));

        if(method_exists($this, 'editAdditional')) {
            $this->editAdditional();
        }

        if(method_exists($this, 'allAdditional')) {
            $this->allAdditional();
        }
        
		$this->setVar('route', URL_SITE . $this->route);
    	$this->setResponse($this->path . '/form.html');
    }

    function delete($id) {
        if(property_exists($this, 'rolesCrud') && !empty($this->rolesCrud['del'])) {
            $this->checkRole($this->rolesCrud['del']);
        }

		$this->model::first($id)->delete();
		$this->flash(URL_SITE . '/' . $this->route, $this->getVar('lang')['action_success'], 'success');
    }

	public function save($id) {
		if(is_null($id)) {
			$record = new $this->model;
		} else {
			$record = $this->model::first($id);
		}

        if(property_exists($this, 'formFields')) {
            foreach ($this->formFields as $value) {
                $info = explode(":", $value);
                if(array_key_exists($info[0], $_POST) && !empty($_POST[$info[0]])) {
                    if(count($info) > 1) {
                        $record->{$info[0]} = ($info[1] == "int" ? intval($_POST[$info[0]]) : $_POST[$info[0]]);
                    } else {
                        $record->{$info[0]} = $_POST[$info[0]];
                    }
                }
            }
        }

        if(method_exists($this, 'saveAdditional')) {
            $this->saveAdditional($record, is_null($id));
        }

        try {
            $record->save();

            if(method_exists($this, 'postSave')) {
                $this->postSave($record);
            }

            $this->flash(URL_SITE . '/' . $this->route, $this->getVar('lang')['action_success'], 'success');
        } catch (\Throwable $th) {
            $message = sprintf($this->getVar('lang')['action_generic_error'], $th->getMessage());
            $this->flashDataPost($message);
        }
		
	}

}
