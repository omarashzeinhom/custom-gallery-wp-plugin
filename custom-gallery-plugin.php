<?php
/*
Plugin Name: Custom Gallery Plugin
Description: A WordPress plugin to display a simple custom gallery.
Version: 1.0
Author: Omar Ashraf Zeinhom - ANDGOEDU
*/




/**
 * Activation & Deactivation Hooks.
 * @link : https://developer.wordpress.org/reference/functions/register_activation_hook/
 * register_activation_hook( string $file, callable $callback )
 * 
 *
 */

function register_gallery_post()
{
    register_post_type('galleryimage', [
        'public' => true,
        'label' => 'Gallery',
        'menu_icon' => 'dashicons-format-gallery'
    ]);
}
/**
 * do_action( ‘init’ )
 * @link: https://developer.wordpress.org/reference/hooks/init/
 */
add_action('init', 'register_gallery_post');



function activate_custom_gallery_plugin()
{
    // Register the Custom Post Type 
    register_gallery_post();
    // Clear the permalinks after the post type has been registered.
    flush_rewrite_rules();
}
/** 
 * 
* register_activation_hook( string $file, callable $callback )
* @link: https://developer.wordpress.org/reference/functions/register_activation_hook/ 
*/
register_activation_hook(__FILE__, 'activate_custom_gallery_plugin');



function deactivate_custom_gallery_plugin()
{
    // UnRegister the Custom Post Type 
    unregister_post_type('galleryimage');
    //unregister_post_type();
    // Clear the permalinks after the post type has been registered.
    flush_rewrite_rules();
}

register_deactivation_hook(__FILE__, 'activate_custom_gallery_plugin');




/** Determining Plugin and Content Directories 
*  @link: https://developer.wordpress.org/plugins/plugin-basics/determining-plugin-and-content-directories/#themes
*/


// DEBUG CODE
// var_dump(wp_upload_dir());
// This Can Be Removed or tested with any of the above 


function custom_gallery_plugin_uninstall(){
    $gallery_images = get_posts(array(
        'post_type' => 'galleryimage',
        'numberpost' => -1,
        'post_status' => 'any'
    ));
    

    foreach($gallery_images as $gallery_image){
        wp_delete_post($gallery_image-> ID, true);
    }
}

register_uninstall_hook(__FILE__,'custom_gallery_plugin_uninstall');



/** TODO
* Checking User Capabilities
* @link: https://developer.wordpress.org/plugins/security/checking-user-capabilities/
*/

/** 
* 1. Make Sure Edit, Post, and Delete are Only available to admins, & editor.
*/