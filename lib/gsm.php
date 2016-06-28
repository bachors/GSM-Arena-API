<?php

/*********************************************************************
PHP Class untuk grab data di website gsm arena
menggunakan cURL dan simple html dom.

* Coded by Ican Bachors 2016.
* http://ibacor.com/labs/gsm-arena-api
* Updates will be posted to this site.
*********************************************************************/

error_reporting(0);

class Gsm
{    
    
    function __construct()
    {
		// Include library simple html dom
        require("simple_html_dom.php");

		// Fix bug slug symbol
		$this->simbol = array("&", "+");
        $this->kata = array("_and_", "_plus_");
		
    }
	   
    ####################### NGE cURL ##########################
	private function mycurl($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_USERAGENT, "Googlebot/2.1 (http://www.googlebot.com/bot.html)");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
		
		// Gagal ngecURL
        if(!$site = curl_exec($ch)){
			return 'offline';
		}
		
		// Sukses ngecURL
		else{
			return $site;
		}
	}   
    ####################### END cURL ##########################

    function search($q = "")
    {
		
		// Initial ARRAY untuk output
		$result = array();
		
		// Run cURL
		$url  = 'http://www.gsmarena.com/results.php3?sQuickSearch=yes&sName='.urlencode($q);
		$ngecurl = $this->mycurl($url);
				
		// Jika situs yang di cURL lagi offline/maintenance maka akan menampilkan error message
		if($ngecurl == 'offline'){
			$result["status"] = "error";
			$result["data"] = array();
		}else{
					
			$html  = str_get_html($ngecurl);

			// Manipulasi DOM menggunakan library simple html dom. Find div dengan nama class st-text
			$div = $html->find('div[class=makers]', 0);
			if($div->find('li', 0)){
				$result["status"] = "sukses";
				// Membuat array. Find li from div
				foreach ($div->find('li') as $li) {
						$grid = $li->find('a', 0);
						$title = $grid->find('span', 0);
						$slug = str_replace(".php", "", $grid->href);
						$result["data"][] = array(
							"title" => str_replace('<br>', ' ', $title->innertext),
							"slug" => str_replace($this->simbol, $this->kata, $slug)
						);
				}
			}else{
				$result["status"] = "error";
				$result["data"] = array();
			}
		}

		return $result;		
    }

    function detail($slug = "")
    {
		
		// Initial ARRAY untuk output
		$result = array();
			
		// Run cURL
		$url  = 'http://www.gsmarena.com/'.str_replace($this->kata, $this->simbol, $slug).'.php';
		$ngecurl = $this->mycurl($url);
				
		// Jika situs yang di cURL lagi offline/maintenance maka akan menampilkan error message
		if($ngecurl == 'offline'){
			$result["status"] = "error";
			$result["data"] = array();
		}else{
					
			$html  = str_get_html($ngecurl);
			if($html->find('title', 0)->innertext == '404 Not Found'){
				$result["status"] = "error";
				$result["data"] = array();
			}else{
				$result["status"] = "sukses";
				$result["title"] = $html->find('h1[class=specs-phone-name-title]', 0)->innertext;
				
				$img_div = $html->find('div[class=specs-photo-main]', 0);
				$result["img"] = $img_div->find('img', 0)->src;

				// Manipulasi DOM menggunakan library simple html dom. Find div dengan nama class specs-list
				$div = $html->find('div[id=specs-list]', 0);
					
				foreach ($div->find('table') as $table) {
					$th = $table->find('th', 0);
					// Membuat array. Find tr from table
					foreach ($table->find('tr') as $tr) {
						($tr->find('td', 0) == "&nbsp;" ? $ttl = "empty" : $ttl = $tr->find('td', 0));
						$search  = array(".", ",", "&", "-", " ");
						$replace = array("", "", "", "_", "_");
						$ttl = strtolower(str_replace($search, $replace, $ttl));
						$nfo = $tr->find('td', 1);					
						$result["data"][strtolower($th->innertext)][] = array(						
							strip_tags($ttl) => strip_tags($nfo)
						);					
					}
				}
				$search  = array("},{", "[", "]", '","nbsp;":"', "nbsp;", " - ");
				$replace = array(",", "", "", "<br>", "", "<br>- ");
				$newjson = str_replace($search, $replace, json_encode($result));
				$result = json_decode($newjson);
			}
		}
		return $result;		
    }
    
}

?>