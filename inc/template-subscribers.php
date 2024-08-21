<?php

require_once plugin_dir_path(__FILE__) . 'GetSubscribers.php';

$getSubscribers = new suno_GetSubscribers();

get_header(); ?>

	<form action="<?php echo esc_url(admin_url('admin-post.php')) ?>" class="create-sub-form" method="POST">
		<p> Enter just the e-mail</p>
		<input type="hidden" name="action" value="createsubscriber">
		<input type="hidden" name="my-noncem" value="<?php echo esc_html(wp_create_nonce('my-noncem')); ?>">
		<input type="text" name="incomingsubscriber" placeholder="e-mail">
		<button> Subscribe </button>
	</form>
	<!--<php } ?>-->


<?php get_footer(); ?>
