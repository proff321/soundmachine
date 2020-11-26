function updateStatus() {
	soundmachine.status()
		.then( status => {
			document
				.getElementById('status')
				.innerHTML = status.status
		})
		.catch( error => console.log(error) )
}

// Check the sound machine status every second
const statusUpdateIntervalId = window.setInterval(updateStatus, 1000)
