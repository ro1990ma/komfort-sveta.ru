<?PHP
/**
 * MoonSerials
 * =======================================================
 * Автор:	kild
 * =======================================================
 * Файл:  moonserials_block.php
 * -------------------------------------------------------
 * Версия: 1.4.5 (8.05.2016)
 * =======================================================
 */
if (!defined('DATALIFEENGINE')) die("Go fuck yourself!");
include('engine/data/moonserials_options.php'); //конфиг модуля
$tpl1->result['moonserials_block'] = dle_cache( "moonserials_block", $config['skin'], true );

if ($tpl1->result['moonserials_block'] === FALSE){
    $sqlcat = str_replace(',','|',$moonserials_options['ms_block_cat']);
    $sqlcat = "REGEXP '[[:<:]]($sqlcat)[[:>:]]'";
    if ($moonserials_options['ms_block_not_cat']) {
    $sqlnotcat = str_replace(',','|',$moonserials_options['ms_block_not_cat']);
    $sqlnotcat = "REGEXP '[[:<:]]($sqlnotcat)[[:>:]]'";
    $sqlnotcat = " and category not $sqlnotcat";
    }
    if ($moonserials_options['ms_block_ower'] < 1) {
    $sqlower = $moonserials_options['field_status'];
    $sqlower = " and xfields not LIKE '%$sqlower%'";
    }
		$thisdate = date ( "Y-m-d H:i:s", time () );
		if ($config['no_date'] AND !$config['news_future']) $where_date = " AND date < '" . $thisdate . "'";
		else $where_date = "";
	$db->query("SELECT id, title, category, date, alt_name, short_story, xfields FROM ".PREFIX."_post WHERE date >= DATE_SUB(CURRENT_DATE, INTERVAL {$moonserials_options['ms_block_day']} DAY) $where_date AND approve='1' and category {$sqlcat}{$sqlnotcat}{$sqlower} GROUP BY date ORDER BY date DESC LIMIT 0 , {$moonserials_options['ms_block_limit']}");
	while($row = $db->get_row())
		$lastnews[substr($row['date'], 0, 10)][$row['id']] = $row;

	$tpl1 = new dle_template();
	$tpl1->dir = TEMPLATE_DIR;
	$tpl1->load_template( '/moonserials/moonserials_block.tpl' );

	foreach ($lastnews as $date => $news){

			/*Обработка даты начало*/
			$dates = strtotime($date);
            $timeformat = $moonserials_options['ms_block_time'];
			if( date( 'Ymd', $dates ) == date( 'Ymd', $_TIME ) )
				$tpl1->set( '{date}', $lang['time_heute'] . langdate( ", $timeformat", $dates ) );
			elseif( date( 'Ymd', $dates ) == date( 'Ymd', ($_TIME - 86400) ) )
				$tpl1->set( '{date}', $lang['time_gestern'] . langdate( ", $timeformat", $dates ) );
			else
				$tpl1->set( '{date}', langdate( "$timeformat", $dates ) );
			/*Обработка даты конец*/

		$tpl12 = new dle_template();
		$tpl12->dir = TEMPLATE_DIR;
		$tpl12->load_template( '/moonserials/moonserials_block_content.tpl' );

		if( strpos( $tpl12->copy_template, "[xfvalue_" ) OR strpos( $tpl12->copy_template, "[xfgiven_" ) ) { $xfound = true; $xfields = xfieldsload(); }
		else $xfound = false;
		foreach ($news as $id => $newscontent){

			/*Обработка ссылок начало*/
        	if( $config['allow_alt_url'] ) {
        		if( $config['seo_type'] == 1 OR $config['seo_type'] == 2 ) {
        			if( $newscontent['category'] and $config['seo_type'] == 2 ) {
        			    $catid = intval( $newscontent['category'] );
                        $full_link = $config['http_home_url'] . get_url( $catid ) . "/" . $newscontent['id'] . "-" . $newscontent['alt_name'] . ".html";
        			} else {
        				$full_link = $config['http_home_url'] . $newscontent['id'] . "-" . $newscontent['alt_name'] . ".html";
        			}
        		} else {
        			$full_link = $config['http_home_url'] . date( 'Y/m/d/', $newscontent['date'] ) . $newscontent['alt_name'] . ".html";
        		}
        	} else {
        		$full_link = $config['http_home_url'] . "index.php?newsid=" . $newscontent['id'];
        	}
			/*Обработка ссылок конец*/

        	// Обработка дополнительных полей
        	$xfieldsdata = xfieldsdataload( $newscontent['xfields'] );
        	foreach ( $xfields as $value ) {
        		$preg_safe_name = preg_quote( $value[0], "'" );
        		if ( $value[6] AND !empty( $xfieldsdata[$value[0]] ) ) {
        			$temp_array = explode( ",", $xfieldsdata[$value[0]] );
        			$value3 = array();
        			foreach ($temp_array as $value2) {
        				$value2 = trim($value2);
        				$value2 = str_replace("&#039;", "'", $value2);
        				if( $config['allow_alt_url'] ) $value3[] = "<a href=\"" . $config['http_home_url'] . "xfsearch/" . urlencode( $value2 ) . "/\">" . $value2 . "</a>";
        				else $value3[] = "<a href=\"$PHP_SELF?do=xfsearch&amp;xf=" . urlencode( $value2 ) . "\">" . $value2 . "</a>";
        			}
        			$xfieldsdata[$value[0]] = implode(", ", $value3);
        			unset($temp_array);
        			unset($value2);
        			unset($value3);
        		}
        		if( empty( $xfieldsdata[$value[0]] ) ) {
        			$tpl12->copy_template = preg_replace( "'\\[xfgiven_{$preg_safe_name}\\](.*?)\\[/xfgiven_{$preg_safe_name}\\]'is", "", $tpl12->copy_template );
        			$tpl12->copy_template = str_replace( "[xfnotgiven_{$value[0]}]", "", $tpl12->copy_template );
        			$tpl12->copy_template = str_replace( "[/xfnotgiven_{$value[0]}]", "", $tpl12->copy_template );
        		} else {
        			$tpl12->copy_template = preg_replace( "'\\[xfnotgiven_{$preg_safe_name}\\](.*?)\\[/xfnotgiven_{$preg_safe_name}\\]'is", "", $tpl12->copy_template );
        			$tpl12->copy_template = str_replace( "[xfgiven_{$value[0]}]", "", $tpl12->copy_template );
        			$tpl12->copy_template = str_replace( "[/xfgiven_{$value[0]}]", "", $tpl12->copy_template );
        		}
        		$xfieldsdata[$value[0]] = stripslashes( $xfieldsdata[$value[0]] );
        		if ($config['allow_links'] AND $value[3] == "textarea" AND function_exists('replace_links')) $xfieldsdata[$value[0]] = replace_links ( $xfieldsdata[$value[0]], $replace_links['news'] );
        		$tpl12->copy_template = str_replace( "[xfvalue_{$value[0]}]", $xfieldsdata[$value[0]], $tpl12->copy_template );
        		if ( preg_match( "#\\[xfvalue_{$preg_safe_name} limit=['\"](.+?)['\"]\\]#i", $tpl12->copy_template, $matches ) ) {
        			$count= intval($matches[1]);
        			$xfieldsdata[$value[0]] = str_replace( "</p><p>", " ", $xfieldsdata[$value[0]] );
        			$xfieldsdata[$value[0]] = strip_tags( $xfieldsdata[$value[0]], "<br>" );
        			$xfieldsdata[$value[0]] = trim(str_replace( "<br>", " ", str_replace( "<br />", " ", str_replace( "\n", " ", str_replace( "\r", "", $xfieldsdata[$value[0]] ) ) ) ));
        			if( $count AND dle_strlen( $xfieldsdata[$value[0]], $config['charset'] ) > $count ) {
        				$xfieldsdata[$value[0]] = dle_substr( $xfieldsdata[$value[0]], 0, $count, $config['charset'] );
        				if( ($temp_dmax = dle_strrpos( $xfieldsdata[$value[0]], ' ', $config['charset'] )) ) $xfieldsdata[$value[0]] = dle_substr( $xfieldsdata[$value[0]], 0, $temp_dmax, $config['charset'] );
        			}
        			$tpl12->set( $matches[0], $xfieldsdata[$value[0]] );
        		}

        	}
        	// Обработка дополнительных полей

			/*Обработка даты начало*/
			$newscontent['date'] = strtotime($newscontent['date']);

			if( date( 'Ymd', $newscontent['date'] ) == date( 'Ymd', $_TIME ) )
				$tpl12->set( '{date}', $lang['time_heute'] . langdate( ", $timeformat", $newscontent['date'] ) );
			elseif( date( 'Ymd', $newscontent['date'] ) == date( 'Ymd', ($_TIME - 86400) ) )
				$tpl12->set( '{date}', $lang['time_gestern'] . langdate( ", $timeformat", $newscontent['date'] ) );
			else
				$tpl12->set( '{date}', langdate( $timeformat, $newscontent['date'] ) );
			/*Обработка даты конец*/

			/*Обработка категории начало*/
			if( ! $newscontent['category'] ) {
				$my_cat = "---";
				$my_cat_link = "---";
			} else {
				$my_cat = array ();
				$my_cat_link = array ();
				$cat_list = explode( ',', $newscontent['category'] );

				if( count( $cat_list ) == 1 ) {
					$my_cat[] = $cat_info[$cat_list[0]]['name'];
					$my_cat_link = get_categories( $cat_list[0] );
				} else {
					foreach ( $cat_list as $element ) {
						if( $element ) {
							$my_cat[] = $cat_info[$element]['name'];
							if( $config['allow_alt_url']) $my_cat_link[] = "<a href=\"" . $config['http_home_url'] . get_url( $element ) . "/\">{$cat_info[$element]['name']}</a>";
							else $my_cat_link[] = "<a href=\"$PHP_SELF?do=cat&category={$cat_info[$element]['alt_name']}\">{$cat_info[$element]['name']}</a>";
						}
					}
					$my_cat_link = implode( ', ', $my_cat_link );
				}		
				$my_cat = implode( ', ', $my_cat );
			}			
			/*Обработка категорий конец*/
				
			$tpl12->set( '[full-link]', "<a href=\"" . $full_link . "\">" );
			$tpl12->set( '[/full-link]', "</a>" );
            $tpl12->set( '{full-link}', $full_link );
			$tpl12->set("{title}", $newscontent['title']);
			$tpl12->set("{category}", $my_cat_link);
			$tpl12->compile( 'mscontent' );
		}	
		
		$tpl1->set("{mscontent}", $tpl12->result['mscontent']); unset($tpl12);
		$tpl1->compile( 'moonserials_block' );
	}

	create_cache( "moonserials_block", $tpl1->result['moonserials_block'], $config['skin'], true );
}

echo $tpl1->result['moonserials_block'];

?>