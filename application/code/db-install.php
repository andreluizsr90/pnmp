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
    $user->is_active = true;
    $user->password = \App\Engine\HelperUtil::passwordGenerate("123");
    $user->save();
    
    $user->profile()->attach($profile);
    $user->save();
    