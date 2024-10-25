<?php
namespace App\Controller;

use App\Engine\Controller;
use App\Model\UserLogins;

class Example extends Controller {

	public function home() {
		$this->setVar('home', 'funcionando....');
		$this->setVar('logins', UserLogins::all());
		
		$this->setResponse('home.html');
	}

}

