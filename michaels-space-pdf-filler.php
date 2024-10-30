<?php

/**
 * @package mmpdff
 */

/**
 * 
 * Plugin Name: Michaels Space PDF Filler
 * Description: Create Forms that send filled pdfs!
 * Version: 1.2.0
 * Author: Michaels Space
 * Author URI: https://www.michaelsspace.com/
 * License: GPLv2 or later
 * Test Domain: mmpdff
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

require __DIR__.'/vendor/autoload.php';

class mmpdffClass
{ // class wrapper

    function __construct()
    {
        add_action('init', array($this, 'mmpdff_custom_post_type'));
        add_action( 'init', array($this,'mmpdff_init_internal') );
        add_action('add_meta_boxes', array($this, 'mmpdff_meta_box'));
        add_action('save_post', array($this, 'save_mmpdff_meta_box_data'));
        add_action( 'parse_request', array($this,'mmpdff_parse_request') );
        add_action("admin_init", array($this,"pdff_settings"));
        add_action("admin_menu", array($this,"menu_item"));

        add_shortcode('mmpdff', array($this, 'mmpdff_shortcode'));
        add_shortcode('mmpdff_embed', array($this, 'mmpdff_embed_shortcode'));

        add_filter( 'query_vars', array($this,'mmpdff_query_vars') );
		add_filter( 'wp_mail_content_type', array($this,'mmpdff_set_content_type') );
        add_filter('plugin_action_links_'. plugin_basename(__FILE__), array($this,'pdff_settings_link') );
        
    }
    

    /*
    * Creating a function to create the PDF Forms post type
    */

    function mmpdff_custom_post_type()
    {

        $labels = array(
            'name'                => _x('PDF Forms', 'Post Type General Name', 'mmpdff'),
            'singular_name'       => _x('PDF Form', 'Post Type Singular Name', 'mmpdff'),
            'menu_name'           => __('PDF Forms', 'mmpdff'),
            'parent_item_colon'   => __('Parent Form', 'mmpdff'),
            'all_items'           => __('All PDF Forms', 'mmpdff'),
            'view_item'           => __('View PDF Form', 'mmpdff'),
            'add_new_item'        => __('Add New PDF Form', 'mmpdff'),
            'add_new'             => __('Add New', 'mmpdff'),
            'edit_item'           => __('Edit PDF Form', 'mmpdff'),
            'update_item'         => __('Update PDF Form', 'mmpdff'),
            'search_items'        => __('Search PDF Forms', 'mmpdff'),
            'not_found'           => __('Not Found', 'mmpdff'),
            'not_found_in_trash'  => __('Not found in Trash', 'mmpdff'),
        );

        $args = array(
            'label'               => __('mm-pdf-forms', 'mmpdff'),
            'description'         => __('PDF Forms', 'mmpdff'),
            'labels'              => $labels,
            'supports'            => array('title'),
            'taxonomies'          => array(),
            'hierarchical'        => false,
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => 5,
            'can_export'          => true,
            'has_archive'         => false,
            'exclude_from_search' => true,
            'publicly_queryable'  => false,
            'capability_type'     => 'post',
            'show_in_rest' => false,

        );

        register_post_type('mm-pdf-forms', $args);
    }

    /*
    * Creating a meta box for the custom post type
    */

    function mmpdff_meta_box()
    {

        add_meta_box(
            'mm-pdf-form',
            __('PDF Form', 'mmpdff'),
            array($this, 'mmpdff_notice_meta_box_callback'),
            'mm-pdf-forms',
            'advanced'
        );
    }

    /*
    * checking the nonce on the meta box/ showing admin form
    */

    function mmpdff_notice_meta_box_callback($post)
    {
        

        // Add a nonce field so we can check for it later.
        wp_nonce_field('mmpdff_nonce', 'mmpdff_nonce');

        ?>


    <?php

        $value = get_post_meta($post->ID, '_mm_pdf_form', true);

        $to = get_post_meta($post->ID, '_mm_pdf_form_to', true);

        $formData = get_post_meta($post->ID, 'mm_pdf_form_data', true);
        
        // _e('Number Of Inputs: <input type="number" id="mm_pdf_form" name="mm_pdf_form" value="' . esc_attr($value) . '">', 'mmpdff'); 


        // _e('<br />', 'mmpdff'); 

        // _e('<br />', 'mmpdff'); 
        	
		// if($value){
		// 	 for ($i = 0; $i < $value; $i++) {

		// 		$input_value = get_post_meta($post->ID, '_mm_pdf_form_' . $i, true);

		// 		echo '<br/>';
		// 		_e( 'Input Name: <input type="text" id="mm_pdf_form_' . $i . '" name="mm_pdf_form_' . $i . '" value="' . esc_attr($input_value) . '"> (Pdf Fillable Form Input Name: '. str_replace(' ', '-', strtolower(esc_attr($input_value))) .')', 'mmpdff');
		// 		echo '<br/>';
		// 	}
			
		// }


        ?>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
            <script src="https://formbuilder.online/assets/js/form-builder.min.js"></script>

            <div id="form-wrap"></div>

            <style>
            #postbox-container-1{
                display: none !important;
            }

            #postbox-container-2, #post-body-content{
                width: 86vw !important; 
            }

            </style>
        

            <script>
            jQuery(function($) {

                var options = {
                    disabledActionButtons: ['data'],
                    disableFields: ['autocomplete', 'file', 'upload', 'button', 'select', 'radio-group', 'hidden', 'checkbox-group', 'number'],
                    onSave: function(evt, formData) {
                        document.getElementById('mm_pdf_form_data').value = formBuilder.actions.getData('json');
                        document.getElementById("post").submit();
                    }
                };

                var fbEditor = document.getElementById('form-wrap');
                var formBuilder = $(fbEditor).formBuilder(options);
                var formData = '<?php echo $formData; ?>';

                setTimeout(function(){ 
                    formBuilder.actions.setData(formData); 
                }, 500);


            });
            </script>

        <?php

         
        echo '<br/>';
        echo '<hr/>';
        echo '<br/>';

        _e('Send Form To: <input type="email" id="mm_pdf_form_to" name="mm_pdf_form_to" value="' . esc_attr($to) .'">', 'mmpdff'); 

        echo '<input type="hidden" id="mm_pdf_form_data" name="mm_pdf_form_data" value="' . $formData .'">'; 

        _e( '<h4 style="text-align:center;">Shortcode: [mmpdff id="'. $post->ID .'"]</h4>', 'mmpdff');

        _e( '<h4 style="text-align:center;">Embed Shortcode: [mmpdff_embed id="'. $post->ID .'"]</h4>', 'mmpdff');

        _e('<p style="text-align:center;">If the users broswer does not support PDF Embed, it will show a download link instead</p>', 'mmpdff');

        _e( '<h2 style="text-align:center;"> Hitting Update Will Save Over Any Already Created/Uploaded!</h2>', 'mmpdff');

        _e( '<h4 style="text-align:center;"> If you already have a pdf upload it to \plugins\michaels-space-pdf-filler\pdf\post-pdfs\</h4>', 'mmpdff');

        _e( '<h4 style="text-align:center;">You will need to name it: '. $post->ID . '-custom </h4>', 'mmpdff');

           
    }

    /*
    * Saving the metabox inputs
    */

    function save_mmpdff_meta_box_data($post_id)
    {

        // Check if our nonce is set.
        if (!isset($_POST['mmpdff_nonce'])) {
            return;
        }

        // Verify that the nonce is valid.
        if (!wp_verify_nonce($_POST['mmpdff_nonce'], 'mmpdff_nonce')) {
            return;
        }

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check the user's permissions.
        if (isset($_POST['post_type']) && 'page' == $_POST['post_type']) {

            if (!current_user_can('edit_page', $post_id)) {
                return;
            }
        } else {

            if (!current_user_can('edit_post', $post_id)) {
                return;
            }
        }

        /* OK, it's safe for us to save the data now. */

        // Make sure that it is set.
        if (!isset($_POST['mm_pdf_form_data'])) {
            return;
        }

        // Sanitize user input.
        // $input_number = sanitize_text_field($_POST['mm_pdf_form']);

        $to = sanitize_email($_POST['mm_pdf_form_to']);

        $formData = $_POST['mm_pdf_form_data'];

        // echo $input_number;

        echo '<br>';

        echo $to;

        echo '<br>';

        echo $formData;

        update_post_meta($post_id, 'mm_pdf_form_data', $formData);
        update_post_meta($post_id, '_mm_pdf_form_to', $to);
		
		// if($input_number){
			
		// 	// Update the meta field in the database.
        //     update_post_meta($post_id, '_mm_pdf_form', $input_number);
            
        //     update_post_meta($post_id, '_mm_pdf_form_to', $to);

        //     $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        //     $pdf->AddPage();


        //     $pdf->setFormDefaultProp(array('lineWidth'=>1, 'borderStyle'=>'solid', 'fillColor'=>array(255, 255, 200), 'strokeColor'=>array(255, 128, 128)));

		// 	for ($i = 0; $i < $input_number; $i++) {
				
		// 		if(isset($_POST['mm_pdf_form_' . $i])){
					
					
		// 			$my_data = sanitize_text_field($_POST['mm_pdf_form_' . $i]);

        //             update_post_meta($post_id, '_mm_pdf_form_' . $i, $my_data);

        //             $pdf->Cell(35, 5, $my_data);

        //             $my_data = str_replace(' ', '-', $my_data);

        //             $pdf->TextField(strtolower($my_data), 50, 5);
        //             $pdf->Ln(6);

		// 		}

        //     }
 
    
        //     $file_url = __DIR__ . '/pdf/post-pdfs/'. $post_id .'-mmpdff.pdf';
    
        //     //Close and output PDF document
        //     $pdf->Output( $file_url , 'F');

        // }
        

    }

    /*
    * Making sure all emails are sent with the text/html content type
    */

    function mmpdff_set_content_type(){
    	return "text/html";
    }
    
    /*
    * Setup route for pdf creation/email 
    */
    
    function mmpdff_init_internal()
    {
        add_rewrite_rule( 'mmpdff_api.php$', 'index.php?mmpdff_api=1', 'top' );
    }

    function mmpdff_query_vars( $query_vars )
    {
        $query_vars[] = 'mmpdff_api';
        return $query_vars;
    }
    
    function mmpdff_parse_request( &$wp )
    {
        if ( array_key_exists( 'mmpdff_api', $wp->query_vars ) ) {
            include 'mmpdff_api.php';
            exit();
        }
        return;
    }
    

    /*
    * Create Shortcode
    */

    function mmpdff_shortcode($atts = array())
    {

        // set up default parameters
        extract(shortcode_atts(array(
            'id' => '1'
        ), $atts));

        include( plugin_dir_path( __FILE__ ) . 'template/form.php');

        return $html;
    }

    function mmpdff_embed_shortcode($atts = array())
    {

        // set up default parameters
        extract(shortcode_atts(array(
            'id' => '1',
            'height' => '150',
            'width' => '1350',
            'text' => 'Download: ',
            'link_text' => 'Pdf File',
        ), $atts));

        $input_number = get_post_meta( $id, '_mm_pdf_form', true);

        $html = '
        <object class="mmpdff__embed" data="/wp-content/plugins/michaels-space-pdf-filler/pdf/post-pdfs/'. $id .'-mmpdff.pdf" type="application/pdf" style="width: '. $width .'px; height: '. $height .'px;">
            <p class="mmpdff__embed__p">'. $text .'<a target="blank" href="/wp-content/plugins/michaels-space-pdf-filler/pdf/post-pdfs/'. $id .'-mmpdff.pdf">'. $link_text .'</a><p>
        </object>';

        return $html;
    }

    /*
    * adding settings menu
    */

    function pdff_settings()
    {
        add_settings_section("recaptcha-section", "Recaptcha Type", null, "pdff-settings");
        
        register_setting("recaptcha-section", "pdff-captcha-select");

        register_setting("recaptcha-section", "pdff-site-key");

        register_setting("recaptcha-section", "pdff-secret-key");

    }

    function pdff_page()
    {
    ?>
        <div class="wrap">
            <h1>MS PDF Form Settings</h1>
    
            <form method="post" action="options.php">

                <?php settings_fields("recaptcha-section"); ?>

                 <select name="pdff-captcha-select">
                    <option value="none" <?php selected(get_option('pdff-captcha-select'), "none"); ?>>None</option>
                    <option value="reCAPTCHA" <?php selected(get_option('pdff-captcha-select'), "reCAPTCHA"); ?>>Google reCAPTCHA</option>
                    <option value="hCaptcha" <?php selected(get_option('pdff-captcha-select'), "hCaptcha"); ?>>hCaptcha</option>
                </select>

                 <p>Site Key</p>
                <input name="pdff-site-key" value="<?php echo get_option('pdff-site-key'); ?>">

                <p>Secret Key</p>
                <input name="pdff-secret-key" value="<?php echo get_option('pdff-secret-key'); ?>">

                <?php submit_button(); ?>

            </form>

        </div>
    <?php
    }

    function menu_item()
    {
        add_submenu_page("options-general.php", "MS PDF Form Settings", "MS PDF Form Settings", "manage_options", "pdff-settings", array($this,"pdff_page"));
    }

    /*
    * Creating Settings Link 
    */

    function pdff_settings_link($links) { 
        $settings_link = '<a href="options-general.php?page=pdff-settings">Settings</a>'; 
        array_unshift($links, $settings_link); 
        return $links; 
    }
    
    

}

if (class_exists('mmpdffClass')) {
    $mmpdffClass = new mmpdffClass();
}