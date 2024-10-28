<?php

  session_start();

  require __DIR__ . '/init-minimal.php';
  
  /**
  * Route Configuration
  */
  // try {
    $requestUrl = explode("?", $_SERVER['REQUEST_URI'])[0];
    if(URL_SUBFOLDER != false) {
      $requestUrl = str_replace(URL_SUBFOLDER, "", $requestUrl);
    }
    $requestUrl = str_replace("//", "/", $requestUrl);

    define('REQUEST_ROUTE', $requestUrl);
    define('REQUEST_METHOD', $_SERVER['REQUEST_METHOD']);

    if (empty($_SESSION['token'])) {
      $_SESSION['token'] = bin2hex(random_bytes(32));
    }

    $router = new Phroute\Phroute\RouteCollector();
    
    require PATH_APP . '/routes/main.php';

    $dispatcher = new Phroute\Phroute\Dispatcher($router->getData());
    $response = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], REQUEST_ROUTE);

    /*
  } catch (\Throwable $th) {
    
    $error = "\n\n" . $th->getFile() . " - Line: ". $th->getLine() . " - " . $th->getMessage() . "\n";
    $error .= $th->getTraceAsString();

    echo $error;
    exit;

    file_put_contents(CFG_FILE_LOG, $error, FILE_APPEND);

    header("location: " . URL_SITE);
  }
*/