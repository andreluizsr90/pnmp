<?php
namespace App\Model;

use App\Model\Internal\BaseModel;

class UserAccount extends BaseModel {
	
	protected $collection = 'user_account';

    public function profile()
    {
        return $this->referencesOne(UserProfile::class, 'profile_user');
    }

    public function institution()
    {
        return $this->referencesOne(Institution::class, 'institution_id');
    }

    public function unit_view()
    {
        return $this->referencesOne(AdministrativeUnit::class, 'unit_view_id');
    }
    
}