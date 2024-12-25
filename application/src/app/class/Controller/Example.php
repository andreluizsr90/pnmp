<?php
namespace App\Controller;

use App\Engine\Controller;
use App\Model\UserLogins;
use App\Engine\HelperUtil;


class Example extends Controller {

	public function home() {
		var_dump(HelperUtil::passwordGenerate('teste123'));
		exit;
		$this->setVar('home', HelperUtil::passwordGenerate('teste123'));
		$this->setVar('existsNotification', false);
		$this->setVar('logins', UserLogins::all());
		
		$this->setResponse('login.html');
	}

}

