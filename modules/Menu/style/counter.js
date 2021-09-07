// max displayed items in tree - JS buttons and processing

let TreeMaxDisplay = function () {
	this.inputCounter = null;
	this.topLimit = 32;
	this.initialize = function() {
		treeMaxDisplay.inputCounter = document.getElementById('pos_count'); // whole element to fill
		treeMaxDisplay.inputCounter.parentElement.append(treeMaxDisplay._arrowsToAppend()); // append arrows
		let current = treeMaxDisplay._getValue();
		treeMaxDisplay._limitCounter(current, current); // arrow down can be disabled
	};
	this._arrowsToAppend = function() {
		let arrows = document.createElement('div');
		arrows.setAttribute('class', 'pos');
		let buttDown = document.createElement('button');
		buttDown.setAttribute('type','button');
		buttDown.setAttribute('onclick','treeMaxDisplay.countDown()');
		buttDown.setAttribute('id','countdown');
		var imgDown = document.createElement('img');
		imgDown.setAttribute('src','/web/ms:sysimage/menu/count_down.png');
		buttDown.appendChild(imgDown);
		arrows.appendChild(buttDown);
		let buttUp = document.createElement('button');
		buttUp.setAttribute('type','button');
		buttUp.setAttribute('onclick','treeMaxDisplay.countUp()');
		buttUp.setAttribute('id','countup');
		var imgUp = document.createElement('img');
		imgUp.setAttribute('src','/web/ms:sysimage/menu/count_up.png');
		buttUp.appendChild(imgUp);
		arrows.appendChild(buttUp);
		return arrows;
	};
	this.countUp = function() {
		if (!treeMaxDisplay.inputCounter) {
			treeMaxDisplay.initialize();
		}
		let current = treeMaxDisplay._getValue();
		treeMaxDisplay._setValue(treeMaxDisplay._limitCounter(current, current + 1));
	};
	this.countDown = function () {
		if (!treeMaxDisplay.inputCounter) {
			treeMaxDisplay.initialize();
		}
		let current = treeMaxDisplay._getValue();
		treeMaxDisplay._setValue(treeMaxDisplay._limitCounter(current, current - 1));
	};
	this._limitCounter = function (current, newOne) {
		treeMaxDisplay._enableButton('countup');
		treeMaxDisplay._enableButton('countdown');
		if (0 >= newOne) {
			treeMaxDisplay._disableButton('countdown');
			return 0;
		} else if (treeMaxDisplay.topLimit <= newOne) {
			treeMaxDisplay._disableButton('countup');
			return treeMaxDisplay.topLimit;
		} else {
			return newOne;
		}
	};
	this._getValue = function() {
		return parseInt(treeMaxDisplay.inputCounter.value);
	};
	this._setValue = function(value) {
		treeMaxDisplay.inputCounter.value = value;
	};
	this._disableButton = function(whichOne) {
		var button = document.getElementById(whichOne);
		if (!button.hasAttribute('disabled')) {
			button.setAttribute('disabled','disabled');
		}
	};
	this._enableButton = function(whichOne) {
		var button = document.getElementById(whichOne);
		if (button.hasAttribute('disabled')) {
			button.removeAttribute('disabled');
		}
	};
};

let treeMaxDisplay = new TreeMaxDisplay();
