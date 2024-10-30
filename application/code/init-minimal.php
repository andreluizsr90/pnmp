<?php

  require __DIR__ . '/vendor/autoload.php';
  require __DIR__ . '/config.php';

  /**
  * Database Configuration
  */
  # Configuração de conexão com a base de dados
  $capsule = new \Illuminate\Database\Capsule\Manager;
  
  $capsule->addConnection(CFG_DB);

  $capsule->setEventDispatcher(
		new \Illuminate\Events\Dispatcher(new \Illuminate\Container\Container)
	);
  
  $capsule->setAsGlobal();
  $capsule->bootEloquent();