<?php
/* This is a simple client example for Statusengine
 * This example store all IP addresses in an text file.
 *
 * ATTENTION!!!
 * This example is coded out of my head and NOT TESTED but shoud work ^^ 
 *
 * ATTENTION again!!
 * This code contain an infinity loop, dont run this code over your webserver with http://abc.efg/example1.php
 * Run on the CLI with php5 example1.php and kill it with CTRL + C !
 *
 * Daniel Ziegler <daniel@statusengine.org>
 * License: MIT License 
 */


$StatusengineExample = new StatusengineExample1();

class StatusengineExample1{
	
	public function __construct(){
		//Define default constants, so its easier for us to work with the events
		$this->_constants();

		//open the text file, where we will wirte our ip addresses in
		$this->file = fopen('/tmp/statusengine_example', 'a+');

		$this->gmanClient = new GearmanClient();
		//add 127.0.0.1:4730
		$this->gmanClient->addServer();

		//Bind all queues
		$this->gmanClient->addFunction('statusngin_objects',					[$this, 'dumpObjects']);
		$this->gmanClient->addFunction('statusngin_servicestatus',				[$this, 'devNull']);
		$this->gmanClient->addFunction('statusngin_hoststatus',					[$this, 'devNull']);
		$this->gmanClient->addFunction('statusngin_servicechecks',				[$this, 'devNull']);
		$this->gmanClient->addFunction('statusngin_hostchecks',					[$this, 'devNull']);
		$this->gmanClient->AddFunction('statusngin_statechanges',				[$this, 'devNull']);
		$this->gmanClient->addFunction('statusngin_logentries',					[$this, 'devNull']);
		$this->gmanClient->addFunction('statusngin_systemcommands',				[$this, 'devNull']);
		$this->gmanClient->addFunction('statusngin_comments',					[$this, 'devNull']);
		$this->gmanClient->addFunction('statusngin_externalcommands',			[$this, 'devNull']);
		$this->gmanClient->addFunction('statusngin_acknowledgements',			[$this, 'devNull']);
		$this->gmanClient->addFunction('statusngin_flappings',					[$this, 'devNull']);
		$this->gmanClient->addFunction('statusngin_downtimes',					[$this, 'devNull']);
		$this->gmanClient->addFunction('statusngin_processdata',				[$this, 'devNull']);
		$this->gmanClient->addFunction('statusngin_notifications',				[$this, 'devNull']);
		$this->gmanClient->addFunction('statusngin_programmstatus',				[$this, 'devNull']);
		$this->gmanClient->addFunction('statusngin_contactstatus',				[$this, 'devNull']);
		$this->gmanClient->addFunction('statusngin_contactnotificationdata',	[$this, 'devNull']);
		$this->gmanClient->addFunction('statusngin_contactnotificationmethod',	[$this, 'devNull']);
		$this->gmanClient->addFunction('statusngin_eventhandler',				[$this, 'devNull']);
		
		//Tell the gearman client to work
		//This is an infinity look, kill with CTRL + C
		while($this->gmanClient->work());
	}

	//This function get called for everything inside of dumpObjects Q
	public function saveIp($job){
		$payload = json_decode($job->workload());
		//Check if this is a host, if yes we want to save the ip address in our file, if now we don't care about the data
		//The dumpObjects queu gets only refilled with an Naemon restart or reload!
		//If you want to run some tests without restarting Naemon use queue statusngin_servicechecks for example
		if($payload->object_type == OBJECT_HOST){
			fwrite($this->file, $payload->address.PHP_EOL);
		}
	}

	//We dont need this data, but we fetch it out of the gearman job server and throw it away
	//This avoids that some queues will grow up to an huge size in memory (RAM!)
	public function devNull($job){
		return;
	}

	public function _constants(){
		$constants = [
			'OBJECT_COMMAND'           => 12,
			'OBJECT_TIMEPERIOD'        =>  9,
			'OBJECT_CONTACT'           => 10,
			'OBJECT_CONTACTGROUP'      => 11,
			'OBJECT_HOST'              =>  1,
			'OBJECT_SERVICE'           =>  2,
			'OBJECT_HOSTGROUP'         =>  3,
			'OBJECT_SERVICEGROUP'      =>  4,
			'OBJECT_HOSTESCALATION'    =>  5,
			'OBJECT_SERVICEESCALATION' =>  6,
			'OBJECT_HOSTDEPENDENCY'    =>  7,
			'OBJECT_SERVICEDEPENDENCY' =>  8,
	
			'START_OBJECT_DUMP'     =>  100,
			'FINISH_OBJECT_DUMP'    =>  101,
		];
	
		foreach($constants as $key => $value){
			define($key, $value);
		}
	}
}