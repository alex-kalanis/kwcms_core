// positions in menu tree - JS buttons and processing


// get and rewrite from original tree.js
// just total example of this kind of code, it's really picky about html source!
let TreePositions = function () {
	this.itemsCount = 0;
	this.nodesCount = 0;
	this.totalCount = 0;
	this.displayLimit = 0;

	this._arrows = function (position) {
		let arrows = document.createElement('td');
		let buttonUp = document.createElement('button');
		buttonUp.setAttribute('type', 'button');
		buttonUp.setAttribute('onclick', 'treePositions.moveUp(\'point_' + position + '\')');
		buttonUp.setAttribute('id', 'moveup_' + position + '');
		let imgUp = document.createElement('img');
		imgUp.setAttribute('src', '/web/ms:sysimage/menu/switch_up.png');
		buttonUp.appendChild(imgUp);
		arrows.appendChild(buttonUp);

		let buttonDown = document.createElement('button');
		buttonDown.setAttribute('type', 'button');
		buttonDown.setAttribute('onclick', 'treePositions.moveDown(\'point_' + position + '\')');
		buttonDown.setAttribute('id', 'movedown_' + position + '');
		let imgDown = document.createElement('img');
		imgDown.setAttribute('src', '/web/ms:sysimage/menu/switch_down.png');
		buttonDown.appendChild(imgDown);
		arrows.appendChild(buttonDown);
		return arrows;
	};

	this._hole = function (position) { // add hole to position with index
		let tr = document.createElement('tr');
		tr.setAttribute('id', 'point_' + position);
		let td = document.createElement('td');
		td.setAttribute('colspan', '2');
		let hr = document.createElement('hr');
		hr.setAttribute('title', position);
		td.appendChild(hr);
		tr.appendChild(td);
		tr.appendChild(treePositions._arrows(position));
		return tr;
	};

	this.load = function () {
		// add JS moving to page
		treePositions.totalCount = treePositions.nodesCount = treePositions._getMaxPoint();

		for (let i = 1; i <= treePositions.nodesCount; i++) { // for each node position do...
			let onPos = treePositions._positionGet(i);
			if (onPos) { // add moving arrows
				onPos.appendChild(treePositions._arrows(i));
			} else { // add hr as free line
				if (2 > i) {
					let last = treePositions._positionGet(treePositions.nodesCount);
					if (!last) { // no content available
						return;
					}
					// insert on first position
					last.parentNode.insertBefore(treePositions._hole(i), last.parentNode.firstChild);
					onPos = last.parentNode.firstChild;
				} else { // not first, we can ask for first one
					onPos = treePositions._hole(i);
					let onPrecedentPos = treePositions._positionGet(i - 1);
					onPrecedentPos.parentNode.insertBefore(onPos, onPrecedentPos.nextSibling);
				}
			}
		}
		treePositions.itemsCount = treePositions._getValue(treePositions._positionGet(treePositions.nodesCount));
		treePositions._resetAttributes();
	};

	this.moveUp = function (id) { // move actual node up
		let currentOne = document.getElementById(id);
		treePositions._swapWithPrevElement(currentOne); // move actual up
		let nextOne = currentOne.nextSibling;
		if ((nextOne === currentOne.parentNode.lastChild) && (treePositions._isHole(nextOne))) { // last is hole
			currentOne.parentNode.removeChild(nextOne);
			treePositions.nodesCount--;
		}
		treePositions._resetAttributes();
	};

	this.moveDown = function (id) { // move actual node down
		// go to next element, swap it with current one
		let currentOne = document.getElementById(id);
		let nextOne = currentOne.nextSibling;
		if (!nextOne) { // add hole before last one
			let pos = treePositions._getValue(currentOne);
			nextOne = treePositions._hole(treePositions._firstFree());
			currentOne.parentNode.appendChild(nextOne);
			treePositions.nodesCount++;
		}
		treePositions._swapWithPrevElement(nextOne); // move next up (and actual down)
		if ((currentOne === currentOne.parentNode.lastChild) && treePositions._isHole(currentOne)) { // last is now hole
			currentOne.parentNode.removeChild(currentOne);
			treePositions.nodesCount--;
		}
		treePositions._resetAttributes();
	};

	this._swapWithPrevElement = function (currentOne) {
		// todo: beware of new lines - prepare for that case too!
		if (currentOne.previousSibling) {
			let previousOne = currentOne.previousSibling;
			treePositions._updateValue(previousOne, treePositions._getValue(previousOne) + 1);
			treePositions._updateValue(currentOne, treePositions._getValue(currentOne) - 1);
			previousOne.parentNode.removeChild(currentOne);
			previousOne.parentNode.insertBefore(currentOne, previousOne);
		}
	};

	this._getValue = function (element) {
		if (treePositions._isHole(element)) { // because I can move holes; part for them
			return parseInt(treePositions._getSeparator(element).title);
		} else { // part for normal nodes
			return parseInt(treePositions._getInput(element).value);
		}
	};

	this._updateValue = function (element, value) {
		if (treePositions._isHole(element)) { // because I can move holes; part for them
			treePositions._getSeparator(element).title = value;
		} else { // part for normal nodes
			treePositions._getInput(element).value = value;
		}
	};

	this._firstFree = function () {
		treePositions.totalCount += 1;
		return treePositions.totalCount;
	};

	this._getId = function (element) {
		return element.getAttribute('id');
	};

	this._getInput = function (element) {
		return element.getElementsByTagName('input')[0];
	};

	this._getSeparator = function (element) {
		return element.getElementsByTagName('hr')[0];
	};

	this._isHole = function (element) {
		return (element.firstChild.colSpan > 1); // get hole
	};

	this._resetAttributes = function () {
		treePositions._limitAfterLimit();
		treePositions._enableButtons();
		treePositions._disableTop();
		treePositions._disableLast();
	};

	this._limitAfterLimit = function () {
		treePositions.displayLimit = treePositions._getDisplayCount();
		let trs = Array.from(treePositions._positionGet(treePositions.displayLimit).parentElement.getElementsByTagName('tr'));
		for (let i in trs) { // for each node position do...
			let onPos = trs[i];
			if (i >= treePositions.displayLimit) { // its invisible
				onPos.setAttribute('class', 'after');
			} else if (onPos.hasAttribute('class')) {
				onPos.removeAttribute('class');
			}
		}
	};

	this._enableButtons = function () {
		let last = treePositions._positionGet(treePositions.nodesCount);
		if (last) {
			let buttons = Array.from(last.parentNode.getElementsByTagName('button'));
			if (buttons) {
				for (let buttonKey in buttons) {
					let button = buttons[buttonKey];
					if (button.hasOwnProperty('hasAttribute') && button.hasAttribute('disabled')) {
						button.removeAttribute('disabled');
					}
				}
			}
		}
	};

	this._disableTop = function () {
		let last = treePositions._positionGet(treePositions.nodesCount);
		if (last) {
			last.parentNode.getElementsByTagName('button')[0].setAttribute('disabled', 'disabled');
		}
	};

	this._disableLast = function () {
		let last = treePositions._positionGet(treePositions.nodesCount);
		if (last) {
			let buttons = Array.from(last.parentNode.getElementsByTagName('button'));
			let lastButton = buttons.slice(-1)[0];
			let val = treePositions._getValue(lastButton.parentNode.parentNode);
			if (val > (treePositions.itemsCount * 2)) {
				lastButton.setAttribute('disabled', 'disabled')
			}
		}
	};

	this._getDisplayCount = function () {
		return parseInt(document.getElementById('display_count').getAttribute('value'));
	};

	this._getMaxPoint = function (defaultLimit = 255) {
		let maxPoint = 0;
		for (let i = 0; i <= defaultLimit; i++) { // try to find any point_X
			if (treePositions._positionGet(i)) {
				maxPoint = i;
			}
		}
		return maxPoint;
	};

	this._positionGet = function (pos) {
		return document.getElementById('point_' + pos);
	};
};

let treePositions = new TreePositions();
document.addEventListener('DOMContentLoaded', function () {
	treePositions.load();
});
