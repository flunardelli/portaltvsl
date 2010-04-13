var vVolume=100;
var oggStatus="loaded";
var isIE  = (navigator.appVersion.indexOf("MSIE") != -1) ? true : false;
var isWin = (navigator.appVersion.toLowerCase().indexOf("win") != -1) ? true : false;
var isFirefox = (navigator.userAgent.indexOf('Firefox') !=-1) ? true : false;
var isChrome = (navigator.userAgent.toLowerCase().indexOf('chrome') > -1) ? true : false;
var audioElement = null;
function doPlayStop()
{
	if (isFirefox){
	  backend = "html5";
	}

	if (isChrome){
	  backend = "java";
	  isIE = true;
	}

	js_log("use " + backend + ' - ' +jBasePath);

	if (backend == 'java') {
		if (isIE)
		  audioElement = document.getElementById(audioElementId + 'IE');
		else
		  audioElement = document.getElementById(audioElementId);
		
		//js_log(audioElement);
		
		js_log("use cortado " + backend + audioElementId + oggStatus );
	    if (oggStatus=="stopped" || (oggStatus=="loaded") ) {
		  document.getElementById('playerbutton').src = jBasePath + 'images/b-stop.png';
		  audioElement.play();  
		  oggStatus="playing";		  
		  onOggState(oggStatus);
	    } else {
		  document.getElementById('playerbutton').src = jBasePath + 'images/b-play.png';
		  audioElement.pause();  
	  	  oggStatus="stopped"; 
		  onOggState(oggStatus);
	    }
	} else if (backend == 'html5'){
		audioElement = document.getElementById(audioElementId + 'H5');	
		js_log("use html5 " + backend + audioElementId + oggStatus + ' ' + audioElement.src);
	    if (oggStatus=="stopped" || (oggStatus=="loaded") ) {
		  document.getElementById('playerbutton').src = jBasePath + 'images/b-stop.png';
		  audioElement.play();  
		  oggStatus="playing";
		  onOggState(oggStatus);
		} else {
		  document.getElementById('playerbutton').src = jBasePath + 'images/b-play.png';
		  audioElement.pause();  
		  audioElement.currentTime = 0;
	  	  oggStatus="stopped"; 
		  onOggState(oggStatus);
	    }
	}
	
}

function onOggState(str)
{
	oggStatus=str;
	var display = document.getElementById("stt");
	if (display) {
	  display.innerHTML=str;
	  if ((oggStatus != "playing") && (oggStatus != "buffering")){
	  	document.getElementById('playerbutton').src = jBasePath + 'images/b-play.png';		
	  }
	}
}

function volumeToggle() {
  if (backend == 'java') {
	volumeToggleJava();
  } else if(backend == 'html5') {
	volumeToggleHTML5();
  }
  js_log("volume toggle " + vVolume);
}

function volumeToggleJava() {
  if (audioElement) {
    if (vVolume == 100){
	  audioElement.setParam("audio",false);
	  vVolume = 0;
	  document.getElementById('volumebutton').src = jBasePath + 'images/volume-off.png';
	  audioElement.restart();		
  	} else {
	  audioElement.setParam("audio",true);
	  vVolume = 100;
	  document.getElementById('volumebutton').src = jBasePath + 'images/volume-on.png';
	  audioElement.restart();
	  audioElement.doPlay();
  	}
  }
	//oggSetVolume(vVolume);
}

function volumeToggleHTML5() {
  if (audioElement) {
    if (vVolume == 100){
	  vVolume = 0;
	  document.getElementById('volumebutton').src = jBasePath + 'images/volume-off.png';
  	} else {
	  vVolume = 100;
	  document.getElementById('volumebutton').src = jBasePath + 'images/volume-on.png';
  	}
  	audioElement.volume = (vVolume/100);
  }
}


function js_log(string){
  if( window.console ){
    window.console.log(string);
  }
  return false;
}

function goPopUp(){
  playerwindow= window.open(jBasePathLink, "playerwindow","resizeable=no,toolbar=no,location=no,status=no,scrollbars=no,width=250,height=64");playerwindow.moveTo(0,0);
}

var tWidth='250px';                  // width (in pixels)
var tHeight='20px';                  // height (in pixels)
var tcolour='#ffffcc';               // background colour:
var moStop=true;                     // pause on mouseover (true or false)
var fontfamily = 'arial,sans-serif'; // font for content
var tSpeed=3;                        // scroll speed (1 = slow, 5 = fast)

