<?php
namespace App\Controller\Admin;

use App\Engine\Controller;
use App\Engine\{TraitSearch, TraitCrud, HelperUtil};

class Institutions extends Controller {
	use TraitSearch, TraitCrud;

	public $model = "App\Model\Institution";
	public $route = "/admin/institutions";
	public $path = "admin/institutions";
	public $orderSearch = ['name' => 1];

    public $formFields = [
		"code",
		"name",
		"phone",
		"address_1",
		"address_2",
		"postal_code",
		"city",
		"parent_code"
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

}

