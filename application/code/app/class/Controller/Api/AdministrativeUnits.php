<?php
namespace App\Controller\Api;

use App\Engine\ControllerApi;
use App\Model\AdministrativeUnit as AdministrativeUnitMdl;

class AdministrativeUnits extends ControllerApi {
    
    function getById() {

		$id = $_GET["id"] ?? null;

		$data = AdministrativeUnitMdl::where(
			['_id' => (int) $id ],
			['_id' => 1, 'code' => 1, 'name' => 1, 'parent_id' => 1 ]
			)->first()->toArray();

		$this->setResponse($data);

    }
    
    function getParentsById() {

		$id = $_GET["id"] ?? null;

		$data = [];
		$data[] = AdministrativeUnitMdl::where(
			['_id' => (int) $id ],
			['_id' => 1, 'parent_id' => 1 ]
			)->first()->toArray();
		
		$data = $this->loadParents($data);

		$this->setResponse($data);

    }

	private function loadParents($data) {

		$lastId = $data[count($data) - 1]["parent_id"] ?? null;

		if(!is_null($lastId)) {
			$checkParent = AdministrativeUnitMdl::where(
				['_id' => (int) $lastId ],
				['_id' => 1, 'parent_id' => 1 ]
			);

			if($checkParent->count() > 0) {
				$data[] = $checkParent->first()->toArray();
				$data = $this->loadParents($data);
			}
		}

		return $data;

	}
    
    function getByParentId() {

		$id = $_GET["id"] ?? null;
		$data = [];
		if(is_null($id)) {
			$where = ['parent_id' => ['$exists' => false ] ];
		} else {
			$where = ['parent_id' => (int) $id ];
		}

		$records = AdministrativeUnitMdl::where($where, ['_id' => 1, 'id' => 1, 'name' => 1 ]);
		foreach ($records as $key => $value) {
			$row = $value->toArray();
			$row['has_children'] = AdministrativeUnitMdl::where(['parent_id' => (int) $value->_id ])->count() > 0;
			$data[] = $row;
		}

		$this->setResponse($data);

    }
}

