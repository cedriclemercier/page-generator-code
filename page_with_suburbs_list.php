<?php
/**
 * Template Name: Page Template with List of Suburbs
 * A full-width template.
 *
 * @package Avada
 * @subpackage Templates
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}
?>
<?php get_header(); ?>
<style>
    .suburbs-list {
        float:left; 
        width: 21%;
        margin-right: 20px;
        list-style: none;
    }
    
    .suburbs-index {
        color: #CD2122;
        border: 1px solid #CD2122;
        border-radius: 2px;
        padding: 4px 8px;
        margin: 0 2px 4px 0;
    }
    
    @media only screen and (max-width: 767px) {
        .suburbs-list {
            width: 50%;
        }
    }
    
    @media only screen and (max-width: 419px) {
        .suburbs-list {
            width: 100%;
        }
    }
</style>
<section id="content" class="full-width">
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php echo fusion_render_rich_snippets_for_pages(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php avada_singular_featured_image(); ?>
			<div class="post-content">
				<?php the_content(); ?>
				
				<?php
				
				include(ABSPATH . 'wp-content/plugins/page-generator-code/includes/constants.php');
    
                $state_to_num = ["nsw" => "3", "vic" => "8", "qld" => "5", "wa" => "9", "sa" => "6", "nt" => "4", "act" => "2", "tas" => "7"];
                $state_from_acronym = ["nsw" => "New South Wales", "vic" => "Victoria", "qld" => "Queensland", "wa" => "Western Australia", "sa" => "South Australia", "nt" => "Northern Territory", "act" => "Australian Capital Territory", "tas" => "Tasmania"];
                
                $result_suburbs = $wpdb->get_results("SELECT * FROM $db_all_suburbs");
                $result_states = $wpdb->get_results("SELECT * FROM $db_all_states");
                $result_extra_data = $wpdb->get_results("SELECT url FROM $db_page_gen_data")[0];
                $result_content = $wpdb->get_results("SELECT h1_title FROM $db_page_gen_content")[0];
                
                function replace_names($suburb, $state, $text) {
                    $new_text = str_replace("{{suburb}}", $suburb, $text);
                    $new_text = str_replace("{{state}}", $state, $new_text);
                    return $new_text;
                }
                
                ?>
                <h2>All Our Suburbs Serviced</h2>
                <?php
                foreach (range('A', 'Z') as $elements)
                {
                ?>
                <div style="margin: 2px 4px 10px 4px; display: inline-block;"><a href="#<?php echo $elements; ?>" class="suburbs-index"><?php echo $elements ?></a></div>
                <?php
                }
                
                $index = 0;
                $cur = 0;
                
                foreach ($result_states as $values) {
                    $select_state = $wpdb->get_results("SELECT * FROM $db_all_suburbs WHERE cat='$values->id'");
                    
                    ?>
                    <h3><?php echo replace_names("", "", $result_content->h1_title); ?> <?=$state_from_acronym[strtolower($values->name)] . " | " . $values->name ?></h3>
                    <?php
                    
                    foreach (range('A', 'Z') as $elements)
                {
                
                    $array_grouped = [];
                    foreach ($select_state as $row)
                    {
                        if ($row->keyword === strtolower($elements))
                        {
                            array_push($array_grouped, $row);
                        }
                    }
                
                    if (count($array_grouped) !== 0)
                    {
                
                        $suburb_per_column = round(count($array_grouped) / 4);
                        $cur_letter = $elements;
                        $cur_letter_lower = strtolower($elements);
                
                        echo "<div class='fusion-builder-row fusion-row' style='padding-bottom: 50px;'>";
                        echo "<h3 id='$cur_letter'>$cur_letter</h3><hr><div style='margin-bottom:30px;'></div>";
                
                        for ($j = 0;$j < 4;$j++)
                        {
                
                            for ($i = 0;$i <= $suburb_per_column;$i++)
                            {
                
                                $sub_name = $array_grouped[$index]->title;
                                $slug = $array_grouped[$index]->filename;
                                ?>
                                
                                <li class='suburbs-list'><a href="https://<?=$_SERVER['HTTP_HOST']?>/<?=replace_names($slug,strtolower($values->name),$result_extra_data->url)?>" style="font-size:14px;"><?php echo $sub_name; ?></a></li> 
                                
                                <?php
                                
                                $index++;
                
                            }
                
                        }
                
                        $index = 0;
                
                        echo "</div>";
                
                    }
                
                }
                    
                }
				
				?>
				<?php fusion_link_pages(); ?>
			</div>
			<?php if ( ! post_password_required( $post->ID ) ) : ?>
				<?php if ( Avada()->settings->get( 'comments_pages' ) ) : ?>
					<?php comments_template(); ?>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	<?php endwhile; ?>
</section>
<?php get_footer(); ?>
