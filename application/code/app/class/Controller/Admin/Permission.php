<?php
namespace App\Controller\Admin;

use App\Engine\Controller;
use App\Engine\{TraitSearch, TraitCrud, HelperUtil};
use App\Model\PermissionRoles;

class Permission extends Controller {
	use TraitSearch, TraitCrud;

	public $model = "App\Model\Permission";
	public $route = "/admin/permission";
	public $path = "admin/permission";
	public $orderSearch = ['name' => 'asc'];

    public $formFields = ['name'];

    public $searchFilterFields = ["name" => 'like'];

    public $rolesCrud = [
        "view" => 'PERM_VIEW',
        "ins" => 'PERM_ADD',
        "edt" => 'PERM_UPD',
        "del" => 'PERM_DEL'
    ];

	public function allAdditional() {
		$this->setVar('roles', $this->getAllRoles());
	}

	public function editAdditional() {
		$roles = [];
		foreach (PermissionRoles::where('permission_id', $this->getVar('record')->id)->get() as $value) {
			$roles[] = $value->role;
		}
		
		$this->setVar('roles_exists', $roles);
	}

	public function saveAdditional() {
		if(empty($_POST['roles']) || !is_array($_POST['roles']) || count($_POST['roles']) == 0) {
			$message = sprintf($this->getVar('lang')['field_empty'], 'Ações');
            $this->flashDataPost($message);
		}
	}

	public function postSave($record) {

		PermissionRoles::where('permission_id', $record->id)->delete();

		foreach ($_POST['roles'] as $value) {
			$role = new PermissionRoles();
			$role->permission_id = $record->id;
			$role->role = $value;
			$role->save();
		}
	}

}

