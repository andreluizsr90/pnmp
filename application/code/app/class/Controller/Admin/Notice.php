<?php
namespace App\Controller\Admin;

use App\Engine\Controller;
use App\Engine\{TraitCrud, HelperUtil};
use App\Model\UserProfile as UserProfileMdl;
use App\Model\Institution as InstitutionMdl;
use App\Model\AdministrativeUnit as AdministrativeUnitMdl;

class Notice extends Controller {
	use TraitCrud;

	public $model = "App\Model\Notice";
	public $route = "/admin/notice";
	public $path = "admin/notice";
	public $orderSearch = ['title' => 1];

    public $formFields = ['title','content'];

    public $rolesCrud = [
        "view" => 'NOTICE_VIEW',
        "ins" => 'NOTICE_ADD',
        "edt" => 'NOTICE_UPD',
        "del" => 'NOTICE_DEL'
    ];

}

