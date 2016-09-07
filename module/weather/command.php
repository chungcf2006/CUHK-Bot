<?php
	if (!timerlock("weather")){
		$hkoData = curl_init("http://www.hko.gov.hk/textonly/v2/warning/warnc.htm");
		curl_setopt($hkoData, CURLOPT_PROXY, PROXY);
		curl_setopt($hkoData, CURLOPT_RETURNTRANSFER, true);
		$hkoData = curl_exec($hkoData);
		$content = "*現時生效警告* (資料由天文台提供)\n";
		preg_match("'<!--生 效 警 告-->\n(.*?)\n<!--/生 效 警 告-->'si", $hkoData, $match);
		if($match){
			$all = $match[1];
			$content .= str_replace(array("<p>", "</p>"), "", $all);
		} else {
			$content .= "`現時並無警告生效。`";
		}
		sendMessage($content);
	}
