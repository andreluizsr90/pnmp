<?php
namespace App\Model;

use App\Model\Internal\BaseModel;

class AdministrativeUnit extends BaseModel {
	
	protected $collection = 'administrative_unit';

    public function getParent()
    {
        return !is_null($this->parent_code) ? AdministrativeUnit::where(['code' => $this->parent_code])->first() : null;
    }
	
}