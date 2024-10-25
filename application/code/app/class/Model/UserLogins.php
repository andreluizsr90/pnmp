<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model as Eloquent;

class UserLogins extends Eloquent {
	
	protected $table = 'user_logins';
	public $timestamps = false;
	
}