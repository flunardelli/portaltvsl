<?php
//$Id: video_scheduler.php,v 1.5.2.6 2009/10/01 15:06:03 heshanmw Exp $
/**
 * @file
 * Implement video rendering scheduling.
 * If you are not using sites/default/settings.php as your settings file,  
 * add an optional parameter for the drupal site url:
 * "php video_scheduler.php http://example.com/" or
 * "php video_scheduler.php http://example.org/drupal/"
 *
 * @author Fabio Varesano <fvaresano at yahoo dot it>
 * @author Heshan Wanigasooriya <heshan at heidisoft dot com, heshanmw at gmail dot com>
 * @todo
 */


/**
 * video_scheduler.php configuration
*/

// number of conversion jobs active at the same time
define('VIDEO_RENDERING_FFMPEG_INSTANCES', 5);

/**
 * video_scheduler.php configuration ends.
 * DO NOT EDIT BELOW THIS LINE
*/

/**
 * Define some constants
*/
define('VIDEO_RENDERING_PENDING', 1);
define('VIDEO_RENDERING_ACTIVE', 5);
define('VIDEO_RENDERING_COMPLETE', 10);
define('VIDEO_RENDERING_FAILED', 20);

if (isset($_SERVER['argv'][1])) {
  $url = parse_url($_SERVER['argv'][1]);
  $_SERVER['SCRIPT_NAME'] = $url['path'];
  $_SERVER['HTTP_HOST'] = $url['host'];
}

include_once('./includes/bootstrap.inc');
//module_load_include('/includes/bootstrap.inc', 'video_scheduler', 'includes/bootstrap');
// disable error reporting for bootstrap process
error_reporting(E_ERROR);
// let's bootstrap: we will be able to use drupal apis
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
// enable full error reporting again
error_reporting(E_ALL);


// allow execution only from the command line!
if(empty($_SERVER['REQUEST_METHOD'])) {
  video_scheduler_main();
}
else {
  print t('This script is only executable from the command line.');
  die();
}



/**
 * Main for video_scheduler.php
*/
function video_scheduler_main() {

  if($jobs = video_scheduler_select()) {
    foreach ($jobs as $job) {
      video_scheduler_start($job);
    }
  }
  else {
    watchdog('video_scheduler', 'no video conversion jobs to schedule.', array(), WATCHDOG_DEBUG);
  }
}


/**
 * Starts rendering for a job
*/
function video_scheduler_start($job) {
  $url = (isset($_SERVER['argv'][1])) ? escapeshellarg($_SERVER['argv'][1]) : '';
  exec("php video_render.php $job->nid $job->vid $url > /dev/null &");
}


/**
 * Select VIDEO_RENDERING_FFMPEG_INSTANCES jobs from the queue
 *
 * @return an array containing jobs
*/
function video_scheduler_select() {

  $result = db_query('SELECT * FROM {video_rendering} vr INNER JOIN {node} n ON vr.vid = n.vid INNER JOIN {video} v ON n.vid = v.vid WHERE n.nid = v.nid AND vr.nid = n.nid AND vr.status = %d ORDER BY n.created', VIDEO_RENDERING_PENDING);

  // TODO: order jobs by priority

  // TODO: use db_query_range
  $jobs = array();
  $i = 0;
  $count = db_result(db_query('SELECT COUNT(*) FROM {video_rendering} vr INNER JOIN {node} n ON vr.vid = n.vid INNER JOIN {video} v ON n.vid = v.vid WHERE n.nid = v.nid AND vr.nid = n.nid AND vr.status = %d', VIDEO_RENDERING_PENDING));
  while($i < $count && $i < VIDEO_RENDERING_FFMPEG_INSTANCES) {
    $jobs[] = db_fetch_object($result);
    $i++;
  }
//print_r($jobs);
  return $jobs;
}




?>
