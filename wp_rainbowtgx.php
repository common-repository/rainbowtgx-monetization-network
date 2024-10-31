<?php
/*
Plugin Name: RainbowTgx Monetization Network
Plugin URI: http://www.rainbowtgx.com
Description: WordPress plugin to allow an easier integration of RainbowTgx on sites running WordPress.
Version: 1.2.1
Author: rtgx team
Author URI: http://www.rainbowtgx.com

Copyright 2015  RainbowTgx  (email : delivery@rainbowtgx.com)

*/


/*
 * Settings
 */
define( "key_rtgx_cid", "rtgx_cid", true );
define( "key_rtgx_custom_script", "rtgx_custom_script", true );

define( "rtgx_default_cid", "", true );
define( "rtgx_default_custom_script", "", true );

add_option( key_rtgx_cid, rtgx_default_cid, __("RainbowTgx Customer ID to use") );
add_option( key_rtgx_place_sidebar, rtgx_default_place_sidebar, __("on") );
add_option( key_rtgx_place_undercontent, rtgx_default_place_undercontent, __("on") );
add_option( key_rtgx_place_beforecontent, rtgx_default_place_beforecontent, __("") );
add_option( key_rtgx_custom_script, rtgx_default_custom_script, __("RainbowTgx custom script") );


/* Sanitization function for checkbox */
function sanitize_checkbox( $value ) {

		return $value ? 'on' : '';
	}
	
function validate_cid( $value ) {

		if (strlen($value) == 36) {
			return $value;
		} else { return '';};
	}	


/*Add RainbowTgx native advertisement to the content*/
function addContent($content) {
	if (is_page() || is_single())
	{
		if (get_option(key_rtgx_place_beforecontent) == "on")
		{
			$content = '<div id="rtgx_beforecontent" class="rtgx_placement"></div>
			<script type="text/javascript">
				try {
					rtgx._updatePlacement({"placement":"rtgx_beforecontent"});
				} catch (e) {}
			</script>'.$content;
		}
		if (get_option(key_rtgx_place_undercontent) == "on")
		{
			$content .= '<div id="rtgx_undercontent" class="rtgx_placement"></div>
			<script type="text/javascript">
				try {
					rtgx._updatePlacement({"placement":"rtgx_undercontent"});
				} catch (e) {}
			</script>';		
		}
	}
return $content;
}   

add_action('the_content', 'addContent');


// Create a option page for settings
add_action('admin_menu', 'add_rainbowtgx_option_page');


/*
 * Hook in the options page function
 */
function add_rainbowtgx_option_page() {
	global $wpdb;
	add_menu_page(__('RainbowTgx Options'), 'RainbowTgx', 8, basename(__FILE__), 'rainbowtgx_option_page', plugins_url( 'images/rainbow_favicon.png', __FILE__ ),61);

}


// Add Shortcode
function rainbowtgx_widget_sc( $atts ) {

	// Attributes
	extract( shortcode_atts(
		array(
			'widget_name' => 'my_rainbowtgx_widget_01',
		), $atts )
	);

	// Code
	$output = '<div id="' . esc_html($widget_name) . '" class="rtgx_placement"></div><script type="text/javascript">try{rtgx._updatePlacement({"placement":"' . esc_html($widget_name) . '"});	} catch (e) {}</script>';

	return $output;
}
add_shortcode( 'rainbowtgx_widget', 'rainbowtgx_widget_sc' );
add_filter('widget_text', 'do_shortcode');


function rainbowtgx_option_page() {
 	if( isset($_POST['rtgx_update']) ) {
		if ( wp_verify_nonce($_POST['rtgx-nonce-key'], 'wp_rainbowtgx') ) {
			$rtgx_cid = validate_cid(sanitize_key($_POST[key_rtgx_cid]));
			update_option( key_rtgx_cid, $rtgx_cid );
	
			$rtgx_place_beforecontent = sanitize_checkbox($_POST[key_rtgx_place_beforecontent]);
			update_option( key_rtgx_place_beforecontent, $rtgx_place_beforecontent );
			
			$rtgx_place_undercontent = sanitize_checkbox($_POST[key_rtgx_place_undercontent]);
			update_option( key_rtgx_place_undercontent, $rtgx_place_undercontent );

			$rtgx_place_sidebar = sanitize_checkbox($_POST[key_rtgx_place_sidebar]);
			update_option( key_rtgx_place_sidebar, $rtgx_place_sidebar );

			// Give an updated message
			echo "<div class='updated fade'><p><strong>" . __("RainbowTgx settings saved") . ".</strong></p></div>";
		}
	}
	// Output the options page
	?>

		<div class="wrap">
		<form method="post" action="admin.php?page=wp_rainbowtgx.php">
		<input type="hidden" name="rtgx-nonce-key" value="<?php echo wp_create_nonce('wp_rainbowtgx'); ?>" />
		<h2><img style="vertical-align: middle;padding-right: 15px; height: 35px;" src="<?php echo plugins_url('images/logoRainbow.png', __FILE__ );?>"/><?php echo("Settings"); ?></h2>
			<?php
			if( get_option(key_rtgx_cid) == rtgx_default_cid ) { ?>
				<div style="margin:10px auto; border:3px #f00 solid; background-color:#fdd; color:#000; padding:10px; text-align:center;">
				<?php echo __("RainbowTgx  is currently enabled") . ", " . __("but you did not enter valid a CID. RainbowTgx will") . " <strong>" . __("NOT") . "</strong> " . __("run") . "."; ?>
				</div>
			<?php }
			?>
			<h3>Insert your RainbowTgx account details to start monetize your site<br><br>Still haven't an account? Fill <a href="http://www.rainbowtgx.com/it/contatti-rainbowtgx/" target="_blank">the form</a> and enter RainbowTgx Network!</h3>
						
			<table class="form-table" cellspacing="2" cellpadding="5" width="100%">
				<tr>
					<td colspan="2">
				<h3><?php echo __("Basic Options"); ?></h3>
					</td>
				</tr>
				<tr>
					<th valign="top" style="padding-top: 10px;">
						<label for="<?php echo key_rtgx_cid; ?>"><?php echo __("Your RainbowTgx Customer ID (CID)");?>:</label>
					</th>
					<td>
						<?php
						echo "<input type='text' size='50' ";
						echo "name='".key_rtgx_cid."' ";
						echo "id='".key_rtgx_cid."' ";
						echo "value='".get_option(key_rtgx_cid)."' />\n";
						?>
						<p style="margin: 5px 10px;"><?php echo __("Enter your RainbowTgx Customer ID (CID) in this box");?>.</p>
					</td>
				</tr>
				<tr>
					<th valign="top" style="padding-top: 10px;">
						<label for="rtgx_cid">Place the RainbowTgx Widget</label>
					</th>
					<td>
						<input type="checkbox" <?php echo "name='".key_rtgx_place_beforecontent."' ";?> id="rtgx_place_beforecontent" <?php if( (get_option(key_rtgx_place_beforecontent) == "on")) echo 'checked="checked"';?> > Before Content <br>	
						<input type="checkbox" <?php echo "name='".key_rtgx_place_undercontent."' ";?> id="rtgx_place_undercontent" <?php if( (get_option(key_rtgx_place_undercontent) == "on")) echo 'checked="checked"';?>> After Content <br>
						<input type="checkbox" <?php echo "name='".key_rtgx_place_sidebar."' ";?> id="rtgx_place_sidebar" <?php if( (get_option(key_rtgx_place_sidebar) == "on")) echo 'checked="checked"';?>> Sidebar Widget - use the RainbowTgx widget available under Appearance -> Widgets<br>
					</td>
				</tr>

			</table>
			<p class="submit">
				<input class="button button-primary" type='submit' name='rtgx_update' value='<?php echo __("Save Changes");?>' />
			</p>
		</div>
		</form>

<?php
}

function insert_rainbowtgx_script() {
	echo "\n\n<!-- Begin RainbowTgx tag -->\n";
		echo "<script type=\"text/javascript\" id=\"rtgx_activation\" src=\"http://cdn-wx.rainbowtgx.com/rtgx.js\" rtgx_c=\"" . esc_html(get_option(key_rtgx_cid)) . "\"></script>\n";
	echo "<!-- RainbowTgx tag -->\n";
}

if (get_option(key_rtgx_cid) != rtgx_default_cid) {
	add_action('wp_head', 'insert_rainbowtgx_script');
}


// Creating the widget 
class rtgx_widget extends WP_Widget {

function __construct() {
parent::__construct(
// Base ID of your widget
'rtgx_widget', 

// Widget name will appear in UI
__('RainbowTgx Widget', 'rtgx_widget_domain'), 

// Widget description
array( 'description' => __( 'RainbowTgx Monetization Network Sidebar widget', 'rtgx_widget_domain' ), ) 
);
}

// Creating widget front-end
// This is where the action happens
public function widget( $args, $instance ) {
$title = apply_filters( 'widget_title', $instance['title'] );
// before and after widget arguments are defined by themes
echo $args['before_widget'];
if ( ! empty( $title ) )
echo $args['before_title'] . $title . $args['after_title'];

// This is where you run the code and display the output
echo __( '<div id="rtgx_sidebar" class="rtgx_placement"></div>
			<script type="text/javascript">
				try {
					rtgx._updatePlacement({"placement":"rtgx_sidebar"});
				} catch (e) {}
			</script>', 'rtgx_widget_domain' );
echo $args['after_widget'];
}
		
// Widget Backend 
public function form( $instance ) {
if ( isset( $instance[ 'title' ] ) ) {
$title = sanitize_text_field($instance[ 'title' ]);
}
else {
$title = __( 'Scelti per te', 'rtgx_widget_domain' );
}
// Widget admin form
?>
<p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>
<?php 
}
	
// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {
$instance = array();
$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
return $instance;
}
} // Class rtgx_widget ends here

// Register and load the widget
function rtgx_load_widget() {
	register_widget( 'rtgx_widget' );
}

if (get_option(key_rtgx_place_sidebar) == "on") {
	add_action( 'widgets_init', 'rtgx_load_widget' );
}
?>
