<?php
namespace App\Controller\Medicines;

use App\Engine\Controller;
use App\Model\InventoryHistory as InventoryHistoryMdl;
use App\Model\Institution as InstitutionMdl;

class InventoryHistory extends Controller {
    
	public $route = "/medicines/inventory";
	public $path = "medicines/inventory/history";

    function __construct() {
        parent::__construct();
        
		$this->setVar('institutions_change', InstitutionMdl::byParentAdministrativeUnit());

    }

    function overview() {

		$this->setVar('movments', InventoryHistoryMdl::where([
            'institution_id' => $this->getVar('institution')["_id"]
        ]));

    	$this->setResponse($this->path . '/overview.html');

    }

}

