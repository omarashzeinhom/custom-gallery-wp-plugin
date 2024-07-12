<?php
/**
 * 1. Header Requirements
 */
/*
Plugin Name: Mini Gallery
Description: A WordPress plugin to display a simple custom gallery.
Version: 1.0
Author: Omar Ashraf Zeinhom AbdElRahman | ANDGOEDU
License: GPLv2 
*/
if ( ! defined( 'ABSPATH' ) ) exit;
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

function mg_post() {
    $args = 
    [
        'public' => true,
        'label' => 'Gallery Image',
        'description'=> 'Manage your galleries here',
        'show_in_rest'=> true, // optional to be exposed in the REST API.
        'rest_base'=> 'gallery-image', // base slug for REST API
        'menu_icon' => 'dashicons-format-gallery',
        'has_archive'=> true,
        'show_in_menu'=> false,
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
add_action('init', 'mg_post');

/** 3. Activation & Deactivation Hooks.
 * 
 * @link : https://developer.wordpress.org/reference/functions/register_activation_hook/
 * register_activation_hook( string $file, callable $callback )
 * 
 *
 */

// 3.1 
function mg_plugin_act() {
    // Register the Custom Post Type
    mg_post();
    // Add the roles and capabilities
    // 1.1 
    marketing_team_role();
    // 2.2 
    mg_capabilities();
    // Clear the permalinks after the post type has been registered.
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'mg_plugin_act');

//  3.2
function mg_plugin_deact()
{
    // UnRegister the Custom Post Type 
    unregister_post_type('galleryimage');
    //1.2 
    remove_role('marketing_team');
    // Clear the permalinks after the post type has been registered.
    flush_rewrite_rules();
}

register_deactivation_hook(__FILE__, 'mg_plugin_deact');

/** 5. Determining Plugin and Content Directories 
 *  @link: https://developer.wordpress.org/plugins/plugin-basics/determining-plugin-and-content-directories/#themes
 */

/** 6. Uninstall Methods  
 *  @link: https://developer.wordpress.org/plugins/plugin-basics/determining-plugin-and-content-directories/#themes
 */
//


function mg_plugin_uninstall()
{
    $gallery_images = get_posts(array(
        'post_type' => 'galleryimage',
        'numberpost' => -1,
        'post_status' => 'any'
    ));


    foreach ($gallery_images as $gallery_image) {
        wp_delete_post(intval($gallery_image->ID), true);
    }

    /**
     * // Remove Roles
     * remove_role( string $role )
     * @link:https://developer.wordpress.org/reference/functions/remove_role/
     */
    remove_role('marketing_team');
}

register_uninstall_hook(__FILE__, 'mg_plugin_uninstall');




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
 function marketing_team_role() {
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
add_action('init', 'marketing_team_role');


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
function mg_capabilities() {
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
add_action('admin_init', 'mg_capabilities');


/** 10. Validation & Menus
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

function mg_menu() {
    // Only allow users who can edit gallery images to access this menu
    if (current_user_can('edit_galleryimages')) {
        add_menu_page(
            'Add New Gallery Image',
            'Gallery',
            'edit_galleryimages', // Capability required to access this menu
            'mini-gallery',    
            'mg_plugin_page', // Custom Page
            'dashicons-format-gallery', // Dash Icon
            6
        );
    }
}
add_action('admin_menu', 'mg_menu');


/**  11.  Handle File Uploads
 * 
 * 	wp_check_filetype                   @link:  https://developer.wordpress.org/reference/functions/wp_check_filetype/
 *	wp_handle_upload                    @link:  https://developer.wordpress.org/reference/functions/wp_handle_upload/
 *  wp_insert_attachment                @link: 	https://developer.wordpress.org/reference/functions/wp_insert_attachment/
 *	wp_generate_attachment_metadata     @link:  https://developer.wordpress.org/reference/functions/wp_generate_attachment_metadata/
 *	set_post_thumbnail                  @link: 	https://developer.wordpress.org/reference/functions/set_post_thumbnail/
 */


 function mg_upload() {
    // Verify nonce
    if ( !isset( $_POST['mg_upload_nonce'] ) || !wp_verify_nonce( $_POST['mg_upload_nonce'], 'mg_upload_nonce' ) ) {
      wp_die( 'Security check' ); 
  }
  // Check if files are uploaded and a title is provided
  if (!empty($_FILES['gallery_images']) && !empty($_POST['image_title'])) {
      $files = $_FILES['gallery_images'];
      $title = sanitize_text_field($_POST['image_title']);
      
      // Create a new post in 'galleryimage' post type
      $post_id = wp_insert_post([
          'post_title' => $title,
          'post_type' => 'galleryimage',
          'post_status' => 'publish'
      ]);

      if ($post_id) {
          // Loop through each uploaded file
          foreach ($files['name'] as $key => $value) {
              if ($files['name'][$key]) {
                  // Process each file
                  $file = [
                      'name'     => $files['name'][$key],
                      'type'     => $files['type'][$key],
                      'tmp_name' => $files['tmp_name'][$key],
                      'error'    => $files['error'][$key],
                      'size'     => $files['size'][$key]
                  ];

                  // Verify the file type
                  $file_type = wp_check_filetype($file['name']);
                  $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];

                  if (in_array($file_type['type'], $allowed_types)) {
                      // Upload the file
                      $uploaded = wp_handle_upload($file, ['test_form' => false]);

                      if (isset($uploaded['file'])) {
                          $file_path = $uploaded['file'];
                          $file_url = $uploaded['url'];

                          // Attach the uploaded image to the post
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
                      }
                  }
              }
          }
      }
  }
  // Redirect back to the plugin page
  wp_redirect(admin_url('admin.php?page=mini-gallery'));
  exit;
}
add_action('admin_post_mg_upload', 'mg_upload');



/** 12 - Plugins Page and Styles
 * 
 * **/
// Display the admin page for managing the gallery
function mg_plugin_page() {
    echo '<h1>' . esc_html__('Mini Gallery', 'mini-gallery') . '</h1>';
    
    // Form to upload new gallery images
    echo '<h2>' . esc_html__('Upload New Images', 'mini-gallery') . '</h2>';
    echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '" enctype="multipart/form-data">';
    
    // Add nonce field
    wp_nonce_field('mg_upload_nonce', 'mg_upload_nonce');

    echo '<input type="hidden" name="action" value="mg_upload">';
    echo '<label for="gallery_images">' . esc_html__('Select Images:', 'mini-gallery') . '</label>';
    echo '<input type="file" id="gallery_images" name="gallery_images[]" accept="image/*" required multiple>';
    echo '<br><br>';
    echo '<label for="image_title">' . esc_html__('Gallery Title:', 'mini-gallery') . '</label>';
    echo '<input type="text" id="image_title" name="image_title" required>';
    echo '<br><br>';
    echo '<input type="submit" class="button button-primary" value="' . esc_attr__('Upload Images', 'mini-gallery') . '">';
    echo '</form>';

    // Display existing galleries with their IDs and shortcodes
    echo '<h2>' . esc_html__('Existing Galleries', 'mini-gallery') . '</h2>';
    $galleries = get_posts(['post_type' => 'galleryimage', 'numberposts' => -1]);

    if ($galleries) {
        foreach ($galleries as $gallery) {
            // Ensure each property of $gallery is escaped
            $gallery_title = esc_html($gallery->post_title);
            $gallery_content = esc_html($gallery->post_content);
            $gallery_id = intval($gallery->ID);

            echo '<div>';
            echo '<h3>' . esc_html($gallery_title) . ' (ID: ' . esc_html($gallery_id) . ')</h3>';
            echo '<p>' . esc_html($gallery_content) . '</p>';

            // Display the shortcode dynamically with the post ID
            echo '<p>' . esc_html__('Shortcode to display this gallery:', 'mini-gallery') . '</p>';
            echo '<pre>[gallery_carousel id="' . esc_attr($gallery_id) . '"]</pre>';

            // Optionally, display the carousel preview using the shortcode
            echo do_shortcode('[gallery_carousel id="' . esc_attr($gallery_id) . '"]');
            echo '</div>';
            echo '<hr>';
        }
    } else {
        echo '<p>' . esc_html__('No galleries found.', 'mini-gallery') . '</p>';
    }
}

function mg_styles() {
    wp_register_style('main', 'public/css/carousel_minigallery.css');
    wp_enqueue_style('main', 'public/css/carousel_minigallery.css');
}
add_action('admin_head', 'mg_styles');

function mg_js() {
    wp_register_script('main', 'public/css/main_minigallery.js');
    wp_enqueue_script('main', 'public/css/carousel_minigallery.js');
}
add_action('admin_head', 'mg_js');


function mg_carousel_shortcode($atts) {
    $atts = shortcode_atts(['id' => ''], $atts, 'gallery_carousel');
    $post_id = intval($atts['id']);

    if (!$post_id) {
        return '<p>Gallery not found.</p>';
    }

    $attachments = get_posts([
        'post_type' => 'attachment',
        'posts_per_page' => -1,
        'post_parent' => $post_id,
        'post_mime_type' => 'image',
        'order' => 'ASC',
        'orderby' => 'menu_order ID'
    ]);

    if (empty($attachments)) {
        return '<p>No images found in this gallery.</p>';
    }

    // Output the carousel HTML
    $output = '<div class="gallery-carousel" id="gallery-carousel-' . $post_id . '">';
    foreach ($attachments as $attachment) {
        $img_url = wp_get_attachment_image_url($attachment->ID, 'large');
        $output .= '<div class="carousel-slide">';
        $output .= '<img src="' . esc_url($img_url) . '" alt="' . esc_attr($attachment->post_title) . '">';
        $output .= '</div>';
    }
    $output .= '</div>';
    return $output;
}
add_shortcode('gallery_carousel', 'mg_carousel_shortcode');
