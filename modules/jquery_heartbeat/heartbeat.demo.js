// $Id:
/*
 * JHeartbeat Demo Functions
 *
 * These are demo functions designed to work with the Drupal jQuery Heartbeat 
 * wrapper module. The functions are used as callbacks for the demo block.
 * 
 * @copyright  (c) 2008 Martin Lanser
 * @author     Martin Lanser (redbox2000@optonline.net
 * @license    Released under GNU/GPL License [ http://www.gnu.org/copyleft/gpl.html ]
 */

_jheartbeat_unique.counter = -1;
function _jheartbeat_unique() { return _jheartbeat_unique.counter++; }
function _jheartbeat_random(iMin, iMax) { return iMin + Math.floor(Math.random() * (iMax - iMin + 1)); }

$.jheartbeat_demo = {

  percent_bar: function(sID) {
    
    var iVal = _jheartbeat_unique() % 100;
    if (iVal < 1) iVal = 1;
        
    $('#'+ sID +' .left').css('width', iVal +'%');
    $('#'+ sID +' .right').css('width', (100 - iVal) + '%');
    $('#'+ sID +' .percent').html('<em>'+ iVal +'%</em>');
  },
  
  random_num: function(sID) {
    var iVal = _jheartbeat_random(1, 1000);
    
    $('#'+ sID).html('<span class="label">Magic number:</span> <span class="data">'+ iVal +'</span>');
  },
  
  random_clr: function(sID) {
    var iR = _jheartbeat_random(0, 255);
    var iG = _jheartbeat_random(0, 255);
    var iB = _jheartbeat_random(0, 255);
    
    $('#'+ sID).css('background-color', 'rgb('+ iR +','+ iG +','+ iG +')');
    $('#'+ sID).css('color', '#FFF');
  }
};
