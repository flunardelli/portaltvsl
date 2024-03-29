// $Id: video_upload.js,v 1.2.4.1 2009/07/25 03:30:50 heshanmw Exp $
/**
 * @file
 * Javascript functions for busy status on video uploads
 *
 * TODO: Support AJAX Uploads :-)
 *
 * @author Fabio Varesano <fvaresano at yahoo dot it>
 * @author Heshan Wanigasooriya <heshan at heidisoft.com><heshanmw at gmail dot com>
 * @todo
*/

/**
 * Hide the node form and show the busy div
*/
Drupal.video_upload_hide = function () {
  // hiding the form (using display: none) makes its file values empty in Konqueror (Possibly also Safari). So let's move the form away of the view of the browser

  $('#node-form').css({ position: "absolute", top: "-4000px" });

  $("#sending").show();
  $("#video_upload_cancel_link").click(Drupal.video_upload_show);
}

Drupal.video_upload_show = function() {
  $('#node-form').show();
  $("#sending").hide();

  //$("form").bind("submit", function() { return false; })
  window.location = window.location;
}

/**
 * Attaches the upload behaviour to the video upload form.
 */
Drupal.video_upload = function() {
  $('#node-form').submit(Drupal.video_upload_hide);
}

// Global killswitch
if (Drupal.jsEnabled) {
  $(document).ready(Drupal.video_upload);
}
