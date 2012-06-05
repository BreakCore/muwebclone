var digital = new Date();

function writeLayer(layerID,txt)
{
  if(document.getElementById)
  {
    document.getElementById(layerID).innerHTML=txt;
  }
  else if(document.all)
  {
    document.all[layerID].innerHTML=txt;
  }
  else if(document.layers)
  {
    document.layers[layerID].document.open();
    document.layers[layerID].document.write(txt);
    document.layers[layerID].document.close();
  }
}
document.write ("<span id='Pendule'>&nbsp;</span>");

function clock()
{
  var hours = digital.getHours();
  var minutes = digital.getMinutes();
  var seconds = digital.getSeconds();
  var ampm = '';
  var d = digital.getDate();
  var m = digital.getMonth();
  var y = digital.getFullYear();
  var dispTime;

  digital.setSeconds( seconds+1 );

  if (minutes < 10) minutes = '0' + minutes;
  if (seconds < 10) seconds = '0' + seconds;

  dispTime = "&nbsp;"+hours + ":" + minutes + ":" + seconds;
  writeLayer( 'Pendule', dispTime );
  setTimeout("clock()", 1000);
}

clock();

function AddPoints(Id,Add)
{	
var val,left;
var t = document.getElementById(Id);
var p = document.getElementById('points');
	
	if(t.value.length > 0 && !isNaN(t.value))	{
		val = parseInt(t.value);
	}
	else{
		val = 0;
	}
	if(p.value.length > 0 && !isNaN(p.value))	{
		left = parseInt(p.value);
	}
	else{
		left = 0;
	}
	if(Add <= left)	{
		val += Add;
		left -= Add;
		
		t.value = val.toString();
		p.value = left.toString();
		return true;
	}
	return false;
}

function AddPointsReset(val)
{
	var t = document.getElementById('points');
	t.value = val.toString();
}