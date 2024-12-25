<?php
namespace App\Job;

class ExampleJob {

    public static function launch() {
        echo PHP_EOL;
        echo "### Start Example ### " . PHP_EOL;
        $startedTime = microtime(true);

        $job = new self();
        $job->execute();

        echo "%%%%%% Seconds processing: " . ceil(microtime(true) - $startedTime) . PHP_EOL;

    }
	
	public function execute() {

        echo "### ----- Finished ### " . PHP_EOL;
    }
	
}