<?php
// AT commerce

/**
 * Override or insert variables into the html template.
 */
function at_commerce_preprocess_html(&$vars) {
  global $theme_key;

  $theme_name = 'at_commerce';
  $path_to_theme = drupal_get_path('theme', $theme_name);

  // Load the media queries styles
  $media_queries_css = array(
    $theme_name . '.responsive.style.css',
    $theme_name . '.responsive.gpanels.css'
  );
  load_subtheme_media_queries($media_queries_css, $theme_name);

  // Load IE specific stylesheets
  $ie_files = array(
    'IE 6'     => 'ie-6.css',
    'lte IE 7' => 'ie-lte-7.css',
    'IE 8'     => 'ie-8.css',
    'lte IE 9' => 'ie-lte-9.css',
  );
  load_subtheme_ie_styles($ie_files, $theme_name);

  // Add a class for the active color scheme
  if (module_exists('color')) {
    $class = check_plain(get_color_scheme_name($theme_key));
    $vars['classes_array'][] = 'color-scheme-' . drupal_html_class($class);
  }

  // Add class for the active theme
  $vars['classes_array'][] = drupal_html_class($theme_key);

  // Add browser and platform classes
  $vars['classes_array'][] = css_browser_selector();

  // Add theme settings classes
  $settings_array = array(
    'font_size',
    'body_background',
    'header_layout',
    'menu_bullets',
    'image_alignment',
    'site_name_case',
    'site_name_weight',
    'site_name_alignment',
    'site_name_shadow',
    'site_slogan_case',
    'site_slogan_weight',
    'site_slogan_alignment',
    'site_slogan_shadow',
    'page_title_case',
    'page_title_weight',
    'page_title_alignment',
    'page_title_shadow',
    'node_title_case',
    'node_title_weight',
    'node_title_alignment',
    'node_title_shadow',
    'comment_title_case',
    'comment_title_weight',
    'comment_title_alignment',
    'comment_title_shadow',
    'block_title_case',
    'block_title_weight',
    'block_title_alignment',
    'block_title_shadow',
    'corner_radius_form_input_text',
    'corner_radius_form_input_submit',
  );
  foreach ($settings_array as $setting) {
    $vars['classes_array'][] = theme_get_setting($setting);
  }

  // Font family settings
  $fonts = array(
    'bf'  => 'base_font',
    'snf' => 'site_name_font',
    'ssf' => 'site_slogan_font',
	  'mmf' => 'main_menu_font',
    'ptf' => 'page_title_font',
    'ntf' => 'node_title_font',
    'ctf' => 'comment_title_font',
    'btf' => 'block_title_font'
  );
  $families = get_font_families($fonts, $theme_key);
  if (!empty($families)) {
    foreach($families as $family) {
      $vars['classes_array'][] = $family;
    }
  }

  // Add Noggin module settings extra classes, not all designs can support header images
  if (module_exists('noggin')) {
    if (variable_get('noggin:use_header', FALSE)) {
      $va = theme_get_setting('noggin_image_vertical_alignment');
      $ha = theme_get_setting('noggin_image_horizontal_alignment');
      $vars['classes_array'][] = 'ni-a-' . $va . $ha;
      $vars['classes_array'][] = theme_get_setting('noggin_image_repeat');
      $vars['classes_array'][] = theme_get_setting('noggin_image_width');
    }
  }

  // Special case for PIE htc rounded corners, not all themes include this
  if (theme_get_setting('ie_corners') == 1) {
    drupal_add_css($path_to_theme . '/css/ie-htc.css', array(
      'group' => CSS_THEME,
      'browsers' => array(
        'IE' => 'lte IE 8',
        '!IE' => FALSE,
        ),
      'preprocess' => FALSE,
      )
    );
  }

}

/**
 * Override or insert variables into the html template.
 */
function at_commerce_process_html(&$vars) {
  if (module_exists('color')) {
    _color_html_alter($vars);
  }
}

/**
 * Override or insert variables into the page template.
 */
function at_commerce_process_page(&$vars) {
  if (module_exists('color')) {
    _color_page_alter($vars);
  }

  // We some extra classes to support the fancy branding layouts
  $branding_classes = array();
  $branding_classes[] = $vars['linked_site_logo'] ? 'with-logo' : 'no-logo';
  $branding_classes[] = !$vars['hide_site_name'] ? 'with-site-name' : 'site-name-hidden';
  $branding_classes[] = $vars['site_slogan'] ? 'with-site-slogan' : 'no-slogan';
  $vars['branding_classes'] = implode(' ', $branding_classes);
}

/**
 * Override or insert variables into the block template.
 */
function at_commerce_preprocess_block(&$vars) {
  if ($vars['block']->module == 'superfish' || $vars['block']->module == 'nice_menu') {
    $vars['content_attributes_array']['class'][] = 'clearfix';
  }
  if (!$vars['block']->subject) {
    $vars['content_attributes_array']['class'][] = 'no-title';
  }
  if ($vars['block']->region == 'menu_bar' || $vars['block']->region == 'menu_bar_top') {
    $vars['title_attributes_array']['class'][] = 'element-invisible';
  }
}

/**
 * Override or insert variables into the field template.
 */
function at_commerce_preprocess_field(&$vars) {
  $element = $vars['element'];
  $vars['classes_array'][] = 'view-mode-'. $element['#view_mode'];
  $vars['image_caption_teaser'] = FALSE;
  $vars['image_caption_full'] = FALSE;
  if(theme_get_setting('image_caption_teaser') == 1) {
    $vars['image_caption_teaser'] = TRUE;
  }
  if(theme_get_setting('image_caption_full') == 1) {
    $vars['image_caption_full'] = TRUE;
  }
  $vars['field_view_mode'] = '';
  $vars['field_view_mode'] = $element['#view_mode'];
}

/**
 * Implements hook_css_alter().
 */
function at_commerce_css_alter(&$css) {

  // Replace all Commerce module CSS files with our own copies
  // for total control over all styles.

  $path = drupal_get_path('theme', 'at_commerce');

  // cart
  $cart_css = drupal_get_path('module', 'commerce_cart') . '/theme/commerce_cart.css';
  if (isset($css[$cart_css])) {
    $css[$cart_css]['data'] = $path . '/css/commerce/commerce_cart.css';
  }

  // checkout
  $checkout_css = drupal_get_path('module', 'commerce_checkout') . '/theme/commerce_checkout.css';
  if (isset($css[$checkout_css])) {
    $css[$checkout_css]['data'] = $path . '/css/commerce/commerce_checkout.css';
  }
  $checkout_admin_css = drupal_get_path('module', 'commerce_checkout') . '/theme/commerce_checkout_admin.css';
  if (isset($css[$checkout_admin_css])) {
    $css[$checkout_admin_css]['data'] = $path . '/css/commerce/commerce_checkout_admin.css';
  }

  // customer
  $customer_css = drupal_get_path('module', 'commerce_customer') . '/theme/commerce_customer_ui.profile_types.css';
  if (isset($css[$customer_css])) {
    $css[$customer_css]['data'] = $path . '/css/commerce/commerce_customer_ui.profile_types.css';
  }

  // file (contrib)
  $file_css = drupal_get_path('module', 'commerce_file') . '/theme/commerce_file.forms.css';
  if (isset($css[$file_css])) {
    $css[$file_css]['data'] = $path . '/css/commerce/commerce_file.forms.css';
  }

  // line items
  $line_item_summary_css = drupal_get_path('module', 'line_item') . '/theme/commerce_line_item_summary.css';
  if (isset($css[$line_item_summary_css])) {
    $css[$line_item_summary_css]['data'] = $path . '/css/commerce/commerce_line_item_summary.css';
  }
  $line_item_ui_types_css = drupal_get_path('module', 'line_item') . '/theme/commerce_line_item_ui.types.css';
  if (isset($css[$line_item_ui_types_css])) {
    $css[$line_item_ui_types_css]['data'] = $path . '/css/commerce/commerce_line_item_ui.types.css';
  }
  $line_item_views_form_css = drupal_get_path('module', 'line_item') . '/theme/commerce_line_item_views_form.css';
  if (isset($css[$line_item_views_form_css])) {
    $css[$line_item_views_form_css]['data'] = $path . '/css/commerce/commerce_line_item_views_form.css';
  }

  // order
  $order_css = drupal_get_path('module', 'commerce_order') . '/theme/commerce_order.css';
  if (isset($css[$order_css])) {
    $css[$order_css]['data'] = $path . '/css/commerce/commerce_order.css';
  }
  $order_views_css = drupal_get_path('module', 'commerce_order') . '/theme/commerce_order_views.css';
  if (isset($css[$order_views_css])) {
    $css[$order_views_css]['data'] = $path . '/css/commerce/commerce_order_views.css';
  }

  // payment
  $payment_css = drupal_get_path('module', 'commerce_payment') . '/theme/commerce_payment.css';
  if (isset($css[$payment_css])) {
    $css[$payment_css]['data'] = $path . '/css/commerce/commerce_payment.css';
  }

  // price
  $price_css = drupal_get_path('module', 'commerce_price') . '/theme/commerce_price.css';
  if (isset($css[$price_css])) {
    $css[$price_css]['data'] = $path . '/css/commerce/commerce_price.css';
  }

  // product
  $product_css = drupal_get_path('module', 'commerce_product') . '/theme/commerce_product.css';
  if (isset($css[$product_css])) {
    $css[$product_css]['data'] = $path . '/css/commerce/commerce_product.css';
  }
  $product_ui_types_css = drupal_get_path('module', 'commerce_product') . '/theme/commerce_product_ui.types.css';
  if (isset($css[$product_ui_types_css])) {
    $css[$product_ui_types_css]['data'] = $path . '/css/commerce/commerce_product_ui.types.css';
  }
  $product_views_css = drupal_get_path('module', 'commerce_product') . '/theme/commerce_product_views.css';
  if (isset($css[$product_views_css])) {
    $css[$product_views_css]['data'] = $path . '/css/commerce/commerce_product_views.css';
  }

  // tax
  $tax_css = drupal_get_path('module', 'commerce_tax') . '/theme/commerce_tax.css';
  if (isset($css[$tax_css])) {
    $css[$tax_css]['data'] = $path . '/css/commerce/commerce_tax.css';
  }

}



/**
 * Alter the search block form.

function at_commerce_form_alter(&$form, &$form_state, $form_id) {
  if ($form_id == 'search_block_form') {
    $form['search_block_form']['#title'] = t('Search');
    $form['search_block_form']['#title_display'] = 'invisible';
    $form['search_block_form']['#size'] = 25;
    $form['actions']['submit']['#value'] = t('GO');
    $form['search_block_form']['#attributes']['placeholder'] = t('enter search terms');
  }
}
*/
