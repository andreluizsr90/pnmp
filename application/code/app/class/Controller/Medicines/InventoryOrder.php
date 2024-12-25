<?php
namespace App\Controller\Medicines;

use App\Engine\Controller;
use App\Business\Inventory as InventoryBusiness;
use App\Model\Medicine as MedicineMdl;
use App\Model\Institution as InstitutionMdl;
use App\Model\OrderMedicine as OrderMedicineMdl;
use App\Model\OrderMedicineQuantity as OrderMedicineQuantityMdl;
use App\Model\UserAccount as UserAccountMdl;

class InventoryOrder extends Controller {
    
	public $routeToReturn = "/medicines/inventory";
	public $route = "/medicines/inventory/order";
	public $path = "medicines/inventory/order";

    function __construct() {
        parent::__construct();
        
		$this->setVar('institutions_change', InstitutionMdl::byParentAdministrativeUnit());

    }

    /*
        ######### Order
    */

    function overview() {

		$this->setVar('orders', OrderMedicineMdl::where([
            'institution_owner' => $this->getVar('institution')["_id"]
        ]));
    	$this->setVar('route', $this->routeToReturn);
    	$this->setResponse($this->path . '/overview.html');

    }

    function newOrder() {
        $this->checkRole('INVENTORY_ORDER_CREATE');

        if(empty($this->getVar('institution')["institution_supplier"])) {
            $this->flash(URL_SITE . '/' . $this->routeToReturn, $this->getVar('lang')['inventory_no_supplier'], 'error');
        }

        $medicineStocks = InventoryBusiness::calcMedicines($this->getVar('institution')["_id"], false);
		$this->setVar('stocks', $medicineStocks);

		$this->setVar('supplier', InstitutionMdl::first($this->getVar('institution')["institution_supplier"]));
		$this->setVar('medicines', MedicineMdl::all());
    	$this->setVar('route', $this->routeToReturn);
    	$this->setResponse($this->path . '/form-new.html');

    }

    function saveNewOrder() {
        $this->checkRole('INVENTORY_ORDER_CREATE');

        $orderMedicine = new OrderMedicineMdl();
        $orderMedicine->status = OrderMedicineMdl::$allowedStatus['OPEN'];    
        $orderMedicine->maker()->attach(UserAccountMdl::first($this->getVar('user')["id"]));
        $orderMedicine->owner()->attach(InstitutionMdl::first($this->getVar('institution')["_id"]));
        $orderMedicine->supplier()->attach(InstitutionMdl::first($this->getVar('institution')["institution_supplier"]));

        foreach ($_POST["fld_quantity"] as $key => $value) {
            if($value == 0) {
                continue;
            }

            $item = new OrderMedicineQuantityMdl();
            $item->medicine_id = (int) $key;
            $item->quantity = (int) $value;

            $orderMedicine->item()->add($item);
        }

        try {
            $orderMedicine->save();

            $this->flash(URL_SITE . '/' . $this->routeToReturn, $this->getVar('lang')['action_success'], 'success');
        } catch (\Throwable $th) {
            $message = sprintf($this->getVar('lang')['action_generic_error'], $th->getMessage());
            $this->flash(URL_SITE . '/' . $this->route, $message, 'error');
        }

    }

    function overviewPendingOrder() {

		$this->setVar('orders', OrderMedicineMdl::where([
            'institution_supplier' => $this->getVar('institution')["_id"],
            'status' => OrderMedicineMdl::$allowedStatus['OPEN']
        ]));
    	$this->setVar('route', $this->routeToReturn);
    	$this->setResponse($this->path . '/overview-pending.html');

    }

    function handlePendingOrder($id) {
        $this->checkRole('INVENTORY_ORDER_APPROVE');

		$this->setVar('order', OrderMedicineMdl::where([
            'institution_supplier' => $this->getVar('institution')["_id"],
            'status' => OrderMedicineMdl::$allowedStatus['OPEN'],
            '_id' => $id
        ])->first());

        $medicineStocks = InventoryBusiness::calcMedicines($this->getVar('institution')["_id"], false);
		$this->setVar('stocks', $medicineStocks);

    	$this->setVar('route', $this->routeToReturn);
    	$this->setResponse($this->path . '/form-pending.html');

    }

    function savePendingOrder($id) {
        $this->checkRole('INVENTORY_ORDER_APPROVE');

        $orderMedicine = OrderMedicineMdl::where([
            'institution_supplier' => $this->getVar('institution')["_id"],
            'status' => OrderMedicineMdl::$allowedStatus['OPEN'],
            '_id' => $id
        ])->first();
        $orderMedicine->status = OrderMedicineMdl::$allowedStatus['APPROVED'];
        $orderMedicine->approved_at = new \MongoDB\BSON\UTCDateTime();
        $orderMedicine->approver()->attach(UserAccountMdl::first($this->getVar('user')["id"]));

        foreach ($_POST["fld_quantity"] as $key => $value) {
            if($value == 0) {
                continue;
            }

            $item = new OrderMedicineQuantityMdl();
            $item->medicine_id = (int) $key;
            $item->quantity = (int) $value;

            $orderMedicine->item_approved()->add($item);
        }

        try {
            $orderMedicine->approved_observation = $_POST["observation"];
            $orderMedicine->save();

            InventoryBusiness::approveOrder($orderMedicine);

            $this->flash(URL_SITE . '/' . $this->routeToReturn, $this->getVar('lang')['action_success'], 'success');
        } catch (\Throwable $th) {
            $message = sprintf($this->getVar('lang')['action_generic_error'], $th->getMessage());
            $this->flash(URL_SITE . '/' . $this->route, $message, 'error');
        }

    }

    function overviewReceivingOrder() {
        $this->checkRole('INVENTORY_ORDER_RECEIVE');

		$this->setVar('orders', OrderMedicineMdl::where([
            'institution_owner' => $this->getVar('institution')["_id"],
            'status' => OrderMedicineMdl::$allowedStatus['APPROVED']
        ]));

    	$this->setVar('route', $this->routeToReturn);
    	$this->setResponse($this->path . '/overview-receiving.html');

    }

    function handleReceivingOrder($id) {
        $this->checkRole('INVENTORY_ORDER_RECEIVE');

        $orderMedicine = OrderMedicineMdl::where([
            'institution_owner' => $this->getVar('institution')["_id"],
            'status' => OrderMedicineMdl::$allowedStatus['APPROVED'],
            '_id' => $id
        ])->first();

        $orderMedicine->status = OrderMedicineMdl::$allowedStatus['DONE'];
        $orderMedicine->received_at = new \MongoDB\BSON\UTCDateTime();
        $orderMedicine->receiver()->attach(UserAccountMdl::first($this->getVar('user')["id"]));

        try {
            $orderMedicine->save();

            InventoryBusiness::receiveOrder($orderMedicine);

            $this->flash(URL_SITE . '/' . $this->routeToReturn, $this->getVar('lang')['action_success'], 'success');
        } catch (\Throwable $th) {
            $message = sprintf($this->getVar('lang')['action_generic_error'], $th->getMessage());
            $this->flash(URL_SITE . '/' . $this->route, $message, 'error');
        }

    }

}

