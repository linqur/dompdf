<?php
    if(isset($_POST['but'])){
      header('Content-type: text/html; charset=utf8');
      $url = 'https://a.pr-cy.ru/api/v1.1.0/analysis/base';
      $params = ['key' => ''];
      $domain = !empty($_POST['domain']) ? $_POST['domain'] : '';
      $domain = str_ireplace('https://','',$domain);  
    if (!empty($domain)) {
        try {
            $data = @file_get_contents("$url/$domain?key=".$params['key']);
            $data = @json_decode($data, true);
            $try = true;
        } catch (Exception $e) {
            echo "Что-то пошло не так";
            $try = false;
        }
    }
    else{
    	echo "Домен не указан";
    	$try = false;
    }
    function get_img($url,$name){
    	$url = 'http://s3-eu-west-1.amazonaws.com/s3.pr-cy.ru/'.$url;
		$img_save = get_img_asist($url);
		if (!file_exists(__DIR__ . '/img')){
			mkdir(__DIR__ . '/img', 0777);
		}
		file_put_contents(__DIR__ .'/img/'. $name, $img_save);
	}
	function get_img_asist($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 0);
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result=curl_exec($ch);
		curl_close($ch);
		return $result;
	}
if($try){
require_once("dompdf/dompdf_config.inc.php");
$html = '';
/*Поисковые системы*/
$true = 0;
$false = 0;
$info = 0;
$next = false;
if($data['yandexCitation'] != null || $data['yandexRank'] != null || $data['yandexIndex'] != null || $data['googleIndex'] != null || $data['yandexCatalog'] != null || $data['yandexGlue'] != null || $data['googleGlue'] != null || $dat['yandexAgs'] != null || $data['roskomnadzor'] != null || $data['yandexSafeBrowsing'] != null || $data['googleSafeBrowsing'] != null){
$html .= '<h2>Поисковые системы</h2>';
if($data['yandexCitation'] != null || $data['yandexRank'] != null){
$html .= '<h3>Основные парпаметры</h3><hr><table>';
	if($data['yandexCitation'] != null){
		($data['yandexCitation']['yandexCitation']==0)?$false++:$true++;
		$html .= '<tr><td>'.(($data['yandexCitation']['yandexCitation']==0)?'<img class="false" src="'.__DIR__.'/img/false.png">':'<img src="'.__DIR__.'/img/true.png">').' Яндекс ТИЦ</td><td>'.$data['yandexCitation']['yandexCitation'].'</td></tr>'.
		'<tr class="info"><td colspan="2"><p>Показатель поисковой системы Яндекс, предназначенный для определения авторитетности сайтов путём подсчёта количества ссылающихся на него ресурсов сходной тематики. Используется для оценки релевантности (степени соответствия поисковым запросам) сайтов в Яндекс.Каталоге, что позволяет определить значимость того или иного проекта.</p></td></tr>';
	}
	if($data['yandexRank'] != null){
		($data['yandexRank']['yandexRank']==0)?$false++:$true++;
		$html .= '<tr><td>'.(($data['yandexRank']['yandexRank']==0)?'<img class="false" src="'.__DIR__.'/img/false.png">':'<img src="'.__DIR__.'/img/true.png">').'Яндекс Rank</td><td>'.$data['yandexRank']['yandexRank'].' из 6</td></tr>'.
			'<tr class="info"><td colspan="2"><p>Показатель поисковой системы Яндекс, напрямую зависящий от числа ТИЦ. На практике не было выявлено влияние данного параметра на что-либо.</p></td></tr>';
	}	
	$html .= '</table>';
}
if($data['yandexIndex'] != null || $data['googleIndex'] != null){
$html .= '<h3>Индексация</h3><hr><table>';
	if($data['yandexIndex'] != null){
		($data['yandexIndex']['yandexIndex']==0)?$false++:$true++;
		$html .= '<tr><td>'.(($data['yandexIndex']['yandexIndex']==0)?'<img class="false" src="'.__DIR__.'/img/false.png">':'<img src="'.__DIR__.'/img/true.png">').'Яндекс</td><td>'.$data['yandexIndex']['yandexIndex'].'<p><br/></p></td></tr>';
	}
	if($data['googleIndex'] != null){
		($data['googleIndex']['googleIndex']==0)?$false++:$true++;
		$html .= '<tr><td>'.(($data['googleIndex']['googleIndex']==0)?'<img class="false" src="'.__DIR__.'/img/false.png">':'<img src="'.__DIR__.'/img/true.png">').'Google</td><td>'.$data['googleIndex']['googleIndex'].'<p><br/></p></td></tr>';
	}
	$html .= '</table>';
}
if($data['yandexCatalog'] != null){
$html .= '<h3>Каталог</h3><hr><table>';
		$html .= '<tr><td><img height="12px" src="'.__DIR__.'/img/info.png" style="margin-top:5px;">Яндекс.Каталог</td><td>'.($data['yandexCatalog']['yandexCatalog'] ? 'Да':'Нет').'</td></tr>'.
		'<tr class="info"><td colspan="2"><p>Размещение сайтов в Яндекс.Каталоге происходит после подачи соответствующей заявки и рассмотрения её модератором.</p><p>Многие веб-мастеры считают, что при выдаче результатов поисковая система «Яндекс» по определённым запросам ранжирует веб-сайты, которые присутствуют в сервисе Яндекс.Каталог, выше остальных при других одинаковых условиях за счет того, что информация из сервиса Яндекс.Каталог используется в отдельных факторах определения ранга.</p></td></tr>';
	$html .= '</table>';
}
if($data['yandexGlue'] != null || $data['googleGlue'] != null || $dat['yandexAgs'] != null || $data['roskomnadzor'] != null){
$html .= '<h3>Санкции</h3><hr><table>';
	if($data['yandexGlue'] != null){
		($data['yandexGlue']['yandexGlue']==false)?$true++:$false++;
		$html .= '<tr><td>'.(($data['yandexGlue']['yandexGlue']==false)?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Склейка домена (Яндекс)</td><td>'.($data['yandexGlue']['yandexGlue'] ? 'Яндекс считает домен склеенным.':'Яндекс не считает домен склеенным.').'</td></tr>'.
		'<tr class="info"><td colspan="2"><p>Обнаруживая сайты с одинаковой информацией, робот Яндекса выбирает один из них в качестве главного зеркала, а все остальные исключает из индексации. Чтобы основным был выбран именно продвигаемый домен, необходимо сообщить поисковой системе, какое зеркало является главным. Следует учитывать, что обновление этой информации происходит один раз в 2-12 недель.</p>'.
			'<p>Чтобы подсказать, какой сайт является основным, а какой — зеркалом, для Google используют 301 редирект, а для Яндекса — специальную директиву host в файле robots.txt. Стоит заметить, что одновременно воспользоваться двумя способами не получится.</p>'.
			'<p><a href="http://pr-cy.ru/news/p/1937">Как склеить домены</a></p></td></tr>';
	}
	if($data['googleGlue'] != null){
		($data['googleGlue']['googleGlue']==false)?$true++:$false++;
		$html .= '<tr><td>'.(($data['googleGlue']['googleGlue']==false)?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Склейка домена (Google)</td><td>'.($data['googleGlue']['googleGlue'] ? 'Google считает домен склеенным.':'Google не считает домен склеенным.').'</td></tr>'.
		'<tr class="info"><td colspan="2"><p>Главное имя домена это то, по которое проиндексировано в Google.</p>'.
		'<p>Чтобы указать основное зеркало сайта, нужно сделать принудительную переадресацию (с www на без www, например). Так же можно указать директиву Host в файле robots.txt. Обязательно проверьте в инструментах Google Webmaster правильную настройку.</p></td></tr>';
	}
	if($dat['yandexAgs'] != null){
		($data['yandexAgs']['yandexAgs']==false)?$true++:$false++;
		$html .= '<tr><td>'.(($data['yandexAgs']['yandexAgs']==false) ?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Фильтр АГС</td><td>'.($data['yandexAgs']['yandexAgs'] ? 'Фильтр обнаружен.':'Фильтр не обнаружен.').'</td></tr>'.
		'<tr class="info"><td colspan="2"><p>Чтобы не попасть под фильтры, размещайте на сайте качественную и нужную информацию, которая несет в себе какую-либо пользу для посетителей.</p>'.
		'<p>АГС — фильтр поисковой системы «Яндекс», ориентированный на обнаружение сайтов с малополезным контентом, сделанных, как правило, для продажи 	ссылок. С помощью данного алгоритма «Яндекс» заносит сайты в черный список.</p>'.
		'<p>Теперь вместо исключения страниц таких сайтов из поиска им будет аннулироваться тИЦ. Это изменение распространяется также на все сайты, которые были обнаружены алгоритмом АГС ранее.</p>'.
		'<p>Ссылки с таких сайтов по-прежнему не будут учитываться в ранжировании, а сами сайты могут ранжироваться ниже.</p>'.
		'<p><a href="http://pr-cy.ru/news/p/5269">Как быть уверенным что это АГС</a></p>'.
		'<p><a href="http://pr-cy.ru/news/p/5227">Обновленный АГС Яндекса</a></p></td></tr>';
	}
	if($data['roskomnadzor'] != null){
		($data['roskomnadzor']['roskomnadzorDomainForbidden']==false)?$true++:$false++;
		$html .= '<tr><td>'.(($data['roskomnadzor']['roskomnadzorDomainForbidden']==false) ?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Реестр запрещённых сайтов</td><td>'.($data['roskomnadzor']['roskomnadzorDomainForbidden'] ? 'Домен найден в реестре.':'Домен не найден в реестре.').'</td></tr>'.
		'<tr class="info"><td colspan="2"><p>Единый реестр доменных имён, указателей страниц сайтов в сети «Интернет» и сетевых адресов, позволяющих идентифицировать сайты в сети «Интернет», содержащие информацию, распространение которой в Российской Федерации запрещено</p>'.
		'<p>По информации Роскомнадзора, обжаловать решение о включении сайта в реестр могут владелец сайта, провайдер хостинга или оператор связи в судебном порядке в трёхмесячный срок.</p>'.
		'<p>Также, в случае удаления противоправной информации, возможно удаление сайта из реестра по обращению владельца сайта, провайдера хостинга или оператора связи. Такие обращения должны быть обработаны Роскомнадзором в течение трёх дней</p>'.
		'<p>Чтобы снять блокировку, нужно убрать материалы на сайте, из-за которых вы получили блокировку. После этого написать письмо на адрес: zapret-info@rsoc.ru</p>'.
		'<p><a href="https://eais.rkn.gov.ru/">Сайт Единого реестра</a></p></td></tr>';
	}
	$html .= '</table>';
}
if($data['yandexSafeBrowsing'] != null || $data['googleSafeBrowsing'] != null){
$html .='<h3>Проверка на вирусы</h3><hr/><table>';
	if($data['yandexSafeBrowsing'] != null){
		$data['yandexSafeBrowsing']['yandexSafeBrowsing']?$true++:$false++;
		$html .= '<tr><td>'.($data['yandexSafeBrowsing']['yandexSafeBrowsing'] ?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Вирусы от Yandex</td><td>'.($data['yandexSafeBrowsing']['yandexSafeBrowsing']?'Сайт не содержит вирусов.':'Сайт содержит вирусы.').'</td></tr>'.
		'<tr class="info"><td colspan="2"><p>Обычно заражение происходит вследствие какой-либо уязвимости, позволившей хакеру получить контроль над сайтом. Он может изменять содержание сайта (например, добавлять спам) или создавать на сайте новые страницы, обычно с целью фишинга (получения личных данных и информации о кредитных картах обманным путем). Хакеры могут также внедрять вредоносный код, например скрипты или фреймы iframe, которые извлекают содержание с другого сайта для атаки компьютеров, с которых выполняется просмотр данной страницы.</p>'.
		'<p><a href="https://webmaster.yandex.ru/">Панель веб-мастера Яндекс</a></p></td></tr>';
	}
	if($data['googleSafeBrowsing'] != null){
		$data['googleSafeBrowsing']['googleSafeBrowsing']?$true++:$false++;
		$html .= '<tr><td>'.($data['googleSafeBrowsing']['googleSafeBrowsing'] ?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Вирусы от Google</td><td>'.($data['googleSafeBrowsing']['googleSafeBrowsing']?'Сайт не содержит вирусов.':'Сайт содержит вирусы.').'</td></tr>'.
		'<tr class="info"><td colspan="2"><p>Обычно заражение происходит вследствие какой-либо уязвимости, позволившей хакеру получить контроль над сайтом. Он может изменять содержание сайта (например, добавлять спам) или создавать на сайте новые страницы, обычно с целью фишинга (получения личных данных и информации о кредитных картах обманным путем). Хакеры могут также внедрять вредоносный код, например скрипты или фреймы iframe, которые извлекают содержание с другого сайта для атаки компьютеров, с которых выполняется просмотр данной страницы.</p>'.
		'<p><a href="https://www.google.com/webmasters/">Панель веб-мастера Google</a></p></td></tr>';
	}
	$html .= '</table>';
}
$next = true;
}
/*Поисковые системы*/
/*Трафик*/
if($data['publicStatistics'] != null || $data['trafficSources'] != null || $data['alexaRank'] != null || $data['alexaRelatedSites'] != null || $data['statisticsSystems'] != null){
if($next){$html .= '<div style="page-break-before: always;"></div>'; $next = false;}
$html .= '</div><h2>Трафик</h2>';
$html .= '<h3>Посещаемость</h3><hr><table>';
	if($data['publicStatistics'] != null){
		$html .= '<tr><td style="vertical-align:top;"><img height="12px" src="'.__DIR__.'/img/info.png" style="margin-top:5px;">Открытая статистика</td><td><table>'.
			'<tr class="tr_head"><th>Данные</th><th>День</th><th>Неделя</th><th>Месяц</th></tr>'.
			'<tr><td>Просмотры</td><td style="text-align:right;">'.$data['publicStatistics']['publicStatisticsPageViewsDaily'].'</td><td style="text-align:right;">'.$data['publicStatistics']['publicStatisticsPageViewsWeekly'].'</td><td style="text-align:right;">'.$data['publicStatistics']['publicStatisticsPageViewsMonthly'].'</td></tr>'.
			'<tr><td>Посетители</td><td style="text-align:right;">'.$data['publicStatistics']['publicStatisticsVisitsDaily'].'</td><td style="text-align:right;">'.$data['publicStatistics']['publicStatisticsVisitsWeekly'].'</td><td style="text-align:right;">'.$data['publicStatistics']['publicStatisticsVisitsMonthly'].'</td></tr>'.
				'<tr><td colspan="4">Источнки: <a href="'.$data['publicStatistics']['publicStatisticsSourceLink'].'
			"> Alexа(примерно)</a></td></tr></table><p><br/></p></td>';
	}
	if($data['trafficSources'] != null){
		$html .= 
		'<tr><td style="vertical-align:top;"><img height="12px" src="'.__DIR__.'/img/info.png" style="margin-top:5px;">Источники трафика</td><td><table>';	
		$traf_names = ['Прямые заходы','Поисковые системы','Ссылки на сайтах','Социальные сети','Почтовые рассылки','Реклама'];
		$traf[0][] = ($data['trafficSources']['trafficSourcesDisplay'] == null) ? 0 : round($data['trafficSources']['trafficSourcesDirect'],2);
		if($data['trafficSources']['trafficSourcesDirectHistory']['days'] !=null){
		reset($data['trafficSources']['trafficSourcesDirectHistory']['days']);
		$key = key($data['trafficSources']['trafficSourcesDirectHistory']['days']);}
		$traf[0][] = ($data['trafficSources']['trafficSourcesDirectHistory'][$key] == null) ? 0 : round($data['trafficSources']['trafficSourcesDirectHistory']['days'][$key],2);/*прямыезаходы*/

		$traf[1][] = ($data['trafficSources']['trafficSourcesSearch']== null) ? 0:round($data['trafficSources']['trafficSourcesSearch'],2); /*Поисковые системы*/
		if($data['trafficSources']['trafficSourcesSearchHistory']['days'] !=null){
		reset($data['trafficSources']['trafficSourcesSearchHistory']['days']);
		$key = key($data['trafficSources']['trafficSourcesSearchHistory']['days']);}
		$traf[1][] = ($data['trafficSources']['trafficSourcesSearchHistory']['days'][$key]== null) ? 0:round($data['trafficSources']['trafficSourcesSearchHistory']['days'][$key],2);

		$traf[2][] = ($data['trafficSources']['trafficSourcesReferrals']== null) ? 0:round($data['trafficSources']['trafficSourcesReferrals'],2);/*Ссылки най стах*/
		if($data['trafficSources']['trafficSourcesReferralsHistory']['days'] !=null){
		reset($data['trafficSources']['trafficSourcesReferralsHistory']['days']);
		$key = key($data['trafficSources']['trafficSourcesReferralsHistory']['days']);}
		$traf[2][] = ($data['trafficSources']['trafficSourcesReferralsHistory']['days'][$key]== null) ? 0:round($data['trafficSources']['trafficSourcesReferralsHistory']['days'][$key],2);

		$traf[3][] = ($data['trafficSources']['trafficSourcesSocial']== null) ? 0:round($data['trafficSources']['trafficSourcesSocial'],2); /*Социальные сети*/
		if($data['trafficSources']['trafficSourcesSocialHistory']['days'] !=null){
		reset($data['trafficSources']['trafficSourcesSocialHistory']['days']);
		$key = key($data['trafficSources']['trafficSourcesSocialHistory']['days']);}
		$traf[3][] = ($data['trafficSources']['trafficSourcesSocialHistory']['days'][$key]== null) ? 0:round($data['trafficSources']['trafficSourcesSocialHistory']['days'][$key],2);

		$traf[4][] = ($data['trafficSources']['trafficSourcesMail']== null) ? 0:round($data['trafficSources']['trafficSourcesMail'],2); /*Социальные сети*/
		if($data['trafficSources']['trafficSourcesMailHistory']['days'] !=null){
		reset($data['trafficSources']['trafficSourcesMailHistory']['days']);
		$key = key($data['trafficSources']['trafficSourcesMailHistory']['days']);}
		$traf[4][] = ($data['trafficSources']['trafficSourcesMailHistory']['days'][$key]== null) ? 0:round($data['trafficSources']['trafficSourcesMailHistory']['days'][$key],2);

		$traf[5][] = ($data['trafficSources']['trafficSourcesDisplay']== null) ? 0:round($data['trafficSources']['trafficSourcesDisplay'],2);/*Реклама*/
		if($data['trafficSources']['trafficSourcesDisplayHistory']['days'] !=null){
		reset($data['trafficSources']['trafficSourcesDisplayHistory']['days']);
		$key = key($data['trafficSources']['trafficSourcesDisplayHistory']['days']);}
		$traf[5][] = ($data['trafficSources']['trafficSourcesDisplayHistory']['days'][$key]== null) ? 0:round($data['trafficSources']['trafficSourcesDisplayHistory']['days'][$key],2);

		for ($i = 0; $i < 6; $i++){
			$val = round($traf[$i][0]-$traf[$i][1],2);
			$html .=
				'<tr><td>'.$traf_names[$i].'</td><td>'.$traf[$i][0].'% '. (($val==0) ? '' : ((($val>0) ? ' &uarr;' : ' &darr;').$val)).'</td></tr>';
		}
		$html .='</table><p><br/></p></td></tr>';
	}
	if($data['alexaRank'] != null){
		$rank[0][] = ($data['alexaRank']['alexaGlobalRank'] == null) ? 0 : $data['alexaRank']['alexaGlobalRank'];
		if($data['alexaRank']['alexaGlobalRankHistory']['days'] !=null){
		reset($data['alexaRank']['alexaGlobalRankHistory']['days']);
		$key = key($data['alexaRank']['alexaGlobalRankHistory']['days']);}
		$rank[0][] = ($data['alexaRank']['alexaGlobalRankHistory']['days'][$key] == null) ? 0 : $data['alexaRank']['alexaGlobalRankHistory']['days'][$key];/*Глобальный рейтинг*/

		$rank[1][] = ($data['alexaRank']['alexaCountryRank'] == null) ? 0 : $data['alexaRank']['alexaCountryRank'];
		if($data['alexaRank']['alexaCountryRankHistory']['days'] !=null){
		reset($data['alexaRank']['alexaCountryRankHistory']['days']);
		$key = key($data['alexaRank']['alexaCountryRankHistory']['days']);}
		$rank[1][] = ($data['alexaRank']['alexaCountryRankHistory']['days'][$key] == null) ? 0 : $data['alexaRank']['alexaCountryRankHistory']['days'][$key];
		$val_glob = $rank[0][0]-$rank[0][1];
		$val_ru = $rank[1][0]-$rank[1][1];
		$html .='<tr><td style="text-align:left; vertical-align:top;"><img height="12px" src="'.__DIR__.'/img/info.png" style="margin-top:5px;">Рейтинг Alexa</td><td><table>'.
				'<tr><td width="100%">Место в мире: '.(($rank[0][0]==0)?'место неизвестно':($rank[0][0].(($val_glob==0)?'': (($val_glob>0)?' &uarr;':' &darr;').$val_glob))).' <td></tr>'.
				'<tr><td width="100%">Место в России: '.(($rank[1][0]==0)?'место неизвестно':($rank[1][0].(($val_ru==0)?'': (($val_ru>0)?' &uarr;':' &darr;').$val_ru))).' <td></tr>'.
				'</table><p><br/></p></td></tr>';
	}
	if($data['alexaRelatedSites'] != null){
		$html .= '<tr><td style="text-align:left; vertical-align:top;"><img height="12px" src="'.__DIR__.'/img/info.png" style="margin-top:5px;">Похожие сайты</td><td><table>';
		foreach ($data['alexaRelatedSites']['alexaRelatedSites'] as $key => $site) {
			$html .= '<tr><td><a href="http://'.$site.'">'.$site.'</a></td></tr>';
		}
		$html .='</table><p><br/></p></td></tr>';
	}
	if($data['statisticsSystems'] != null){
		($data['statisticsSystems']['statisticsSystems'] != null)?$true++:$false++;
		$html .= '<tr><td style="vertical-align:top;">'.(($data['statisticsSystems']['statisticsSystems'] != null) ?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Система статистики</td><td>';
		if($data['statisticsSystems']['statisticsSystems'] != null){
			$html .= '<table>';
			foreach ($data['statisticsSystems']['statisticsSystems'] as $key => $res) {
					$html .= '<tr><td>'.$res.'</td></tr>';
			}
			$html .= '</table></td></tr>';
		}
		else{
			$html .= 'Нет ресурсов</td></tr>';
		}
		$html .='<tr class="info"><td colspan="2"><p>Сбор статистических данных о работе сайта - один из важных этапов продвижения ресурса. Обработка данных о посещениях сайта предоставляет огромное количество необходимых для бизнеса сведений. Любой посетитель может быть потенциальным клиентом, поэтому владельцы сайтов стараются узнать о своих пользователях как можно больше информации. В этом им помогает статистика посещений.</p>'.
			'<p>Яндекс Метрика и Google Analytics – это популярные бесплатные сервисы обработки данных. Они предоставляют все необходимые отчеты о посещениях вашего сайта и способствуют скорейшей индексации страниц в поисковых системах.</p>'.
			'<p><a href="http://metrika.yandex.ru/">Яндекс Метрика</a></p><p><a href="http://www.google.com/analytics/">Google Analytics</a></p><p><a href="http://almost.ru/check-traffic/">Проверка посещаемости конкурентов</a></p></td></tr>';
	}
	$html .= '</table>';
	$next = true;
}
/*Трафик*/

/*Ссылки на сайт*/
if($data['solomonoDataHrefs'] != null || $data['solomonoDataDonors'] != null || $data['socialCounters'] != null || $data['facebookSocial'] != null || $data['vkontakteSocial'] != null || $data['googlePlusSocial'] != null || $data['twitterSocial'] != null){
if($next){$html .= '<div style="page-break-before: always;"></div>'; $next = false;}
$html .= '<h2>Ссылки на сайт</h2>';
if($data['solomonoDataHrefs'] != null || $data['solomonoDataDonors'] != null){
$html .= '<h3>Данные LinkPad</h3><hr><table>';
	if($data['solomonoDataHrefs'] != null){
		$val_hrefs = $data['solomonoDataHrefs']['solomonoHrefsDiff'];
		$html .= '<tr><td><img height="12px" src="'.__DIR__.'/img/info.png" style="margin-top:5px;">Ссылается страниц</td><td><p>'.$data['solomonoDataHrefs']['solomonoHrefs'].' '.(($val_hrefs==0)?'':((($val_hrefs>0)?'&uarr;':'&darr;').$val_hrefs)).'</p></td></tr>';
	}
	if($data['solomonoDataDonors'] != null){
		$val_dom = $data['solomonoDataDonors']['solomonoDonorsDiff'];
		$html .= '<tr><td><img height="12px" src="'.__DIR__.'/img/info.png" style="margin-top:5px;">Ссылается доменов</td><td><p>'.$data['solomonoDataDonors']['solomonoDonors'].' '.(($val_dom==0)?'':((($val_dom>0)?'&uarr;':'&darr;').$val_dom)).'</p></td></tr>';
	}
	$html .= '</table>';
}
if($data['socialCounters'] != null || $data['facebookSocial'] != null || $data['vkontakteSocial'] != null || $data['googlePlusSocial'] != null || $data['twitterSocial'] != null){
$html .= '<h3>Социальные сети</h3><hr><table>';
	if($data['socialCounters'] != null){
		($data['socialCounters']['overallCount'] !=0)?$true++:$false++;
		$html .= 
		'<tr><td>'.(($data['socialCounters']['overallCount'] !=0) ?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Социальная активность</td><td><table><tr><td width="280px">Общая социальная активность составляет</td><td>'.$data['socialCounters']['overallCount'].'</td></tr>'.
		'<tr><td>ВКонтакте</td><td>'.$data['socialCounters']['vkontakteShareCount'].'</td></tr>'.
		'<tr><td>Facebook шареды</td><td>'.$data['socialCounters']['facebookLinkShareCount'].'</td></tr>'.
		'<tr><td>Google+ </td><td>'.$data['socialCounters']['googlePlusCount'].'</td></tr></table></td></tr>'.
		'<tr class="info"><td colspan="2"><p>Ссылки из социальных сетей не имеют определяющего значения на положение сайта в выдаче и не передают вес сайту, но тем не менее косвенно влияют на его продвижение. Социальные сети и блогосфера – это миллионы людей, которые своими поведенческими реакциями отражают в том числе и свое отношение к сайтам.</p>'.
		'<p>Для поисковиков социальные факторы – это в первую очередь человеческие сигналы, которые так же можно применять для улучшения позиций сайта в выдаче. Если у вашей компании нет своей странички/группы в социальной сети, вы не ведете корпоративный блог или твиттер, имеет смысл хотя бы разместить на сайте кнопки социальных сетей для привлечения дополнительного трафика.</p>'.
		'<p>Система не определяет группы или профили с Goolge+. Важно указать ссылку на бизнес-страничку в социальной сети. На страничке в соц. сети должна быть ссылка на ваш сайт.</p>'.
		'<p><a href="http://pr-cy.ru/news/p/5539">Почему социальная страница не найдена?</a></p>'.
		'<p><a href="https://sociale.ru">Сервис социальных кнопок</a></p></td></tr>';
	}
	if($data['facebookSocial'] != null){
		$data['facebookSocial']['link']?$true++:$false++;
		$html .= '<tr><td>'.($data['facebookSocial']['link'] ?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Facebook</td><td>'.(($data['facebookSocial']['link']!=false) ? '<a href="'.$data['facebookSocial']['link'].'">'.$data['facebookSocial']['link'].'</a>':'Ссылка на страницу в Фейсбук не найдена.').'</td></tr>'.
		'<tr class="info"><td colspan="2"><p>Cсылки с Facebook индексируются поисковиками. И наибольшую пользу приносят лайки самой социальной странички компании. Именно такие ссылки учитываются поисковыми системами. Чем больше лайков получает страница в Facebook продвигаемого сайта, тем больше ссылок с разных страниц пользователей социальной сети будет стоять на продвигаемый сайт.</p>'.
		'<p>Наибольшую ценность и вес, представляют лайки авторитетных аккаунтов и известных страниц. Это так же положительно сказывается и на трафике сайта. Для привлечения лайков на продвигаемый сайт можно создавать интересный контент, проводить различные конкурсы и опросы.</p>'.
		'<p>Количество поделившихся вашей ссылкой на Facebook схоже с обратными ссылками, поэтому может иметь даже большую ценность, чем лайки.</p>'.
		'<p>Система не определяет группы или профили с Facebook. Важно указать ссылку на бизнес-страничку в социальной сети. На страничке в соц. сети должна быть ссылка на ваш сайт.</p>'.
		'<p><a href="http://pr-cy.ru/news/p/5539">Почему социальная страница не найдена?</a></p>'.
		'<p><a href="http://pr-cy.ru/news/p/5406">Как добавить информацию на сайте о социальных страницах</a></p></td></tr>';
	}
	if($data['vkontakteSocial'] != null){	
		$data['vkontakteSocial']['link'] ?$true++:$false++;
		$html .=
		'<tr><td>'.($data['vkontakteSocial']['link'] ?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'ВКонтакте</td><td>'.(($data['vkontakteSocial']['link'] !=flase) ? '<a href="'.$data['vkontakteSocial']['link'].'">'.$data['vkontakteSocial']['link'].'</a>':'Ссылка на страницу в ВКонтакте не найдена.').'</td></tr>'.
			'<tr class="info"><td colspan="2"><p>ВКонтакте все ссылки стоят через редирект. В выдаче Яндекса можно найти профили из ВКонтакте, а в поиске по блогам – статусы и заметки, где так же могут быть размещены и ссылки. Это может обеспечить дополнительный трафик.</p>'.
			'<p>Яндекс так же обращает внимание на количество человек в группе продвигаемого сайта и учитывает эти данные при ранжировании сайта.</p>'.
			'<p><a href="http://pr-cy.ru/news/p/5406">Как добавить информацию на сайте о социальных страницах</a></p>'.
			'<p><a href="http://rssvk.com">Сервис автопостинга через RSS в VK</a></p></td></tr>';
		}
	if($data['googlePlusSocial'] != null){	
		$data['googlePlusSocial']['link']?$true++:$false++;
		$html .=
		'<tr><td>'.($data['googlePlusSocial']['link'] ?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Google+</td><td>'.(($data['googlePlusSocial']['link'] !=false)? '<a href="'.$data['googlePlusSocial']['link'] .'">'.$data['googlePlusSocial']['link'] .'</a>':'Ссылка на страницу в Google+ не найдена.').'</td></tr>'.
			'<tr class="info"><td colspan="2"><p>В выдаче Google отдает предпочтение сайтам, добавленным в Google+ Круги. При поиске по местным результатам Google часто ставит местные Google+ результаты выше других.</p>'.
			'<p>Положительно на продвижение влияют не только +1 от авторитетных пользователей (Google Author Rang), но и пользователей старой почты Gmail. Проверенное авторство на информацию в Google+ служит залогом доверия у поисковой машины, т.е. в выдаче информация от проверенного аккаунта будет иметь большую релевантность, чем информация без подобной проверки.</p>'.
			'<p>Не стоит искусственно накручивать +1, это чревато санкциями со стороны поисковика, ведь определить такие манипуляции для Google не представляет большого труда.</p>'.
			'<p><a href="http://pr-cy.ru/news/p/5406">Как добавить информацию на сайте о социальных страницах</a></p></td></tr>';
	}
	if($data['twitterSocial'] != null){	
		$data['twitterSocial']['link']?$true++:$false++;
		$html .=
		'<tr><td>'.($data['twitterSocial']['link'] ?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Twitter</td><td>'.(($data['twitterSocial']['link']!=false) ? '<a href="'.$data['twitterSocial']['link'] .'">'.$data['twitterSocial']['link'] .'</a>':'Ссылка на страницу в Twitter не найдена.').'</td></tr>'.
			'<tr class="info"><td colspan="2"><p>Поисковые системы охотно индексируют твиты. У Яндекса даже есть отдельный поиск по твитам. Ссылки в твитах также индексируются, в том числе и непрямые (например, через сервисы goo.gl и др.). При этом твиттер индексируют быстророботы. Чтобы ссылка с твиттера имела влияние на продвижение сайта в Google и Яндексе, она должна быть проиндексирована поисковыми системами.</p>'.
			'<p>Твиттер помогает продвижению сайта и ускоряет его индексацию.</p>'.
			'<p><a href="http://pr-cy.ru/news/p/5406">Как добавить информацию на сайте о социальных страницах</a></p></td></tr>';
	}
	$html .= '</table>';
}
$next = true;
}
/*Ссылки на сайт*/

/*отптимизация*/
if($data['mainPageTitle'] != null || $data['mainPageDescription'] != null || $data['mainPageHeaders'] != null || $data['mainPageWordsCount'] != null || $data['mainPageTextLength'] != null || $data['mainPageSickness'] != null || $data['mainPagePageSize'] != null || $data['loadTime'] != null || $data['mainPageInternalLinks'] != null || $data['mainPageExternalLinks'] != null || $data['htmlValidator'] != null || $data['wot'] != null || $data['microdataSchemaOrg'] != null || $data['microdataOpenGraph'] != null || $data['ip'] != null || $data['ipCountry'] != null || $data['ipIsp'] != null || $data['whoisCreationDate'] != null || $data['whoisExpirationDate'] != null || $data['ssl'] != null || $data['wwwRedirect'] != null || $data['mainPageEncoding'] != null || $data['mainPageTechs'] != null || $data['robotsTxt'] != null || $data['sitemap'] != null){
if($next){$html .= '<div style="page-break-before: always;"></div>'; $next = false;}
$html .='<h2>Оптимизация</h2>';
if($data['mainPageTitle'] != null || $data['mainPageDescription'] != null || $data['mainPageHeaders'] != null || $data['mainPageWordsCount'] != null || $data['mainPageTextLength'] != null || $data['mainPageSickness'] != null || $data['mainPagePageSize'] != null || $data['loadTime'] != null || $data['mainPageInternalLinks'] != null || $data['mainPageExternalLinks'] != null || $data['htmlValidator'] != null || $data['wot'] != null || $data['microdataSchemaOrg'] != null || $data['microdataOpenGraph'] != null){
$html .='<h3>Контент</h3><hr><table>';
	if($data['mainPageTitle'] != null){	
		$data['mainPageTitle']['titleIsGood'] ?$true++:$false++;
		$html .= 
			'<tr><td>'.($data['mainPageTitle']['titleIsGood'] ?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Заголовок страницы</td><td>'.(($data['mainPageTitle']['titleIsGood'])?($data['mainPageTitle']['title'].'<br/><b>Длинна:</b>'.$data['mainPageTitle']['titleLength']):('Заголовок не найден')).'</td></tr>'.
			'<tr class="info"><td colspan="2"><p>Заголовком страницы выступает тег title, который является ключевым в SEO-структуре сайта. Тот заголовок, который прописан в теге title, и выдаётся в результатах поисковой машины.</p>'.
			'<p>Текст, который является заголовком страницы, обязательно должен нести полную информативность, чёткость, быть уникальным и варьироваться в размере от 10 до 70 символов.</p></td></tr>';
		}
	if($data['mainPageDescription'] != null){	
		$data['mainPageDescription']['descriptionIsGood']?$true++:$false++;
		$html .= 
			'<tr><td>'.($data['mainPageDescription']['descriptionIsGood'] ?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Описание страницы</td><td>'.(($data['mainPageDescription']['descriptionIsGood'])?$data['mainPageDescription']['description']:('Не найдено')).'</td></tr>'.
			'<tr class="info"><td colspan="2"><p>Описание страницы отображается в мета-теге description. Для каждой страницы должно быть своё описание. Важность описания страницы в том, что поисковая система может использовать его для создания сниппетов. Описание имеет влияние на ранжирование результатов в поисковике.</p>'.
			'<p>Напишите для каждой страницы description длиною от 70 до 160 символов (включая пробелы). Используйте ключевые слова, которые максимально отображают суть текста. Сделайте текст уникальным. Наиболее важные ключевые слова расположите в начале описания.</p></td></tr>';
	}
	if($data['mainPageHeaders'] != null){	
		($data['mainPageHeaders']['headersCount']['h1']!=0)?$true++:$false++;
		$html .= 
			'<tr><td>'.(($data['mainPageHeaders']['headersCount']['h1']!=0) ?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Заголовки</td><td>'.
			'<table><tr><td><b>h1:</b>'.$data['mainPageHeaders']['headersCount']['h1'].'</td>'.
			'<td><b>H2:</b>'.$data['mainPageHeaders']['headersCount']['h2'].'</td>'.
			'<td><b>H3:</b>'.$data['mainPageHeaders']['headersCount']['h3'].'</td>'.
			'<td><b>H4:</b>'.$data['mainPageHeaders']['headersCount']['h4'].'</td>'.
			'<td><b>H5:</b>'.$data['mainPageHeaders']['headersCount']['h5'].'</td>'.
			'<td><b>H6:</b>'.$data['mainPageHeaders']['headersCount']['h6'].'</td></tr></table></td></tr>'.
			'<tr class="info"><td colspan="2"><p>Заголовки на странице (теги h2-h6) используются для показания важности текста, который расположен после каждого заголовка. С его помощью вы можете структурировать свой текст по подзаголовкам, что придаст тексту более ухоженный и упорядоченный вид при продвижении вашего сайта.</p>'.
			'<p>Наиболее важным тегом является h2, то есть самый главный заголовок, который стоит размещать сверху страницы. Не добавляйте более одного тега h2, так как поисковый робот может неоднозначно определить данный тег и отбросить важную информацию.</p>'.
			'<p>Подзаголовки h2-h6 применяйте сколько угодно по своему усмотрению. Грамотное использование тегов заголовков поможет стимулировать рост трафика. Не нужно размещать в тегах заголовков полностью весь текст, ведь поисковик может определить только первые несколько слов текста, а остальные отбросить.</p></td></tr>';
	}
	if($data['mainPageWordsCount'] != null){	
		$data['mainPageWordsCount']['wordsCount']?$true++:$false++;
		$html .=
			'<tr><td>'.($data['mainPageWordsCount']['wordsCount']?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Количество слов</td><td>'.$data['mainPageWordsCount']['wordsCount'].' слов</td></tr>'.
			'<tr class="info"><td colspan="2"><p>Длина текста на странице не должна быть слишком короткой — иначе в ней не будет достаточного количества ключевых слов, но и не быть слишком длинной — в этом случае статья станет "размытой" в глазах поисковых систем и ключевые слова затеряются в длинном тексте.</p>'.
			'<p>Оптимальная длина текста — где-то 1000-2000 слов для двух-трёх продвигаемых ключевых слов/фраз. Конечно, по тем или иным причинам не всегда удаётся уложиться в эти рамки. Кстати, такая длина текста хороша не только для поисковых систем, но и для посетителя. Люди не любят читать уж больно длинные тексты, как и текст, разбитый на тысячу страниц.</p>'.
			'<p><a href="http://pr-cy.ru/analysis_content/">Анализ контента сайта</a><p>'.
			'<p><a href="http://almost.ru/check-contents/">Проверка вхождений сайтов конкурентов</a></p></td></tr>';
	}
	if($data['mainPageTextLength'] != null){
		$data['mainPageTextLength']['textLength']?$true++:$false++;	
		$html .=
			'<tr><td>'.($data['mainPageTextLength']['textLength']?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Длина текста</td><td>'.$data['mainPageTextLength']['textLength'].' символов<div style="page-break-before: always;"></div></tr>';
	}
	if($data['mainPageSickness'] != null){	
		($data['mainPageSickness']['sickness']<8)?$true++:$false++;
		$html .= 
			'<tr><td>'.(($data['mainPageSickness']['sickness']<8) ?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Тошнота (без стоп слов)</td><td>'.$data['mainPageSickness']['sickness'].'</td></tr>'.
			'<tr class="info"><td colspan="2"><p>Тошнотой является один из качественных показателей текста и подразумевает частоту повтора в текстовом документе одинаковых слов. "Академическая частота" равная доле повторяемых слов ко всему объему текста.</p>'.
			'<p>Тексты с высоким уровнем тошноты (выше 8%) имеют низкое качество, считаются заспамленными, обладают плохой читабельностью, что, несомненно, отпугнет реальных посетителей. А поисковые машины при их обнаружении снижают свой траст к сайту и могут даже его забанить. Низкий же уровень тошноты не поможет в продвижении сайта.</p>'.
			'<p>Занимаясь написанием текста, не допускайте повышение тошнотности более 8-9%. Также не стоит стремиться к нулю. Нормальный уровень тошнотности - 4-6%. Практически вся классическая литература имеет такой уровень тошнотности.</p></td></tr>';
	}
	if($data['mainPagePageSize'] != null){	
		($data['mainPagePageSize']['pageSize']<300)?$true++:$false++;
		$html .=
			'<tr><td>'.(($data['mainPagePageSize']['pageSize']<300)?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Размер HTML страницы</td><td>'.$data['mainPagePageSize']['pageSize'].' КБ</td></tr>'.
			'<tr class="info"><td colspan="2"><p>Размер страницы не должен превышать 300 КБ, его необходимо уменьшать в разумных пределах, удаляя не информативный контент. Оптимальным считается размер документа до 100 Кб.</p></td></tr>';
	}
	if($data['loadTime'] != null){	
		($data['loadTime']['loadTime']<3) ?$true++:$false++;
		$html .=
			'<tr><td>'.(($data['loadTime']['loadTime']<3) ?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Скорость загрузки HTML</td><td>'.round($data['loadTime']['loadTime'],2).', быстрее чем '.$data['loadTime']['percent'].'% проверенных сайтов  </td></tr>'.
			'<tr class="info"><td colspan="2"><p>Скорость загрузки напрямую влияет на пользовательские факторы. Снижение времени загрузки напрямую снижает показатели отказов. Уменьшение времени загрузки на одну секунду увеличивает конверсию на два процента (но функция не линейная). А увеличение времени загрузки до 7 секунд увеличивает показатель отказов на 30%. Всё, что загружается 7 секунд и больше, вызывает рост показателя отказов.</p>'.
			'<p>Робот Яндекса реже посещает медленные сайты. Это влияет на эффективность продвижения, такой сайт редко индексируется. Также было установлено, что прямое влияние на ранжирование запросов оказывает время ответа сервера.</p></td></tr>';
	}
	if($data['mainPageInternalLinks'] != null){	
		($data['mainPageInternalLinks']['internalCount']<100)?$true++:$false++;
		$html .=
			'<tr><td>'.(($data['mainPageInternalLinks']['internalCount']<100) ?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Внутренние ссылки</td><td>'.$data['mainPageInternalLinks']['internalCount'].', из них индексируются '.$data['mainPageInternalLinks']['internalIndexCount'].'</td></tr>'.
			'<tr class="info"><td colspan="2"><p>С помощью внутренних ссылок можно влиять на перераспределение веса между отдельными страницами ресурса, ссылаясь на более значимые разделы или статьи. Это перераспределение веса называется перелинковкой и широко используется опытными оптимизаторами как часть внутренней оптимизации сайта.</p>'.
			'<p>Внутренние ссылки также помогают пользователям сайта упрощать навигацию, позволяют иметь доступ к любой части ресурса и ускоряют попадание посетителя в тот или иной раздел. Правильная перелинковка - это не только любовь от поисковых систем, но также и от пользователей, поскольку является одной из составляющих юзабилити.</p></td></tr>';
	}
	if($data['mainPageExternalLinks'] != null){	
		($data['mainPageExternalLinks']['externalCount']<100)?$true++:$false++;
		$html .=
			'<tr><td>'.(($data['mainPageExternalLinks']['externalCount']<100) ?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Внешние ссылки</td><td>'.$data['mainPageExternalLinks']['externalCount'].', из них индексируются '.$data['mainPageExternalLinks']['externalIndexCount'].'</td></tr>'.
			'<tr class="info"><td colspan="2"><p>Внешняя ссылка означает то, что вы ссылаетесь на внешний ресурс. Старайтесь не ссылаться на плохие ресурсы, то есть те, которые имеют недостоверную информацию и могут навредить пользователю. Много исходящих ссылок на вашем сайте – тоже не очень хорошо. Ссылайтесь только на авторитетные ресурсы.</p>'.
			'<p>Не ставьте исходящие ссылки на главной странице. Продажа ссылок сильно портит продвижение.</p>'.
			'<p><a href="http://pr-cy.ru/lib/seo/Vneshnie-ssylki">Подробнее про внешние ссылки</a></p></td></tr>';
	}
	if($data['htmlValidator'] != null){	
		$html .=
			'<tr><td><img height="12px" src="'.__DIR__.'/img/info.png" style="margin-top:5px;">Ошибки HTML кода</td><td>Найдено ошибок: '.$data['htmlValidator']['errors'].'. Найдено предупреждений: '.$data['htmlValidator']['warnings'].'</td></tr>'.
			'<tr class="info"><td colspan="2"><p>W3C валидатор – это сервис, который позволяет проверить веб-страницы по нескольким стандартам одновременно, проверить, соответствует ли ваш сайт формату HTML (XHTML).</p>'.
			'<p>Проверка поможет избежать мелких ошибок (пропущенных скобок, кавычек, неправильно вложенных тэгов и т.д.); поддержка W3C валидатора современными браузерами, что сказывается на правильности отображения страниц в браузере; валидный код легче интерпретировать и обрабатывать; если код валиден, то это гарантирует совместимость и с существующими, и с будущими версиями браузеров.</p>'.
			'<p><a href="http://validator.w3.org/check?uri=http://'.$domain.'">Сервис W3C</a></p></td></tr>';
	}
	if($data['wot'] != null){	

		$html .=
			'<tr><td><img height="12px" src="'.__DIR__.'/img/info.png" style="margin-top:5px;">Уровень доверия</td><td>'.(($data['wot']['reputation']==false)?'Сайт еще не имеет оценок пользователей в сервисе Web of Trust (WOT)':($data['wot']['reputation'].' из 100 - репутация'.$data['wot']['childSafety'].'из 100 - безопасность для детей')).'</td></tr>'.
			'<tr class="info"><td colspan="2"><p>Уровень доверия в сервисе Web of Trust (WOT) показывает наглядную оценку сайта от пользователей данного ресурса, которые уже установили это расширение в свой браузер. Принцип работы: одни пользователи ставят сайту оценку, а другие, исходя из оценки, решают, заходить на этот сайт или нет. WOT полностью бесплатен.</p>'.
			'<p>После установки расширения, которое доступно для многих популярных браузеров, в поисковой выдаче возле сайта будет определённый индикатор. Зелёный кружочек означает, что сайт безопасен. Жёлтый – что нужно быть осторожным, используя данный ресурс. Красный кружочек говорит, что сайт имеет вредоносный контент и заходить на него опасно.</p></td></tr>';
	}
	if($data['microdataSchemaOrg'] != null){
		$data['microdataSchemaOrg']['microdataSchemaOrgExists']?$true++:$false++;	
		$html .=
			'<tr><td>'.($data['microdataSchemaOrg']['microdataSchemaOrgExists']?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Микроразметка Schema.org</td><td>'.($data['microdataSchemaOrg']['microdataSchemaOrgExists']?'найдена':'не найдена').'</td></tr>'.
			'<tr class="info"><td colspan="2"><p>Schema.org является единым общепризнанным стандартом, который распознают наиболее популярные поисковые системы, такие как Google, Яндекс, Yahoo и Bing.</p>'.
			'<p>Микроразметка — это семантическая разметка страниц сайта с целью структурирования данных, основанная на внедрении специальных атрибутов в HTML код документа.</p>'.
			'<p>Плюсы микроразметки:</p>'.
			'<ul><li>Логическая структура информации на странице помогает поисковым системам извлекать и обрабатывать данные.</li>'.
			'<li>Формирование расширенных сниппетов на странице с результатами поискового запроса улучшает кликабельность.</li></ul>'.	
			'<p>Разметка происходит непосредственно в HTML-коде страниц с помощью специальных атрибутов и не требует создания отдельных экспортных файлов.</p>'.
			'<p><a href="http://pr-cy.ru/news/p/5333">Как внедрить микроразметку информации на сайте, и что это дает</a></p>'.
			'<p><a href="https://yandex.ru/support/webmaster/schema-org/what-is-schema-org.xml">Schema.org в Яндекс<a></p></td></tr>';
	}
	if($data['microdataOpenGraph'] != null){

		$html .=
			'<tr><td><img height="12px" src="'.__DIR__.'/img/info.png" style="margin-top:5px;">Микроразметка Open Graph</td><td>'.($data['microdataOpenGraph']['ogFound']?'найдена':'не найдена').'</td></tr>'.
			'<tr class="info"><td colspan="2"><p>Стандарт Open Graph разработан социальной сетью Facebook. Его чаще всего используют для того, чтобы публикуемые ссылки с сайтов были расширенными, красивыми и понятными.</p>'.
			'<p>Поисковые системы могут использовать разметку для формирования заголовка сниппета. Яндекс также использует разметку для передачи данных в сервис Яндекс.Видео.</p>'.
			'<p>Чтобы передать информацию сервисам, необходимо в HTML-код (в элемент head) добавить следующие обязательные метатеги:</p>'.
			'<ul><li>og:type — тип объекта, например, video.movie (фильм) или website. Если у вас несколько объектов на странице, выберите один из них (главный). В зависимости от типа можно указать дополнительные свойства.</li>'.
			'<li>og:image — в этом теге прописывается адрес картинки, которую мы хотим видеть в анонсе. Указать можно любую картинку.</li>'.
			'<li>og:title — здесь указывается заголовок вашей страницы или анонса.</li>'.
			'<li>og:description — в этом теге и прописывается, текст анонса или описание сайта. Facebook считывает около 300 символов, поэтому больше писать не имеет смысла.</li></ul>'.
			'<p><a href="http://pr-cy.ru/news/p/5407">Open Graph: что это и почему каждый должен настроить</a></p>'.
			'<p><a href="http://almost.ru/open-graph/">Проверить Open Graph любой страницы</a></p>'.
			'<p><a href="http://ruogp.me/">Open Graph на русском</a></p>'.
			'<p><a href="https://yandex.ru/support/webmaster/open-graph/intro-open-graph.xml">Яндекс и Open Graph</a></p></td></tr>';
	}
	$html .= '</table>';
}
if($data['ip'] != null || $data['ipCountry'] != null || $data['ipIsp'] != null || $data['whoisCreationDate'] != null || $data['whoisExpirationDate'] != null || $data['ssl'] != null || $data['wwwRedirect'] != null || $data['mainPageEncoding'] != null || $data['mainPageTechs'] != null || $data['robotsTxt'] != null || $data['sitemap'] != null){
$html .= '<h3>Серверная информация</h3><hr><table>';/*серверная инфа*/
	if($data['ip'] != null){	
		$html .= '<tr><td><img height="12px" src="'.__DIR__.'/img/info.png" style="margin-top:5px;">IP</td><td>'.$data['ip']['ip'].'<p><br/></p></td></tr>';
	}
	if($data['ipCountry'] != null){	
		$html .= 
			'<tr><td><img height="12px" src="'.__DIR__.'/img/info.png" style="margin-top:5px;">Местоположение сервера</td><td>'.$data['ipCountry']['ipCountryCode'].'</td></tr>'.
			'<tr class="info"><td colspan="2"><p>Поисковые системы учитывают в какой стране находится сервер. В идеальном случае сервер должен быть расположен в той стране, где находится ваша целевая аудитория.</p></td></tr>';
	}
	if($data['ipIsp'] != null){	
		$html .= '<tr><td><img height="12px" src="'.__DIR__.'/img/info.png" style="margin-top:5px;">Датацентр</td><td>'.$data['ipIsp']['ipIspName'].'<p><br/></p></td></tr>';
	}
	if($data['whoisCreationDate'] != null){	
		$now = new DateTime();
		$past = new DateTime($data['whoisCreationDate']['whoisCreationDate']);
		$old = $now->diff($past); 
		($old->{'y'}<1)?$true++:$false++;
		$html .=
			'<tr><td>'.( ($old->{'y'}<1)?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Возраст домена</td><td>'.$old->{'y'}.' лет и '.$old->{'m'}.' месяцев</td></tr>'.
			'<tr class="info"><td colspan="2"><p>Молодые и новые домены плохо продвигаются в высококонкурентных тематиках. Также важна история домена и сайта. Старые домены с плохой историей сложно продвинуть. Поисковые системы любят старые, тематические домены с хорошей историй (без фильтров, спама, черного сео и т.п.).</p></td></tr>';
	}
	if($data['whoisExpirationDate'] != null){	
		$future = new DateTime($data['whoisExpirationDate']['whoisExpirationDate']);
		$long =  $now->diff($future);
		($long->{'m'}<1)?$true++:$false++;
		$html .=
			'<tr><td>'.(($long->{'m'}<1) ?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Окончание домена</td><td> Осталось '.$long->{'y'}.' лет '.$long->{'m'}.' месяцев '.$long->{'d'}.' дней</br></td></tr>'.
			'<tr class="info"><td colspan="2"><p>Не забывайте продлевать доменное имя. Лучше включить автоматическое продление у своего регистратора. После окончания регистрации домена есть шанс потерять доступ к домену.></p></td></tr>';
	}
	if($data['ssl'] != null){	
		$html .= 
			'<tr><td><img height="12px" src="'.__DIR__.'/img/info.png" style="margin-top:5px;">SSL-сертификат</td><td>'.($data['ssl']['sslAccess']?'Сайт доступен по HTTPS.':'Сайт не доступен по HTTPS.').'</br></td></tr>'.	
			'<tr class="info"><td colspan="2"><p>Для продвижения сайтов коммерческой направленности важна конфиденциальность обмена информацией между сервером и посетителями. Это повышает лояльность потенциальных клиентов к ресурсу и увеличивает его уровень доверия. Это влияет на конверсию ресурса и на рост позиций в выдаче практически по всем запросам.</p>'.
			'<p><a href="http://pr-cy.ru/news/p/4554">Заявление Google</a></p></td></tr>';
	}
	if($data['wwwRedirect'] != null){	
		$data['wwwRedirect']['wwwRedirect']?$true++:$false++;
		$html .= 
			'<tr><td>'.($data['wwwRedirect']['wwwRedirect'] ?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Редирект c WWW</td><td>'.($data['wwwRedirect']['wwwRedirect']?'Перенаправление настроено.':'Перенаправление не настроено.').'</br></td></tr>'.	
			'<tr class="info"><td colspan="2"><p>Если сайты www.indoortv116.ru и indoortv116.ru работают по отдельности без редиректов. Эти две копии могут «склеится» поисковыми системами, что негативно скажется на поисковой оптимизации.</p></td></tr>';
	}
	if($data['mainPageEncoding'] != null){	
		($data['mainPageEncoding']['encoding']=='utf-8')?$true++:$false++;
		$html .=
			'<tr><td>'.(($data['mainPageEncoding']['encoding']=='utf-8') ?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Кодировка</td><td>'.(($data['mainPageEncoding']['encoding'] == false) ?'кодировка не указана':$data['mainPageEncoding']['encoding']).'<p><br/></p></td></tr>';
	}
	if($data['mainPageTechs'] != null){	
		$html.=
			'<tr><td><img height="12px" src="'.__DIR__.'/img/info.png" style="margin-top:5px;">Технологии</td><td><table>';
		if ($data['mainPageTechs']['browserTechs'] != null){
			foreach($data['mainPageTechs']['browserTechs'] as $tech){
				foreach ($tech as $key => $val) {
					$html .= '<tr><td>'.$val.'</td></tr>';
				}
			}
		}
		else{
			$html .= '<tr><td>Технологии не указаны</td></tr>';
		}
		$html .= '</table><p><br/></p></td></tr>';
	}
	if($data['robotsTxt'] != null){	
		$data['robotsTxt']['robotsFileExists']?$true++:$false++;
		$html .= 
			'<tr><td>'.($data['robotsTxt']['robotsFileExists'] ?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Файл robots.txt</td><td>'.($data['robotsTxt']['robotsFileExists']?'Файл robots.txt найден.':'Файл robots.txt не найден.').'</td></tr>'.
			'<tr class="info"><td colspan="2"><p>Файл robots.txt – это список ограничений для поисковых роботов (ботов), которые посещают сайт и сканируют информацию на нем. Перед тем как сканировать и индексировать ваш сайт, все роботы обращаются к файлу robots.txt и ищут правила.</p>'.
			'<p>Файл robots.txt – это простой текстовый файл, которые находится в корневом каталоге Вашего сайта. И он должен быть доступен по URL: indoortv116.ru/robots.txt</p>'.
			'<p>Есть несколько причин использовать файл robots.txt на сайте:</p>'.
			'<ul><li>убрать дублированный контент;</li>'.
			'<li>скрыть нежелательную информацию;</li>'.
			'<li><a>ограничить скорость индексации.</li></ul>'.
			'<p><a href="http://pr-cy.ru/news/p/5414">Все о robots.txt для новичков</a></p>'.
			'<p><a href="https://yandex.ru/support/webmaster/controlling-robot/robots-txt.xml">Подробнее о robots.txt</a></p></td></tr>';
	}
	if($data['sitemap'] != null){	
		$html .= 
			'<tr><td><img height="12px" src="'.__DIR__.'/img/info.png" style="margin-top:5px;">Наличие Sitemap</td><td>'.($data['sitemap']['sitemapUrl']?'Карта сайта найдена.':'Карта сайта не найдена.').'</td></tr>'.
			'<tr class="info"><td colspan="2"><p>Файл Sitemap — это файл с информацией о страницах сайта, подлежащих индексированию. Разместив этот файл на сайте, вы можете:</p>'.
			'<ul><li>сообщить поисковикам, какие страницы вашего сайта нужно индексировать;</li>'.
			'<li>как часто обновляется информация на страницах;</li>'.
			'<li>индексирование каких страниц наиболее важно.</li></ul>'.
			'<p><a href="http://pr-cy.ru/news/p/5433">Карта сайта (файл Sitemap) от А до Я</a></p>'.
			'<p><a href="http://www.sitemaps.org/ru/protocol.html">Официальная документация</a></p></td></tr>';
	}
	$html .= '</table>';
}
$next = true;

}
/*отптимизация*/

/*Юзабилити*/
if($data['favicon'] != null || $data['page404StatusCode'] != null || $data['page404BackLink'] != null || $data['pageSpeedLeverageBrowserCaching'] != null || $data['mainPageTitle'] != null || $data['pageSpeedEnableGzipCompression'] != null || $data['pageSpeedOptimizeImages'] != null || $data['pageSpeedMinifyResources'] != null || $data['pageSpeedPrioritizeVisibleContent'] != null || $data['pageSpeedMinimizeRenderBlockingResources'] != null || $data['pageSpeedAvoidLandingPageRedirects'] != null){
if($next){$html .= '<div style="page-break-before: always;"></div>'; $next = false;}
$html .= '<h2>Юзабилити</h2>';
if($data['favicon'] != null || $data['page404StatusCode'] != null || $data['page404BackLink'] != null){
$html .= '<h3>Основное</h3><hr><table>';
	if($data['favicon'] != null){	
		$data['favicon']['faviconSrc']?$true++:$false++;
		$html .=
			'<tr><td>'.($data['favicon']['faviconSrc'] ?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Favicon</td><td>'.($data['favicon']['faviconSrc']?'у сайта есть Favicon':' у сайта нет Favicon').'</td></tr>'.
			'<tr class="info"><td colspan="2"><p>Чтобы выделить свой сайт, используйте Favicon – картинку специального формата, которая отображается рядом с адресом вашего сайта в поисковой системе и в адресной строке.</p>'.
			'<p>Чтобы браузеры показывали иконку вашего сайта, положите её в корневую папку вашего сайта. Вы можете назначить отдельным страницам разные иконки.</p>'.
			'<p>Сервисы для создания фавикон:</p>'.
			'<p><a href="http://pr-cy.ru/favicon/">Создание Favicon</a></p>'.
			'<p><a href="http://almost.ru/favicon/">Создание Retina Favicon</a></p></td></tr>';
	}
	if($data['page404StatusCode'] != null){	
		$data['page404StatusCode']['statusCode']?$true++:$false++;
		$html .= 
			'<tr><td>'.($data['page404StatusCode']['statusCode']?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Код ответа страницы 404</td><td>'.($data['page404StatusCode']['statusCode']?'Ссылка со страницы 404 найдена.':' Ссылка со не страницы 404 найдена.').'</td></tr>'.
			'<tr class="info"><td colspan="2"><p>При запросе страницы, которая не существует, сервер должен возвращать ошибку 404, то есть «страница не найдена». Данный код ответа говорит серверам и браузерам, что такая страница не существует.</p>'.
			'<p>Если сервер настроен неправильно, и будет возвращаться ошибка 200 — страница существует. В связи с этим поисковые системы могут проиндексировать все страницы вашего сайта с ошибками.</p>'.
			'<p>Настройте свой сайт так, чтобы при запросе несуществующих страниц появлялся код ответа 404, то есть страница не найдена, или код ответа 410, то есть страница удалена.</p></td></tr>';
	}
	if($data['page404BackLink'] != null){	
		$data['page404BackLink']['backLink']?$true++:$false++;
		$html .=
			'<tr><td>'.($data['page404BackLink']['backLink']?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Ссылка со страницы 404</td><td>'.($data['page404BackLink']['backLink']?'Ссылка со страницы 404 найдена.':' Ссылка со страницы 404 не найдена.').'</td></tr>'.
			'<tr class="info"><td colspan="2"><p>При запросе страницы, которая не существует, сервер отображает стандартную страницу с ошибкой 404. Для удобства пользователей рекомендуется сделать уникальную 404 страницу. А также добавить на эту страницу обратную ссылку на сайт.</p></td></tr>';
	}
	$html .= '</table>';
}
if($data['pageSpeedLeverageBrowserCaching'] != null || $data['mainPageTitle'] != null || $data['pageSpeedEnableGzipCompression'] != null || $data['pageSpeedOptimizeImages'] != null || $data['pageSpeedMinifyResources'] != null || $data['pageSpeedPrioritizeVisibleContent'] != null || $data['pageSpeedMinimizeRenderBlockingResources'] != null || $data['pageSpeedAvoidLandingPageRedirects'] != null){
	$html .= '<h3>Скорость загрузки</h3><hr><table>';
	if($data['pageSpeedLeverageBrowserCaching'] != null){	
		$data['pageSpeedLeverageBrowserCaching']['pageSpeedLeverageBrowserCaching']?$true++:$false++;
		$html .=
			'<tr><td>'.($data['pageSpeedLeverageBrowserCaching']['pageSpeedLeverageBrowserCaching']?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Кеш браузера</td><td>'.($data['pageSpeedLeverageBrowserCaching']['pageSpeedLeverageBrowserCaching']?'Кеширование настроено правильно.':'Кеширование настроено неправильно.').'</td></tr>'.
			'<tr class="info"><td colspan="2"><p>Благодаря кешированию пользователи, повторно посещающие ваш сайт, тратят меньше времени на загрузку страниц. Заголовки кеширования должны применяться ко всем кешируемым статическим ресурсам.</p>'.
			'<p>Включите для своего сервера кеширование в браузере. Продолжительность хранения статических ресурсов в кеше должна составлять не менее недели. Внешние ресурсы (объявления, виджеты и др.) должны храниться не менее 1 дня.</p></td></tr>';
	}
	if($data['mainPageTitle'] != null){	
		$data['pageSpeedServerResponseTime']['pageSpeedServerResponseTime']?$true++:$false++;
		$html .= 
			'<tr><td>'.($data['pageSpeedServerResponseTime']['pageSpeedServerResponseTime']?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Время ответа сервера</td><td>'.($data['pageSpeedServerResponseTime']['pageSpeedServerResponseTime']?'Ваш сервер ответил быстро.':'Ваш сервер ответил медленно.').'</td></tr>'.
			'<tr class="info"><td colspan="2"><p>Время ответа сервера определяет, сколько занимает загрузка кода HTML для отображения страницы.</p>'.
			'<p>Уменьшите время ответа сервера, чтобы оно составляло не более 200 мс. Большое время ответа может быть связано с десятками факторов: логика приложения, медленная работа с базой данных, маршрутизация, программная платформа, библиотеки, нехватка процессорной мощности или памяти. Все эти обстоятельства следует учитывать при оптимизации.</p></td></tr>';
	}
	if($data['pageSpeedEnableGzipCompression'] != null){	
		$data['pageSpeedEnableGzipCompression']['pageSpeedEnableGzipCompression']?$true++:$false++;
		$html .= 
			'<tr><td>'.($data['pageSpeedEnableGzipCompression']['pageSpeedEnableGzipCompression']?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Cжатие gzip</td><td>'.($data['pageSpeedEnableGzipCompression']['pageSpeedEnableGzipCompression']?'Сжатие включено.':'Сжатие выключено.').'</td></tr>'.
			'<tr class="info"><td colspan="2"><p>Многие веб-серверы могут перед отправкой сжимать файлы в формат GZIP, используя собственные процедуры или сторонние модули. Это позволяет ускорить загрузку ресурсов, необходимых для отображения веб-сайта.</p>'.
			'<p>Сжатие ресурсов с помощью функций gzip или deflate позволяет сократить объем данных, передаваемых по сети.</p></td></tr>';
	}
	if($data['pageSpeedOptimizeImages'] != null){	
		($data['pageSpeedOptimizeImages']['pageSpeedOptimizeImages']==false)?$true++:$false++;
		$html .= 
			'<tr><td>'.(($data['pageSpeedOptimizeImages']['pageSpeedOptimizeImages']==false)?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Сжатие изображений</td><td>'.(($data['pageSpeedOptimizeImages']['pageSpeedOptimizeImages']==true)?'Изображения не оптимизированы.':'Изображения оптимизированы.').'</td></tr>'.
			'<tr class="info"><td colspan="2"><p>Постарайтесь свести размер изображений к минимуму: это ускорит загрузку ресурсов. Правильный формат и сжатие изображений позволяет сократить их объем. Благодаря этому пользователи смогут сэкономить время и деньги.</p>'.
			'<p>Следует проводить базовую и расширенную оптимизацию всех изображений. В рамках базовой оптимизации обрезаются ненужные поля, уменьшается глубина цвета (до минимально приемлемого значения), удаляются комментарии и изображение сохраняется в подходящем формате. Базовую оптимизацию можно выполнить с помощью любой программы для редактирования изображений.</p></td></tr>';
	}
	if($data['pageSpeedMinifyResources'] != null){	
		$page_res = $data['pageSpeedMinifyResources']['pageSpeedMinifyResources'];
		($page_res['css'] && $page_res['html'] && $page_res['js'])?$true++:$false++;
		$html .= 
			'<tr><td>'.(($page_res['css'] && $page_res['html'] && $page_res['js'])?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Сократите ресурсы</td><td>';
				$html .= ($page_res['css'] && $page_res['html'] && $page_res['js'])?'Статические ресурсы (html, js, css) сокращены':'';
				$html .= $page_res['css']?'':'css ресурсы не сокращены';
				$html .= $page_res['html']?'':'html ресурсы не сокращены';
				$html .= $page_res['js']?'':'js ресурсы не сокращены';
				$html .= '</td></tr>'.
			'<tr class="info"><td colspan="2"><p>Размер ресурса можно уменьшить, удалив ненужные байты, например лишние пробелы, переносы строки и отступы. Сократив код HTML, CSS и JavaScript, вы ускорите загрузку, синтаксический анализ и отображение страницы.</p></td></tr>';
	}
	if($data['pageSpeedPrioritizeVisibleContent'] != null){	
		$data['pageSpeedPrioritizeVisibleContent']['pageSpeedPrioritizeVisibleContent']?$true++:$false++;
		$html .= 
			'<tr><td>'.($data['pageSpeedPrioritizeVisibleContent']['pageSpeedPrioritizeVisibleContent']?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Видимое содержание</td><td>'.(($data['pageSpeedPrioritizeVisibleContent']['pageSpeedPrioritizeVisibleContent'])?'Содержание верхней части страницы оптимизировано.':'Содержание верхней части страницы не оптимизировано.').'</td></tr>'.
			'<tr class="info"><td colspan="2"><p>Если количество необходимых данных вверху страницы слишком велико, браузер пользователя будет отправлять дополнительные запросы на сервер. В среде с большой задержкой (например, в мобильных сетях) это может существенно замедлить загрузку страницы.</p>'.
			'<p>Чтобы страница загружалась быстрее, ограничьте объем данных, которые должны отображаться в ее верхней части (код HTML, изображения, CSS, JavaScript).</p></td></tr>';
	}
	if($data['pageSpeedMinimizeRenderBlockingResources'] != null){	
		($data['pageSpeedMinimizeRenderBlockingResources']['pageSpeedMinimizeRenderBlockingResources']['num_css']>0 || $data['pageSpeedMinimizeRenderBlockingResources']['pageSpeedMinimizeRenderBlockingResources']['num_js']>0)?$false++:$true++;
		$html .= 
			'<tr><td>'.(($data['pageSpeedMinimizeRenderBlockingResources']['pageSpeedMinimizeRenderBlockingResources']['num_css']>0 || $data['pageSpeedMinimizeRenderBlockingResources']['pageSpeedMinimizeRenderBlockingResources']['num_js']>0) ?'<img class="false" src="'.__DIR__.'/img/false.png">':'<img src="'.__DIR__.'/img/true.png">').'JS, CSS в верхней части</td><td>В верхней части страницы найден блокирующий CSS: '.(($data['pageSpeedMinimizeRenderBlockingResources']['pageSpeedMinimizeRenderBlockingResources']['num_css'] == null )?'0':$data['pageSpeedMinimizeRenderBlockingResources']['pageSpeedMinimizeRenderBlockingResources']['num_css']).'<br/>В верхней части страницы найден блокирующий JS: '.(($data['pageSpeedMinimizeRenderBlockingResources']['pageSpeedMinimizeRenderBlockingResources']['num_js'] == null)?'0':$data['pageSpeedMinimizeRenderBlockingResources']['pageSpeedMinimizeRenderBlockingResources']['num_js']).'</td></tr>'.
			'<tr class="info"><td colspan="2"><p>Перед отображением страницы браузер должен выполнить ее синтаксический анализ. Если при этом он обнаруживает внешний скрипт, он должен его загрузить. Это лишний цикл операций, который замедляет показ страницы. Также браузеры запрашивают внешние файлы CSS перед отображением контента на экране. Это приводит к задержке и замедляет обработку страницы.</p>'.
			'<p>Код JavaScript, необходимый для отображения верхней части страницы, должен быть встроенным, а код, отвечающий за дополнительные функции, должен выполняться после загрузки верхних элементов.</p>
			<p>Если внешние ресурсы CSS имеют малый объем, их можно вставить непосредственно в документ HTML. Подобное встраивание позволяет браузеру продолжать загрузку страницы. Если файл CSS слишком велик, вам необходимо найти код CSS, отвечающий за контент в верхней части страницы и встроить его в HTML, отложив загрузку остальных стилей.</p></td></tr>';
	}
	if($data['pageSpeedAvoidLandingPageRedirects'] != null){
		$data['yandexCitation']['yandexCitation']?$true++:$false++;	
		$html .= 
			'<tr><td>'.($data['pageSpeedAvoidLandingPageRedirects']['pageSpeedAvoidLandingPageRedirects']?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Переадресация</td><td>'.($data['pageSpeedAvoidLandingPageRedirects']['pageSpeedAvoidLandingPageRedirects']?'Отсутствуют':'Присутствуют').'</td></tr>'.
			'<tr class="info"><td colspan="2"><p>При переадресации выполняется запрос к серверу, что приводит к дополнительной задержке, поэтому количество переадресаций следует свести к минимуму. Чем меньше переадресаций, тем быстрее загружается страница. Рекомендуем вам внимательно изучить дизайн сайта и выполнить его оптимизацию для ускорения загрузки.</p></td></tr>';
	}
	$html .= '</table>';
}
$next = true;
}
/*Юзабилити*/

/*мобильность*/
if($data['pageSpeedSizeContentToViewport'] !=null || $data['pageSpeedAvoidPlugins'] !=null || $data['screenshotSmartphone'] !=null || $data['pageSpeedSizeTapTargetsAppropriately'] !=null || $data['pageSpeedUseLegibleFontSizes'] !=null || $data['pageSpeedAvoidPlugins'] !=null || $data['pageSpeedAvoidInterstitials'] !=null){
if($next){$html .= '<div style="page-break-before: always;"></div>'; $next = false;}
	$html .= '<h2>Мобильность</h2><h3>Адаптивность для мобильных устройств</h3><hr><table>';
	if($data['pageSpeedSizeContentToViewport'] !=null){
		$data['pageSpeedSizeContentToViewport']['pageSpeedSizeContentToViewport']?$true++:$false++;
		$html .=
		'<tr><td>'.($data['pageSpeedSizeContentToViewport']['pageSpeedSizeContentToViewport']?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Длина текста</td><td>'.($data['pageSpeedSizeContentToViewport']['pageSpeedSizeContentToViewport']?'Ваша страница целиком находится в области экрана.':'Ваша страница выходит за пределы области экрана.').'</td></tr>'.
		'<tr class="info"><td colspan="2"><p>Пользователи ПК и мобильных устройств привыкли выполнять вертикальную, а не горизонтальную прокрутку веб-сайтов. Если для просмотра всего содержания необходимо прокрутить сайт по горизонтали или уменьшить масштаб, это вызывает неудобства.</p>'.
		'<p>При разработке сайта для мобильных устройств с мета-тегом viewport вы можете случайно расположить содержание так, что оно не поместится в указанную область просмотра. Например, если изображение шире области просмотра, может возникнуть необходимость в горизонтальной прокрутке. Во избежание этого нужно изменить контент так, чтобы он помещался целиком.</p></td></tr>';
	}
	if($data['screenshotSmartphone'] !=null){
		get_img($data['screenshotSmartphone']['filePath'],'phone.png');
		$html .=
			'<tr><td><img height="12px" src="'.__DIR__.'/img/info.png" style="margin-top:5px;">Скриншот сайта на смартфоне</td><td><img src="'.__DIR__.'/img/phone.png" width="150px"></td></tr>'.
			'<tr class="info"><td colspan="2"><p>Дизайн сайта под мобильные телефоны решает три задачи: обеспечивает пользователям самый комфортный просмотр сайта с любого устройства, способствует формированию позитивного имиджа компании и положительно влияет на поисковое ранжирование сайта.</p>'.
			'<p>Проверьте, чтобы ваш сайт хорошо отображался на мобильных устройствах и был удобен в использовании.</p></td></tr>';
	}
	if($data['pageSpeedSizeTapTargetsAppropriately'] !=null){
		$data['pageSpeedSizeTapTargetsAppropriately']['pageSpeedSizeTapTargetsAppropriately']?$true++:$false++;
		$html .=
			'<tr><td>'.($data['pageSpeedSizeTapTargetsAppropriately']['pageSpeedSizeTapTargetsAppropriately']?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Размер элементов</td><td>'.($data['pageSpeedSizeTapTargetsAppropriately']['pageSpeedSizeTapTargetsAppropriately']?'Размер активных элементов (ссылки, кнопки) достаточен для взаимодействия с ними.':'Размер активных элементов (ссылки, кнопки) некорректен для взаимодействия с ними.').'</td></tr>'.
			'<tr class="info"><td colspan="2"><p>Попасть пальцем в маленькие или тесно сгруппированные ссылки (кнопки) гораздо сложнее, чем указателем мыши. Чтобы пользователь мог без затруднений выбрать нужный активный элемент, они должны быть достаточно крупными, а располагать их следует на удалении друг от друга. Это сведет к минимум количество ложных нажатий. Средняя ширина подушечки пальца взрослого человека составляет 10 миллиметров, поэтому в рекомендациях по интерфейсу приложений Android рекомендуется задавать размер активных элементов не менее 7 мм, или 48 пикселей CSS на сайте с правильно настроенной областью просмотра для мобильных устройств.</p></td></tr>';
	}
	if($data['pageSpeedUseLegibleFontSizes'] !=null){
		$data['pageSpeedUseLegibleFontSizes']['pageSpeedUseLegibleFontSizes']?$true++:$false++;
		$html .=
			'<tr><td>'.($data['pageSpeedUseLegibleFontSizes']['pageSpeedUseLegibleFontSizes'] ?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Размеры шрифтов</td><td>'.($data['pageSpeedUseLegibleFontSizes']['pageSpeedUseLegibleFontSizes']?'Размер шрифта и высота строк на вашем сайте позволяют удобно читать текст.':'Размер шрифта и высота строк на вашем сайте не позволяют удобно читать текст.').'</td></tr>'.
			'<tr class="info"><td colspan="2"><p>Одна из самых часто встречающихся проблем чтения сайтов на мобильных устройствах — это слишком маленький размер шрифта. Приходится постоянное масштабировать сайт, чтобы прочитать мелкий текст, а это очень раздражает пользователя. Даже если у сайта есть мобильная версия или адаптивный дизайн, проблема плохой читаемости из-за мелкого шрифта встречается нередко.</p>'.
			'<p>Используйте удобочитаемые размеры шрифтов, чтобы сделать свой сайт удобнее.</p></td></tr>';
	}
	if($data['pageSpeedAvoidPlugins'] !=null){
		$data['pageSpeedAvoidPlugins']['pageSpeedAvoidPlugins']?$true++:$false++;
		$html .=
			'<tr><td>'.($data['pageSpeedAvoidPlugins']['pageSpeedAvoidPlugins']?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Плагины</td><td>'.($data['pageSpeedAvoidPlugins']['pageSpeedAvoidPlugins']?'Плагины не найдены.':'Плагины найдены.').'</td></tr>'.
			'<tr class="info"><td colspan="2"><p>Плагины помогают браузеру обрабатывать особый контент, например Flash, Silverlight или Java. Большинство мобильных устройств не поддерживают плагины, что приводит к множеству ошибок и нарушений безопасности в браузерах, обеспечивающих такую поддержку. В связи с этим многие браузеры ограничивают работу плагинов.</p></td></tr>';
	}
	if($data['pageSpeedAvoidInterstitials'] !=null){
		$data['pageSpeedAvoidInterstitials']['pageSpeedAvoidInterstitials']?$true++:$false++;
		$html .=
			'<tr><td>'.($data['pageSpeedAvoidInterstitials']['pageSpeedAvoidInterstitials']?'<img src="'.__DIR__.'/img/true.png">':'<img class="false" src="'.__DIR__.'/img/false.png">').'Всплывающие окна</td><td>'.($data['pageSpeedAvoidInterstitials']['pageSpeedAvoidInterstitials']?'На вашей странице нет всплывающих окон.':'На вашей странице есть всплывающие окна.').'</td></tr>'.
			'<tr class="info"><td colspan="2"><p>На многих сайтах размещены всплывающие окна или оверлеи с рекламой приложений, формами подписки и т. д. При просмотре сайтов на мобильных устройствах такие объявления частично или полностью закрывают контент, а это мало кому понравится. В некоторых случаях всплывающие объявления созданы таким образом, чтобы пользователю было сложно закрыть их. Поскольку экраны мобильных устройств обычно невелики, всплывающие объявления могут доставлять существенное неудобство.</p></td></tr>';
	}
	$html .= '</table>';
}
	$html .= 
	'<script type="text/php">'.
        'if ( isset($pdf) ) {'.
            '$x  = 580;'.
            '$y = 762;'.
            '$text = "{PAGE_NUM}/{PAGE_COUNT}";'.
            '$size = 10;'.
            '$color = array(0,0,0);'.
            '$word_space = 0.0;'. 
            '$char_space = 0.0;'.  
            '$angle = 0.0;'. 
            '$pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);}'.
    '</script>';


$header =
'<html>'.
	'<head>'.
		'<meta http-equiv="content-type" content="text/html; charset=utf-8"/>'.
	'</head>'.
'<style>'.
	'@font-face {'.
	  'font-family: "Roboto"'";'.
	  'font-style: normal;'.
	  'font-weight: 400;'.
	  'src: local("'"Roboto"'"), local("'"Roboto-Regular"'"), url('.__DIR__.'/Roboto-Regular.ttf) format("'"ttf"'");'.
	  'unicode-range: U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;}'.
	'*{font-family: "Roboto", sans-serif;}'.
	'header h1{margin:0;}'.
	'header p{margin-top:10px;}'.
	'table{width:100%; border: 0; margin:0;padding:0; font-size:14px;}'.
	'h2{margin-left:-10px;}'.
	'td{text-align:left; vertical-align:top; width:50%;}'.
	'.tr_head th{text-align:right;}'.
	'.tr_head th:first-child{text-align:left;}'.
	'h3{font-weight:normal; margin-bottom: 10px; margin-top:0;}'.
	'hr{border-style:none; border-bottom: 1px solid #999;}'.
	'a{text-decoration:none;}'.
	'.up .down{width:30px;}'.
	'.up{transform: rotate(180deg)}'.
	'.false{width18px; margin-right:5px;}'.
	'.info p{margin:0; padding-bottom:5px; margin-left:15px; margin-right:15px; font-size:12px; background-color: #F1F9FF;}'.
	'.info ul{margin:0 15px; background-color: #F1F9FF; padding:0 20px; padding-bottom:5px; font-size:12px;}'.
	'td p:last-child{margin-bottom:20px !important;}'.
	'#footer{position:fixed;left:-45px;bottom:-160px;right:-45px;height:150px;border-top:1px solid #ccc;padding-top:10px;font-size:13px;}'.
	'#footer img{width:150px;margin-left:10px; margin-top:-5px;}'.
	'#footer .center-title{position:absolute; width:100%; text-align:center; top: -12px;}'.
	'.tr_head{border-bottom:1px solid #999;}'.
	'.n_page{position:absolute;text-align:right;right:25px;bottom:-2px;}'.
	'#footer .page:after{content:counter(page,upper-roman);}'.
'</style>'.
'<body>'.
	'<header><table><tr>';
if($data['screenshotDesktop'] != null){
	$img_name = get_img($data['screenshotDesktop']['filePath'],'main.png');
	$header .= '<td width="370px"><img src="'.__DIR__.'/img/main.png" width="350px"></td>';
}
$header.='<td width="300px"><h1>'.$domain.'<h1><p>Аудит создан '.date('d.m.y').'</p><p>Оценка сайта - '.round((100/($true+$false))*$true).'%</p><p>Успешных тестов: '.$true.'<br/>Ошибок: '.$false.'</p></td></tr></table></header>';
$header .='<div id="footer">'.
		'<img src="'.__DIR__.'/img/krzl_footer.png">'.
		'<p class="center-title">Монстр ваших продаж</p>'.
	'</div>';
$footer = '</body></html>';
$html = $header . $html . $footer;
/*мобильность*/
$html .= $footer;
$dompdf = new DOMPDF();
$dompdf->load_html($html);
$dompdf->render();
$dompdf->stream($domain.'.pdf');
  }
  }
?>