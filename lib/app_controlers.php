<?php
/**
 * APP Controllers
 */
class app_controlers {
	
	public $Controller;
	public $Model;
	public $layout = 'default';
	public $fileview;
	public $NewCrontime;

	function __construct() {
		add_action('admin_menu', array($this, 'ica_admin_menu'));
		$this->UpdateOptions();
	}
	
	function ica_admin_menu() {
		add_options_page('Islamic Content Archive', 'Islamic Content Archive', 'manage_options',ICA_Page_SLUG, array($this, 'settings_page'));
	}
	
	public function UpdateOptions()
	{
		if(!empty($_POST)){
			foreach ($_POST as $key => $value) {
				// if post name start wthi ica_
				
				if(substr($key, 0, strlen(ICA_Input_SLUG) ) === ICA_Input_SLUG){
					/*if($key == ICA_Input_SLUG.'cronjobtime'){
						wp_clear_scheduled_hook( 'ica_cronjob' );
						//$this->NewCrontime = $_POST[ICA_Input_SLUG.'cronjobtime'];
						//add_action('wp', array(this,'Addcronjob'));
					}*/
					$array[] = $this->_UpdateOptions($key,$value);
				}
			}
			if ( isset($_POST[ICA_Input_SLUG."-settings-page"]))
			{
				add_action( 'admin_notices', [$this,'alert_success']);
			}
			
		}
	}
	
	private function _UpdateOptions($key,$value = null)
	{
		if($key){
			$old_option = get_option($key);
			if($old_option !== false){
				// update
				return update_option($key,$value,true);
			}else{
				// add
				return add_option($key,$value); 
			}
		}
	}
	function alert_success() {
		$class = 'notice notice-success is-dismissible';
		$message = __( 'Done');
		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
	}

	public function loadModel($modelname='')
	{
		$modelname = $this->preloadfilename($modelname,'model');
		$this->Model = str_replace('.php','',$modelname);
		$path = ICA_Modelspath.$modelname;
		if (file_exists($path)) {
			include_once $path;
			$this->Model = new $this->Model();
		} else {
			echo sprintf("Model <b>%s</b> not found in path <b>%s</b>",$modelname,ICA_Modelspath);
		}
	}
	
	public function loadController($controllername='')
	{
		
		$controllername = $this->preloadfilename($controllername);
		$this->Controller = str_replace('.php','',$controllername);
		$path = ICA_Controlerspath.$controllername;
		if (file_exists($path)) {
			include_once $path;
			$this->Controller = new $this->Controller();
		} else {
			echo sprintf("Controller <b>%s</b> not found in path <b>%s</b>",$controllername,ICA_Controlerspath);
		}
	}
	public function loadView($filename='')
	{
		//$filename = str_replace('.php','',$filename).'.php';
		$layoutpath = ICA_Layoutpath.ICA_DS.str_replace('.php','',$this->layout).'.php';
		if(file_exists($layoutpath)){
			$this->fileview = str_replace('.php','',$filename);
			$mainViewFile = $this->inziliation_view_file($filename);
			if(!file_exists($mainViewFile)){
				echo sprintf("View File <b>%s</b> not found in path <b>%s</b>",$filename.'.php',ICA_Viewspath);
			}else{
				
				include_once $layoutpath;
			}
		}else{
			echo sprintf("Layout <b>%s</b> not found in path <b>%s</b>",$this->layout,ICA_Layoutpath);
		}

	}
    public function inziliation_view_file($fileview='')
	{
		if($fileview){
			$fileview = str_replace('.php','',$fileview).'.php';
			$path = ICA_Viewspath.$fileview;
			return $path;
		}
		return ;
	}	
	private function preloadfilename($name='',$type='controller')
	{
		return  str_replace('.php','',$name).'_'.$type.'.php';
	}
	
	function settings_page() {
		if(isset($_GET['tab'])){
			$tab = sanitize_text_field($_GET['tab']);
		}else{
			$tab = '';
		}
		switch ($tab) {
			case 'language':
				$this->loadController('language');
				break;
			case 'options':
				$this->loadController('options');
				break;
			case 'categories':
				$this->loadController('categories');
				break;
			default:
				$this->loadController('language');
				break;
		}
	}

}
new app_controlers;