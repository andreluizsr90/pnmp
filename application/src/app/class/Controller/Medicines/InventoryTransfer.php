<?php
namespace App\Controller\Medicines;

use App\Business\Inventory as InventoryBusiness;
use App\Model\{
    Medicine as MedicineMdl,
    Institution as InstitutionMdl,
    TransferMedicine as TransferMedicineMdl,
    ItemMedicineQuantity as ItemMedicineQuantityMdl,
    UserAccount as UserAccountMdl
};
use App\Model\Enum\TransferMedicineStatus;

class InventoryTransfer extends InventoryBase {
    
	public $routeToReturn = "/medicines/inventory";
	public $route = "/medicines/inventory/transfer";
	public $path = "medicines/inventory/transfer";

    /*
        ######### Transfer
    */

    function overview() {

		$this->setVar('transfers', TransferMedicineMdl::where([
            'institution_owner' => $this->getVar('institution')["_id"]
        ]));
    	$this->setVar('route', $this->routeToReturn);
    	$this->setResponse($this->path . '/overview.html');

    }

    function newTransfer() {
        $this->checkRole('INVENTORY_TRANSFER_CREATE');

        $medicineStocks = InventoryBusiness::calcMedicines($this->getVar('institution')["_id"], false);
		$this->setVar('stocks', $medicineStocks);

        if(count($medicineStocks) == 0) {
            $this->flash(URL_SITE . '/' . $this->routeToReturn, $this->getVar('lang')['medicines_msg']['inventory_no_stocks'], 'error');
            exit;
        }

		$this->setVar('supplier', InstitutionMdl::first($this->getVar('institution')["institution_supplier"]));
		$this->setVar('medicines', MedicineMdl::all());
    	$this->setVar('route', $this->routeToReturn);
        
    	$this->setResponse($this->path . '/form-new.html');

    }

    function saveNewTransfer() {
        $this->checkRole('INVENTORY_TRANSFER_CREATE');
        
        if($_POST["institution_id"] == $this->getVar('institution')["_id"]) {
            $this->flash(URL_SITE . '/' . $this->route, $this->getVar('lang')['medicines_msg']['inventory_transfer_same_unit'], 'error');
            exit;
        }

        $institution = InstitutionMdl::first($this->getVar('institution')["_id"]);

        $transferMedicine = new TransferMedicineMdl();
        $transferMedicine->status = TransferMedicineStatus::OPEN;    
        $transferMedicine->maker()->attach(UserAccountMdl::first($this->getVar('user')["id"]));
        $transferMedicine->owner()->attach($institution);
        $transferMedicine->destination()->attach(InstitutionMdl::first((int) $_POST["institution_id"]));
        $transferMedicine->observation = $_POST["observation"];

        $existsTransfer = false;
        foreach ($_POST["fld_quantity"] as $key => $value) {
            if($value == 0) {
                continue;
            }

            if(!isset($institution->stocks[$key])) {
                continue;
            }

            if($institution->stocks[$key] < $value) {
                $this->flash(URL_SITE . '/' . $this->route, $this->getVar('lang')['medicines_msg']['inventory_no_stocks'], 'error');
                exit;
            }

            $item = new ItemMedicineQuantityMdl();
            $item->medicine_id = (int) $key;
            $item->quantity = (int) $value;

            $transferMedicine->item()->add($item);

            $existsTransfer = false;
        }

        try {
            $transferMedicine->save();

            InventoryBusiness::createTransfer($transferMedicine);

            $this->flash(URL_SITE . '/' . $this->routeToReturn, $this->getVar('lang')['action_success'], 'success');
        } catch (\Throwable $th) {
            $message = sprintf($this->getVar('lang')['action_generic_error'], $th->getMessage());
            $this->flash(URL_SITE . '/' . $this->route, $message, 'error');
        }

    }

    function overviewReceivingTransfer() {
        $this->checkRole('INVENTORY_TRANSFER_RECEIVE');

		$this->setVar('transfers', TransferMedicineMdl::where([
            'institution_destination' => $this->getVar('institution')["_id"],
            'status' => TransferMedicineStatus::OPEN
        ]));

    	$this->setVar('route', $this->routeToReturn);
    	$this->setResponse($this->path . '/overview-receiving.html');

    }

    function handleReceivingTransfer($id) {
        $this->checkRole('INVENTORY_TRANSFER_RECEIVE');

        $transferMedicine = TransferMedicineMdl::where([
            'institution_destination' => $this->getVar('institution')["_id"],
            'status' => TransferMedicineStatus::OPEN,
            '_id' => $id
        ])->first();

        if(empty($transferMedicine) || OrderMedicineStatus::from($transferMedicine->status) != TransferMedicineStatus::OPEN) {
            $this->flash(URL_SITE . '/' . $this->routeToReturn, $this->getVar('lang')['medicines_msg']['inventory_no_order'], 'error');
        }

        $transferMedicine->status = TransferMedicineStatus::DONE;
        $transferMedicine->received_at = new \MongoDB\BSON\UTCDateTime();
        $transferMedicine->receiver()->attach(UserAccountMdl::first($this->getVar('user')["id"]));

        try {
            $transferMedicine->save();

            InventoryBusiness::receiveTransfer($transferMedicine);

            $this->flash(URL_SITE . '/' . $this->routeToReturn, $this->getVar('lang')['action_success'], 'success');
        } catch (\Throwable $th) {
            $message = sprintf($this->getVar('lang')['action_generic_error'], $th->getMessage());
            $this->flash(URL_SITE . '/' . $this->route, $message, 'error');
        }

    }

}