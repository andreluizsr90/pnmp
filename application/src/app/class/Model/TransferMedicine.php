<?php
namespace App\Model;

use App\Model\Internal\BaseModel;

class TransferMedicine extends BaseModel {

	public static $allowedStatus = [
        'OPEN' => 'OPEN',
        'DONE' => 'DONE',
        'CANCELED' => 'CANCELED',
    ];
	
	protected $collection = 'transfer_medicine';

    public function owner()
    {
        return $this->referencesOne(Institution::class, 'institution_owner');
    }

    public function destination()
    {
        return $this->referencesOne(Institution::class, 'institution_destination');
    }
    
    public function item()
    {
        return $this->embedsMany(ItemMedicineQuantity::class, 'medicines');
    }

    public function maker()
    {
        return $this->referencesOne(UserAccount::class, 'user_account');
    }

    public function receiver()
    {
        return $this->referencesOne(UserAccount::class, 'user_account_receiver');
    }
	
}