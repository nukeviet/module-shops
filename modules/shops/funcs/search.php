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

function BoldKeywordInStr($str, $keyword)
{
    $tmp = explode(' ', $keyword);
    foreach ($tmp as $k) {
        $tp = strtolower($k);
        $str = str_replace($tp, "<span class=\"keyword\">" . $tp . "</span>", $str);
        $tp = strtoupper($k);
        $str = str_replace($tp, "<span class=\"keyword\">" . $tp . "</span>", $str);
        $k[0] = strtoupper($k[0]);
        $str = str_replace($k, "<span class=\"keyword\">" . $k . "</span>", $str);
    }
    return $str;
}

$key = nv_substr($nv_Request->get_title('q', 'get', '', 1), 0, 100);
$from_date = $nv_Request->get_title('from_date', 'get', '', 1);
$to_date = $nv_Request->get_title('to_date', 'get', '', 1);
$catid = $nv_Request->get_int('catid', 'get', 0);
$price1_temp = $nv_Request->get_string('price1', 'get', '');
$price2_temp = $nv_Request->get_string('price2', 'get', '');

$price1_temp = preg_replace('/[^0-9,\.]/', '', $price1_temp);
$price2_temp = preg_replace('/[^0-9,\.]/', '', $price2_temp);

$price1_sql = preg_replace('/[^0-9]/', '', $price1_temp);
$price2_sql = preg_replace('/[^0-9]/', '', $price2_temp);
$typemoney = $nv_Request->get_string('typemoney', 'get', '');

$check_num = $nv_Request->get_int('choose', 'get', 1);
$pages = $nv_Request->get_int('page', 'get', 1);
$date_array['from_date'] = $from_date;
$date_array['to_date'] = $to_date;
$array_price['price1'] = $price1_temp;
$array_price['price2'] = $price2_temp;
$per_pages = 20;

$page_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=search';
if ($page > 1) {
    $page_url .= '&amp;page=' . $page;
}
$canonicalUrl = getCanonicalUrl($page_url);

$array_cat_search = array();
$array_cat_search[0] = array(
    'catid' => 0,
    'title' => $lang_module['search_all'],
    'select' => (0 == $catid) ? "selected" : "",
    'xtitle' => ''
);

foreach ($global_array_shops_cat as $arr_cat_i) {
    $xtitle = '';
    if ($arr_cat_i['lev'] > 0) {
        for ($i = 1; $i <= $arr_cat_i['lev']; $i++) {
            $xtitle .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        }
    }

    $array_cat_search[$arr_cat_i['catid']] = array(
        'catid' => $arr_cat_i['catid'],
        'title' => $arr_cat_i['title'],
        'select' => ($arr_cat_i['catid'] == $catid) ? "selected" : "",
        'xtitle' => $xtitle
    );
}

$contents = call_user_func('search_theme', $key, $check_num, $date_array, $array_cat_search, $array_price, $typemoney);
$where = '';
$tbl_src = '';

if (strlen($key) >= NV_MIN_SEARCH_LENGTH) {
    $dbkey = $db->dblikeescape($key);
    $where = "AND ( product_code LIKE '%" . $dbkey . "%' OR " . NV_LANG_DATA . "_title LIKE '%" . $dbkey . "%' OR " . NV_LANG_DATA . "_bodytext LIKE '%" . $dbkey . "%' ) ";

    if ($pro_config['sortdefault'] == 0) {
        $orderby = 'id DESC';
    } elseif ($pro_config['sortdefault'] == 1) {
        $orderby = 'product_price ASC, t1.id DESC';
    } else {
        $orderby = 'product_price DESC, t1.id DESC';
    }

    if ($catid != 0) {
        if ($global_array_shops_cat[$catid]['numsubcat'] == 0) {
            $where .= 'AND listcatid=' . $catid;
        } else {
            $array_cat = array();
            $array_cat = GetCatidInParent($catid);
            $where .= 'AND listcatid IN (' . implode(',', $array_cat) . ')';
        }
    }

    if ($to_date != '') {
        preg_match('/^([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{4})$/', $to_date, $m);
        $tdate = mktime(0, 0, 0, $m[2], $m[1], $m[3]);
        preg_match('/^([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{4})$/', $from_date, $m);
        $fdate = mktime(0, 0, 0, $m[2], $m[1], $m[3]);
        $where .= " AND ( publtime < $fdate AND publtime >= $tdate ) ";
    }

    if (!empty($price1_sql)) {
        $where .= " AND product_price >= " . $price1_sql;
    }

    if (!empty($price2_sql)) {
        $where .= " AND product_price <= " . $price2_sql;
    }
    
    if (!empty($typemoney)) {
        $where .= " AND money_unit = " . $db->quote($typemoney);
    }

    $table_search = $db_config['prefix'] . '_' . $module_data . '_rows';

    // Fetch Limit
    $db->sqlreset()->select('COUNT(*)')->from($table_search)->where('status =1 ' . $where);

    $numRecord = $db->query($db->sql())->fetchColumn();

    $db->select('id, ' . NV_LANG_DATA . '_title, ' . NV_LANG_DATA . '_alias, listcatid, ' . NV_LANG_DATA . '_hometext, publtime, homeimgfile, homeimgthumb')->order($orderby)->limit($per_pages)->offset(($page - 1) * $per_page);

    $result = $db->query($db->sql());

    $array_content = array();
    $url_link = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=';

    while (list($id, $title, $alias, $listcatid, $hometext, $publtime, $homeimgfile, $homeimgthumb) = $result->fetch(3)) {
        if ($homeimgthumb == 1) {
            //image thumb

            $thumb = NV_BASE_SITEURL . NV_FILES_DIR . '/' . $module_upload . '/' . $homeimgfile;
        } elseif ($homeimgthumb == 2) {
            //image file

            $thumb = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $homeimgfile;
        } elseif ($homeimgthumb == 3) {
            //image url

            $thumb = $homeimgfile;
        } else {
            //no image

            $thumb = NV_STATIC_URL . 'themes/' . $module_info['template'] . '/images/' . $module_file . '/no-image.jpg';
        }

        $array_content[] = array(
            'id' => $id,
            'title' => $title,
            'alias' => $alias,
            'listcatid' => $listcatid,
            'hometext' => $hometext,
            'publtime' => $publtime,
            'homeimgthumb' => $thumb,
        );
    }
    $contents .= call_user_func('search_result_theme', $key, $numRecord, $per_pages, $pages, $array_content, $url_link, $catid);
}

if (empty($key)) {
    $page_title = $module_info['custom_title'];
} else {
    $page_title = $key . ' ' . NV_TITLEBAR_DEFIS . ' ' . $lang_module['search_title'] . ' ' . NV_TITLEBAR_DEFIS . ' ' . $module_info['custom_title'];
}

$key_words = $module_info['keywords'];
$mod_title = $lang_module['main_title'];

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
