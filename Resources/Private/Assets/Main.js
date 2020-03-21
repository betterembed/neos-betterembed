const NAMESPACE = 'betterembed';
const SWITCH_CLASS = `js-${NAMESPACE}-switch`;
const CLOSE_CLASS = `js-${NAMESPACE}-close`;
const LOAD_REMOTE_CLASS = `js-${NAMESPACE}-load-remote`;
const REMOTE_VISIBLE_CLASS = `is-${NAMESPACE}-remote-visible`;
const SHOW_MESSAGE_CLASS = `is-${NAMESPACE}-dialog-visible`;
const CONFIRMED_MESSAGE_CLASS = `is-${NAMESPACE}-confirmed`;

const getContainer = element => element.closest(`.js-${NAMESPACE}`);
const getEmbedElement = container => container.querySelector(`.js-${NAMESPACE}-embed`);

document.addEventListener('click', function(event) {
    const TARGET = event.target;

    if (TARGET.classList.contains(SWITCH_CLASS)) {
        const CONTAINER = getContainer(TARGET);
        const CLASS_LIST = CONTAINER.classList;
        const ALREADY_CONFIRMED = CLASS_LIST.contains(CONFIRMED_MESSAGE_CLASS);

        // toggle remote if already confirmed, otherwhise toggle message
        CLASS_LIST.toggle(ALREADY_CONFIRMED ? REMOTE_VISIBLE_CLASS : SHOW_MESSAGE_CLASS);
    }

    if (TARGET.classList.contains(CLOSE_CLASS)) {
        const CONTAINER = getContainer(TARGET);

        // Hide Message
        CONTAINER.classList.remove(SHOW_MESSAGE_CLASS);
    }

    if (TARGET.classList.contains(LOAD_REMOTE_CLASS)) {
        const CONTAINER = getContainer(TARGET);
        const CLASS_LIST = CONTAINER.classList;
        const EMBED_ELEMENT = getEmbedElement(CONTAINER);

        if (!EMBED_ELEMENT.innerHTML) {
            // This is the first-time click
            EMBED_ELEMENT.innerHTML = JSON.parse(CONTAINER.dataset.embedHtml);

            // Run trough every script and active it
            [...EMBED_ELEMENT.querySelectorAll('script')].forEach(scriptTag => {
                if (scriptTag.src) {
                    const TAG = document.createElement('script');
                    TAG.src = scriptTag.src;
                    document.head.appendChild(TAG);
                } else {
                    // The script is an inline JS, so we have to use eval
                    // jshint -W061
                    window.eval(scriptTag.innerHTML);
                    // jshint +W061
                }
            });
        }
        CLASS_LIST.add(REMOTE_VISIBLE_CLASS);
        CLASS_LIST.add(CONFIRMED_MESSAGE_CLASS);
        CLASS_LIST.remove(SHOW_MESSAGE_CLASS);
    }
});
