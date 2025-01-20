<?php
namespace App\Model;

use App\Model\Internal\BaseModel;

class DispensationMedicine extends BaseModel {

	public static $allowedStatus = [
        'DONE' => 'DONE',
        'CANCELED' => 'CANCELED',
    ];
	
	protected $collection = 'dispensation_medicine';

    public function owner()
    {
        return $this->referencesOne(Institution::class, 'institution_owner');
    }
    
    public function item()
    {
        return $this->embedsMany(ItemMedicineQuantity::class, 'medicines');
    }

    public function maker()
    {
        return $this->referencesOne(UserAccount::class, 'user_account');
    }
    
}