<?php

    require(__DIR__ . "/init-minimal.php");
    
    $profile = new \App\Model\UserProfile();
    $profile->name = 'Developer';
    $profile->is_internal = true;
    $profile->roles = ['DEV'];
    $profile->save();
    
    $user = new \App\Model\UserAccount();
    $user->name = 'André Rodrigues';
    $user->email = 'andreluizweb@gmail.com';
    $user->password = '$2y$10$V7J3r.1hOJ45HTxr4nhqvuqyFbOZzgskY6QTaRw7X2gBF/bVjTx8.'; // teste123
    $user->save();
    
    $user->profile()->attach($profile);
    $user->save();

    $mongoClient = (new \MongoDB\Client(CFG_DB, [], ['serverApi' => new \MongoDB\Driver\ServerApi(\MongoDB\Driver\ServerApi::V1)]));

    $collection = $mongoClient->selectCollection('pnmp', 'administrative_unit');
    $indexName = $collection->createIndexes([
        [ 'key' => [ 'code' => 1 ], 'unique' => true ],
    ]);

    //$mongoClient->pnmp->drop();
    