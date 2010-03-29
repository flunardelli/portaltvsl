<?php
/**
 * Theme function for the 'default' filefield formatter.
 */
function theme_portaltvsl_formatter_audio_player($element) {
  $file = $element['#item'];
  $field = content_fields($element['#field_name']);
  $output = theme('portaltvsl_file', $file, $field);
  return $output;
}

/**
 * Theme function for the 'generic' single file formatter.
 */
function theme_portaltvsl_file($file) {
  if (empty($file['fid'])) {
    return '';
  }

  $path = $file['filepath'];
  $url = file_create_url($path);
  $icon = theme('filefield_icon', $file);

  $options = array(
    'attributes' => array(
      'type' => $file['filemime'] . '; length=' . $file['filesize'],
    ),
  );

  if (empty($file['data']['description'])) {
    $link_text = $file['filename'];
  }
  else {
    $link_text = $file['data']['description'];
    $options['attributes']['title'] = $file['filename'];
  }
  $player = '<audio id="player-audio-"'.$file['fid'].' src="'.$url.'" controls></audio>';

  return '<div class="portaltvsl-file clear-block">'. $icon . l($link_text, $url, $options) .'</div><div class="portaltvsl-file clear-block player-audio">'.$player.'</div>';
}
