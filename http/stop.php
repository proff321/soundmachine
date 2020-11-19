
<?php

require('../lib/socket.php');

header('Content-Type: application/json');

try {
	sendMessageToSocket('stop');
	echo json_encode([
		'success' => true,
		'message' => 'Sound machine stop requested',
	]);
} catch (Exception $e) {
	echo json_encode([
		'success' => false,
		'message' => $e->getMessage(),
	]);
}
