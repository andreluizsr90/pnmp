<?php
namespace App\Controller\Medicines;

use App\Business\Inventory as InventoryBusiness;
use App\Model\{
    Address as AddressMdl,
    Medicine as MedicineMdl,
    Institution as InstitutionMdl,
    OrderMedicine as OrderMedicineMdl,
    ItemMedicineQuantity as ItemMedicineQuantityMdl,
    UserAccount as UserAccountMdl
};
use App\Model\Enum\OrderMedicineStatus;

class InventoryOrder extends InventoryBase {
    
	public $routeToReturn = "/medicines/inventory";
	public $route = "/medicines/inventory/order";
	public $path = "medicines/inventory/order";

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
            $this->flash(URL_SITE . '/' . $this->routeToReturn, $this->getVar('lang')['medicines_msg']['inventory_no_supplier'], 'error');
        }

        if(OrderMedicineMdl::hasByStatus($this->getVar('institution')["_id"], OrderMedicineStatus::OPEN)) {
            $this->flash(URL_SITE . '/' . $this->routeToReturn, $this->getVar('lang')['medicines_msg']['inventory_exists_order_open'], 'error');
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
        $orderMedicine->status = OrderMedicineStatus::OPEN;
        $orderMedicine->maker()->attach(UserAccountMdl::first($this->getVar('user')["id"]));
        $orderMedicine->owner()->attach(InstitutionMdl::first($this->getVar('institution')["_id"]));
        $orderMedicine->supplier()->attach(InstitutionMdl::first($this->getVar('institution')["institution_supplier"]));

        $orderMedicine->delivery_institution_name = $_POST["institution_name"];
        $orderMedicine->delivery_cnpj = $_POST["cnpj"];
        $orderMedicine->delivery_name = $_POST["receiver_name"];
        $orderMedicine->delivery_phones = $_POST["phones"];
        $orderMedicine->delivery_email = $_POST["email"];
        
        $address = new AddressMdl();
        $address->line_1 = $_POST["line_1"];
        $address->line_2 = $_POST["line_2"];
        $address->city = $_POST["city"];
        $address->postal_code = $_POST["postal_code"];
        
        $orderMedicine->delivery_address = $address;
        $orderMedicine->delivery_administrative_unit = $_POST["administrative_unit"];

        foreach ($_POST["fld_quantity"] as $key => $value) {
            if($value == 0) {
                continue;
            }

            $item = new ItemMedicineQuantityMdl();
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
            'status' => OrderMedicineStatus::OPEN
        ]));
    	$this->setVar('route', $this->routeToReturn);
    	$this->setResponse($this->path . '/overview-pending.html');

    }

    function handlePendingOrder($id) {
        $this->checkRole('INVENTORY_ORDER_APPROVE');

		$this->setVar('order', OrderMedicineMdl::where([
            'institution_supplier' => $this->getVar('institution')["_id"],
            'status' => OrderMedicineStatus::OPEN,
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
            'status' => OrderMedicineStatus::OPEN,
            '_id' => $id
        ])->first();

        if(empty($orderMedicine) || OrderMedicineStatus::from($orderMedicine->status) != OrderMedicineStatus::OPEN) {
            $this->flash(URL_SITE . '/' . $this->routeToReturn, $this->getVar('lang')['medicines_msg']['inventory_no_order'], 'error');
        }

        if(isset($_POST["justification"]) && !empty($_POST["justification"])) {
            $this->refusePendingOrder($orderMedicine);
            exit;
        }

        $orderMedicine->status = OrderMedicineStatus::APPROVED;
        $orderMedicine->approved_at = new \MongoDB\BSON\UTCDateTime();
        $orderMedicine->approver()->attach(UserAccountMdl::first($this->getVar('user')["id"]));

        foreach ($_POST["fld_quantity"] as $key => $value) {
            if($value == 0) {
                continue;
            }

            $item = new ItemMedicineQuantityMdl();
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

    private function refusePendingOrder(OrderMedicineMdl $orderMedicine) {
        $orderMedicine->status = OrderMedicineStatus::CANCELED;
        $orderMedicine->canceled_at = new \MongoDB\BSON\UTCDateTime();
        $orderMedicine->canceller()->attach(UserAccountMdl::first($this->getVar('user')["id"]));

        try {
            $orderMedicine->observation = $_POST["justification"];
            $orderMedicine->save();

            // InventoryBusiness::refuseOrder($orderMedicine);

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
            'status' => OrderMedicineStatus::APPROVED
        ]));

    	$this->setVar('route', $this->routeToReturn);
    	$this->setResponse($this->path . '/overview-receiving.html');

    }

    function handleReceivingOrder($id) {
        $this->checkRole('INVENTORY_ORDER_RECEIVE');

        $orderMedicine = OrderMedicineMdl::where([
            'institution_owner' => $this->getVar('institution')["_id"],
            'status' => OrderMedicineStatus::APPROVED,
            '_id' => $id
        ])->first();

        $orderMedicine->status = OrderMedicineStatus::DONE;
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

