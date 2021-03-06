<?php
/**
 * Handles all of the admin functions and hooks.
 *
 * @package WordPress
 * @subpackage vanlig
 * @since 1.0
 * @version 2.0
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

  // Needed for Yoast SEO plugin..
  $role_object->add_cap( 'theme_options' );
  $role_object->add_cap( 'manage_options' );

  // Remove certain parts of the Appearance menu
  $user = wp_get_current_user();
  if ( in_array( 'editor', (array) $user->roles ) ) {
    unset($submenu['themes.php'][5]); // Themes
    unset($submenu['themes.php'][6]); // Customize
    remove_menu_page('options-general.php'); // Settings
    remove_menu_page('edit.php?post_type=acf-field-group'); // ACF
  }

}
add_action('admin_menu', 'as_remove_menus');

/**
* Allows the editor role to use Yoast SEO.
*
* @return mixed|void
* @uses yoast_seo
**/
function wpseo_manage_options_capability() {
  $manage_options_cap = 'edit_others_posts';

  return $manage_options_cap;
}
add_filter( 'wpseo_manage_options', 'wpseo_manage_options_capability' );

/**
* Add ACF theme Settings to WP
*/
if( function_exists('acf_add_options_page') ) {

	$option_page = acf_add_options_page(array(
		'page_title' 	=> 'Theme Settings',
		'menu_title' 	=> 'Theme Settings',
		'menu_slug' 	=> 'theme-settings',
		'capability' 	=> 'edit_posts',
		'redirect' 	=> false
	));

  // Load ACF Fields
  require_once( 'acf.php' );

}

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
    :root {
      --palette-primary: #242a39;
    }
    .login h1 a {
        background-image: url('<?php echo get_stylesheet_directory_uri(); ?>/assets/img/halo-logo.png') !important;
        height: 44px !important;
        width: 100% !important;
    }
    .button-primary {
        background: var(--palette-primary) !important;
        border: 1px solid var(--palette-primary) !important;
        text-shadow: none !important;
        box-shadow: none !important;
    }
    input[type=checkbox]:checked:before {
      color: var(--palette-primary) !important;
    }
    .input:focus {
        box-shadow: none !important;
        border-color: var(--palette-primary) !important;
    }
    a:hover {
        color: var(--palette-primary) !important;
    }
  </style>
<?php }
add_action( 'login_enqueue_scripts', 'login_custom_css' );

/**
* Allow SVGs to be uploaded into WP Media
* @return mixed
**/
function svg_media($mimes) {
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
add_filter('upload_mimes', 'svg_media');

/**
* Move Yoast Meta Box to bottom of Post / Page
* @uses wordpress-seo
**/
function yoasttobottom() {
  return 'low';
}
add_filter( 'wpseo_metabox_prio', 'yoasttobottom');

/**
* Toggle the Toolbar Toggle to be showing by deafult
**/
function WP_editor_toggle_toolbar($toolbar) {

    // customize the buttons
    $toolbar['theme_advanced_buttons1'] = 'bold,italic,underline,bullist,numlist,hr,blockquote,link,unlink,justifyleft,justifycenter,justifyright,justifyfull,outdent,indent';
    $toolbar['theme_advanced_buttons2'] = 'formatselect,pastetext,pasteword,charmap,undo,redo';

    // Keep the "kitchen sink" open
    $toolbar[ 'wordpress_adv_hidden' ] = FALSE;
    return $toolbar;
}
add_filter( 'tiny_mce_before_init', 'WP_editor_toggle_toolbar' );

?>
