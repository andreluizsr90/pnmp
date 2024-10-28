<?php
namespace App\Controller;

use App\Engine\Controller;

class Dashboard extends Controller {

	public function init() {		
		$this->setResponse('dashboard.html');
	}

}

