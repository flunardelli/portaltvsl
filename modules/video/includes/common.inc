<?php
//$Id: common.inc,v 1.2.4.10 2009/11/07 17:00:05 heshanmw Exp $
/**
 * @file
 * Add some common functions for the various video types supported
 *
 * @author Fabio Varesano <fvaresano at yahoo dot it>
 * @author Heshan Wanigasooriya <heshan at heidisoft dot com><heshanmw at gmail dot com>
 * @author Glen Marianko Twitter@demoforum <glenm at demoforum dot com>
 * @todo   Configure default players by video filetype (user configurable?)
 */

/**
 * Get the object for the suitable player for the parameter resource
*/
function _video_common_get_player($node) {
  $op = ($node->vtype == "upload")?  _video_get_filetype($node->vidfile) : $node->vtype;
  switch ($op) {
    case 'divx':
      return theme('video_play_divx', $node);
    case 'mov':
    case '3gp':
    case '3g2':
    case 'mp4':
      return theme('video_play_quicktime', $node);
    case 'rm':
      return theme('video_play_realmedia', $node);
    case 'f4v':
    case 'flv': // flowplayer also supprts MP4, H.264 (.extension?)
      return theme('video_play_flash', $node);
    case 'swf':
      return theme('video_play_swf', $node);
    case 'dir':
    case 'dcr':
      return theme('video_play_dcr', $node);
    case 'asf':
    case 'wmv':
    case 'avi':
    case 'mpg':
    case 'mpeg':
     return theme('video_play_windowsmedia', $node);
    case 'ogg':
      return theme('video_play_ogg_theora', $node);
    case 'youtube':
      return theme('video_play_youtube', $node);
    case 'googlevideo':
      return theme('video_play_googlevideo', $node);
    default:
      drupal_set_message('Video type not supported', 'error');
      break;
  }
}

/*********************************************************************
 * Themeable functions for playing videos. They print a page with a player embedded.
 *********************************************************************/

 /**
 * Play videos from in FLV Flash video format
 *
 * @param $node
 *   object with node information
 *
 * @return
 *   string of content to display
 */
function theme_video_play_flash($node) {
  $url = _video_get_fileurl($node->vidfile);
  $file = basename($url);
  $base_url = substr($url, 0, strrpos($url, '/'));
  $height = $node->video_scaled_y + 24; // add commands height

  if (variable_get('video_flowplayer_module', FALSE) && module_exists('flowplayer')) {
    //$video = theme('flowplayer', 'http://player-e7.simplecdn.net/flowplayer.flv');
    $output = theme('flowplayer', array(
      'clip' => array(
        'url' => $base_url . '/' . $file,
        'autoPlay' => variable_get('video_autoplay', TRUE) ? TRUE : FALSE, // Turn autoplay on/off
      ),
    ));
  }
  else {
    $loader_location = variable_get('video_flvplayerloader', 'FlowPlayer.swf');

    // this will be executed by not Internet Explorer browsers
    $output = '<!--[if !IE]> <-->
      <object type="application/x-shockwave-flash" width="'. $node->video_scaled_x .'" height="'. $height .'"
      data="'. url() . check_plain($loader_location) .'">
      <!--> <![endif]-->' . "\n";

    // this will be executed by Internet Explorer
    $output .= '<!--[if IE]>
      <object type="application/x-shockwave-flash" width="'. $node->video_scaled_x .'" height="'. $height .'"
      classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
      codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0">
      <![endif]-->' . "\n";

    // params will be passed to both IE or not IE browsers
    $config = sprintf("config={'playerId':'player','clip':{'url':'%s'},'playlist':[{'url':'%s','autoPlay':%s}]}", $base_url . '/' . $file, $base_url . '/' . $file, (variable_get('video_autoplay', TRUE) ? TRUE : FALSE));
    $output .= '<param name="movie" value="' . url() . check_plain($loader_location) . '" />
               <param name="allowFullScreen" value="true" />
               <param name="wmode" value="transparent" />
               <param name="allowScriptAccess" value="sameDomain" />
               <param name="quality" value="high" />
               <param name="flashvars" value="' . $config . '" />'
        . _video_get_parameters($node) .
        '<p>'. t('Your browser is not able to display this multimedia content.') .'</p>
      </object>';

    $output = theme('video_format_play', $output, t('http://get.adobe.com/flashplayer/'),
        t('Link to Adobe Flash Player Download Page'),
        t('Download latest Flash Player'));
  }
  return $output;
}

/**
 * Play Flash .swf files.
 *
 * @param $node
 *   object with node information
 *
 * @return
 *   string of content to display
 */
function theme_video_play_swf($node) {

  $url = _video_get_fileurl($node->vidfile);

  // this will be executed by not Internet Explorer browsers
  $output = '<!--[if !IE]> <-->
<object type="application/x-shockwave-flash" width="'. $node->video_scaled_x .'" height="'. $node->video_scaled_y .'"
data="'. $url .'">
<!--> <![endif]-->' . "\n";

  // this will be executed by Internet Explorer
  $output .= '<!--[if IE]>
<object type="application/x-shockwave-flash" width="'. $node->video_scaled_x .'" height="'. $node->video_scaled_y .'"
classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0">
<![endif]-->' . "\n";

  // params will be passed to both IE or not IE browsers
  $output .= '<param name="movie" value="'. $url .'" />'. "\n" .
              '<param name="wmode" value="transparent" />' . "\n"
  . _video_get_parameters($node) .
  '<p>'. t('Your browser is not able to display this multimedia content.') .'</p>
</object>';

  $output = theme('video_format_play', $output, t('http://www.macromedia.com/go/getflashplayer'), t('Link to Flash player download'), t('Download the latest Flash player'));
  return $output;
}



/**
 * Play Director .dcr/.dir files.
 *
 * @param $node
 *   object with node information
 *
 * @return
 *   string of content to display
 */

function theme_video_play_dcr($node) {

  $url = _video_get_fileurl($node->vidfile);

  // this will be executed by not Internet Explorer browsers
  $output = '<!--[if !IE]> <-->
<object type="application/x-director" width="'. $node->video_scaled_x .'" height="'. $node->video_scaled_y .'"
data="'. $url .'">
<!--> <![endif]-->' . "\n";

  // this will be executed by Internet Explorer
  $output .= '<!--[if IE]>
<object type="application/x-director" width="'. $node->video_scaled_x .'" height="'. $node->video_scaled_y .'"
classid="clsid:166B1BCA-3F9C-11CF-8075-444553540000"
codebase="http://download.macromedia.com/pub/shockwave/cabs/director/sw.cab#version=10,0,0,0">
<![endif]-->' . "\n";

// params will be passed to both IE or not IE browsers
  $output .= '<param name="src" value="'. $url .'" />' . "\n"
  . _video_get_parameters($node) .
  '<p>'. t('Your browser is not able to display this multimedia content.') .'</p>
</object>';

  $output = theme('video_format_play', $output, t('http://www.macromedia.com/shockwave/download/'),
                                      t('Link to Macromedia Shockwave Player Download Page'),
                                      t('Download latest Shockwave Player'));
   return $output;
}

/**
 * Play videos from in DivX format
 *
 * @see http://developer.apple.com/internet/ieembedprep.html
 * @param $node
 *   object with node information
 *
 * @return
 *   string of content to display
 */
function theme_video_play_divx($node) {
  //Increase the height to accommodate the player controls on the bottom.
  $height = $node->video_scaled_y + 20;

  $url = _video_get_fileurl($node->vidfile);

  $output = '<!-- [if IE] -->
<object classid="clsid:67DABFBF-D0AB-41fa-9C46-CC0F21721616" width="'.$node->video_scaled_x.'" height="'.$height.'" codebase="http://go.divx.com/plugin/DivXBrowserPlugin.cab">
<!--> <![endif]-->'. "\n";
  // this will be executed by not Internet Explorer browsers
  $output = '<!-- [if !IE] -->
<object type="video/divx" data="'.$url.'" width="'.$node->video_scaled_x.'" height="'.$height.'" mode="zero">
<!--> <![endif]-->'."\n";

   $output .= '<param name="src" value="'.$url.'"/>'."\n";
  $output .= '<param name="mode" value="zero"/>'."\n";
  $output .= '</object>';
  $output = theme('video_format_play', $output,t('http://www.divx.com/divx/webplayer/'),
                                     t('Link to DivX Download Page'),
                                     t('Download latest DivX Web Player'));
  return $output;
}

/**
 * Play videos from in Quicktime format
 *
 * @see http://developer.apple.com/internet/ieembedprep.html
 * @param $node
 *   object with node information
 *
 * @return
 *   string of content to display
 */
function theme_video_play_quicktime($node) {
  //Increase the height to accommodate the player controls on the bottom.
  $height = $node->video_scaled_y + 16;

  $url = _video_get_fileurl($node->vidfile);


  // this will be executed by not Internet Explorer browsers
  $output = '<!--[if !IE]> <-->
<object type="video/quicktime" width="'. $node->video_scaled_x .'" height="'. $height .'"
data="'. $url .'">
<!--> <![endif]-->' . "\n";

  // this will be executed by Internet Explorer
  $output .= '<!--[if IE]>
<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" codebase="http://www.apple.com/qtactivex/qtplugin.cab" width="'. $node->video_scaled_x .'" height="'. $height .'" scale="tofit" >
<![endif]-->' . "\n";

  // params will be passed to both IE or not IE browsers
  //GMM: kioskmode enabled so users don't bypass download security video through player
   $output .= '<param name="src" value="'. $url .'" />
              <param name="AUTOPLAY" value="'.(variable_get('video_autoplay', TRUE) ? TRUE : FALSE).'" />
              <param name="KIOSKMODE" value="true" />
              <param name="CONTROLLER" value="true" />' . "\n"
   . _video_get_parameters($node) .
   '<p>'. t('Your browser is not able to display this multimedia content.') .'</p>
</object>'; // only one </object> needed becouse only one opening tag has been parsed by browsers


  /*
  $output = '<script language="JavaScript" type="text/javascript">';
  $output .= "InsertQuicktimeVideo('{$node->vidfile}','$height','{$node->video_scaled_x}');";
  $output .= '</script>';
  */


  $output = theme('video_format_play', $output, t('http://www.apple.com/quicktime/download'),
                                      t('Link to QuickTime Download Page'),
                                      t('Download latest Quicktime Player'));
  return $output;
}

/**
 * Play videos from in Realmedia format
 *
 * @param $node
 *   object with node information
 *
 * @return
 *   string of content to display
 */
function theme_video_play_realmedia($node) {
  // Real's embeded player includes the controls
  // in the height
  $node->video_scaled_y += 40;

  $url = _video_get_fileurl($node->vidfile);

  // this will be executed by not Internet Explorer browsers
  $output = '<!--[if !IE]> <-->
<object type="audio/x-pn-realaudio-plugin" width="'. $node->video_scaled_x .'" height="'. $node->video_scaled_y .'"
data="'. $url .'">
<!--> <![endif]-->' . "\n";

  // this will be executed by Internet Explorer
  $output .= '<!--[if IE]>
<object type="audio/x-pn-realaudio-plugin" width="'. $node->video_scaled_x .'" height="'. $node->video_scaled_y .'"
classid="clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA" >
<![endif]-->' . "\n";

  // params will be passed to both IE or not IE browsers
   $output .= '<param name="src" value="'. $url .'" />
              <param name="_ExtentX" value="7276" />
              <param name="" value="3307" />
              <param name="AUTOSTART" value="'.(variable_get('video_autoplay', TRUE) ? 'true' : 'false').'" />
              <param name="SHUFFLE" value="0" />
              <param name="PREFETCH" value="0" />
              <param name="NOLABELS" value="0" />
              <param name="CONTROLS" value="All" />
              <param name="CONSOLE" value="Clip1" />
              <param name="LOOP" value="0" />
              <param name="NUMLOOP" value="0" />
              <param name="CENTER" value="0" />
              <param name="MAINTAINASPECT" value="1" />
              <param name="BACKGROUNDCOLOR" value="#000000" />'
   . _video_get_parameters($node) .
   '<p>'. t('Your browser is not able to display this multimedia content.') .'</p>
</object>'; // only one </object> needed becouse only one opening tag has been parsed by browsers


  $output = theme('video_format_play', $output, t('http://www.real.com/'),
                                      t('Link to Real'),
                                      t('Download latest Realmedia Player'));
  return $output;
}

/**
 * Play videos from in WindowsMediaVideo format
 *
 * @param $node
 *   object with node information
 *
 * @return
 *   string of content to display
 */
function theme_video_play_windowsmedia($node) {
  // Windows Media's embeded player includes the controls in the height
  $node->video_scaled_y += 68;
  $url = _video_get_fileurl($node->vidfile);

  // this will be executed by not Internet Explorer browsers
  $output = '<!--[if !IE]> <-->
<object type="application/x-mplayer2" width="'. $node->video_scaled_x .'" height="'. $node->video_scaled_y .'"
data="'. $url .'">
<!--> <![endif]-->' . "\n";

  // this will be executed by Internet Explorer
  $output .= '<!--[if IE]>
<object type="application/x-oleobject" width="'. $node->video_scaled_x .'" height="'. $node->video_scaled_y .'"
classid="clsid:6BF52A52-394A-11d3-B153-00C04F79FAA6" >
<![endif]-->' . "\n";

  // params will be passed to both IE or not IE browsers
   $output .= '<param name="src" value="'. $url .'" />
              <param name="URL" value="'.$url.'" />
              <param name="animationatStart" value="true" />
              <param name="transparentatStart" value="true" />
              <param name="autoStart" value="'.(variable_get('video_autoplay', TRUE) ? 'true' : 'false').'" />
              <param name="showControls" value="true" />
              <param name="loop" value="true" />'
   . _video_get_parameters($node) .
   '<p>'. t('Your browser is not able to display this multimedia content.') .'</p>
</object>'; // only one </object> needed becouse only one opening tag has been parsed by browsers


  $output = "\n<div id=\"video-player\">\n" . $output;
  $output .= "<p>\n". t('Problems viewing videos?');
  $output .= "<br />\n";
  $output .= l(t('Download latest Windows Media Player'), t('http://windowsupdate.microsoft.com/'), array('attributes' => array('title' => t('Link to Windows Update')), 'absolute' => TRUE));
  //GMM: add link to Windows Media Player plug-in for Firefox browsers
    $output .= "<br />\n";
    $output .= l(t('Download the plug-in for Firefox'), t('http://support.mozilla.com/en-US/kb/Using+the+Windows+Media+Player+plugin+with+Firefox'), array('attributes' => array('title' => t('Link to Firefox Plug-in')), 'absolute' => TRUE));
  $output .= "\n</p> \n </div>\n";
  return $output;
}




/**
 * Play Ogg Theora videos with Cortado Applet
 *
 * @param $node
 *   object with node information
 *
 * @return
 *   string of content to display
 */
function theme_video_play_ogg_theora($node) {
  global $base_url;
  $cortado_location = variable_get('video_cortado', $base_url . '/cortado.jar');
  $url = _video_get_fileurl($node->vidfile);

  $width = ($node->video_scaled_x ? $node->video_scaled_x : '425');
  $height = ($node->video_scaled_y ? $node->video_scaled_y : '350');

  $output = '
  <!--[if !IE]>-->
  <object classid="java:com.fluendo.player.Cortado.class"
          type="application/x-java-applet"
          archive="' . $cortado_location . '"
          width="' . $width . '" height="' . $height . '" >
  <!--<![endif]-->
    <object classid="clsid:8AD9C840-044E-11D1-B3E9-00805F499D93"
              codebase="http://java.sun.com/update/1.5.0/jinstall-1_5_0-windows-i586.cab"
              width="' . $width . '" height="' . $height . '" >
        <param name="code" value="com.fluendo.player.Cortado" />
    <!--[if !IE]>-->
    </object>
    <!--<![endif]-->
      <!-- IE and Konqueror browser need the archive param -->
      <param name="archive" value="' . $cortado_location . '" />
      <param name="url" value="' . $url . '"/>
      <param name="local" value="false" />
      <param name="keepaspect" value="true" />
      <param name="video" value="true" />
      <param name="audio" value="true" />
      <param name="seekable" value="true" />
      <param name="duration" value="' . $node->playtime_seconds . '" />
      <param name="bufferSize" value="200" />
      <strong>
          This browser does not have a Java Plug-in.<br />
          <a href="http://java.com/download/">
            Get the latest Java Plug-in here.
          </a>
      </strong>
  </object>
  ';

  $output = theme('video_format_play', $output,
    t('http://java.com/download/'), t('Link to java.com'), t('Download Java'));
  return $output;
}

/**
 * Play videos hosted on youtube.com
 * Allows users to host videos on youtube.com and then use the video ID to post it in the module.
 * In the future it could also use the youtube developer API to get info and comments of the video.
 *
 * @param $node
 *   object with node information
 *
 * @return
 *   string of content to display
 */
function theme_video_play_youtube($node) {
  $width = ($node->video_scaled_x ? $node->video_scaled_x : '425');
  $height = ($node->video_scaled_y ? $node->video_scaled_y : '350');

  $id = _video_youtube_get_id(check_plain($node->vidfile));

  // related video setting
  $rel = variable_get('video_youtube_related', false) ? '1' : '0';

  // this will be executed by not Internet Explorer browsers
  $output = '<!--[if !IE]> <-->
<object type="application/x-shockwave-flash" width="'. $width .'" height="'. $height .'"
data="http://www.youtube.com/v/' . $id . '&rel='.$rel.'">
<!--> <![endif]-->' . "\n";

  // this will be executed by Internet Explorer
  $output .= '<!--[if IE]>
<object type="application/x-shockwave-flash" width="'. $width .'" height="'. $height .'"
classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0">
<![endif]-->' . "\n";

  // params will be passed to both IE or not IE browsers
  $output .= '<param name="movie" value="http://www.youtube.com/v/' . $id . '&rel='.$rel.'" />' . "\n"
      . '<param name="wmode" value="transparent" />' . "\n"
      . _video_get_parameters($node) .
      '<p>'. t('Your browser is not able to display this multimedia content.') .'</p>
</object>';


  $output = theme('video_format_play', $output, t('http://www.google.com/support/youtube'), t('Link to youtube.com'), t('youtube.com'));
  return $output;
}