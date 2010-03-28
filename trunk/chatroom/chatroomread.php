<?php
// $Id: chatroomread.php,v 1.29.4.9 2010/03/14 05:39:55 justinrandell Exp $

/**
 * @file
 * Process chatroom polling requests.
 *
 * This file is a performance hack, aimed at making moderately sized rooms
 * possible with this module.
 * 
 * The intent is to handle the polling requests from chatrooms without doing 
 * a full Drupal bootstrap unless absolutely necessary. All simple polling 
 * requests are handled by this script, as they are by far the most common 
 * type of request, and are very easy to check against a cache.
 *
 * First, we check to see if the last message seen in a given chat client-side
 * is older than the latest message in that chat server-side. 
 *
 * Only if the chat being polled has messages newer than what the requesting
 * client has seen do we bootstrap Drupal. 
 */

// We need the $latest_msg_id, $chat_id and $chat_cache_file to check the
// cache for this chat. 
if (!isset($_POST['latest_msg_id']) || !preg_match('/^\d+$/', $_POST['latest_msg_id'])) {
  exit;
}
$client_latest_msg_id = $_POST['latest_msg_id'];

if (!isset($_POST['chat_id']) || !preg_match('/^\d+$/', $_POST['chat_id'])) {
  exit;
}
$chat_id = $_POST['chat_id'];

if (!isset($_POST['chat_cache_directory']) || !is_dir($_POST['chat_cache_directory'])) {
  exit;
}
$chat_cache_file = $_POST['chat_cache_directory'] . '/chatroom.chat.' . $chat_id . '.cache';

if (!isset($_POST['skip_cache'])) {
  exit;
}
$skip_cache = $_POST['skip_cache'] == 1 ? TRUE : FALSE;

// Bootstrap configuration to make sure we get the right
// value from session_name() and some paths for caching.
require_once './includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_CONFIGURATION);

// We let the client signal that we should skip the cache. Right now we're 
// using this to make sure users last-seen time is updated, and there may 
// be more uses for it down the track.
if (!$skip_cache) {
  
  if (file_exists($chat_cache_file)) {
    // Do a quick DoS check - we don't validate the path, so we have to make
    // sure we're not reading arbitrarily big files into memory. Our cache file 
    // should contain a single numeric id. So, if the file is bigger than 25 
    // bytes, something is fishy, and we should just bail out.
    $file_stats = stat($chat_cache_file);
    if ($file_stats['size'] > 25) {
      exit;
    }

    $server_latest_msg_id = trim(file_get_contents($chat_cache_file));
    if ($server_latest_msg_id == $client_latest_msg_id) {
      print json_encode(array('data' => array('cacheHit' => 1, 'messages' => array())));
      exit;
    }
  }
  
  $chatroom_module_dir = variable_get('chatroom_module_dir', 'sites/all/modules/chatroom');
  require_once dirname(__FILE__) . "/$chatroom_module_dir/chatroom.cache.inc";

  if ($chat_user = chatroom_get_cached_user()) {
    if (in_array($chat_id, $chat_user->allowed_chats)) {
      if ($cached_messages = chatroom_get_cached_messages($chat_id)) {
        $valid_cache_messages = array();
        foreach ($cached_messages as $message) {     
          if ($message->cmid > $client_latest_msg_id) {
            if ($message->type == 'private_message' && $chat_user->uid != $message->recipient_uid) {
              continue;
            }
            $valid_cache_messages[] = $message;
          }
        }
        if ($valid_cache_messages) {
          print json_encode(array('data' => array('cacheHit' => 1, 'messages' => $valid_cache_messages)));
          exit;
        }
      }
    }
  }
}

// Make this look like a normal request to Drupal, then execute index.php.
$_GET['q'] = "chatroom/chat/get/latest/messages/$chat_id/$client_latest_msg_id";
require_once './index.php';

