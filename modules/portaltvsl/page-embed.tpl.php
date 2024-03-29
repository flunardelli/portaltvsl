<?php
// $Id: page.tpl.php,v 1.26 2009/11/05 13:54:57 johnalbin Exp $

/**
 * @file
 * Theme implementation to display a single Drupal page.
 *
 * Available variables:
 *
 * General utility variables:
 * - $base_path: The base URL path of the Drupal installation. At the very
 *   least, this will always default to /.
 * - $css: An array of CSS files for the current page.
 * - $directory: The directory the template is located in, e.g. modules/system
 *   or themes/garland.
 * - $is_front: TRUE if the current page is the front page. Used to toggle the mission statement.
 * - $logged_in: TRUE if the user is registered and signed in.
 * - $is_admin: TRUE if the user has permission to access administration pages.
 *
 * Page metadata:
 * - $language: (object) The language the site is being displayed in.
 *   $language->language contains its textual representation.
 *   $language->dir contains the language direction. It will either be 'ltr' or 'rtl'.
 * - $head_title: A modified version of the page title, for use in the TITLE tag.
 * - $head: Markup for the HEAD section (including meta tags, keyword tags, and
 *   so on).
 * - $styles: Style tags necessary to import all CSS files for the page.
 * - $scripts: Script tags necessary to load the JavaScript files and settings
 *   for the page.
 * - $classes: String of classes that can be used to style contextually through
 *   CSS. It should be placed within the <body> tag. When selecting through CSS
 *   it's recommended that you use the body tag, e.g., "body.front". It can be
 *   manipulated through the variable $classes_array from preprocess functions.
 *   The default values can be one or more of the following:
 *   - front: Page is the home page.
 *   - not-front: Page is not the home page.
 *   - logged-in: The current viewer is logged in.
 *   - not-logged-in: The current viewer is not logged in.
 *   - node-type-[node type]: When viewing a single node, the type of that node.
 *     For example, if the node is a "Blog entry" it would result in "node-type-blog".
 *     Note that the machine name will often be in a short form of the human readable label.
 *   The following only apply with the default 'sidebar_first' and 'sidebar_second' block regions:
 *     - two-sidebars: When both sidebars have content.
 *     - no-sidebars: When no sidebar content exists.
 *     - one-sidebar and sidebar-first or sidebar-second: A combination of the
 *       two classes when only one of the two sidebars have content.
 * - $node: Full node object. Contains data that may not be safe. This is only
 *   available if the current page is on the node's primary url.
 *
 * Site identity:
 * - $front_page: The URL of the front page. Use this instead of $base_path,
 *   when linking to the front page. This includes the language domain or prefix.
 * - $logo: The path to the logo image, as defined in theme configuration.
 * - $site_name: The name of the site, empty when display has been disabled
 *   in theme settings.
 * - $site_slogan: The slogan of the site, empty when display has been disabled
 *   in theme settings.
 * - $mission: The text of the site mission, empty when display has been disabled
 *   in theme settings.
 *
 * Navigation:
 * - $search_box: HTML to display the search box, empty if search has been disabled.
 * - $primary_links (array): An array containing the Primary menu links for the
 *   site, if they have been configured.
 * - $secondary_links (array): An array containing the Secondary menu links for
 *   the site, if they have been configured.
 * - $breadcrumb: The breadcrumb trail for the current page.
 *
 * Page content (in order of occurrence in the default page.tpl.php):
 * - $title: The page title, for use in the actual HTML content.
 * - $messages: HTML for status and error messages. Should be displayed prominently.
 * - $tabs: Tabs linking to any sub-pages beneath the current page (e.g., the
 *   view and edit tabs when displaying a node).
 * - $help: Dynamic help text, mostly for admin pages.
 * - $content: The main content of the current page.
 * - $feed_icons: A string of all feed icons for the current page.
 *
 * Footer/closing data:
 * - $footer_message: The footer message as defined in the admin settings.
 * - $closure: Final closing markup from any modules that have altered the page.
 *   This variable should always be output last, after all other dynamic content.
 *
 * Helper variables:
 * - $classes_array: Array of html class attribute values. It is flattened
 *   into a string within the variable $classes.
 *
 * Regions:
 * - $content_top: Items to appear above the main content of the current page.
 * - $content_bottom: Items to appear below the main content of the current page.
 * - $navigation: Items for the navigation bar.
 * - $sidebar_first: Items for the first sidebar.
 * - $sidebar_second: Items for the second sidebar.
 * - $header: Items for the header region.
 * - $footer: Items for the footer region.
 * - $page_closure: Items to appear below the footer.
 *
 * The following variables are deprecated and will be removed in Drupal 7:
 * - $body_classes: This variable has been renamed $classes in Drupal 7.
 *
 * @see template_preprocess()
 * @see template_preprocess_page()
 * @see zen_preprocess()
 * @see zen_process()
 */

if (arg(0) == 'embed' && is_numeric(arg(2))) {
    if ($node = node_load(arg(2)) and (!in_array($node->type,array('video','audio','aovivo')))) {
	  die();
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php print $language->language; ?>" lang="<?php print $language->language; ?>" dir="<?php print $language->dir; ?>">

<head>
  <title>embeded content</title>
  <?php print $head; ?>
  <?php print $styles; ?>
</head>
<body class="<?php print $classes; ?>">
  <div class="content">
  <?
global $base_url; 
global $base_path;	

if ($node->type == 'aovivo'){
  $node->videox = $node->field_width[0]["value"];
  $node->videoy = $node->field_height[0]["value"];
  $width = $node->videox;
  $height = $node->videoy;
  $video_path = url($node->field_urlstream[0]["value"]);
  $snapshot = $base_url . '/themes/tvsl/images/tvsl-banner-logo.png';
  $autoplay = 'true';
  $description = '<span style="font-size: 10px">' .$node->field_aovivo_status[0]["value"] .( ($node->field_aovivodatetime[0]["value"]) ? ' - '. $node->field_aovivodatetime[0]["value"] : '').'</span>';
} else if ($node->type == 'video') {
  $video_path = url($node->current_video_upload_file->filepath);
  $width = '480';
  $width = ($node->videox) ? $node->videox : $width;
  $height = '360';
  $height = ($node->videoy) ? $node->videoy : $height;		  
  if ($node->iid) {
  	$imagenode = node_load($node->iid);
  	$snapshot = $base_url . '/' . $imagenode->images['snapshot'];
  } else {
    $snapshot = '';
  }
  $autoplay = 'false';
  $description = '';
} 

if (in_array(arg(1),array('video','aovivo'))) {  
	?>
	<div id="video-media-embed" class="video-media-body">
	<?php	
	$element = new stdClass();
    $element->width = $width;
	$element->height = $height;
	$element->poster = $snapshot;
	$element->mediafile =  $video_path;
	$element->showcontrols = true;
	$element->autoplay = $autoplay;
	$element->statusheight = 30;
	$element->autobuffer = true;  
	print theme('portaltvsl_player',$element);	
	?>
	</div>
	<a href="<?php print $base_url?>" target="new"><img src="<?php print $base_url;?>/themes/tvsl/images/logo-embed.png" border="0"/></a>  
	<span id="video-label" style="width:<?=$width?>;font-size: 14px;font-weight: bold; vertical-align: top;line-height: 30px;"><? print l($node->title,$node->path,array('target' => 'new')) ?> <?php print $description ?></span>
  <? 
  } else if(arg(1) == 'radio') {
  ?>  
  
  <?
  } 
  ?>
  </div>
</body>
</html>
