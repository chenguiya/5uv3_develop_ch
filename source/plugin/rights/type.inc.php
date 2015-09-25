<?php
if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

global $_G;
loadcache('plugin');
$_setting = $_G['cache']['plugin']['rights'];

if (submitcheck('rightstypesubmit')) {
	if (is_array($_POST['delete'])) {
		C::t('#rights#plugin_rights_type')->delete($_POST['delete']);
		C::t('#rights#plugin_rights')->update_by_typeid($_POST['delete'], array('typeid'=>0));
	}
	if (is_array($_POST['newtype'])) {
		$_POST['newtype'] = dhtmlspecialchars(daddslashes($_POST['newtype']));
		foreach ($_POST['newtype'] AS $key => $val) {
			if (trim($val)) {
				C::t('#rights#plugin_rights_type')->insert(array('typename' => trim($val)));
			}
		}
	}
}

showtips('');
showformheader('plugins&operation=config&do=25&identifier=rights&pmod=type&');
showtableheader();
showtablerow('class="header"', array('', ''), array(
	cplang('delete'),
	lang('plugin/rights', 'rights_type'),
));

showtablerow('', '', array(
	'<input class="checkbox" type="checkbox" value ="" disabled="disabled" >',
	lang('plugin/rights', 'rights_type_default'),
));
foreach (C::t('#rights#plugin_rights_type')->range(0, 0, 'ASC') as $result) {
	showtablerow('', '', array(
		'<input class="checkbox" type="checkbox" name = "delete[]" value = "'.$result['id'].'" >',
		$result['typename'],
	));
}
echo <<<EOT
<script type="text/JavaScript">
	var rowtypedata = [
		[[1,''], [1,'<input type="text" class="txt" size="30" name="newtype[]">']],
	];
</script>
EOT;
echo '<tr><td></td><td colspan="2"><div><a href="###" onclick="addrow(this, 0)" class="addtr">'.$lang['add_new'].'</a></div></td></tr>';
showsubmit('rightstypesubmit', 'submit', 'select_all');
showtablefooter();
showformfooter();