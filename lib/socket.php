<?php

require_once('logger.php');

const SOCKET_NAME = '/home/pi/soundmachine/soundmachine.socket';


function createSocket() {
	$socket = socket_create(AF_UNIX, SOCK_STREAM, getprotobyname('icmp'));

	if ($socket === false) {
		throw new Exception('Unable to create socket');
	}


	removeSocketFile();  // May exist due to power failure
	$bindResult = socket_bind($socket, SOCKET_NAME);

	// Update the socket permissions so Apache can write to it
	chmod(SOCKET_NAME, 0777);

	if (!$bindResult) {
		throw new Exception('Unable to bind socket');
	}

	$listenResult = socket_listen($socket);

	if (!$listenResult) {
		throw new Exception('Unable to bind socket');
	}

	logger('Socket created with name: ' . SOCKET_NAME);
	return $socket;
}

function removeSocketFile(): void {
	if (file_exists(SOCKET_NAME)) {
		unlink(SOCKET_NAME);
	}
}

function cleanupSocket($socket) {
	socket_close($socket);

	removeSocketFile();
	
	logger('Finished cleaning up socket');
}

function sendMessageToSocket(string $message): void {
	$socket = socket_create(AF_UNIX, SOCK_STREAM, getprotobyname('icmp'));

	if ($socket === false) {
		throw new Exception('Unable to create socket');
	}

	$connectionResult = socket_connect($socket, SOCKET_NAME);

	if ($connectionResult === false) {
		throw new Exception('Unable to connect to socket');
	}

	$message = $message . PHP_EOL;
	$writeResult = socket_write($socket, $message, strlen($message));

	if ($writeResult === false) {
		throw new Exception('Unable to write to socket');
	}

	socket_close($socket);
}
