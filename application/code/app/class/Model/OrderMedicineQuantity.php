<?php
namespace App\Model;

class OrderMedicineQuantity extends \Mongolid\Model\AbstractModel {
	
    protected $collection = null;

	protected $fillable = [
        'quantity'
    ];

    public function medicine()
    {
        return $this->referencesOne(Medicine::class, 'medicine_id');
    }
	
}