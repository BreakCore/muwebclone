function az(n) {
	r=(n.toString().length==1)? "0" + n : n;
	return r;
}
function srvtime()
{
	sd.setSeconds(sd.getSeconds()+1)
	tm = az(sd.getHours())+":"+az(sd.getMinutes())+":"+az(sd.getSeconds());
	document.getElementById("srvtime").innerHTML = tm;
}
