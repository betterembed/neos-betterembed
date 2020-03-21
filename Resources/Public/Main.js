/*!
 * BetterEmbed.NeosEmbed - created by David Spiola and Jon Uhlmann
 * @link https://github.com/betterembed/neos-betterembed
 * Copyright 2020 David Spiola and Jon Uhlmann
 * Licensed under GPL-3.0-or-later
 */
!function(){"use strict";var e="betterembed",c="js-".concat(e,"-switch"),t="js-".concat(e,"-close"),s="js-".concat(e,"-load-remote"),n="is-".concat(e,"-remote-visible"),a="is-".concat(e,"-dialog-visible"),i="is-".concat(e,"-confirmed"),o=function(c){return c.closest(".js-".concat(e))};document.addEventListener("click",(function(r){var l=r.target;if(l.classList.contains(c)){var d=o(l).classList,m=d.contains(i);d.toggle(m?n:a)}l.classList.contains(t)&&o(l).classList.remove(a);if(l.classList.contains(s)){var u=o(l),v=u.classList,f=u.querySelector(".js-".concat(e,"-embed"));f.innerHTML||(f.innerHTML=JSON.parse(u.dataset.embedHtml),Array.from(f.querySelectorAll("script")).forEach((function(e){if(e.src){var c=document.createElement("script");c.src=e.src,document.head.appendChild(c)}else window.eval(e.innerHTML)}))),v.add(n),v.add(i),v.remove(a)}}))}();
//# sourceMappingURL=Main.js.map
