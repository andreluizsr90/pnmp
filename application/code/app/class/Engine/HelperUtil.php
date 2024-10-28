<?php
namespace App\Engine;

class HelperUtil {

  public static function passwordGenerate($password) {
    return password_hash($password, PASSWORD_DEFAULT);
  }

  public static function passwordCheck($password, $hash) {
    return password_verify($password, $hash);
  }

  public static function isUserAllowed(string $role) {
    if(isset($_SESSION['user_account'])) {
      if(!in_array('DEV', $_SESSION['user_account']["roles"]) && !in_array($role, $_SESSION['user_account']["roles"])) {
        return false;
      }

      return true;
    } else {
      return false;
    }

    return true;
  }

}