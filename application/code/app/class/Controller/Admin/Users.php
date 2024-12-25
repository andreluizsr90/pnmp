<?php
namespace App\Controller\Admin;

use App\Engine\Controller;
use App\Engine\{TraitSearch, TraitCrud, HelperUtil};
use App\Model\UserProfile as UserProfileMdl;
use App\Model\Institution as InstitutionMdl;
use App\Model\AdministrativeUnit as AdministrativeUnitMdl;

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

		if(empty($_POST["institution_id"])) {
			$message = sprintf($this->getVar('lang')['field_required'], 'institution_id');
            $this->flashDataPost($message);
		} else {
			$record->institution()->attach(InstitutionMdl::first((int) $_POST["institution_id"]));
		}

		if(empty($_POST["unit_view_id"])) {
			unset($record->unit_view_id);
		} else {
			$record->unit_view()->attach(AdministrativeUnitMdl::first((int) $_POST["unit_view_id"]));
		}

		
		
		$record->is_active = !empty($_POST["is_active"]);

		// $record->password = HelperUtil::passwordGenerate("123");
	}

}

