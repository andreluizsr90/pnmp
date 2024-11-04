<?php
namespace App\Model;

use App\Model\Internal\BaseModel;

class UserAccount extends BaseModel {
	
	protected $collection = 'user_account';

    /**
     * Get the phone associated with the user.
     */
    public function profile()
    {
        return $this->referencesOne(UserProfile::class, 'profile_user');
    }

    /**
     * Get the phone associated with the user.
     */
    public function institution()
    {
        return $this->referencesOne(Institution::class, 'institution_id');
    }
    
}