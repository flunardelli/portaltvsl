<?php
// $Id: template.php,v 1.21 2009/08/12 04:25:15 johnalbin Exp $

/**
 * @file
 * Contains theme override functions and preprocess functions for the theme.
 *
 * ABOUT THE TEMPLATE.PHP FILE
 *
 *   The template.php file is one of the most useful files when creating or
 *   modifying Drupal themes. You can add new regions for block content, modify
 *   or override Drupal's theme functions, intercept or make additional
 *   variables available to your theme, and create custom PHP logic. For more
 *   information, please visit the Theme Developer's Guide on Drupal.org:
 *   http://drupal.org/theme-guide
 *
 * OVERRIDING THEME FUNCTIONS
 *
 *   The Drupal theme system uses special theme functions to generate HTML
 *   output automatically. Often we wish to customize this HTML output. To do
 *   this, we have to override the theme function. You have to first find the
 *   theme function that generates the output, and then "catch" it and modify it
 *   here. The easiest way to do it is to copy the original function in its
 *   entirety and paste it here, changing the prefix from theme_ to tvsl_.
 *   For example:
 *
 *     original: theme_breadcrumb()
 *     theme override: tvsl_breadcrumb()
 *
 *   where tvsl is the name of your sub-theme. For example, the
 *   zen_classic theme would define a zen_classic_breadcrumb() function.
 *
 *   If you would like to override any of the theme functions used in Zen core,
 *   you should first look at how Zen core implements those functions:
 *     theme_breadcrumbs()      in zen/template.php
 *     theme_menu_item_link()   in zen/template.php
 *     theme_menu_local_tasks() in zen/template.php
 *
 *   For more information, please visit the Theme Developer's Guide on
 *   Drupal.org: http://drupal.org/node/173880
 *
 * CREATE OR MODIFY VARIABLES FOR YOUR THEME
 *
 *   Each tpl.php template file has several variables which hold various pieces
 *   of content. You can modify those variables (or add new ones) before they
 *   are used in the template files by using preprocess functions.
 *
 *   This makes THEME_preprocess_HOOK() functions the most powerful functions
 *   available to themers.
 *
 *   It works by having one preprocess function for each template file or its
 *   derivatives (called template suggestions). For example:
 *     THEME_preprocess_page    alters the variables for page.tpl.php
 *     THEME_preprocess_node    alters the variables for node.tpl.php or
 *                              for node-forum.tpl.php
 *     THEME_preprocess_comment alters the variables for comment.tpl.php
 *     THEME_preprocess_block   alters the variables for block.tpl.php
 *
 *   For more information on preprocess functions and template suggestions,
 *   please visit the Theme Developer's Guide on Drupal.org:
 *   http://drupal.org/node/223440
 *   and http://drupal.org/node/190815#template-suggestions
 */


/**
 * Implementation of HOOK_theme().
 */
function tvsl_theme(&$existing, $type, $theme, $path) {
  $hooks = zen_theme($existing, $type, $theme, $path);
  // Add your theme hooks like this:
  /*
  $hooks['hook_name_here'] = array( // Details go here );
  */
  // @TODO: Needs detailed comments. Patches welcome!
  return $hooks;
}

/**
 * Override or insert variables into all templates.
 *
 * @param $vars
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered (name of the .tpl.php file.)
 */
/* -- Delete this line if you want to use this function
function tvsl_preprocess(&$vars, $hook) {
  $vars['sample_variable'] = t('Lorem ipsum.');
}
// */

/**
 * Override or insert variables into the page templates.
 *
 * @param $vars
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("page" in this case.)
 */
/* -- Delete this line if you want to use this function
function tvsl_preprocess_page(&$vars, $hook) {
  $vars['sample_variable'] = t('Lorem ipsum.');
}
// */

/**
 * Override or insert variables into the node templates.
 *
 * @param $vars
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("node" in this case.)
 */
/* -- Delete this line if you want to use this function
function tvsl_preprocess_node(&$vars, $hook) {
  $vars['sample_variable'] = t('Lorem ipsum.');

  // Optionally, run node-type-specific preprocess functions, like
  // tvsl_preprocess_node_page() or tvsl_preprocess_node_story().
  $function = __FUNCTION__ . '_' . $vars['node']->type;
  if (function_exists($function)) {
    $function($vars, $hook);
  }
}

function zen_progression_preprocess_page(&$vars, $hook) {
   
    if ($vars['is_front']) {
          $css = drupal_add_css(drupal_get_path('theme', 'zen_SUBTHEME').'/page-front.css', 'theme', 'all');
	      }
	          $vars['styles'] = drupal_get_css();
		  }
// */

/**
 * Override or insert variables into the comment templates.
 *
 * @param $vars
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("comment" in this case.)
 */
/* -- Delete this line if you want to use this function
function tvsl_preprocess_comment(&$vars, $hook) {
  $vars['sample_variable'] = t('Lorem ipsum.');
}
// */

/**
 * Override or insert variables into the block templates.
 *
 * @param $vars
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("block" in this case.)
 */
/* -- Delete this line if you want to use this function
function tvsl_preprocess_block(&$vars, $hook) {
  $vars['sample_variable'] = t('Lorem ipsum.');
}
// */

function tvsl_tagadelic_weighted($terms) {
  $output = '';
    foreach ($terms as $term) {
        $output .= l($term->name, taxonomy_term_path($term), array('attributes' => array('class' => "tagadelic level$term->weight", 'rel' => 'tag'))) ."<span class=\"tagadelic-term-separator\"></span>\n";
    }
    return $output;
}
	    
function tvsl_tagadelic_more($vid) {
  return "<div class='more-link'>". l(t('more'), "tagadelic/chunk/$vid") ."</div>";
}

function tvsl_views_slideshow_thumbnailhover_control_previous($id, $view, $options) {
  return '<a href="#" id="views_slideshow_thumbnailhover_prev_' . $id . '" class="views_slideshow_thumbnailhover_previous"><img alt="'.t('Previous').'" src="'.drupal_get_path('theme', 'tvsl').'/images/t-previous.png" border="0"></a>'."\n";
}

function tvsl_views_slideshow_thumbnailhover_control_pause($id, $view, $options) {
  return '<a href="#" id="views_slideshow_thumbnailhover_playpause_' . $id . '" class="views_slideshow_thumbnailhover_pause"><img alt="'.t('Pause').'" src="'.drupal_get_path('theme', 'tvsl').'/images/t-pause.png" border="0"></a>'."\n";
}

function tvsl_views_slideshow_thumbnailhover_control_next($id, $view, $options) {
  return '<a href="#" id="views_slideshow_thumbnailhover_next_' . $id . '" class="views_slideshow_thumbnailhover_next"><img alt="'.t('Next').'" src="'. drupal_get_path('theme', 'tvsl').'/images/t-next.png" border="0"></a>'."\n";
}

//function tvsl_chatroom_chat($node){
//  return "";
//}

//function tvsl_chatroom_buttons($node){
//  return "";
//}

function tvsl_chatroom_user_list($node){
  return "";
}

function chatload($chat_nid) {
  $cnode = node_load($chat_nid);
  $node = node_prepare($cnode);
  drupal_add_css(drupal_get_path('module', 'chatroom') .'/chatroom.css');
  if (!isset($node->chat->when_archived)) {
	if (chatroom_max_users_reached($node)) {
  	  $content = theme('chatroom_chat_max_users', $node);
  	}
    else {
      if (!chatroom_chat_register_user($node)) {
        chatroom_update_last_seen_time($node, session_id());
  	  }
  	  $node->chat->users = chatroom_load_online_users($node);
  	  chatroom_add_js($node);
  	  $node->chat->latest_messages = array();
  	  foreach (chatroom_chat_load_latest_messages($node) as $message) {
    	if ($message->msg_type == 'private_message' && $user->uid != $message->recipient_uid) {
      	  continue;
    	}
    	$node->chat->latest_messages[] = $message;
  	  }
  	  $content = theme('chatroom_chat', $node);
  	}
	}
	return $content;
}
