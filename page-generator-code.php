<?php
/**
 * * Plugin Name: Page Generator for Suburbs
 * * Plugin URI: https://www.cedricdesigns.com.au
 * * Description: This will add page generator functionality for several suburbs in Australia.
 * * Version: 1.0
 * Author: Cedric
 * Author URI: https://www.cedricdesigns.com.au
 **/
 
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

include("includes/constants.php");


function add_page_gen_code() {
    // Code here
    $initial_callback = 'page_gen_init';
    global $wpdb;
    global $db_page_gen_data;
    if ($wpdb->get_var("SHOW TABLES LIKE '$db_page_gen_data'") == $db_page_gen_data) {
        $initial_callback = 'page_gen_html';
    }
    
    $page_title = 'Add page gen code';
	$menu_title = 'Page Gen';
	$capatibily = 'manage_options';
	$slug = 'page-gen-code';
	$callback = $initial_callback;
	$icon = 'dashicons-schedule';
	$position = 60;

	add_menu_page($page_title, $menu_title, $capatibily, $slug, $callback, $icon, $position);
}
 
 
add_action( 'admin_menu', 'add_page_gen_code' );

function topbar_register_settings() {
    register_setting('topbar_option_group', 'page_gen_id');
}

add_action('admin_init', 'topbar_register_settings');

function page_gen_html() { 

global $wpdb;
global $db_page_gen_content;
global $db_page_gen_data;

// Check if content table exists
if ($wpdb->get_var("SHOW TABLES LIKE '$db_page_gen_content'") != $db_page_gen_content) {
    $result =  null;
    echo "Content table not found. Creating empty content...";
} else {
    print_r("Not found table!");
    $result = $wpdb->get_results("SELECT * FROM $db_page_gen_content");
}

$result_additional_data = $wpdb->get_results("SELECT * FROM $db_page_gen_data")[0];
?>

<style>
    table {
        width: 100%;
    }
    table, th, td {
      border: 1px solid black;
    }
    th {
        padding: 10px 10px 10px 40px;
        text-align: left;
    }
    textarea {
        width: 100%;
        max-width: 100%;
    }
</style>
<div class="wrap page-gen-wrapper">
    <div class='page-gen-confirm-box'>
        <?php
         if ((isset($_POST['page_title']) && isset($_POST['meta_description']) && isset($_POST['h1_title']) && isset($_POST['data']))) {
             ?>
             <div style='padding: 20px; background-color: rgba(164, 249, 200, 0.3)' class='updated'>
                 <p>Database updated.</p>
             </div>
             <?php
         } else if  (isset($_POST['analytics']) || isset($_POST['page_gen_code'])) {
              ?>
             <div style='padding: 20px; background-color: rgba(164, 249, 200, 0.3)' class='updated'>
                 <p>Page gen extra data updated.</p>
             </div>
             <?php
         }
        ?>
    </div>
    <h1>PAGE GEN PLUGIN</h1>
    
    <h2>Page gen content for suburbs here:</h2>
    <form method='post'>
        <table>
            <tr>
                <th>
                    <label for="page_title">Page title:</label>
                </th>
                <td>
                    <textarea id="page_title" name="page_title" rows="2"><?=$result[0]->page_title ?? '' ?></textarea>
                </td>
            </tr>
            
            <tr>
                <th>
                    <label for="meta_description">Meta Description:</label>
                </th>
                <td>
                    <textarea id="meta_description" name="meta_description" rows="5"><?=$result[0]->meta ?? '' ?></textarea>
                </td>
            </tr>
            
            <tr>
                <th>
                    <label for="meta_keywords">Meta Keywords</label>
                </th>
                <td>
                    <textarea id="meta_keywords" name="meta_keywords" rows="2"><?=$result[0]->keywords ?? '' ?></textarea>
                </td>
            </tr>
            
            <tr>
                <th>
                    <label for="h1_title">H1 title</label>
                </th>
                <td>
                    <textarea id="h1_title" name="h1_title" rows="2"><?=$result[0]->h1_title ?? '' ?></textarea>
                </td>
            </tr>
            
            <tr>
                <th>
                    <label for="data">Main Content:</label>
                </th>
                <td>
                    <?php
                        wp_editor($result[0]->data ?? '', 'data');
                    ?>
                </td>
            </tr>
        </table>
        
        <?php submit_button(); ?>
    </form>
    
    <h2>Extra data for page gen</h2>
    <form method='post'>
        <table align='left'>
            <tr>
                <th>Extra data</th>
            </tr>
            <tr>
                <th>
                    <label for="analytics">Analytics code:</label>
                </th>
                <td>
                    <textarea id="analytics" name="analytics" rows="10"><?php echo $result_additional_data->analytics; ?></textarea>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="page_gen_code">Page gen <code>.htaccess</code> code:</label>
                    <p>This the .htaccess code that rewrites URL into readable format.</p>
                </th>
                <td>
                    <textarea id="page_gen_code" name="page_gen_code" rows="20"><?php echo $result_additional_data->htaccess_code; ?></textarea>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="page_gen_url">Initial URL:</label>
                </th>
                <td>
                    <input id="page_gen_url" name="page_gen_url" value="<?=$_SERVER['HTTP_HOST']?>/<?=$result_additional_data->url?>" />
                </td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>
    
    <div style="padding: 20px; border: 1px solid red;">
        <h3 style="color: red;">Reset all data</h3>
        
        <form method="post">
            <input type="hidden" name="delete_page_gen" id="delete_page_gen" />
            Deletes page gen tables, htaccess code and page template.  <strong>Deleting is irreversible.</strong>
        <?php submit_button('Delete all data'); ?>
        </form>
        
    </div>
</div>
    
<?php }


// ALL STARTS HERE
// The first function that runs. Asks what page suburb URL the user wants

// TODO: Implements choice: All Suburbs (Default) / My suburbs (upload file)
function page_gen_init() {
    ?>
    <style>
        .section {
            padding: 30px 30px;
            margin: 20px 0;
            background-color: #fff;
        }
    </style>
    <div class="wrap page-gen-wrapper">
        
        <?php
        
        if (isset($_POST['delete_page_gen'])) {
            ?>
            <div style='padding: 20px; background-color: rgba(230, 57, 70, 0.3)' class='deleted'>
                 <p>Page gen data deleted.</p>
             </div>
            <?php
        }
        
        ?>
        
        <h1>Create your suburb pages.</h1>
        
        
        <form method='post'>
            <?php wp_nonce_field( 'page_gen_init_suburbs_form', 'page_gen_init_suburbs_form_nonce' ); ?>
            <input type="hidden" name="page_gen_init_suburbs_form" value="page_gen_init_suburbs_form">
            <div class="section">
            <h3>Upload suburbs database</h3>
            <input type="file" name="suburbsToUpload" id="suburbsToUpload" /><br><br>
            <em>Optional: If no file is provided, the default list with all suburbs in Australia will be used.</em>
        </div>
        <h2>Set an URL for suburbs pages</h2>
        <table>
            <tr>
                <td><strong>Permalink: </strong></td>
                <td><? echo $_SERVER['HTTP_HOST']; ?>/<input type="text" placeholder="areas/{{state}}/{{suburb}}" id="page_gen_code_init" name="page_gen_code_init" /></td>
                <td><?php submit_button(); ?></td>
            </tr>
        </table>
    
        
        </form>
        
        <strong>Note: </strong>
        <ul>
            <li>
                {{suburb}} tag will be replaced by the suburb name.
            </li>
            <li>
                {{state}} tag will be replaced by the state name. (optional) do not include if all suburbs belong to one state.
            </li>
        </ul>
    </div>
    
    <?php
}

function create_htaccess_code() {

    
    $init_page_gen_code = $_POST['page_gen_code_init'];
    
    
    $url_modified = str_replace('{{state}}', '%2', $init_page_gen_code);
    $url_modified = str_replace('{{suburb}}', '%1', $url_modified);
    $url_modified = str_replace('-', '\\\-', $url_modified);
    
    $url_modified2 = str_replace('{{state}}', '(.*)', $init_page_gen_code);
    $url_modified2 = str_replace('{{suburb}}', '(.*)', $url_modified2);
    $url_modified2 = str_replace('-', '\\\-', $url_modified2);
    
    $query_strings = "suburb.php?suburb=\$2&state=\$1";
    
    if (strpos($init_page_gen_code, "{{suburb}}") < strpos($init_page_gen_code, "{{state}}")) {
        $query_strings = "suburb.php?suburb=\$1&state=\$2";
    }
    
    $code = "
    <IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
    RewriteBase /
    # Page gen
    RewriteCond %{THE_REQUEST} /suburb.php\\\?suburb=([^\\\s]+)\&state=([^\\\s]+) [NC]
    RewriteRule ^.+$ /$url_modified? [L,R]
    RewriteRule ^$url_modified2$ $query_strings [L]
    </IfModule>
    ";
    
    // <IfModule mod_rewrite.c>
    // RewriteEngine On
    // RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
    // RewriteBase /
    // # Page gen
    // RewriteCond %{THE_REQUEST} /suburb.php\?suburb=([^\s]+)\&state=([^\s]+) [NC]
    // RewriteRule ^.+$ /sharps-clearances\-%1\-%2? [L,R]
    // RewriteRule ^sharps-clearances\-(.*)\-(.*)$ suburb.php?suburb=$1&state=$2 [L]
    // </IfModule>
    
    return $code;
}


function save_page_gen_inputs() {
    // [$page_title, $meta_description, $meta_keywords, $h1_title, $data] = $_POST;
    
    global $wpdb;
    global $db_page_gen_content;
    $table = $db_page_gen_content;
    
    // To use in future : id INT NOT NULL AUTO_INCREMENT,
    $sql_create = "CREATE TABLE $table (
        
        page_title TEXT,
        meta TEXT,
        keywords TEXT,
        h1_title VARCHAR(100),
        data LONGTEXT,
        id INT NOT NULL,
        PRIMARY KEY (id)
        )";
        
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_create);
    
    $data = array(
        'page_title' => '',
        'h1_title' => '',
        'data' => '',
        'meta' => '',
        'keywords' => '',
        'id' => 1
    );
    
    $wpdb->replace($table, $data, $format);
    
    // $data = array(
    //     'page_title' => $_POST['page_title'],
    //     'h1_title' => $_POST['h1_title'],
    //     'data' => $_POST['data'],
    //     'meta' => $_POST['meta_description'],
    //     'keywords' => $_POST['meta_keywords'],
    //     'id' => 1
    // );
    
    $page_title = $_POST['page_title'];
    $h1_title = $_POST['h1_title'];
        $data = $_POST['data'];
        $meta = $_POST['meta_description'];
        $keywords = $_POST['meta_keywords'];
    
    $sql_update = "UPDATE $db_page_gen_content SET page_title='$page_title', meta='$meta', keywords='$keywords', h1_title='$h1_title',data='$data' WHERE id=1";

    
    $wpdb->query($sql_update);
    
}


function save_page_gen_extra_data() {
    global $wpdb;
    global $db_page_gen_data;
    global $db_all_suburbs;
    $table = $db_page_gen_data;
    
    $get_extra_data = $wpdb->get_results("SELECT * FROM $db_page_gen_data")[0];
    
    // Creating or updating table
    $sql_create = "CREATE TABLE $table (
        analytics TEXT,
        url VARCHAR(100),
        htaccess_code TEXT,
        suburbs_file VARCHAR(50),
        id INT NOT NULL,
        PRIMARY KEY (id)
        )";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_create);
    
    // Inserting initial data
    $data = array(
        'analytics' => '',
        'url' => $_POST['page_gen_code_init'],
        'htaccess_code' => '',
        'suburbs_file' => $db_all_suburbs,
        'id' => 1
    );
    $wpdb->replace($table, $data);
    
    $htaccess_code = $_POST['page_gen_code'];
    $page_gen_url = $_POST['page_gen_code_init'];
    
    if (isset($_POST['page_gen_code_init'])) {
        $htaccess_code = create_htaccess_code($_POST['page_gen_code_init']);
        $page_gen_url = $_POST['page_gen_code_init'];
    } else {
        $htaccess_code = $get_extra_data->htaccess_code;
        $page_gen_url = $get_extra_data->url;
    }
    
    $analytics = $_POST['analytics'];
    $page_gen_htaccess_code = $htaccess_code;
    $url = $_SERVER['HTTP_HOST'];
    
    $sql_update = "UPDATE $db_page_gen_data SET analytics='$analytics', htaccess_code='$page_gen_htaccess_code', url='$page_gen_url' WHERE id=1";
    $wpdb->query($sql_update);
    
    
}


function insert_into_htaccess() {
    $file = ABSPATH . ".htaccess";
    $lines = $_POST['page_gen_id'];
    
    $code = stripslashes($_POST['page_gen_code']);
    
    global $wpdb;
    global $db_page_gen_data;
    $code = $wpdb->get_results("SELECT htaccess_code FROM $db_page_gen_data")[0];
    
    if (!strpos(file_get_contents( ABSPATH . '.htaccess' ), "Page gen code")) {
        $str  = "# BEGIN Page gen code\n " . $code->htaccess_code .  "\n# END Page gen code\n";
        $str .= file_get_contents( ABSPATH . '.htaccess' );
        file_put_contents( ABSPATH . '.htaccess', $str );
    }
    
    // // Old code, inserts only after the Wordpress lines in .htaccess file, which is not enough to make the page gen work.
    return insert_with_markers($file, "Page gen code", $code->htaccess_code);
}

function move_suburb_page() {
    require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
    require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';

    $wpsf = new WP_Filesystem_Direct(true);
    $wpsf->copy($wpsf->wp_content_dir().'plugins/page-generator-code/suburb.php', ABSPATH.'suburb.php');
}


function save_to_database() {
    global $wpdb;
    global $db_all_suburbs;
    global $db_all_states;
    require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
    require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    // Read from CSV
    $wpsf = new WP_Filesystem_Direct(true);
    $get_all_suburbs = $wpsf->get_contents($wpsf->wp_content_dir().'plugins/page-generator-code/'.$db_all_suburbs.'.sql');
    $get_all_states = $wpsf->get_contents($wpsf->wp_content_dir().'plugins/page-generator-code/'.$db_all_states.'.sql');
    
    dbDelta($get_all_suburbs);
    dbDelta($get_all_states);
    
}

function copy_suburbs_pagelist_template() {
    require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
    require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';

    $wpsf = new WP_Filesystem_Direct(true);
    $wpsf->copy($wpsf->wp_content_dir().'plugins/page-generator-code/page_with_suburbs_list.php', get_template_directory() . '/page_with_suburbs_list.php');
}


// Old function that duplicates the regular page template and add code to list all suburbs, replaced with just copying file above
/**
function create_page_template_for_suburb_list() {
    // Duplicate page in /wp-content/themes/<theme>/100-width.php
    // If there is a child theme (for Avada for example, duplicate it to Avada child)
    require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
    require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';

    $wpsf = new WP_Filesystem_Direct(true);
    
    $new_template_file = get_template_directory() . '/page_with_suburbs_list.php';
    
    if (wp_get_theme()->Name === 'Avada') {
        $wpsf->copy(get_template_directory() . '/100-width.php', $new_template_file);
    } else if (wp_get_theme()->Name === 'Avada-Child-Theme') {
        $wpsf->copy(get_theme_root() . '/Avada/100-width.php', z);
    }
    
    // Replace page template name: Template Name: 100$ Width
    $new_template_file_contents = $wpsf->get_contents($new_template_file);
    $new_template_file_contents = str_replace('Template Name: 100% Width', 'Template Name: Page Template with List of Suburbs', $new_template_file_contents);
    $new_template_file_contents = str_replace('<?php the_content(); ?>
				<?php fusion_link_pages(); ?>', '<?php the_content(); ?>'. 'test' .'<?php fusion_link_pages(); ?>', $new_template_file_contents);

    file_put_contents($new_template_file , $new_template_file_contents );
    
    
    // Add code under <div class='post-content'> : Database query and list
    
    
}

**/

function delete_all_page_gen_data() {
    global $wpdb, $db_all_suburbs, $db_all_states, $db_page_gen_data, $db_page_gen_content;
    
    // Delete page gen tables from database
    $wpdb->query("DROP TABLE " . $db_all_suburbs);
    $wpdb->query("DROP TABLE " . $db_page_gen_content);
    $wpdb->query("DROP TABLE " . $db_page_gen_data);
    $wpdb->query("DROP TABLE " . $db_all_states);
    
    // Delete suburb.php page gen page template
    require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
    require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
    $wpsf = new WP_Filesystem_Direct(true);
    $wpsf->delete(ABSPATH.'suburb.php');
    $wpsf->delete(get_template_directory() . '/page_with_suburbs_list.php');
    
}

function create_table_from_csv() {
    global $wpdb;
    global $db_page_gen_data;
    $charset_collate = $wpdb->get_charset_collate();
    
    $tablename = $db_page_gen_data;
    
    $sql = "CREATE TABLE $tablename (
        id int(11) NOT NULL AUTO_INCREMENT,
        title varchar(24) NOT NULL,
        filename varchar(39) NOT NULL,
        keyword varchar(1) NOT NULL,
        PRIMARY KEY (id)
        ) $charset_collate;";
        
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}


if (isset($_POST['page_gen_id'])) {
    add_action("admin_init", "insert_into_htaccess");
}

else if (isset($_POST['page_title']) && isset($_POST['meta_description']) && isset($_POST['h1_title']) && isset($_POST['data'])) {
    add_action("admin_init", "save_page_gen_inputs");
    copy_suburbs_pagelist_template();
}

else if (isset($_POST['analytics']) || isset($_POST['page_gen_code'])) {
    add_action("admin_init", "save_page_gen_extra_data");
    add_action("admin_init", "insert_into_htaccess");
}

else if (isset($_POST['page_gen_code_init'])) {
    add_action("admin_init", "save_page_gen_extra_data");
    add_action("admin_init", "insert_into_htaccess");
    move_suburb_page();
    save_to_database();
    header("Refresh:0");
}

else if (isset($_POST['delete_page_gen'])) {
    delete_all_page_gen_data();
    add_action("admin_init", "insert_into_htaccess");
    header("Refresh:1");
}


