<?php
namespace App\Controller\Admin;

use App\Engine\Controller;
use App\Engine\{TraitSearch, TraitCrud, HelperUtil};

class Profiles extends Controller {
	use TraitSearch, TraitCrud;

	public $model = "App\Model\UserProfile";
	public $route = "/admin/profiles";
	public $path = "admin/profiles";
	public $orderSearch = ['name' => 1];

    public $formFields = ['name', 'view_type'];

    public $searchFilterFields = ["name" => 'like'];

    public $rolesCrud = [
        "view" => 'PROFILE_VIEW',
        "ins" => 'PROFILE_ADD',
        "edt" => 'PROFILE_UPD',
        "del" => 'PROFILE_DEL'
    ];

	public function allAdditional() {
		$this->setVar('roles', $this->getAllRoles());
		$this->setVar('view_type', ['ALL', 'UNIT','INSTITUTION']);
	}

	public function saveAdditional($record, $isNew) {
		if(empty($_POST['roles']) || !is_array($_POST['roles']) || count($_POST['roles']) == 0) {
			$message = sprintf($this->getVar('lang')['field_empty'], 'Ações');
            $this->flashDataPost($message);
		}
		$record->roles = $_POST['roles'];
	}

}

