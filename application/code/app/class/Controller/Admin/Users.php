<?php
namespace App\Controller\Admin;

use App\Engine\Controller;
use App\Engine\{TraitSearch, TraitCrud};
use App\Model\Permission as PermissionMdl;

class Users extends Controller {
	use TraitSearch, TraitCrud;

	public $model = "App\Model\UserAccount";
	public $route = "/admin/users";
	public $path = "admin/users";
	public $orderSearch = [
		'name' => 'asc'
	];

	public function indexAdditional() {
		$this->setVar('permissions', PermissionMdl::all());
	}

}

