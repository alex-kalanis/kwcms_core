// javascript for transcode
// you do not want to know how it works....

window.onload = function() {
    var txt = document.getElementById('dataContent');

    var letters = [];
    var buttons1 = document.getElementsByClassName('add-button');
    var i1 = buttons1.length;
    while (i1--) {
        letters.push(buttons1[i1].getAttribute('data-content'));
        buttons1[i1].onclick = function(ev) {
            txt.value += ev.target.innerText;
        }
    }

    var buttons2 = document.getElementsByClassName('remove-button');
    var i2 = buttons2.length;
    while (i2--) {
        buttons2[i2].onclick = function() {
            letters.sort(function(a, b) {
                return a.length - b.length;
            });
            var ll = letters.length;
            while (ll--) {
                var letter = letters[ll];
                var finalLength = txt.value.length - letter.length;
                if (txt.value.substring(finalLength, txt.value.length) == letter) {
                    txt.value = txt.value.substring(0, finalLength);
                    return;
                }
            }
        }
    }
};
