<?php

class app_cronjob extends app_models {
    public $pageCountKey;
    public $curncyPageKey;
	public $perPageCount = 20;

	function __construct() {
		parent::__construct();
		$this->pageCountKey =  get_option(ICA_Input_SLUG.'language')."-api-all-p-count-";
		$this->curncyPageKey =  get_option(ICA_Input_SLUG.'language')."-api-curency-page-";
	}
	public function steTransientKeys($slug)
	{
		$this->pageCountKey = $this->pageCountKey.$slug."-";
		$this->curncyPageKey = $this->curncyPageKey.$slug."-";
	}
    private function getCache($key)
    {
        $data =  get_transient( $key );
        if($data !== false)
        {
            return (int) $data;   
        }
        return set_transient( $key, 1);
    }
    private function setCache($key, $value)
    {
         return set_transient( $key, $value);
    }
    private function incresCurnacrpage($categoryId){

            $oldValue = get_transient($this->curncyPageKey.$categoryId);
            $incress = ($oldValue+1);
            return set_transient( $this->curncyPageKey.$categoryId, $incress);
    }
    private function resteCache($id = 6){
        set_transient( $this->curncyPageKey.$id,0);
        set_transient( $this->pageCountKey.$id,0);
    }
	public function CategoryBySlug() {
		return $this->getCategoryBySlug();
	}

	public function importUrl() {
		$array = array();
		$list = $this->CategoryBySlug();
		
		if (is_array($list)) {
			foreach ($list as $key => $value) {
				$option_slug = $value["option_slug"];
				$this->steTransientKeys($option_slug);

				foreach ($value['import_url'] as $key => $_value) {
					$GetData = $this->getData($_value.'&count='.$this->perPageCount);
					if(isset($GetData->status ) && $GetData->status == "ok")
					{
						$categoryId = $GetData->category->id;
						//$this->resteCache($categoryId);
						// set or get page count
    					$allPageCount = $this->getCache($this->pageCountKey.$categoryId);
    					$pageCount = (int) $GetData->pages;
    					
    					// get curancr page number
    					$curancyPage = $this->getCache($this->curncyPageKey.$categoryId);
    					
 					    // if empty pr page count is greate than  page count
    					if(empty($allPageCount) || $allPageCount <= 0 || $allPageCount < $pageCount || $allPageCount > $pageCount){
    					    $this->setCache($this->pageCountKey.$categoryId, $pageCount);
    					}
    					
    					// if curancy page > all page count reset curancy page to 1
    					if($curancyPage > $pageCount )
    					{
    					    $this->setCache($this->curncyPageKey.$categoryId,1);   
    					}
						
    					// get all page count
    					$allPageCount = $this->getCache($this->pageCountKey.$categoryId);
    					
    					if( $allPageCount > 0 && $curancyPage <= $allPageCount){
    					    
    					   // get posts from json wih page
    					   $GetPostsData = $this->getData($_value.'&page='.$curancyPage.'&count='.$this->perPageCount);  
    					   $array[$value['option_slug']][] = $GetPostsData;
    					   
        					 // stop increment page if curany Page > all pahes 
        					if($curancyPage <= $allPageCount-1)
        					{
        					    // update curance page with one
        					    $this->incresCurnacrpage($categoryId);
        					}
    					  
    					}
    					/**header('Content-Type: application/json');
    					echo json_encode([
    					   // "incresCurnacrpage"=>$this->incresCurnacrpage($categoryId),
    					    "key"=> $this->curncyPageKey.$categoryId,
    					    'categoryId' => $categoryId,
    					    "pageCount" => $pageCount,
    					    'allPageCount'=>$allPageCount,
    					    'curancyPage'=>$curancyPage,
    					    'data'=>$array,
    					]);
    				    die;**/
					}
					
				}
			}
		}
		return $array;
	}

	public function getData($url = '') {
		$response = wp_remote_get($url,[ 'timeout' => 5000, 'httpversion' => '1.1','sslverify' => false]);
		if ( is_array( $response ) && ! is_wp_error( $response ) && !empty($response['body']) ) {
			return json_decode($response['body']);
		}
		return;
	}

	public function Parent_id($name = '', $taxonamy = 'category') {
		$idObj = term_exists($name, $taxonamy);
		if ($idObj) {
			return $idObj['term_id'];
		} else {
			$insert = (array)wp_insert_term($name, // the term
			$taxonamy, // the taxonomy
			array('slug' => $name, ));
			return $insert['term_id'];

		}
	}

	public function insertTags($tags = array(), $post_ID='') {
		$tags_title = array();
		if (!empty($tags) && is_array($tags)) {
			foreach ($tags as $key => $value) {
				$tags_title[] = $value->title;
			}
			return wp_set_post_tags($post_ID, join(",", $tags_title), true);
		}

	}

	public function insertCategory($cat, $parent='', $taxonamy = 'category') {
			$term_exists = term_exists($cat->title, $taxonamy, $parent );
			if(!empty($term_exists) && is_array($term_exists)){
				return $term_exists['term_id'];
			}
			$insert = wp_insert_term($cat->title, // the term
			$taxonamy, // the taxonomy
			array('description' => $cat->description, 'parent' => $parent, 'slug' => $cat->slug));
			if (is_object($insert)) {
				return $insert->term_id;
			} else {
				return $insert['term_id'];
			}

	}

	public function InsertImage($url = '', $post_id='') {
		// Add Featured Image to Post
		$image_url = $url;
		// Define the image URL here
		$upload_dir = wp_upload_dir();

		// Create image file name

		// Set upload folder
		$image_data = @file_get_contents($image_url);
		// Get image data
		$filename = basename($image_url);
		// Check folder permission and define file location
		if (wp_mkdir_p($upload_dir['path'])) {
			$file = $upload_dir['path'] . '/' . $filename;
		} else {
			$file = $upload_dir['basedir'] . '/' . $filename;
		}
		if (!file_exists($file)) {
			// Create the image  file on the server
			$putcontent = @file_put_contents($file, $image_data);
			if ($putcontent) {
				// Check image file type
				$wp_filetype = wp_check_filetype($filename, null);

				// Set attachment data
				$attachment = array('post_mime_type' => $wp_filetype['type'], 'post_title' => sanitize_file_name($filename), 'post_content' => '', 'post_status' => 'inherit');

				// Create the attachment
				$attach_id = wp_insert_attachment($attachment, $file, $post_id);

				// Include image.php
				require_once (ABSPATH . 'wp-admin/includes/image.php');

				// Define attachment metadata
				$attach_data = wp_generate_attachment_metadata($attach_id, $file);

				// Assign metadata to attachment
				wp_update_attachment_metadata($attach_id, $attach_data);
				// And finally assign featured image to post
				set_post_thumbnail($post_id, $attach_id);
			}
		}

	}

	public function InsertPosts($posts,$cateagories,$parent) {
		$post = array();
		$result = array();
		if (is_array($posts)) {
			foreach ($posts as $key => $value) {
				unset($value->categories);
				unset($value->author);
				unset($value->comments);
				//unset($value->attachments);
				unset($value->comment_count);
				unset($value->comment_status);
				unset($value->thumbnail);
				unset($value->custom_fields);
				unset($value->thumbnail_size);
				unset($value->thumbnail_size);
				// valid if post exist
				$post_id = $this->post_exists($value->title);
				$post['post_content'] = $value->content . '[ica_orginalurl]';
				$post['post_title'] = $value->title;
				$post['post_excerpt'] = $value->excerpt;
				$post['post_name'] = $value->slug;
				$post['post_category'] = array($cateagories,$parent);
				$post['post_author'] = 1;
				$post['post_status'] = 'publish';
				// if found update
				if ($post_id <= 0) {
					//else insert 1-post  2-tage  3-meta post orginal link
					try {
						$post_id = wp_insert_post($post);
						if(!is_wp_error($post_id)){
							//code...
							add_post_meta($post_id, 'orginal_url', $value->url);
							if(!empty($value->count_pages)){
								add_post_meta($post_id, 'count_pages', $value->count_pages);    
							}
							if(!empty($value->book_version)){
								add_post_meta($post_id, 'book_version', $value->book_version);    
							}
							if(!empty($value->book_publisher)){
								add_post_meta($post_id, 'book_publisher', $value->book_publisher);    
							}
							if(!empty($value->book_year)){
								add_post_meta($post_id, 'book_year', $value->book_year);    
							}
							if(!empty($value->book_url)){
								add_post_meta($post_id, 'book_url', $value->book_url);    
							}
							if(!empty($value->book_image)){
								add_post_meta($post_id, 'book_image', $value->book_image);    
							}
							if(!empty($value->author_name)){
								add_post_meta($post_id, 'author_name', $value->author_name);    
							}
        					if(!empty($value->attachments) && count($value->attachments) > 0){
        					    
        					    foreach($value->attachments as $img){
        					       if(isset($img->url)){
        					            $this -> InsertImage($img->url, $post_id);   
        					       }
        					       
        					    }     
        					    
        					}
							if (!empty($value->thumbnail_images->full)) {
								$this->InsertImage($value->thumbnail_images->full->url, $post_id);
							}
							if(!empty($value->tags)){
								$this->insertTags($value->tags, $post_id);
							}
							
						}
					} catch (\Throwable $th) {

					}
				}
				unset($post);
			}
		}
		//pr($posts);

	}

	public function InsetPost() {

		$html = new app_helpers();
		$lang = $html->class_lang;
		$posts = $this->importUrl();
		foreach ($posts as $key => $value) {

			$parentname = $lang[$key];
			$parent = $this->Parent_id($parentname);
			foreach ($value as $cat_key => $cat_value) {
				if (!empty($cat_value)) {
					$cat_id = $this->insertCategory($cat_value->category, $parent);
					$this->InsertPosts($cat_value->posts, $cat_id,$parent);
					sleep(1);
				}

			}

		}
		unset($html);
	}

	function post_exists($post_name, $post_type = 'post') {
		global $wpdb;
		$_posts = $wpdb->posts;
		$query = "SELECT `ID` FROM `$_posts` WHERE 1=1";
		$args = array();

		if (!empty($post_name)) {
			$query .= " AND `post_title` = '%s' ";
			$args[] = $post_name;
		}
		if (!empty($post_type)) {
			$query .= " AND `post_type` = '%s' ";
			$args[] = $post_type;
		}

		if (!empty($args)) {
			return $wpdb->get_var($wpdb->prepare($query, $args));
		}

		return 0;
	}

}

$ica_cronjob = new app_cronjob();
//$ica_cronjob->InsetPost();
?>