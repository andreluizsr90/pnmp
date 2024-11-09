<?php
namespace App\Controller\Admin;

use App\Engine\Controller;
use App\Engine\{TraitSearch, TraitCrud, HelperUtil};

class Medicines extends Controller {
	use TraitSearch, TraitCrud;

	public $model = "App\Model\Medicine";
	public $route = "/admin/medicines";
	public $path = "admin/medicines";
	public $orderSearch = ['name' => 1];

    public $formFields = [
		"code",
		"name",
		"category",
		"dosage:int",
		"dosage_unit",
		"presentation"
	];

    public $searchFilterFields = [
		"name" => 'like',
		"category" => 'strict',
		"dosage_unit" => 'strict',
		"presentation" => 'strict'
	];

    public $rolesCrud = [
        "view" => 'MEDICINE_VIEW',
        "ins" => 'MEDICINE_ADD',
        "edt" => 'MEDICINE_UPD',
        "del" => 'MEDICINE_DEL'
    ];

	public function allAdditional() {
		$this->setVar('field_category', ['ORAL', 'INJECTABLE']);
		$this->setVar('field_dosage_unit', ['MG', 'ML']);
		$this->setVar('field_presentation', ['BOTTLE', 'TABLET', 'AMPOULE', 'CAPSULE', 'INJECTABLE']);
	}

}

