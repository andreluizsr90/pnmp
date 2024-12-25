<?php
namespace App\Model;

use App\Model\Internal\BaseModel;

class InventoryHistory extends BaseModel {

    public static $types = [
        'BATCH' => 'BATCH',
        'ORDER_APPROVED' => 'ORDER_APPROVED',
        'ORDER_RECEIVED' => 'ORDER_RECEIVED',
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
	
}