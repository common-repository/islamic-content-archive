<?php
$Html = new html_helper();
global $categories,$ica_categories_lang;
$category_slug = esc_attr($_GET['cat_slug']);

$ica_lang = get_option(ICA_Input_SLUG.'language');
$link = null;
$jsoncaturl = null;
$cat_options = [];
if(isset($ica_categories_lang[$ica_lang][$category_slug])){
	$link = $ica_categories_lang[$ica_lang][$category_slug]['url'];
	$jsoncaturl = $ica_categories_lang[$ica_lang][$category_slug]['cat'];
	$slug = $category_slug.'_'.$ica_lang;
	$cat_options = $Html->categoryFromTransient($jsoncaturl,$slug);

}

?>
<div class="category-head">
	<table width="100%">
		<tr>
			<td width="80px"><span class="category-logo"><?php echo ica_cat_logo($category_slug,array('width'=>'80px','class'=>$category_slug)) ?></span></td>
			<td><h1 class="category-title"><a target="_blank" href="<?php echo $link; ?>"><?php echo $this->getLang($category_slug); ?></a></h1></td>
		</tr>
	</table>

</div>
<hr />
<?php
	echo $Html->Input('checkbox',array('name'=>'category_'.$ica_lang.'_'.$category_slug.'[]','options'=>$cat_options));
?>
