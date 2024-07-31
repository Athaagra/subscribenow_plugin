<?php
class sn_GetSubscribers{
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
		if (isset($_GET['email']) and !wp_verify_nonce( sanitize_text_field( wp_unslash ($_POST['email'])),'name_of_my_action')) $temp['email'] =  sanitize_text_field( wp_unslash ($_GET['email']));
		if (isset($_GET['subject']) and !wp_verify_nonce( sanitize_text_field( wp_unslash ($_POST['subject'])),'name_of_my_action')) $temp['subject'] =  sanitize_text_field( wp_unslash ($_GET['subject']));
		if (isset($_GET['message']) and !wp_verify_nonce( sanitize_text_field( wp_unslash ($_POST['message'])),'name_of_my_action')) $temp['message'] =  sanitize_text_field( wp_unslash ($_GET['message']));
		if (isset($_GET['severadd']) and !wp_verify_nonce( sanitize_text_field( wp_unslash ($_POST['serveradd'])),'name_of_my_action')) $temp['serveradd'] =  sanitize_text_field( wp_unslash ($_GET['serveradd']));
		if (isset($_GET['smtpname']) and !wp_verify_nonce( sanitize_text_field( wp_unslash ($_POST['smtpname'])),'name_of_my_action')) $temp['smtpname'] =  sanitize_text_field( wp_unslash ($_GET['smtpname']));
		if (isset($_GET['smtpusername']) and !wp_verify_nonce( sanitize_text_field( wp_unslash ($_POST['smtpusername'])),'name_of_my_action') ) $temp['smtpusername'] =  sanitize_text_field( wp_unslash ($_GET['smtpusername']));
		if (isset($_GET['smtppass'])  and !wp_verify_nonce( sanitize_text_field( wp_unslash ($_POST['smtppass'])),'name_of_my_action')) $temp['smtppass'] =  sanitize_text_field( wp_unslash ($_GET['smtppass']));
		if (isset($_GET['smtppo'])  and !wp_verify_nonce( sanitize_text_field( wp_unslash ($_POST['smtppo'])),'name_of_my_action')) $temp['smtppo'] =  sanitize_text_field( wp_unslash ($_GET['smtppo']));
		if (isset($_GET['smtpsec'])  and !wp_verify_nonce( sanitize_text_field( wp_unslash ($_POST['smtpsec'])),'name_of_my_action')) $temp['smtpsec'] =  sanitize_text_field( wp_unslash ($_GET['smtpsec']));
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