<?php
// $Id:

/*
 * This is a demo file for the Drupal wrapper version of the Hearbeat 
 * jQuery plug-in. When used with the jQuery Heartbeat module/block, it will 
 * display the current time
 *
 * CREDITS: some ideas borrowed from examples in "Advanced PHP Programming" 
 *          by George Schlossnagle.
 */

// Backwards compatibility for HTTP 1.0 clients
header("Expires: 0");
header("Pragma: no-cache");

// Now a little HTTP 1.1 stuff
header("Cache-Control: no-cache,no-store,max-age=0,s-maxage=0,must-revalidate");

header('Content-Type: text/plain');
date_default_timezone_set('America/New_York');
echo '<span class="label">Server time:</span> <span class="data">'. date('Y-m-d H:i:s') .'</span>';
?>