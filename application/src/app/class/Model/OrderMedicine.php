<?php
namespace App\Model;

use App\Model\Internal\BaseModel;
use App\Model\Enum\OrderMedicineStatus;

class OrderMedicine extends BaseModel {
	
	protected $collection = 'order_medicine';

    public function owner()
    {
        return $this->referencesOne(Institution::class, 'institution_owner');
    }

    public function supplier()
    {
        return $this->referencesOne(Institution::class, 'institution_supplier');
    }
    
    public function item()
    {
        return $this->embedsMany(ItemMedicineQuantity::class, 'medicines');
    }
    
    public function item_approved()
    {
        return $this->embedsMany(ItemMedicineQuantity::class, 'medicines_approved');
    }

    public function maker()
    {
        return $this->referencesOne(UserAccount::class, 'user_account');
    }

    public function approver()
    {
        return $this->referencesOne(UserAccount::class, 'user_account_approved');
    }

    public function receiver()
    {
        return $this->referencesOne(UserAccount::class, 'user_account_receiver');
    }

    public function canceller()
    {
        return $this->referencesOne(UserAccount::class, 'user_account_canceled');
    }

    public static function hasByStatus($institutionId, OrderMedicineStatus $status)
    {
		return OrderMedicine::where([
            'institution_owner' => $institutionId,
            'status' => $status
        ])->count() > 0;
    }
	
}