var vis = (function(){
	var stateKey,
		eventKey,
		keys = {
			hidden: "visibilitychange",
			webkitHidden: "webkitvisibilitychange",
			mozHidden: "mozvisibilitychange",
			msHidden: "msvisibilitychange"
		};
	for (stateKey in keys) {
		if (stateKey in document) {
			eventKey = keys[stateKey];
			break;
		}
	}
	return function(c) {
		if (c) document.addEventListener(eventKey, c);
		return !document[stateKey];
	}
})();

// check if current tab is active or not
vis(function(){

	if(vis()){

		// tween resume() code goes here
		setTimeout(function(){
			console.log("tab is visible - has focus");
		},300);

	} else {
		window.location.replace("https://www.google.com");

		// tween pause() code goes here
		console.log("tab is invisible - has blur");
	}
});