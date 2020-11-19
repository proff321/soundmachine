<?php

require('../lib/proc-info.php');

header('Content-Type: application/json');

echo json_encode([
	'status' => soundPlaying() ? 'on' : 'off',
	'processId' => soundPlaying() ? playProcessId() : null,
]);
