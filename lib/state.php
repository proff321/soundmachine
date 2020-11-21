<?php

require_once('sound.php');

const STATE_FILE = '/tmp/requested-state';
const SOUND_FILE = '../sounds/audiocheck.net_white_192k_-3dBFS.wav';

class States {
	const START = 'start';
	const STOP = 'stop';
	const SHUTDOWN = 'shutdown';
}

function getSoundFilePath(): string {
	return dirname(__FILE__) . '/' . SOUND_FILE;
}

function trackLastState(string $state) {
	file_put_contents(STATE_FILE, $state);
}

function getState(): string {
	return trim(file_get_contents(STATE_FILE));
}

function existingState(): bool {
	return file_exists(STATE_FILE);
}

function cleanupState() {
	if (existingState()){
		unlink(STATE_FILE);
	}
}

function syncState(string $requestedState) {

	trackLastState($requestedState);
	if ($requestedState === States::START) {
		startSound(getSoundFilePath());
	}

	if ($requestedState === States::STOP) {
		stopSound();
	}
}
