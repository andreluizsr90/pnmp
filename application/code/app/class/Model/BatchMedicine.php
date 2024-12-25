<?php
namespace App\Model;

class BatchMedicine extends \Mongolid\Model\AbstractModel {
	
    protected $collection = null;

	protected $fillable = [
        'laboratory',
        'valid_date',
        'quantity',
        'unit_price'
    ];

    public function medicine()
    {
        return $this->referencesOne(Medicine::class, 'medicine_id');
    }
	
}