// $Id:
/*
 * jHeartbeat 0.2.0
 * (C)Alex Richards - http://www.ajtrichards.co.uk/heartbeat
 */
/* [ ML -- 11FEB2008 ] 
 *    As part of the Drupal wrapper module project 'jquery_heartbeat', I changed 
 *    the default 'heartbeat' page reference to 'heartbeat.php', and the default
 *    <div> tag ID name to 'heartbeat'. I also changed the code to only add the 
 *    <div> tag if it does not already exist, and I changed the "$().append(data)"
 *    to the $.().html(data) so that the retrieved data now replaces any existing 
 *    data rather then appending it.
 *
 * [ ML -- 12FEB2008 ]
 *    Added 'startstop' function.
 *
 *    NOTE: this file is also available in 'compressed' form.
 */
$.jheartbeat = {

  options: {
    url:        'heartbeat.php',
    delay:      10000,
    div_id:     'heartbeat',
    replace:    true,
    lbl_start:  'Start',
    lbl_stop:   'Stop'
  },
  
  beatfunction:  function(){
    
  },
  
  timeoutobj: {
    id: -1
  },
  
  set: function(options, onbeatfunction) {
    if (this.timeoutobj.id > -1) {
      clearTimeout(this.timeoutobj.id);
    }
    if (options) {
      $.extend(this.options, options);
    }
    if (onbeatfunction) {
      this.beatfunction = onbeatfunction;
    }
    
    // Add the HeartBeatDIV to the page if needed
    if ($('#'+ this.options.div_id).length < 1) {
      $('body').append('<div id="'+ this.options.div_id +'" style="display: none;"></div>');
    }
    this.timeoutobj.id = setTimeout('$.jheartbeat.beat();', this.options.delay);
  },
  
  startstop: function(oBtn, sMode) {
    if (sMode != 'start' && sMode != 'stop') {
      sMode = (this.timeoutobj.id > -1) ? 'stop' : 'start';
    }
    
    if (sMode == 'start') {
      if (this.timeoutobj.id > -1) {
        clearTimeout(this.timeoutobj.id);
      }
      $('#'+ oBtn.id).html(this.options.lbl_stop);
      $('#'+ oBtn.id).css({backgroundColor: 'maroon', color: 'white'});
      this.timeoutobj.id = setTimeout('$.jheartbeat.beat();', this.options.delay);
    }
    else {
      if (this.timeoutobj.id > -1) {
        clearTimeout(this.timeoutobj.id);
      }
      this.timeoutobj.id = -1;
      $('#'+ oBtn.id).html(this.options.lbl_start);
      $('#'+ oBtn.id).css({backgroundColor: 'green', color: 'white'});
    }
  },
  
  beat: function() {
    $.ajax({
      url:      this.options.url,
      dataType: 'html',
      type:     'GET',
      error:    function(e, textStatus, errorThrown) {
                  var sMsg = '<span class="error">Erro</span>';
                  if ($.jheartbeat.options.replace) { $('#'+ $.jheartbeat.options.div_id).html(sMsg); }
                  else                              { $('#'+ $.jheartbeat.options.div_id).append(sMsg); }
                },
      success:  function(data) {
                  if ($.jheartbeat.options.replace) { $('#'+ $.jheartbeat.options.div_id).html(data); }
                  else                              { $('#'+ $.jheartbeat.options.div_id).append(data); }
                }
    });
    this.timeoutobj.id = setTimeout('$.jheartbeat.beat();', this.options.delay);
    this.beatfunction();
  }
};
