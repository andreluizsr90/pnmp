<?php
namespace App\Model;

use App\Model\Internal\BaseModel;

class OrderMedicine extends BaseModel {

	public static $allowedStatus = [
        'OPEN' => 'OPEN',
        'APPROVED' => 'APPROVED',
        'SENT' => 'SENT',
        'DONE' => 'DONE',
        'CANCELED' => 'CANCELED',
    ];
	
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
        return $this->embedsMany(OrderMedicineQuantity::class, 'medicines');
    }
    
    public function item_approved()
    {
        return $this->embedsMany(OrderMedicineQuantity::class, 'medicines_approved');
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
	
}