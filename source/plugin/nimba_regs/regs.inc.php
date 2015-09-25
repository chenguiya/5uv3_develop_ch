<?php
/*
 * 主页：http://addon.discuz.com/?@ailab
 * 人工智能实验室：Discuz!应用中心十大优秀开发者！
 * 插件定制 联系QQ594941227
 * From www.ailab.cn
 */
 
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
@require_once DISCUZ_ROOT.'./source/discuz_version.php';
@require_once DISCUZ_ROOT.'./source/plugin/nimba_regs/function/regs.fun.php';
$langvar=lang('plugin/nimba_regs');
loadcache('plugin');
$vars=$_G['cache']['plugin']['nimba_regs'];	
$group=empty($vars['regs_group'])? 10:$vars['regs_group'];
echo '<table class="tb tb2 " id="tips">
	<tr>
		<th  class="partition">'.$langvar['tips'].'</th>
	</tr>
	<tr>
		<td class="tipsblock" s="1">
			<ul id="tipslis">
				<li>'.$langvar['tip1'].'</li>
				<li>'.$langvar['tip2'].'</li>
				<li>'.$langvar['tip3'].'</li>
			</ul>
		</td>
	</tr>
</table><br>';
if($_POST['regnum']||$_GET['reging']){
	echo '<br>';
	if(submitcheck('submit')&&!$_GET['reging']){
		$usertype=empty($_POST['usertype'])? 5:intval($_POST['usertype']);
		if(empty($_POST['pw'])) $pw='';
		else $pw=addslashes($_POST['pw']);
		$reging=intval($_POST['regnum']);
		echo "<script>window.location.href='".ADMINSCRIPT."?action=plugins&operation=config&do=$pluginid&identifier=nimba_regs&pmod=regs&reging=$reging&pw=$pw&usertype=$usertype&num=0&dateline=".time()."';</script>";
	}else{
		$reging=intval($_GET['reging']);
		$usertype=intval($_GET['usertype']);
		$pw=addslashes($_GET['pw']);
		$num=intval($_GET['num']);
		$dateline=intval($_GET['dateline']);
		if($num<$reging){
			echo $langvar['tip4'].($num+1).$langvar['tip5'];
			@require_once DISCUZ_ROOT.'./source/plugin/nimba_regs/function/regs.fun.php';
			$uid=@creatuser($usertype,$group,$pw);
			if($uid){
				$num++;
				echo "<script>window.location.href='".ADMINSCRIPT."?action=plugins&operation=config&do=$pluginid&identifier=nimba_regs&pmod=regs&reging=$reging&pw=$pw&usertype=$usertype&num=$num&dateline=$dateline';</script>";
			}else echo "<script>window.location.href='".ADMINSCRIPT."?action=plugins&operation=config&do=$pluginid&identifier=nimba_regs&pmod=regs&reging=$reging&pw=$pw&usertype=$usertype&num=$num&dateline=$dateline';</script>";
		}else{
			echo $langvar['tip6'].(time()-$dateline).$langvar['tip7'].'<a href="'.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=nimba_regs&pmod=regs">'.$langvar['tip8'].'</a>';
		}
	}
}else include template('nimba_regs:regs');
?>