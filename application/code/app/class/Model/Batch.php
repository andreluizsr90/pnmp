<?php
namespace App\Model;

use App\Model\Internal\BaseModel;

class Batch extends BaseModel {
	
	protected $collection = 'batch';

    public function owner()
    {
        return $this->referencesOne(Institution::class, 'institution');
    }
    
    public function medicine()
    {
        return $this->embedsMany(BatchMedicine::class, 'medicines');
    }
	
}