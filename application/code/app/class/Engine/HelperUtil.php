<?php
namespace App\Engine;

class HelperUtil {

  public static function passwordGenerate($password) {
    return password_hash($password, PASSWORD_DEFAULT);
  }

  public static function passwordCheck($password, $hash) {
    return password_verify($password, $hash);
  }

}