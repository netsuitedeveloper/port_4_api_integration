<?php
define('THEME_DIRECTORY', get_template_directory_uri());

function xpel_setup() {

    // Adds RSS feed links to <head> for posts and comments.
    add_theme_support( 'automatic-feed-links' );

    register_nav_menu( 'primary', __( 'Primary Menu', 'xpel' ) );
    register_nav_menu( 'top_menu', __( 'Top Menu', 'xpel' ) );
    register_nav_menu( 'secondary', __( 'Secondary Menu', 'xpel' ) );
    
    register_nav_menu( 'footer_top', __( 'Footer Top', 'xpel' ) );
    register_nav_menu( 'footer_bottom', __( 'Footer Bottom', 'xpel' ) );

    // This theme uses a custom image size for featured images, displayed on "standard" posts.
    add_theme_support( 'post-thumbnails' );

    update_option('medium_size_w', 316);
    update_option('medium_size_h', 108);
}
add_action( 'after_setup_theme', 'xpel_setup' );

/**
 * Return the Google font stylesheet URL if available.
 *
 * The use of Open Sans by default is localized. For languages that use
 * characters not supported by the font, the font can be disabled.
 *
 * @return string Font stylesheet or empty string if disabled.
 */
function xpel_get_font_url() {
    $font_url = '';

    $subsets = 'latin,latin-ext';

    $protocol = is_ssl() ? 'https' : 'http';
    $query_args = array(
        'family' => 'Source+Sans+Pro:300,400,600,700,400italic',
        'subset' => $subsets,
    );
    $font_url = add_query_arg( $query_args, "$protocol://fonts.googleapis.com/css" );

    return $font_url;
}

/**
 * Enqueue scripts and styles for front-end.
 */
function xpel_scripts_styles() {
    global $wp_styles;

    // load styles
    $font_url = xpel_get_font_url();
    if ( ! empty( $font_url ) )
        wp_enqueue_style( 'xpel-fonts', esc_url_raw( $font_url ), array(), null );

    // Loads jquery.sidr plugin
    wp_enqueue_style( 'awesomefont', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css' );

    wp_enqueue_style( 'xpel-sidr-style', get_template_directory_uri() . '/css/jquery.sidr.dark.css' );
    wp_enqueue_style( 'xpel-modal-style', get_template_directory_uri() . '/js/modal/jquery.modal.css' );
    wp_enqueue_style( 'xpel-modal-style', get_template_directory_uri() . '/js/modal/jquery.modal.theme-xenon.css' );
    wp_enqueue_style( 'xpel-modal-style', get_template_directory_uri() . '/js/modal/jquery.modal.theme-atlant.css' );
    
    // homepage
    if(is_front_page()) {
        // load bx slider
        wp_enqueue_style( 'xpel-bxslider-style', get_template_directory_uri() . '/js/bxslider/jquery.bxslider.min.css' );
    }
    wp_enqueue_style( 'xpel-icheck-style', get_template_directory_uri() . '/js/icheck/skins/square/grey.css' ); // load only square-grey
    
    wp_enqueue_style( 'xpel-style', get_template_directory_uri() . '/style.css' );

    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'xpel-plugins', get_template_directory_uri() . '/js/jquery.main.js', array( 'jquery' ), null, true );
    wp_enqueue_script( 'xpel-sidr', get_template_directory_uri() . '/js/jquery.sidr.min.js', array( 'jquery' ), null, true );
    wp_enqueue_script( 'xpel-modal', get_template_directory_uri() . '/js/modal/jquery.modal.js', array( 'jquery' ), null, true );
    
    // homepage
    if(is_front_page()) {
        // load bx slider
        wp_enqueue_script( 'xpel-bxslider', get_template_directory_uri() . '/js/bxslider/jquery.bxslider.min.js', array( 'jquery' ), null, true );
    }
    wp_enqueue_script( 'xpel-icheck', get_template_directory_uri() . '/js/icheck/icheck.min.js', array( 'jquery' ), null, true );

    if(is_page_template('page-search.php') || is_page_template('page-request-quote.php')) {
        wp_enqueue_script( 'xpel-multisteps', get_template_directory_uri() . '/js/jFormslider/jFormslider.js', array( 'jquery' ), null, true );
    }

    wp_enqueue_script( 'xpel-main', get_template_directory_uri() . '/js/main.js', array( 'jquery' ), null, true );
}
add_action( 'wp_enqueue_scripts', 'xpel_scripts_styles' );


/**
 * Filter TinyMCE CSS path to include Google Fonts.
 *
 * Adds additional stylesheets to the TinyMCE editor if needed.
 *
 * @uses xpel_get_font_url() To get the Google Font stylesheet URL.
 *
 * @param string $mce_css CSS path to load in TinyMCE.
 * @return string Filtered CSS path.
 */
function xpel_mce_css( $mce_css ) {
    $font_url = xpel_get_font_url();

    if ( empty( $font_url ) )
        return $mce_css;

    if ( ! empty( $mce_css ) )
        $mce_css .= ',';

    $mce_css .= esc_url_raw( str_replace( ',', '%2C', $font_url ) );

    return $mce_css;
}
add_filter( 'mce_css', 'xpel_mce_css' );

/**
 * Register sidebars.
 *
 * Registers our main widget area and the front page widget areas.
 */
function xpel_widgets_init() {
    register_sidebar( array(
        'name' => __( 'Home Sidebar', 'xpel' ),
        'id' => 'sidebar-home',
        'description' => __( 'Appears on homepage.', 'xpel' ),
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => '</aside>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ) );

    register_sidebar( array(
        'name' => __( 'Testimonial Slide Widget', 'xpel' ),
        'id' => 'whyxpel_testimonial_widget',
        'description' => __( 'Appears on Why XPEL page.', 'xpel' ),
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => '</aside>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ) );

    register_sidebar( array(
        'name' => __( 'Post Sidebar', 'xpel' ),
        'id' => 'sidebar-4',
        'description' => __( 'Appears on posts, which has its own widgets', 'xpel' ),
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => '</aside>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ) );

    register_sidebar( array(
        'name' => __( 'Request a Quote Widget', 'xpel' ),
        'id' => 'request_quote_widget',
        'description' => __( 'Appears on pages.', 'xpel' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ) );
    register_sidebar( array(
        'name' => __( 'Competition Widget', 'xpel' ),
        'id' => 'competition_widget',
        'description' => __( 'Appears on pages.', 'xpel' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ) );
    register_sidebar( array(
        'name' => __( 'Installation Widget', 'xpel' ),
        'id' => 'installation_widget',
        'description' => __( 'Appears on pages.', 'xpel' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ) );

    register_sidebar( array(
        'name' => __( 'Get a Quote Button Widget', 'xpel' ),
        'id' => 'get_quote_btn_widget',
        'description' => __( 'Appears on pages.', 'xpel' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ) );
    
    register_sidebar( array(
        'name' => __( 'Contact Sidebar', 'xpel' ),
        'id' => 'sidebar-contact',
        'description' => __( 'Appears on homepage.', 'xpel' ),
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => '</aside>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ) );    

    register_sidebar( array(
        'name' => __( 'Request a Quote Form', 'xpel' ),
        'id' => 'request_form_widget',
        'description' => __( 'Appears on Request a Quote Page.', 'xpel' ),
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => '</aside>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ) );    
}
add_action( 'widgets_init', 'xpel_widgets_init' );

/**
 * Extend the default WordPress body classes.
 *
 * @param array $classes Existing class values.
 * @return array Filtered class values.
 */
function xpel_body_class( $classes ) {
    $background_color = get_background_color();
    $background_image = get_background_image();

    if ( is_page_template( 'templates/why_xpel.php' ) ) {
        $classes[] = 'whyxpel-page';
    }

    if ( is_page_template( 'templates/company.php' ) ) {
        $classes[] = 'company-page';
    }
    return $classes;
}
add_filter( 'body_class', 'xpel_body_class' );

function xpel_show_admin_bar($show) {
    if (wp_is_mobile()) {
        return false;
    } else {
        return $show;
    }
}
add_filter('show_admin_bar', 'xpel_show_admin_bar');

/*function create_custom_post_types() {
    register_post_type( 'vehicles',
        array(
            'labels' => array(
                'name' => __( 'Vehicles' ),
                'singular_name' => __( 'Vehicle' )
            ),
            'supports' => array( 'title'),
            'public' => true,
            'has_archive' => false
        )
    );    
}
add_action('init', 'create_custom_post_types', 0);*/

// Custom Rewrite Rules
function xpel_custom_rewrite_tag() {
    add_rewrite_tag('%type%', '([^&]+)');
    add_rewrite_tag('%year_filter%', '([^&]+)');
    add_rewrite_tag('%make%', '([^&]+)');
    add_rewrite_tag('%model%', '([^&]+)');
    add_rewrite_tag('%submodel%', '([^&]+)');
    add_rewrite_tag('%series%', '([^&]+)');
}
add_action('init', 'xpel_custom_rewrite_tag', 10, 0);

function xpel_custom_rewrite_rule() {
    add_rewrite_rule('^search/([^/]*)/([^/]*)/([^/]*)/([^/]*)/([^/]*)/([^/]*)/?', 'index.php?pagename=search&type=$matches[1]&year_filter=$matches[2]&make=$matches[3]&model=$matches[4]&submodel=$matches[5]&series=$matches[6]', 'top');
    add_rewrite_rule('^search/([^/]*)/([^/]*)/([^/]*)/([^/]*)/([^/]*)/?', 'index.php?pagename=search&type=$matches[1]&year_filter=$matches[2]&make=$matches[3]&model=$matches[4]&submodel=$matches[5]', 'top');
    add_rewrite_rule('^search/([^/]*)/([^/]*)/([^/]*)/([^/]*)/?', 'index.php?pagename=search&type=$matches[1]&year_filter=$matches[2]&make=$matches[3]&model=$matches[4]', 'top');
    add_rewrite_rule('^search/([^/]*)/([^/]*)/([^/]*)/?', 'index.php?pagename=search&type=$matches[1]&year_filter=$matches[2]&make=$matches[3]', 'top');
    add_rewrite_rule('^search/([^/]*)/([^/]*)/?', 'index.php?pagename=search&type=$matches[1]&year_filter=$matches[2]', 'top');
    add_rewrite_rule('^search/([^/]*)/?', 'index.php?pagename=search&type=$matches[1]', 'top');
}
add_action('init', 'xpel_custom_rewrite_rule', 10, 0);

function get_original_query_value($original) {
    
     /*
    $terms = explode('-', $original);
    foreach ( $terms as $term ) {
        $term[0] = strtoupper($term[0]);
        $new_terms[] = $term;
    }
    
    return implode(' ', $new_terms);  */ 
    
    return str_replace('_', ' ', $original);
    
}

/*add_action('init', 'startSession', 1);
add_action('wp_logout', 'endSession');
add_action('wp_login', 'endSession');

function startSession() {
    if(!session_id()) {
        session_start();
    }
}

function endSession() {
    session_destroy();
}*/

function encode_url_param($str) {
    $str = str_replace("/", "__", $str);
    $str = str_replace("&amp;", "and", $str);
    $str = str_replace("&", "and", $str);
    $str = str_replace(" ", "_", $str);
    return $str;
}

function decode_url_param($str) {
    $str = str_replace("__", "/", $str);
    $str = str_replace("_and_", "_&_", $str);
    $str = str_replace("-and-", "&", $str);
    $str = str_replace("_", " ", $str);
    return $str;
}

add_action( 'wpcf7_before_send_mail', 'CF7_pre_send' );
function CF7_pre_send($cf7) {

    // set $mode = 1 for now
    if($cf7->id == 0) {
        $mode = 0;
    } else {
        $mode = 1;
    }

    $result = collectDataToNetsuite($mode);
	
	if(isset($result->success) && $result->success === true) {
        // in case netsuite api returns success message, no need to send email, so just return
        $returnData = array(
            'mailSent' => true
        );
        echo json_encode($returnData);exit;
    }
}

add_filter( 'wpcf7_mail_components', 'custom_mail_components');
 
function custom_mail_components($wpcf7_data, $form = null) {
 
    // post request to netsuite
    $message_body = $wpcf7_data['body'];
    
    // replace special unicode
    $message_body = str_replace('&#x40;', '@', $message_body);
    
    if(isset($_POST['coverage_options'])) {
        $message_body = str_replace('[vehicle_info]', $_POST['coverage_options'], $message_body);
        $message_body = str_replace('[coverage_options]', $_POST['coverage_options'], $message_body);
    }
    
    // last trick process to remove [vehicle_info]
    $message_body = str_replace('[vehicle_info]', "", $message_body);
    $message_body = str_replace('[coverage_options]', "", $message_body);
    
    // update country info
    $country = '';
    if(isset($_POST['country'])) {
        $country = $country_list[$_POST['country']];
    }
    $message_body = str_replace('[country_value]', $country, $message_body);

    // update post data
    $post_data = '';
    if(isset($_POST['posttitle'])) {
        $post_data .= 'Post Title : '.$_POST['posttitle']."\r\n";
        $post_data .= 'Post Url : '.$_POST['posturl'];
    }
    $message_body = str_replace('[post-info]', $post_data, $message_body);
    
    $wpcf7_data['body'] = $message_body;

    return $wpcf7_data;
}

function collectDataToNetsuite($mode) {
    global $wpdb;
    $netsuite_api_url = ot_get_option('netsuite_collect_data_url');
    //$netsuite_api_url = "https://forms.na1.netsuite.com/app/site/hosting/scriptlet.nl?script=612&deploy=1&compid=TSTDRV1276599&h=572a926b9f17ae9831bd";

    $param = array();
    $test_param = array();
    foreach($_POST as $key=> $val) {
        if(substr($key, 0, 6) == '_wpcf7') {
            continue;
        }
        if($key == '_wpnonce') {
            continue;
        }
        if($key == 'action') {
            continue;
        }
        if($key == 'vehicle_id') {
            $vehicle_id = $_POST['vehicle_id'];
            continue;
        }
        if(!empty($key)) {
            $param[$key] = stripcslashes(str_replace("-", " ", str_replace("&#x40;", "@",$val))); // remove "-" dash for netsuite problem
            if($key == "email") {
                $param[$key] = str_replace(" ", "", $param[$key]);
            }
            $test_param[$key] = $val;
        }
    }
	
    if(isset($_POST['coverage_options'])) {
        $param['vehicle_coverage'] = $_POST['coverage_options'];
        unset($param['coverage_options']);
    }    
    
    if(!$mode) { // ir contact form
        if(isset($param['subscribe'])) {
            $param['subscribe'] = ot_get_option('g_yes');
        } else {
            $param['subscribe'] = ot_get_option('g_no');
        }
    }
	
	// debug - start //
    $debug_sql = $wpdb->prepare("insert into wp_xpel_collect_data set data='%s', ip='".$_SERVER['REMOTE_ADDR']."', created=now()", serialize($test_param));
    $wpdb->query($debug_sql);
	print_r($debug_sql);
    // debug - end //
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$netsuite_api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_POST, 1);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $data_json = json_encode($param);
	pr