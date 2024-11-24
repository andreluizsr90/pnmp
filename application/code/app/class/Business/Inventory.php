<?php
namespace App\Business;

use App\Model\Batch as BatchMdl;
use App\Model\Institution as InstitutionMdl;
use App\Model\InventoryBatchInformation as InventoryBatchInformationMdl;
use App\Model\InventoryHistory as InventoryHistoryMdl;
use App\Model\Medicine as MedicineMdl;

class Inventory {

    public static function registerBatch(BatchMdl $batch) {

        $history = new InventoryHistoryMdl();
        $history->batch()->attach($batch);
        $history->user_id = $_SESSION['user_account']["id"];

        $institution = InstitutionMdl::first($batch->institution);

        $history->stocks_old = $institution->stocks;

        // Register new values
        if(isset($institution->stocks) && is_array($institution->stocks)) {
            $stocks = $institution->stocks;
        } else {
            $stocks = [];
        }
        foreach ($batch->medicine()->get() as $stockPosition) {
            $batchInfo = new \stdClass();
            $batchInfo->batch_id = $batch->_id;
            $batchInfo->medicine_id = $stockPosition->medicine_id;
            $batchInfo->quantity = $stockPosition->quantity;

            $stocks[] = $batchInfo;
        }

        $institution->stocks = $stocks;
        $institution->save();
        
        $history->stocks_new = $stocks;
        $history->save();
    }
}