<?php 

function movie_listing_register_widget() {
    register_widget( 'movie_listing' );
}
    
add_action( 'widgets_init', 'movie_listing_register_widget' );

class movie_listing extends WP_Widget {

    // Set up the widget name and description.
	public function __construct() {
        
        $widget_options = array( 'classname' => 'movie_listing_widget', 'description' => 'This widget displays movie list' );
        parent::__construct( 'movie_listing_widget', 'Movie Listing Widget', $widget_options );
    }

    public function widget( $args, $instance ) {
        $title = apply_filters( 'widget_title', $instance['title'] );
        $number_movies = apply_filters( 'widget_title', $instance['number_movies'] );
        echo $args['before_widget'];
        //if title is present
        if ( ! empty( $title ) )
        echo $args['before_title'] . $title . $args['after_title'];
        
        echo "<ul>";
            // Define our WP Query Parameters
            $the_query = new WP_Query( array(
                'post_type' => array('movies'),
                 'posts_per_page' => $number_movies,
            )); 

            
            if( $the_query -> have_posts()) {
                
                while ($the_query -> have_posts()) : $the_query -> the_post();
                
                    // Display the Post Title with Hyperlink
                    echo "<li><a href=".get_permalink($the_query -> ID).">" . get_the_title($the_query -> ID) . "</a></li>";            

                // Repeat the process and reset once it hits the limit
                endwhile;
            
            }else{
                // No posts//
                echo "No movies found";
            }

            
            wp_reset_postdata();

        echo "</ul>";


        //output
        // echo __( 'Movie Lisitng', 'text_domain' );
        echo $args['after_widget'];
    }

    //displaying the form in widget
    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) )
            $title = $instance[ 'title' ];
        else
            $title = __( '', 'text_domain' );
        

        if ( isset( $instance[ 'number_movies' ] ) )
            $number_movies = $instance[ 'number_movies' ];
        else
            $number_movies = __( '', 'text_domain' );
        ?>
        <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /><br/>
        <label for="<?php echo $this->get_field_id( 'number_movies' ); ?>"><?php _e( 'Number of movies to be displayed' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'number_movies' ); ?>" name="<?php echo $this->get_field_name( 'number_movies' ); ?>" type="number" value="<?php echo esc_attr( $number_movies ); ?>" /><br/>
        </p>
        <?php
    }

    //saving the data
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['number_movies'] = ( ! empty( $new_instance['number_movies'] ) ) ? strip_tags( $new_instance['number_movies'] ) : '';
        return $instance;
    }
}
