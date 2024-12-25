<?php

    require(__DIR__ . "/init-minimal.php");
    
    ini_set('memory_limit', -1);

    if($argc < 2) {
    	echo "Necessário informar um JOB ou os parametros corretamente! \n\n";
    	exit;
    }

    $jobName = $argv[1];
    if(empty($jobName)) {
    	echo "Necessário informar um JOB ou os parametros corretamente! \n\n";
    	exit;
    }

    $jobs = explode(",", $jobName);
    foreach ($jobs as $jobName) {
        $job = "App\Job\\" . $jobName;
        $job::launch();
    }

    