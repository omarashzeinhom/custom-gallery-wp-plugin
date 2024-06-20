<?php
/**
 * 1. Header Requirements
 */
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


/** 4. Register Gallery Post
 * 
 * */

function register_gallery_post() {
    $args = 
    [
        'public' => true,
        'label' => 'Gallery Image',
        'description'=> 'Manage your galleries here',
        'show_in_rest'=> true, // optional to be exposed in the REST API.
        'rest_base'=> 'gallery-image', // base slug for REST API
        'menu_icon' => 'dashicons-format-gallery',
        'has_archive'=> true,
        'show_in_menu'=> true,
        'supports'=> array('
        title', 'editor', 'author', 'thumbnail', 'excerpt'), // supports
        'capability_type' => 'galleryimage',
        'map_meta_cap' => true, // Ensures custom capabilities are mapped correctly
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
        ];

    register_post_type('galleryimage', $args );
}
add_action('init', 'register_gallery_post');

/** 3. Activation & Deactivation Hooks.
 * 
 * @link : https://developer.wordpress.org/reference/functions/register_activation_hook/
 * register_activation_hook( string $file, callable $callback )
 * 
 *
 */

// 3.1 
function activate_custom_gallery_plugin() {
    // Register the Custom Post Type
    register_gallery_post();
    // Add the roles and capabilities
    // 1.1 
    add_marketing_team_role();
    // 2.2 
    add_all_team_capabilities();
    // Clear the permalinks after the post type has been registered.
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'activate_custom_gallery_plugin');

//  3.2
function deactivate_custom_gallery_plugin()
{
    // UnRegister the Custom Post Type 
    unregister_post_type('galleryimage');
    //1.2 
    remove_role('marketing_team');
    // Clear the permalinks after the post type has been registered.
    flush_rewrite_rules();
}

register_deactivation_hook(__FILE__, 'deactivate_custom_gallery_plugin'); // Fix here

/** 5. Determining Plugin and Content Directories 
 *  @link: https://developer.wordpress.org/plugins/plugin-basics/determining-plugin-and-content-directories/#themes
 */

/** 6. Uninstall Methods  
 *  @link: https://developer.wordpress.org/plugins/plugin-basics/determining-plugin-and-content-directories/#themes
 */
//


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

    /**
     * // Remove Roles
     * remove_role( string $role )
     * @link:https://developer.wordpress.org/reference/functions/remove_role/
     */
    remove_role('marketing_team');
}

register_uninstall_hook(__FILE__, 'custom_gallery_plugin_uninstall');




/** 7. Licenses
 * GNU Licenses 
 * @link: https://www.gnu.org/licenses/license-list.html#OtherLicenses
 * @link: https://opensource.org/licenses
 */




/*  Security - Videos From 8 to 13
 *  ****************************************************************************************
 *         _______. _______   ______  __    __  .______       __  .___________.____    ____ 
 *        /       ||   ____| /      ||  |  |  | |   _  \     |  | |           |\   \  /   / 
 *       |   (----`|  |__   |  ,----'|  |  |  | |  |_)  |    |  | `---|  |----` \   \/   /  
 *       \   \    |   __|  |  |     |  |  |  | |      /     |  |     |  |       \_    _/   
 *   .----)   |   |  |____ |  `----.|  `--'  | |  |\  \----.|  |     |  |         |  |     
 *  |_______/    |_______| \______| \______/  | _| `._____||__|     |__|         |__| 
 * 
 *  ****************************************************************************************
 * Guiding principles
 *
 *  Never trust user input.
 *  Escape as late as possible.
 *  Escape everything from untrusted sources (e.g., databases and users), third-parties (e.g., Twitter), etc.
 *  Never assume anything.
 *  Sanitation is okay, but validation/rejection is better.
 * 
 *  Security    @link: https://developer.wordpress.org/apis/security/
 * 
 * From Video 8 To 13 
 * 8  - User Roles
 * 9  - User Capabilties
 * 10 - Menus
 * 11 - Form Uploads
 * 12 - Styling Plugin
 * 13 - Sanitizing and Validating 
 * 
 * **/




/** 8. Roles
 * 
 * 
 * 1. Super Admin
 * 2. Administrator
 * 3. Editor
 * 4. Author
 * 5. Contributor
 * 6. Subscriber
 * 
 * 
 * add_role                 @link: https://developer.wordpress.org/reference/functions/add_role/
 * roles-and-capabilities   @link: https://wordpress.org/documentation/article/roles-and-capabilities/
 * roles-and-capabilities   @link: https://developer.wordpress.org/plugins/users/roles-and-capabilities/
 * remove_role()            @link:https://developer.wordpress.org/reference/functions/remove_role/
 * 
 *   
 **/

 // 8.1 Roles
 function add_marketing_team_role() {
    add_role(
        'marketing_team',
        'Marketing Team',
        array(
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
        )
    );
}
add_action('init', 'add_marketing_team_role');


/** 9. Capabilties
 * 
 *                               
 *  Checking User Capabilities              @link: https://developer.wordpress.org/plugins/security/checking-user-capabilities/
 *  WP_Role::has_cap( string $cap ): bool   @link:  https://developer.wordpress.org/reference/classes/wp_role/has_cap/
 *  class WP_Role {}                        @link: https://developer.wordpress.org/reference/classes/wp_role/
 *  user-roles-and-capabilities             @link: https://developer.wordpress.org/apis/security/user-roles-and-capabilities/
 *  add_cap                                 @link: https://developer.wordpress.org/reference/classes/wp_role/add_cap/
 *  special-user-roles-capabilities         @link: https://developer.wordpress.org/plugins/wordpress-org/special-user-roles-capabilities/
 *
 */
function add_all_team_capabilities() {
    $roles = ['administrator', 'marketing_team'];

    foreach ($roles as $role_name) {
        $role = get_role($role_name);
        if ($role) {
            $role->add_cap('edit_galleryimage');
            $role->add_cap('read_galleryimage');
            $role->add_cap('delete_galleryimage');
            $role->add_cap('edit_galleryimages');
            $role->add_cap('edit_others_galleryimages');
            $role->add_cap('publish_galleryimages');
            $role->add_cap('read_private_galleryimages');
        }
    }
}
add_action('admin_init', 'add_all_team_capabilities');

/** 10. Menus
 * 
 * - Top Menu
 * @link:  https://developer.wordpress.org/plugins/administration-menus/top-level-menus/
 * - add_menu_page( string $page_title, string $menu_title, string $capability, string $menu_slug, callable $callback = ”, string $icon_url = ”, int|float $position = null ): string
 * @link: https://developer.wordpress.org/reference/functions/add_menu_page/
 * 
 * -    Sub Menu
 * @link: https://developer.wordpress.org/plugins/administration-menus/sub-menus/ 
 * add_submenu_page( string $parent_slug, string $page_title, string $menu_title, string $capability, string $menu_slug, callable $callback = ”, int|float $position = null ): string|false
 * @link: https://developer.wordpress.org/reference/functions/add_submenu_page/
 *
 *   -    PreDefined Sub Menus 
 * @link: https://developer.wordpress.org/plugins/administration-menus/sub-menus/#predefined-sub-menus
 * @link: https://developer.wordpress.org/plugins/settings/
 * @link: https://developer.wordpress.org/plugins/settings/settings-api/
 * 
 * */

 function custom_gallery_plugin_menu() {
    // Only allow users who can edit gallery images to access this menu
    if (current_user_can('edit_galleryimages')) {
        add_menu_page(
            'Add New Gallery Image',
            'Gallery',
            'edit_galleryimages', // Capability required to access this menu
            'custom-gallery-plugin',    
            'custom_gallery_plugin_page', // Custom Page
            'dashicons-format-gallery', // Dash Icon
            6
        );
    }
}
add_action('admin_menu', 'custom_gallery_plugin_menu');
