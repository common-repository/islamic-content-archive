<?php

if (!function_exists('pr')) {
	function pr($data) {
		echo "<pre>";
		print_r($data);
		echo "</pre>";

	}

}

if (!function_exists('ica_cat_logo')) {
	function ica_cat_logo($slug, $attr = array('width'=>'80px')) {
		global $categories;
		$_attr = NULL;
		if (!empty($attr) && is_array($attr)) {
			foreach ($attr as $key => $value) {
				$_attr .= sprintf('%s="%s" ', $key, $value);
			}

		}
		if (!empty($categories[$slug]['logo'])) {
			return sprintf('<img src="%s" %s />', ICA_Logourl . $categories[$slug]['logo'], $_attr);
		}
		return NULL;
	}

}

if (!function_exists('ica_cat_icon')) {
	function ica_cat_icon($slug, $attr = array('width'=>'80px')) {
		global $categories;
		$_attr = NULL;
		if (!empty($attr) && is_array($attr)) {
			foreach ($attr as $key => $value) {
				$_attr .= sprintf('%s="%s" ', $key, $value);
			}

		}
		if (file_exists(ICA_Iconpath . $slug)) {
			return sprintf('<img src="%s" %s />', ICA_Iconurl . $slug, $_attr);
		}
		return NULL;
	}

}

if (!function_exists('ica_cat_flags')) {
	function ica_cat_flags($slug, $attr = array('width'=>'30px')) {
		global $categories;
		$_attr = NULL;
		if (!empty($attr) && is_array($attr)) {
			foreach ($attr as $key => $value) {
				$_attr .= sprintf('%s="%s" ', $key, $value);
			}
		}
		if (file_exists(ICA_Flagspath . $slug)) {
			return sprintf('<img src="%s" %s />', ICA_Flagsurl . $slug, $_attr);
		}
		return NULL;
	}

}

if (!function_exists('set_value')) {
	function set_value($key) {
		if (!empty($_POST[$key])) {
			return $_POST[$key];
		} else {
			return get_option($key);
		}
	}

}

if (!function_exists('ica_get_data')) {
	function ica_get_data($url = NULL) {
 
		$response = wp_remote_get($url,[ 'timeout' => 5000, 'httpversion' => '1.1','sslverify' => false]);
		if ( is_array( $response ) && ! is_wp_error( $response ) && !empty($response['body']) ) {
			return json_decode($response['body']);
		}
		return;
	}

}

if (!function_exists('ica_set_transient')) {
	function ica_set_transient($slug, $data) {
		global $wpdb;
		if (is_array($data)) {
			$data = json_encode($data);
		}
		return $wpdb->insert($wpdb->prefix . ICA_DB_Table, array('ica_key' => $slug, 'ica_value' => $data), array('%s', '%s'));
	}

}

if (!function_exists('ica_get_transient')) {
	function ica_get_transient($slug) {
		global $wpdb;
		$result = array();
		$tablename = $wpdb->prefix . ICA_DB_Table;
		$return = $wpdb->get_row("SELECT * FROM `$tablename` WHERE `ica_key`='$slug'");
		if ($return) {
			$result['id'] = $return->id;
			$result['ica_key'] = $return->ica_key;
			$result['ica_value'] = json_decode($return->ica_value);
			return $result;
		}
		return NULL;
	}

}
if (!function_exists('ica_do_transient')) {
	function ica_do_transient($slug, $data = NULL) {
		$old = ica_get_transient($slug);
		if (empty($old)) {
			ica_set_transient($slug, $data);
		}
		return ica_get_transient($slug);

	}

}

if (!function_exists('fun_loadlang')) {

	function fun_loadlang() {
		$__lang = get_option(ICA_Input_SLUG . 'language');
		if ($__lang) {
			$def_lang = get_option(ICA_Input_SLUG . 'language') . '.php';
			$path = ICA_Langpath . $def_lang;
			if (file_exists($path)) {
				include_once $path;
				return $lang;
			} else {
				add_action( 'admin_notices', function() use($def_lang){
                    $class = 'notice notice-error';
                    $message = sprintf("Lnaguage File <b>%s</b> not found in path <b>%s</b>", $def_lang, ICA_Langpath);
                    printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );   
                } );
				return array();
			}
		}else{
			return array();
		}

	}

}
?>