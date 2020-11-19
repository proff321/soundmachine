<?php
require('./lib/logger.php');
require('./lib/proc-info.php');
require('./lib/socket.php');
require('./lib/sound.php');
require('./lib/state.php');

pcntl_async_signals(true);

class Process {

	private $running = true;
	private $socket;

	private function bootup() {
		// Handle a power failure
		if (existingState()) {
			syncState(getState());
		}

		// $this->registerSignalHandlers();
		$this->socket = createSocket();
	}

	private function shutdown() {
		stopSound();
		cleanupState();
		cleanupSocket($this->socket);
	}

	/**
	 * This method does **NOT** work yet.
	 */
	private function registerSignalHandlers() {
		$handler = function($signo, $sigInfo) {
			$this->run = false;
		};

		pcntl_signal(SIGINT, $handler);
		pcntl_signal(SIGTERM, $handler);
		pcntl_signal(SIGALRM, $handler);
	}
	 

	public function run() {
		logger('Starting sync service', true);

		$this->bootup();

		// TODO:  Make this stop gracefully
		// Reference: https://stackoverflow.com/a/26934307
		// while($this->run) {
		while(true) {
			$commSocket = socket_accept($this->socket);

			if($commSocket === false) {
				throw new Exception('Unable to accept a connection on socket');
			}

			$requestedState = socket_read($commSocket, 7, PHP_NORMAL_READ);
			$requestedState = trim($requestedState);
			logger('New requested state received: ' . $requestedState);
			syncState($requestedState);
			socket_close($commSocket);
		}

		$this->shutdown();
		logger('Stopped sync service', true);
	}

}

$process = new Process();
$process->run();
