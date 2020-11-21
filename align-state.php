<?php
require('./lib/logger.php');
require('./lib/proc-info.php');
require('./lib/socket.php');
require('./lib/sound.php');
require('./lib/state.php');

class Process {


	private $run = true;
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

	private function handleRequest($commSocket): void {
		if($commSocket === false) {
			throw new Exception('Unable to accept a connection on socket');
		}

		$requestedState = trim(
			socket_read($commSocket, 8, PHP_NORMAL_READ)
		);
		logger('New requested state received: ' . $requestedState);

		socket_close($commSocket);

		if ($requestedState === States::SHUTDOWN) {
			$this->run = false;
		} else {
			syncState($requestedState);
		}
	}

	public function run() {
		logger('Starting sync service', true);

		$this->bootup();

		while($this->run) {
			$this->handleRequest(socket_accept($this->socket));
		}

		$this->shutdown();
		logger('Stopped sync service', true);
	}

}

$process = new Process();
$process->run();
