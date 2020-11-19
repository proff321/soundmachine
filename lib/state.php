<?php

require_once('sound.php');

const STATE_FILE = 'requested-state';
const SOUND_FILE = '../sounds/audiocheck.net_white_192k_-3dBFS.wav';

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
	unlink(STATE_FILE);
}

function syncState(string $requestedState) {

	trackLastState($requestedState);
	if ($requestedState == 'start') {
		startSound(getSoundFilePath());
	}

	if ($requestedState == 'stop') {
		stopSound();
	}
}
