<?php

require_once('logger.php');

function startSound($soundFilePath) {
	logger('Start requested');
	if (soundPlaying()) {
		logger('Sound is already playing');
		return;
	}
	logger('Running sound command');
	// Pipe to `:` which is a noop so that the `play` command no longer holds the process open
	$command = "play -q --volume 1 -c 1 ${soundFilePath} repeat - | : &";
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
