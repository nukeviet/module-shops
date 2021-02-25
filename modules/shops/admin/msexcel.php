<?php

/**
 * @Project NUKEVIET 4.x
 * @Author MyNukeViet (contact@mynukeviet.com)
 * @Copyright (C) 2016 MyNukeViet. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 9-8-2016 14:43
 */
if (!defined('NV_IS_FILE_ADMIN')) {
    die('Stop!!!');
}

$page_title = $lang_module['content_list'];

if (!class_exists('PHPExcel') and file_exists(NV_ROOTDIR . '/includes/class/PHPExcel.php')) {
    require_once NV_ROOTDIR . '/includes/class/PHPExcel.php';
}

if (!class_exists('PHPExcel')) {
    $contents = nv_theme_alert($lang_module['phpexcel_not_exists_title'], $lang_module['phpexcel_not_exists_content'], 'danger');
    include NV_ROOTDIR . '/includes/header.php';
    echo nv_admin_theme($contents);
    include NV_ROOTDIR . '/includes/footer.php';
}

if ($nv_Request->isset_request('import', 'get')) {

    if ($nv_Request->isset_request('perform', 'post,get')) {

        if (!isset($_FILES['upload_fileupload'])) {
            die('NO_' . $lang_module['phpexcel_required_file']);
        } elseif (!is_uploaded_file($_FILES['upload_fileupload']['tmp_name'])) {
            die('NO_' . $lang_module['phpexcel_file_not_exists']);
        }

        $objPHPExcel = PHPExcel_IOFactory::load($_FILES['upload_fileupload']['tmp_name']);
        $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
        $highestRow = $objWorksheet->getHighestRow();

        @unlink($_FILES['upload_fileupload']['tmp_name']);

        $startCol = 'A';
        $startRow = 4;
        $highestColumn = 'L';

        $array_field = array(
            'id',
            'listcatid',
            'product_code',
            NV_LANG_DATA . '_title',
            NV_LANG_DATA . '_hometext',
            'product_number',
            'product_price',
            'money_unit',
            'product_unit',
            'product_weight',
            'weight_unit',
            'discount_id'
        );

        $array_data = array();
        for ($row = $startRow; $row <= $highestRow; $row++) {
            $i = $col = 0;
            for ($column = $startCol; $column <= $highestColumn; $column++) {
                $col_field = $array_field[$col];
                $array_data[$row][$col_field] = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                $col++;
            }
        }

        $array_error = array();
        foreach ($array_data as $data) {
            $error = array(
                'id' => intval($data['id']),
                'cat' => isset($global_array_shops_cat[$data['listcatid']]) ? $global_array_shops_cat[$data['listcatid']]['title'] : '',
                'code' => $data['product_code'],
                'title' => $data[NV_LANG_DATA . '_title']
            );

            if (empty($data['listcatid']) or !is_numeric($data['listcatid'])) {
                $error['message'] = $lang_module['phpexcel_required_listcatid'];
                $array_error[] = $error;
            } elseif (!isset($global_array_shops_cat[$data['listcatid']])) {
                $error['message'] = $lang_module['phpexcel_isset_listcatid'];
                $array_error[] = $error;
            }

            if (empty($data[NV_LANG_DATA . '_title'])) {
                $error['message'] = $lang_module['phpexcel_required_title'];
                $array_error[] = $error;
            }

            if (empty($data['product_number']) or $data['product_number'] < 0) {
                $error['message'] = $lang_module['phpexcel_required_number'];
                $array_error[] = $error;
            }

            if (!is_numeric($data['product_price'])) {
                $error['message'] = $lang_module['phpexcel_required_price'];
                $array_error[] = $error;
            }

            if (!isset($money_config[$data['money_unit']])) {
                $error['message'] = $lang_module['phpexcel_isset_money_unit'];
                $array_error[] = $error;
            }
        }

        if (!empty($array_error)) {
            die(json_encode($array_error));
        }

        foreach ($array_data as $data) {
            try {
                $stmt = $db->prepare('UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_rows SET
                listcatid = :listcatid,
                product_code = :product_code,
                ' . NV_LANG_DATA . '_title = :title,
                ' . NV_LANG_DATA . '_hometext = :hometext,
                product_number = :product_number,
                product_price = :product_price,
                money_unit = :money_unit,
                product_unit = :product_unit,
                product_weight = :product_weight,
                weight_unit = :weight_unit,
                discount_id = :discount_id
                WHERE id=' . $data['id']);

                $stmt->bindParam(':listcatid', $data['listcatid'], PDO::PARAM_INT);
                $stmt->bindParam(':product_code', $data['product_code'], PDO::PARAM_STR);
                $stmt->bindParam(':title', $data[NV_LANG_DATA . '_title'], PDO::PARAM_STR);
                $stmt->bindParam(':hometext', $data[NV_LANG_DATA . '_hometext'], PDO::PARAM_STR);
                $stmt->bindParam(':product_number', $data['product_number'], PDO::PARAM_INT);
                $stmt->bindParam(':product_price', $data['product_price'], PDO::PARAM_STR);
                $stmt->bindParam(':money_unit', $data['money_unit'], PDO::PARAM_STR);
                $stmt->bindParam(':product_unit', $data['product_unit'], PDO::PARAM_INT);
                $stmt->bindParam(':product_weight', $data['product_weight'], PDO::PARAM_STR);
                $stmt->bindParam(':weight_unit', $data['weight_unit'], PDO::PARAM_INT);
                $stmt->bindParam(':discount_id', $data['discount_id'], PDO::PARAM_INT);
                $stmt->execute();
            } catch (Exception $e) {
                //
            }
        }
        die('OK');
    }

    $xtpl = new XTemplate($op . '.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('ACTION', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&import=1');

    $xtpl->parse('import');
    $contents = $xtpl->text('import');

    $page_title = $lang_module['import'];

    include NV_ROOTDIR . '/includes/header.php';
    echo nv_admin_theme($contents);
    include NV_ROOTDIR . '/includes/footer.php';
}

// List pro_unit
$array_unit = array();
$sql = 'SELECT id, ' . NV_LANG_DATA . '_title FROM ' . $db_config['prefix'] . '_' . $module_data . '_units';
$result_unit = $db->query($sql);
if ($result_unit->rowCount() == 0) {
    Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=prounit');
    die();
} else {
    while ($row = $result_unit->fetch()) {
        $array_unit[$row['id']] = $row;
    }
}

$stype = $nv_Request->get_string('stype', 'get', '-');
$catid = $nv_Request->get_int('catid', 'get', 0);
$from_time = $nv_Request->get_string('from', 'get', '');
$to_time = $nv_Request->get_string('to', 'get', '');
$per_page_old = $nv_Request->get_int('per_page', 'cookie', 50);
$per_page = $nv_Request->get_int('per_page', 'get', $per_page_old);
$is_download = $nv_Request->get_int('download', 'get', 0);

if ($per_page < 1 and $per_page > 500) {
    $per_page = 50;
}

if ($per_page_old != $per_page) {
    $nv_Request->set_Cookie('per_page', $per_page, NV_LIVE_COOKIE_TIME);
}

$q = $nv_Request->get_title('q', 'get', '');
$q = str_replace('+', ' ', $q);
$q = nv_substr($q, 0, NV_MAX_SEARCH_LENGTH);
$qhtml = nv_htmlspecialchars($q);
$ordername = $nv_Request->get_string('ordername', 'get', 'publtime');
$order = $nv_Request->get_string('order', 'get') == 'asc' ? 'asc' : 'desc';

$listcatid = $nv_Request->get_int('listcatid', 'get');
$where = '';
if (!empty($listcatid)) {
    if (isset($global_array_shops_cat[$listcatid])) {
        $subcatid = $global_array_shops_cat[$listcatid]['subcatid'];
        $where = 'listcatid=' . $listcatid;
        if ($subcatid != 0) {
            $where .= ' or listcatid IN (' . $subcatid . ')';
        }
    }
}

$array_search = array(
    'product_code' => $lang_module['search_product_code'],
    'title' => $lang_module['search_title'],
    'bodytext' => $lang_module['search_bodytext'],
    'author' => $lang_module['search_author'],
    'admin_id' => $lang_module['search_admin']
);
$array_in_rows = array(
    'title',
    'bodytext'
);
$array_in_ordername = array(
    'title',
    'publtime',
    'exptime',
    'hitstotal',
    'product_number',
    'num_sell'
);

if (!in_array($stype, array_keys($array_search))) {
    $stype = '-';
}

if (!in_array($ordername, array_keys($array_in_ordername))) {
    $ordername = 'id';
}

$from = $db_config['prefix'] . '_' . $module_data . '_rows AS a LEFT JOIN ' . NV_USERS_GLOBALTABLE . ' AS b ON a.user_id=b.userid';

$page = $nv_Request->get_int('page', 'get', 1);
$checkss = $nv_Request->get_string('checkss', 'get', '');

if ($checkss == md5(session_id())) {
    // Tim theo tu khoa
    if ($stype == 'product_code') {
        $from .= " WHERE product_code LIKE '%" . $db->dblikeescape($q) . "%' ";
    } elseif (in_array($stype, $array_in_rows) and !empty($q)) {
        $from .= " WHERE " . NV_LANG_DATA . "_" . $stype . " LIKE '%" . $db->dblikeescape($qhtml) . "%' ";
    } elseif ($stype == 'admin_id' and !empty($q)) {
        $sql = "SELECT userid FROM " . NV_USERS_GLOBALTABLE . " WHERE userid IN (SELECT admin_id FROM " . NV_AUTHORS_GLOBALTABLE . ") AND username LIKE '%" . $db->dblikeescape($q) . "%' OR first_name LIKE '%" . $db->dblikeescape($q) . "%' OR last_name LIKE '%" . $db->dblikeescape($q) . "%'";
        $result = $db->query($sql);
        $array_admin_id = array();
        while (list ($admin_id) = $result->fetch(3)) {
            $array_admin_id[] = $admin_id;
        }
        $from .= " WHERE admin_id IN (0," . implode(",", $array_admin_id) . ",0)";
    } elseif (!empty($q)) {
        $sql = "SELECT userid FROM " . NV_USERS_GLOBALTABLE . " WHERE userid IN (SELECT admin_id FROM " . NV_AUTHORS_GLOBALTABLE . ") AND username LIKE '%" . $db->dblikeescape($q) . "%' OR first_name LIKE '%" . $db->dblikeescape($q) . "%'OR last_name LIKE '%" . $db->dblikeescape($q) . "%'";
        $result = $db->query($sql);

        $array_admin_id = array();
        while (list ($admin_id) = $result->fetch(3)) {
            $array_admin_id[] = $admin_id;
        }

        $arr_from = array();
        $arr_from[] = "(product_code LIKE '%" . $db->dblikeescape($qhtml) . "%')";
        foreach ($array_in_rows as $val) {
            $arr_from[] = "(" . NV_LANG_DATA . "_" . $val . " LIKE '%" . $db->dblikeescape($qhtml) . "%')";
        }
        $from .= " WHERE ( " . implode(" OR ", $arr_from);
        if (!empty($array_admin_id)) {
            $from .= ' OR (admin_id IN (0,' . implode(',', $array_admin_id) . ',0))';
        }
        $from .= ' )';
    }

    // Tim theo loai san pham
    if (!empty($catid)) {
        if (empty($q)) {
            $from .= ' WHERE';
        } else {
            $from .= ' AND';
        }

        if ($global_array_shops_cat[$catid]['numsubcat'] == 0) {
            $from .= ' listcatid=' . $catid;
        } else {
            $array_cat = array();
            $array_cat = GetCatidInParent($catid);
            $from .= ' listcatid IN (' . implode(',', $array_cat) . ')';
        }
    }

    // Tim theo ngay thang
    if (!empty($from_time)) {
        if (empty($q) and empty($catid)) {
            $from .= ' WHERE';
        } else {
            $from .= ' AND';
        }

        if (!empty($from_time) and preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $from_time, $m)) {
            $time = mktime(0, 0, 0, $m[2], $m[1], $m[3]);
        } else {
            $time = NV_CURRENTTIME;
        }

        $from .= ' publtime >= ' . $time . '';
    }

    if (!empty($to_time)) {
        if (empty($q) and empty($catid) and empty($from_time)) {
            $from .= ' WHERE';
        } else {
            $from .= ' AND';
        }

        if (!empty($to_time) and preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $to_time, $m)) {
            $to = mktime(23, 59, 59, $m[2], $m[1], $m[3]);
        } else {
            $to = NV_CURRENTTIME;
        }
        $from .= ' publtime <= ' . $to . '';
    }
}
if (!empty($where)) {
    $from .= ' WHERE ' . $where;
}

$num_items = $db->query('SELECT COUNT(*) FROM ' . $from)->fetchColumn();
$url_download = parse_url($_SERVER['REQUEST_URI']);
$url_download = str_replace('items', 'msexcel', $url_download['query']);

$xtpl = new XTemplate($op . '.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);
$xtpl->assign('URL_DOWNLOAD', NV_BASE_ADMINURL . 'index.php?' . $url_download . '&download=1');
$xtpl->assign('URL_IMPORT', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;import=1');

// Loai san pham
foreach ($global_array_shops_cat as $cat) {
    if ($cat['catid'] > 0) {
        $xtitle_i = '';
        if ($cat['lev'] > 0) {
            $xtitle_i .= '&nbsp;&nbsp;&nbsp;|';
            for ($i = 1; $i <= $cat['lev']; ++$i) {
                $xtitle_i .= '---';
            }
            $xtitle_i .= '>&nbsp;';
        }
        $xtitle_i .= $cat['title'];
        $cat['title'] = $xtitle_i;

        $cat['selected'] = $cat['catid'] == $catid ? ' selected="selected"' : '';
        $xtpl->assign('CATID', $cat);
        $xtpl->parse('main.catid');
    }
}

// Kieu tim kiem
foreach ($array_search as $key => $val) {
    $xtpl->assign('STYPE', array(
        'key' => $key,
        'title' => $val,
        'selected' => ($key == $stype) ? ' selected="selected"' : ''
    ));
    $xtpl->parse('main.stype');
}

// So san pham hien thi
$i = 5;
while ($i <= 1000) {
    $xtpl->assign('PER_PAGE', array(
        'key' => $i,
        'title' => $i,
        'selected' => ($i == $per_page) ? ' selected="selected"' : ''
    ));
    $xtpl->parse('main.per_page');
    $i = $i + 5;
}

if ($ordername == 'title') {
    $xtpl->parse('main.order_title.' . ($order == 'desc' ? 'desc' : 'asc'));
    $xtpl->parse('main.order_title');
} else {
    $xtpl->parse('main.no_order_title');
}

if ($ordername == 'publtime') {
    $xtpl->parse('main.order_publtime.' . ($order == 'desc' ? 'desc' : 'asc'));
    $xtpl->parse('main.order_publtime');
} else {
    $xtpl->parse('main.no_order_publtime');
}

if ($ordername == 'hitstotal') {
    $xtpl->parse('main.order_hitstotal.' . ($order == 'desc' ? 'desc' : 'asc'));
    $xtpl->parse('main.order_hitstotal');
} else {
    $xtpl->parse('main.no_order_hitstotal');
}

if ($ordername == 'product_number') {
    $xtpl->parse('main.order_product_number.' . ($order == 'desc' ? 'desc' : 'asc'));
    $xtpl->parse('main.order_product_number');
} else {
    $xtpl->parse('main.no_order_product_number');
}

if ($ordername == 'num_sell') {
    $xtpl->parse('main.order_num_sell.' . ($order == 'desc' ? 'desc' : 'asc'));
    $xtpl->parse('main.order_num_sell');
} else {
    $xtpl->parse('main.no_order_num_sell');
}

// Thong tin tim kiem
$xtpl->assign('Q', $q);
$xtpl->assign('FROM', $from_time);
$xtpl->assign('TO', $to_time);
$xtpl->assign('CHECKSESS', md5(session_id()));
$xtpl->assign('SEARCH_NOTE', sprintf($lang_module['search_note'], NV_MIN_SEARCH_LENGTH, NV_MAX_SEARCH_LENGTH));
$xtpl->assign('NV_MAX_SEARCH_LENGTH', NV_MAX_SEARCH_LENGTH);

$order2 = ($order == 'asc') ? 'desc' : 'asc';
$base_url_name = NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&per_page=' . $per_page . '&catid=' . $catid . '&stype=' . $stype . '&q=' . $q . '&checkss=' . $checkss . '&ordername=title&order=' . $order2 . '&page=' . $page;
$base_url_publtime = NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&per_page=' . $per_page . '&catid=' . $catid . '&stype=' . $stype . '&q=' . $q . '&checkss=' . $checkss . '&ordername=publtime&order=' . $order2 . '&page=' . $page;
$base_url_hitstotal = NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&per_page=' . $per_page . '&catid=' . $catid . '&stype=' . $stype . '&q=' . $q . '&checkss=' . $checkss . '&ordername=hitstotal&order=' . $order2 . '&page=' . $page;
$base_url_product_number = NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&per_page=' . $per_page . '&catid=' . $catid . '&stype=' . $stype . '&q=' . $q . '&checkss=' . $checkss . '&ordername=product_number&order=' . $order2 . '&page=' . $page;
$base_url_num_sell = NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&per_page=' . $per_page . '&catid=' . $catid . '&stype=' . $stype . '&q=' . $q . '&checkss=' . $checkss . '&ordername=num_sell&order=' . $order2 . '&page=' . $page;

// Order
$xtpl->assign('BASE_URL_NAME', $base_url_name);
$xtpl->assign('BASE_URL_PUBLTIME', $base_url_publtime);
$xtpl->assign('BASE_URL_HITSTOTAL', $base_url_hitstotal);
$xtpl->assign('BASE_URL_PNUMBER', $base_url_product_number);
$xtpl->assign('BASE_URL_NUM_SELL', $base_url_num_sell);

$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&per_page=' . $per_page . '&catid=' . $catid . '&amp;stype=' . $stype . '&q=' . $q . '&checkss=' . $checkss . '&ordername=' . $ordername . '&order=' . $order;
$ord_sql = ($ordername == 'title' ? NV_LANG_DATA . '_title' : $ordername) . ' ' . $order;
$theme = $site_mods[$module_name]['theme'] ? $site_mods[$module_name]['theme'] : $global_config['site_theme'];

if (!$is_download) {
    $db->sqlreset()
        ->select('id, listcatid, user_id, homeimgfile, homeimgthumb, ' . NV_LANG_DATA . '_title, ' . NV_LANG_DATA . '_alias, hitstotal, status, edittime, publtime, exptime, product_number, product_price, money_unit, product_unit, num_sell, username')
        ->from($from)
        ->order($ord_sql)
        ->limit($per_page)
        ->offset(($page - 1) * $per_page);
    $result = $db->query($db->sql());

    $a = 0;
    while (list ($id, $listcatid, $admin_id, $homeimgfile, $homeimgthumb, $title, $alias, $hitstotal, $status, $edittime, $publtime, $exptime, $product_number, $product_price, $money_unit, $product_unit, $num_sell, $username) = $result->fetch(3)) {
        $publtime = nv_date('H:i d/m/y', $publtime);
        $edittime = nv_date('H:i d/m/y', $edittime);
        $title = nv_clean60($title);

        $catid_i = 0;
        if ($catid > 0) {
            $catid_i = $catid;
        } else {
            $catid_i = $listcatid;
        }

        // Xac dinh anh nho
        if ($homeimgthumb == 1) {
            // image thumb

            $thumb = NV_BASE_SITEURL . NV_FILES_DIR . '/' . $module_upload . '/' . $homeimgfile;
            $imghome = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $homeimgfile;
        } elseif ($homeimgthumb == 2) {
            // image file

            $imghome = $thumb = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $homeimgfile;
        } elseif ($homeimgthumb == 3) {
            // image url

            $imghome = $thumb = $homeimgfile;
        } elseif (file_exists(NV_ROOTDIR . '/themes/' . $theme . '/images/' . $module_file . '/no-image.jpg')) {
            $imghome = $thumb = NV_BASE_SITEURL . 'themes/' . $theme . '/images/' . $module_file . '/no-image.jpg';
        } else {
            $imghome = $thumb = NV_BASE_SITEURL . 'themes/default/images/' . $module_file . '/no-image.jpg';
        }

        $xtpl->assign('ROW', array(
            'id' => $id,
            'link' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $global_array_shops_cat[$catid_i]['alias'] . '/' . $alias . $global_config['rewrite_exturl'],
            'link_seller' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=seller&amp;pro_id=' . $id . '&amp;nv_redirect=' . nv_redirect_encrypt($base_url),
            'link_copy' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=content&amp;copy&amp;id=' . $id,
            'link_warehouse' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=warehouse&amp;listid=' . $id . '&amp;checkss=' . md5($global_config['sitekey'] . session_id()),
            'title' => $title,
            'publtime' => $publtime,
            'edittime' => $edittime,
            'hitstotal' => $hitstotal,
            'num_sell' => $num_sell,
            'product_unit' => isset($array_unit[$product_unit]) ? $array_unit[$product_unit][NV_LANG_DATA . '_title'] : '',
            'status' => $lang_module['status_' . $status],
            'admin_id' => !empty($username) ? $username : '',
            'product_number' => $product_number,
            'product_price' => nv_number_format($product_price, nv_get_decimals($money_unit)),
            'money_unit' => $money_unit,
            'thumb' => $thumb,
            'imghome' => $imghome,
            'imghome_info' => nv_is_image(NV_ROOTDIR . '/' . $imghome),
            'link_edit' => nv_link_edit_page($id),
            'link_delete' => nv_link_delete_page($id)
        ));

        if ($num_sell > 0) {
            $xtpl->parse('main.loop.seller');
        } else {
            $xtpl->parse('main.loop.seller_empty');
        }

        // Hien thi nhap kho
        if ($pro_config['active_warehouse']) {
            $xtpl->parse('main.loop.warehouse_icon');
        }

        $xtpl->parse('main.loop');

        ++$a;
    }

    $xtpl->assign('COUNT_PRODUCT', sprintf($lang_module['count_product'], $a, $num_items));
} else {

    $array_data = array();

    $db->sqlreset()
        ->select('*')
        ->from($from)
        ->order($ord_sql);
    $result = $db->query($db->sql());

    $a = 0;
    while ($row = $result->fetch()) {
        $array_data[] = $row;
    }

    if (!empty($array_data)) {
        $type = 'xlsx';
        $array_title = array(
            'id' => $lang_module['phpexcel_id'],
            'listcatid' => $lang_module['phpexcel_listcatid'],
            'product_code' => $lang_module['phpexcel_code'],
            NV_LANG_DATA . '_title' => $lang_module['name'],
            NV_LANG_DATA . '_hometext' => $lang_module['phpexcel_hometext'],
            'product_number' => $lang_module['phpexcel_number'],
            'product_price' => $lang_module['content_product_product_price'],
            // 'price_config' => $lang_module['phpexcel_price_config'],
            'money_unit' => $lang_module['content_product_money_unit'],
            'product_unit' => $lang_module['phpexcel_product_unit'],
            'product_weight' => $lang_module['phpexcel_weight'],
            'weight_unit' => $lang_module['weight_unit'],
            'discount_id' => $lang_module['phpexcel_discount']
        );

        $array = array(
            'objType' => 'Excel2007',
            'objExt' => 'xlsx'
        );

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);

        // Set properties
        $objPHPExcel->getProperties()
            ->setCreator($admin_info['username'])
            ->setLastModifiedBy($admin_info['username'])
            ->setTitle($lang_module['content_list'])
            ->setSubject($lang_module['content_list'])
            ->setDescription($lang_module['content_list'])
            ->setCategory($module_name);

        $columnIndex = 0; // Cot bat dau ghi du lieu
        $rowIndex = 3; // Dong bat dau ghi du lieu

        // Tieu de cot
        $col = $columnIndex;
        foreach ($array_title as $title) {
            $objPHPExcel->getActiveSheet()->setCellValue(PHPExcel_Cell::stringFromColumnIndex($col) . $rowIndex, $title);
            $col++;
        }

        // Hien thi danh sach cau tra loi
        $i = $rowIndex + 1;
        $number = 1;
        foreach ($array_data as $data) {
            $j = $columnIndex;
            foreach ($array_title as $field => $title) {
                $col = PHPExcel_Cell::stringFromColumnIndex($j);
                $CellValue = $data[$field];
                $objPHPExcel->getActiveSheet()->setCellValue($col . $i, $CellValue);
                $j++;
            }
            $i++;
        }

        $highestRow = $i - 1;
        $highestColumn = PHPExcel_Cell::stringFromColumnIndex($j - 1);

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Sheet 1');

        // Set page orientation and size
        $objPHPExcel->getActiveSheet()
            ->getPageSetup()
            ->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
        $objPHPExcel->getActiveSheet()
            ->getPageSetup()
            ->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        // Excel title
        $objPHPExcel->getActiveSheet()->mergeCells('A2:' . $highestColumn . '2');
        $objPHPExcel->getActiveSheet()->setCellValue('A2', strtoupper($lang_module['content_list']));
        $objPHPExcel->getActiveSheet()
            ->getStyle('A2')
            ->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()
            ->getStyle('A2')
            ->getAlignment()
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        // Set color
        $styleArray = array(
            'borders' => array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'argb' => 'FF000000'
                    )
                )
            )
        );
        $objPHPExcel->getActiveSheet()
            ->getStyle('A3' . ':' . $highestColumn . $highestRow)
            ->applyFromArray($styleArray);

        // Set font size
        $objPHPExcel->getActiveSheet()
            ->getStyle("A1:" . $highestColumn . $highestRow)
            ->getFont()
            ->setSize(13);

        // Set auto column width
        foreach (range('A', $highestColumn) as $columnID) {
            $objPHPExcel->getActiveSheet()
                ->getColumnDimension($columnID)
                ->setAutoSize(true);
        }

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $array['objType']);
        $file_src = NV_ROOTDIR . NV_BASE_SITEURL . NV_TEMP_DIR . '/' . change_alias($lang_module['content_list']) . '.' . $array['objExt'];
        $objWriter->save($file_src);

        $download = new NukeViet\Files\Download($file_src, NV_ROOTDIR . NV_BASE_SITEURL . NV_TEMP_DIR);
        $download->download_file();
        die();
    }
}

$array_list_action = array(
    'delete' => $lang_global['delete'],
    'publtime' => $lang_module['publtime'],
    'exptime' => $lang_module['exptime'],
    'addtoblock' => $lang_module['addtoblock']
);

if ($pro_config['active_warehouse']) {
    $array_list_action['warehouse'] = $lang_module['warehouse'];
}

while (list ($catid_i, $title_i) = each($array_list_action)) {
    $xtpl->assign('ACTION', array(
        'key' => $catid_i,
        'title' => $title_i
    ));
    $xtpl->parse('main.action');
}

$xtpl->assign('ACTION_CHECKSESS', md5($global_config['sitekey'] . session_id()));

$generate_page = nv_generate_page($base_url, $num_items, $per_page, $page);
if (!empty($generate_page)) {
    $xtpl->assign('GENERATE_PAGE', $generate_page);
    $xtpl->parse('main.generate_page');
}

if(!class_exists('PHPExcel')){
    $xtpl->parse('main.disabled');
    $xtpl->assign('DISABLED', 'disabled');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
