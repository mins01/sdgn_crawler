<?
require('class.Mproxy.php');
require('lib.php');
$mp = new Mproxy();


	$cookieRaw = null;
	$opts= array();
	$opts[CURLOPT_USERAGENT] = "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.102 Safari/537.36"; //User Agent 설정
$fn = 'data.tmp/lists.txt';
if(!is_file($fn)){
	$able = true;
	$page = 1;


	$lists = array();
	while($able && $page<10){
		$url = 'http://www.sdgn.co.kr/Gundam/UnitList/'.$page;
		$page++;
		$rslt = $mp->getContent($url,null,$cookieRaw,array(),$opts);
		$body = $rslt['body'];
		list($t0,$t1) = explode('<!--unit - list -->',$body);
		list($t0,$t1) = explode('<!--//unit - list -->',$t1);
		$body = trim($t0);
		$body = preg_replace('/(<img[^>]*)(>)/','$1 /$2',$body);
		$sxml = simplexml_load_string('<div>'.$body.'</div>', 'SimpleXMLElement');
		$arr = json_decode(json_encode($sxml), TRUE);
		$temp_lists = parse_4_lists($arr);
		//var_dump($temp_lists);
		//exit;
		if($temp_lists===false){
			$able = false;
			continue;
		}
		$lists = array_merge($lists,$temp_lists);
	}
	
	file_put_contents($fn,serialize($lists));
}else{
	echo "{$fn} : cached\n";
	$lists = unserialize(file_get_contents($fn));
}
//print_r($lists);
//=== 상세 목록
$rows = $lists;
foreach($rows as $k=> &$row){
	$fn = 'data.tmp/detail_'.$row['unit_idx'].'.txt';
	if(!is_file($fn)){
		$url = $row['unit_defail_href'];
		$rslt = $mp->getContent($url,null,$cookieRaw,array(),$opts);
		$body = $rslt['body'];
		list($t0,$t1) = explode('<!-- list -->',$body);
		list($t0,$t1) = explode('<!-- //list -->',$t1);
		file_put_contents($fn,$t0);
	}else{
		echo "{$fn} : cached\n";
		$body = file_get_contents($fn);
	}
	$temp_lists = parse_4_detail($body,$row);
}

$fn = 'data.tmp/_unit.sql';
//print_r($rows);
$sqls = to_insert_sql($rows);
file_put_contents($fn,implode("\n",$sqls));
echo "save : {$fn}\n";