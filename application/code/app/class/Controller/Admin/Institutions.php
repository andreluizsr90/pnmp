<?php
namespace App\Controller\Admin;

use App\Engine\Controller;
use App\Engine\{TraitSearch, TraitCrud, HelperUtil};
use App\Model\Institution as InstitutionMdl;
use App\Model\AdministrativeUnit as AdministrativeUnitMdl;
use App\Model\Address as AddressMdl;

class Institutions extends Controller {
	use TraitSearch, TraitCrud;

	public $model = "App\Model\Institution";
	public $route = "/admin/institutions";
	public $path = "admin/institutions";
	public $orderSearch = ['name' => 1];

    public $formFields = [
		"code",
		"name",
		"phones",
		"parent_code",
		"types"
	];

    public $searchFilterFields = [
		"name" => 'like',
		"city" => 'like'
	];

    public $rolesCrud = [
        "view" => 'INST_VIEW',
        "ins" => 'INST_ADD',
        "edt" => 'INST_UPD',
        "del" => 'INST_DEL'
    ];

	public function allAdditional() {
		$this->setVar('institution_types', ["NOTIFICATION","TREATMENT","MEDICINE_STORE"]);
	}
 
	function saveAdditional($record, $isNew) {
		$administrativeUnitId = (int) $_POST["administrative_unit"][count($_POST["administrative_unit"]) - 1];
		$record->unit()->attach(AdministrativeUnitMdl::first($administrativeUnitId));

		$address = new AddressMdl();
		$address->line_1 = $_POST["line_1"];
		$address->line_2 = $_POST["line_2"];
		$address->city = $_POST["city"];
		$address->postal_code = $_POST["postal_code"];

		$record->location()->add($address);

		if(empty($_POST["institution_supplier"])) {
			unset($record->institution_supplier);
		} else {
			$record->supplier()->attach(InstitutionMdl::first((int) $_POST["institution_supplier"]));
		}
		
		if(!isset($_POST["types"])) {
			$record->types = [];
		}
	}

}

