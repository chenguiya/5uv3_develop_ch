<?php
if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

global $_G;

if (submitcheck('columnsubmit')) {
	
// 	var_dump($_POST);die;
	
	if (is_array($_POST['delete'])) {
		C::t('#extends#plugin_extends_column')->delete($_POST['delete']);
	}
	if (is_array($_POST['newcolumn'])) {
		$_POST['newcolumn'] = dhtmlspecialchars(daddslashes($_POST['newcolumn']));
		foreach ($_POST['newcolumn']['keyword'] AS $key => $val) {
			if (trim($val)) {
				C::t('#extends#plugin_extends_column')->insert(array('displayorder' => intval($_POST['newcolumn']['displayorder'][$key]), 'keyword' => trim($val)));
			}
		}
	}
}

showtips('<li>栏目通过tag标签聚合，关键词即为tag标签。</li>');
showformheader('plugins&operation=config&do=33&identifier=extends&pmod=column');
showtableheader();
showtablerow('class="header"', array('', ''), array(
	'删除',
	'排序',
	'关键词',
	'操作'
));
foreach (C::t('#extends#plugin_extends_column')->range(0, 0, 'ASC') as $result) {
	showtablerow('', '', array(
		'<input class="checkbox" type="checkbox" name ="delete[]" value ="'.$result['id'].'" >',
		$result['displayorder'],
		$result['keyword'],
		'<a href="">推荐</a>&nbsp;|&nbsp;<a href="">启用</a>',
	));
}
echo <<<EOT
<script type="text/JavaScript">
	var rowtypedata = [
		[[1,''], [1,'<input type="text" class="txt" size="30" name="newcolumn[displayorder][]" value="0">'], [1,'<input type="text" class="txt" size="30" name="newcolumn[keyword][]">']], [1,'<input type="text" class="hidden" name="newcolumn[position][]" value="0">', [1,'<input type="text" class="hidden" name="newcolumn[open][]" value="0">']
	];
</script>
EOT;
echo '<tr><td></td><td colspan="2"><div><a href="###" onclick="addrow(this, 0)" class="addtr">'.$lang['add_new'].'</a></div></td></tr>';
showsubmit('columnsubmit', 'submit', 'select_all');
showtablefooter();
showformfooter();