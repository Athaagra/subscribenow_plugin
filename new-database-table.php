<?php

/*
   Plugin Name: SubscribeNow
   description: Subscription plugin
   Version: 1.0.0
   Author: OaA
   Author URI: https://www.agrafiotis.info
   License: GPL v3 or later
   License URI: https://www.gnu.org/licenses/gpl-3.0.html
*/

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
function suno_table(){

  global $wpdb;
  $charset_collate = $wpdb->get_charset_collate();

  $tablename = $wpdb->prefix."subscribers";

  $sql = "CREATE TABLE $tablename (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  email varchar(60),
  subject varchar(60) NULL DEFAULT '',
  message varchar(255),
  serveradd varchar(255),
  smtpname varchar(255) NULL DEFAULT '',
  smtpusername varchar(255) NULL DEFAULT '',
  smtppass varchar(255) NULL DEFAULT '',
  smtppo varchar(255) NULL DEFAULT '',
  smtpsec varchar(255) NULL DEFAULT '',
  PRIMARY KEY (id)
  ) $charset_collate;";

  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $sql );

}
function ch_empt($lifespan) {
	if(empty($lifespan)){
			return TRUE;
	}else{
			return FALSE;
	}
}
#wp_create_nonce('check_empt');
#wp_nonce_field('check_empty','check_empt');


register_activation_hook( __FILE__, 'suno_table' );

function suno_tbare($atts) {
	ob_start();?>
	
	<form action="<?php echo esc_url(admin_url('admin-post.php')) ?>" class="create-sub-form" method="POST">
				<input type="hidden" name="action" value="createsubscriber">
				<input type="hidden" name="my-nonces" value="<?php echo wp_create_nonce('my-nonces'); ?>">
				<input type="text" name="incomingsubscriber" placeholder="e-mail">
				<button> Subscribe </button>
			</form>
	<?php
	$Content = ob_get_contents();
	ob_clean();
	return $Content;
			
}

add_shortcode('subscribe_area', 'suno_tbare');

//if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly NOT USE FOR NOW
//require_once plugin_dir_path(__FILE__) . 'inc/generateUser.php';
define( 'NEWDATABASETABLEPATH', plugin_dir_path( __FILE__ ));
require_once plugin_dir_path(__FILE__) . 'inc/GetSubscribers.php';
//include '../../../wp-load.php';

class suno_SubscribeNow {
  function __construct() {
		global $wpdb;
		$this->charset = $wpdb->get_charset_collate();
		$this->tablename = $wpdb->prefix . "subscribers";
		
    add_action('activate_new-database-table/new-database-table.php', array($this, 'onActivate'));
    //add_action('admin_head', array($this, 'onAdminRefresh'));
	//add_action( 'init', array($this,'sendForm'));
	add_action('admin_post_createsubscriber', array($this, 'createsubscriber'));
	add_action('admin_post_nopriv_createsubscriber', array($this, 'createsubscriber'));
	add_action('admin_post_createmessage', array($this, 'createmessage'));
	add_action('admin_post_nopriv_createmessage', array($this, 'createmessage'));
	add_action('admin_post_deletesubscriber', array($this, 'deletesubscriber'));
	add_action('admin_post_nopriv_deletesubscriber', array($this, 'deletesubscriber'));
	add_action('admin_post_createsmtp', array($this, 'createsmtp'));
	add_action('admin_post_nopriv_createsmtp', array($this, 'createsmtp'));
    add_action('wp_enqueue_scripts', array($this, 'loadAssets'));
	add_action('admin_menu',array($this,'ourMenu'));
	add_action('init', array($this,'subscribeBlock'));
    add_filter('template_include', array($this, 'loadTemplate'), 99);
	add_filter('pre_wp_mail', 'sendForm', 10, 2);	
  }
 
  function ourMenu(){
	add_menu_page('SubscribeNow','SubscribeNow','manage_options','subscribenow', array($this, 'subscriberlist'),plugin_dir_url(__FILE__) . 'custooaa.png',100);
	add_submenu_page('subscribenow', 'SubscribeNow', 'Subscribe Main Page', 'manage_options', 'subscribenow', array($this, 'subscriberlist'));
	add_submenu_page('subscribenow', 'SubscribeNow Options','Options','manage_options', 'subscribenow-options', array($this, 'optionsSubPage'));
  }
  function subscribeBlock(){
	wp_register_script('subscribeBlockScript', get_stylesheet_directory_uri() . '/build/subscribenow.js', array('wp-blocks','wp-editor'));
	register_block_type("ourblocktheme/subscribe", array(
		'editor_script' => 'subscribeBlockBlockScript'
		));
	}
  

  function subscribeBlocka(){ 
	}
	//<php  
  function myShortCodeRender() { 
//    ob_start(); 
//    $this->subscribeBlocka();
//    return ob_get_clean();
  }
   function shortcodes_init(){
	//add_shortcode( 'subscribe_area', 'subscribeBlocka');
  }
  
  function optionsSubPage(){
	?>
	<div class="wrap">
		<h1>Subscribe Now</h1>
		<?php  ?>
		<p>display with a shortcode [subscribe_area]</p>
		<form action="<?php echo esc_url(admin_url('admin-post.php')) ?>" class="create-sub-form" method="POST">
			<input type="hidden" name="action" value="createsmtp">
			<div>
			<input type="text" name="serveraddress" placeholder="Server address:smtp.gmail.com">
			<input type="hidden" name="my-nonce" value="<?php echo wp_create_nonce('my-nonce'); ?>">
			</div>
			<div>
			<input type="text" name="smtpname" placeholder="full name: Hakin">
			</div>
			<div>
			<input type="text" name="smtpusername" placeholder="full Gmail address: athaagrak@gmail.com">
			</div>
			<div>
			<input type="text" name="smtppassword" placeholder="Password 16 dig.: dhasafa121w3i21u">
			</div>
			<div>
			<input type="text" name="smtpport" placeholder="The port 587(tls) 465(ssl): 587">
			</div>
			<div>
			<input type="text" name="smtpsecure" placeholder="Protocol tls,ssl: tls">
			</div>
			<button>Save settings</button>
		</form>
	</div>
  <?php }
  
  function handleForm() {
	$getSubscribers = new suno_GetSubscribers();
	//if($getSubscribers->num_rows > 0){ 
	$delimiter = ","; 
	$filename = "members-data_" . gmdate('Y-m-d') . ".csv";      
		// Create a file pointer 
	$f = fopen('php://output', 'w'); 
		// Set column headers 
	$fields = array('ID', 'EMAIL'); 
	foreach($getSubscribers->subscribers as $subscriber){
			$subemail= $subscriber->email;
			$lineData = array($subemail);
			fputcsv($f,$lineData); 
	} 
	fseek($f, 0);
	header('Content-Type: application/octet-stream'); 
	header('Content-Disposition: attachment; filename="' . $filename . '";');
	header("Pragma: no-cache");
    header("Expires: 0");
	fpassthru($f); 
	fclose($f);
  }
	
	
	
	
	
	
  function subscriberlist() { 
	$getSubscribers = new suno_GetSubscribers();
 
	$justsubmitted = false;?>
	 <table class="sub-adoption-table">
    <tr>
      <th>E-mail</th>
    </tr>
	<div class="wrap" style="width: 48%; float: right;">
		<h1>Newsletter Area</h1>
		//<?php ?>
		//<php if (isset($_POST['newsl']))>
		<form action="<?php echo esc_url(admin_url('admin-post.php')) ?>" method="POST">
			<input type="hidden" name="action" value="createmessage">
			<input type="hidden" name="my-noncem" value="<?php echo wp_create_nonce('my-noncem'); ?>">
			<label for="plugin_subscribe_now"><p> Enter the message </p></label>
			
			<textarea name="plugin_subj" id="plugin_subj" placeholder="subject"></textarea>
			<!--<p class="errorsubj"><php echo esc_html($errorsubj); ?></p>-->
			<div class="word-filter__flex-container">
				<textarea name="plugin_subscribers" id="plugin_subscribers" placeholder="Newsletter Area" style="height: 300px; width:500px;"></textarea>
			</div>
			<!--<p class="errorsubscribers"><php echo esc_html($errorsubscribers); ?></p>-->
			<button class="send-subscriber-button"> save message</button>
		</form>
	</div>
	<?php
	foreach($getSubscribers->subscribers as $subscriber) { ?>
    <tr>
      <td><?php echo esc_html($subscriber->email); ?></td>
      <!--<php if(current_user_can('administrator')) { ?>-->
		<td style="text-align: center;">
		<form action="<?php echo esc_url(admin_url('admin-post.php')) ?>" method="POST">
			<input type="hidden" name="action" value="deletesubscriber">
			<input type="hidden" name="my-nonced" value="<?php echo wp_create_nonce('my-nonced'); ?>">
			<input type="hidden" name="idtodelete" value="<?php echo $subscriber->id; ?>">
			<button class="delete-subscriber-button"> X </button>
		</form>
	  </td>
	  <td style="text-align: center;">
		<form method="POST">
			<input type="hidden" name="sendmail" value="true">
			<input type="hidden" name="idsendmail" value="<?php echo esc_html($subscriber->email); ?>">
			<input type="hidden" name="my-nonceis" value="<?php echo wp_create_nonce('my-nonceis'); ?>">
			<button class="send-subscriber-button"> send mail..</button>
			<?php } ?>
			<?php if (isset($_POST['sendmail'])and wp_verify_nonce($_POST['my-nonceis'],'my-nonceis') and !empty($_POST['sendmail'])) $this->sendForm() ?> 
		</form>
	  </td>
	  <!--<php } ?>-->
	  </tr>
	
  </table>
  <?php if (isset($_POST['justsubmitted'])) $this->handleForm() ?> 
  <form method="POST">
		<input type="hidden" name="justsubmitted" value="true">
		<input type="submit" name="submit" id="submit" class="button button-primary" value="download csv ">
	</form>
   <?php
  }
  
  function sendForm() {
	  $getSubscribers = new suno_GetSubscribers();
	  foreach($getSubscribers->messagequery as $msg){ 
		$subj= $msg->subject; 
		$mes = $msg->message;
	  }
	  foreach($getSubscribers->settingsquery as $setting){ 
		$server_address= $setting->serveradd; 
		$server_name = $setting->smtpname;
		$server_username = $setting->smtpusername;
		$server_password = $setting->smtppass;
		$server_port = $setting->smtppo;
		$server_protocol = $setting->smtpsec;
	  }
	$options = array();
    $options['smtp_host'] = $server_address;
    $options['smtp_auth'] = True;
    $options['smtp_username'] = $server_username;
    $options['smtp_password'] = $server_password;
    $options['type_of_encryption'] = $server_protocol;
    $options['smtp_port'] = $server_port;
    $options['from_email'] = $server_username;
    $options['from_name'] = $server_name;
    $options['force_from_address'] = True;
    $options['disable_ssl_verification'] = False;
//function smtp_mailer_pre_wp_mail($null, $atts)
//{

    $to = $server_username;
//    if ( ! is_array( $to ) ) {
//            $to = explode( ',', $to );
//    }
    $subject = $subj;
    $message = $mes;
//    if ( isset( $atts['headers'] ) ) {
//            $headers = $atts['headers'];
//    }
//    if ( isset( $atts['attachments'] ) ) {
//            $attachments = $atts['attachments'];
//            if ( ! is_array( $attachments ) ) {
//                    $attachments = explode( "\n", str_replace( "\r\n", "\n", $attachments ) );
//            }
//    }
    
//    $options = smtp_mailer_get_option();
    
    global $phpmailer;

    // (Re)create it, if it's gone missing.
    if ( ! ( $phpmailer instanceof PHPMailer\PHPMailer\PHPMailer ) ) {
            require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
            require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
            require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
            $phpmailer = new PHPMailer( true );

            $phpmailer::$validator = static function ( $email ) {
                    return (bool) is_email( $email );
            };
    }

    // Headers.
    $cc       = array();
    $bcc      = array();
    $reply_to = array();

    if ( empty( $headers ) ) {
            $headers = array();
    } else {
            if ( ! is_array( $headers ) ) {
                    /*
                     * Explode the headers out, so this function can take
                     * both string headers and an array of headers.
                     */
                    $tempheaders = explode( "\n", str_replace( "\r\n", "\n", $headers ) );
            } else {
                    $tempheaders = $headers;
            }
            $headers = array();

            // If it's actually got contents.
            if ( ! empty( $tempheaders ) ) {
                    // Iterate through the raw headers.
                    foreach ( (array) $tempheaders as $header ) {
                            if ( ! str_contains( $header, ':' ) ) {
                                    if ( false !== stripos( $header, 'boundary=' ) ) {
                                            $parts    = preg_split( '/boundary=/i', trim( $header ) );
                                            $boundary = trim( str_replace( array( "'", '"' ), '', $parts[1] ) );
                                    }
                                    continue;
                            }
                            // Explode them out.
                            list( $name, $content ) = explode( ':', trim( $header ), 2 );

                            // Cleanup crew.
                            $name    = trim( $name );
                            $content = trim( $content );

                            switch ( strtolower( $name ) ) {
                                    // Mainly for legacy -- process a "From:" header if it's there.
                                    case 'from':
                                            $bracket_pos = strpos( $content, '<' );
                                            if ( false !== $bracket_pos ) {
                                                    // Text before the bracketed email is the "From" name.
                                                    if ( $bracket_pos > 0 ) {
                                                            $from_name = substr( $content, 0, $bracket_pos );
                                                            $from_name = str_replace( '"', '', $from_name );
                                                            $from_name = trim( $from_name );
                                                    }

                                                    $from_email = substr( $content, $bracket_pos + 1 );
                                                    $from_email = str_replace( '>', '', $from_email );
                                                    $from_email = trim( $from_email );

                                                    // Avoid setting an empty $from_email.
                                            } elseif ( '' !== trim( $content ) ) {
                                                    $from_email = trim( $content );
                                            }
                                            break;
                                    case 'content-type':
                                            if ( str_contains( $content, ';' ) ) {
                                                    list( $type, $charset_content ) = explode( ';', $content );
                                                    $content_type                   = trim( $type );
                                                    if ( false !== stripos( $charset_content, 'charset=' ) ) {
                                                            $charset = trim( str_replace( array( 'charset=', '"' ), '', $charset_content ) );
                                                    } elseif ( false !== stripos( $charset_content, 'boundary=' ) ) {
                                                            $boundary = trim( str_replace( array( 'BOUNDARY=', 'boundary=', '"' ), '', $charset_content ) );
                                                            $charset  = '';
                                                    }

                                                    // Avoid setting an empty $content_type.
                                            } elseif ( '' !== trim( $content ) ) {
                                                    $content_type = trim( $content );
                                            }
                                            break;
                                    case 'cc':
                                            $cc = array_merge( (array) $cc, explode( ',', $content ) );
                                            break;
                                    case 'bcc':
                                            $bcc = array_merge( (array) $bcc, explode( ',', $content ) );
                                            break;
                                    case 'reply-to':
                                            $reply_to = array_merge( (array) $reply_to, explode( ',', $content ) );
                                            break;
                                    default:
                                            // Add it to our grand headers array.
                                            $headers[ trim( $name ) ] = trim( $content );
                                            break;
                            }
                    }
            }
    }

    // Empty out the values that may be set.
    $phpmailer->clearAllRecipients();
    $phpmailer->clearAttachments();
    $phpmailer->clearCustomHeaders();
    $phpmailer->clearReplyTos();
    $phpmailer->Body    = '';
    $phpmailer->AltBody = '';

    // Set "From" name and email.

    // If we don't have a name from the input headers.
    if ( ! isset( $from_name ) ) {
            $from_name = $options['from_name'];//'WordPress';
    }

    /*
     * If we don't have an email from the input headers, default to wordpress@$sitename
     * Some hosts will block outgoing mail from this address if it doesn't exist,
     * but there's no easy alternative. Defaulting to admin_email might appear to be
     * another option, but some hosts may refuse to relay mail from an unknown domain.
     * See https://core.trac.wordpress.org/ticket/5007.
     */
    if ( ! isset( $from_email ) ) {
            // Get the site domain and get rid of www.
            $sitename   = wp_parse_url( network_home_url(), PHP_URL_HOST );
            $from_email = 'wordpress@';

            if ( null !== $sitename ) {
                    if ( str_starts_with( $sitename, 'www.' ) ) {
                            $sitename = substr( $sitename, 4 );
                    }

                    $from_email .= $sitename;
            }
            $from_email = $options['from_email'];//'wordpress@' . $sitename;
    }

    /**
     * Filters the email address to send from.
     *
     * @since 2.2.0
     *
     * @param string $from_email Email address to send from.
     */
    $from_email = apply_filters( 'wp_mail_from', $from_email );

    /**
     * Filters the name to associate with the "from" email address.
     *
     * @since 2.3.0
     *
     * @param string $from_name Name associated with the "from" email address.
     */
    $from_name = apply_filters( 'wp_mail_from_name', $from_name );
    //force from address if checked
    if(isset($options['force_from_address']) && !empty($options['force_from_address'])){
        $from_name = $options['from_name'];
        $from_email = $options['from_email'];
    }
    try {
            $phpmailer->setFrom( $from_email, $from_name, false );
    } catch ( PHPMailer\PHPMailer\Exception $e ) {
            $mail_error_data                             = compact( 'to', 'subject', 'message', 'headers', 'attachments' );
            $mail_error_data['phpmailer_exception_code'] = $e->getCode();

            /** This filter is documented in wp-includes/pluggable.php */
            do_action( 'wp_mail_failed', new WP_Error( 'wp_mail_failed', $e->getMessage(), $mail_error_data ) );

            return false;
    }
    /*reply_to code */
    $smtpmailer_reply_to = '';
    $smtpmailer_reply_to = apply_filters('smtpmailer_reply_to', $smtpmailer_reply_to);
    if(isset($smtpmailer_reply_to) && !empty($smtpmailer_reply_to)){
        $temp_reply_to_addresses = explode(",", $smtpmailer_reply_to);
        $reply_to = array();
        foreach($temp_reply_to_addresses as $temp_reply_to_address){
            $reply_to_address = trim($temp_reply_to_address);
            $reply_to[] = $reply_to_address;
        }
    }
    /*end of reply_to code */
    // Set mail's subject and body.
    $phpmailer->Subject = $subj;
    $phpmailer->Body    = $mes;

    // Set destination addresses, using appropriate methods for handling addresses.
    $address_headers = compact( 'to', 'cc', 'bcc', 'reply_to' );

    foreach ( $address_headers as $address_header => $addresses ) {
            if ( empty( $addresses ) ) {
                    continue;
            }

            foreach ( (array) $addresses as $address ) {
                    try {
                            // Break $recipient into name and address parts if in the format "Foo <bar@baz.com>".
                            $recipient_name = '';

                            if ( preg_match( '/(.*)<(.+)>/', $address, $matches ) ) {
                                    if ( count( $matches ) === 3 ) {
                                            $recipient_name = $matches[1];
                                            $address        = $matches[2];
                                    }
                            }

                            switch ( $address_header ) {
                                    case 'to':
                                            $phpmailer->addAddress( $address, $recipient_name );
                                            break;
                                    case 'cc':
                                            $phpmailer->addCc( $address, $recipient_name );
                                            break;
                                    case 'bcc':
                                            $phpmailer->addBcc( $address, $recipient_name );
                                            break;
                                    case 'reply_to':
                                            $phpmailer->addReplyTo( $address, $recipient_name );
                                            break;
                            }
                    } catch ( PHPMailer\PHPMailer\Exception $e ) {
                            continue;
                    }
            }
    }

    // Tell PHPMailer to use SMTP
    $phpmailer->isSMTP(); //$phpmailer->isMail();
    // Set the hostname of the mail server
    $phpmailer->Host = $options['smtp_host'];
    // Whether to use SMTP authentication
    if(isset($options['smtp_auth']) && $options['smtp_auth'] == "true"){
        $phpmailer->SMTPAuth = true;
        // SMTP username
        $phpmailer->Username = $options['smtp_username'];
        // SMTP password
        $phpmailer->Password = $options['smtp_password'];//base64_decode($options['smtp_password']);  
    }
    // Whether to use encryption
    $type_of_encryption = $options['type_of_encryption'];
    if($type_of_encryption=="none"){
        $type_of_encryption = '';  
    }
    $phpmailer->SMTPSecure = $type_of_encryption;
    // SMTP port
    $phpmailer->Port = $options['smtp_port'];  

    // Whether to enable TLS encryption automatically if a server supports it
    $phpmailer->SMTPAutoTLS = false;
    //enable debug when sending a test mail
    if(isset($_POST['smtp_mailer_send_test_email']) and wp_verify_nonce($_POST['smtp_mailer_send_test_email'],'ch_empty')){
        $phpmailer->SMTPDebug = 1;
        // Ask for HTML-friendly debug output
        $phpmailer->Debugoutput = 'html';
    }

    //disable ssl certificate verification if checked
    if(isset($options['disable_ssl_verification']) && !empty($options['disable_ssl_verification'])){
        $phpmailer->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
    }
    // Set Content-Type and charset.

    // If we don't have a Content-Type from the input headers.
    if ( ! isset( $content_type ) ) {
            $content_type = 'text/plain';
    }

    $content_type = apply_filters( 'wp_mail_content_type', $content_type );

    $phpmailer->ContentType = $content_type;

    // Set whether it's plaintext, depending on $content_type.
    if ( 'text/html' === $content_type ) {
            $phpmailer->isHTML( true );
    }

    // If we don't have a charset from the input headers.
    if ( ! isset( $charset ) ) {
            $charset = get_bloginfo( 'charset' );
    }


    $phpmailer->CharSet = apply_filters( 'wp_mail_charset', $charset );

    // Set custom headers.
    if ( ! empty( $headers ) ) {
            foreach ( (array) $headers as $name => $content ) {
                    // Only add custom headers not added automatically by PHPMailer.
                    if ( ! in_array( $name, array( 'MIME-Version', 'X-Mailer' ), true ) ) {
                            try {
                                    $phpmailer->addCustomHeader( sprintf( '%1$s: %2$s', $name, $content ) );
                            } catch ( PHPMailer\PHPMailer\Exception $e ) {
                                    continue;
                            }
                    }
            }

            if ( false !== stripos( $content_type, 'multipart' ) && ! empty( $boundary ) ) {
                    $phpmailer->addCustomHeader( sprintf( 'Content-Type: %s; boundary="%s"', $content_type, $boundary ) );
            }
    }

    if ( isset( $attachments ) && ! empty( $attachments ) ) {
            foreach ( $attachments as $filename => $attachment ) {
                    $filename = is_string( $filename ) ? $filename : '';

                    try {
                            $phpmailer->addAttachment( $attachment, $filename );
                    } catch ( PHPMailer\PHPMailer\Exception $e ) {
                            continue;
                    }
            }
    }

    /**
     * Fires after PHPMailer is initialized.
     *
     * @since 2.2.0
     *
     * @param PHPMailer $phpmailer The PHPMailer instance (passed by reference).
     */
    do_action_ref_array( 'phpmailer_init', array( &$phpmailer ) );

    $mail_data = compact( 'to', 'subject', 'message', 'headers' );

    // Send!
    try {
            $send = $phpmailer->send();

            /**
             * Fires after PHPMailer has successfully sent an email.
             *
             * The firing of this action does not necessarily mean that the recipient(s) received the
             * email successfully. It only means that the `send` method above was able to
             * process the request without any errors.
             *
             * @since 5.9.0
             *
             * @param array $mail_data {
             *     An array containing the email recipient(s), subject, message, headers, and attachments.
             *
             *     @type string[] $to          Email addresses to send message.
             *     @type string   $subject     Email subject.
             *     @type string   $message     Message contents.
             *     @type string[] $headers     Additional headers.
             *     @type string[] $attachments Paths to files to attach.
             * }
             */
            do_action( 'wp_mail_succeeded', $mail_data );

            return $send;
    } catch ( PHPMailer\PHPMailer\Exception $e ) {
            $mail_data['phpmailer_exception_code'] = $e->getCode();

            /**
             * Fires after a PHPMailer\PHPMailer\Exception is caught.
             *
             * @since 4.4.0
             *
             * @param WP_Error $error A WP_Error object with the PHPMailer\PHPMailer\Exception message, and an array
             *                        containing the mail recipient, subject, message, headers, and attachments.
             */
            do_action( 'wp_mail_failed', new WP_Error( 'wp_mail_failed', $e->getMessage(), $mail_data ) );

            return false;
    }
}

  function deletesubscriber() {
	  if (current_user_can('administrator')){
		  if(wp_verify_nonce($_POST['my-nonced'],'my-nonced') && !empty($_POST['idtodelete'])){
			$id = sanitize_text_field( wp_unslash ( $_POST['idtodelete'] ) ) ; //sanitize_text_field($_POST['idtodelete']);
		  }else{
			  echo esc_html('There is no entry to delete');
		  }
		  global $wpdb;
		  $wpdb -> delete($this->tablename, array('id'=> $id));
		  wp_safe_redirect(site_url('/wp-admin/admin.php?page=subscribenow'));
	  }else{
		  wp_safe_redirect(site_url());
	  }
	  exit;
  }
  
  
  
  function createsubscriber() {
	  //if (current_user_can('administrator')){
		  //$subscriber = suno_generateUser();
	    if(!filter_var($_POST['incomingsubscriber'],FILTER_VALIDATE_EMAIL) and wp_verify_nonce($_POST['my-nonces'],'my-nonces')){
			echo esc_html("Email is required");
			wp_safe_redirect(site_url());
		}else{
			$subscriber['email'] = sanitize_text_field( wp_unslash ( $_POST['incomingsubscriber'] ) ) ; //sanitize_text_field($_POST['incomingsubscriber']);
		  
		  global $wpdb;
		  $wpdb -> insert($this->tablename, $subscriber);
		  //wp_safe_redirect(site_url('/subscribers/'));
	  //}else{
		  wp_safe_redirect(site_url());
	  }
	  exit;
  }
  
  function createmessage() {
	  $errorsubj=null;
	  $errorsubscribers=null;
	  if (current_user_can('administrator')){
		if(wp_verify_nonce($_POST['my-noncem'],'my-noncem')  && !empty($_POST['plugin_subj'])){
			$subscriber['subject'] = sanitize_text_field( wp_unslash ( $_POST['plugin_subj'] ) ) ; //sanitize_text_field($_POST['plugin_subj']);
		}else{
			echo esc_html('Message subject is required');
		}
		if(wp_verify_nonce($_POST['my-noncem'],'my-noncem') && !empty($_POST['plugin_subscribers'])){
			$subscriber['message'] = sanitize_text_field( wp_unslash ( $_POST['plugin_subscribers'] ) ) ;//sanitize_text_field($_POST['plugin_subscribers']);
		}else {
			echo esc_html('Text message is required');
		}
		  //}
		global $wpdb;
		$wpdb -> insert($this->tablename, $subscriber);
		wp_safe_redirect(site_url('/wp-admin/admin.php?page=subscribenow'));
	  }else{
		  wp_safe_redirect(site_url());
	  }
	  exit;
  }
 
  function createsmtp() {
	  $errorsmtp= null;
	  $errorname= null;
	  $errormail= null;
	  $errorpass= null;
	  $errorport= null;
	  $errorsecure = null;
	  if (current_user_can('administrator')){
		if(wp_verify_nonce($_POST['my-nonce'],'my-nonce') && !empty($_POST['serveraddress']) && strpos($_POST['serveraddress'],".") !==false){
				$subscriber['serveradd'] = sanitize_text_field( wp_unslash ( $_POST['serveraddress'] ) ) ;//sanitize_text_field($_POST['serveraddress']);
		} else{
				echo esc_html('Server Address is required');
		}
		if(wp_verify_nonce($_POST['my-nonce'],'my-nonce') && !empty($_POST['smtpname'])){
				$subscriber['smtpname'] =  sanitize_text_field( wp_unslash ( $_POST['smtpname'] ) ) ;//sanitize_text_field($_POST['smtpname']);
			} else {
				  echo esc_html('smtp name is required');
			}
		if(wp_verify_nonce($_POST['my-nonce'],'my-nonce') && !empty($_POST['smtpusername']) && strpos($_POST['smtpusername'],"@") !==false){
				$subscriber['smtpusername'] = sanitize_text_field( wp_unslash ( $_POST['smtpusername'] ) ) ;// sanitize_text_field($_POST['smtpusername']);  
		    } else {
				  echo esc_html('Smtp email is required');
		    }
		if(wp_verify_nonce($_POST['my-nonce'],'my-nonce') && !empty($_POST['smtppassword'])){
				$subscriber['smtppass'] = sanitize_text_field( wp_unslash ( $_POST['smtppassword'] ) ) ;//sanitize_text_field($_POST['smtppassword']);
			} else {
				  echo esc_html('smtp password is required');
			}
		if(wp_verify_nonce($_POST['my-nonce'],'my-nonce') && !empty($_POST['smtpport'])){
				$subscriber['smtppo'] = sanitize_text_field( wp_unslash ( $_POST['smtpport'] ) ) ;//sanitize_text_field($_POST['smtpport']);
			} else {
				   echo esc_html('Port is required');
			}
		if(wp_verify_nonce($_POST['my-nonce'],'my-nonce') && !empty($_POST['smtpsecure'])){
				$subscriber['smtpsec'] = sanitize_text_field( wp_unslash ( $_POST['smtpsecure'] ) ) ;//sanitize_text_field($_POST['smtpsecure']);
			} else {
				  echo esc_html('TLS number is required');
			}
		  global $wpdb;
		  $wpdb -> insert($this->tablename, $subscriber);
		  wp_safe_redirect(site_url('/wp-admin/admin.php?page=subscribenow'));
	  }else{
		  wp_safe_redirect(site_url());
	  }
	  exit;
  }

  function onActivate() {
	  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta("CREATE TABLE $this->tablename (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			email varchar(60) NOT NULL DEFAULT '',
			PRIMARY KEY (id)
		) $this->charset; ");
  }

  function onAdminRefresh() {
    global $wpdb;
	$wpdb->insert($this->tablename,suno_generateUser());
  }

  function loadAssets() {
    if (is_page('subscribers')) {
      wp_enqueue_style('subscriberscss', plugin_dir_url(__FILE__) . 'subscribers-s.css');
    }
  }

  function loadTemplate($template) {
    if (is_page('subscribers')) {
      return plugin_dir_path(__FILE__) . 'inc/template-subscribers.php';
    }
    return $template;
  }
	
  function populateFast() {
    $query = "INSERT INTO $this->tablename (`email`) VALUES ";
    $numberofusers = 100000;
    for ($i = 0; $i < $numberofusers; $i++) {
      $subscriber = suno_generateUser();
      $query .= "('{$subscriber['email']}')";
      if ($i != $numberofusers - 1) {
        $query .= ", ";
      }
    }
    /*
    Never use query directly like this without using $wpdb->prepare in the
    real world. I'm only using it this way here because the values I'm 
    inserting are coming fromy my innocent generator function so I
    know they are not malicious, and I simply want this example script
    to execute as quickly as possible and not use too much memory.
    
    global $wpdb;
    $wpdb->query($query)*/
  }
}

 

$subscribeNow = new suno_SubscribeNow();

class suno_OurPluginPlaceholderBlock {
  function __construct($name) {
    $this->name = $name;
    add_action('init', [$this, 'onInit']);
  }
 
  function ourRenderCallback($attributes, $content) {
    ob_start();
    require plugin_dir_path(__FILE__) . 'our-blocks/' . $this->name . '.php';
    return ob_get_clean();
  }
 
  function onInit() {
    wp_register_script($this->name, plugin_dir_url(__FILE__) . "/our-blocks/{$this->name}.js", array('wp-blocks', 'wp-editor'));
    
    register_block_type("ourdatabaseplugin/{$this->name}", array(
      'editor_script' => $this->name,
      'render_callback' => [$this, 'ourRenderCallback']
    ));
  }
}
new suno_OurPluginPlaceholderBlock("subscribenow");