<?php
require('/var/www/html/proc-info.php');
pcntl_async_signals(true);

function quietMode(): bool {
	global $argv;

	if(count($argv) > 1 && $argv[1] === '--quiet') {
		return true;
	}

	return false;
}

function logger($msg, $force = false) {

	if (quietMode() && !$force) {
		return;
	}

	echo 'SOUND-MACHINE::' . $msg . PHP_EOL;
}

function startSound() {
	logger('Start requested');
	if (soundPlaying()) {
		logger('Sound is already playing');
		return;
	}
	logger('Running sound command');
	// Pipe to `:` which is a noop so that the `play` command no longer holds the process open
	$command = 'play -q --volume 1 -c 1 /var/www/html/audiocheck.net_white_192k_-3dBFS.wav repeat - | : &';
	exec($command);
}

function stopSound() {
	logger('Stop requested');
	if (!soundPlaying()) {
		logger('Sound is not playing');
		return;
	}

	logger('Sending stop command');
	$processId = playProcessId();
	$command ="kill ${processId}";
	exec($command);
}

function syncState() {

	// TODO: Redesign so it doesn't kill the SD card
	$requestedState = trim(file_get_contents('/var/www/html/requested-state'));
	logger('Requested state: ' . $requestedState);

	logger('Evaluating state change');
	if ($requestedState == 'start') {
		startSound();
	}


	if ($requestedState == 'stop') {
		stopSound();
	}

}


function main() {
	logger('Starting sync service', true);

	$run = true;

	$handleStop = function($signo, $sigInfo) use (&$run) {
		logger('Stop signal received');
		// Stop the sound process and then end the run loop
		$run = false;
	};

	// Register an event handler for SIGNTERM
	pcntl_signal(SIGINT, $handleStop);
	pcntl_signal(SIGTERM, $handleStop);

	// See if this can wait on a socket instead.
	while($run) {
		syncState();
		logger('State synced, sleeping');
		sleep(1);
	}

	stopSound();
	logger('Stopped sync service', true);
}

main();
