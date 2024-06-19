<?php
/*
Plugin Name: Custom Gallery Plugin
Description: A WordPress plugin to display a simple custom gallery.
Version: 1.0
Author: Omar Ashraf Zeinhom - ANDGOEDU
*/

/*
 *  ****************************************************************************************
 *  .______        ___           _______. __    ______     _______.
 *  |   _  \      /   \         /       ||  |  /      |   /       |
 *  |  |_)  |    /  ^  \       |   (----`|  | |  ,----'  |   (----`
 *  |   _  <    /  /_\  \       \   \    |  | |  |        \   \    
 *  |  |_)  |  /  _____  \  .----)   |   |  | |  `----.----)   |   
 *  |______/  /__/     \__\ |_______/    |__|  \______|_______/    
 *
 *  ****************************************************************************************
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
        'menu_icon' => 'dashicons-format-gallery',
        'description' => 'Manage your galleries here',
        'show_in_rest' => true, // optional to be exposed in the REST API.
        'rest_base' => 'gallery-image', // base slug for REST API
        'has_archive' => true,
        'show_in_menu' => true,
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt'),
        'capabiltity_type' => 'galleryimage',
        'map_meta_cap' => true,
        'capabilities' => [
            'edit_post' => 'edit_galleryimage',
            'read_post' => 'read_galleryimage',
            'delete_post' => 'delete_galleryimage',
            'edit_posts' => 'edit_galleryimages',
            'edit_others_posts' => 'edit_others_galleryimages',
            'publish_posts' => 'publish_galleryimages',
            'read_private_posts' => 'read_private_galleryimages',
            'delete_posts' => 'delete_galleryimages',
            'delete_private_posts' => 'delete_private_galleryimages',
            'delete_published_posts' => 'delete_published_galleryimages',
            'delete_others_posts' => 'delete_others_galleryimages',
            'edit_private_posts' => 'edit_private_galleryimages',
            'edit_published_posts' => 'edit_published_galleryimages',
            'create_posts' => 'create_galleryimages',
        ]
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

    add_marketing_team_role();
    add_marketing_team_capabilties();
    add_admin_capabilties();
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
    remove_role('marketing_team');
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


function custom_gallery_plugin_uninstall()
{
    $gallery_images = get_posts(array(
        'post_type' => 'galleryimage',
        'numberpost' => -1,
        'post_status' => 'any'
    ));


    foreach ($gallery_images as $gallery_image) {
        wp_delete_post($gallery_image->ID, true);
    }

    remove_role('marketing_team');
}

register_uninstall_hook(__FILE__, 'custom_gallery_plugin_uninstall');




/**
 * GNU Licenses 
 * @link: https://www.gnu.org/licenses/license-list.html#OtherLicenses
 * @link: https://opensource.org/licenses
 */




/*
 *  ****************************************************************************************
 *         _______. _______   ______  __    __  .______       __  .___________.____    ____ 
 *        /       ||   ____| /      ||  |  |  | |   _  \     |  | |           |\   \  /   / 
 *       |   (----`|  |__   |  ,----'|  |  |  | |  |_)  |    |  | `---|  |----` \   \/   /  
 *       \   \    |   __|  |  |     |  |  |  | |      /     |  |     |  |       \_    _/   
 *   .----)   |   |  |____ |  `----.|  `--'  | |  |\  \----.|  |     |  |         |  |     
 *  |_______/    |_______| \______| \______/  | _| `._____||__|     |__|         |__| 
 * 
 *  ****************************************************************************************
 */




/** 
 * Checking User Capabilities
 * @link: https://developer.wordpress.org/plugins/security/checking-user-capabilities/
 * - Roles and Capabilities
 * Default Roles Are
 * 1. Super Admin
 * 2. Administrator
 * 3. Editor
 * 4. Author
 * 5. Contributor
 * 6. Subscriber
 * add_role( string $role, string $display_name, bool[] $capabilities = array() ): WP_Role|void
 * @link: https://developer.wordpress.org/reference/functions/add_role/
 * @link: https://wordpress.org/documentation/article/roles-and-capabilities/
 * @link https://developer.wordpress.org/plugins/users/roles-and-capabilities/
 * - Here are four roles a user can have with regards to plugins. 
 * - All can be managed from the advanced view section of a plugin page
 * @link: https://developer.wordpress.org/plugins/wordpress-org/special-user-roles-capabilities/
 */

/** 
 * 1. Make Sure Edit, Post, and Delete are Only available to admins, & editor.
 */






function add_marketing_team_role()
{
    add_role(
        'marketing_team',
        'Marketing Team',
        array(
            'unfiltered_upload'=> true,
            'read' => true,
            'upload_files' => true,
            'edit_files' => true,
            'edit_galleryimage',
            'read_galleryimage' => true,
            'delete_galleryimage' => true,
            'edit_galleryimages' => true,
            'edit_others_galleryimages' => true,
            'publish_galleryimages' => true,
            'read_private_galleryimages' => true,
            'delete_galleryimages' => true,
            'delete_private_galleryimages' => true,
            'delete_published_galleryimages' => true,
            'delete_others_galleryimages' => true,
            'edit_private_galleryimages' => true,
            'edit_published_galleryimages' => true,
            'create_galleryimages' => true,
        )
    );
}
add_action('init', 'add_marketing_team_role');















function add_marketing_team_capabilties(){
    $role = get_role('marketing_team');

    if($role){
        $capabilities = [ 
            'unfiltered_upload'=> true,
            'read' => true,
            'upload_files'=> true,
            'edit_files' => true,
            'edit_galleryimage'  => true,
            'read_galleryimage'  => true,
            'delete_galleryimage'  => true,
            'edit_galleryimages'  => true,
            'edit_others_galleryimages'  => true,
            'publish_galleryimages'  => true,
            'read_private_galleryimages'  => true,
            'delete_galleryimages'  => true,
            'delete_private_galleryimages'  => true,
            'delete_published_galleryimages'  => true,
            'delete_others_galleryimages'  => true,
            'edit_private_galleryimages'  => true,
            'edit_published_galleryimages'  => true,
            'create_galleryimages' => true,
        ];


        foreach ($capabilities as $cap){
            $role-> add_cap($cap , true);
        }
    }
}
add_action('init', 'add_marketing_team_capabilties');



function add_admin_capabilties(){
    $role = get_role('marketing_team');

    if($role){
        $capabilities = [ 
            'unfiltered_upload'=> true,
            'read' => true,
            'upload_files'=> true,
            'edit_files' => true,
            'edit_galleryimage'  => true,
            'read_galleryimage'  => true,
            'delete_galleryimage'  => true,
            'edit_galleryimages'  => true,
            'edit_others_galleryimages'  => true,
            'publish_galleryimages'  => true,
            'read_private_galleryimages'  => true,
            'delete_galleryimages'  => true,
            'delete_private_galleryimages'  => true,
            'delete_published_galleryimages'  => true,
            'delete_others_galleryimages'  => true,
            'edit_private_galleryimages'  => true,
            'edit_published_galleryimages'  => true,
            'create_galleryimages' => true,
        ];


        foreach ($capabilities as $cap){
            $role-> add_cap($cap , true);
        }
    }
}
add_action('admin_init', 'add_admin_capabilties');


























/** Plugins Menus
 * @link:  https://developer.wordpress.org/plugins/administration-menus/top-level-menus/
 * Top Menu
 * add_menu_page( string $page_title, string $menu_title, string $capability, string $menu_slug, callable $callback = ”, string $icon_url = ”, int|float $position = null ): string
 * @link: https://developer.wordpress.org/reference/functions/add_menu_page/
 * Sub Menu
 * @link: https://developer.wordpress.org/plugins/administration-menus/sub-menus/ 
 * add_submenu_page( string $parent_slug, string $page_title, string $menu_title, string $capability, string $menu_slug, callable $callback = ”, int|float $position = null ): string|false
 * @link: https://developer.wordpress.org/reference/functions/add_submenu_page/
 *  PreDefined Sub Menus 
 * @link: https://developer.wordpress.org/plugins/administration-menus/sub-menus/#predefined-sub-menus
 * @link: https://developer.wordpress.org/plugins/settings/
 * @link: https://developer.wordpress.org/plugins/settings/settings-api/
 * */


function check_current_user_capabilties()
{
    $current_user = wp_get_current_user();
    echo '<pre>';
    print_r($current_user->roles);
    print_r($current_user->allcaps);
    echo '</pre>';
}
add_action('admin_notices', 'check_current_user_capabilties');
