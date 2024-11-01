<?php
namespace App\Model;

use App\Model\Internal\BaseModel;

class UserProfile extends BaseModel {
	
	protected $collection = 'user_profile';
    /**
     * Get the comments for the blog post.
     */
    public function users()
    {
        return $this->referencesMany(UserAccount::class, 'users');
    }
	
}