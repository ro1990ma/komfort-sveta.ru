<?php
/**
 * MoonSerials
 * =======================================================
 * Автор:	kild
 * =======================================================
 * Файл:  moonserials.php
 * -------------------------------------------------------
 * Версия: 1.4.5
 * =======================================================
 */
		if ( !defined('DATALIFEENGINE'))
				die("Go fuck yourself!");
		include ( 'engine/data/moonserials_options.php' );
		global $row;
		$myConfig = array( 'cachePrefix' => $moonserials_options['cashe_prefix_dle'], 'cacheSuffix' => !empty( $cacheSuffix ) ? $cacheSuffix : false, 'id' => $row['id'], );
		if ( $config['charset'] == 'windows-1251' )
		{
				function encoding(& $data, $in, $to)
				{
						if ( is_array($data))
						{
								foreach ( $data as $key => $value )
								{
										if ( is_array($data[$key]))
										{
												encoding($data[$key], $in, $to);
										}
										else
										{
												$data[$key] = iconv($in, $to, $value);
										}
								}
						}
						else
						{
								$data = iconv($in, $to, $data);
						}
				}
		}
		if ( $moonserials_options['allow_module_on'] != 0 )
		{
				$cacheName = md5(implode('_', $myConfig));
				$myModule = false;
				$allow_cache = ( $config['version_id'] >= '10.2' ) ? $config['allow_cache'] == '1' : $config['allow_cache'] == "yes";
				if ( !$allow_cache )
				{
						if ( $config['version_id'] >= '10.2' )
								$config['allow_cache'] = '1';
						else
								$config['allow_cache'] = "yes";
						$is_change = true;
				}
				$myModule = dle_cache($myConfig['cachePrefix'], $cacheName . $config['skin'], $myConfig['cacheSuffix']);
				if ( $myModule === false )
				{
						$post_id = $row['id'];
						$xfieldsdata = xfieldsdataload($row['xfields']);
						$xfields_n = $xfieldsdata;
						$kinopoisk_id = $xfieldsdata[$moonserials_options['field_kpid']];
						if ( $xfieldsdata[$moonserials_options['field_season']] AND $xfieldsdata[$moonserials_options['field_series']] AND $xfieldsdata[$moonserials_options['field_season']] !== $moonserials_options['if_series_ower'] )
						{
								$str = strpos($xfieldsdata[$moonserials_options['field_season']], " ");
								$seasonTemp = substr($xfieldsdata[$moonserials_options['field_season']], 0, $str);
								$str = strpos($xfieldsdata[$moonserials_options['field_series']], " ");
								$seriesTemp = substr($xfieldsdata[$moonserials_options['field_series']], 0, $str);
						}
						else
						{
								$seasonTemp = false;
								$seriesTemp = false;
						}
						if ( $moonserials_options['allow_module_new_series_max'] > 0 AND $moonserials_options['field_series-max'] AND !$xfieldsdata[$moonserials_options['field_series-max']] AND $xfieldsdata[$moonserials_options['field_season_iframe']] )
						{
								$url = "http://api.kinopoisk.cf/tvshow/get?kinopoiskID=" . $kinopoisk_id;
								$curlpost = curl_init($url);
								curl_setopt($curlpost, CURLOPT_URL, $url);
								curl_setopt($curlpost, CURLOPT_RETURNTRANSFER, 1);
								curl_setopt($curlpost, CURLOPT_HEADER, 0);
								curl_setopt($curlpost, CURLOPT_FOLLOWLOCATION, 1);
								curl_setopt($curlpost, CURLOPT_ENCODING, "");
								curl_setopt($curlpost, CURLOPT_CONNECTTIMEOUT, 120);
								curl_setopt($curlpost, CURLOPT_TIMEOUT, 120);
								curl_setopt($curlpost, CURLOPT_MAXREDIRS, 10);
								$data = curl_exec($curlpost);
								curl_close($curlpost);
								if ( $data )
								{
										$data = json_decode($data, true);
										$seriesMax = $data['episodesInSeason'][$xfieldsdata[$moonserials_options['field_season_iframe']]];
								}
						}
						elseif ( $xfieldsdata[$moonserials_options['field_series-max']] AND $moonserials_options['allow_module_new_series_max'] > 0 AND $xfieldsdata[$moonserials_options['field_season_iframe']] )
								$seriesMax = $xfieldsdata[$moonserials_options['field_series-max']];
						if ( $moonserials_options['allow_module_new'] )
						{
								$tpl1 = new dle_template();
								$tpl1->dir = TEMPLATE_DIR;
								$tpl1->load_template('/moonserials/moonserials_iframe.tpl');
						}
						if ( $xfieldsdata[$moonserials_options['field_status_name']] == $moonserials_options['field_status'] )
						{
								if ( $moonserials_options['allow_fields_spy'] )
								{
										$xfields_n[$moonserials_options['field_season']] = $moonserials_options['if_series_ower'];
										if ( $xfieldsdata[$moonserials_options['field_series']] )
												unset( $xfields_n[$moonserials_options['field_series']] );
										foreach ( $xfields_n as $key => & $value )
												$arr_field[] = $key . "|" . str_replace('|', '&#124;', $value);
										$xfields_n = implode("||", $arr_field);
										unset( $arr_field );
										$xfields_n = $db->safesql($xfields_n);
										$db->query("UPDATE " . PREFIX . "_post SET `xfields` = '$xfields_n' WHERE id = {$post_id}");
								}
								if ( $moonserials_options['allow_module_new'] )
								{
										$tpl1->set('[ower]', "");
										$tpl1->set('[/ower]', "");
										$tpl1->set_block("'\\[not-ower\\](.*?)\\[/not-ower\\]'si", "");
								}
						}
						else
						{
								if ( $moonserials_options['allow_module_new'] )
								{
										$tpl1->set_block("'\\[ower\\](.*?)\\[/ower\\]'si", "");
										$tpl1->set_block("'\\[not-ower\\](.*?)\\[/not-ower\\]'si", "\\1");
								}
						}
						if ( $curl = curl_init())
						{
								curl_setopt($curl, CURLOPT_URL, 'http://moonwalk.cc/api/videos.json?kinopoisk_id=' . $kinopoisk_id . '&api_token=' . $moonserials_options['api_token'] . '');
								curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
								$out = curl_exec($curl);
								$output_tr = json_decode($out, true);
								curl_close($curl);
						}
						if ( $config['charset'] == 'windows-1251' )
						{
								encoding($output_tr, "UTF-8", "WINDOWS-1251");
						}
						if ( !$output_tr or $output_tr['error'] == 'videos_not_found' )
						{
								if ( $moonserials_options['allow_module_new'] )
								{
										if ( !$xfieldsdata[$moonserials_options['field_season_iframe']] )
										{
												$tpl1->set('[error]', "");
												$tpl1->set('[/error]', "");
												$tpl1->set_block("'\\[not-error\\](.*?)\\[/not-error\\]'si", "");
												$tpl1->set_block("'\\[soon\\](.*?)\\[/soon\\]'si", "");
												$tpl1->set_block("'\\[not-soon\\](.*?)\\[/not-soon\\]'si", "\\1");
										}
										else
										{
												$tpl1->set_block("'\\[error\\](.*?)\\[/error\\]'si", "");
												$tpl1->set_block("'\\[not-error\\](.*?)\\[/not-error\\]'si", "\\1");
												$tpl1->set('[soon]', "");
												$tpl1->set('[/soon]', "");
												$tpl1->set_block("'\\[not-soon\\](.*?)\\[/not-soon\\]'si", "");
										}
										$tpl1->set_block("'\\[studios\\](.*?)\\[/studios\\]'si", "");
										$tpl1->set_block("'\\[not-studios\\](.*?)\\[/not-studios\\]'si", "\\1");
								}
						}
						else
						{
								if ( $moonserials_options['allow_module_new'] )
								{
										$tpl1->set_block("'\\[error\\](.*?)\\[/error\\]'si", "");
										$tpl1->set_block("'\\[not-error\\](.*?)\\[/not-error\\]'si", "\\1");
										$tpl1->set('{title}', $output_tr[0]['title_ru']);
										if ( count($output_tr) == 1 )
										{
												$tpl1->set_block("'\\[studios\\](.*?)\\[/studios\\]'si", "");
												$tpl1->set_block("'\\[not-studios\\](.*?)\\[/not-studios\\]'si", "\\1");
										}
										else
										{
												$tpl1->set('[studios]', "");
												$tpl1->set('[/studios]', "");
												$tpl1->set_block("'\\[not-studios\\](.*?)\\[/not-studios\\]'si", "");
										}
										$tpl2 = new dle_template();
										$tpl2->dir = TEMPLATE_DIR;
										$tpl2->load_template('/moonserials/moonserials_iframe_title.tpl');
										$tpl3 = new dle_template();
										$tpl3->dir = TEMPLATE_DIR;
										$tpl3->load_template('/moonserials/moonserials_iframe_content.tpl');
								}
								foreach ( ( array ) $output_tr as $kk => $vv )
								{
										$seasonfound = 0;
										$seriesfound = 0;
										if ( $curl = curl_init())
										{
												curl_setopt($curl, CURLOPT_URL, 'http://moonwalk.cc/api/serial_episodes.json?kinopoisk_id=' . $kinopoisk_id . '&api_token=' . $moonserials_options['api_token'] . '&translator_id=' . $vv['translator_id'] . '');
												curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
												$out = curl_exec($curl);
												$output_sr = json_decode($out, true);
												curl_close($curl);
										}
										if ( $moonserials_options['disable_sub'] > 0 AND $vv['translator'] == 'Субтитры' )
												continue;
										if ( $config['charset'] == 'windows-1251' )
												encoding($output_sr, "UTF-8", "WINDOWS-1251");
										$title_ru = $vv['title_ru'];
										if ( !$vv['translator'] )
										{
												$vv['translator'] = 'Неизвестно';
										}
										elseif ( $vv['translator'] == 'двухголосый закадровый' )
										{
												$vv['translator'] = 'Двухголосый';
										}
										elseif ( $vv['translator'] == 'многоголосый закадровый' )
										{
												$vv['translator'] = 'Многоголосый';
										}
										elseif ( $vv['translator'] == 'одноголосый закадровый' )
										{
												$vv['translator'] = 'Одноголосый';
										}
										if ( $moonserials_options['field_studios_sp'] AND $vv['translator'] !== "Неизвестно" )
												$studios_sp = !$studios_sp ? $vv['translator'] : $studios_sp . ", " . $vv['translator'];
										foreach ( ( array ) $output_sr["season_episodes_count"] as $kkk => $vvv )
										{
												if ( $xfieldsdata[$moonserials_options['field_season_iframe']] )
												{
														if ( $vvv["season_number"] == $xfieldsdata[$moonserials_options['field_season_iframe']] )
														{
																$season = $vvv["season_number"];
																$seriesA = $vvv["episodes"];
																$seriesC = $vvv["episodes_count"];
														}
												}
												else
												{
														if ( $vvv["season_number"] > $season )
														{
																$season = $vvv["season_number"];
																$seriesA = $vvv["episodes"];
																$seriesC = $vvv["episodes_count"];
														}
												}
												if ( $moonserials_options['allow_module_new'] )
												{
														if ( $xfieldsdata[$moonserials_options['field_season_iframe']] )
														{
																if ( $vvv["season_number"] == $xfieldsdata[$moonserials_options['field_season_iframe']] )
																{
																		$seasonfound = 1;
																		if ( $xfieldsdata[$moonserials_options['field_series_iframe']] )
																		{
																				foreach ( ( array ) $vvv["episodes"] as $vvvvv )
																				{
																						if ( $vvvvv == $xfieldsdata[$moonserials_options['field_series_iframe']] )
																						{
																								$seriesfound = 1;
																								break;
																						}
																				}
																		}
																}
														}
												}
												foreach ( ( array ) $seriesA as $vvvv )
												{
														if ( $vvvv == 0 )
																$seriesT = - 1;
												}
												if ( $seriesT == - 1 AND $seriesC == 1 )
														$series = $moonserials_options['if_pilot_series'];
												else
														$series = max($seriesA);
										}
										$tabs[] = array( 'translator' => $vv['translator'], 'iframe_url' => $vv['iframe_url'], 'season' => $season, 'series' => $series, 'seasonfound' => $seasonfound, 'seriesfound' => $seriesfound );
										unset( $season );
										unset( $series );
										usleep(100000);
								}
								foreach ( ( array ) $tabs as $k => $v )
								{
										if ( $v['series'] == $moonserials_options['if_pilot_series'] )
												$v['series'] = 0;
										if ( $v['translator'] == "Субтитры" )
												$v['series'] = $v['series'] - 0.5;
										$se[$k] = $v['season'];
										$ep[$k] = $v['series'];
								}
								array_multisort($se, SORT_NUMERIC, SORT_DESC, $ep, SORT_NUMERIC, SORT_DESC, $tabs);
								if ( $moonserials_options['allow_module_new'] )
								{
										$tplstudios = false;
										foreach ( ( array ) $tabs as $tabskey => $tabsvalue )
										{
												if ( $xfieldsdata[$moonserials_options['field_season_iframe']] AND $xfieldsdata[$moonserials_options['field_series_iframe']] )
												{
														if ( $tabsvalue['seasonfound'] > 0 AND $tabsvalue['seriesfound'] > 0 )
														{
																if ( !$tplstudios )
																		$tplstudios = $tabsvalue['translator'];
																$tpl2->set('{ms-title}', $tabsvalue['translator']);
																$tpl2->compile('tabs-title');
																$ifrm = '' . $tabsvalue['iframe_url'] . '?season=' . $xfieldsdata[$moonserials_options['field_season_iframe']] . '&episode=' . $xfieldsdata[$moonserials_options['field_series_iframe']] . '&nocontrols=1';
																$tpl3->set('{data-season}', $xfieldsdata[$moonserials_options['field_season_iframe']]);
																$tpl3->set('{data-series}', $tabsvalue['series']);
																$tpl3->set('{ms-content}', $ifrm);
																$tpl3->compile('tabs-content');
														}
												}
												elseif ( $xfieldsdata[$moonserials_options['field_season_iframe']] AND !$xfieldsdata[$moonserials_options['field_series_iframe']] )
												{
														if ( $tabsvalue['seasonfound'] > 0 )
														{
																if ( !$tplstudios )
																		$tplstudios = $tabsvalue['translator'];
																$tpl2->set('{ms-title}', $tabsvalue['translator']);
																$tpl2->compile('tabs-title');
																$ifrm = '' . $tabsvalue['iframe_url'] . '?season=' . $xfieldsdata[$moonserials_options['field_season_iframe']] . '';
																$tpl3->set('{data-season}', $xfieldsdata[$moonserials_options['field_season_iframe']]);
																$tpl3->set('{data-series}', $tabsvalue['series']);
																$tpl3->set('{ms-content}', $ifrm);
																$tpl3->compile('tabs-content');
														}
												}
												elseif ( !$xfieldsdata[$moonserials_options['field_season_iframe']] AND !$xfieldsdata[$moonserials_options['field_series_iframe']] )
												{
														if ( !$tplstudios )
																$tplstudios = $tabsvalue['translator'];
														$tpl2->set('{ms-title}', $tabsvalue['translator']);
														$tpl2->compile('tabs-title');
														$tpl3->set('{ms-content}', $tabsvalue['iframe_url']);
														$tpl3->compile('tabs-content');
												}
										}
										if ( !$tplstudios )
												$tplstudios = $tabs[0]['translator'];
										$tpl1->set('{season}', $tabs[0]['season']);
										$tpl1->set('{studios}', $tplstudios);
										if ( $moonserials_options['add_series_one_tpl'] )
												$tpl1->set('{series}', $tabs[0]['series'] + 1);
										else
												$tpl1->set('{series}', $tabs[0]['series']);
								}
								$season = $tabs[0]['season'];
								$series = $tabs[0]['series'];
								$studios = $tabs[0]['translator'];
								if ( $moonserials_options['allow_module_new'] )
								{
										if ( $season < $xfieldsdata[$moonserials_options['field_season_iframe']] OR ( $xfieldsdata[$moonserials_options['field_season_iframe']] == $season AND $series < $xfieldsdata[$moonserials_options['field_series_iframe']] ))
										{
												$tpl1->set('[soon]', "");
												$tpl1->set('[/soon]', "");
												$tpl1->set_block("'\\[not-soon\\](.*?)\\[/not-soon\\]'si", "");
										}
										else
										{
												$tpl1->set_block("'\\[soon\\](.*?)\\[/soon\\]'si", "");
												$tpl1->set_block("'\\[not-soon\\](.*?)\\[/not-soon\\]'si", "\\1");
										}
								}
								if ( $xfieldsdata[$moonserials_options['field_season_iframe']] )
								{
										$season = $xfieldsdata[$moonserials_options['field_season_iframe']];
										if ( $moonserials_options['allow_module_new'] )
												$tpl1->set('{season}', $xfieldsdata[$moonserials_options['field_season_iframe']]);
								}
								if ( $xfieldsdata[$moonserials_options['field_series_iframe']] )
								{
										$series = $xfieldsdata[$moonserials_options['field_series_iframe']];
										if ( $moonserials_options['allow_module_new'] )
												$tpl1->set('{series}', $xfieldsdata[$moonserials_options['field_series_iframe']]);
								}
								if ( $season AND !$seasonTemp AND $series AND !$seriesTemp AND $xfieldsdata[$moonserials_options['field_status_name']] !== $moonserials_options['field_status'] )
										$notdateup = true;
								else
										$notdateup = false;
								if ( ( ( $seasonTemp AND $seriesTemp ) AND ( ( int ) $season > ( int ) $seasonTemp OR ( int ) $series > ( int ) $seriesTemp ) AND $xfieldsdata[$moonserials_options['field_status_name']] !== $moonserials_options['field_status'] ) OR ( $season AND !$seasonTemp AND $series AND !$seriesTemp AND $xfieldsdata[$moonserials_options['field_status_name']] !== $moonserials_options['field_status'] ))
								{
										$season = $season . " сезон";
										$series = $series . " серия";
										if ( $moonserials_options['field_series_form'] == 'type1' OR $series == '1 серия' AND $moonserials_options['field_series_form'] )
										{
												$series1 = ( $tabs[0]['series'] + 1 ) . " серия";
												$series0 = $tabs[0]['series'] . " серия";
										}
										elseif ( $moonserials_options['field_series_form'] == 'type2' )
										{
												$series1 = "1-" . ( $tabs[0]['series'] + 1 ) . " серия";
												$series0 = "1-" . $tabs[0]['series'] . " серия";
										}
										elseif ( $moonserials_options['field_series_form'] == 'type3' )
										{
												$series1 = $tabs[0]['series'] + 1;
												for ( $i = 1; $i <= $series1; $i++ )
												{
														$seriesSrt .= $i . ",";
												}
												$seriesSrt = substr($seriesSrt, 0, - 1);
												$series1 = $seriesSrt . " серия";
												unset( $seriesSrt );
												for ( $i = 1; $i <= $tabs[0]['series']; $i++ )
												{
														$seriesSrt .= $i . ",";
												}
												$seriesSrt = substr($seriesSrt, 0, - 1);
												$series0 = $seriesSrt . " серия";
												unset( $seriesSrt );
										}
										elseif ( $moonserials_options['field_series_form'] == 'type4' )
										{
												$series1 = $tabs[0]['series'] + 1 . "," . ( $tabs[0]['series'] + 2 ) . "," . ( $tabs[0]['series'] + 3 ) . " серия";
												$series0 = $tabs[0]['series'] . "," . ( $tabs[0]['series'] + 1 ) . "," . ( $tabs[0]['series'] + 2 ) . " серия";
										}
										elseif ( $moonserials_options['field_series_form'] == 'type5' )
										{
												if ( $tabs[0]['series'] <= 5 )
												{
														$series1 = $tabs[0]['series'] + 1;
														for ( $i = 1; $i <= $series1; $i++ )
														{
																$seriesSrt .= $i . ",";
														}
														$seriesSrt = substr($seriesSrt, 0, - 1);
														$series1 = $seriesSrt . " серия";
														unset( $seriesSrt );
														for ( $i = 1; $i <= $tabs[0]['series']; $i++ )
														{
																$seriesSrt .= $i . ",";
														}
														$seriesSrt = substr($seriesSrt, 0, - 1);
														$series0 = $seriesSrt . " серия";
														unset( $seriesSrt );
												}
												else
												{
														$series1 = "1-" . ( $tabs[0]['series'] + 1 ) . "," . ( $tabs[0]['series'] + 2 ) . "," . ( $tabs[0]['series'] + 3 ) . " серия";
														$series0 = "1-" . ( $tabs[0]['series'] ) . "," . ( $tabs[0]['series'] + 1 ) . "," . ( $tabs[0]['series'] + 2 ) . " серия";
												}
										}
										if ( $moonserials_options['field_season_form'] == 'type1' OR $season == '1 сезон' )
												$season0 = $tabs[0]['season'] . " сезон";
										elseif ( $moonserials_options['field_season_form'] == 'type2' )
												$season0 = "1-" . $tabs[0]['season'] . " сезон";
										elseif ( $moonserials_options['field_season_form'] == 'type3' )
										{
												for ( $i = 1; $i <= $tabs[0]['season']; $i++ )
												{
														$seasonSrt .= $i . ",";
												}
												$seasonSrt = substr($seasonSrt, 0, - 1);
												$season0 = $seasonSrt . " сезон";
												unset( $seasonSrt );
										}
										$xfields_n[$moonserials_options['field_season']] = $season;
										if ( $moonserials_options['add_series_one'] > 0 )
												$xfields_n[$moonserials_options['field_series']] = ( $tabs[0]['series'] + 1 ) . ' серия';
										else
												$xfields_n[$moonserials_options['field_series']] = $series;
										if ( $studios AND $studios !== "Неизвестно" AND $moonserials_options['field_studios'] )
												$xfields_n[$moonserials_options['field_studios']] = $studios;
										if ( !$xfieldsdata[$moonserials_options['field_title_ru']] AND $moonserials_options['field_title_ru'] AND $title_ru )
												$xfields_n[$moonserials_options['field_title_ru']] = $title_ru;
										if ( $studios_sp AND $moonserials_options['field_studios_sp'] )
												$xfields_n[$moonserials_options['field_studios_sp']] = $studios_sp;
										if ( $moonserials_options['season_mod'] )
												$xfields_n[$moonserials_options['season_mod']] = $season0;
										if ( $moonserials_options['series_mod'] )
										{
												if ( $moonserials_options['add_series_one'] )
														$xfields_n[$moonserials_options['series_mod']] = $series1;
												else
														$xfields_n[$moonserials_options['series_mod']] = $series0;
										}
										if ( !$xfieldsdata[$moonserials_options['field_series-max']] AND $seriesMax AND $moonserials_options['allow_module_new_series_max'] > 0 )
												$xfields_n[$moonserials_options['field_series-max']] = $seriesMax;
										if ( $moonserials_options['allow_module_new_series_max'] > 0 AND $seriesMax == $tabs[0]['series'] AND $xfieldsdata[$moonserials_options['field_status_name']] !== $moonserials_options['field_status'] )
												$xfields_n[$moonserials_options['field_status_name']] = $moonserials_options['field_status'];
										if ( $moonserials_options['allow_news_update'] AND !$notdateup )
												$myNewDate = ( $moonserials_options['allow_news_update'] != 0 ) ? ", `date` = '" . date('Y-m-d H:i:s') . "'" : false;
										if ( $moonserials_options['allow_news_title_update'] )
										{
												$ms_title_date = !empty( $moonserials_options['ms_title_date'] ) ? langdate($moonserials_options['ms_title_date']) : false;
												$ms_title_preffix = !empty( $moonserials_options['ms_title_preffix'] ) ? $db->safesql(trim(strip_tags(stripslashes($moonserials_options['ms_title_preffix'])))) . " " : false;
												$ms_title_year = !empty( $moonserials_options['ms_title_year'] ) ? trim(strip_tags(stripslashes($xfieldsdata[$moonserials_options['ms_title_year']]))) . " " : false;
												$ms_title_t1 = !empty( $moonserials_options['ms_title_t1'] ) ? $db->safesql(trim(strip_tags(stripslashes($moonserials_options['ms_title_t1'])))) . " " : false;
												$ms_title_t2 = !empty( $moonserials_options['ms_title_t2'] ) ? $db->safesql(trim(strip_tags(stripslashes($moonserials_options['ms_title_t2'])))) . " " : false;
												$ms_title_field = !empty( $moonserials_options['ms_title_field'] ) ? $db->safesql(trim(strip_tags(stripslashes($xfieldsdata[$moonserials_options['ms_title_field']])))) . " " : false;
												if ( $moonserials_options['ms_title_season'] )
												{
														if ( $moonserials_options['ms_title_season_one'] AND $season == "1 сезон" )
																$ms_title_season = false;
														else
														{
																if ( $moonserials_options['title_season_form'] == 'type1' OR $season == '1 сезон' )
																		$ms_title_season = $tabs[0]['season'] . " сезон";
																elseif ( $moonserials_options['title_season_form'] == 'type2' )
																		$ms_title_season = "1-" . $tabs[0]['season'] . " сезон";
																elseif ( $moonserials_options['title_season_form'] == 'type3' )
																{
																		for ( $i = 1; $i <= $tabs[0]['season']; $i++ )
																		{
																				$seasonSrt .= $i . ",";
																		}
																		$seasonSrt = substr($seasonSrt, 0, - 1);
																		$ms_title_season = $seasonSrt . " сезон";
																		unset( $seasonSrt );
																}
														}
												}
												if ( $moonserials_options['ms_title_series'] )
												{
														if ( $moonserials_options['title_series_form'] == 'type1' OR $series == '1 серия' AND $moonserials_options['title_series_form'] )
														{
																$ms_title_series1 = ( $tabs[0]['series'] + 1 ) . " серия";
																$ms_title_series0 = $tabs[0]['series'] . " серия";
														}
														elseif ( $moonserials_options['title_series_form'] == 'type2' )
														{
																$ms_title_series1 = "1-" . ( $tabs[0]['series'] + 1 ) . " серия";
																$ms_title_series0 = "1-" . $tabs[0]['series'] . " серия";
														}
														elseif ( $moonserials_options['title_series_form'] == 'type3' )
														{
																$ms_title_series1 = $tabs[0]['series'] + 1;
																for ( $i = 1; $i <= $ms_title_series1; $i++ )
																{
																		$seriesSrt .= $i . ",";
																}
																$seriesSrt = substr($seriesSrt, 0, - 1);
																$ms_title_series1 = $seriesSrt . " серия";
																unset( $seriesSrt );
																for ( $i = 1; $i <= $tabs[0]['series']; $i++ )
																{
																		$seriesSrt .= $i . ",";
																}
																$seriesSrt = substr($seriesSrt, 0, - 1);
																$ms_title_series0 = $seriesSrt . " серия";
																unset( $seriesSrt );
														}
														elseif ( $moonserials_options['title_series_form'] == 'type4' )
														{
																$ms_title_series1 = $tabs[0]['series'] + 1 . "," . ( $tabs[0]['series'] + 2 ) . "," . ( $tabs[0]['series'] + 3 ) . " серия";
																$ms_title_series0 = $tabs[0]['series'] . "," . ( $tabs[0]['series'] + 1 ) . "," . ( $tabs[0]['series'] + 2 ) . " серия";
														}
														elseif ( $moonserials_options['title_series_form'] == 'type5' )
														{
																if ( $tabs[0]['series'] <= 5 )
																{
																		$ms_title_series1 = $tabs[0]['series'] + 1;
																		for ( $i = 1; $i <= $ms_title_series1; $i++ )
																		{
																				$seriesSrt .= $i . ",";
																		}
																		$seriesSrt = substr($seriesSrt, 0, - 1);
																		$ms_title_series1 = $seriesSrt . " серия";
																		unset( $seriesSrt );
																		for ( $i = 1; $i <= $tabs[0]['series']; $i++ )
																		{
																				$seriesSrt .= $i . ",";
																		}
																		$seriesSrt = substr($seriesSrt, 0, - 1);
																		$ms_title_series0 = $seriesSrt . " серия";
																		unset( $seriesSrt );
																}
																else
																{
																		$ms_title_series1 = "1-" . ( $tabs[0]['series'] + 1 ) . "," . ( $tabs[0]['series'] + 2 ) . "," . ( $tabs[0]['series'] + 3 ) . " серия";
																		$ms_title_series0 = "1-" . ( $tabs[0]['series'] ) . "," . ( $tabs[0]['series'] + 1 ) . "," . ( $tabs[0]['series'] + 2 ) . " серия";
																}
														}
														if ( $moonserials_options['ms_title_series_add'] > 0 )
																$ms_title_series = $ms_title_series1;
														else
																$ms_title_series = $ms_title_series0;
												}
												if ( $ms_title_series && $season !== '1 сезон')
														$ms_title_season = $ms_title_season . " ";
												$myModule_title = $ms_title_season . $ms_title_series . " ";
												$ms_title_up = $ms_title_preffix . $title_ru . " " . $ms_title_year . $ms_title_t1 . $myModule_title . $ms_title_t2 . $ms_title_field . $ms_title_date;
												if ( substr($ms_title_up, - 1) == " " )
														$ms_title_up = substr($ms_title_up, 0, - 1);
												$ms_title_up = ", `metatitle`='" . $ms_title_up . "'";
										}
										if ( $moonserials_options['allow_news_cpu_update'] )
										{
												$ms_cpu_date = !empty( $moonserials_options['ms_cpu_date'] ) ? totranslit(langdate($moonserials_options['ms_cpu_date'])) : false;
												$ms_cpu_preffix = !empty( $moonserials_options['ms_cpu_preffix'] ) ? totranslit($moonserials_options['ms_cpu_preffix']) . '-' : false;
												$ms_cpu_year = !empty( $moonserials_options['ms_cpu_year'] ) ? totranslit($xfieldsdata[$moonserials_options['ms_cpu_year']]) . '-' : false;
												$ms_cpu_t1 = !empty( $moonserials_options['ms_cpu_t1'] ) ? totranslit($moonserials_options['ms_cpu_t1']) . '-' : false;
												$ms_cpu_t2 = !empty( $moonserials_options['ms_cpu_t2'] ) ? totranslit($moonserials_options['ms_cpu_t2']) . '-' : false;
												$ms_cpu_field = !empty( $moonserials_options['ms_cpu_field'] ) ? totranslit($xfieldsdata[$moonserials_options['ms_cpu_field']]) . "_" : false;
												$title_ru_cpu = totranslit($title_ru) . '-';
												if ( $moonserials_options['ms_cpu_season'] )
												{
														if ( $moonserials_options['ms_cpu_season_one'] AND $season == "1 сезон" )
																$ms_cpu_season = false;
														else
														{
																if ( $moonserials_options['cpu_season_form'] == 'type1' OR $season == '1 сезон' )
																		$ms_cpu_season = $tabs[0]['season'] . " сезон";
																elseif ( $moonserials_options['cpu_season_form'] == 'type2' )
																		$ms_cpu_season = "1-" . $tabs[0]['season'] . " сезон";
																elseif ( $moonserials_options['cpu_season_form'] == 'type3' )
																{
																		for ( $i = 1; $i <= $tabs[0]['season']; $i++ )
																		{
																				$seasonSrt .= $i . ",";
																		}
																		$seasonSrt = substr($seasonSrt, 0, - 1);
																		$ms_cpu_season = $seasonSrt . " сезон";
																		unset( $seasonSrt );
																}
														}
												}
												if ( $moonserials_options['ms_cpu_series'] )
												{
														if ( $moonserials_options['cpu_series_form'] == 'type1' OR $series == '1 серия' AND $moonserials_options['cpu_series_form'] )
														{
																$ms_cpu_series1 = ( $tabs[0]['series'] + 1 ) . " серия";
																$ms_cpu_series0 = $tabs[0]['series'] . " серия";
														}
														elseif ( $moonserials_options['cpu_series_form'] == 'type2' )
														{
																$ms_cpu_series1 = "1-" . ( $tabs[0]['series'] + 1 ) . " серия";
																$ms_cpu_series0 = "1-" . $tabs[0]['series'] . " серия";
														}
														elseif ( $moonserials_options['cpu_series_form'] == 'type3' )
														{
																$ms_cpu_series1 = $tabs[0]['series'] + 1;
																for ( $i = 1; $i <= $ms_cpu_series1; $i++ )
																{
																		$seriesSrt .= $i . ",";
																}
																$seriesSrt = substr($seriesSrt, 0, - 1);
																$ms_cpu_series1 = $seriesSrt . " серия";
																unset( $seriesSrt );
																for ( $i = 1; $i <= $tabs[0]['series']; $i++ )
																{
																		$seriesSrt .= $i . ",";
																}
																$seriesSrt = substr($seriesSrt, 0, - 1);
																$ms_cpu_series0 = $seriesSrt . " серия";
																unset( $seriesSrt );
														}
														elseif ( $moonserials_options['cpu_series_form'] == 'type4' )
														{
																$ms_cpu_series1 = $tabs[0]['series'] + 1 . "," . ( $tabs[0]['series'] + 2 ) . "," . ( $tabs[0]['series'] + 3 ) . " серия";
																$ms_cpu_series0 = $tabs[0]['series'] . "," . ( $tabs[0]['series'] + 1 ) . "," . ( $tabs[0]['series'] + 2 ) . " серия";
														}
														elseif ( $moonserials_options['cpu_series_form'] == 'type5' )
														{
																if ( $tabs[0]['series'] <= 5 )
																{
																		$ms_cpu_series1 = $tabs[0]['series'] + 1;
																		for ( $i = 1; $i <= $ms_cpu_series1; $i++ )
																		{
																				$seriesSrt .= $i . ",";
																		}
																		$seriesSrt = substr($seriesSrt, 0, - 1);
																		$ms_cpu_series1 = $seriesSrt . " серия";
																		unset( $seriesSrt );
																		for ( $i = 1; $i <= $tabs[0]['series']; $i++ )
																		{
																				$seriesSrt .= $i . ",";
																		}
																		$seriesSrt = substr($seriesSrt, 0, - 1);
																		$ms_cpu_series0 = $seriesSrt . " серия";
																		unset( $seriesSrt );
																}
																else
																{
																		$ms_cpu_series1 = "1-" . ( $tabs[0]['series'] + 1 ) . "," . ( $tabs[0]['series'] + 2 ) . "," . ( $tabs[0]['series'] + 3 ) . " серия";
																		$ms_cpu_series0 = "1-" . ( $tabs[0]['series'] ) . "," . ( $tabs[0]['series'] + 1 ) . "," . ( $tabs[0]['series'] + 2 ) . " серия";
																}
														}
														if ( $moonserials_options['ms_cpu_series_add'] > 0 )
																$ms_cpu_series = $ms_cpu_series1;
														else
																$ms_cpu_series = $ms_cpu_series0;
												}
												if ( $ms_cpu_series )
														$ms_cpu_season = $ms_cpu_season . " ";
												$myModule_cpu = $ms_cpu_season . $ms_cpu_series;
												$myModule_cpu = str_replace(",", "-", $myModule_cpu);
												$myModule_cpu = totranslit($myModule_cpu);
												$myModule_cpu = $myModule_cpu . '-';
												$ms_cpu_up = $ms_cpu_preffix . $title_ru_cpu . $ms_cpu_year . $ms_cpu_t1 . $myModule_cpu . $ms_cpu_t2 . $ms_cpu_field . $ms_cpu_date;
												if ( substr($ms_cpu_up, - 1) == "-" )
														$ms_cpu_up = substr($ms_cpu_up, 0, - 1);
												$ms_cpu_up = ", `alt_name`='" . $ms_cpu_up . "'";
										}
										if ( $moonserials_options['allow_news_title2_update'] )
										{
												$ms_title2_date = !empty( $moonserials_options['ms_title2_date'] ) ? langdate($moonserials_options['ms_title2_date']) . " " : false;
												$ms_title2_preffix = !empty( $moonserials_options['ms_title2_preffix'] ) ? $db->safesql(trim(strip_tags(stripslashes($moonserials_options['ms_title2_preffix'])))) . " " : false;
												$ms_title2_year = !empty( $moonserials_options['ms_title2_year'] ) ? trim(strip_tags(stripslashes($xfieldsdata[$moonserials_options['ms_title2_year']]))) . " " : false;
												$ms_title2_t1 = !empty( $moonserials_options['ms_title2_t1'] ) ? $db->safesql(trim(strip_tags(stripslashes($moonserials_options['ms_title2_t1'])))) . " " : false;
												$ms_title2_t2 = !empty( $moonserials_options['ms_title2_t2'] ) ? $db->safesql(trim(strip_tags(stripslashes($moonserials_options['ms_title2_t2'])))) . " " : false;
												$ms_title2_field = !empty( $moonserials_options['ms_title2_field'] ) ? $db->safesql(trim(strip_tags(stripslashes($xfieldsdata[$moonserials_options['ms_title2_field']])))) . " " : false;
												if ( $moonserials_options['ms_title2_season'] )
												{
														if ( $moonserials_options['ms_title2_season_one'] AND $season == "1 сезон" )
																$ms_title2_season = false;
														else
														{
																if ( $moonserials_options['title2_season_form'] == 'type1' OR $season == '1 сезон' )
																		$ms_title2_season = $tabs[0]['season'] . " сезон";
																elseif ( $moonserials_options['title2_season_form'] == 'type2' )
																		$ms_title2_season = "1-" . $tabs[0]['season'] . " сезон";
																elseif ( $moonserials_options['title2_season_form'] == 'type3' )
																{
																		for ( $i = 1; $i <= $tabs[0]['season']; $i++ )
																		{
																				$seasonSrt .= $i . ",";
																		}
																		$seasonSrt = substr($seasonSrt, 0, - 1);
																		$ms_title2_season = $seasonSrt . " сезон";
																		unset( $seasonSrt );
																}
														}
												}
												if ( $moonserials_options['ms_title2_series'] )
												{
														if ( $moonserials_options['title2_series_form'] == 'type1' OR $series == '1 серия' AND $moonserials_options['title2_series_form'] )
														{
																$ms_title2_series1 = ( $tabs[0]['series'] + 1 ) . " серия";
																$ms_title2_series0 = $tabs[0]['series'] . " серия";
														}
														elseif ( $moonserials_options['title2_series_form'] == 'type2' )
														{
																$ms_title2_series1 = "1-" . ( $tabs[0]['series'] + 1 ) . " серия";
																$ms_title2_series0 = "1-" . $tabs[0]['series'] . " серия";
														}
														elseif ( $moonserials_options['title2_series_form'] == 'type3' )
														{
																$ms_title2_series1 = $tabs[0]['series'] + 1;
																for ( $i = 1; $i <= $ms_title2_series1; $i++ )
																{
																		$seriesSrt .= $i . ",";
																}
																$seriesSrt = substr($seriesSrt, 0, - 1);
																$ms_title2_series1 = $seriesSrt . " серия";
																unset( $seriesSrt );
																for ( $i = 1; $i <= $tabs[0]['series']; $i++ )
																{
																		$seriesSrt .= $i . ",";
																}
																$seriesSrt = substr($seriesSrt, 0, - 1);
																$ms_title2_series0 = $seriesSrt . " серия";
																unset( $seriesSrt );
														}
														elseif ( $moonserials_options['title2_series_form'] == 'type4' )
														{
																$ms_title2_series1 = $tabs[0]['series'] + 1 . "," . ( $tabs[0]['series'] + 2 ) . "," . ( $tabs[0]['series'] + 3 ) . " серия";
																$ms_title2_series0 = $tabs[0]['series'] . "," . ( $tabs[0]['series'] + 1 ) . "," . ( $tabs[0]['series'] + 2 ) . " серия";
														}
														elseif ( $moonserials_options['title2_series_form'] == 'type5' )
														{
																if ( $tabs[0]['series'] <= 5 )
																{
																		$ms_title2_series1 = $tabs[0]['series'] + 1;
																		for ( $i = 1; $i <= $ms_title2_series1; $i++ )
																		{
																				$seriesSrt .= $i . ",";
																		}
																		$seriesSrt = substr($seriesSrt, 0, - 1);
																		$ms_title2_series1 = $seriesSrt . " серия";
																		unset( $seriesSrt );
																		for ( $i = 1; $i <= $tabs[0]['series']; $i++ )
																		{
																				$seriesSrt .= $i . ",";
																		}
																		$seriesSrt = substr($seriesSrt, 0, - 1);
																		$ms_title2_series0 = $seriesSrt . " серия";
																		unset( $seriesSrt );
																}
																else
																{
																		$ms_title2_series1 = "1-" . ( $tabs[0]['series'] + 1 ) . "," . ( $tabs[0]['series'] + 2 ) . "," . ( $tabs[0]['series'] + 3 ) . " серия";
																		$ms_title2_series0 = "1-" . ( $tabs[0]['series'] ) . "," . ( $tabs[0]['series'] + 1 ) . "," . ( $tabs[0]['series'] + 2 ) . " серия";
																}
														}
														if ( $moonserials_options['ms_title2_series_add'] > 0 )
																$ms_title2_series = $ms_title2_series1;
														else
																$ms_title2_series = $ms_title2_series0;
												}
												if ( $ms_title2_series )
														$ms_title2_season = $ms_title2_season . " ";
												$myModule_title2 = $ms_title2_season . $ms_title2_series . " ";
												$ms_title2_up = $ms_title2_preffix . $title_ru . " " . $ms_title2_year . $ms_title2_t1 . $myModule_title2 . $ms_title2_t2 . $ms_title2_field . $ms_title2_date;
												if ( substr($ms_title2_up, - 1) == " " )
														$ms_title2_up = substr($ms_title2_up, 0, - 1);
												$ms_title2_up = ", `title`='" . $ms_title2_up . "'";
										}
										foreach ( $xfields_n as $key => & $value )
												$arr_field[] = $key . "|" . str_replace('|', '&#124;', $value);
										$xfields_n = implode("||", $arr_field);
										unset( $arr_field );
										$xfields_n = $db->safesql($xfields_n);
										$db->query("UPDATE " . PREFIX . "_post SET `xfields` = '$xfields_n' {$myNewDate} {$ms_title_up} {$ms_title2_up} {$ms_cpu_up} WHERE id = {$post_id}");
										if ( $moonserials_options['sendpm'] AND !$xfieldsdata[$moonserials_options['field_season_iframe']] )
										{
												$user_id = '1';
												$user_id = ( int ) $user_id;
												$now = time();
												$subject = 'Вышла ' . $series . ' сериала ' . $title_ru . '';
												$subject = $db->safesql($subject);
												$from = 'MoonSerials';
												$from = $db->safesql($from);
												$text = '<h3>Вышла ' . $series . ' сериала ' . $title_ru . '</h3>';
												$text .= '<p><b>Теперь можно:</b></p>';
												$text .= '<ul><li><a href="' . $config['http_home_url'] . 'index.php?newsid=' . $post_id . '" target="_blank">Открыть новость на сайте</a></li>';
												$text .= '<li><a href="' . $config['admin_path'] . '?mod=editnews&action=editnews&id=' . $post_id . '" target="_blank">Редактировать в админпанели</a></li>';
												$text .= '<li><a href="' . $config['admin_path'] . '?mod=addnews&action=addnews" target="_blank">Добавить новую новость в админпанели</a></li></ul>';
												$text = $db->safesql($text);
												$db->query("INSERT into " . PREFIX . "_pm (subj, text, user, user_from, date, pm_read, folder) VALUES ('$subject', '$text', '$user_id', '$from', '$now', '0', 'inbox')");
												$db->query("UPDATE " . USERPREFIX . "_users set pm_unread = pm_unread + 1, pm_all = pm_all+1  where user_id = '$user_id'");
										}
								}
						}
						if ( $moonserials_options['allow_module_new'] )
						{
								$tpl1->set("{tabs-title}", $tpl2->result['tabs-title']);
								unset( $tpl2 );
								$tpl1->set("{tabs-content}", $tpl3->result['tabs-content']);
								unset( $tpl3 );
								$tpl1->compile('myModule');
								$myModule = $tpl1->result['myModule'];
						}
						elseif ( $xfieldsdata[$moonserials_options['field_status_name']] !== $moonserials_options['field_status'] )
						{
								if ( $moonserials_options['allow_module_new_season'] AND $moonserials_options['allow_module_new_series'] )
										$myModule = $tabs[0]['season'] . " сезон " . $tabs[0]['series'] . " серия";
								elseif ( $moonserials_options['allow_module_new_season'] AND !$moonserials_options['allow_module_new_series'] )
										$myModule = $tabs[0]['season'] . " сезон";
								elseif ( !$moonserials_options['allow_module_new_season'] AND $moonserials_options['allow_module_new_series'] )
										$myModule = $tabs[0]['series'] . " серия";
						}
						else
								$myModule = $moonserials_options['if_series_ower'];
						create_cache($myConfig['cachePrefix'], $myModule, $cacheName . $config['skin'], $myConfig['cacheSuffix']);
						if ( $is_change )
								$config['allow_cache'] = false;
						if ( $moonserials_options['allow_module_new'] )
								$tpl1->clear();
				}
				echo $myModule;
		}
?>