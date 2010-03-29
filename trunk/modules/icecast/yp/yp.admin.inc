<?php
// $Id: yp.admin.inc,v 1.1.2.1 2008/03/01 04:12:02 mfb Exp $

/**
 * Form builder function.
 */
function yp_admin_settings() {
  $form['yp_debug'] = array(
    '#type' => 'radios',
    '#title' => t('CGI debug logging'),
    '#default_value' => variable_get('yp_debug', 0),
    '#options' => array(t('Disabled'), t('Enabled')),
    '#description' => t('Log additional debugging information to watchdog.'),
  );
  return system_settings_form($form);
}
