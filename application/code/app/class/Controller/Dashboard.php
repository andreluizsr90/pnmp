<?php
namespace App\Controller;

use App\Engine\Controller;
use App\Model\Notice as NoticeMdl;

class Dashboard extends Controller {

	public function init() {
		$this->setVar('notices', NoticeMdl::all()->sort(['created_at' => -1]));
		$this->setResponse('dashboard.html');
	}

}

