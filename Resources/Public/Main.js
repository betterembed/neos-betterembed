(()=>{var s="betterembed",l=`js-${s}-switch`,A=`js-${s}-close`,d=`js-${s}-load-remote`,L=`is-${s}-remote-visible`,E=`is-${s}-dialog-visible`,a=`is-${s}-confirmed`,S=n=>n.closest(`.js-${s}`),C=n=>n.querySelector(`.js-${s}-embed`);document.addEventListener("click",function(n){let e=n.target;if(e.classList.contains(l)){let t=S(e).classList,o=t.contains(a);t.toggle(o?L:E)}if(e.classList.contains(A)&&S(e).classList.remove(E),e.classList.contains(d)){let c=S(e),t=c.classList,o=C(c);o.innerHTML||(o.innerHTML=JSON.parse(c.dataset.embedHtml),Array.from(o.querySelectorAll("script")).forEach(i=>{if(i.src){let r=document.createElement("script");r.src=i.src,document.head.appendChild(r)}else window.eval(i.innerHTML)})),t.add(L),t.add(a),t.remove(E)}});})();
//# sourceMappingURL=Main.js.map
