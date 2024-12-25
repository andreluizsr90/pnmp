<?php

  require __DIR__ . '/vendor/autoload.php';
  require __DIR__ . '/config.php';

  /**
  * Database Configuration
  */
  # Configuração de conexão com a base de dados
  use Mongolid\Connection\Manager;
  use Mongolid\Connection\Connection;
  
  $connection = new Connection(CFG_DB);
  $connection->defaultDatabase = 'pnmp';

  $manager = new Manager();
  $manager->setConnection($connection);