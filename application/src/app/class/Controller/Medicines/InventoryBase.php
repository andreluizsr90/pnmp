<?php
namespace App\Controller\Medicines;

use App\Engine\Controller;
use App\Model\{
    Institution as InstitutionMdl,
    OrderMedicine as OrderMedicineMdl,
    TransferMedicine as TransferMedicineMdl
};

class InventoryBase extends Controller {

    function __construct() {
        parent::__construct();
        
		$this->setVar('institutions_change', InstitutionMdl::byParentAdministrativeUnit());

		$this->setVar('orders_pending', OrderMedicineMdl::where([
            'institution_supplier' => $this->getVar('institution')["_id"],
            'status' => OrderMedicineMdl::$allowedStatus['OPEN']
        ])->count());

		$this->setVar('orders_receiving', OrderMedicineMdl::where([
            'institution_owner' => $this->getVar('institution')["_id"],
            'status' => OrderMedicineMdl::$allowedStatus['APPROVED']
        ])->count());

		$this->setVar('transfers_pending', TransferMedicineMdl::where([
            'institution_destination' => $this->getVar('institution')["_id"],
            'status' => TransferMedicineMdl::$allowedStatus['OPEN']
        ])->count());

    }

}

