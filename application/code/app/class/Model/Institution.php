<?php
namespace App\Model;

use App\Model\Internal\BaseModel;

class Institution extends BaseModel {
	
	protected $collection = 'institution';

    /**
     * Get the phone associated with the user.
     */
    public function unit()
    {
        return $this->referencesOne(AdministrativeUnit::class, 'administrative_unit');
    }

    /**
     * Get the phone associated with the user.
     */
    public function supplier()
    {
        return $this->referencesOne(Institution::class, 'institution_supplier');
    }
	
}