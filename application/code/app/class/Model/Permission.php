<?php
namespace App\Model;

use App\Model\Internal\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Permission extends BaseModel {
	
	protected $table = 'permission';
	public $timestamps = false;

    /**
     * Get the comments for the blog post.
     */
    public function users(): HasMany
    {
        return $this->hasMany(UserAccount::class, 'permission_id', 'id');
    }
	
}