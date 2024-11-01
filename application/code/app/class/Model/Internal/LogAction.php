<?php
namespace App\Model\Internal;

class LogAction extends \Mongolid\Model\AbstractModel {

    protected $collection = 'log_action';

    public $timestamps = false;  

    protected $fillable = [
        'entity_id',    // ID of the affected entity
        'entity_type',  // Type of entity (e.g., "User", "Product")
        'action',       // Action type (e.g., "create", "update", "delete")
        'user_id',      // ID of the user who performed the action
        'timestamp',    // Time when the action occurred
        'data'          // Optional: changed data for update actions
    ];

}