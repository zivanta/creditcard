<?php
//BANK-DETAILS
$bank_name = "comerica";
$bank_url = "https://www.comerica.com/personal-finance/banking/cards/comerica-credit-card.html";
$content_count = 0;
$account_type = "CREDITCARD";
$bank_id = 67;

//CORE-FILE INCLUDED
require("/home/crawling-user/www/creditcard_crawling/code/base_path/data_crawling_base_path.php");
require(SITE_ROOT5 . '/coretest.php');
require(SITE_ROOT6 . '/creditcard_crawling_db.php');

// Current date and Yesterday date
if (!empty($_GET['date'])) {
    $get_cur_date = $_GET['date'];
} else {
    $get_cur_date = date('Y-m-d');
}

$dir_serialize = SITE_ROOT3 . '/' . $bank_name;
if (!is_dir($dir_serialize)) {
    mkdir($dir_serialize, 0777);
    chmod($dir_serialize, 0777);
}
$dir_serialize .= '/' . $get_cur_date;
if (!is_dir($dir_serialize)) {
    mkdir($dir_serialize, 0777);
    chmod($dir_serialize, 0777);
}

$dbc = new DBConfigration;
$con = $dbc->db_connect();

$all_cards_link_crawl = SITE_ROOT2 . '/' . $bank_name . '/' . $get_cur_date . '/all_comerica_cards_crawl.txt';
$all_cards_link_contents = file_get_contents($all_cards_link_crawl) or die("file not found");

preg_match_all('/<a.*?href="([^"]+)".*?>/',$all_cards_link_contents,$matches); 
 
	
			$product_url_final=array();

			for ($j=48; $j<55;$j++) {		 
            $product_url_final[]=$matches[1][$j];
			}
        $cnt=0;
		foreach ($product_url_final as $key => $value) {

			//FOR CARD DETAILS
			$credit_card_url = trim($value);   
			$credit_card_name=explode('=',$value);
			
			$credit_card_file1 = SITE_ROOT2 . '/' . $bank_name . '/' . $get_cur_date . '/card_details' . $credit_card_name[2]. '.txt';
			$credit_card_contents1 = file_get_contents($credit_card_file1) or die("file not found");
			preg_match_all("/<button.*?href='([^']+)'.*?>/",$credit_card_contents1,$apply_now); 
			
			preg_match_all('/<a\shref="([^"]+)"\starget="no">Terms and Conditions/i', $credit_card_contents1,$terms_url);
			
			
			$credit_card_file = SITE_ROOT2 . '/' . $bank_name . '/' . $get_cur_date . '/term_' . $credit_card_name[2]. '.txt';
			$credit_card_contents = file_get_contents($credit_card_file) or die("file not found");
			
		
			$credit_card_name_final=get_tag_array($credit_card_contents,'<div class="title basicElement">','</div>');
			pr($credit_card_name_final);
			
			
			
			$card_name1 =trim(str_replace(array("®",'Terms','and','Conditions','&#174;'), '', $credit_card_name_final[0]));		
			$card_name = trim(preg_replace("/[^a-zA-Z0-9]+/", " ", strip_tags($card_name1)));
			$modified_card_name = ucwords(strtolower($card_name));			
			
			$url="https://online1.elancard.com".$terms_url[1][0];
			$card_details_url = trim($value); 
			$data_crawled_url = "https://online1.elancard.com".$terms_url[1][0];
			if($card_name=='Visa Secured Card'){
			$apply_now_url = $card_details_url;
			}else{
			$apply_now_url = "https://online1.elancard.com".$apply_now[1][1];
			}
			
			pr($card_name);
			
			$all_record=get_tag_array($credit_card_contents,'<table','</table>');	
			$all_record_1=get_tag_array($all_record[0],'<span','</span>');
			pr($all_record_1);
				
			//Annual Fee & Annual Fee waiver promotion period
			if($card_name=='Visa Secured Card'){
			$annual_fee = strip_tags($all_record_1[18]);
            $waiver_promotion_period_in_months = 'NA';
			}elseif($card_name=='College Rewards Visa Card'){
			$annual_fee = '$0';
            $waiver_promotion_period_in_months = 'NA';
			}elseif($card_name=='Visa Bonus Rewards PLUS Card' || $card_name=='Visa Bonus Rewards Card'){
			$annual_fee_1 = strip_tags($all_record_1[27]);
				if($annual_fee_1=='None'){
				$annual_fee= '$0';
				}else{
				$annual_fee=$annual_fee_1;
				}
			$waiver_promotion_period_in_months = 'NA';		
			}else{
			$annual_fee_1 = strip_tags($all_record_1[22]);
				if($annual_fee_1=='None'){
				$annual_fee= '$0';
				}else{
				$annual_fee=$annual_fee_1;
				}
            $waiver_promotion_period_in_months = 'NA';			
			}
			
			//Network
			if (strstr($card_name, 'Visa')) {
				$network='Visa';
			}else{
			$network='American Express';
			}
			
			//Billing Cycle					
            $billing_cycle_in_days = '30';
			
			//Purchase //Balance Transfer //Cash Advance //Transaction fees/usage fees
			if($card_name=='Visa Secured Card'){
			$purchase_regular_apr_max = strip_tags($all_record_1[3]);
            $purchase_regular_apr_min = strip_tags($all_record_1[3]);
			$purchase_promotion_apr ='NA';
            $purchase_promotion_apr_period_in_months ='NA';
			
			$balance_transfers_max = strip_tags($all_record_1[5]);
            $balance_transfers_min = strip_tags($all_record_1[5]);
			$balance_transfers_promo_apr = 'NA';
            $balance_transfers_promo_apr_period_in_months = 'NA';
			
			$bank_cash_adv_apr_max = strip_tags($all_record_1[7]);
            $bank_cash_adv_apr_min = strip_tags($all_record_1[7]);
						
			$transaction_fees_balance_transfers_percentage_min = strip_tags($all_record_1[22]);
            $transaction_fees_balance_transfers_percentage_max = strip_tags($all_record_1[22]);
            $transaction_fees_balance_transfers_dollars = strip_tags($all_record_1[23]);	
			$transaction_fees_cash_advances_percentage =strip_tags($all_record_1[34]);
            $transaction_fees_cash_advances_dollar = strip_tags($all_record_1[35]);
			
			$foreign_transaction_fees_dollar = 'NA';
            $foreign_transaction_fees_percentage = strip_tags($all_record_1[46]);
			
			$late_payment_penalty_fee_min = strip_tags($all_record_1[50]);
            $late_payment_penalty_fee_max = strip_tags($all_record_1[50]);
            $returned_check_penalty_fees =  strip_tags($all_record_1[53]);
			
			}elseif($card_name=='College Rewards Visa Card'){
			$purchase_regular_apr_max = strip_tags($all_record_1[4]);
            $purchase_regular_apr_min = strip_tags($all_record_1[3]);
			$purchase_promotion_apr ='NA';
            $purchase_promotion_apr_period_in_months ='NA';
			
			$balance_transfers_max = strip_tags($all_record_1[7]);
            $balance_transfers_min = strip_tags($all_record_1[6]);
			$balance_transfers_promo_apr = 'NA';
            $balance_transfers_promo_apr_period_in_months = 'NA';
			
			$bank_cash_adv_apr_max = strip_tags($all_record_1[9]);
            $bank_cash_adv_apr_min = strip_tags($all_record_1[9]);
			
			$transaction_fees_balance_transfers_percentage_min = strip_tags($all_record_1[24]);
            $transaction_fees_balance_transfers_percentage_max = strip_tags($all_record_1[24]);
            $transaction_fees_balance_transfers_dollars = strip_tags($all_record_1[25]);				
			$transaction_fees_cash_advances_percentage =strip_tags($all_record_1[36]);
            $transaction_fees_cash_advances_dollar = strip_tags($all_record_1[37]);
			
			$foreign_transaction_fees_dollar = 'NA';
            $foreign_transaction_fees_percentage = strip_tags($all_record_1[48]);
			
			$late_payment_penalty_fee_min = strip_tags($all_record_1[52]);
            $late_payment_penalty_fee_max = strip_tags($all_record_1[52]);
            $returned_check_penalty_fees =  strip_tags($all_record_1[55]);
			
			}elseif($card_name=='Visa Bonus Rewards PLUS Card'){
			$purchase_regular_apr_max = strip_tags($all_record_1[5]);
            $purchase_regular_apr_min = strip_tags($all_record_1[4]);
			$purchase_promotion_apr = strip_tags($all_record_1[3]);
            $purchase_promotion_apr_period_in_months ='6';	

			$balance_transfers_max = strip_tags($all_record_1[11]);
            $balance_transfers_min = strip_tags($all_record_1[10]);
			$balance_transfers_promo_apr = strip_tags($all_record_1[9]);
            $balance_transfers_promo_apr_period_in_months = '6';
			
			$bank_cash_adv_apr_max = strip_tags($all_record_1[16]);
            $bank_cash_adv_apr_min = strip_tags($all_record_1[15]);
			
			$transaction_fees_balance_transfers_percentage_min = strip_tags($all_record_1[31]);
            $transaction_fees_balance_transfers_percentage_max = strip_tags($all_record_1[31]);
            $transaction_fees_balance_transfers_dollars = strip_tags($all_record_1[32]);				
			$transaction_fees_cash_advances_percentage =strip_tags($all_record_1[43]);
            $transaction_fees_cash_advances_dollar = strip_tags($all_record_1[44]);
			
			$foreign_transaction_fees_dollar = 'NA';
            $foreign_transaction_fees_percentage = 'NA';
			
			$late_payment_penalty_fee_min = strip_tags($all_record_1[58]);
            $late_payment_penalty_fee_max = strip_tags($all_record_1[58]);
            $returned_check_penalty_fees =  strip_tags($all_record_1[61]);
			
			}elseif($card_name=='Visa Bonus Rewards Card'){			
			$purchase_regular_apr_max = strip_tags($all_record_1[5]);
            $purchase_regular_apr_min = strip_tags($all_record_1[4]);
			$purchase_promotion_apr = strip_tags($all_record_1[3]);
            $purchase_promotion_apr_period_in_months ='6';	

			$balance_transfers_max = strip_tags($all_record_1[11]);
            $balance_transfers_min = strip_tags($all_record_1[10]);
			$balance_transfers_promo_apr = strip_tags($all_record_1[9]);
            $balance_transfers_promo_apr_period_in_months = '6';
			
			$bank_cash_adv_apr_max = strip_tags($all_record_1[16]);
            $bank_cash_adv_apr_min = strip_tags($all_record_1[15]);
			
			$transaction_fees_balance_transfers_percentage_min = strip_tags($all_record_1[31]);
            $transaction_fees_balance_transfers_percentage_max = strip_tags($all_record_1[31]);
            $transaction_fees_balance_transfers_dollars = strip_tags($all_record_1[32]);				
			$transaction_fees_cash_advances_percentage =strip_tags($all_record_1[43]);
            $transaction_fees_cash_advances_dollar = strip_tags($all_record_1[44]);
			
			$foreign_transaction_fees_dollar = 'NA';
            $foreign_transaction_fees_percentage = strip_tags($all_record_1[55]);
			
			$late_payment_penalty_fee_min = strip_tags($all_record_1[59]);
            $late_payment_penalty_fee_max = strip_tags($all_record_1[59]);
            $returned_check_penalty_fees =  strip_tags($all_record_1[62]);
			
			}elseif($card_name=='Travel Rewards American Express Card'){
			$purchase_regular_apr_max = strip_tags($all_record_1[5]);
            $purchase_regular_apr_min = strip_tags($all_record_1[4]);
			$purchase_promotion_apr = strip_tags($all_record_1[3]);
            $purchase_promotion_apr_period_in_months ='6';	

			$balance_transfers_max = strip_tags($all_record_1[9]);
            $balance_transfers_min = strip_tags($all_record_1[8]);
			$balance_transfers_promo_apr = strip_tags($all_record_1[7]);
            $balance_transfers_promo_apr_period_in_months = '6';
			
			$bank_cash_adv_apr_max = strip_tags($all_record_1[11]);
            $bank_cash_adv_apr_min = strip_tags($all_record_1[11]);
			
			$transaction_fees_balance_transfers_percentage_min = strip_tags($all_record_1[26]);
            $transaction_fees_balance_transfers_percentage_max = strip_tags($all_record_1[26]);
            $transaction_fees_balance_transfers_dollars = strip_tags($all_record_1[27]);	
			$transaction_fees_cash_advances_percentage =strip_tags($all_record_1[38]);
            $transaction_fees_cash_advances_dollar = strip_tags($all_record_1[39]);
			
			$foreign_transaction_fees_dollar = 'NA';
            $foreign_transaction_fees_percentage = 'NA';
			
			$late_payment_penalty_fee_min = strip_tags($all_record_1[53]);
            $late_payment_penalty_fee_max = strip_tags($all_record_1[53]);
            $returned_check_penalty_fees =  strip_tags($all_record_1[56]);
			}elseif($card_name=='Visa Platinum Card'){			
			$purchase_regular_apr_max = strip_tags($all_record_1[5]);
            $purchase_regular_apr_min = strip_tags($all_record_1[4]);
			$purchase_promotion_apr = strip_tags($all_record_1[3]);
            $purchase_promotion_apr_period_in_months ='12';	

			$balance_transfers_max = strip_tags($all_record_1[9]);
            $balance_transfers_min = strip_tags($all_record_1[8]);
			$balance_transfers_promo_apr = strip_tags($all_record_1[7]);
            $balance_transfers_promo_apr_period_in_months = '12';
			
			$bank_cash_adv_apr_max = strip_tags($all_record_1[11]);
            $bank_cash_adv_apr_min = strip_tags($all_record_1[11]);
			
			$transaction_fees_balance_transfers_percentage_min = strip_tags($all_record_1[26]);
            $transaction_fees_balance_transfers_percentage_max = strip_tags($all_record_1[26]);
            $transaction_fees_balance_transfers_dollars = strip_tags($all_record_1[27]);	
			$transaction_fees_cash_advances_percentage =strip_tags($all_record_1[38]);
            $transaction_fees_cash_advances_dollar = strip_tags($all_record_1[39]);
			
			$foreign_transaction_fees_dollar = 'NA';
            $foreign_transaction_fees_percentage = strip_tags($all_record_1[50]);
			
			$late_payment_penalty_fee_min = strip_tags($all_record_1[54]);
            $late_payment_penalty_fee_max = strip_tags($all_record_1[54]);
            $returned_check_penalty_fees =  strip_tags($all_record_1[57]);
			}else{			
			$purchase_regular_apr_max = strip_tags($all_record_1[5]);
            $purchase_regular_apr_min = strip_tags($all_record_1[4]);
			$purchase_promotion_apr = strip_tags($all_record_1[3]);
            $purchase_promotion_apr_period_in_months ='6';	

			$balance_transfers_max = strip_tags($all_record_1[9]);
            $balance_transfers_min = strip_tags($all_record_1[8]);
			$balance_transfers_promo_apr = strip_tags($all_record_1[7]);
            $balance_transfers_promo_apr_period_in_months = '6';
			
			$bank_cash_adv_apr_max = strip_tags($all_record_1[11]);
            $bank_cash_adv_apr_min = strip_tags($all_record_1[11]);
			
			$transaction_fees_balance_transfers_percentage_min = strip_tags($all_record_1[26]);
            $transaction_fees_balance_transfers_percentage_max = strip_tags($all_record_1[26]);
            $transaction_fees_balance_transfers_dollars = strip_tags($all_record_1[27]);	
			$transaction_fees_cash_advances_percentage =strip_tags($all_record_1[38]);
            $transaction_fees_cash_advances_dollar = strip_tags($all_record_1[39]);
			
			$foreign_transaction_fees_dollar = 'NA';
            $foreign_transaction_fees_percentage = strip_tags($all_record_1[50]);
			
			$late_payment_penalty_fee_min = strip_tags($all_record_1[54]);
            $late_payment_penalty_fee_max = strip_tags($all_record_1[54]);
            $returned_check_penalty_fees =  strip_tags($all_record_1[57]);
			}
			
			//Direct deposit
            $direct_deposit_check_cash_adv_apr_max = 'NA';
            $direct_deposit_check_cash_adv_apr_min = 'NA';
			
			//Overdraft Advance Fees
			$overdraft_advance_apr_max ='NA';
            $overdraft_advance_apr_min ='NA';
			
			//direct deposit check cash advance fee
			$direct_deposit_check_cash_adv_fee_percentage = 'NA';
            $direct_deposit_check_cash_adv_fee_dollar = 'NA';
			
			//Penalty Apr
			$late_payment_penalty_apr = 'NA';	
			
			//echo "hello world";
		echo "hi shrabani das";
			
			
  	//For creating the serialize file
		$category_query = $con->query("select id,category_name,cat_status from creditcard_category where parent_id != 0 and cat_status='Y'");
		$category_query->setFetchMode(PDO::FETCH_BOTH);
		while ($row = $category_query->fetch()) {
                if ($$row['category_name'] == '') {
                    $value = 'NA';
                } else {
                    $value = $$row['category_name'];
                }
                $category_array[$row['id']] = trim($value);
            }


				$card_array=array();
                $final_array = array();
                $card_array[$modified_card_name] = array(
                    "card_name"=> $card_name,                    
                    "category" => $category_array
                );
				pr($card_array);
                $final_array[$bank_id] = $card_array;
                $card_name_array[] = str_replace(' ', '_', strtolower($card_name));
                
                $credit_card_serialize_file = $dir_serialize . '/serialize_' . str_replace(' ', '_', strtolower($card_name)) . '.txt';
                $credit_card_serialize_contents = serialize($card_array);
                if (!file_exists($credit_card_serialize_file)) {
                    file_put_contents($credit_card_serialize_file, $credit_card_serialize_contents);
                    chmod($credit_card_serialize_file, 0777);
                }
            }
        
        $card_name_serialize_file = $dir_serialize . '/serialize_card_name.txt';
        $card_name_serialize_contents = serialize($card_name_array);
        if (!file_exists($card_name_serialize_file)) {
            file_put_contents($card_name_serialize_file, $card_name_serialize_contents);
            chmod($card_name_serialize_file, 0777);
        }
// END Purchaces / RATES..............<<<<<<<<>>>>>>>>>>>>>      
?>
