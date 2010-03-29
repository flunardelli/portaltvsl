<?php
// $Id: node.node_widget.inc,v 1.1 2010/02/09 12:27:12 antoniodemarco Exp $

/**
 * Implementation of hook_node_widget_form_alter()
 */
function node_node_widget_form_alter(&$form, &$form_state, $form_id) {
  if (isset($form['type']) && isset($form['#node']) && $form['type']['#value'] .'_node_widget_form' == $form_id) {

    module_load_include('inc', 'node', 'node.pages');
    if ($extra = node_invoke($form['#node'], 'form', $form_state)) {
      foreach ($extra as $key => $value) {
        if (!$form_state['#node_widget_settings'][$key]) {
          unset($extra[$key]);
        }
      }
      $form = array_merge_recursive($form, $extra);
    }
    if (!isset($form['title']['#weight'])) {
      $form['title']['#weight'] = -5;
    }

  }
}

/**
 * Implementation of hook_node_widget_widget_settings()
 */
function node_node_widget_widget_settings($type) {
  $settings = array();
  if ($type->has_title) {
    $settings[] = array(
      'name' => 'title',
      'label' => t($type->title_label),
      'supported' => TRUE,
      'message' => NULL,
    );
  }
  if ($type->has_body) {
    $settings[] = array(
      'name' => 'body',
      'label' => t($type->body_label),
      'supported' => FALSE,
      'message' => t('Node body is not supported'),
    );
  }
  return $settings;
}

/**
 * Implementation of hook_node_widget_is_empty()
 */
function node_node_widget_is_empty(&$form, &$form_state) {
  // @todo: make this more accurate considering also other fields
  return empty($form_state['values']['title']);
}
