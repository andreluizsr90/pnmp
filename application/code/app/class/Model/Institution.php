<?php
namespace App\Model;

use App\Model\Internal\BaseModel;

class Institution extends BaseModel {
	
	protected $collection = 'institution';
    
    public function location()
    {
        return $this->embedsOne(Address::class, 'address');
    }

    public function unit()
    {
        return $this->referencesOne(AdministrativeUnit::class, 'administrative_unit');
    }

    public function supplier()
    {
        return $this->referencesOne(Institution::class, 'institution_supplier');
    }
	
}