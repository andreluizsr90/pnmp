<?php
namespace App\Model;

use App\Model\Internal\BaseModel;

class InventoryHistory extends BaseModel {
	
	protected $collection = 'inventory_history';

    public function batch()
    {
        return $this->referencesOne(Batch::class, 'batch_id');
    }

    public function institutionInfo()
    {
        return $this->referencesOne(Institution::class, 'institution');
    }
	
}