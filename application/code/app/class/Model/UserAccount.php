<?php
namespace App\Model;

use App\Model\Internal\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserAccount extends BaseModel {
	
	protected $table = 'user_account';
	public $timestamps = false;

    /**
     * Get the phone associated with the user.
     */
    public function permission(): HasOne
    {
        return $this->hasOne(Permission::class, 'id', 'permission_id');
    }

    /**
     * Get the phone associated with the user.
     */
    public function roles() : array
    {
        $roles = [];
        foreach (PermissionRoles::where('permission_id', $this->permission_id)->get() as $key => $value) {
            $roles[] = $value->role;
        }
        return $roles;
    }
	
}