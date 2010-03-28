<?php
// $Id: yp.cgi.inc,v 1.1.2.12 2008/07/10 09:40:52 mfb Exp $

function yp_cgi() {
  if (variable_get('yp_debug', 0)) {
    watchdog('yp', check_plain(print_r($_POST, TRUE)), NULL, WATCHDOG_DEBUG);
  }
  $action = isset($_POST['action']) ? trim($_POST['action']) : '';
  switch ($action) {
    case 'add':
      new YPCGIAdd();
      break;
    case 'touch':
      new YPCGITouch();
      break;
    case 'remove':
      new YPCGIRemove();
      break;
  }
}

class YPCGI {

  function YPCGI() {
    $this->receive();
    $this->process();
    $this->response();
  }

  function receive() {
    $this->last_touch = time();
    foreach ($this->map as $key => $variables) {
      $this->$key = '';
      foreach ($variables as $variable) {
        if (isset($_POST[$variable])) {
          $this->$key = trim($_POST[$variable]);
        }
      }
    }
  }

}

class YPCGIAdd extends YPCGI {

  var $map = array(
    'server_name' => array('sn'),
    'server_type' => array('type'),
    'genre' => array('genre'),
    'bitrate' => array('audio_bitrate', 'b', 'bitrate', 'ice-bitrate'),
    'samplerate' => array('audio_samplerate', 'samplerate', 'ice-samplerate'),
    'channels' => array('audio_channels', 'channels', 'ice-channels'),
    'listen_url' => array('listenurl'),
    'description' => array('desc'),
    'url' => array('url'),
    'cluster_password' => array('cpswd'),
  );

  function process() {
    $this->listing_ip = ip_address();
    drupal_write_record('yp_stream', $this);
    $this->yp_response = $this->sid ? 1 : 0;
    $this->yp_message = $this->yp_response ? 'Added' : 'Error';
  }

  function response() {
    drupal_set_header("SID: $this->sid");
    drupal_set_header('TouchFreq: 200');
    drupal_set_header("YPMessage: $this->yp_message");
    drupal_set_header("YPResponse: $this->yp_response");
  }
}

class YPCGITouch extends YPCGI {

  var $map = array(
    'sid' => array('sid'),
    'listeners' => array('listeners'),
    'max_listeners' => array('max_listeners'),
    'server_subtype' => array('stype'),
    'current_song' => array('st'),
  );

  function process() { 
    drupal_write_record('yp_stream', $this, 'sid');
    $this->yp_response = db_affected_rows() ? 1 : 0;
    $this->yp_message = $this->yp_response ? 'Touched' : 'SID not found';
  }

  function response() {
    drupal_set_header("YPResponse: $this->yp_response");
    drupal_set_header("YPMessage: $this->yp_message");
  }

}

class YPCGIRemove extends YPCGI {

  var $map = array(
    'sid' => array('sid'),
  );

  function process() { 
    db_query("DELETE FROM {yp_stream} WHERE sid = %d", $this->sid);
  }

  function response() {
    drupal_set_header('YPResponse: 1');
    drupal_set_header('YPMessage: Removed');
  }

}
