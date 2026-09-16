function startClock() {
	// Ottenere il riferimento all'elemento del DOM che rappresenta l'orologio
	var clock = document.getElementById("clock");
	
	// Aggiornare l'orologio ogni secondo
	setInterval(function() {
		// Ottenere la data corrente
		var date = new Date();
		
		// Ottenere le ore, i minuti e i secondi dalla data corrente
		var hours = date.getHours();
		var minutes = date.getMinutes();
		var seconds = date.getSeconds();
		
		// Formattare le ore in formato 24 ore
		if (hours < 10) {
			hours = "0" + hours;
		}
		
		// Formattare i minuti e i secondi con lo zero iniziale
		if (minutes < 10) {
			minutes = "0" + minutes;
		}
		if (seconds < 10) {
			seconds = "0" + seconds;
		}
		
		// Creare una stringa che rappresenta l'orario corrente in formato "HH:MM:SS"
		var time = hours + ":" + minutes + ":" + seconds;
		
		// Aggiornare il contenuto dell'elemento del DOM che rappresenta l'orologio con l'orario corrente
		clock.innerHTML = time;
	}, 1000); // 1000 millisecondi = 1 secondo
}