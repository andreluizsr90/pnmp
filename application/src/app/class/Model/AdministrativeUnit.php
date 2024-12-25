<?php
namespace App\Model;

use App\Model\Internal\BaseModel;

class AdministrativeUnit extends BaseModel {
	
	protected $collection = 'administrative_unit';

    public function parent()
    {
        return $this->referencesOne(AdministrativeUnit::class, 'parent_id');
    }
	
}