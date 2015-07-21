<?php
/*
Plugin Name: LH Login Page
Plugin URI: http://lhero.org/plugins/lh-login-page/
Description: HTML5 custom login page via shortcode
Author: shawfactor
Version: 1.1
Author URI: http://shawfactor.com/

License:
Released under the GPL license
http://www.gnu.org/copyleft/gpl.html

Copyright 2011  Peter Shaw  (email : pete@localhero.biz)


This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


class LH_login_page_plugin {

var $filename;

var $opt_name = 'lh_login_page-options';
var $page_id_field = 'lh_login_page-page_id';


function uri_to_array($uri){
  $result = array();

  parse_str(substr($uri, strpos($uri, '?') + 1), $result);
  list($result['user'], $result['page']) = explode('/', trim($uri, '/'));

  return $result;
}


function plugin_menu() {
add_options_page('LH Login Page Options', 'LH Login Page', 'manage_options', $this->filename, array($this,"plugin_options"));

}

function plugin_options() {

if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}

    $lh_login_page_page_id_hidden_field_name = 'lh_login_page_page_id_submit_hidden';
   
 // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'

if( isset($_POST[  $lh_login_page_page_id_hidden_field_name ]) && $_POST[  $lh_login_page_page_id_hidden_field_name ] == 'Y' ) {



if (($_POST[ $this->page_id_field ] != "") and ($page = get_page(sanitize_text_field($_POST[ $this->page_id_field ])))){

if ( has_shortcode( $page->post_content, 'lh_login_page_form' ) ) {

$options[ $this->page_id_field ] = sanitize_text_field($_POST[ $this->page_id_field ]);

} else {

echo "shortcode not found";


}

}

// Save the posted value in the database
update_option( $this->opt_name , $options );



        // Put an settings updated message on the screen



?>
<div class="updated"><p><strong><?php _e('Login page id saved', 'menu-test' ); ?></strong></p></div>
<?php

    } else {

$options  = get_option($this->opt_name);

}

    // Now display the settings editing screen

    echo '<div class="wrap">';

    // header

    echo "<h2>" . __('LH Login Page ID', 'menu-test' ) . "</h2>";

    // settings form
    
    ?>

<form name="lh_login_page-backend_form" method="post" action="">
<input type="hidden" name="<?php echo $lh_login_page_page_id_hidden_field_name; ?>" value="Y">

<p><?php _e("Login Page ID;", 'menu-test' ); ?> 
<input type="number" name="<?php echo $this->page_id_field; ?>" id="<?php echo $this->page_id_field; ?>" value="<?php echo $options[ $this->page_id_field ]; ?>" size="10" />
</p>



<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
</p>

</form>



</div>

<?php

	

}


function filter_login_url( $login_url ){

if ($options  = get_option($this->opt_name)){

$bits = $this->uri_to_array($login_url);

if ($bits[redirect_to]){

$login_url = add_query_arg( 'redirect_to', urlencode($bits[redirect_to]), get_permalink($options[$this->page_id_field]) );

} else {

$login_url = get_permalink($options[$this->page_id_field]);

}

}

return $login_url;

}



function filter_logout_url( $logout_url ){

if ($options  = get_option($this->opt_name)){

$logout_url = get_permalink($options[$this->page_id_field]);

$logout_url = add_query_arg( 'lh-login-page-action', 'logout', $logout_url );

$logout_url = add_query_arg( 'lh_login_page_nonce', wp_create_nonce("lh_login_page_nonce"), $logout_url );

}

return $logout_url;

}


function lh_login_page_form_output($return_string){


$return_string .= '
<noscript>Please switch on Javascript to enable this registration</noscript>
';


if(isset($_GET['login']) && $_GET['login'] == 'failed'){

$return_string .= '
<p>Login failed: You have entered an incorrect email or password, please try again.</p>
';

}

$return_string .= '
<form name="lh_login_page-login_form" id="lh_login_page-login_form" action="" method="post" accept-charset="utf-8"
data-lh_login_page-nonce="'.wp_create_nonce("lh_login_page_nonce").'">
';

$return_string .= '<p><!--[if lt IE 10]><br/><label for="lh-login-page-user-email">Email</label><br/><![endif]-->
';


$return_string .= '
<input type="email" name="lh-login-page-user-email" placeholder="yourname@email.com" required="required"  ';

if ($_POST['lh-login-page-user-email']){

$return_string .= ' value="'.$_POST['lh-login-page-user-email'].'"';

}


$return_string .= '></p>
';

$return_string .= '<p><!--[if lt IE 10]><br/><label for="lh-login-page-password">Password</label><br/><![endif]-->
';

$return_string .= '
<input type="password" name="lh-login-page-password" placeholder="password" required="required" /></p>
'; 


$return_string .= '<input type="hidden" id="lh_login_page-nonce" name="lh_login_page-nonce" value="" />';


$return_string .= '
<input class="btn btn-primary btn-lg btn-block" type="submit" name="lh-login-page-login-submit" value="Login"/>
';

$return_string .= '
</form>
';

$return_string .= '<a href="'.wp_lostpassword_url().'" title="Lost Password">Lost Password</a>
';

wp_enqueue_script('lh_login_page_script', plugins_url( '/assets/lh-login-page.js' , __FILE__ ), array(), '0.01', true  );


return $return_string;


}


function lh_login_page_shortcode_output() {

if ( is_user_logged_in() ) {

$user = wp_get_current_user();


$return_string = '<p>'.$user->display_name.' you are already logged in</p>';

$return_string .= '<p>If this is not you, please <a href="'.wp_logout_url(get_permalink()).'" title="Logout">logout</a></p>';

$return_string .= '<script type="text/javascript">
if (window.frameElement) {
alert("in frame");
} else {
//alert("not in frame");
}

</script>';

} else {


$return_string .= $this->lh_login_page_form_output($return_string);

}

return $return_string;

}


function register_shortcodes(){

add_shortcode('lh_login_page_form', array($this,"lh_login_page_shortcode_output"));

}

function force_logout() {

if ($_GET['lh-login-page-action']){

if ( wp_verify_nonce( $_GET['lh_login_page_nonce'], "lh_login_page_nonce") ) {

wp_logout();

$options  = get_option($this->opt_name);

$url =  add_query_arg( 'lh-login-page-cachebuster', wp_generate_password(), get_permalink($options[$this->page_id_field]));

wp_redirect( $url ); exit;

}

}

}


function extend_cookie_expiration_to_1_year( $expirein ) {
   return 31556926; // 1 year in seconds
}



function redirect_if_logged_in() { 
   global $wp_query; 

if ( is_singular() ) { 


$post = $wp_query->get_queried_object(); 

if ( $post->ID == get_option('lh_login_page_page_id') ) { 

if ( is_user_logged_in() ) { 


wp_redirect( home_url() ); exit; 


} 

} 

} 

}


function login_user(){

if ($_POST['lh-login-page-login-submit']){

if ( wp_verify_nonce( $_POST['lh_login_page-nonce'], "lh_login_page_nonce") ) {



$login_data = array();
$user = get_user_by( 'email', sanitize_user($_POST['lh-login-page-user-email']) );

if ( isset( $user, $user->user_login, $user->user_status ) && 0 == (int) $user->user_status ){
			 $login_data['user_login'] = $user->user_login;
}

 $login_data['user_password'] = sanitize_text_field($_POST['lh-login-page-password']);
 $login_data['remember'] = true;

$user_verify = wp_signon( $login_data, false ); 


if ( is_wp_error($user_verify) ) {

$url =  add_query_arg( 'login', 'failed' );

wp_redirect( $url ); exit;

} else {    

if ($_GET[redirect_to]){

wp_redirect( $_GET[redirect_to] ); exit;

} else {

wp_redirect( home_url( '/' ) ); exit;

}

}

} 

}

}

// add a settings link next to deactive / edit
public function add_settings_link( $links, $file ) {

	if( $file == $this->filename ){
		$links[] = '<a href="'. admin_url( 'options-general.php?page=' ).$this->filename.'">Settings</a>';
	}
	return $links;
}



function __construct() {

$this->filename = plugin_basename( __FILE__ );

add_action('admin_menu', array($this,"plugin_menu"));

add_filter( 'login_url', array($this,"filter_login_url"));

add_filter( 'logout_url', array($this,"filter_logout_url"));

add_action( 'init', array($this,"register_shortcodes"));

add_action( 'after_setup_theme', array($this,"force_logout"));

add_filter( 'auth_cookie_expiration', array($this,"extend_cookie_expiration_to_1_year"));

add_action('template_redirect', array($this,"redirect_if_logged_in"));

add_action( 'after_setup_theme', array($this,"login_user"));

add_filter('plugin_action_links', array($this,"add_settings_link"), 10, 2);

}


}

$lh_login_page = new LH_login_page_plugin();


?>