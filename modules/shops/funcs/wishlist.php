<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2017 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 04/18/2017 09:47
 */

if (!defined('NV_IS_MOD_SHOPS')) {
    die('Stop!!!');
}

$page_title = $lang_module['wishlist_product'];

if (!defined('NV_IS_USER')) {
    $redirect = nv_url_rewrite(NV_STATIC_URL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name, true);
    nv_redirect_location(NV_STATIC_URL . 'index.php?' . NV_NAME_VARIABLE . '=users&' . NV_OP_VARIABLE . '=login&nv_redirect=' . nv_redirect_encrypt($redirect));
}

if (empty($array_wishlist_id)) {
    nv_redirect_location(NV_STATIC_URL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name, true);
}

if (preg_match('/^page\-([0-9]+)$/', (isset($array_op[1]) ? $array_op[1] : ''), $m)) {
    $page = (int) $m[1];
}

$data_content = array();
$array_wishlist_id = implode(',', $array_wishlist_id);

$compare_id = $nv_Request->get_string($module_data . '_compare_id', 'session', '');
$compare_id = unserialize($compare_id);

// Fetch Limit
$db->sqlreset()
    ->select('COUNT(*)')
    ->from($db_config['prefix'] . '_' . $module_data . '_rows t1')
    ->where('t1.inhome=1 AND t1.status =1 AND id IN (' . $array_wishlist_id . ')');

$num_items = $db->query($db->sql())
    ->fetchColumn();

$db->select('t1.id, t1.listcatid, t1.publtime, t1.' . NV_LANG_DATA . '_title, t1.' . NV_LANG_DATA . '_alias, t1.' . NV_LANG_DATA . '_hometext, t1.homeimgalt, t1.homeimgfile, t1.homeimgthumb, t1.product_code, t1.product_number, t1.product_price, t1.money_unit, t1.showprice, t2.newday')
    ->join('INNER JOIN ' . $db_config['prefix'] . '_' . $module_data . '_catalogs t2 ON t2.catid = t1.listcatid')
    ->limit($per_page)
    ->offset(($page - 1) * $per_page);

$result = $db->query($db->sql());

while (list ($id, $listcatid, $publtime, $title, $alias, $hometext, $homeimgalt, $homeimgfile, $homeimgthumb, $product_code, $product_number, $product_price, $money_unit, $showprice, $newday) = $result->fetch(3)) {
    if ($homeimgthumb == 1) {
        //image thumb

        $thumb = NV_STATIC_URL . NV_FILES_DIR . '/' . $module_upload . '/' . $homeimgfile;
    } elseif ($homeimgthumb == 2) {
        //image file

        $thumb = NV_STATIC_URL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $homeimgfile;
    } elseif ($homeimgthumb == 3) {
        //image url

        $thumb = $homeimgfile;
    } else {
        //no image

        $thumb = NV_STATIC_URL . 'themes/' . $module_info['template'] . '/images/' . $module_file . '/no-image.jpg';
    }

    $data_content[] = array(
        'id' => $id,
        'listcatid' => $listcatid,
        'publtime' => $publtime,
        'title' => $title,
        'alias' => $alias,
        'hometext' => $hometext,
        'homeimgalt' => $homeimgalt,
        'homeimgthumb' => $thumb,
        'product_price' => $product_price,
        'product_code' => $product_code,
        'product_number' => $product_number,
        'money_unit' => $money_unit,
        'showprice' => $showprice,
        'newday' => $newday,
        'link_pro' => NV_STATIC_URL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $global_array_shops_cat[$listcatid]['alias'] . '/' . $alias . $global_config['rewrite_exturl'],
        'link_order' => NV_STATIC_URL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=setcart&amp;id=' . $id
    );
}

if (empty($data_content) and $page > 1) {
    nv_redirect_location(NV_STATIC_URL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name, true);
}

$base_url = NV_STATIC_URL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=wishlist';
$html_pages = nv_alias_page($page_title, $base_url, $num_items, $per_page, $page);

$contents = nv_template_wishlist($data_content, $html_pages);

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
