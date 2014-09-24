<?php

/*------------------------------------*\
    External Modules/Files
\*------------------------------------*/

// Load any external files you have here

/*------------------------------------*\
    Theme Support
\*------------------------------------*/

if (!isset($content_width))
{
    $content_width = 900;
}

if (function_exists('add_theme_support'))
{
    // Add Menu Support
    add_theme_support('menus');

    // Add Thumbnail Theme Support
    add_theme_support('post-thumbnails');
    add_image_size('large', 700, '', true); // Large Thumbnail
    add_image_size('medium', 250, '', true); // Medium Thumbnail
    add_image_size('small', 120, '', true); // Small Thumbnail
    add_image_size('slider', 1000, 350, true); // Slider Image

 
    //NEW HTML5 Galleries

    add_theme_support( 'html5', array( 'gallery', 'caption' ) );

    
    // Add Support for Custom Backgrounds - Uncomment below if you're going to use
    /*add_theme_support('custom-background', array(
	'default-color' => 'FFF',
	'default-image' => get_template_directory_uri() . '/img/bg.jpg'
    ));*/

    // Add Support for Custom Header - Uncomment below if you're going to use
    /*add_theme_support('custom-header', array(
	'default-image'			=> get_template_directory_uri() . '/img/headers/default.jpg',
	'header-text'			=> false,
	'default-text-color'		=> '000',
	'width'				=> 1000,
	'height'			=> 198,
	'random-default'		=> false,
	'wp-head-callback'		=> $wphead_cb,
	'admin-head-callback'		=> $adminhead_cb,
	'admin-preview-callback'	=> $adminpreview_cb
    ));*/

    // Enables post and comment RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Localisation Support
    load_theme_textdomain('line5theme', get_template_directory() . '/languages');
}

/*------------------------------------*\
	Functions
\*------------------------------------*/

// line5 Theme navigation
function line5theme_nav()
{
	wp_nav_menu(
	array(
		'theme_location'  => 'header-menu',
		'menu'            => '',
		'container'       => 'div',
		'container_class' => 'menu-{menu slug}-container',
		'container_id'    => '',
		'menu_class'      => 'menu',
		'menu_id'         => '',
		'echo'            => true,
		'fallback_cb'     => 'wp_page_menu',
		'before'          => '',
		'after'           => '',
		'link_before'     => '',
		'link_after'      => '',
		'items_wrap'      => '<ul itemscope=itemscope itemtype="http://www.schema.org/SiteNavigationElement">%3$s</ul>',
		'depth'           => 0,
		'walker'          => new schema_walker_nav_menu
		)
	);
}


// Custom Walker to add schema markup to the li and a element.

class schema_walker_nav_menu extends Walker_Nav_Menu {
	/**
	 * Start the element output.
	 *
	 * @see Walker::start_el()
	 *
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item   Menu item data object.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 * @param int    $id     Current item ID.
	 */
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		/**
		 * Filter the CSS class(es) applied to a menu item's <li>.
		 *
		 * @since 3.0.0
		 *
		 * @see wp_nav_menu()
		 *
		 * @param array  $classes The CSS classes that are applied to the menu item's <li>.
		 * @param object $item    The current menu item.
		 * @param array  $args    An array of wp_nav_menu() arguments.
		 */
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		/**
		 * Filter the ID applied to a menu item's <li>.
		 *
		 * @since 3.0.1
		 *
		 * @see wp_nav_menu()
		 *
		 * @param string $menu_id The ID that is applied to the menu item's <li>.
		 * @param object $item    The current menu item.
		 * @param array  $args    An array of wp_nav_menu() arguments.
		 */
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<li itemprop=name' . $id . $class_names .'>';

		$atts = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
		$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
		$atts['href']   = ! empty( $item->url )        ? $item->url        : '';

		/**
		 * Filter the HTML attributes applied to a menu item's <a>.
		 *
		 * @since 3.6.0
		 *
		 * @see wp_nav_menu()
		 *
		 * @param array $atts {
		 *     The HTML attributes applied to the menu item's <a>, empty strings are ignored.
		 *
		 *     @type string $title  Title attribute.
		 *     @type string $target Target attribute.
		 *     @type string $rel    The rel attribute.
		 *     @type string $href   The href attribute.
		 * }
		 * @param object $item The current menu item.
		 * @param array  $args An array of wp_nav_menu() arguments.
		 */
		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$item_output = $args->before;
		$item_output .= '<a itemprop=url'. $attributes .'>';
		/** This filter is documented in wp-includes/post-template.php */
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		/**
		 * Filter a menu item's starting output.
		 *
		 * The menu item's starting output only includes $args->before, the opening <a>,
		 * the menu item's title, the closing </a>, and $args->after. Currently, there is
		 * no filter for modifying the opening and closing <li> for a menu item.
		 *
		 * @since 3.0.0
		 *
		 * @see wp_nav_menu()
		 *
		 * @param string $item_output The menu item's starting HTML output.
		 * @param object $item        Menu item data object.
		 * @param int    $depth       Depth of menu item. Used for padding.
		 * @param array  $args        An array of wp_nav_menu() arguments.
		 */
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

} // Walker_Nav_Menu


// Load line5 Theme scripts (header.php)
function line5theme_header_scripts()
{
    if (!is_admin()) {

    	wp_deregister_script('jquery'); // Deregister WordPress jQuery
    	wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js', array(), '1.9.1'); // Google CDN jQuery
    	wp_enqueue_script('jquery'); // Enqueue it!

    	wp_register_script('conditionizr', 'http://cdnjs.cloudflare.com/ajax/libs/conditionizr.js/4.0.0/conditionizr.js', array(), '4.0.0'); // Conditionizr
        wp_enqueue_script('conditionizr'); // Enqueue it!

        wp_register_script('modernizr', 'http://cdnjs.cloudflare.com/ajax/libs/modernizr/2.7.1/modernizr.min.js', array(), '2.6.2'); // Modernizr
        wp_enqueue_script('modernizr'); // Enqueue it!

        wp_register_script('flexslider', get_template_directory_uri() . '/js/jquery.flexslider-min.js', array(), '1.0.0'); // flexslider
        wp_enqueue_script('flexslider'); // Enqueue it!

        wp_register_script('line5themescripts', get_template_directory_uri() . '/js/scripts.js', array(), '1.0.0'); // Custom scripts
        wp_enqueue_script('line5themescripts'); // Enqueue it!

        wp_register_script('meanmenu', get_template_directory_uri() . '/js/jquery.meanmenu.min.js', array(), '1.0.0'); // Custom scripts
        wp_enqueue_script('meanmenu'); // Enqueue it!
    }


}

function admin_scripts(){
  
  if (is_admin()) {

        //wp_register_script('font-awesome', get_template_directory_uri() . '/js/font-awesome.js', array(), '1.0.0'); // Custom scripts
        //wp_enqueue_script('font-awesome'); // Enqueue it! 

    wp_register_style('fontawesome', '//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css');
    wp_enqueue_style( 'fontawesome'); 
    
    }

}

add_action( 'admin_head', 'admin_scripts' );

// Load line5 Theme conditional scripts
function line5theme_conditional_scripts(){

    if (is_page('pagenamehere')) {
        wp_register_script('scriptname', get_template_directory_uri() . '/js/scriptname.js', array('jquery'), '1.0.0'); // Conditional script(s)
        wp_enqueue_script('scriptname'); // Enqueue it!


    }

}


// Load line5 Theme styles
function line5theme_styles()
{
    wp_register_style('normalize', get_template_directory_uri() . '/normalize.css', array(), '1.0', 'all');
    wp_enqueue_style('normalize'); // Enqueue it!

    wp_register_style('flexslider', get_template_directory_uri() . '/css/flexslider.css', array(), '1.0', 'all');
    wp_enqueue_style('flexslider'); // Enqueue it!

    wp_register_style('googleFonts', 'http://fonts.googleapis.com/css?family=Lato:400,900|Asap:400,700');
    wp_enqueue_style( 'googleFonts');

    wp_register_style('fontawesome', '//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css');
    wp_enqueue_style( 'fontawesome');

    wp_register_style('line5theme', get_template_directory_uri() . '/style.css', array(), '1.0', 'all');
    wp_enqueue_style('line5theme'); // Enqueue it!

}

// Register line5 Theme Navigation
function register_line5_menu()
{
    register_nav_menus(array( // Using array to specify more menus if needed
        'header-menu' => __('Header Menu', 'line5theme'), // Main Navigation,
        'footer-menu' => __('Footer Menu', 'line5theme') // Footer Navigation
    ));
}

// Remove the <div> surrounding the dynamic navigation to cleanup markup
function my_wp_nav_menu_args($args = '')
{
    $args['container'] = false;
    return $args;
}

// Remove Injected classes, ID's and Page ID's from Navigation <li> items
function my_css_attributes_filter($var)
{
    return is_array($var) ? array() : '';
}

// Remove invalid rel attribute values in the categorylist
function remove_category_rel_from_category_list($thelist)
{
    return str_replace('rel="category tag"', 'rel="tag"', $thelist);
}

// Add page slug to body class, love this - Credit: Starkers Wordpress Theme
function add_slug_to_body_class($classes)
{
    global $post;
    if (is_home()) {
        $key = array_search('blog', $classes);
        if ($key > -1) {
            unset($classes[$key]);
        }
    } elseif (is_page()) {
        $classes[] = sanitize_html_class($post->post_name);
    } elseif (is_singular()) {
        $classes[] = sanitize_html_class($post->post_name);
    }

    return $classes;
}

// If Dynamic Sidebar Exists
if (function_exists('register_sidebar'))
{
    // Define Sidebar Widget Area 1
    register_sidebar(array(
        'name' => __('Widget Area 1', 'line5theme'),
        'description' => __('Description for this widget-area...', 'line5theme'),
        'id' => 'widget-area-1',
        'before_widget' => '<div id="%1$s" class="%2$s widget">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ));

}

// Remove wp_head() injected Recent Comment styles
function my_remove_recent_comments_style()
{
    global $wp_widget_factory;
    remove_action('wp_head', array(
        $wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
        'recent_comments_style'
    ));
}

// Pagination for paged posts, Page 1, Page 2, Page 3, with Next and Previous Links, No plugin
function line5_wp_pagination()
{
    global $wp_query;
    $big = 999999999;
    echo paginate_links(array(
        'base' => str_replace($big, '%#%', get_pagenum_link($big)),
        'format' => '?paged=%#%',
        'current' => max(1, get_query_var('paged')),
        'total' => $wp_query->max_num_pages
    ));
}

// Custom Excerpt Function Example: custom_excerpt(40, 'Learn More');
//This will create an excerpt with 40 words and a Learn More link.
function custom_excerpt($length_callback = '', $more_callback = '')
{
    global $post;
    $content = get_the_content();
    $trimmed_content = wp_trim_words( $content, $length_callback, '... <a class="more" href="'. get_permalink() .'">' .$more_callback .'</a>' );
    echo $trimmed_content;
}

// Custom View Article link to Post
function line5_blank_view_article($more)
{
    global $post;
    return '... <a class="view-article" href="' . get_permalink($post->ID) . '">' . __('View Article', 'line5theme') . '</a>';
}

// Remove Admin bar
function remove_admin_bar()
{
    return false;
}

// Remove 'text/css' from our enqueued stylesheet
function line5_style_remove($tag)
{
    return preg_replace('~\s+type=["\'][^"\']++["\']~', '', $tag);
}

// Remove thumbnail width and height dimensions that prevent fluid images in the_thumbnail
function remove_thumbnail_dimensions( $html )
{
    $html = preg_replace('/(width|height)=\"\d*\"\s/', "", $html);
    return $html;
}

// Custom Gravatar in Settings > Discussion
function line5themegravatar ($avatar_defaults)
{
    $myavatar = get_template_directory_uri() . '/img/gravatar.jpg';
    $avatar_defaults[$myavatar] = "Custom Gravatar";
    return $avatar_defaults;
}

// Threaded Comments
function enable_threaded_comments()
{
    if (!is_admin()) {
        if (is_singular() AND comments_open() AND (get_option('thread_comments') == 1)) {
            wp_enqueue_script('comment-reply');
        }
    }
}

// Custom Comments Callback
function line5themecomments($comment, $args, $depth)
{
	$GLOBALS['comment'] = $comment;
	extract($args, EXTR_SKIP);

	if ( 'div' == $args['style'] ) {
		$tag = 'div';
		$add_below = 'comment';
	} else {
		$tag = 'li';
		$add_below = 'div-comment';
	}
?>
    <!-- heads up: starting < for the html tag (li or div) in the next line: -->
    <<?php echo $tag ?> <?php comment_class(empty( $args['has_children'] ) ? '' : 'parent') ?> id="comment-<?php comment_ID() ?>">
	<?php if ( 'div' != $args['style'] ) : ?>
	<div id="div-comment-<?php comment_ID() ?>" class="comment-body">
	<?php endif; ?>
	<div class="comment-author vcard">
	<?php if ($args['avatar_size'] != 0) echo get_avatar( $comment, $args['180'] ); ?>
	<?php printf(__('<cite class="fn">%s</cite> <span class="says">says:</span>'), get_comment_author_link()) ?>
	</div>
<?php if ($comment->comment_approved == '0') : ?>
	<em class="comment-awaiting-moderation"><?php _e('Your comment is awaiting moderation.') ?></em>
	<br />
<?php endif; ?>

	<div class="comment-meta commentmetadata"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>">
		<?php
			printf( __('%1$s at %2$s'), get_comment_date(),  get_comment_time()) ?></a><?php edit_comment_link(__('(Edit)'),'  ','' );
		?>
	</div>

	<?php comment_text() ?>

	<div class="reply">
	<?php comment_reply_link(array_merge( $args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
	</div>
	<?php if ( 'div' != $args['style'] ) : ?>
	</div>
	<?php endif; ?>
<?php }

//Grabs LOGO
function logo(){

    if(of_get_option( 'logo_upload' )){
        $logo = of_get_option( 'logo_upload' );
    } else {
        $logo = get_template_directory_uri().'/img/logo.png';
    }

    return $logo;
}

/*----------------------------------------------*\
     Actions + Filters + ShortCodes
\*----------------------------------------------*/

// Add Actions
add_action('init', 'line5theme_header_scripts'); // Add Custom Scripts to wp_head
add_action('wp_print_scripts', 'line5theme_conditional_scripts'); // Add Conditional Page Scripts
add_action('get_header', 'enable_threaded_comments'); // Enable Threaded Comments
add_action('wp_enqueue_scripts', 'line5theme_styles'); // Add Theme Stylesheet
add_action('init', 'register_line5_menu'); // Add line5 Theme Menu
add_action('init', 'create_post_type_html5'); // Add our line5 Theme Custom Post Type
add_action('widgets_init', 'my_remove_recent_comments_style'); // Remove inline Recent Comment Styles from wp_head()
add_action('init', 'line5_wp_pagination'); // Add our HTML5 Pagination

// Remove Actions
remove_action('wp_head', 'feed_links_extra', 3); // Display the links to the extra feeds such as category feeds
remove_action('wp_head', 'feed_links', 2); // Display the links to the general feeds: Post and Comment Feed
remove_action('wp_head', 'rsd_link'); // Display the link to the Really Simple Discovery service endpoint, EditURI link
remove_action('wp_head', 'wlwmanifest_link'); // Display the link to the Windows Live Writer manifest file.
remove_action('wp_head', 'index_rel_link'); // Index link
remove_action('wp_head', 'parent_post_rel_link', 10, 0); // Prev link
remove_action('wp_head', 'start_post_rel_link', 10, 0); // Start link
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0); // Display relational links for the posts adjacent to the current post.
remove_action('wp_head', 'wp_generator'); // Display the XHTML generator that is generated on the wp_head hook, WP version
remove_action('wp_head', 'start_post_rel_link', 10, 0);
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
remove_action('wp_head', 'rel_canonical');
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);

// Add Filters
add_filter('avatar_defaults', 'line5themegravatar'); // Custom Gravatar in Settings > Discussion
add_filter('body_class', 'add_slug_to_body_class'); // Add slug to body class (Starkers build)
add_filter('widget_text', 'do_shortcode'); // Allow shortcodes in Dynamic Sidebar
add_filter('widget_text', 'shortcode_unautop'); // Remove <p> tags in Dynamic Sidebars (better!)
add_filter('wp_nav_menu_args', 'my_wp_nav_menu_args'); // Remove surrounding <div> from WP Navigation
// add_filter('nav_menu_css_class', 'my_css_attributes_filter', 100, 1); // Remove Navigation <li> injected classes (Commented out by default)
// add_filter('nav_menu_item_id', 'my_css_attributes_filter', 100, 1); // Remove Navigation <li> injected ID (Commented out by default)
// add_filter('page_css_class', 'my_css_attributes_filter', 100, 1); // Remove Navigation <li> Page ID's (Commented out by default)
add_filter('the_category', 'remove_category_rel_from_category_list'); // Remove invalid rel attribute
add_filter('the_excerpt', 'shortcode_unautop'); // Remove auto <p> tags in Excerpt (Manual Excerpts only)
add_filter('the_excerpt', 'do_shortcode'); // Allows Shortcodes to be executed in Excerpt (Manual Excerpts only)
add_filter('excerpt_more', 'line5_blank_view_article'); // Add 'View Article' button instead of [...] for Excerpts
add_filter('show_admin_bar', 'remove_admin_bar'); // Remove Admin bar
add_filter('style_loader_tag', 'line5_style_remove'); // Remove 'text/css' from enqueued stylesheet
add_filter('post_thumbnail_html', 'remove_thumbnail_dimensions', 10); // Remove width and height dynamic attributes to thumbnails
add_filter('image_send_to_editor', 'remove_thumbnail_dimensions', 10); // Remove width and height dynamic attributes to post images

// Remove Filters
remove_filter('the_excerpt', 'wpautop'); // Remove <p> tags from Excerpt altogether

// Shortcodes
add_shortcode('line5_shortcode_demo', 'line5_shortcode_demo'); // You can place [line5_shortcode_demo] in Pages, Posts now.
add_shortcode('line5_shortcode_demo_2', 'line5_shortcode_demo_2'); // Place [line5_shortcode_demo_2] in Pages, Posts now.

// Shortcodes above would be nested like this -
// [line5_shortcode_demo] [line5_shortcode_demo_2] Here's the page title! [/line5_shortcode_demo_2] [/line5_shortcode_demo]

/*------------------------------------*\
	Custom Post Types
\*------------------------------------*/

// Create 1 Custom Post type for a Demo, called line5-Theme
function create_post_type_html5()
{
   /* register_taxonomy_for_object_type('category', 'line5-theme'); // Register Taxonomies for Category
    register_taxonomy_for_object_type('post_tag', 'line5-theme');
    register_post_type('line5-theme', // Register Custom Post Type
        array(
        'labels' => array(
            'name' => __('line5 Theme Custom Post', 'line5theme'), // Rename these to suit
            'singular_name' => __('line5 Theme Custom Post', 'line5theme'),
            'add_new' => __('Add New', 'line5theme'),
            'add_new_item' => __('Add New line5 Theme Custom Post', 'line5theme'),
            'edit' => __('Edit', 'line5theme'),
            'edit_item' => __('Edit line5 Theme Custom Post', 'line5theme'),
            'new_item' => __('New line5 Theme Custom Post', 'line5theme'),
            'view' => __('View line5 Theme Custom Post', 'line5theme'),
            'view_item' => __('View line5 Theme Custom Post', 'line5theme'),
            'search_items' => __('Search line5 Theme Custom Post', 'line5theme'),
            'not_found' => __('No line5 Theme Custom Posts found', 'line5theme'),
            'not_found_in_trash' => __('No line5 Theme Custom Posts found in Trash', 'line5theme')
        ),
        'public' => true,
        'hierarchical' => true, // Allows your posts to behave like Hierarchy Pages
        'has_archive' => true,
        'supports' => array(
            'title',
            'editor',
            'excerpt',
            'thumbnail'
        ), // Go to Dashboard Custom line5 Theme post for supports
        'can_export' => true, // Allows export in Tools > Export
        'taxonomies' => array(
            'post_tag',
            'category'
        ) // Add Category and Post Tags support
    ));*/

    register_post_type('slider', // Register Custom Post Type
        array(
        'labels' => array(
            'name' => __('Slider', 'line5theme'), // Rename these to suit
            'singular_name' => __('Slider', 'line5theme'),
            'add_new' => __('Add New', 'line5theme'),
            'add_new_item' => __('Add New Slider', 'line5theme'),
            'edit' => __('Edit', 'line5theme'),
            'edit_item' => __('Edit Slider', 'line5theme'),
            'new_item' => __('New Slider', 'line5theme'),
            'view' => __('View Slider', 'line5theme'),
            'view_item' => __('View Slider', 'line5theme'),
            'search_items' => __('Search Slider', 'line5theme'),
            'not_found' => __('No Sliders found', 'line5theme'),
            'not_found_in_trash' => __('No Sliders found in Trash', 'line5theme')
        ),
        'public' => true,
        'hierarchical' => true, // Allows your posts to behave like Hierarchy Pages
        'has_archive' => true,
        'supports' => array(
            'title',
            'thumbnail',
        ), // Go to Dashboard Custom line5 Theme post for supports
        'can_export' => true, // Allows export in Tools > Export
    ));

register_taxonomy_for_object_type('category', 'line5-theme');
register_post_type('testimonials', // Register Custom Post Type
        array(
        'labels' => array(
            'name' => __('Testimonial', 'line5theme'), // Rename these to suit
            'singular_name' => __('Testimonial', 'line5theme'),
            'add_new' => __('Add New', 'line5theme'),
            'add_new_item' => __('Add New Testimonial', 'line5theme'),
            'edit' => __('Edit', 'line5theme'),
            'edit_item' => __('Edit Testimonial', 'line5theme'),
            'new_item' => __('New Testimonial', 'line5theme'),
            'view' => __('View Testimonial', 'line5theme'),
            'view_item' => __('View Testimonial', 'line5theme'),
            'search_items' => __('Search Testimonial', 'line5theme'),
            'not_found' => __('No Testimonials found', 'line5theme'),
            'not_found_in_trash' => __('No Testimonials found in Trash', 'line5theme')
        ),
        'public' => true,
        'hierarchical' => true, // Allows your posts to behave like Hierarchy Pages
        'has_archive' => true,
        'supports' => array(
            'title',
            'editor',
            'thumbnail',
        ), // Go to Dashboard Custom line5 Theme post for supports
        'can_export' => true, // Allows export in Tools > Export
        'taxonomies' => array(
            'category'
        ) // Add Category
    ));
}

/*------------------------------------*\
    Custom Fields
\*------------------------------------*/

require_once( trailingslashit( get_template_directory() ). 'inc/custom-fields.php' );


// This allows a custom JS file to run on the admin when restricting custom fields to page templates.
//Orginally the custom fields would show up on any page until you saved it. This hides it until you select
//the page template you set it too. 

function customField_admin_scripts($hook) {
     $screen = get_current_screen();

    if( in_array( $screen->id, array( 'page'/* custom post_type_names too */ ) ) ) {
        wp_enqueue_script( 'custom-fields-js', get_template_directory_uri()."/js/custom-fields.js", array( 'jquery' ));
    }
}
add_action('admin_enqueue_scripts', 'customField_admin_scripts');

/*-----------------------------------------*\
    Drag and Drop Page Order
\*------------------------------------------*/

require_once( trailingslashit( get_template_directory() ). 'inc/page-order/simple-page-ordering.php' );

add_action( 'pre_get_posts', 'change_order_by' );

/*-- Change order of queries to match menu order --*/
function change_order_by( $query ) {
    
        $query->set( 'orderby', 'menu_order' );
 
}


/*------------------------------------*\
      ShortCode Functions
\*------------------------------------*/

// Shortcode Demo with Nested Capability
function line5_shortcode_demo($atts, $content = null)
{
    return '<div class="shortcode-demo">' . do_shortcode($content) . '</div>'; // do_shortcode allows for nested Shortcodes
}

// Shortcode Demo with simple <h2> tag
function line5_shortcode_demo_2($atts, $content = null) // Demo Heading H2 shortcode, allows for nesting within above element. Fully expandable.
{
    return '<h2>' . $content . '</h2>';
}


/*
	THEME OPTIONS FRAMEWORK

 * Loads the Options Panel
 *
 * If you're loading from a child theme use stylesheet_directory
 * instead of template_directory
 */

define( 'OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/inc/' );
require_once dirname( __FILE__ ) . '/inc/options-framework.php';

/*
 * This is an example of how to add custom scripts to the options panel.
 * This one shows/hides the an option when a checkbox is clicked.
 *
 * You can delete it if you not using that option
 */

add_action( 'optionsframework_custom_scripts', 'optionsframework_custom_scripts' );

function optionsframework_custom_scripts() { ?>

<script type="text/javascript">
jQuery(document).ready(function() {

	jQuery('#example_showhidden').click(function() {
  		jQuery('#section-example_text_hidden').fadeToggle(400);
	});

	if (jQuery('#example_showhidden:checked').val() !== undefined) {
		jQuery('#section-example_text_hidden').show();
	}

});
</script>

<?php
}

/*------------------------------------*\
   BETTER FILE EDITOR
\*------------------------------------*/

class BetterFileEditorPlugin {

    function BetterFileEditorPlugin() {
        add_action('admin_footer-theme-editor.php', array($this, 'admin_footer'));
        add_action('admin_footer-plugin-editor.php', array($this, 'admin_footer'));
    }

    function admin_footer() {
        ?>
        <script src="<?php echo get_template_directory_uri(). '/js/require.js'; ?>"></script>
        <script src="<?php echo get_template_directory_uri(). '/js/ace/ace.js'; ?>"></script>
        <script src="<?php echo get_template_directory_uri(). '/js/ace/ext-modelist.js'; ?>"></script>
        <script type="text/javascript" charset="utf-8">
            jQuery(document).ready(function() {
                /**
                 * Detecting the HTML5 Canvas API (usually) gives us IE9+ and
                 * of course all modern browsers. This should be adequate for
                 * minimum requirements instead of browser sniffing.
                 */
                if(!!document.createElement('canvas').getContext)
                {
                    var wpacejs = document.createElement('script');
                    wpacejs.type = 'text/javascript'; wpacejs.charset = 'utf-8';
                    wpacejs.src = '<?php echo get_template_directory_uri(). "/js/wp-ace.js"; ?>';
                    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(wpacejs, s);
                }
            });
        </script>
        <style type="text/css">
            #template div {
                /* Need to reset margin here from core styles since it destroys
                   every single div contained in the editor... */
                margin-right: 0px;
            }
            #template #editor, #template > div {
                /* ... then redefine it in a much more scoped manner. */
                margin-right: 210px;
            }
            #template div #newcontent {
                width: 100%;
            }
            #wp-ace-editor {
                position: relative;
                height: 560px;
                font-size: 12px;
                border: 1px solid #BBB;
                border-radius: 3px;
            }
            .ace_editor {
                font-family: Consolas, Menlo, "Liberation Mono", Courier, monospace !important;
            }
            #wp-ace-editor-controls table td {
                vertical-align: center;
                padding: 5px;
            }
        </style>
        <?php
    }

}

$bfe_plugin = new BetterFileEditorPlugin();

//ADD TEXT EDITOR TO WORDPRESS POSTS TEXT EDITOR!!!!!

define('HESH_LIBS',get_template_directory_uri().'/inc/lib/');

class wp_html_editor_syntax {
    public function __construct(){
        add_action('admin_head',array(&$this,'admin_head'));
        add_action('admin_footer',array(&$this,'admin_footer'));
    }
    public function admin_head(){
        if (!$this->is_editor())
            return;
        ?>
        <link rel="stylesheet" href="<?php echo HESH_LIBS; ?>hesh.min.css">
        <?php
    }
    public function admin_footer(){
        if (!$this->is_editor())
            return;
        ?>
        <script src="<?php echo HESH_LIBS; ?>hesh.min.js"></script>
        <?php
    }
    private function is_editor(){
        if (!strstr($_SERVER['SCRIPT_NAME'],'post.php') && !strstr($_SERVER['SCRIPT_NAME'],'post-new.php')) {
            return false;
        }
        return true;
    }
}

if (is_admin())
    $hesh = new wp_html_editor_syntax();


/*------------------------------------*\
    CUSTOM H1
\*------------------------------------*/

function custom_title($title) {
    global $post;

    if (in_the_loop() ) {

        if(get_post_meta($post->ID,'custom_h1', true)){
            return get_post_meta($post->ID,'custom_h1', true);
        }else{
          return $post->post_title;  
        }
    }

    return $title;
}
add_filter('the_title', 'custom_title', 10, 2);



/*------------------------------------------------*\
    TESTIMONIALS SHORTCODE
\*------------------------------------------------*/

function testimonials($atts, $content = null) {

    extract(shortcode_atts(array(
        "words" => '50',
        "more" => 'View Testimonial',
        "posts" => 5,
        "category" => ''

    ), $atts));

    $cat_slug = str_replace(' ', '-', $category);

 global $post;

    ob_start();?>

    <div class="flexslider testimonial_slider">
                <ul class="slides">
                    <?php
                
                    $args = array( 'posts_per_page' => $posts, 'post_type' => 'testimonials', 'category_name' => $cat_slug );

                    $myposts = get_posts( $args );
                    foreach ( $myposts as $post ) : setup_postdata( $post ); ?>
                        <li>
                           
                             <blockquote>

                                    <!-- post thumbnail -->
                                    <?php if ( has_post_thumbnail()) : // Check if thumbnail exists ?>
                                        <?php the_post_thumbnail('thumbnail'); ?>
                                    <?php endif; ?>
                                    <!-- /post thumbnail -->
                                <?php

                                $content = get_the_content();
                                $trimmed_content = wp_trim_words( $content, $words, '<a href="'. get_permalink() .'">... '. $more .'</a>' );
                                
                                ?>
                                
                                <p><?php echo $trimmed_content;?></p>


                                 <footer class="author">
                                   &ndash; <?php the_title(); ?>
                                 </footer>
                               </blockquote>
                        </li>
                    <?php endforeach; 
                    wp_reset_postdata();?>

                </ul>

            </div>

    <?php $content = ob_get_contents();
    ob_end_clean();
    return $content;

}
add_shortcode('testimonials', 'testimonials');

// THE SHORTCODE ----> [testimonials words="50" more="View More" posts="5" category=""]

/*-------------------------------------------------*\
    CUSTOM MENU SHORTCODE
\*-------------------------------------------------*/

function menu($atts, $content = null) {

    extract(shortcode_atts(array(
        "menu_name" => ''
    ), $atts));

    ob_start();?>

    <div class="custom_menu">
                    
        <?php wp_nav_menu( array('menu' => $menu_name )); ?>
    </div>

    <?php $content = ob_get_contents();
    ob_end_clean();
    return $content;

}
add_shortcode('menu', 'menu');


/*--------------------------------------------------*\
    FEATURE POSTS SHORCODE
\*--------------------------------------------------*/

function featured($atts, $content = null) {

    extract(shortcode_atts(array(
        "post_type" => '',
        "post_id" => '',
        "posts" => ''
    ), $atts));

    global $post;

    ob_start();?>

    <?php

        // The Query
        $feature_query = new WP_Query( $args );

        // The Loop
        if ( $feature_query->have_posts() ) {
            echo '<ul class="featured">';
            while ( $feature_query->have_posts() ) {
                $feature_query->the_post(); ?>

                <li>
                    
                    <!-- post thumbnail -->
                        <?php if ( has_post_thumbnail()) : // Check if thumbnail exists ?>
                            <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
                                <?php the_post_thumbnail('featured-mega'); // Declare pixel size you need inside the array ?>
                            </a>
                        <?php endif; ?>
                    <!-- /post thumbnail -->
                    <h5><?php the_title(); ?></h5>

                    <p>
                        <?php custom_excerpt(10, 'Learn More'); ?>
                    </p>
                    </li>

                </li>                


           <?php }
            echo '</ul>';
        } else {
            // no posts found
        }
        /* Restore original Post Data */
        wp_reset_postdata(); 

    $content = ob_get_contents();
    ob_end_clean();
    return $content;

}
add_shortcode('featured', 'featured');

/*-------------------------------------------------------------------*\
    ADD SHORTCODE BUTTON FOR TINYMCE
\*-------------------------------------------------------------------*/

// Hooks your functions into the correct filters
function add_custom_menu_button() {
    // check user permissions
    if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
        return;
    }
    // check if WYSIWYG is enabled
    if ( 'true' == get_user_option( 'rich_editing' ) ) {
        add_filter( 'mce_external_plugins', 'cm_add_tinymce_plugin' );
        add_filter( 'mce_buttons', 'cm_register_mce_button' );
    }
}
add_action('admin_head', 'add_custom_menu_button');

// Declare script for new button
function cm_add_tinymce_plugin( $plugin_array ) {
    $plugin_array['custom_menu_button'] = get_template_directory_uri() .'/js/menu_shortcodes.js';
    return $plugin_array;
}

// Register new button in the editor
function cm_register_mce_button( $buttons ) {
    array_push( $buttons, 'custom_menu_button' );
    return $buttons;
}


//ADD HOMEPAGE EDIT LINK IN ADMIN MENU UNDER DASHBOARD MENU
add_action( 'admin_menu' , 'admin_menu_new_items' );
function admin_menu_new_items() {
    global $submenu;
 
 $front_page = get_option('page_on_front');
 
 if($front_page != 0){
 $submenu['index.php'][500] = array( 'Edit Home Page', 'manage_options' , get_edit_post_link($front_page) ); 
 
 }
}

//ADD HOMEPAGE EDIT LINK TO ADMIN BAR
add_action('admin_bar_menu', 'add_toolbar_items',999);

function add_toolbar_items($admin_bar){
	
	$front_page = get_option('page_on_front');
	
 if($front_page != 0){	
    $admin_bar->add_menu( array(
        'id'    => 'edit-home',
        'parent' => 'site-name',
        'title' => 'Edit Home Page',
        'href'  => get_edit_post_link($front_page),
        'meta'  => array(
            'title' => __('Edit Home Page'),            
        ),
    ));
	}
}

?>