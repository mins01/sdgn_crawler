<?
require('simple_html_dom.php');

function parse_4_lists($body){
	$body = preg_replace('#(<img[^>]*)([^/])(>)#','$1 $2 /$3',$body);
	$body = str_replace('//>','/>',$body);
	$html = str_get_html('<div>'.$body.'</div>');
	$lis = $html->find('li');
	if(count($lis)==0){
		return false;
	}
	$rs = array();
	foreach($lis as $li){
		$r = array();
		$r['unit_defail_href']='http://sdgn.co.kr'.$li->find('a',0)->href;
		$r['unit_idx'] = preg_replace('#(^.*unitdetail/|\?.*$)#','',$r['unit_defail_href']);
		$r['unit_img']=$li->find('img',0)->src;
		$r['unit_name']=$li->find('a',1)->innertext;
		$r['unit_name'] = str_replace('<br>',' ',$r['unit_name']);
		$rs[] = $r;
	}
	return $rs;
}
function parse_4_lists2($lists){
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

function parse_4_detail($body,& $row, & $weapons){

	$body = preg_replace('#(<img[^>]*)([^/])(>)#','$1 $2 /$3',$body);
	$body = str_replace('//>','/>',$body);
	$html = str_get_html('<div>'.$body.'</div>');
	$row['unit_anime_img'] = $html->find('h3 img[alt=]',0)->src;
	
	//웨폰체인지
	$t = $html->find('section.wpn_change',0); 
	if($t){
		$row['unit_is_weapon_change'] = 1;
	}else{
		$row['unit_is_weapon_change'] = 0;
	}
	//변신가능여부
	$t = $html->find('div.info_tab_svc',0);
	if($t){
		$row['unit_is_transform'] = 1;
	}else{
		$row['unit_is_transform'] = 0;
	}
	
	//unit_is_weapon_change
	$trs = $html->find('div.unit_tbl td');
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
	$row['unit_txt'] = trim(htmlspecialchars_decode(strip_tags($html->find('p.unit_txt',0)->innertext), ENT_QUOTES));
	$row['unit_img2'] = $html->find('div.unit_img img',0)->src;;
	
	$ths = $html->find('div.tbl_status th');;
	$row['unit_weapon1'] = $ths[0]->innertext;
	$row['unit_weapon2'] = $ths[1]->innertext;
	$row['unit_weapon3'] = $ths[2]->innertext;
	$row['unit_weapon4'] = @$ths[3]->innertext;
	$row['unit_weapon5'] = @$ths[4]->innertext;
	$row['unit_weapon6'] = @$ths[5]->innertext;
	$imgs = $html->find('div.tbl_status img');;
	$row['unit_weapon1_img'] = $imgs[0]->src;
	$row['unit_weapon2_img'] = $imgs[1]->src;
	$row['unit_weapon3_img'] = $imgs[2]->src;
	$row['unit_weapon4_img'] = @$imgs[3]->src;
	$row['unit_weapon5_img'] = @$imgs[4]->src;
	$row['unit_weapon6_img'] = @$imgs[5]->src;
	
	
	$imgs = $html->find('div.skill_status td img');

	$row['unit_skill1'] = $imgs[0]->alt;
	$row['unit_skill2'] = $imgs[1]->alt;
	$row['unit_skill3'] = $imgs[2]->alt;
	
	$row['unit_skill1_img'] = $imgs[0]->src;
	$row['unit_skill2_img'] = $imgs[1]->src;
	$row['unit_skill3_img'] = $imgs[2]->src;


	$row['unit_skill4'] = '';
	$row['unit_skill5'] = '';
	$row['unit_skill6'] = '';
	$row['unit_skill4_img'] = '';
	$row['unit_skill5_img'] = '';
	$row['unit_skill6_img'] = '';
	$row['unit_skill4_desc'] = '';
	$row['unit_skill5_desc'] = '';
	$row['unit_skill6_desc'] = '';
	
	
	if(isset($imgs[3])){
		$row['unit_skill4'] = $imgs[3]->alt;
		$row['unit_skill5'] = $imgs[4]->alt;
		$row['unit_skill6'] = $imgs[5]->alt;
		$row['unit_skill4_img'] = $imgs[3]->src;
		$row['unit_skill5_img'] = $imgs[4]->src;
		$row['unit_skill6_img'] = $imgs[5]->src;
	}
	
	$skill_txts = $html->find('.skill_txt');;
	
	//$row['unit_skill3_desc'] = preg_replace('/<[]/','',$skill_txts[0]->innertext);
	$t = $skill_txts[0]->find('text');
	$row['unit_skill1_desc'] = trim(preg_replace('/\s+/u',' ',$t[3]->innertext));
	$t = $skill_txts[1]->find('text');
	$row['unit_skill2_desc'] = trim(preg_replace('/\s+/u',' ',$t[3]->innertext));
	$t = $skill_txts[2]->find('text');
	$row['unit_skill3_desc'] = trim(preg_replace('/\s+/u',' ',$t[3]->innertext));
	if($row['unit_is_transform']==1){
		$t = $skill_txts[3]->find('text');
		$row['unit_skill4_desc'] = trim(preg_replace('/\s+/u',' ',$t[3]->innertext));
		$t = $skill_txts[4]->find('text');
		$row['unit_skill5_desc'] = trim(preg_replace('/\s+/u',' ',$t[3]->innertext));
		$t = $skill_txts[5]->find('text');
		$row['unit_skill6_desc'] = trim(preg_replace('/\s+/u',' ',$t[3]->innertext));	
	}
	$row['unit_is_change_skill'] = 0; //스킬 변경 여부
	if(isset($row['unit_skill4'][0])){
		if($row['unit_skill1']!=$row['unit_skill4'] || $row['unit_skill2']!=$row['unit_skill5']){
			$row['unit_is_change_skill'] = 1;
		}
	}

	//-- 웨폰만 따로 처리.
	$weapon_d = array('unit_idx' => $row['unit_idx']);
	$t = $html->find('div.tb_weapon'); //기본무기
	if(isset($t[0])){//기본무기
		$imgs = $t[0]->find('img');
		foreach($imgs as $k=>$img){
				$weapon = $weapon_d;
				$weapon['sw_name'] = $img->alt;
				$weapon['sw_img'] = $img->src;
				$weapon['sw_sort'] = $k+1;
				$weapon['sw_is_transform'] = 0;
				$weapon['sw_is_change'] = 0;
				$weapon['sw_key'] = md5($weapon['unit_idx'].$weapon['sw_name'].$weapon['sw_is_change'].$weapon['sw_is_transform'].$weapon['sw_sort']);
				$weapon['sw_key2'] = md5($weapon['unit_idx'].$weapon['sw_name'].$weapon['sw_is_change'].$weapon['sw_sort']);
				$weapons[]=$weapon;
		}
	}
	if(isset($t[1])){//가변무기
		$imgs = $t[1]->find('img');
		foreach($imgs as $k=>$img){
				$weapon = $weapon_d;
				$weapon['sw_name'] = $img->alt;
				$weapon['sw_img'] = $img->src;
				$weapon['sw_sort'] = $k+1;
				$weapon['sw_is_transform'] = 1;
				$weapon['sw_is_change'] = 0;
				$weapon['sw_key'] = md5($weapon['unit_idx'].$weapon['sw_name'].$weapon['sw_is_change'].$weapon['sw_is_transform'].$weapon['sw_sort']);
				$weapon['sw_key2'] = md5($weapon['unit_idx'].$weapon['sw_name'].$weapon['sw_is_change'].$weapon['sw_sort']);
				$weapons[]=$weapon;
		}
	}
	if(isset($t[2])){//가변 후 가변 무기
		$imgs = $t[2]->find('img');
		foreach($imgs as $k=>$img){
				$weapon = $weapon_d;
				$weapon['sw_name'] = $img->alt;
				$weapon['sw_img'] = $img->src;
				$weapon['sw_sort'] = $k+1;
				$weapon['sw_is_transform'] = 2;
				$weapon['sw_is_change'] = 0;
				$weapon['sw_key'] = md5($weapon['unit_idx'].$weapon['sw_name'].$weapon['sw_is_change'].$weapon['sw_is_transform'].$weapon['sw_sort']);
				$weapon['sw_key2'] = md5($weapon['unit_idx'].$weapon['sw_name'].$weapon['sw_is_change'].$weapon['sw_sort']);
				$weapons[]=$weapon;
		}
	}
	$t = $html->find('.wpn_change');	//웨폰체인지
	if(isset($t[0])){//웨폰체인지
		$imgs = $t[0]->find('img');
		foreach($imgs as $k=>$img){
				$weapon = $weapon_d;
				$weapon['sw_name'] = $img->alt;
				$weapon['sw_img'] = $img->src;
				$weapon['sw_sort'] = $k+1;
				$weapon['sw_is_transform'] = 0;
				$weapon['sw_is_change'] = 1;
				$weapon['sw_key'] = md5($weapon['unit_idx'].$weapon['sw_name'].$weapon['sw_is_change'].$weapon['sw_is_transform'].$weapon['sw_sort']);
				$weapon['sw_key2'] = md5($weapon['unit_idx'].$weapon['sw_name'].$weapon['sw_is_change'].$weapon['sw_sort']);
				$weapons[]=$weapon;
		}
	}	
}


function to_insert_sql($rows,$tbl='sdgn_units'){
	$sqls = array();
	

	$sql = '';
	foreach($rows as $row){
		ksort($row);
		foreach($row as $k=>$v){ //한글 URL을 강제 변경하기
			if(strpos($v,'http://')===0){
				$v = str_replace(array('%3A','%2F'),array(':','/'),rawurlencode($v));
			}
			$row[$k]=addslashes($v);
		}
		//$k_str = implode(',',array_keys($row));
		$v_str = implode("','",($row));
		
		if(strlen($sql)>102400){
			$sqls[]=$sql.';';
			$sql='';
		}
		
		if(strlen($sql)==0){
			$k_str = implode(',',array_keys($row));
			$sql = "REPLACE INTO {$tbl} ({$k_str}) values('{$v_str}')";
		}else{
			$sql.=",('{$v_str}')";
		}
	}
	if(strlen($sql)>0){
		$sqls[]=$sql.';';
		$sql='';
	}
	
	return $sqls;
}




