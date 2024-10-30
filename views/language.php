

<div class="category-head">
		<table width="100%">
		<tr>
			<td width="80px"><span class="category-logo"><?php echo ica_cat_icon('language.png',array('width'=>'80px')) ?></span></td>
			<td><h1 class="category-title"><?php echo $this->getLang('control-language') ?></h1></td>
		</tr>
	</table>
</div>
<hr />
<?php
echo $this->Input('select', array('name' => 'language', 'options' => $this->getLangList(), 'label' => $this->getLang('label-selectlang')));
?>