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
    register_post_type('galleryimage', [
        'public' => true,
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
    add_marketing_team_capabilities();
    // 2.3 
    add_admin_capabilities();
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

// 9.1  add_admin_capabilities // Test this when uninstalled
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


/**  11.  Handle File Uploads
 * 
 * 	wp_check_filetype                   @link:  https://developer.wordpress.org/reference/functions/wp_check_filetype/
 *	wp_handle_upload                    @link:  https://developer.wordpress.org/reference/functions/wp_handle_upload/
 *  wp_insert_attachment                @link: 	https://developer.wordpress.org/reference/functions/wp_insert_attachment/
 *	wp_generate_attachment_metadata     @link:  https://developer.wordpress.org/reference/functions/wp_generate_attachment_metadata/
 *	set_post_thumbnail                  @link: 	https://developer.wordpress.org/reference/functions/set_post_thumbnail/
 */


function handle_custom_gallery_upload() {
    // Check if a file is uploaded
    if (!empty($_FILES['gallery_image']) && !empty($_POST['image_title'])) {
        $file = $_FILES['gallery_image'];
        $title = sanitize_text_field($_POST['image_title']);

        // Verify the file type
        $file_type = wp_check_filetype($file['name']);
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];

        if (in_array($file_type['type'], $allowed_types)) {
            // Upload the file
            $uploaded = wp_handle_upload($file, ['test_form' => false]);

            if (isset($uploaded['file'])) {
                $file_path = $uploaded['file'];
                $file_url = $uploaded['url'];

                // Create a new post in 'galleryimage' post type
                $post_id = wp_insert_post([
                    'post_title' => $title,
                    'post_type' => 'galleryimage',
                    'post_status' => 'publish'
                ]);

                // Attach the uploaded image to the post
                if ($post_id) {
                    $attachment_id = wp_insert_attachment([
                        'guid' => $file_url,
                        'post_mime_type' => $file_type['type'],
                        'post_title' => $title,
                        'post_content' => '',
                        'post_status' => 'inherit'
                    ], $file_path, $post_id);

                    // Generate the metadata for the attachment, and update the database record
                    require_once(ABSPATH . 'wp-admin/includes/image.php');
                    $attach_data = wp_generate_attachment_metadata($attachment_id, $file_path);
                    wp_update_attachment_metadata($attachment_id, $attach_data);

                    // Set the attachment as the post thumbnail
                    set_post_thumbnail($post_id, $attachment_id);
                }
            }
        }
    }
    // Redirect back to the plugin page
    wp_redirect(admin_url('admin.php?page=custom-gallery-plugin'));
    exit;
}
add_action('admin_post_custom_gallery_upload', 'handle_custom_gallery_upload');


/** 12 - Plugins Page and Styles
 * 
 * **/
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

// 12.1 Add the CSS to style the gallery images and form
function custom_gallery_plugin_styles() {
    echo '<style>
        .gallery-images {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .gallery-image {
            text-align: center;
            width: 150px;
            margin-bottom: 20px;
        }
        .gallery-image img {
            border: 1px solid #ccc;
            padding: 5px;
            background: #f9f9f9;
            opacity: 0.7;
            transition: 0.2s ease-in-out;
            box-shadow: 0.1rem 0.1rem 0.1rem 0.1rem gray;
        }
        .gallery-image img:hover {
            border: 1px solid #ccc;
            padding: 5px;
            background: #f9f9f9;
            opacity: 1;
            box-shadow: 0.1rem 0.1rem 0.1rem 0.1rem black;
        }
        .gallery-image p {
            margin: 5px 0;
        }
        .gallery-image a {
            color: #0073aa;
            text-decoration: none;
        }
        .gallery-image a:hover {
            text-decoration: underline;
            
        }
    </style>';
}
add_action('admin_head', 'custom_gallery_plugin_styles');



// 8.0 Debug User Roles and Capabilities

function check_current_user_capabilities() {
    $current_user = wp_get_current_user();
    echo '<pre>';
    print_r($current_user->roles); // Print user roles
    print_r($current_user->allcaps); // Print user capabilities
    echo '</pre>';
}
add_action('admin_notices', 'check_current_user_capabilities'); // This will display the roles and capabilities in the admin area.