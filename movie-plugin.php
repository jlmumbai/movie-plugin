<?php 

/*
 * Plugin Name:       Movie Plugin
 * Plugin URI:        
 * Description:       Handle the basic post type movie with this plugin
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Jaya Lakshmi
 * Author URI:        https://author.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       movie-plugin
 * Domain Path:       /languages
 */


 if ( ! defined('ABSPATH')){
    die;
 }

 class MoviePlugin{

    function __construct(){
        add_action ('init',array($this,'custom_post_type'));
        add_action( 'add_meta_boxes',array($this,'movie_add_meta_box' ));
        add_action( 'save_post', array($this,'movie_save_meta_box_data' ));
        require_once plugin_dir_path(__FILE__) . 'add_widget.php';
    }
    function activate(){
        
        $this->custom_post_type();
        
        
        //$this->add_meta_callbacks();
        flush_rewrite_rules();
        
    }

    function deactivate(){
        flush_rewrite_rules();
    }

    function custom_post_type(){

        

        $labels = array(
            'name'                  => _x( 'Movies', 'Post type general name', 'textdomain' ),
            'singular_name'         => _x( 'Movie', 'Post type singular name', 'textdomain' ),
            'menu_name'             => _x( 'Movies', 'Admin Menu text', 'textdomain' ),
            'name_admin_bar'        => _x( 'Movie', 'Add New on Toolbar', 'textdomain' ),
            'add_new'               => __( 'Add New', 'textdomain' ),
            'add_new_item'          => __( 'Add New Movie', 'textdomain' ),
            'new_item'              => __( 'New Movie', 'textdomain' ),
            'edit_item'             => __( 'Edit Movie', 'textdomain' ),
            'view_item'             => __( 'View Movie', 'textdomain' ),
            'all_items'             => __( 'All Movies', 'textdomain' ),
            'search_items'          => __( 'Search Movies', 'textdomain' ),
            'parent_item_colon'     => __( 'Parent Movies:', 'textdomain' ),
            'not_found'             => __( 'No Movies found.', 'textdomain' ),
            'not_found_in_trash'    => __( 'No Movies found in Trash.', 'textdomain' ),
            'featured_image'        => _x( 'Movie Cover Image', 'Overrides the “Featured Image” phrase for this post type.', 'textdomain' ),
            'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type.', 'textdomain' ),
            'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type.', 'textdomain' ),
            'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type.', 'textdomain' ),
            'archives'              => _x( 'Movie archives', 'The post type archive label used in nav menus. Default “Post Archives”.', 'textdomain' ),
            'insert_into_item'      => _x( 'Insert into Movie', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post).', 'textdomain' ),
            'uploaded_to_this_item' => _x( 'Uploaded to this Movie', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post).', 'textdomain' ),
            'filter_items_list'     => _x( 'Filter Movies list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”.', 'textdomain' ),
            'items_list_navigation' => _x( 'Movies list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”.', 'textdomain' ),
            'items_list'            => _x( 'Movies list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”.', 'textdomain' ),
        );
    
        $args = array(
            'labels'             => $labels,
            'description'        => 'This is custom post type for movies list',
            'menu_icon'          => 'dashicons-tickets-alt',
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'movie' ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 20,
            'query_var'             => true,
            'can_export'            => true,
            'supports'           => array( 'title', 'thumbnail', 'excerpt'),
        );

        register_post_type('Movies',$args);
    }

    function movie_add_meta_box() {
        //this will add the metabox for the movie post type
        $screens = array( 'movies' );
        foreach ( $screens as $screen ) {
            add_meta_box(
                'movie_id',
                __( 'Movie Details', 'textdomain' ),
                array($this,'movie_meta_box_callback'),
                $screen
            );
         }
    }
        
    /**
     * Prints the box content.
     *
     * @param WP_Post $post The object for the current post/page.
     */
    function movie_meta_box_callback( $post ) {
    
    // Add a nonce field so we can check for it later.
    wp_nonce_field('movie_save_meta_box_data', 'movie_meta_box_nonce' );
    
    /*
        * Use get_post_meta() to retrieve an existing value
        * from the database and use the value for the form.
        */
    $value_d = get_post_meta( $post->ID, 'movie_dnew_field', true );
    $value_a = get_post_meta( $post->ID, 'movie_anew_field', true );
    
    echo '<label for="movie_dnew_field">';
    _e( 'Director name', 'textdomain' );
    echo '</label> ';
    echo '<input type="text" id="movie_dnew_field" name="movie_dnew_field" value="' . esc_attr( $value_d ) . '" size="25" /><br/><br/>';
    echo '<label for="movie_anew_field">';
    _e( 'Actor name', 'textdomain' );
    echo '</label> ';
    echo '<input type="text" id="movie_anew_field" name="movie_anew_field" value="' . esc_attr( $value_a ) . '" size="25" />';
    }
    
    /**
     * When the post is saved, saves our custom data.
     *
     * @param int $post_id The ID of the post being saved.
     */
    function movie_save_meta_box_data( $post_id ) {
        if ( ! isset( $_POST['movie_meta_box_nonce'] ) ) {
        return;
        }

        if ( ! wp_verify_nonce( $_POST['movie_meta_box_nonce'], 'movie_save_meta_box_data' ) ) {
        return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
        }

        // Check the user's permissions.
        if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return;
        }

        } else {

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
        }

        if ( ! isset( $_POST['movie_dnew_field'] ) &&  ! isset( $_POST['movie_anew_field'] )) {
        return;
        }

        $my_ddata = sanitize_text_field( $_POST['movie_dnew_field'] );
        $my_adata = sanitize_text_field( $_POST['movie_anew_field'] );

        

        update_post_meta( $post_id, 'movie_dnew_field', $my_ddata );
        update_post_meta( $post_id, 'movie_anew_field', $my_adata );
    }
        
 }

 if (class_exists('MoviePlugin')){
    $movieplugin = new MoviePlugin();
 }

 //activate
 register_activation_hook(__FILE__, array($movieplugin,'activate'));

 //deactivate
 register_deactivation_hook(__FILE__, array($movieplugin,'deactivate'));
