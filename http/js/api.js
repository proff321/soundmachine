const soundmachine = {

	generateReponseHandler: function(http) {
		return new Promise( (res, rej) => {
			http.onreadystatechange = () => {
				this.responseHandler(res, rej, http)
			}
		})
	},

	responseHandler: function (res, rej, http) {
		if (http.readyState === XMLHttpRequest.DONE) {
			if (http.status === 200) {
				const data = JSON.parse(http.responseText)
				res(data)
			} else {
				rej('Server responded with status: ' + http.status)
			}
		}
	},


	status: function() {
		const http = new XMLHttpRequest()
		const result = this.generateReponseHandler(http)
		http.open('GET', 'status.php')
		http.send()

		return result

	},

	start: function() {
		const http = new XMLHttpRequest()
		const result = this.generateReponseHandler(http)
		http.open('GET', 'start.php')
		http.send()

		return result
	},

	stop: function() {
		const http = new XMLHttpRequest()
		const result = this.generateReponseHandler(http)
		http.open('GET', 'stop.php')
		http.send()

		return result
	}
}
