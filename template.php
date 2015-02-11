<?php

/**
 * @file
 * Overrides for the temple theme.
 */

/**
 * Override of theme('breadcrumb').
 */
function temple_breadcrumb($vars) {
  $output = '<div class="breadcrumb">';

  // Add current page onto the end.
  if (!drupal_is_front_page()) {
    $item = menu_get_item();
    $end = end($vars['breadcrumb']);
    if ($end && strip_tags($end) !== $item['title']) {
      $vars['breadcrumb'][] = "<strong>" . check_plain($item['title']) . "</strong>";
    }
  }

  // Optional: Add the site name to the front of the stack.
  if (!empty($vars['prepend'])) {
    $site_name = empty($vars['breadcrumb']) ? "<strong>" . check_plain(variable_get('site_name', '')) . "</strong>" : l(variable_get('site_name', ''), '<front>', array('purl' => array('disabled' => TRUE)));
    array_unshift($vars['breadcrumb'], $site_name);
  }

  $depth = 0;
  $count = count($vars['breadcrumb']);
  foreach ($vars['breadcrumb'] as $link) {
    $output .= "<span class='breadcrumb-link breadcrumb-depth-{$depth}'>{$link}</span>";
    $output .= ($depth < ($count - 1)) ? '<span class="delimiter">»</span>': '';
    $depth++;
  }
  return $output . '</div>';
}

/**
 * Override of theme('status_messages').
 */
function temple_status_messages($vars) {
  $output = '';
  if (!isset($vars['messages'])) {
    $display = $vars['display'];
    $vars['messages'] = drupal_get_messages($display);
  }
  $status_heading = array(
    'status' => t('Status message'),
    'error' => t('Error message'),
    'warning' => t('Warning message'),
  );

  if (count($vars['messages']) === 0) {
    return '';
  }

  foreach ($vars['messages'] as $type => $messages) {
    $output .= "<div class=\"messages $type\">\n";
    if (!empty($status_heading[$type])) {
      $output .= '<h2 class="element-invisible">' . $status_heading[$type] . "</h2>\n";
    }
    if (count($messages) > 1) {
      $output .= " <ul>\n";
      foreach ($messages as $message) {
        $output .= '  <li>' . $message . "</li>\n";
      }
      $output .= " </ul>\n";
    }
    else {
      $output .= $messages[0];
    }
    $output .= "</div>\n";
  }
  return $output;
}

/**
 * Override or insert variables into the block templates.
 */
function temple_preprocess_block(&$vars) {
  // Classes describing the position of the block within the region.
  if ($vars['block_id'] == 1) {
    $vars['classes_array'][] = 'first';
    $vars['attributes_array']['class'][] = 'first';
  }
  // The last_in_region property is set in temple_page_alter().
  if (isset($vars['block']->last_in_region)) {
    $vars['classes_array'][] = 'last';
    $vars['attributes_array']['class'][] = 'last';
  }
}

/**
 * Implements hook_page_alter().
 */
function temple_page_alter(&$page) {
  // Look in each visible region for blocks.
  foreach (system_region_list($GLOBALS['theme'], REGIONS_VISIBLE) as $region => $name) {
    if (!empty($page[$region])) {
      // Find the last block in the region.
      $blocks = array_reverse(element_children($page[$region]));
      while ($blocks && !isset($page[$region][$blocks[0]]['#block'])) {
        array_shift($blocks);
      }
      if ($blocks) {
        $page[$region][$blocks[0]]['#block']->last_in_region = TRUE;
      }
    }
  }
}

/**
 * Implements template_preprocess_region().
 */
function temple_preprocess_region(&$vars) {
  $vars['attributes_array']['role'] = 'brad';
  if ($vars['region'] === 'pre_content') {
    $vars['attributes_array']['style'] = _temple_hero_image();
  }
  $vars['attributes'] = drupal_attributes($vars['attributes_array']);
}

/**
 * Implements template_preprocess_entity().
 */
function temple_preprocess_entity(&$vars) {
  $vars['classes_array'][] = drupal_html_class('view-mode-' . $vars['view_mode']);
  
  // Make blockquote paragraphs into blockquotes.
  if ($vars['entity_type'] === 'paragraphs_item') {
    $vars['tag'] = 'div';
    if ($vars['elements']['#bundle'] === 'blockquote') {
      $vars['tag'] = 'blockquote';
    }
  }
}

/**
 * Implements template_preprocess_node().
 */
function temple_preprocess_node(&$vars) {
  $vars['classes_array'][] = drupal_html_class('view-mode-' . $vars['view_mode']);
  unset($vars['content']['links']);
}

/**
 * Override the default button theming to actually use buttons.
 */
function temple_button($vars) {
  $element = $vars['element'];
  $element['#attributes']['type'] = 'submit';
  element_set_attributes($element, array('id', 'name', 'value'));

  $element['#attributes']['class'][] = 'form-' . $element['#button_type'];
  if (!empty($element['#attributes']['disabled'])) {
    $element['#attributes']['class'][] = 'form-button-disabled';
  }

  return '<button' . drupal_attributes($element['#attributes']) . '>' . $element['#value'] . '</button>';
}

/**
 * Override of theme_image().
 */
function temple_image($vars) {
  $attributes = $vars['attributes'];
  $attributes['src'] = file_create_url($vars['path']);

  foreach (array('alt', 'title') as $key) {
    if (isset($vars[$key])) {
      $attributes[$key] = $vars[$key];
    }
  }
  
  // Just in case there isn't an alt attribute by this point.
  if (!isset($attributes['alt'])) {
    $attributes['alt'] = '';
  }
  
  return '<img' . drupal_attributes($attributes) . ' />';
}

/**
 * Helper to return a hero image.
 */
function _temple_hero_image() {
  $path = 'public://stock_laptop.jpg';
  
  // Find hero images on nodes or attached to views.
  $page = menu_get_item(current_path());
  if ($page['page_callback'] === 'views_page') {
    $compro_hero = variable_get('compro_hero', '');
    $args = $page['page_arguments'];
    if (isset($compro_hero[$args[0]][$args[1]])) {
      $hero = entity_load('hero', array($compro_hero[$args[0]][$args[1]]));
      $hero = array_pop($hero);
      if (isset($hero->field_hero_image[LANGUAGE_NONE][0]['uri'])) {
        $path = $hero->field_hero_image[LANGUAGE_NONE][0]['uri'];
      }
    }
  }
  else if ($page['page_callback'] === 'node_page_view') {
    $node = $page['page_arguments'][0];
    if (isset($node->field_hero_image[LANGUAGE_NONE][0]['uri'])) {
      $path = $node->field_hero_image[LANGUAGE_NONE][0]['uri'];
    }
  }
  
  return 'background-image: url(' . image_style_url('1920_slide', $path) . ');';
}
