<?php
namespace App\Controller\Admin;

use App\Engine\Controller;
use App\Engine\{TraitSearch, TraitCrud, HelperUtil};
use App\Model\Permission as PermissionMdl;

class Users extends Controller {
	use TraitSearch, TraitCrud;

	public $model = "App\Model\UserAccount";
	public $route = "/admin/users";
	public $path = "admin/users";
	public $orderSearch = ['name' => 'asc'];

    public $formFields = ['name','email','phone','permission_id'];

    public $searchFilterFields = [
        "permission_id" => 'strict',
        "name" => 'like',
        "email" => 'strict'
    ];

	public function allAdditional() {
		$this->setVar('permissions', PermissionMdl::all());
	}

	public function saveAdditional($record, $isNew) {
		if($isNew) {
			$checkEmailUnique = $this->model::where('email', $_POST['email'])->first();
		} else {
			$checkEmailUnique = $this->model::where('email', $_POST['email'])->where('id', '!=', $record->id)->first();
		}
		
		if(!is_null($checkEmailUnique)) {
			$message = strtr($this->getVar('lang')['field_duplicated'], ['{$1}' => 'email', '{$2}' => $_POST['email']]);
            $this->flashDataPost($message);
		}

		$record->password = HelperUtil::passwordGenerate("123");
	}

}
