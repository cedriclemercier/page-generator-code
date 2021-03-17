<?php

include("wp-content/plugins/page-generator-code/includes/constants.php");

// print_r($db_all_suburbs);

// Get the query strings for suburb and state
$suburb_query = htmlspecialchars($_GET["suburb"]);
$state_query = htmlspecialchars($_GET["state"]);


// Load Wordpress features
if ( ! isset( $wp_did_header ) ) {

	$wp_did_header = true;

	// Load the WordPress library.
	require_once __DIR__ . '/wp-load.php';

	// Set up the WordPress query.
	wp();

	// Load the theme template.
	require_once ABSPATH . WPINC . '/template-loader.php';

    }
    
// Set useful arrays
$state_from_slug = ["nsw" => "New South Wales", "vic" => "Victoria", "qld" => "Queensland", "wa" => "Western Australia", "sa" => "South Australia", "nt" => "Northern Territory", "act" => "Capital Territory", "tas" => "Tasmania"];
$state_to_num = ["nsw" => "3", "vic" => "8", "qld" => "5", "wa" => "9", "sa" => "6", "nt" => "4", "act" => "2", "tas" => "7"];
$state_from_num = ["3" => "New South Wales", "8" => "Victoria", "5" => "Queensland", "9" => "Western Australia", "6" => "South Australia", "4" => "Northern Territory", "2" => "Capital Territory", "7" => "Tasmania"];

// Query all data from database: Suburbs list, page gen extra data and content
$get_content = $wpdb->get_results("SELECT * FROM ". $db_page_gen_content)[0];
$get_analytics = $wpdb->get_row("SELECT analytics FROM " . $db_page_gen_data);
$get_suburbs_file = $wpdb->get_results("SELECT suburbs_file FROM " . $db_page_gen_data)[0];
$get_suburb = $wpdb->get_row("SELECT * FROM " . $get_suburbs_file->suburbs_file ." WHERE cat = '$state_to_num[$state_query]' AND filename='$suburb_query' ");
$suburb = $get_suburb->title;
// Get full state name
$state = $state_from_slug[$state_query];
    
// Set page title hook, with high priority argument
add_filter('pre_get_document_title', 'set_title', 9999);

// Remove noindex by default and add index,follow to robots meta tag
add_filter('wpseo_robots', 'yoast_no_home_noindex', 999);

// Set meta tags in header
add_filter('wp_head', 'set_meta', 2);


// Page setup functions
function yoast_no_home_noindex($string= "") {
    $string= "index,follow";
    return $string;
}

function set_title($title) {
    global $wpdb;
    global $suburb;
    global $state;
    global $state_query;
    global $db_page_gen_content;
    $get_title = $wpdb->get_results("SELECT page_title FROM " . $db_page_gen_content)[0];
    
    // FOR TESTING
    // $get_title = (object) ['page_title' => 'Sharps First Australia'];
    
    $title = prepare_suburb_data($get_title->page_title, $suburb, $state, strtoupper($state_query));
    return $title;
}


function set_meta() {
    global $wpdb;
    global $suburb;
    global $state;
    global $state_query;
    global $db_page_gen_content;
    $get_content = $wpdb->get_results("SELECT page_title, meta, keywords FROM " . $db_page_gen_content)[0];
    
    // FOR TESTING
    // $get_content = (object) ['page_title' => 'Australia', 'h1_title' => 'Main title here', 'meta' => 'Meta data here', 'keywords' => 'Keywords here', 'data' => 'Page content here'];
    
    $meta_desc = prepare_suburb_data($get_content->meta, $suburb, $state, strtoupper($state_query));
    $meta_keywords = prepare_suburb_data($get_content->keywords, $suburb, $state, strtoupper($state_query));
    remove_action('wp_head', 'rel_canonical');
    echo "<meta name='description' content='$meta_desc'>";
    echo "<meta name='keywords' content='$meta_keywords'>";
    echo "<link rel='canonical' href='https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."'>";
    echo "<meta property='og:type' content='website' />";
    echo "<meta property='og:description' content='$meta_desc' />";
    echo "<meta property='og:url' content='https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."' />";
    
    // For images
    // echo "<meta property='og:image' content='https://crimescenecleanups.com.au/wp-content/uploads/2020/10/ALL_LOGOS_WHITE_BIOHAZARD-e1603258257294.png' />";
}

// Function to replace {{suburb}} and {{state}} placeholders
function prepare_suburb_data($text, $replace_suburb, $replace_state, $replace_acronym) {
    $new_content = str_replace('{{suburb}}', $replace_suburb, $text);
    $new_content = str_replace('{{state}}', $replace_state, $new_content);
    $new_content = str_replace('{{STATE}}', $replace_acronym, $new_content);
    return $new_content;
}

// TODO redirects to homepage if arguments are wrong
// if ($get_suburb != null) {
//     header("Location: index.php");
// }

// Ensures the page result is successful if suburb data is found
header("HTTP/2.0 200 OK");

?>

<?php
/**
 * Template used for pages.
 *
 * @package Avada
 * @subpackage Templates
 */

// Do not allow directly accessing this file.
// if ( ! defined( 'ABSPATH' ) ) {
// 	exit( 'Direct script access denied.' );
// }
?>
<!DOCTYPE html>
<html class="<?php avada_the_html_class(); ?>" <?php language_attributes(); ?>>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<?php Avada()->head->the_viewport(); ?>

	<?php wp_head(); ?>
	

	<?php
	/**
	 * The setting below is not sanitized.
	 * In order to be able to take advantage of this,
	 * a user would have to gain access to the database
	 * in which case this is the least of your worries.
	 */
	echo apply_filters( 'avada_space_head', Avada()->settings->get( 'space_head' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
	?>
</head>

<?php
$object_id      = get_queried_object_id();
$c_page_id      = Avada()->fusion_library->get_page_id();
$wrapper_class  = 'fusion-wrapper';
$wrapper_class .= ( is_page_template( 'blank.php' ) ) ? ' wrapper_blank' : '';
?>
<body <?php body_class(); ?> <?php fusion_element_attributes( 'body' ); ?>>
	<?php do_action( 'avada_before_body_content' ); ?>
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'Avada' ); ?></a>

	<div id="boxed-wrapper">
		<div class="fusion-sides-frame"></div>
		<div id="wrapper" class="<?php echo esc_attr( $wrapper_class ); ?>">
			<div id="home" style="position:relative;top:-1px;"></div>
			<?php if ( has_action( 'avada_render_header' ) ) : ?>
				<?php do_action( 'avada_render_header' ); ?>
			<?php else : ?>

				<?php avada_header_template( 'below', ( is_archive() || Avada_Helper::bbp_is_topic_tag() ) && ! ( class_exists( 'WooCommerce' ) && is_shop() ) ); ?>
				<?php if ( 'left' === fusion_get_option( 'header_position' ) || 'right' === fusion_get_option( 'header_position' ) ) : ?>
					<?php avada_side_header(); ?>
				<?php endif; ?>

				<?php avada_sliders_container(); ?>

				<?php avada_header_template( 'above', ( is_archive() || Avada_Helper::bbp_is_topic_tag() ) && ! ( class_exists( 'WooCommerce' ) && is_shop() ) ); ?>

			<?php endif; ?>

			<div class="avada-page-titlebar-wrapper">
                <div class="fusion-page-title-bar fusion-page-title-bar-breadcrumbs fusion-page-title-bar-left">
                    <div class="fusion-page-title-row">
                        <div class="fusion-page-title-wrapper">
                            <div class="fusion-page-title-captions">
                                <h1 class="entry-title fusion-responsive-typography-calculated" style="--fontSize: 46;" data-fontsize="46" data-lineheight="normal"><?php echo prepare_suburb_data($get_content->h1_title, $suburb, $state, strtoupper($state_query)); ?></h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

			<?php
			$row_css    = '';
			$main_class = '';

			if ( apply_filters( 'fusion_is_hundred_percent_template', false, $c_page_id ) ) {
				$row_css    = 'max-width:100%;';
				$main_class = 'width-100';
			}

			if ( fusion_get_option( 'content_bg_full' ) && 'no' !== fusion_get_option( 'content_bg_full' ) ) {
				$main_class .= ' full-bg';
			}
			do_action( 'avada_before_main_container' );
			?>
			<main id="main" class="clearfix <?php echo esc_attr( $main_class ); ?>">
				<div class="fusion-row" style="<?php echo esc_attr( $row_css ); ?>">
				    
				    <section id="content" <?php Avada()->layout->add_style( 'content_style' ); ?>>
                    		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    			<?php echo fusion_render_rich_snippets_for_pages(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				    <div class="post-content">
				        <?php 
				        echo prepare_suburb_data($get_content->data, $suburb, $state, strtoupper($state_query));
				        ?>
				        </div>
				        <?php if ( ! post_password_required( $post->ID ) ) : ?>
                    				<?php do_action( 'avada_before_additional_page_content' ); ?>
                    				<?php if ( class_exists( 'WooCommerce' ) ) : ?>
                    					<?php $woo_thanks_page_id = get_option( 'woocommerce_thanks_page_id' ); ?>
                    					<?php $is_woo_thanks_page = ( ! get_option( 'woocommerce_thanks_page_id' ) ) ? false : is_page( get_option( 'woocommerce_thanks_page_id' ) ); ?>
                    					<?php if ( Avada()->settings->get( 'comments_pages' ) && ! is_cart() && ! is_checkout() && ! is_account_page() && ! $is_woo_thanks_page ) : ?>
                    						<?php comments_template(); ?>
                    					<?php endif; ?>
                    				<?php else : ?>
                    					<?php if ( Avada()->settings->get( 'comments_pages' ) ) : ?>
                    						<?php comments_template(); ?>
                    					<?php endif; ?>
                    				<?php endif; ?>
                    				<?php do_action( 'avada_after_additional_page_content' ); ?>
                    			<?php endif; // Password check. ?>
                    		</div>
				        </section>
<?php do_action( 'avada_after_content' ); ?>
<?php get_footer(); ?>