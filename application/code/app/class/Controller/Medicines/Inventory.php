<?php
namespace App\Controller\Medicines;

use App\Engine\Controller;
use App\Business\Inventory as InventoryBusiness;
use App\Model\Medicine as MedicineMdl;
use App\Model\Institution as InstitutionMdl;
use App\Model\Batch as BatchMdl;
use App\Model\BatchMedicine as BatchMedicineMdl;

class Inventory extends Controller {
    
	public $route = "/medicines/inventory";
	public $path = "medicines/inventory";

    function overview() {

        $medicineStocks = $this->calcMedicines($this->getVar('institution')["_id"], false);
		$this->setVar('stocks', $medicineStocks);
		$this->setVar('institutions_change', InstitutionMdl::byParentAdministrativeUnit());
    	$this->setResponse($this->path . '/overview.html');

    }

    private function calcMedicines($institutionId, $onlyAvailable = true) {

        $medicineStocks = [];
        foreach (InstitutionMdl::first($institutionId)->stocks as $line) {
            
            if(!array_key_exists($line->medicine_id, $medicineStocks)) {
                $medicineStocks[$line->medicine_id] = [
                    'medicine' => MedicineMdl::first($line->medicine_id),
                    'quantity' => 0,
                    'batches' => []
                ];
            }
            
            $batchData = BatchMdl::first($line->batch_id);
            $validDate = null;
            foreach ($batchData->medicine()->get() as $batchDataInfo) {
                if($batchDataInfo->medicine_id == $line->medicine_id) {
                    $validDate = $batchDataInfo->valid_date;
                    break;
                }
            }

            if($onlyAvailable && strtotime($validDate) > time()) {
                return;
            }

            $medicineStocks[$line->medicine_id]['quantity'] += $line->quantity;

            $medicineStocks[$line->medicine_id]['batches'][] = [
                'batch' => $batchData,
                'valid_date' => $validDate,
                'quantity' => $line->quantity
            ];
            
        }

        return $medicineStocks;

    }

    /*
        ######### Batch
    */

    function newBatch() {
		$this->setVar('medicines', MedicineMdl::all());
    	$this->setResponse($this->path . '/batch/form-new.html');

    }

    function saveNewBatch() {

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

    /*
        ######### Order
    */

    function newOrder() {

		$this->setVar('medicines', MedicineMdl::all());
    	$this->setResponse($this->path . '/order/form-new.html');

    }

    function saveNewOrder() {

        $batch = new OrderMdl();
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

    /*
        ######### Transfer
    */

    function newTransfer() {
        
        $medicineAvailableStocks = $this->calcMedicines($this->getVar('institution')["_id"], false);

		$this->setVar('medicines', MedicineMdl::all());
    	$this->setResponse($this->path . '/batch/form-new.html');

    }

    function saveNewTransfer() {

        $batch = new TransferMdl();
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

