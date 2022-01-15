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

$order_id = $nv_Request->get_string('order_id', 'get', '');
$checkss = $nv_Request->get_string('checkss', 'get', '');

if ($order_id > 0 and $checkss == md5($order_id . $global_config['sitekey'] . session_id())) {
    // Chặn lập chỉ mục tìm kiếm
    $nv_BotManager->setPrivate();

    $table_name = $db_config['prefix'] . '_' . $module_data . '_orders';
    $link = NV_STATIC_URL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=';

    $result = $db->query('SELECT * FROM ' . $table_name . ' WHERE order_id=' . $order_id);
    $data = $result->fetch();

    if (empty($data)) {
        nv_redirect_location(NV_STATIC_URL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name, true);
    }

    // Thong tin chi tiet mat hang trong don hang
    $listid = $listnum = $listprice = $listgroup = array();
    $result = $db->query('SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . '_orders_id WHERE order_id=' . $order_id);
    while ($row = $result->fetch()) {
        $listid[] = $row['proid'];
        $listnum[] = $row['num'];
        $listprice[] = $row['price'];
        $listgroup[] = $row['group_id'];
    }

    $data_pro = array();
    $temppro = array();
    $i = 0;

    foreach ($listid as $proid) {
        if (empty($listprice[$i])) {
            $listprice[$i] = 0;
        }
        if (empty($listnum[$i])) {
            $listnum[$i] = 0;
        }

        $temppro[$proid] = array( 'price' => $listprice[$i], 'num' => $listnum[$i] );

        $arrayid[] = $proid;
        ++$i;
    }

    if (! empty($arrayid)) {
        $templistid = implode(',', $arrayid);

        $sql = 'SELECT t1.id, t1.listcatid, t1.publtime, t1.' . NV_LANG_DATA . '_title, t1.' . NV_LANG_DATA . '_alias, t1.' . NV_LANG_DATA . '_hometext, t2.' . NV_LANG_DATA . '_title, t1.money_unit FROM ' . $db_config['prefix'] . '_' . $module_data . '_rows as t1 LEFT JOIN ' . $db_config['prefix'] . '_' . $module_data . '_units as t2 ON t1.product_unit = t2.id WHERE t1.id IN (' . $templistid . ') AND t1.status =1';
        $result = $db->query($sql);

        while (list($id, $listcatid, $publtime, $title, $alias, $hometext, $unit, $money_unit) = $result->fetch(3)) {
            $data_pro[] = array(
                'id' => $id,
                'publtime' => $publtime,
                'title' => $title,
                'alias' => $alias,
                'hometext' => $hometext,
                'product_price' => $temppro[$id]['price'],
                'product_unit' => $unit,
                'money_unit' => $money_unit,
                'link_pro' => $link . $global_array_shops_cat[$listcatid]['alias'] . '/' . $alias . $global_config['rewrite_exturl'],
                'product_number' => $temppro[$id]['num']
            );
        }
    }

    $page_title = $data['order_code'];
    $contents = call_user_func('print_pay', $data, $data_pro);

    include NV_ROOTDIR . '/includes/header.php';
    echo nv_site_theme($contents, false);
    include NV_ROOTDIR . '/includes/footer.php';
} else {
    nv_redirect_location(NV_STATIC_URL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name, true);
}
