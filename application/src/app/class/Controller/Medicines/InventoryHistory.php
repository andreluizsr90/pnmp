<?php
namespace App\Controller\Medicines;

use App\Business\Inventory as InventoryBusiness;
use App\Model\{
    InventoryHistory as InventoryHistoryMdl,
    Institution as InstitutionMdl
};

class InventoryHistory extends InventoryBase {
    
	public $route = "/medicines/inventory";
	public $path = "medicines/inventory/history";

    function overview() {

		$this->setVar('medicines', InventoryBusiness::allMedicinesByIdKey());
		$this->setVar('movments', InventoryHistoryMdl::where([
            'institution_id' => $this->getVar('institution')["_id"]
        ]));

    	$this->setResponse($this->path . '/overview.html');

    }

}

