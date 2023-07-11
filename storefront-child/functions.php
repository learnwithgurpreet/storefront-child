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

function storefront_child_check_for_updates() {
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

      if($new_version !== $theme_version) {
        // Display update notification in the admin dashboard
        add_action('admin_notices', function() use ($download_link, $new_version) {
          $update_message = sprintf(
            'A new version (%s) of the theme is available. <a href="%s">Click here</a> to update.',
            $new_version,
            $download_link
          );
            echo '<div class="notice notice-success is-dismissible"><p>' . $update_message . '</p></div>';
          });
        }
      }
    }
}
add_action('admin_init', 'storefront_child_check_for_updates');

?>
