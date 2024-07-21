<?php

require_once plugin_dir_path(__FILE__) . 'GetSubscribers.php';
$getSubscribers = new suno_GetSubscribers();

get_header(); ?>

<!--<div class="page-banner">
  <div class="page-banner__bg-image" style="background-image: url(<php echo get_theme_file_uri('/images/ocean.jpg'); ?>);"></div>
  <div class="page-banner__content container container--narrow">
    <h1 class="page-banner__title">Subscribers</h1>
    <div class="page-banner__intro">
      <p>Providing forever homes one search at a time.</p>
    </div>
  </div>  
</div>

<!--<div class="container container--narrow page-section">-->
<!--<php
foreach($getSubscribers->messagequery as $msg){ 
	$subj= $msg->subject; 
	$mes = $msg->message; >
	<p><php echo $mes; ></p>
	<p><php echo $subj; ></p>
  <p>This page took <strong><php echo timer_stop();></strong> seconds to prepare <strong></strong>. Found <strong><php echo $getSubscribers->counts; ?></strong> results (showing the first <php echo count($getSubscribers->subscribers) ?>).</p>-->
<!--<php } >-->
<!--  <table class="sub-adoption-table">
    <tr>
      <th>E-mail</th>
    </tr>-->
	<!--<php
	foreach($getSubscribers->subscribers as $subscriber) { ?>
		<php echo $subscriber->email; >-->
    <!--<tr>
      <td><php echo $subscriber->email; ?></td>
      <php if(current_user_can('administrator')) { ?>
		<td style="text-align: center;">
		<form action="<php echo esc_url(admin_url('admin-post.php')) ?>" method="POST">
			<input type="hidden" name="action" value="deletesubscriber">
			<input type="hidden" name="idtodelete" value="<php echo $subscriber->email; ?>">
			<button class="delete-subscriber-button"> X </button>
		</form>
	  </td>-->
	  <!--<php  >-->
<!--<php 
foreach($getSubscribers->settingsquery as $setting){ 
		$server_address= $setting->serveradd; 
		$server_name = $setting->smtpname;
		$server_username = $setting->smtpusername;
		$server_password = $setting->smtppass;
		$server_port = $setting->smtppo;
		$server_protocol = $setting->smtpsec; ?>
	  <p><php echo $server_address; ?></p>
	<p><php echo $server_name; ?></p>
	<p><php echo $server_username; ?></p>
	<p><php echo $server_password; ?></p>
	<p><php echo $server_port; ?></p>
	<p><php echo $server_protocol; ?></p>
	  <php } ?>-->

	  <!--
      <td></td>
	  -->
 <!--   </tr>
	<php } ?>
<  </table>-->
  <!--<php if (current_user_can('administrator')) { ?>-->
	<form action="<?php echo esc_url(admin_url('admin-post.php')) ?>" class="create-sub-form" method="POST">
		<p> Enter just the e-mail</p>
		<input type="hidden" name="action" value="createsubscriber">
		<input type="hidden" name="my-noncem" value="<?php echo wp_create_nonce('my-noncem'); ?>">
		<input type="text" name="incomingsubscriber" placeholder="e-mail">
		<button> Subscribe </button>
	</form>
	<!--<php } ?>-->


<?php get_footer(); ?>