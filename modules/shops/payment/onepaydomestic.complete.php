<?php

/**
 * @Project NUKEVIET 3.0
 * @Author VINADES., JSC (contact@vinades.vn)
 * @Copyright (C) 2010 VINADES ., JSC. All rights reserved
 * @Createdate Dec 29, 2010  10:42:00 PM 
 */

if( ! defined( 'NV_IS_MOD_SHOPS' ) ) die( 'Stop!!!' );

/**
 * null2unknown()
 * 
 * @param mixed $data
 * @return
 */
function null2unknown( $data )
{
	return $data == "" ? "No Value Returned" : $data;
}

$SECURE_SECRET = $payment_config['secure_secret'];
$vpc_Txn_Secure_Hash = $_GET["vpc_SecureHash"];
unset( $_GET["vpc_SecureHash"] );

if( strlen( $SECURE_SECRET ) > 0 and $_GET["vpc_TxnResponseCode"] != "7" and $_GET["vpc_TxnResponseCode"] != "No Value Returned" )
{
	$stringHashData = "";

	foreach( $_GET as $key => $value )
	{
		if( $key != "vpc_SecureHash" and ( strlen( $value ) > 0 ) and ( ( substr( $key, 0, 4 ) == "vpc_" ) || ( substr( $key, 0, 5 ) == "user_" ) ) )
		{
			$stringHashData .= $key . "=" . $value . "&";
		}
	}

	$stringHashData = rtrim( $stringHashData, "&" );

	if( strtoupper( $vpc_Txn_Secure_Hash ) == strtoupper( hash_hmac( 'SHA256', $stringHashData, pack( 'H*', $SECURE_SECRET ) ) ) )
	{
		$hashValidated = "CORRECT";
	}
	else
	{
		$hashValidated = "INVALID HASH";
	}
}
else
{
	$hashValidated = "INVALID HASH";
}

$amount = null2unknown( $_GET["vpc_Amount"] ); // So tien thanh toan
$orderInfo = null2unknown( $_GET["vpc_OrderInfo"] ); // Ma hoa don (ID dat hang)
$txnResponseCode = null2unknown( $_GET["vpc_TxnResponseCode"] ); // Ma tra ve
$vpc_MerchTxnRef = null2unknown( $_GET["vpc_MerchTxnRef"] ); // Ma giao dich do OnePage Sinh ra dung de QueryDR sau nay
$payment_id = ( int )$_GET['vpc_TransactionNo'];

if( $hashValidated == "CORRECT" and $txnResponseCode == "0" )
{
	// Giao dich thanh cong
	$nv_transaction_status = 4;
}
elseif( $hashValidated == "INVALID HASH" and $txnResponseCode == "0" )
{
	// Tam giu
	$nv_transaction_status = 2;
}
else
{
	// Giao dich that bai
	$nv_transaction_status = 3;
}

$error_text = "";

// Chi tiet
$transaction_i = array();
$transaction_i['nv_transaction_status'] = $nv_transaction_status;
$transaction_i['amount'] = round( ( int )$amount / 100 );
$transaction_i['created_time'] = NV_CURRENTTIME;
$transaction_i['vpc_MerchTxnRef'] = $vpc_MerchTxnRef;

$payment_amount = intval( $transaction_i['amount'] );
$payment_time = $transaction_i['created_time'];

list( $order_id ) = $db->sql_fetchrow( $db->sql_query( "SELECT `order_id` FROM `" . $db_config['prefix'] . "_" . $module_data . "_orders` WHERE `order_code`=" . $db->dbescape_string( $orderInfo ) ) );
if( $order_id > 0 )
{
	$error_update = false;
	$payment_data = nv_base64_encode( serialize( $transaction_i ) );
	list( $payment_data_old ) = $db->sql_fetchrow( $db->sql_query( "SELECT `payment_data` FROM `" . $db_config['prefix'] . "_" . $module_data . "_transaction` WHERE `payment`='" . $payment . "' AND `payment_id`=" . $db->dbescape_string( $payment_id ) . " ORDER BY `transaction_id` DESC LIMIT 1" ) );
	if( $payment_data != $payment_data_old )
	{
		$transaction_id = $db->sql_query_insert_id( "INSERT INTO `" . $db_config['prefix'] . "_" . $module_data . "_transaction` (`transaction_id`, `transaction_time`, `transaction_status`, `order_id`, `userid`, `payment`, `payment_id`, `payment_time`, `payment_amount`, `payment_data`) VALUES (NULL, UNIX_TIMESTAMP(), '" . $nv_transaction_status . "', '" . $order_id . "', '0', '" . $payment . "', '" . $payment_id . "', '" . $payment_time . "', '" . $payment_amount . "', '" . $payment_data . "')" );
		if( $transaction_id > 0 )
		{
			$db->sql_query( "UPDATE `" . $db_config['prefix'] . "_" . $module_data . "_orders` SET transaction_status=" . $nv_transaction_status . " , transaction_id = " . $transaction_id . " , transaction_count = transaction_count+1 WHERE `order_id`=" . $order_id );
		}
		else
		{
			$error_update = true;
		}
	}
	if( ! $error_update )
	{
		$nv_redirect = NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=history";
		$contents = redict_link( $lang_module['payment_complete'], $lang_module['back_history'], $nv_redirect );
	}
}

if( $error_text != "" )
{
	$contents = $error_text;
}
else
{
	$contents = $lang_module['payment_erorr'];
}

?>