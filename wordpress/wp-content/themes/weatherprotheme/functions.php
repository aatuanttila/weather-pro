<?php
function load_stylesheets() 
{
    wp_register_style('style', get_template_directory_uri() . '/style.css', array(), false, 'all');
    wp_enqueue_style('style');
}
add_action('wp_enqueue_scripts', 'load_stylesheets');

function loadjs() {
    wp_register_script('customjs', get_template_directory_uri() . '/js/scripts.js', '', 1, true);
    wp_enqueue_script('customjs');
}
add_action('wp_enqueue_scripts', 'loadjs');

function register_widget_areas() {
    register_sidebar( array(
        'name'          => 'Footer',
        'id'            => 'footer',
        'description'   => 'This is footer',
        'before_widget' => '<section class="footer">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2>',
        'after_title'   => '</h2>',
    ));
}
add_action( 'widgets_init', 'register_widget_areas' );

function create_posttype() {
    register_post_type( 'sijainnit',
        array(
            'labels' => array(
                'name' => __( 'Sijainnit' ),
                'singular_name' => __( 'Sijainti' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'sijainnit'),
            'show_in_rest' => true,
            'register_meta_box_cb' => 'add_sijainnit_metaboxes',
        )
    );
}
add_action( 'init', 'create_posttype');

function add_sijainnit_metaboxes() {
	add_meta_box(
		'sijainti_koordinaatti',
		'Sijainti koordinaatti',
        'sijainti_koordinaatti', 		
        'sijainnit',
		'side',
		'default' 
	);
}

/**
 * Output the HTML for the metabox.
 */
function sijainti_koordinaatti() {
	global $post;

	// Nonce field to validate form request came from current site
	wp_nonce_field( basename( __FILE__ ), 'sijainti_fields');

	// Get the location data if it's already been entered
	$sijainti_koordinaatti = get_post_meta( $post->ID, 'sijainti_koordinaatti', true );

	// Output the field
	echo '<input type="text" name="sijainti_koordinaatti" value="' . esc_textarea( $sijainti_koordinaatti )  . '" class="widefat">';
}

/**
 * Save the metabox data
 */
function save_sijainnit_meta( $post_id, $post ) {

	// Return if the user doesn't have edit permissions.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}

	// Verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times.
	if ( ! isset( $_POST['sijainti_koordinaatti'] ) || ! wp_verify_nonce( $_POST['sijainti_fields'], basename(__FILE__) ) ) {
		return $post_id;
	}

	// Now that we're authenticated, time to save the data.
	// This sanitizes the data from the field and saves it into an array $events_meta.
	$sijainnit_meta['sijainti_koordinaatti'] = esc_textarea( $_POST['sijainti_koordinaatti'] );

	// Cycle through the $events_meta array.
	// Note, in this example we just have one item, but this is helpful if you have multiple.
	foreach ( $sijainnit_meta as $key => $value ) :

		// Don't store custom data twice
		if ( 'revision' === $post->post_type ) {
			return;
		}

		if ( get_post_meta( $post_id, $key, false ) ) {
			// If the custom field already has a value, update it.
			update_post_meta( $post_id, $key, $value );
		} else {
			// If the custom field doesn't have a value, add it.
			add_post_meta( $post_id, $key, $value);
		}

		if ( ! $value ) {
			// Delete the meta key if there's no value
			delete_post_meta( $post_id, $key );
		}

	endforeach;

}
add_action( 'save_post', 'save_sijainnit_meta', 1, 2 );