<?php


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
