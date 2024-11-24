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

    public function convertToArray()
    {
        $dataConverted = $this->toArray();
        $dataConverted['address'] = $this->location->toArray();
        $dataConverted['administrative_unit'] = $this->unit->toArray();
        return $dataConverted;
    }

    public static function byParentAdministrativeUnit()
    {
        
        $aggregate = [];

        foreach (AdministrativeUnit::where(['parent_id' => ['$exists' => false]]) as $unit) {

            $institutions = [];
            $cursor = $unit->getCollection()->aggregate([
                ['$match' => ['parent_code_all' => $unit->code]], 
                ['$lookup' => ['from' => 'institution', 'localField' => '_id', 'foreignField' => 'administrative_unit', 'as' => 'institutions']],
                ['$project' => ['_id' => null, 'institutions' => 1]],
                ['$unwind' => ['path' => '$institutions', 'preserveNullAndEmptyArrays' => false]]
            ]);
            foreach ($cursor as $inst) {
                $institutions[] = $inst->institutions;
            }

            $unit->institutions = $institutions;

            $aggregate[] = $unit;

        }

        return $aggregate;
    }
	
}