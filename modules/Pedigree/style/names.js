/// helper with getting names and their keys from database

let Names = function () {

	this.setId = function (inputSelect, lookedUpValue) { // podle id nastavi select; a je ID, b je kde...
		let s = document.getElementById(inputSelect);
		for (let i = 0; i < s.length; i++) {
			s.options[i].selected = false;
			if (lookedUpValue === s.options[i].value) {
				s.selectedIndex = i;
				s.options[i].selected = true;
			}
		}
	};

	this.updateHelper = function (inputId, type, inputListing, inputSelect) { // hlidani zmeny obsahu; a je odchyt ukazatele prvku, b parametr (jestli fena nebo pes), c je ID vypisoveho prvku, d je id selectu
		let v = document.getElementById(inputId).value;
		if (3 < v.length) { // longer than three characters due MySQL limitation
			$.get(
				document.getElementById('helper_target_link').getAttribute('value'),
				{"key": v, "sex": type},
				function (result) {
					console.log(result);
					// document.getElementById(inputListing).innerHTML = result;
					document.getElementById(inputListing).innerHTML = '';
					for (let pos in result.data) {
						let item = document.createElement('li');
						let link = document.createElement('a');
						link.setAttribute('onClick', 'names.setId(\'' + inputSelect + '\',\'' + result.data[pos].id + '\')');
						link.appendChild(document.createTextNode(result.data[pos].name + ' ' + result.data[pos].family));
						item.appendChild(link);
						document.getElementById(inputListing).appendChild(item);
					}
				}
			);
		}
	};

	this.load = function (startFrom, inputId, type, inputListing, inputSelect) {
		let ajx_pos = document.getElementById(startFrom);
		if (null == ajx_pos) {
			// nothing
			return;
		}
		let inp = document.createElement('input');
		inp.setAttribute('type', 'text');
		inp.setAttribute('id', inputId);
		inp.setAttribute('placeholder', 'Part of wanted name');
		inp.setAttribute('onKeyUp', 'names.updateHelper(\'' + inputId + '\',\'' + type + '\',\'' + inputListing + '\',\'' + inputSelect + '\')');
		let lst = document.createElement('ul');
		lst.setAttribute('id', inputListing);
		lst.setAttribute('class', 'menu submenu nowrap');
		ajx_pos.appendChild(inp);
		ajx_pos.appendChild(lst);
	};
};

let names = new Names();
document.addEventListener('DOMContentLoaded', function () {
	names.load('father_help', 'father_write', 'male', 'father_listing', 'father_select');
	names.load('mother_help', 'mother_write', 'female', 'mother_listing', 'mother_select');
});
