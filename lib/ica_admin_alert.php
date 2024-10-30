<?php
function ica_admin_errors_notice() {
	global $ica_lang;	
	$result = array();
	
	$link = admin_url("options-general.php?page=".ICA_Page_SLUG."&tab=language");
	$ica_cronjob = get_option(ICA_Input_SLUG . 'cronjobtime');
	$ica_ch_lang = get_option(ICA_Input_SLUG . 'language');
	if (empty($ica_ch_lang)) {
		$result[] = "<p>".sprintf($ica_lang['alert-ica_must_select_lang'],$link)."</p>";
	}
	
	$link = admin_url("options-general.php?page=".ICA_Page_SLUG."&tab=options");
	$ica_cronjob = get_option(ICA_Input_SLUG . 'cronjobtime');
	if (empty($ica_cronjob)) {
		$result[] = "<p>".sprintf($ica_lang['alert-ica_must_select_cron_time'],$link)."</p>";
	}
	
	$link = admin_url("options-general.php?page=".ICA_Page_SLUG."&tab=options");
	$ica_source = get_option(ICA_Input_SLUG . 'source');
	if (empty($ica_source)) {
		$result[] = "<p>".sprintf($ica_lang['alert-ica_must_add_source_link'],$link)."</p>";
	}	
	
	return $result;
}


function display_ica_admin_errors_notice() {
	global $ica_lang;	
	$errors = ica_admin_errors_notice();
	if(is_array($errors) && !empty($errors)){
		$class = "error";
		echo "<div class=\"$class\"><h3 class='ica_logo'>".$ica_lang['label-ica_plugin_title']."</h3><ol>";
		foreach ($errors as $key => $value) {
			echo "<li>".$value."</li>";
		}
		echo "</ol></div>";
	}

}
//add_action('admin_notices', 'display_ica_admin_errors_notice');
?>