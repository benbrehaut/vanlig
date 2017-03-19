<?php
/**
 * Handles all of the admin functions and hooks.
 *
 * @package WordPress
 * @subpackage halos
 * @since 1.0
 * @version 1.0
 */

/**
* Disable WP file editing
**/
define( 'DISALLOW_FILE_EDIT', true );

/**
* Remove Customize Link on the WP Admin Menu
**/
function remove_customize_link() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('customize');
}
add_action( 'wp_before_admin_bar_render', 'remove_customize_link' );

/**
* Give editor roles to menus and hide certain parts.
**/
function as_remove_menus () {
  global $submenu;

  // Allow Editors to edit the menu
  $role_object = get_role( 'editor' );
  $role_object->add_cap( 'edit_theme_options' );

  // Remove certain parts of the Appearance menu
  $user = wp_get_current_user();
  if ( in_array( 'editor', (array) $user->roles ) ) {
    unset($submenu['themes.php'][5]); // Themes
    unset($submenu['themes.php'][6]); // Customize
  }

}
add_action('admin_menu', 'as_remove_menus');

/**
* Change link of logo to site url.
* @return url
**/
function login_img_url() {
  return site_url();
}
add_filter( 'login_headerurl', 'login_img_url' );

/**
* Change logo alt text to site name
* @return string
**/
function login_title() {
  return get_option( 'blogname' );
}
add_filter( 'login_headertitle', 'login_title' );

/**
* Updating login error message
* So it does not hint that the username is correct.
* @return string
**/
function login_fail_message() {
	return '<strong>ERROR</strong>: The Username or Password that you have entered is incorrect.';
}
add_filter( 'login_errors', 'login_fail_message' );

/**
* Custom CSS for Login Page
**/
function login_custom_css() { ?>
    <style type="text/css">
        .login h1 a {
            background-image: url('<?php echo get_stylesheet_directory_uri(); ?>/assets/img/halo-logo.png') !important;
            height: 44px !important;
            width: 100% !important;
        }
        .button-primary {
            background: #1e1e1e !important;
            border: 1px solid #1e1e1e !important;
            text-shadow: none !important;
            box-shadow: none !important;
        }
        input[type=checkbox]:checked:before {
          color: #131313 !important;
        }
        .input:focus {
            box-shadow: none !important;
            border-color: #1e1e1e !important;
        }
        a:hover {
            color: #1e1e1e !important;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'login_custom_css' );

?>