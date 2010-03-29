<?php
// $Id: node.tpl.php,v 1.10 2009/11/02 17:42:27 johnalbin Exp $

/**
 * @file
 * Theme implementation to display a node.
 *
 * Available variables:
 * - $title: the (sanitized) title of the node.
 * - $content: Node body or teaser depending on $teaser flag.
 * - $user_picture: The node author's picture from user-picture.tpl.php.
 * - $date: Formatted creation date. Preprocess functions can reformat it by
 *   calling format_date() with the desired parameters on the $created variable.
 * - $name: Themed username of node author output from theme_username().
 * - $node_url: Direct url of the current node.
 * - $terms: the themed list of taxonomy term links output from theme_links().
 * - $display_submitted: whether submission information should be displayed.
 * - $links: Themed links like "Read more", "Add new comment", etc. output
 *   from theme_links().
 * - $classes: String of classes that can be used to style contextually through
 *   CSS. It can be manipulated through the variable $classes_array from
 *   preprocess functions. The default values can be one or more of the
 *   following:
 *   - node: The current template type, i.e., "theming hook".
 *   - node-[type]: The current node type. For example, if the node is a
 *     "Blog entry" it would result in "node-blog". Note that the machine
 *     name will often be in a short form of the human readable label.
 *   - node-teaser: Nodes in teaser form.
 *   - node-preview: Nodes in preview mode.
 *   The following are controlled through the node publishing options.
 *   - node-promoted: Nodes promoted to the front page.
 *   - node-sticky: Nodes ordered above other non-sticky nodes in teaser
 *     listings.
 *   - node-unpublished: Unpublished nodes visible only to administrators.
 *   The following applies only to viewers who are registered users:
 *   - node-by-viewer: Node is authored by the user currently viewing the page.
 *
 * Other variables:
 * - $node: Full node object. Contains data that may not be safe.
 * - $type: Node type, i.e. story, page, blog, etc.
 * - $comment_count: Number of comments attached to the node.
 * - $uid: User ID of the node author.
 * - $created: Time the node was published formatted in Unix timestamp.
 * - $classes_array: Array of html class attribute values. It is flattened
 *   into a string within the variable $classes.
 * - $zebra: Outputs either "even" or "odd". Useful for zebra striping in
 *   teaser listings.
 * - $id: Position of the node. Increments each time it's output.
 *
 * Node status variables:
 * - $build_mode: Build mode, e.g. 'full', 'teaser'...
 * - $teaser: Flag for the teaser state (shortcut for $build_mode == 'teaser').
 * - $page: Flag for the full page state.
 * - $promote: Flag for front page promotion state.
 * - $sticky: Flags for sticky post setting.
 * - $status: Flag for published status.
 * - $comment: State of comment settings for the node.
 * - $readmore: Flags true if the teaser content of the node cannot hold the
 *   main body content.
 * - $is_front: Flags true when presented in the front page.
 * - $logged_in: Flags true when the current user is a logged-in member.
 * - $is_admin: Flags true when the current user is an administrator.
 *
 * The following variables are deprecated and will be removed in Drupal 7:
 * - $picture: This variable has been renamed $user_picture in Drupal 7.
 * - $submitted: Themed submission information output from
 *   theme_node_submitted().
 *
 * @see template_preprocess()
 * @see template_preprocess_node()
 * @see zen_preprocess()
 * @see zen_preprocess_node()
 * @see zen_process()
 */

//$base = drupal_get_path('module', 'portaltvsl');
//drupal_add_js($base . '/mv_embed/mv_embed.js', 'module');

global $base_url;

$width = $node->field_width[0]["value"] ? $node->field_width[0]["value"] : 320;
$node->videox = ($node->videox) ? $node->videox : $width;
$height = $node->field_height[0]["value"] ? $node->field_height[0]["value"] : 240;
$node->videoy = ($node->videoy) ? $node->videoy : $height;
$snapshot = $base_url . '/themes/tvsl/images/tvsl-banner-logo.png';

?>
<div id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?> clearfix">
  <?php print $user_picture; ?>

  <?php if (!$page): ?>
    <h2 class="title"><a href="<?php print $node_url; ?>"><?php print $title; ?></a></h2>
  <?php endif; ?>

  <?php if ($unpublished): ?>
    <div class="unpublished"><?php print t('Unpublished'); ?></div>
  <?php endif; ?>

  <?php if ($display_submitted || $terms): ?>
    <div class="meta">
      <?php if ($display_submitted): ?>
        <span class="submitted">
          <?php
            print t('Submitted by !username on !datetime',
              array('!username' => $name, '!datetime' => $date));
          ?>
        </span>
      <?php endif; ?>

      <?php if ($terms): ?>
        <div class="terms terms-inline"><?php print $terms; ?></div>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <div class="content">
   <div id="video-media" class="video-media-body">
    
   <?php
   
  $element = new stdClass();
  $element->width = $width;
  $element->height = $height;
  $element->poster = $snapshot;
  $element->mediafile = $node->field_urlstream[0]['value'];
  $element->duration = $node->playtime_seconds;
  $element->showcontrols = true;
  $element->autoplay = true;
  $element->statusheight = 30;
  $element->autobuffer = true;
    
  print theme('portaltvsl_player',$element); 
  print theme('portaltvsl_player_embed_toolbar',$element,$node);    

  $tabs = array();
  if (isset($node->field_chatroom[0]["nid"])) {
	$tabs['first'] = array(
	'title' => t('Chat'),
	'type' => 'block',
	'bid' => 'block_delta_5',
	'hide_title' => TRUE,
	);
  }
  if (isset($node->field_twitter_search[0]['value'])) {
	$tabs['second'] = array(
	'title' => t('Twitter'),
	'type' => 'block',
	'bid' => 'block_delta_6',
	'hide_title' => TRUE,
	);
  }  
  if(isset($node->field_salascreencap[0]['nid'])){
   $tabs['third'] = array(
	'title' => t('Slides'),
	'type' => 'block',
	'bid' => 'block_delta_7', 
	'hide_title' => TRUE,
   );
  }

  $quicktabs['qtid'] = 'aovivo';
  $quicktabs['tabs'] = $tabs;
  $quicktabs['style'] = 'default';
  $quicktabs['ajax'] = FALSE;
  print theme('quicktabs', $quicktabs);
  ?>
  </div>
  
  <?php
  $view = views_get_current_view();
  if ($view->name != 'vod_images') {
	print '<div id="video-content">' . $node->content['body']['#value'] . '</div>';
	?>
	  <div class="video-label">
	  <ul>
	  <li><b>Arquivo: </b>	<img class="field-icon-ogv" alt="<?php print $node->current_video_upload_file->filemime ?> icon" src="<?php print $base_url.'/modules/filefield/icons/protocons/16x16/mimetypes/video-x-generic.png' ?>" />
<a href="<?php print $node->field_urlstream[0]["value"] ?>" type="video/ogv"><?php print substr(strrchr($node->field_urlstream[0]["value"], "/"),1)?></a></li>
	  <li><b>Data: </b><?php print $node->field_aovivodatetime[0]["view"]?></li>
	  <li><b>Estado: </b><?php print $node->field_aovivo_status[0]["view"]?></li>
	  </ul>
	</div>
	<div id="video-fivestar">
	<?php print $node->content['fivestar_widget']['#value'] ?>
	</div>
	<?php
  }			     
  ?>
  </div>
  

  <?php print $links; ?>
</div> <!-- /.node -->

