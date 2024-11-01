<?php
namespace App\Engine;

trait TraitSearch {

	public function search() {

		$searchFilter = [];
		$searchWhere = [];
		foreach ($_GET as $key => $value) {
			if(array_key_exists($key, $this->searchFilterFields) && !empty($value)) {
				$searchFilter[$key] = $value;
                $info = explode(":", $this->searchFilterFields[$key]);
				if($info[0] == 'strict') {
					$searchWhere[$key] = (count($info) > 1 && $info[1] == "int" ? intval($value) : $value);
				}else if($info[0] == 'like') {
					$searchWhere[$key] = new \MongoDB\BSON\Regex("$value", 'i');
				}
			}
		}

		$records = $this->model::where($searchWhere);
		if(property_exists($this, 'orderSearch')) {
			if(is_array($this->orderSearch)) {
				$records->sort($this->orderSearch);
			} else {
				$records->sort([$this->orderSearch => 1]);
			}
		}

		$this->setVar('records', $records);
		$this->setVar('searchFilter', $searchFilter);
		$this->setVar('searchActive', (count($searchFilter) > 0));
	}

}
