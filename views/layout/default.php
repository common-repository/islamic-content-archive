<form method="post" action="<?php admin_url( 'options-general.php?page='.ICA_Page_SLUG ); ?>">
<?php
	echo wp_nonce_field(ICA_Input_SLUG,ICA_Input_SLUG."-settings-page"); 
	
	$HtmlHelper = new html_helper();
	$HtmlHelper->ica_admin_tabs();
	$HtmlHelper->MainContent($mainViewFile);
?>
    
      <?php submit_button($HtmlHelper->getLang('btn-updatesetting')); ?>
      <input type="hidden" name="ilc-settings-submit" value="Y" />
    
</form>