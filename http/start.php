<?php

require('../lib/socket.php');

header('Content-Type: application/json');

try {
	sendMessageToSocket('start');
	echo json_encode([
		'success' => true,
		'message' => 'Sound machine start requested',
	]);
} catch (Exception $e) {
	echo json_encode([
		'success' => false,
		'message' => $e->getMessage(),
	]);
}
