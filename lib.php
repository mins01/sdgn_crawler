<?
require('simple_html_dom.php');

function parse_4_lists($lists){
	//$lists['ul']['li']
	print_r($lists);
	if(!is_array($lists['ul']['li'])){
		return false;
	}
	$rs = array();
	foreach($lists['ul']['li'] as $v){
		$r = array();
		$r['unit_defail_href']='http://sdgn.co.kr'.$v['span']['a']['@attributes']['href'];
		$r['unit_idx'] = preg_replace('#(^.*unitdetail/|\?.*$)#','',$r['unit_defail_href']);
		$r['unit_img']=$v['span']['a']['img']['@attributes']['src'];
		$r['unit_name']=$v['span']['a']['img']['@attributes']['alt'];
		$r['unit_name'] = str_replace('<br>',' ',$r['unit_name']);
		$rs[] = $r;
	}
	return $rs;
}

function parse_4_detail($body,& $row){
	$body = preg_replace('#(<img[^>]*)([^/])(>)#','$1 $2 /$3',$body);
	$body = str_replace('//>','/>',$body);
	$html = str_get_html('<div>'.$body.'</div>');
	$row['unit_anime_img'] = $html->find('h3 img[alt=]',0)->src;
	$trs = $html->find('div.unit_tbl td');;
	$row['unit_rank']= $trs[0]->innertext;
	$row['unit_properties']= $trs[1]->innertext;
	switch($row['unit_properties']){
		case '어썰트':$row['unit_properties_num'] = 1;break;
		case '밸런스':$row['unit_properties_num'] = 2;break;
		case '슈터':$row['unit_properties_num'] = 3;break;
		default:$row['unit_properties_num'] = 0;break;
	}
	$row['unit_movetype']= $trs[2]->innertext;
	$row['unit_anime']= $trs[3]->innertext;
	$row['unit_txt'] = trim(strip_tags($html->find('p.unit_txt',0)->innertext));
	$row['unit_img2'] = $html->find('div.unit_img img',0)->src;;
	
	$ths = $html->find('div.tbl_status th');;
	$row['unit_weapon1'] = $ths[0]->innertext;
	$row['unit_weapon2'] = $ths[1]->innertext;
	$row['unit_weapon3'] = $ths[2]->innertext;
	$imgs = $html->find('div.tbl_status img');;
	$row['unit_weapon1_img'] = $imgs[0]->src;
	$row['unit_weapon2_img'] = $imgs[1]->src;
	$row['unit_weapon3_img'] = $imgs[2]->src;
	
	$imgs = $html->find('div.skill_status td img');;
	$row['unit_skill1'] = $imgs[0]->alt;
	$row['unit_skill2'] = $imgs[1]->alt;
	$row['unit_skill3'] = $imgs[2]->alt;
	$row['unit_skill1_img'] = $imgs[0]->src;
	$row['unit_skill2_img'] = $imgs[1]->src;
	$row['unit_skill3_img'] = $imgs[2]->src;
	$skill_txts = $html->find('.skill_txt');;
	//$row['unit_skill3_desc'] = preg_replace('/<[]/','',$skill_txts[0]->innertext);
	$t = $skill_txts[0]->find('text');
	$row['unit_skill1_desc'] = trim(preg_replace('/\s+/u',' ',$t[3]->innertext));
	$t = $skill_txts[1]->find('text');
	$row['unit_skill2_desc'] = trim(preg_replace('/\s+/u',' ',$t[3]->innertext));
	$t = $skill_txts[2]->find('text');
	$row['unit_skill3_desc'] = trim(preg_replace('/\s+/u',' ',$t[3]->innertext));
}


function to_insert_sql($rows){
	$sqls = array();
	foreach($rows as $row){
		$k_str = implode(',',array_keys($row));
		$v_str = implode("','",($row));
		$sql = "REPLACE INTO sdgn_units ({$k_str}) values('{$v_str}');";
		$sqls[]=$sql;
	}
	return $sqls;
}




