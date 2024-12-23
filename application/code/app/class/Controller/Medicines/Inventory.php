<?php
namespace App\Controller\Medicines;

use App\Engine\Controller;
use App\Business\Inventory as InventoryBusiness;
use App\Model\Medicine as MedicineMdl;
use App\Model\Institution as InstitutionMdl;
use App\Model\Batch as BatchMdl;
use App\Model\BatchMedicine as BatchMedicineMdl;
use App\Model\OrderMedicine as OrderMedicineMdl;

class Inventory extends Controller {
    
	public $route = "/medicines/inventory";
	public $path = "medicines/inventory";

    function __construct() {
        parent::__construct();
        
		$this->setVar('institutions_change', InstitutionMdl::byParentAdministrativeUnit());

    }

    function overview() {

        $medicineStocks = InventoryBusiness::calcMedicines($this->getVar('institution')["_id"], false);
		$this->setVar('stocks', $medicineStocks);
		$this->setVar('orders_pending', OrderMedicineMdl::where([
            'institution_supplier' => $this->getVar('institution')["_id"],
            'status' => OrderMedicineMdl::$allowedStatus['OPEN']
        ]));
    	$this->setResponse($this->path . '/overview.html');

    }

    /*
        ######### Batch
    */

    function newBatch() {
        $this->checkRole('INVENTORY_BATCH_CREATE');

		$this->setVar('medicines', MedicineMdl::all());
    	$this->setResponse($this->path . '/batch/form-new.html');

    }

    function saveNewBatch() {
        $this->checkRole('INVENTORY_BATCH_CREATE');

        $batch = new BatchMdl();
        $batch->number = $_POST["number"];
        $batch->owner()->attach(InstitutionMdl::first($this->getVar('institution')["_id"]));

        foreach ($_POST["medicine_id"] as $key => $value) {
            $item = new BatchMedicineMdl();
            $item->medicine_id = (int) $value;
            $item->laboratory = $_POST["laboratory"][$key];
            $item->valid_date = $_POST["valid_date"][$key];
            $item->quantity = (int) $_POST["quantity"][$key];
            $item->unit_price = (float) $_POST["unit_price"][$key];

            $batch->medicine()->add($item);
        }

        try {
            $batch->save();
            InventoryBusiness::registerBatch($batch);

            $this->flash(URL_SITE . '/' . $this->route, $this->getVar('lang')['action_success'], 'success');
        } catch (\Throwable $th) {
            $message = sprintf($this->getVar('lang')['action_generic_error'], $th->getMessage());
            $this->flash(URL_SITE . '/' . $this->route, $message, 'error');
        }

    }

}

