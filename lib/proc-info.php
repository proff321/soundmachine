<?php

function soundPlaying(): bool {
	$processInfo = exec('ps -u pi | grep play');
	return !empty($processInfo);
}

function playProcessId(): int {
	$processInfo = exec('ps -u pi | grep play');

	preg_match('/(\d+) .+play$/', $processInfo, $matches);

	if ($matches && count($matches) == 2) {
		$processId = $matches[1];
	} else {
		throw new Exception('Unable to locate play process Id');
	}

	return $processId;
}
