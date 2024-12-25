<?php
namespace App\Model\Internal;

class Sequence extends \Mongolid\Model\AbstractModel
{
    protected $collection = 'sequence';

    public $timestamps = false;  

    protected $fillable = [
        'entity',
        'sequence'
    ];
}