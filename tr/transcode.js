// javascript for transcode
// you do not want to know how it works....

window.onload = function() {
    let txt = document.getElementById('datacontent');

    let letters = [];
    let buttons1 = document.getElementsByClassName('add-button');
    let i1 = buttons1.length;
    while (i1--) {
        letters.push(buttons1[i1].getAttribute('data-content'));
        buttons1[i1].onclick = function(ev) {
            txt.value += ev.target.innerText;
        }
    }

    let buttons2 = document.getElementsByClassName('remove-button');
    let i2 = buttons2.length;
    while (i2--) {
        buttons2[i2].onclick = function() {
            letters.sort(function(a, b) {
                return a.length - b.length;
            });
            let ll = letters.length;
            while (ll--) {
                let letter = letters[ll];
                let finalLength = txt.value.length - letter.length;
                if (txt.value.substring(finalLength, txt.value.length) == letter) {
                    txt.value = txt.value.substring(0, finalLength);
                    return;
                }
            }
        }
    }
};
