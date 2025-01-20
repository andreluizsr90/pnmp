<?php
namespace App\Model;

use App\Model\Internal\BaseModel;

class InventoryHistory extends BaseModel {

    public static $types = [
        'BATCH' => 'BATCH',
        'ORDER_APPROVED' => 'ORDER_APPROVED',
        'ORDER_RECEIVED' => 'ORDER_RECEIVED',
        'TRANSFER_CREATED' => 'TRANSFER_CREATED',
        'TRANSFER_RECEIVED' => 'TRANSFER_RECEIVED',
        'DISPENSATION_CREATED' => 'DISPENSATION_CREATED'
    ];
	
	protected $collection = 'inventory_history';

    public function institution()
    {
        return $this->referencesOne(Institution::class, 'institution_id');
    }

    public function batch()
    {
        return $this->referencesOne(Batch::class, 'batch_id');
    }

    public function order()
    {
        return $this->referencesOne(OrderMedicine::class, 'order_id');
    }

    public function transfer()
    {
        return $this->referencesOne(TransferMedicine::class, 'transfer_id');
    }

    public function dispensation()
    {
        return $this->referencesOne(DispensationMedicine::class, 'dispensation_id');
    }
	
}