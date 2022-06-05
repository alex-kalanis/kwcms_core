/// helper with changing languages on login page

let LangChanger = function () {
	this.load = function (elementId) {
		let element = document.getElementById(elementId);
		element.onchange = function (e) {
			// send form to set session language
			let link = document.getElementById('helper_lang_target_link').getAttribute('data-link');
			if (0 < link.length) {
				$.get(
					link,
					{"lang": element.options[element.selectedIndex].value},
					function (result) {
						console.log(result);
						window.location.reload();
					}
				);
			}
		}
	}
};

let langChanger = new LangChanger();
document.addEventListener('DOMContentLoaded', function () {
	langChanger.load('lang_change');
});
