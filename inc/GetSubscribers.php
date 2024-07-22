<?php
class suno_GetSubscribers{
	function __construct(){
		global $wpdb;
		$tablename = $wpdb->prefix . 'subscribers';
		$query_q = 'email';
		$query_m = 'message';
		$query_a = 'serveradd';
		$this->args = $this->getArgs();
		//$this->placeholders = $this->createPlaceholders();
		
		//$query = "SELECT * FROM $tablename WHERE $query_q IS NOT NULL";
		//$countQuery = "SELECT COUNT(*) FROM $tablename";
		//$messageQuery = "SELECT * FROM $tablename WHERE $query_m IS NOT NULL ORDER BY ID DESC LIMIT 1";
		//$settingsQuery = "SELECT * FROM $tablename WHERE $query_a IS NOT NULL ORDER BY ID DESC LIMIT 1";
		//$query .= $this->createWhereText();
		//$countQuery .= $this->createWhereText();
		//$messageQuery .= $this->createWhereText();
		//$settingsQuery .= $this->createWhereText();
		//$query .= " LIMIT 100";
		/*$ourQuery = $wpdb->prepare("SELECT * FROM $tablename LIMIT 100");*/
		$this->subscribers = $wpdb->get_results($wpdb->prepare("SELECT * FROM $tablename WHERE $query_q IS NOT NULL"));
		$this->messagequery = $wpdb->get_results($wpdb->prepare("SELECT * FROM $tablename WHERE $query_m IS NOT NULL ORDER BY ID DESC LIMIT 1"));
		$this->settingsquery = $wpdb->get_results($wpdb->prepare("SELECT * FROM $tablename WHERE $query_a IS NOT NULL ORDER BY ID DESC LIMIT 1"));
		$this->counts = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $tablename"));
		}
	function getArgs(){
		//$temp = array(
		//	'email'=> sanitize_text_field($_GET['email']),
		//);
		$temp = [];
		if (isset($_GET['email']) and !empty($_GET['email']) and !wp_verify_nonce($_POST['email'],'name_of_my_action')) $temp['email'] = ! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_GET['email'] ) ) , 'name_of_my_action' );//sanitize_text_field($_GET['email']);
		if (isset($_GET['subject']) and !wp_verify_nonce($_POST['subject'],'name_of_my_action')) $temp['subject'] = ! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_GET['subject'] ) ) , 'name_of_my_action' ); //sanitize_text_field($_GET['subject']);
		if (isset($_GET['message']) and !empty($_GET['severadd']) and !wp_verify_nonce($_POST['message'],'name_of_my_action')) $temp['message'] = ! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_GET['message'] ) ) , 'name_of_my_action' ); //sanitize_text_field($_GET['message']);
		if (isset($_GET['severadd']) and !empty($_GET['severadd']) and !wp_verify_nonce($_POST['serveradd'],'name_of_my_action')) $temp['serveradd'] = ! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_GET['serveradd'] ) ) , 'name_of_my_action' );//sanitize_text_field($_GET['serveradd']);
		if (isset($_GET['smtpname']) and !wp_verify_nonce($_POST['smtpname'],'name_of_my_action')) $temp['smtpname'] =! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_GET['smtpname'] ) ) , 'name_of_my_action' ); //sanitize_text_field($_GET['smtpname']);
		if (isset($_GET['smtpusername']) and !empty($_GET['smtpusername']) and !wp_verify_nonce($_POST['smtpusername'],'name_of_my_action') ) $temp['smtpusername'] =! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_GET['smtpusername'] ) ) , 'name_of_my_action' ); //sanitize_text_field($_GET['smtpusername']);
		if (isset($_GET['smtppass'])  and !wp_verify_nonce($_POST['smtppass'],'name_of_my_action')) $temp['smtppass'] = ! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_GET['smtppass'] ) ) , 'name_of_my_action' );//sanitize_text_field($_GET['smtppass']);
		if (isset($_GET['smtppo'])  and !wp_verify_nonce($_POST['smtppo'],'name_of_my_action')) $temp['smtppo'] = ! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_GET['smtppo'] ) ) , 'name_of_my_action' );//sanitize_text_field($_GET['smtppo']);
		if (isset($_GET['smtpsec'])  and !wp_verify_nonce($_POST['smtpsec'],'name_of_my_action')) $temp['smtpsec'] = ! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_GET['smtpsec'] ) ) , 'name_of_my_action' );//sanitize_text_field($_GET['smtpsec']);
		return array_filter($temp, function($x){
			return $x;
			});
	}
	
	function createPlaceholders(){
		return array_map(function($x){
			return $x;
		}, $this->args);
	}
	
	function createWhereText(){
		$whereQuery= "";
		
		if (count($this->args)){
			$whereQuery = "WHERE ";
		}
		
		$currentPosition = 0;
		foreach($this->args as $index => $item){
			$whereQuery .= $this->specificQuery($index);
			if ($currentPosition!=count($this->args) - 1){
				$whereQuery .= " AND ";
			}
			$currentPosition++;
 		}
		return $whereQuery;
	}
	
	function specificQuery($index){
		switch($index){
		 case "minweight":
			return "petweight >= %d";
		 case "maxweight":
			return "petweight >= %d";
		 case "minyear":
			return "birthyear >= %d";
		 case "maxyear":
			return "birthyear <= %d";
		 default:
			return $index . " = %s";
		}
	}
	
	
	}
?>