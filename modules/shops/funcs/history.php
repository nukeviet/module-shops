<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2017 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 04/18/2017 09:47
 */

if (! defined('NV_IS_MOD_SHOPS')) {
    die('Stop!!!');
}

if (! defined('NV_IS_USER')) {
    $redirect = NV_STATIC_URL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=cart";
    nv_redirect_location(NV_STATIC_URL . "index.php?" . NV_NAME_VARIABLE . "=users&" . NV_OP_VARIABLE . "=login&nv_redirect=" . nv_redirect_encrypt($redirect));
}

$data_content = array();

$sql = "SELECT order_id, order_code, order_note, user_id, unit_total, order_total, order_time, transaction_status, transaction_id, transaction_count FROM " . $db_config['prefix'] . "_" . $module_data . "_orders WHERE user_id = " . $user_info["userid"] . " ORDER BY order_id DESC";
$result = $db->query($sql);

$link_module = NV_STATIC_URL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name;

while (list($order_id, $order_code, $order_note, $user_id, $unit_total, $order_total, $order_time, $transaction_status, $transaction_id, $transaction_count) = $result->fetch(3)) {
    $checkss = md5($order_id . $global_config['sitekey'] . session_id());
    $data_content[] = array(
        "checkss" => $checkss,
        "order_id" => $order_id,
        "order_code" => $order_code,
        "transaction_status" => $transaction_status,
        "transaction_id" => $transaction_id,
        "transaction_count" => $transaction_count,
        "order_note" => $order_note,
        "user_id" => $user_id,
        "unit_total" => $unit_total,
        "order_total" => $order_total,
        "order_time" => $order_time,
        "link" => $link_module . "&amp;" . NV_OP_VARIABLE . "=payment&amp;order_id=" . $order_id . "&checkss=" . $checkss,
        "link_remove" => $link_module . "&amp;" . NV_OP_VARIABLE . "=delhis&amp;order_id=" . $order_id . "&checkss=" . $checkss
    );
}

$contents = call_user_func("history_order", $data_content);

$page_title = $lang_module['history_title'];

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
