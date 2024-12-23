<?php
namespace App\Business;

use App\Model\Batch as BatchMdl;
use App\Model\Institution as InstitutionMdl;
use App\Model\InventoryBatchInformation as InventoryBatchInformationMdl;
use App\Model\InventoryHistory as InventoryHistoryMdl;
use App\Model\OrderMedicine as OrderMedicineMdl;
use App\Model\Medicine as MedicineMdl;

class Inventory {


    public static  function allMedicinesByIdKey() {

        $medicines = [];
        foreach (MedicineMdl::all() as $line) {
            $medicines[$line->_id] = $line;
        }

        return $medicines;

    }

    public static  function calcMedicines($institutionId, $onlyAvailable = true) {

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

    public static function registerBatch(BatchMdl $batch) {

        $history = new InventoryHistoryMdl();
        $history->batch()->attach($batch);
        $history->type = InventoryHistoryMdl::$types['BATCH'];
        $history->user_id = $_SESSION['user_account']["id"];

        $institution = InstitutionMdl::first($batch->institution);
        $history->institution()->attach($institution);

        // Register new values
        if(isset($institution->stocks) && is_array($institution->stocks)) {
            $history->stocks_old = array_map(fn ($o) => clone $o, $institution->stocks); // Clone array
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

    public static function approveOrder(OrderMedicineMdl $order) {

        $history = new InventoryHistoryMdl();
        $history->order()->attach($order);
        $history->type = InventoryHistoryMdl::$types['ORDER_APPROVED'];
        $history->user_id = $_SESSION['user_account']["id"];

        $institution = InstitutionMdl::first($order->institution_supplier);
        $history->institution()->attach($institution);

        $history->stocks_old = array_map(fn ($o) => clone $o, $institution->stocks); // Clone array

        // Register new values
        $stocks = $institution->stocks;
        $stocksLocked = [];

        foreach ($order->item_approved()->get() as $aprovedItem) {
            $quantityToRemove = $aprovedItem->quantity;
            foreach ($stocks as $sKey => $inStock) {
                if($quantityToRemove > 0 && $inStock->medicine_id == $aprovedItem->medicine_id) {
                        
                    $stockPosition = new \stdClass();
                    $stockPosition->batch_id = $inStock->batch_id;
                    $stockPosition->medicine_id = $inStock->medicine_id;
                    $stockPosition->order_id = $order->_id;

                    if($inStock->quantity > $quantityToRemove) {
                        $stockPosition->quantity = $quantityToRemove;
                        $inStock->quantity -= $quantityToRemove;
                        $quantityToRemove = 0;
                    } else {
                        $stockPosition->quantity = $inStock->quantity;
                        $inStock->quantity -= $inStock->quantity;
                        $quantityToRemove -= $inStock->quantity;  
                    }

                    $stocksLocked[] = $stockPosition;
                }
            }
        }

        $institution->stocks_locked = $stocksLocked;
        $institution->stocks = $stocks;
        $institution->save();
        
        $history->stocks_new = $stocks;
        $history->save();
    }

    public static function receiveOrder(OrderMedicineMdl $order) {

        $history = new InventoryHistoryMdl();
        $history->order()->attach($order);
        $history->type = InventoryHistoryMdl::$types['ORDER_RECEIVED'];
        $history->user_id = $_SESSION['user_account']["id"];

        $institution = InstitutionMdl::first($order->institution_owner);
        $history->institution()->attach($institution);

        if(isset($institution->stocks) && is_array($institution->stocks)) {
            $history->stocks_old = array_map(fn ($o) => clone $o, $institution->stocks); // Clone array
            $stocks = $institution->stocks;
        } else {
            $stocks = [];
        }
        
        $institution_supplier = InstitutionMdl::first($order->institution_supplier);
        $lockedStocks = array_map(fn ($o) => clone $o, $institution_supplier->stocks_locked); // Clone array

        foreach ($lockedStocks as $lKey => $aprovedItem) {
            if($aprovedItem->order_id != $order->_id) {
                continue;
            }

            $quantityToAdd = $aprovedItem->quantity;
            $notExistsBatch = true;
            foreach ($stocks as $sKey => $inStock) {
                if($quantityToAdd > 0) {
                    if($inStock->medicine_id == $aprovedItem->medicine_id && $inStock->batch_id == $aprovedItem->batch_id) {
                        $stocks[$sKey]->quantity += $quantityToAdd;
                        $notExistsBatch = false;
                    }
                }
            }

            if($notExistsBatch) {
                $stockPosition = new \stdClass();
                $stockPosition->batch_id = $aprovedItem->batch_id;
                $stockPosition->medicine_id = $aprovedItem->medicine_id;
                $stockPosition->quantity = $quantityToAdd;
                $stocks[] = $stockPosition;
            }

            unset($lockedStocks[$lKey]);
        }
        
        $institution->stocks = $stocks;
        $institution->save();
        
        $institution_supplier->stocks_locked = $lockedStocks;
        $institution_supplier->save();
        
        $history->stocks_new = $stocks;
        $history->save();
    }
}