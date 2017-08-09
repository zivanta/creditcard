<?php
//CORE-FILE INCLUDED
	require("/home/crawling-user/www/creditcard_crawling/code/base_path/data_crawling_base_path.php");
	require_once "/home/crawling-user/www/ultimate-web-scraper/support/http.php";
	require_once "/home/crawling-user/www/ultimate-web-scraper/support/web_browser.php";
	require_once "/home/crawling-user/www/ultimate-web-scraper/support/simple_html_dom.php";
	require(SITE_ROOT5 . '/coretest.php');
//BANK-DETAILS
	$bank_name = "comerica";	
	$bank_url="https://www.comerica.com/personal-finance/banking/cards/comerica-credit-card.html";	
	
// Current date and Yesterday date
	if (!empty($_GET['date'])) {
	  echo  $get_cur_date = $_GET['date'];  
	} else {
	  echo   $get_cur_date = date('Y-m-d');
	}


	$dir_cache = SITE_ROOT2 . '/'. $bank_name;
	if (!is_dir($dir_cache)) {
		mkdir($dir_cache, 0777);
		chmod($dir_cache, 0777);
	}
	$dir_cache .= '/' . $get_cur_date;
	if (!is_dir($dir_cache)) {
		mkdir($dir_cache, 0777);
		chmod($dir_cache, 0777);
	}
	
	// Simple HTML DOM tends to leak RAM like
	// a sieve.  Declare what you will need here.
	// Objects are reusable.
	$html = new simple_html_dom();	
	$web = new WebBrowser();
	$result = $web->Process($bank_url);

	$all_cards_crawl = $dir_cache . '/all_comerica_cards_crawl.txt';
	file_put_contents($all_cards_crawl,$result);
	$all_cards_contents = file_get_contents($all_cards_crawl) or die("file not found"); 
	
	 preg_match_all('/<a.*?href="([^"]+)".*?>/',$result['body'],$matches); 

	pr($matches);
			$product_url_final=array();

			for($j=48; $j<55;$j++){		 
            $product_url_final[]=$matches[1][$j];
			}
			//pr($product_url_final);
		
	foreach ($product_url_final as $key =>$value) {		
							
					//FOR CARD DETAILS
					$credit_card_url = trim($value);   
					$credit_card_name=explode('=',$value);
					//pr($credit_card_name);
					
					$credit_card_contents = $web->Process($credit_card_url);				
					$credit_card_file = $dir_cache . '/card_details' . $credit_card_name[2]. '.txt';
					file_put_contents($credit_card_file,$credit_card_contents);

					//pr($credit_card_contents);
					
					preg_match_all('/<a\shref="([^"]+)"\starget="no">Terms and Conditions/i', $credit_card_contents['body'],$terms_url);
					pr($terms_url);
					echo "hello nirmalya1";
					
					echo $term_url_final="https://online1.elancard.com".$terms_url[1][0];
					
					$term_contents = $web->Process($term_url_final);				
					$term_file = $dir_cache . '/term_' . $credit_card_name[2]. '.txt';
					file_put_contents($term_file,$term_contents);
					
					//die();
					//   END  OF THE Suntrust CREDIT CARD LINK ..............................>>>>>>>>>>>>>>>>>>>										   			
		}
                      
?>
