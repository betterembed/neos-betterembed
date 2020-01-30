var betterembed_show = document.querySelectorAll('.js-betterembed-show-message,.js-betterembed-load-remote,.js-betterembed-close');
document.querySelectorAll('button.js-betterembed-show-message').forEach(function(button) {
    button.innerHTML = 'show original';
});
var showhidebtn = document.getElementsByClassName('.js-betterembed-show-message')[0];
for (var i = 0; i < betterembed_show.length; i++) {
    betterembed_show[i].addEventListener('click', function(e) {

        var container = this.closest('.js-betterembed');
        var showhidebtn = container.querySelector('button.js-betterembed-show-message');

        if(e.currentTarget.classList.contains('js-betterembed-show-message')) {
            container.classList.toggle('is-betterembed-msg-visible');
            container.classList.remove('is-betterembed-remote-visible');
            showhidebtn.innerHTML = 'show original';
        }

        if(e.currentTarget.classList.contains('js-betterembed-close')) {
            container.classList.remove('is-betterembed-msg-visible');
        }

        if(e.currentTarget.classList.contains('js-betterembed-load-remote')) {
            var embed_elem = container.lastElementChild;
            embed_elem.innerHTML = JSON.parse(container.getAttribute('data-embed-html'));

            //embed_elem.innerHTML = load_remote;
            container.classList.toggle('is-betterembed-remote-visible');

            //search for script tags and make executing this
            var scripts = Array.prototype.slice.call(embed_elem.getElementsByTagName("script"));
            for (var i = 0; i < scripts.length; i++) {
                if (scripts[i].src != "") {
                    var tag = document.createElement("script");
                    tag.src = scripts[i].src;
                    document.getElementsByTagName("head")[0].appendChild(tag);
                } else {
                    eval(scripts[i].innerHTML);
                }
            }
            showhidebtn.innerHTML = 'hide original';

        }

        e.preventDefault();

    }, false);
}
