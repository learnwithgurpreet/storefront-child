<?php
function storefront_child_enqueue_styles() {
  wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'storefront_child_enqueue_styles' );

if ( ! function_exists( 'storefront_credit' ) ) {
	/**
	 * Display the theme credit
	 *
	 * @since  1.0.0
	 * @return void
	 */
	/* Display custom WooCommerce shop credit message */
	function storefront_credit() {
       ?>
       <div class="site-info has-text-align-center">
		   <?php echo esc_html( apply_filters( 'storefront_copyright_text', $content = '&copy; '. get_bloginfo( 'name' ) .' ' . date( 'Y' ) ) ); ?>
       </div><!-- .site-info -->
     <?php 
	}
}

if ( is_admin() ) {
  add_filter('pre_set_site_transient_update_themes', 'storefront_child_check_for_updates');
}

function storefront_child_check_for_updates($transient) {
  if ( empty( $transient->checked ) ) {
    return $transient;
  }

  $theme = wp_get_theme();
  $theme_name = $theme->get('Name');
  $theme_version = $theme->get('Version');
  $theme_text_domain = $theme->get('TextDomain');
  $update_url = 'https://learnwithgurpreet.github.io/'.$theme_text_domain.'/update.json'; // Replace with your custom update API URL

  // Make the request to the update API
  $request = wp_remote_get($update_url, array(
    'timeout' => 10,
    'headers' => array(
      'Accept' => 'application/json',
    )
  ));

  if (!is_wp_error($request) && wp_remote_retrieve_response_code($request) === 200) {
    $response = json_decode(wp_remote_retrieve_body($request), true);

    // Check if an update is available
    if (isset($response['download_link']) && $response['download_link'] && isset($response['new_version']) && $response['new_version']) {
      $download_link = $response['download_link'];
      $new_version = $response['new_version'];

      if ( version_compare( $theme_version, $new_version, '<' ) ) {
        if ( version_compare( $theme_version, $new_version, '<' ) ) {
          $transient->response[$theme_text_domain] = array(
            'url' => $download_link,
            'new_version' => $new_version,
            'slug' => $theme_text_domain,
            'package' => $download_link
          );
        }
      }
    }
  }

  return $transient;
}

add_filter('auto_update_theme', 'enable_child_theme_auto_updates', 10, 2);
function enable_child_theme_auto_updates($update, $item) {
  if (isset($item->theme) && $item->theme === 'storefront-child') {
    return true; // Enable auto-updates for the child theme
  }
  return $update;
}

?>
