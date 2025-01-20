<?php
namespace App\Controller\Medicines;

use App\Business\Inventory as InventoryBusiness;
use App\Model\{
    Medicine as MedicineMdl,
    Institution as InstitutionMdl,
    DispensationMedicine as DispensationMedicineMdl,
    ItemMedicineQuantity as ItemMedicineQuantityMdl,
    UserAccount as UserAccountMdl
};

class InventoryDispensation extends InventoryBase {
    
	public $routeToReturn = "/medicines/inventory";
	public $route = "/medicines/inventory/dispensation";
	public $path = "medicines/inventory/dispensation";

    /*
        ######### Dispensation
    */

    function overview() {

		$this->setVar('dispensations', DispensationMedicineMdl::where([
            'institution_owner' => $this->getVar('institution')["_id"]
        ]));
    	$this->setVar('route', $this->routeToReturn);
    	$this->setResponse($this->path . '/overview.html');

    }

    function newDispensation() {
        $this->checkRole('INVENTORY_DISPENSATION_CREATE');

        $medicineStocks = InventoryBusiness::calcMedicines($this->getVar('institution')["_id"], false);
		$this->setVar('stocks', $medicineStocks);

		$this->setVar('medicines', MedicineMdl::all());
    	$this->setVar('route', $this->routeToReturn);
        
    	$this->setResponse($this->path . '/form-new.html');

    }

    function saveNewDispensation() {
        $this->checkRole('INVENTORY_DISPENSATION_CREATE');

        $dispensationMedicine = new DispensationMedicineMdl();
        $dispensationMedicine->status = DispensationMedicineMdl::$allowedStatus['DONE'];    
        $dispensationMedicine->maker()->attach(UserAccountMdl::first($this->getVar('user')["id"]));
        $dispensationMedicine->owner()->attach(InstitutionMdl::first($this->getVar('institution')["_id"]));
        $dispensationMedicine->observation = $_POST["observation"];

        foreach ($_POST["fld_quantity"] as $key => $value) {
            if($value == 0) {
                continue;
            }

            $item = new ItemMedicineQuantityMdl();
            $item->medicine_id = (int) $key;
            $item->quantity = (int) $value;

            $dispensationMedicine->item()->add($item);
        }

        try {
            $dispensationMedicine->save();

            InventoryBusiness::createDispensation($dispensationMedicine);

            $this->flash(URL_SITE . '/' . $this->routeToReturn, $this->getVar('lang')['action_success'], 'success');
        } catch (\Throwable $th) {
            $message = sprintf($this->getVar('lang')['action_generic_error'], $th->getMessage());
            $this->flash(URL_SITE . '/' . $this->route, $message, 'error');
        }

    }

}

