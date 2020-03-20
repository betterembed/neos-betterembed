/*!
 * BetterEmbed.NeosEmbed - created by David Spiola and Jon Uhlmann
 * @link https://github.com/betterembed/neos-betterembed
 * Copyright 2020 David Spiola and Jon Uhlmann
 * Licensed under GPL-3.0-or-later
 */

!function(){"use strict";function t(t){return function(t){if(Array.isArray(t)){for(var e=0,n=new Array(t.length);e<t.length;e++)n[e]=t[e];return n}}(t)||function(t){if(Symbol.iterator in Object(t)||"[object Arguments]"===Object.prototype.toString.call(t))return Array.from(t)}(t)||function(){throw new TypeError("Invalid attempt to spread non-iterable instance")}()}var e="betterembed",n="js-".concat(e,"-switch"),r="js-".concat(e,"-close"),c="js-".concat(e,"-load-remote"),s="is-".concat(e,"-remote-visible"),i="is-".concat(e,"-dialog-visible"),a="is-".concat(e,"-confirmed"),o=function(t){return t.closest(".js-".concat(e))};document.addEventListener("click",(function(l){var d=l.target;if(d.classList.contains(n)){var u=o(d).classList,f=u.contains(a);u.toggle(f?s:i)}d.classList.contains(r)&&o(d).classList.remove(i);if(d.classList.contains(c)){var m=o(d),v=m.classList,b=m.querySelector(".js-".concat(e,"-embed"));b.innerHTML||(b.innerHTML=JSON.parse(m.dataset.embedHtml),t(b.querySelectorAll("script")).forEach((function(t){if(t.src){var e=document.createElement("script");e.src=t.src,document.head.appendChild(e)}else window.eval(t.innerHTML)}))),v.add(s),v.add(a),v.remove(i)}}))}();
//# sourceMappingURL=Main.js.map
