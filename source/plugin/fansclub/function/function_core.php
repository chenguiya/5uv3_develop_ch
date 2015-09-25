<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function multi_array_sort($multi_array, $sort_key, $sort=SORT_ASC) {
	if (is_array($multi_array)) {
		foreach ($multi_array as $key => $row_array) {
			if (is_array($row_array)) {
				$key_array[] = $row_array[$sort_key];
			} else {
				return false;
			}
		}
	} else {
		return false;
	}
	array_multisort($key_array, $sort, $multi_array);
	return $multi_array;
}
?>