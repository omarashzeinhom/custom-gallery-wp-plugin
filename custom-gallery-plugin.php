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

 function register_gallery_post() {
    register_post_type('galleryimage', [
        'public' => true,
        'label' => 'Gallery',
        'menu_icon' => 'dashicons-format-gallery',
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
    ]);
}
add_action('init', 'register_gallery_post');



function activate_custom_gallery_plugin() {
    // Register the Custom Post Type
    register_gallery_post();
    // Add the roles and capabilities
    add_marketing_team_role();
    add_admin_capabilities();
    // Clear the permalinks after the post type has been registered.
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'activate_custom_gallery_plugin');


function deactivate_custom_gallery_plugin()
{
    // UnRegister the Custom Post Type 
    unregister_post_type('galleryimage');
    //unregister_post_type();
    // Clear the permalinks after the post type has been registered.
    flush_rewrite_rules();
}

register_deactivation_hook(__FILE__, 'deactivate_custom_gallery_plugin'); // Fix here




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

    /**
     * // Remove Roles
     * remove_role( string $role )
     * @link:https://developer.wordpress.org/reference/functions/remove_role/
     */
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
 * 1. ADD User Role for Gallery - Photographers or Marketing Team.
 * Checking User Capabilities
 * @link: https://developer.wordpress.org/plugins/security/checking-user-capabilities/
 * 
 * - Roles and Capabilities
 * Default Roles Are
 * 1. Super Admin
 * 2. Administrator
 * 3. Editor
 * 4. Author
 * 5. Contributor
 * 6. Subscriber
 * 
 * add_role( string $role, string $display_name, bool[] $capabilities = array() ): WP_Role|void
 * @link: https://developer.wordpress.org/reference/functions/add_role/
 * @link: https://wordpress.org/documentation/article/roles-and-capabilities/
 * @link https://developer.wordpress.org/plugins/users/roles-and-capabilities/
 * 
 * - All can be managed from the advanced view section of a plugin page
 * @link: https://developer.wordpress.org/plugins/wordpress-org/special-user-roles-capabilities/
 * @link : https://developer.wordpress.org/reference/classes/wp_role/add_cap/
 * 
 * - Unregister User and Role When Uninstalling Plugin
 * remove_role( string $role )
 * @link:https://developer.wordpress.org/reference/functions/remove_role/
 * 
 * Capabilties
 * WP_Role::has_cap( string $cap ): bool
 * @link:  https://developer.wordpress.org/reference/classes/wp_role/has_cap/
 *  class WP_Role {}
 * @link: https://developer.wordpress.org/reference/classes/wp_role/
 *  
 * @link: https://developer.wordpress.org/apis/security/user-roles-and-capabilities/
 *
 *  
 **/

 function add_marketing_team_role() {
  
    add_role(
        'marketing_team',
        'Marketing Team',
   
    );
}
add_action('init', 'add_marketing_team_role');

function add_marketing_team_capabilities() {
    $role = get_role('marketing_team');
    if ($role) {
        $capabilities = [
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

        foreach ($capabilities as $cap) {
            $role->add_cap($cap, true);
        }
    }
}
add_action('init', 'add_marketing_team_capabilities');

function add_admin_capabilities() {
    $role = get_role('administrator');
    if ($role) {
        $capabilities = [
            'edit_galleryimage',
            'read_galleryimage',
            'delete_galleryimage',
            'edit_galleryimages',
            'edit_others_galleryimages',
            'publish_galleryimages',
            'read_private_galleryimages',
            'delete_galleryimages',
            'delete_private_galleryimages',
            'delete_published_galleryimages',
            'delete_others_galleryimages',
            'edit_private_galleryimages',
            'edit_published_galleryimages'
            
        ];

        foreach ($capabilities as $cap) {
            $role->add_cap($cap);
        }
    }
}
add_action('admin_init', 'add_admin_capabilities'); // Admin capabilities on admin init



function custom_gallery_plugin_menu() {
    // Only allow users who can edit gallery images to access this menu
    if (current_user_can('edit_galleryimages')) {
        add_menu_page(
            'Custom Gallery Plugin',
            'Gallery Plugin',
            'edit_galleryimages', // Capability required to access this menu
            'custom-gallery-plugin',
            'custom_gallery_plugin_page',
            'dashicons-format-gallery',
            6
        );
    }
}
add_action('admin_menu', 'custom_gallery_plugin_menu');

function custom_gallery_plugin_page() {
    // Fetch gallery images
    $gallery_images = get_posts([
        'post_type' => 'galleryimage',
        'numberposts' => -1,
        'post_status' => 'publish'
    ]);

    // Output the page content
    echo '<div class="wrap">';
    echo '<h1>Custom Gallery Plugin</h1>';
    echo '<p>Manage your gallery images here. You can upload new images or view and manage existing ones.</p>';

    // Section to display existing gallery images
    echo '<h2>Existing Gallery Images</h2>';
    if (!empty($gallery_images)) {
        echo '<div class="gallery-images" style="display: flex; flex-wrap: wrap; gap: 20px;">';
        foreach ($gallery_images as $image) {
            $image_url = wp_get_attachment_url(get_post_thumbnail_id($image->ID)); // Get the featured image URL
            echo '<div class="gallery-image" style="text-align: center; width: 150px;">';
            if ($image_url) {
                echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($image->post_title) . '" style="max-width: 150px; max-height: 150px;" />';
            }
            echo '<p>' . esc_html($image->post_title) . '</p>';

            // Add edit and view links
            echo '<p>';
            echo '<a href="' . esc_url(get_edit_post_link($image->ID)) . '">Edit</a> | ';
            echo '<a href="' . esc_url(get_permalink($image->ID)) . '" target="_blank">View</a>';
            echo '</p>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<p>No images found. Upload your first image below!</p>';
    }

    // Form to upload new gallery images
    echo '<h2>Upload New Image</h2>';
    echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '" enctype="multipart/form-data">';
    echo '<input type="hidden" name="action" value="custom_gallery_upload">';
    echo '<label for="gallery_image">Select Image:</label>';
    echo '<input type="file" id="gallery_image" name="gallery_image" accept="image/*" required>';
    echo '<br><br>';
    echo '<label for="image_title">Image Title:</label>';
    echo '<input type="text" id="image_title" name="image_title" required>';
    echo '<br><br>';
    echo '<input type="submit" class="button button-primary" value="Upload Image">';
    echo '</form>';

    echo '</div>';
}

// Add the CSS to style the gallery images and form
function custom_gallery_plugin_styles() {
    echo '<style>
        .gallery-images {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .gallery-image {
            text-align: center;
        }
        .gallery-image img {
            border: 1px solid #ccc;
            padding: 5px;
            background: #f9f9f9;
        }
    </style>';
}
add_action('admin_head', 'custom_gallery_plugin_styles');

//Debug User Roles and Capabilities

function check_current_user_capabilities() {
    $current_user = wp_get_current_user();
    echo '<pre>';
    print_r($current_user->roles); // Print user roles
    print_r($current_user->allcaps); // Print user capabilities
    echo '</pre>';
}
add_action('admin_notices', 'check_current_user_capabilities'); // This will display the roles and capabilities in the admin area.



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
