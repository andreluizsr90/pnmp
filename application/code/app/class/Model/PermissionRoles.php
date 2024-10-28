<?php
namespace App\Model;

use App\Model\Internal\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PermissionRoles extends BaseModel {
	
	protected $table = 'permission_roles';
	public $timestamps = false;

    public function permission(): HasOne
    {
        return $this->hasOne(Permission::class, 'id', 'permission_id');
    }

	
}