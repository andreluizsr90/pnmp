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
		"type",
		"dosage",
		"presentation"
	];

    public $searchFilterFields = [
		"name" => 'like',
		"type" => 'strict',
		"dosage" => 'strict',
		"presentation" => 'strict'
	];

    public $rolesCrud = [
        "view" => 'MEDICINE_VIEW',
        "ins" => 'MEDICINE_ADD',
        "edt" => 'MEDICINE_UPD',
        "del" => 'MEDICINE_DEL'
    ];

	public function allAdditional() {
		$this->setVar('field_type', ['ORAL', 'INJECTABLE']);
		$this->setVar('field_dosage', ['MG', 'ML']);
		$this->setVar('field_presentation', ['BOTTLE', 'TABLET', 'AMPOULE', 'CAPSULE', 'INJECTABLE']);
	}

}

