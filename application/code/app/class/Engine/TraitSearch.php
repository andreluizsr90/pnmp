<?php
namespace App\Engine;

trait TraitSearch {

	public function search() {

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
		}

		$searchFilter = [];
		foreach ($_GET as $key => $value) {
			if(array_key_exists($key, $this->searchFilterFields) && !empty($value)) {
				$searchFilter[$key] = $value;
				if($this->searchFilterFields[$key]["type"] == 'strict') {
					$records = !is_null($records) ? $records->where($key, $value) : $this->model::where($key, $value);
				}else if($this->searchFilterFields[$key]["type"] == 'like') {
					$records = !is_null($records) ? $records->where($key, 'like', '%'.$value.'%') : $this->model::where($key, 'like', '%'.$value.'%');
				}
				
			}
		}

		$this->setVar('records', $records->get());
		$this->setVar('searchFilter', $searchFilter);
		$this->setVar('searchActive', (count($searchFilter) > 0));
	}

}
