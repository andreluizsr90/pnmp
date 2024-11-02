<?php
namespace App\Controller\Api;

use App\Engine\ControllerApi;
use App\Model\Institution as InstitutionMdl;

class Institutions extends ControllerApi {
    
    function getByType() {

		$type = $_GET["type"] ?? null;
		$ignore = $_GET["ignore"] ?? null;
		$data = [];

		$records = InstitutionMdl::where(['types' => $type ], ['_id' => 1, 'code' => 1, 'name' => 1, 'city' => 1 ]);
		foreach ($records as $key => $value) {
			if($ignore == $value->code) {
				continue;
			}
			
			$data[] = $value->toArray();
		}

		$this->setResponse($data);

    }
}

