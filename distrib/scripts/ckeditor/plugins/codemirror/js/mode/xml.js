﻿CodeMirror.defineMode("xml",function(v,l){function j(a,c){function d(d){c.tokenize=d;return d(a,c)}var b=a.next();if("<"==b){if(a.eat("!"))return a.eat("[")?a.match("CDATA[")?d(n("atom","]]\>")):null:a.match("--")?d(n("comment","--\>")):a.match("DOCTYPE",!0,!0)?(a.eatWhile(/[\w\._\-]/),d(o(1))):null;if(a.eat("?"))return a.eatWhile(/[\w\._\-]/),c.tokenize=n("meta","?>"),"meta";b=a.eat("/");g="";for(var e;e=a.eat(/[^\s\u00a0=<>\"\'\/?]/);)g+=e;if(!g)return"error";h=b?"closeTag":"openTag";c.tokenize=
p;return"tag"}if("&"==b)return(a.eat("#")?a.eat("x")?a.eatWhile(/[a-fA-F\d]/)&&a.eat(";"):a.eatWhile(/[\d]/)&&a.eat(";"):a.eatWhile(/[\w\.\-:]/)&&a.eat(";"))?"atom":"error";a.eatWhile(/[^&<]/);return null}function p(a,c){var d=a.next();if(">"==d||"/"==d&&a.eat(">"))return c.tokenize=j,h=">"==d?"endTag":"selfcloseTag","tag";if("="==d)return h="equals",null;if(/[\'\"]/.test(d))return c.tokenize=w(d),c.tokenize(a,c);a.eatWhile(/[^\s\u00a0=<>\"\']/);return"word"}function w(a){return function(c,d){for(;!c.eol();)if(c.next()==
a){d.tokenize=p;break}return"string"}}function n(a,c){return function(d,b){for(;!d.eol();){if(d.match(c)){b.tokenize=j;break}d.next()}return a}}function o(a){return function(c,d){for(var b;null!=(b=c.next());){if("<"==b)return d.tokenize=o(a+1),d.tokenize(c,d);if(">"==b)if(1==a){d.tokenize=j;break}else return d.tokenize=o(a-1),d.tokenize(c,d)}return"meta"}}function k(){for(var a=arguments.length-1;0<=a;a--)b.cc.push(arguments[a])}function e(){k.apply(null,arguments);return!0}function q(){b.context&&
(b.context=b.context.prev)}function x(a){return"openTag"==a?(b.tagName=g,b.tagStart=r.column(),e(m,y(b.startOfLine))):"closeTag"==a?(a=!1,b.context?b.context.tagName!=g&&(i.implicitlyClosed.hasOwnProperty(b.context.tagName.toLowerCase())&&q(),a=!b.context||b.context.tagName!=g):a=!0,a&&(f="error"),e(z(a))):e()}function y(a){return function(c){var d=b.tagName;b.tagName=b.tagStart=null;if("selfcloseTag"==c||"endTag"==c&&i.autoSelfClosers.hasOwnProperty(d.toLowerCase()))return s(d.toLowerCase()),e();
"endTag"==c&&(s(d.toLowerCase()),c=i.doNotIndent.hasOwnProperty(d)||b.context&&b.context.noIndent,b.context={prev:b.context,tagName:d,indent:b.indented,startOfLine:a,noIndent:c});return e()}}function z(a){return function(c){a&&(f="error");if("endTag"==c)return q(),e();f="error";return e(arguments.callee)}}function s(a){for(var c;b.context;){c=b.context.tagName.toLowerCase();if(!i.contextGrabbers.hasOwnProperty(c)||!i.contextGrabbers[c].hasOwnProperty(a))break;q()}}function m(a){if("word"==a)return f=
"attribute",e(A,m);if("endTag"==a||"selfcloseTag"==a)return k();f="error";return e(m)}function A(a){if("equals"==a)return e(B,m);i.allowMissing?"word"==a&&(f="attribute"):f="error";return"endTag"==a||"selfcloseTag"==a?k():e()}function B(a){if("string"==a)return e(t);if("word"==a&&i.allowUnquoted)return f="string",e();f="error";return"endTag"==a||"selfCloseTag"==a?k():e()}function t(a){return"string"==a?e(t):k()}var u=v.indentUnit,C=l.multilineTagIndentFactor||1,i=l.htmlMode?{autoSelfClosers:{area:!0,
base:!0,br:!0,col:!0,command:!0,embed:!0,frame:!0,hr:!0,img:!0,input:!0,keygen:!0,link:!0,meta:!0,param:!0,source:!0,track:!0,wbr:!0},implicitlyClosed:{dd:!0,li:!0,optgroup:!0,option:!0,p:!0,rp:!0,rt:!0,tbody:!0,td:!0,tfoot:!0,th:!0,tr:!0},contextGrabbers:{dd:{dd:!0,dt:!0},dt:{dd:!0,dt:!0},li:{li:!0},option:{option:!0,optgroup:!0},optgroup:{optgroup:!0},p:{address:!0,article:!0,aside:!0,blockquote:!0,dir:!0,div:!0,dl:!0,fieldset:!0,footer:!0,form:!0,h1:!0,h2:!0,h3:!0,h4:!0,h5:!0,h6:!0,header:!0,hgroup:!0,
hr:!0,menu:!0,nav:!0,ol:!0,p:!0,pre:!0,section:!0,table:!0,ul:!0},rp:{rp:!0,rt:!0},rt:{rp:!0,rt:!0},tbody:{tbody:!0,tfoot:!0},td:{td:!0,th:!0},tfoot:{tbody:!0},th:{td:!0,th:!0},thead:{tbody:!0,tfoot:!0},tr:{tr:!0}},doNotIndent:{pre:!0},allowUnquoted:!0,allowMissing:!0}:{autoSelfClosers:{},implicitlyClosed:{},contextGrabbers:{},doNotIndent:{},allowUnquoted:!1,allowMissing:!1},D=l.alignCDATA,g,h,b,r,f;return{startState:function(){return{tokenize:j,cc:[],indented:0,startOfLine:!0,tagName:null,tagStart:null,
context:null}},token:function(a,c){!c.tagName&&a.sol()&&(c.startOfLine=!0,c.indented=a.indentation());if(a.eatSpace())return null;f=h=g=null;var d=c.tokenize(a,c);c.type=h;if((d||h)&&"comment"!=d){b=c;for(r=a;!(c.cc.pop()||x)(h||d););}c.startOfLine=!1;return f||d},indent:function(a,c,d){var b=a.context;if(a.tokenize!=p&&a.tokenize!=j||b&&b.noIndent)return d?d.match(/^(\s*)/)[0].length:0;if(a.tagName)return a.tagStart+u*C;if(D&&/<!\[CDATA\[/.test(c))return 0;b&&/^<\//.test(c)&&(b=b.prev);for(;b&&!b.startOfLine;)b=
b.prev;return b?b.indent+u:0},electricChars:"/",configuration:l.htmlMode?"html":"xml"}});CodeMirror.defineMIME("text/xml","xml");CodeMirror.defineMIME("application/xml","xml");CodeMirror.mimeModes.hasOwnProperty("text/html")||CodeMirror.defineMIME("text/html",{name:"xml",htmlMode:!0});