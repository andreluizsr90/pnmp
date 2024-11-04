<?php
namespace App\Controller\Api;

use App\Engine\ControllerApi;
use App\Model\Institution as InstitutionMdl;

class Institutions extends ControllerApi {
    
    function getByFilter() {

		$type = $_GET["type"] ?? null;
		$adminUnit = $_GET["administrative_unit"] ?? null;
		$ignore = $_GET["ignore"] ?? null;

		$where = [];
		if(!is_null($type)) {
			$where['types'] = $type;
		}
		if(!is_null($adminUnit)) {
			$where['administrative_unit'] = (int) $adminUnit;
		}

		$data = [];
		if(count($where) > 0) {

			$records = InstitutionMdl::where($where, ['_id' => 1, 'code' => 1, 'name' => 1, 'city' => 1, 'administrative_unit' => 1 ]);
			foreach ($records as $key => $value) {
				if($ignore == $value->_id) {
					continue;
				}
				
				$data[] = $value->toArray();
			}
		}




		$this->setResponse($data);

    }
}

