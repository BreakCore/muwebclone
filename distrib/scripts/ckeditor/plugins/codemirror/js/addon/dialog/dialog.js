﻿(function(){function j(a,f,b){a=a.getWrapperElement().appendChild(document.createElement("div"));a.className=b?"CodeMirror-dialog CodeMirror-dialog-bottom":"CodeMirror-dialog CodeMirror-dialog-top";a.innerHTML=f;return a}CodeMirror.defineExtension("openDialog",function(a,f,b){function d(){g||(g=!0,e.parentNode.removeChild(e))}var e=j(this,a,b&&b.bottom),g=!1,h=this,c=e.getElementsByTagName("input")[0];if(c){CodeMirror.on(c,"keydown",function(a){if(!b||!b.onKeyDown||!b.onKeyDown(a,c.value,d))if(13==
a.keyCode||27==a.keyCode)CodeMirror.e_stop(a),d(),h.focus(),13==a.keyCode&&f(c.value)});if(b&&b.onKeyUp)CodeMirror.on(c,"keyup",function(a){b.onKeyUp(a,c.value,d)});b&&b.value&&(c.value=b.value);c.focus();CodeMirror.on(c,"blur",d)}else if(a=e.getElementsByTagName("button")[0])CodeMirror.on(a,"click",function(){d();h.focus()}),a.focus(),CodeMirror.on(a,"blur",d);return d});CodeMirror.defineExtension("openConfirm",function(a,f,b){function d(){g||(g=!0,e.parentNode.removeChild(e),h.focus())}var e=j(this,
a,b&&b.bottom),a=e.getElementsByTagName("button"),g=!1,h=this,c=1;a[0].focus();for(b=0;b<a.length;++b){var i=a[b];(function(a){CodeMirror.on(i,"click",function(b){CodeMirror.e_preventDefault(b);d();a&&a(h)})})(f[b]);CodeMirror.on(i,"blur",function(){--c;setTimeout(function(){0>=c&&d()},200)});CodeMirror.on(i,"focus",function(){++c})}})})();