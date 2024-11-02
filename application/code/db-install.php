<?php

    require(__DIR__ . "/init-minimal.php");

    $mongoClient = (new \MongoDB\Client(CFG_DB, [], ['serverApi' => new \MongoDB\Driver\ServerApi(\MongoDB\Driver\ServerApi::V1)]));

    $mongoClient->pnmp->drop();

    $collAdministrativeUnit = $mongoClient->selectCollection('pnmp', 'administrative_unit');
    $collAdministrativeUnit->createIndexes([
        [ 'key' => [ 'code' => 1 ], 'unique' => true ],
    ]);
    $collAdministrativeUnit->createIndexes([
        [ 'key' => [ 'types' => 1 ] ],
    ]);

    $collInstitutions = $mongoClient->selectCollection('pnmp', 'institution');
    $collInstitutions->createIndexes([
        [ 'key' => [ 'code' => 1 ], 'unique' => true ],
    ]);
    $collInstitutions->createIndexes([
        [ 'key' => [ 'types' => 1 ] ],
    ]);
    
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
    