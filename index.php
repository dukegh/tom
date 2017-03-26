<?php

header('Access-Control-Allow-Origin: *');

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$itemId = isset($_GET['itemId']) ? (int) $_GET['itemId'] : 0;
$s = isset($_GET['id']) ? $_GET['id'] : "";
if ((string) $id == $s) {
	$url = "http://www.ex.ua/view/$id";
	$title = "Noname";
	$content = file_get_contents($url);
	foreach (preg_split('/\n/', $content) as $line) {
		if (preg_match('/<h1>(.*)<\/h1>/', $line, $m)) { 
			$title = $m[1];
			break;
		}
	}
	echo $title;
} elseif ($itemId) {
	$url = "http://www.ex.ua/view/" . $itemId;
	$content = file_get_contents($url);
	$res = [];
	$isDescr = false;
	$isFile = false;
	$fname = "";
	$fsize = "";
	$descr = "";
	$ids = [];
	foreach (preg_split('/\n/', $content) as $line) {
		if (preg_match("/^<img src='([^']+)' width/", $line, $m)) $res['img'] = $m[1];
		if (preg_match("#^<h1>([^<]+)</h1><br>#", $line, $m)) $res['title'] = $m[1];
		if ($isDescr && ! preg_match('/^\s*$/', $line)) {
			$isDescr = false;
			$res['description'] = $line;
		}
		if (preg_match('#</small><p>\s*$#', $line)) $isDescr = true; 
		if (preg_match("#/get/\d+' title='([^']+)'#", $line, $m)) $fname = $m[1];
		if ($isFile && preg_match("#<a href='/search\?s=md5:.*</a><br>(.*)$#", $line, $m)) { $descr = $m[1]; }
		if ($isFile && preg_match("#^\s+<b>([0-9,]+)</b>#", $line, $m)) { $fsize = $m[1]; }
		if ($isFile && preg_match("#<a href='/load/(\d+)' rel='nofollow'>#", $line, $m)) {
			$isFile = false;
			if (! isset($res['list'])) $res['list'] = [];
			$res['list'][] = ['fname' => $fname, 'size' => $fsize, 'description' => $descr, 'id' => $m[1]];
			$ids[] = $m[1];
		}
		if (preg_match("#'return play_index#", $line)) $isFile = true;
	}
	$res['ids'] = implode(',', $ids);
	echo json_encode($res);
} elseif (preg_match('/^i(\d+)$/', $s, $m)) {
	$itemId = $m[1];
	$url = "http://www.ex.ua/view/" . $itemId;
	$content = file_get_contents($url);
	$res = [];
	$isDescr = false;
	foreach (preg_split('/\n/', $content) as $line) {
		if (preg_match("/^<img src='([^']+)' width/", $line, $m)) $res['img'] = $m[1];
		if (preg_match("#^<h1>([^<]+)</h1><br>#", $line, $m)) $res['title'] = $m[1];
		if ($isDescr && ! preg_match('/^\s*$/', $line)) {
			$isDescr = false;
			$res['description'] = $line;
		}
		if (preg_match('#</small><p>\s+$#', $line)) $isDescr = true; 
	}
	$res['id'] = $itemId;
	$res['fsize'] = 0;
	$res['url'] = "http://www.ex.ua/view/$itemId";
	echo json_encode([$res]);
} else {
	$url = "http://www.ex.ua/search?s=" . urlencode($s);
	$content = file_get_contents($url);
	file_put_contents("/tmp/out.txt",$content);
	$list = "";
	foreach (preg_split('/\n/', $content) as $line) {
		if (preg_match('/<tr><td><a href=.\/\d/', $line)) { 
			$list .= $line;
		}
	}
	$res = [];
	foreach (preg_split('/<tr><td>/', $list) as $item) {
		if (preg_match("#href='([^']+)'.*src='([^']+)'.*alt='([^']+)'.*small><p>(.*)<small>([^<]+)<#", $item, $m)) {
			$id = preg_replace('/^.*\//', '', $m[1]);
			$res[] = ['url' => $m[1], 'img' => $m[2], 'title' => $m[3], 'description' => $m[4], 'fsize' => $m[5], 'id' => $id];
		}
	}
	echo json_encode($res);
}
?>
