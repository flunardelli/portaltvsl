// $Id: chatroom.js,v 1.50.4.31 2010/07/08 03:35:41 justinrandell Exp $

Drupal.chatroom = Drupal.chatroom || {};

/**
 * only run this code once.
 */
$(document).ready(function () {
  setInterval("Drupal.chatroom.poll()", Drupal.settings.chatroom.pollInterval * 1000);
  Drupal.settings.chatroom.pageTitle = document.title;
  Drupal.settings.chatroom.hasFocus = true;
  if (Drupal.settings.chatroom.latestMsgId > 0) {
    var targetOffset = $('div.new-message:last').offset().top;
    var boardOffset = $('#chatroom-board').offset().top;
    var scrollAmount = targetOffset - boardOffset;
    $('#chatroom-board').animate({scrollTop: '+='+ scrollAmount +'px'}, 500);
    $('.new-message').removeClass('new-message');
  }
  $(self).focus(
    function() {
      clearInterval(Drupal.settings.chatroom.warnInterval);
      Drupal.settings.chatroom.hasFocus = true;
      document.title = Drupal.settings.chatroom.pageTitle;
    }
  );
  $(self).blur(
    function() {
      Drupal.settings.chatroom.hasFocus = false;
    }
  );
  Drupal.settings.chatroom.isPopout = opener == undefined ? 'false' : 'true';
});

/**
 * Add behaviours to chatroom elements.
 */
Drupal.behaviors.chatroom = function(context) {
  $("#chatroom-popout-link").click(function (e) {
    e.preventDefault();
    e.stopPropagation();
    url: Drupal.settings.basePath + Drupal.settings.chatroom.banUserPath + '/' + 
    window.open(Drupal.settings.basePath + 'node/' + Drupal.settings.chatroom.chatId + '?chatroom_popout=true', '', Drupal.settings.chatroom.popoutParams);
  });

  $('#edit-chatroom-message-entry-box').keyup(function(e) { 
    var messageText = $('#edit-chatroom-message-entry-box').val().replace(/^\s+|\s+$/g, '');
    if (messageText && e.keyCode == 13 && !e.shiftKey && !e.ctrlKey) {
      Drupal.chatroom.postMessage(messageText);
      $('#edit-chatroom-message-entry-box').val('').focus();
    }
    else {
      return true;
    }
  });
  $('#edit-chatroom-message-entry-submit').click(function (e) {
    e.preventDefault();
    e.stopPropagation();
    var messageText = $('#edit-chatroom-message-entry-box').val().replace(/^\s+|\s+$/g, '');
    if (messageText) {
      Drupal.chatroom.postMessage(messageText);
      $('#edit-chatroom-message-entry-box').val('').focus();
    }
  });

  $('.chatroom-kick-user-link').click(function (e) {
    e.preventDefault();
    e.stopPropagation();
    Drupal.chatroom.kickUser(e.target.parentNode.id);
  });

  $('.chatroom-ban-user-link').click(function (e) {
    e.preventDefault();
    e.stopPropagation();
    Drupal.chatroom.banUser(e.target.parentNode.id);
  });

  $('.chatroom-remove-user-link').click(function (e) {
    e.preventDefault();
    e.stopPropagation();
    Drupal.chatroom.removeUser(e.target.parentNode.id);
  });
}

Drupal.chatroom.banUser = function(uid) {
  $.ajax({
    type: 'POST',
    url: Drupal.settings.basePath + Drupal.settings.chatroom.banUserPath + '/' + Drupal.settings.chatroom.chatId,
    dataType: 'json',
    success: Drupal.chatroom.pollHandler,
    data: { 
      uid: uid
    }
  });
}

Drupal.chatroom.kickUser = function(uid) {
  $.ajax({
    type: 'POST',
    url: Drupal.settings.basePath + Drupal.settings.chatroom.kickUserPath + '/' + Drupal.settings.chatroom.chatId,
    dataType: 'json',
    success: Drupal.chatroom.pollHandler,
    data: { 
      uid: uid,
      is_popout: Drupal.settings.chatroom.isPopout
    }
  });
}

Drupal.chatroom.removeUser = function(uid) {
  $.ajax({
    type: 'POST',
    url: Drupal.settings.basePath + Drupal.settings.chatroom.removeUserPath + '/' + Drupal.settings.chatroom.chatId,
    dataType: 'json',
    success: Drupal.chatroom.pollHandler,
    data: { 
      uid: uid,
      is_popout: Drupal.settings.chatroom.isPopout
    }
  });
}

Drupal.chatroom.poll = function() {
  var skipCacheCheck = 0;
  if (Drupal.settings.chatroom.successiveCacheHits > Drupal.settings.chatroom.skipCacheCheckCount) {
    skipCacheCheck = 1;
  }
  
  $.ajax({
    type: 'POST',
    url: Drupal.settings.basePath + 'chatroomread.php',
    dataType: 'json',
    success: Drupal.chatroom.pollHandler,
    data: {
      latest_msg_id: Drupal.settings.chatroom.latestMsgId, 
      chat_cache_directory: Drupal.settings.chatroom.cacheDirectory, 
      chat_id: Drupal.settings.chatroom.chatId,
      skip_cache: skipCacheCheck,
      is_popout: Drupal.settings.chatroom.isPopout,
      successive_cache_hits: Drupal.settings.chatroom.successiveCacheHits
    } 
  });
}

Drupal.chatroom.pollHandler = function(response, responseStatus) {
  // If the user was kicked or banned, get them out of here.
  if (response.data.accessDenied) {
    window.location = Drupal.settings.basePath + Drupal.settings.chatroom.accessDeniedPath + '/' + Drupal.settings.chatroom.chatId + '/' + response.data.accessDenied;
  }

  // If the chat was archived, reload the page.
  if (response.data.archived) {
    window.location = Drupal.settings.basePath + Drupal.settings.chatroom.chatPath;
  }

  // If we hit the cache, then keep track of that. If the number of
  // successive cache hits gets high enough, we may want to signal to the
  // server that we should skip the cache check so that our online time
  // gets updated.
  if (response.data.cacheHit) {
    Drupal.settings.chatroom.successiveCacheHits++;
  }
  else {
    Drupal.settings.chatroom.successiveCacheHits = 0;
  }

  if (response.data.messages) {
    var newMessage = false;
    for (var i = 0; i < response.data.messages.length; i++) {   
      // Poll requests can pass each other over the wire, so we can't rely on 
      // getting a given message once only, so only add if we haven't already
      // done so.
      if (response.data.messages[i].cmid > Drupal.settings.chatroom.latestMsgId) {
        Drupal.settings.chatroom.latestMsgId = response.data.messages[i].cmid;
        $('#chatroom-board').append(response.data.messages[i].html);
        newMessage = response.data.messages[i];
        if (response.data.messages[i].newDayHtml) {
          $('#chatroom-board').append(response.data.messages[i].newDayHtml);
        }
      }
    }
    if (newMessage) {
      Drupal.chatroom.scrollToLatestMessage();
      if (Drupal.settings.chatroom.hasFocus == false) {
        Drupal.settings.chatroom.newMsg = newMessage;
        clearInterval(Drupal.settings.chatroom.warnInterval);
        Drupal.settings.chatroom.warnInterval = setInterval("Drupal.chatroom.warnNewMsgLoop()", 1500);
      }
    }
  }

  if (response.data.usersHtml) {
    $('#chatroom-user-list-wrapper').replaceWith(response.data.usersHtml);
    Drupal.attachBehaviors('#chatroom-user-list-wrapper');
  }

  if (response.data.commandResponse) {
    Drupal.chatroom.addCommandMessage(response.data.commandResponse);
  }
}

Drupal.chatroom.addCommandMessage = function(response) {
  $('#chatroom-board').append('<div class="new-message command-message">** ' + response.msg + '</div>');
  Drupal.chatroom.scrollToLatestMessage();
}

Drupal.chatroom.scrollToLatestMessage = function() {
  var boardOffset = $('#chatroom-board').offset().top;
  var targetOffset = $('div.new-message:last').offset().top;
  var scrollAmount = targetOffset - boardOffset;
  $('#chatroom-board').animate({scrollTop: '+='+ scrollAmount +'px'}, 500);
  $('.new-message').removeClass('new-message');
}

Drupal.chatroom.postMessage = function(message) {
  $.ajax({
    type: 'POST',
    url: Drupal.settings.basePath + Drupal.settings.chatroom.postMessagePath + '/' + Drupal.settings.chatroom.chatId + '/' + Drupal.settings.chatroom.latestMsgId,
    dataType: 'json',
    success: Drupal.chatroom.pollHandler,
    data: { message: message } 
  })
}

Drupal.chatroom.warnNewMsgLoop = function() {
  if (document.title == Drupal.settings.chatroom.pageTitle) {
    document.title = Drupal.settings.chatroom.newMsg.name + ' says: ' + Drupal.settings.chatroom.newMsg.text;
  }
  else {
    document.title = Drupal.settings.chatroom.pageTitle;
  }
}

// vi:ai:expandtab:sw=2 ts=2 

