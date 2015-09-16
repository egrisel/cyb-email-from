<?php
/**
* Plugin Name: Cyb Email From
* Plugin URI: http://www.cybernaute.ch/plugins/cyb-email-from/
* Description: Let you change the default email and name used to send email when a new user register on your WordPress website.
* Author: Cybernaute.ch
* Version: 0.1
* Author URI: http://www.cybernaute.ch
* Text Domain: cyb-email-from
* Domain Path: /languages
* License: GPLv2 or later
*/

if (! function_exists ( 'add_action' )) {
	header ( 'Status: 403 Forbidden' );
	header ( 'HTTP/1.1 403 Forbidden' );
	exit ();
}

class CybEmailFrom
{

    public function __construct()
    {
        /* Filter for the email and the name of the sender of WordPress emails */
        add_filter( 'wp_mail_from', array( $this, 'mail_from' ) );
        add_filter( 'wp_mail_from_name', array( $this, 'mail_from_name' ) );
        
        /* Add an admin menu */
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ), 20 );
        
        /* Register the settings */
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        
        /* Load the translations */
        add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
    }
    
    public function mail_from()
    {
        if ( false == get_option( 'cyb_email_options' ) )
            return get_option( 'admin_email' );
        $options = get_option( 'cyb_email_options' );
        if ( empty( $options['email'] ) )
            return get_option( 'admin_email' );
        return $options['email'];
    }
    
    public function mail_from_name()
    {
        if ( false == get_option( 'cyb_email_options' ) )
            return get_option( 'blogname' );
        $options = get_option( 'cyb_email_options' );
        if ( empty( $options['name'] ) )
            return get_option( 'blogname' );
        return $options['name'];
    }
    
    public function add_admin_menu()
    {
        add_submenu_page( 'options-general.php', __( 'Email parameters', 'cyb-email-from' ), __( 'Email parameters', 'cyb-email-from' ), 'manage_options', 'cyb_email', array( $this, 'menu_html' ) );
    }
    
    public function menu_html()
    {
        echo '<h1>' . get_admin_page_title() . '</h1>';
        ?>
        <!-- Crée le formulaire qui sera utilisé pour afficher nos options -->
        <form method="post" action="options.php">
            <?php settings_fields( 'cyb_email_settings' ); ?>
            <?php do_settings_sections( 'cyb_email_settings' ); ?>
            <?php submit_button(); ?>
        </form>
        <?php  
    }
    
    public function register_settings()
    {
        register_setting( 'cyb_email_settings', 'cyb_email_options' );
        
        add_settings_section( 'cyb_email_section', '', array( $this, 'section_html' ), 'cyb_email_settings' );
        
        add_settings_field( 'cyb_email_url', __( 'Email of the sender', 'cyb-email-from' ), array( $this, 'email_html' ), 'cyb_email_settings', 'cyb_email_section' );
        add_settings_field( 'cyb_email_name', __( 'Name of the sender', 'cyb-email-from' ), array( $this, 'name_html' ), 'cyb_email_settings', 'cyb_email_section' );
    }
    
    public function section_html()
    {
        _e('Complete the parameters for the emails sent by your website.', 'cyb-email-from' );
    }
    
    public function email_html()
    {
        $options = get_option( 'cyb_email_options' );
        if ( $options == false || empty( $options['email'] ) ) {
            $email = get_option( 'admin_email' );
        } else {
            $email = $options['email'];
        }
        ?>
        <input type="text" name="cyb_email_options[email]" value="<?php echo esc_attr( $email ); ?>" />
        <?php
    }

    public function name_html()
    {
        $options = get_option( 'cyb_email_options' );
        if ( $options == false || empty( $options['name'] ) ) {
            $name = get_option( 'blogname' );
        } else {
            $name = $options['name'];
        }
        ?>
        <input type="text" name="cyb_email_options[name]" value="<?php echo esc_attr( $name ); ?>" />
        <?php
    }
    
    public function load_plugin_textdomain()
    {
        load_plugin_textdomain( 'cyb-email-from', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
    }
    
}

new CybEmailFrom();
