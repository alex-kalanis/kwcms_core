var DisplayNotification = {
	mouseDown: false,
	mouseX: 0,
	left: 0,
	element: null,
	interval: 0,

	moveOutRight: function() {
		DisplayNotification.interval = setInterval(function(){
			DisplayNotification.left += 50;
			DisplayNotification.element.style.left = DisplayNotification.left + "px";
			if(DisplayNotification.left >= 1000) {
				DisplayNotification.element.style.display = "none";
				DisplayNotification.reset();
			}
		}, 10)
	},

	close: function(element) {
		DisplayNotification.element = element.parentElement;
		DisplayNotification.moveOutRight();
	},

	reset: function() {
		DisplayNotification.left =  0;
		DisplayNotification.element = null;
		clearInterval(DisplayNotification.interval);
	}
};
