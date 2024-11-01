<?php
namespace App\Controller\Admin;

use App\Engine\Controller;
use App\Engine\{TraitSearch, TraitCrud, HelperUtil};
use App\Model\UserProfile as UserProfileMdl;

class Users extends Controller {
	use TraitSearch, TraitCrud;

	public $model = "App\Model\UserAccount";
	public $route = "/admin/users";
	public $path = "admin/users";
	public $orderSearch = ['name' => 1];

    public $formFields = ['name','email','phone','profile_user:int'];

    public $searchFilterFields = [
        "profile_user" => 'strict:int',
        "name" => 'like',
        "email" => 'strict'
    ];

    public $rolesCrud = [
        "view" => 'USER_VIEW',
        "ins" => 'USER_ADD',
        "edt" => 'USER_UPD',
        "del" => 'USER_DEL'
    ];

	public function allAdditional() {
		$this->setVar('profiles', UserProfileMdl::all());
	}

	public function saveAdditional($record, $isNew) {
		if($isNew) {
			$checkEmailUnique = $this->model::where(['email' => $_POST['email']])->first();
		} else {
			$checkEmailUnique = $this->model::where(['email' => $_POST['email'], 'id' => ['$ne' =>  $record->id]])->first();
		}
		
		if(!is_null($checkEmailUnique)) {
			$message = sprintf($this->getVar('lang')['field_duplicated'], 'email', $_POST['email']);
            $this->flashDataPost($message);
		}

		$record->password = HelperUtil::passwordGenerate("123");
	}

}

