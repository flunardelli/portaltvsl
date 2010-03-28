<?php
// $Id: yplog.theme.inc,v 1.1.2.1 2008/07/04 04:57:31 mfb Exp $

function template_preprocess_yplog_graph(&$variables) {
  $variables['image'] = theme('image', $variables['filename']);
  $variables['listen_link'] = l($variables['listen_url'] .'.m3u', $variables['listen_url'] .'.m3u');
}
