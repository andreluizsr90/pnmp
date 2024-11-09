<?php
namespace App\Model;

class Address extends \Mongolid\Model\AbstractModel {
	
    protected $collection = null;

	protected $fillable = [
        'line_1',
        'line_2',
        'city',
        'postal_code'
    ];
	
}