<?php
namespace App\Controller;

use App\Engine\Controller;
use App\Engine\HelperUtil;
use App\Model\UserAccount as UserAccountMdl;

class Auth extends Controller {
	private $route = '/login';
	public function logIn() {
		$this->setResponse('auth\login.html');
	}

    public function logInAction() {
		
    	$email = isset($_POST['email']) ? $_POST['email'] : null;
    	$password = isset($_POST['password']) ? $_POST['password'] : null;

    	if(is_null($email) || is_null($password)) {
			$this->flash(URL_SITE . $route, $this->getVar('lang')['user_invalid_login'], 'error');
    	}

    	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$this->flash(URL_SITE . $route, $this->getVar('lang')['user_invalid_login'], 'error');
    	}

    	$user = UserAccountMdl::where('email', $email)->first();
    	if (is_null($user)) {
			$this->flash(URL_SITE . $route, $this->getVar('lang')['user_invalid_login'], 'error');
    	}

    	if (!HelperUtil::passwordCheck($password, $user->password)) {
			$this->flash(URL_SITE . $route, $this->getVar('lang')['user_invalid_login'], 'error');
		}

    	$_SESSION['user_account'] = [
			'id' => $user->id,
			'name' => $user->name,
			'email' => $user->email,
			'roles' => $user->roles(),
		];

    	header('Location: ' . URL_SITE);


    }

    public function logOff() {

    	session_destroy();
	    header('Location: ' . URL_SITE);
    }

}

