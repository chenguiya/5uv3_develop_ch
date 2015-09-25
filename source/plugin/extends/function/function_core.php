<?php
/**
 * 获取帖子列表
 * @param unknown $tidarray
 * @return multitype:multitype:string unknown Ambigous <string> Ambigous <string, unknown, mixed, number, multitype:, multitype:string number , multitype:string , multitype:multitype: multitype:string  , NULL, multitype:string NULL mixed Ambigous <> >
 */
function getthreadsbytids($tidarray) {
	global $_G;

	require_once libfile('function/extends');
	$threadlist = array();
	if(!empty($tidarray)) {
		include_once libfile('function_misc', 'function');
		require_once libfile('function/extends');
		$fids = $resultnew = array();
		foreach(C::t('forum_thread')->my_fetch_all_by_tid($tidarray, 0, 0, 'dateline') as $result) {
			$resultnew['url'] = 'forum.php?mod=viewthread&tid='.$result['tid'];
			if ($result['attachment'] == 2) {
				// $attachment = getattachment($result['tid'], 1);
				// $resultnew['imgUrl'] = $attachment[0];
				
				
				// 2015-08-24 zhangjh 根据tid取附件ID
				$attachment = getattachment($result['tid'], 1, TRUE);
				$resultnew['imgUrl'] = getforumimg($attachment[0], 0, 260, 200, 2);
				
			} else {
				$resultnew['imgUrl'] = $_G['style']['tpldir'].'/common/images/lanmu-default.png';
			}
			$resultnew['title'] = $result['subject'];
			$resultnew['authorThumbUrl'] = avatar($result['authorid'], 'small', 1);
			$resultnew['authorName'] = $result['author'];
			$resultnew['authorId'] = $result['authorid'];
			
			if (get_member_info_by_uid($result['authorid'], 'gender') == 0) {
				$resultnew['sex'] = 'secrecy';
			} elseif (get_member_info_by_uid($result['authorid'], 'gender') == 1) {
				$resultnew['sex'] = 'man';
			} elseif (get_member_info_by_uid($result['authorid'], 'gender') == 2) {
				$resultnew['sex'] = 'femen';
			}
			$resultnew['time'] = date('Y-m-d', $result['dateline']);
			$resultnew['replay'] = $result['replies'];
			$threadlist[$result['tid']] = $resultnew;
		}		
	}
	// 	var_dump($threadlist);die;
	return $threadlist;
}