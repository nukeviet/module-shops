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

if ($nv_Request->isset_request('check_quantity', 'post')) {
    $id_pro = $nv_Request->get_int('id_pro', 'post', 0);
    $unit = $nv_Request->get_string('pro_unit', 'post', '');
    $listid = $nv_Request->get_string('listid', 'post');
    $listid = array_filter(array_unique(array_map('intval', explode(',', $listid))));
    if (empty($listid)) {
        nv_htmlOutput('OK_' . $quantity . '_' . $lang_module['detail_pro_number'] . ': ' . $quantity . ' ' . $unit);
    }
    asort($listid);

    $quantity = $db->query('SELECT quantity FROM ' . $db_config['prefix'] . '_' . $module_data . '_group_quantity WHERE pro_id = ' . $id_pro . ' AND listgroup="' . implode(',', $listid) . '"')->fetchColumn();
    if (empty($quantity)) {
        $sum = 0;
        $count = 0;
        $listgroupid = GetGroupID($id_pro, 1);
        if (!empty($listgroupid) and !empty($global_array_group)) {
            foreach ($listgroupid as $gid => $subid) {
                $parent_info = $global_array_group[$gid];
                if ($parent_info['in_order']) {
                    $count++;
                }
            }
        }

        $result = $db->query('SELECT listgroup, quantity FROM ' . $db_config['prefix'] . '_' . $module_data . '_group_quantity WHERE pro_id = ' . $id_pro);
        while (list ($listgroup, $quantity) = $result->fetch(3)) {
            $listgroup = explode(',', $listgroup);
            $_t = 0;
            foreach ($listgroup as $_idgroup) {
                if (in_array($_idgroup, $listid)) {
                    $_t = $_t + 1;
                }
            }
            if ($_t == sizeof($listid) or empty($listid)) {
                $sum += $quantity;
            }
        }

        if ($sum == 0 or $count == sizeof($listid)) {
            die('NO_0_' . $lang_module['product_empty']);
        } else {
            die('NO_0_' . $lang_module['detail_pro_number'] . ': ' . $sum . ' ' . $unit);
        }
    } else {
        die('OK_' . $quantity . '_' . $lang_module['detail_pro_number'] . ': ' . $quantity . ' ' . $unit);
    }
}

$compare_id = $nv_Request->get_string($module_data . '_compare_id', 'session', '');
$compare_id = unserialize($compare_id);

// Thiet lap quyen xem chi tiet
$contents = '';
$publtime = 0;

$sql = $db->query('SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . '_rows WHERE ' . NV_LANG_DATA . '_alias = ' . $db->quote($alias_url) . ' AND status=1');
$data_content = $sql->fetch();
if (empty($data_content)) {
    $nv_redirect = NV_STATIC_URL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name;
    redict_link($lang_module['detail_do_not_view'], $lang_module['redirect_to_back_shops'], $nv_redirect);
}
$id = $data_content['id'];

$data_content['array_custom'] = [];
$data_content['array_custom_template'] = [];
$data_content['array_custom_lang'] = [];
$data_content['template'] = [];
$idtemplates = [];

// Dữ liệu tùy biến
if ($global_array_shops_cat[$data_content['listcatid']]['form'] != '') {
    $array_forms = explode(',', $global_array_shops_cat[$data_content['listcatid']]['form']);
    $where = [];
    foreach ($array_forms as $cat_form) {
        $where[] = "alias=" . $db->quote(preg_replace("/[\_]/", "-", $cat_form));
    }
    $cat_templates = $db->query('SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . '_template WHERE ' . implode(' OR ', $where) . ' ORDER BY weight ASC')->fetchAll();
    if (!empty($cat_templates)) {
        foreach ($cat_templates as $cat_form) {
            $idtemplate = $cat_form['id'];
            $listfield = [];
            $array_tmp = [];
            $result = $db->query('SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . '_field ORDER BY weight');
            while ($row = $result->fetch()) {
                $listtemplate = explode('|', $row['listtemplate']);
                if (in_array($idtemplate, $listtemplate)) {
                    $idtemplates[] = $idtemplate;
                    $listfield[] = $row['fid'];
                    $array_tmp[$row['field']] = unserialize($row['language']);
                }
            }

            if (!empty($listfield)) {
                $result = $db->query('SELECT t1.field_value, t2.field, t2.listtemplate FROM ' . $db_config['prefix'] . "_" . $module_data . "_field_value_" . NV_LANG_DATA . ' t1
                INNER JOIN ' . $db_config['prefix'] . '_' . $module_data . '_field t2 WHERE t1.field_id=t2.fid AND t1.rows_id=' . $id);
                $data_content['template'][] = $cat_form;
                while ($row = $result->fetch()) {
                    // Xếp theo danh sách
                    $data_content['array_custom'][$row['field']] = $row['field_value'];

                    // Xếp theo nhóm
                    $row['listtemplate'] = explode(',', $row['listtemplate']);
                    foreach ($row['listtemplate'] as $_tid) {
                        $data_content['array_custom_template'][$_tid][$row['field']] = $row['field_value'];
                    }
                }

                if (!empty($array_tmp)) {
                    foreach ($array_tmp as $f_key => $field) {
                        foreach ($field as $key_lang => $lang_data) {
                            if ($key_lang == NV_LANG_DATA) {
                                $data_content['array_custom_lang'][$f_key] = $lang_data[0];
                            }
                        }
                    }
                    unset($array_tmp);
                }
            }
        }
    }
}

$page_title = !empty($data_content[NV_LANG_DATA . '_tag_title']) ? $data_content[NV_LANG_DATA . '_tag_title'] : $data_content[NV_LANG_DATA . '_title'];
$description = !empty($data_content[NV_LANG_DATA . '_tag_description']) ? $data_content[NV_LANG_DATA . '_tag_description'] : $data_content[NV_LANG_DATA . '_hometext'];
$array_images = [];

if (nv_user_in_groups($global_array_shops_cat[$catid]['groups_view'])) {
    $popup = $nv_Request->get_int('popup', 'post,get', 0);

    $time_set = $nv_Request->get_int($module_data . '_' . $op . '_' . $id, 'session');
    if (empty($time_set)) {
        $nv_Request->set_Session($module_data . '_' . $op . '_' . $id, NV_CURRENTTIME);
        $sql = 'UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_rows SET hitstotal=hitstotal+1 WHERE id=' . $id;
        $db->query($sql);
    }

    $catid = $data_content['listcatid'];
    $base_url_rewrite = nv_url_rewrite(NV_STATIC_URL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $global_array_shops_cat[$catid]['alias'] . '/' . $data_content[NV_LANG_DATA . '_alias'] . $global_config['rewrite_exturl'], true);

    if ($_SERVER['REQUEST_URI'] != $base_url_rewrite and NV_MY_DOMAIN . $_SERVER['REQUEST_URI'] != $base_url_rewrite and !$popup) {
        nv_redirect_location($base_url_rewrite);
    }

    // Lay don vi san pham
    $sql = $db->query('SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . '_units WHERE id = ' . $data_content['product_unit']);
    $data_unit = $sql->fetch();
    $data_unit['title'] = $data_unit[NV_LANG_DATA . '_title'];

    // Hien thi tabs
    $sql = 'SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . '_tabs where active=1 ORDER BY weight ASC';
    $data_content['tabs'] = $nv_Cache->db($sql, 'id', $module_name);

    $data_content['files'] = [];
    if (!empty($data_content['tabs'])) {
        // Download tai lieu san pham
        if ($pro_config['download_active']) {
            $result = $db->query('SELECT id, ' . NV_LANG_DATA . '_title title, ' . NV_LANG_DATA . '_description description, path, filesize, extension, download_groups FROM ' . $db_config['prefix'] . '_' . $module_data . '_files WHERE id IN (SELECT id_files FROM ' . $db_config['prefix'] . '_' . $module_data . '_files_rows WHERE id_rows=' . $data_content['id'] . ')');
            while ($row = $result->fetch()) {
                $row['filesize'] = !empty($row['filesize']) ? nv_convertfromBytes($row['filesize']) : $lang_module['download_file_unknown'];
                $data_content['files'][] = $row;
            }
        }
    }

    // Danh gia - Phan hoi
    $rating_total = 0;
    $result = $db->query('SELECT rating FROM ' . $db_config['prefix'] . '_' . $module_data . '_review WHERE product_id = ' . $data_content['id'] . ' AND status=1');
    $rating_count = $result->rowCount();
    if ($rating_count > 0) {
        while (list ($rating) = $result->fetch(3)) {
            $rating_total += $rating;
        }
    }
    $data_content['rating_total'] = $rating_count;
    $data_content['rating_point'] = $rating_total;
    $data_content['rating_value'] = $rating_count > 0 ? round($rating_total / $rating_count) : 0;

    // Ảnh chính
    $homeimgfile = $data_content['homeimgfile'];
    if ($data_content['homeimgthumb'] == 1) {
        $data_content['homeimgthumb'] = NV_STATIC_URL . NV_FILES_DIR . '/' . $module_upload . '/' . $homeimgfile;
        $data_content['homeimgfile'] = NV_STATIC_URL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $homeimgfile;
    } elseif ($data_content['homeimgthumb'] == 2) {
        $data_content['homeimgthumb'] = $data_content['homeimgfile'] = NV_STATIC_URL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $homeimgfile;
    } elseif ($data_content['homeimgthumb'] == 3) {
        $data_content['homeimgthumb'] = $data_content['homeimgfile'] = $homeimgfile;
    } else {
        $data_content['homeimgthumb'] = $data_content['homeimgfile'] = NV_STATIC_URL . 'themes/' . $module_info['template'] . '/images/' . $module_file . '/no-image.jpg';
    }

    array_push($array_images, array(
        'thumb' => $data_content['homeimgthumb'],
        'file' => $data_content['homeimgfile']
    ));

    // Tu khoa
    $array_keyword = [];
    $key_words = [];
    $_query = $db->query('SELECT a1.keyword keyword, a2.alias alias FROM ' . $db_config['prefix'] . '_' . $module_data . '_tags_id_' . NV_LANG_DATA . ' a1 INNER JOIN ' . $db_config['prefix'] . '_' . $module_data . '_tags_' . NV_LANG_DATA . ' a2 ON a1.tid=a2.tid WHERE a1.id=' . $data_content['id']);
    while ($row = $_query->fetch()) {
        $array_keyword[] = $row;
        $key_words[] = $row['keyword'];
    }
    $key_words = !empty($key_words) ? implode(',', $key_words) : '';

    // metatag image facebook
    $meta_property['og:image'] = NV_MY_DOMAIN . $data_content['homeimgfile'];

    // Fetch Limit
    $db->sqlreset()
        ->select(' id, listcatid, ' . NV_LANG_DATA . '_title, ' . NV_LANG_DATA . '_alias, homeimgfile, homeimgthumb, addtime, publtime, product_code, product_number, product_price, price_config, money_unit, showprice, ' . NV_LANG_DATA . '_hometext,' . NV_LANG_DATA . '_gift_content, gift_from, gift_to')
        ->from($db_config['prefix'] . '_' . $module_data . '_rows')
        ->where('id!=' . $id . ' AND listcatid = ' . $data_content['listcatid'] . ' AND status=1')
        ->order('ID DESC')
        ->limit($pro_config['per_row'] * 2);
    $result = $db->query($db->sql());

    $data_others = [];
    while (list ($_id, $listcatid, $title, $alias, $homeimgfile, $homeimgthumb, $addtime, $publtime, $product_code, $product_number, $product_price, $price_config, $money_unit, $showprice, $hometext, $gift_content, $gift_from, $gift_to) = $result->fetch(3)) {
        if ($homeimgthumb == 1) {
            // image thumb
            $thumb = NV_STATIC_URL . NV_FILES_DIR . '/' . $module_upload . '/' . $homeimgfile;
        } elseif ($homeimgthumb == 2) {
            // image file
            $thumb = NV_STATIC_URL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $homeimgfile;
        } elseif ($homeimgthumb == 3) {
            // image url
            $thumb = $homeimgfile;
        } else {
            // no image
            $thumb = NV_STATIC_URL . 'themes/' . $module_info['template'] . '/images/' . $module_file . '/no-image.jpg';
        }

        $data_others[] = array(
            'id' => $_id,
            'listcatid' => $listcatid,
            'title' => $title,
            'alias' => $alias,
            'publtime' => $publtime,
            'homeimgthumb' => $thumb,
            'hometext' => $hometext,
            'addtime' => $addtime,
            'product_code' => $product_code,
            'product_number' => $product_number,
            'product_price' => $product_price,
            'price_config' => $price_config,
            'money_unit' => $money_unit,
            'showprice' => $showprice,
            'newday' => $global_array_shops_cat[$data_content['listcatid']]['newday'],
            'gift_content' => $gift_content,
            'gift_from' => $gift_from,
            'gift_to' => $gift_to,
            'link_pro' => NV_STATIC_URL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $global_array_shops_cat[$data_content['listcatid']]['alias'] . '/' . $alias . $global_config['rewrite_exturl'],
            'link_order' => NV_STATIC_URL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=setcart&amp;id=' . $_id
        );
    }

    $array_other_view = [];
    if (!empty($_SESSION[$module_data . '_proview'])) {
        $arrid = [];
        foreach ($_SESSION[$module_data . '_proview'] as $id_i => $data_i) {
            if ($id_i != $id) {
                $arrid[] = $id_i;
            }
        }
        $arrtempid = implode(',', $arrid);
        if (!empty($arrtempid)) {
            // Fetch Limit
            $db->sqlreset()
                ->select('id, listcatid, ' . NV_LANG_DATA . '_title, ' . NV_LANG_DATA . '_alias, homeimgfile, homeimgthumb, addtime, publtime, product_code, product_number, product_price, money_unit, showprice, ' . NV_LANG_DATA . '_hometext,' . NV_LANG_DATA . '_gift_content, gift_from, gift_to')
                ->from($db_config['prefix'] . '_' . $module_data . '_rows')
                ->where('id IN ( ' . $arrtempid . ') AND status=1')
                ->order('id DESC')
                ->limit($pro_config['per_row'] * 2);
            $result = $db->query($db->sql());
            while (list ($_id, $listcatid, $title, $alias, $homeimgfile, $homeimgthumb, $addtime, $publtime, $product_code, $product_number, $product_price, $money_unit, $showprice, $hometext, $gift_content, $gift_from, $gift_to) = $result->fetch(3)) {
                if ($homeimgthumb == 1) {
                    // image thumb
                    $thumb = NV_STATIC_URL . NV_FILES_DIR . '/' . $module_upload . '/' . $homeimgfile;
                } elseif ($homeimgthumb == 2) {
                    // image file
                    $thumb = NV_STATIC_URL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $homeimgfile;
                } elseif ($homeimgthumb == 3) {
                    // image url
                    $thumb = $homeimgfile;
                } else {
                    // no image
                    $thumb = NV_STATIC_URL . 'themes/' . $module_info['template'] . '/images/' . $module_file . '/no-image.jpg';
                }

                $array_other_view[] = array(
                    'id' => $_id,
                    'listcatid' => $listcatid,
                    'title' => $title,
                    'alias' => $alias,
                    'publtime' => $publtime,
                    'homeimgthumb' => $thumb,
                    'hometext' => $hometext,
                    'addtime' => $addtime,
                    'product_code' => $product_code,
                    'product_number' => $product_number,
                    'product_price' => $product_price,
                    'money_unit' => $money_unit,
                    'showprice' => $showprice,
                    'newday' => $global_array_shops_cat[$data_content['listcatid']]['newday'],
                    'gift_content' => $gift_content,
                    'gift_from' => $gift_from,
                    'gift_to' => $gift_to,
                    'link_pro' => NV_STATIC_URL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $global_array_shops_cat[$data_content['listcatid']]['alias'] . '/' . $alias . $global_config['rewrite_exturl'],
                    'link_order' => NV_STATIC_URL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=setcart&amp;id=' . $_id
                );
            }
        }
    }

    SetSessionProView($data_content['id'], $data_content[NV_LANG_DATA . '_title'], $data_content[NV_LANG_DATA . '_alias'], $data_content['addtime'], NV_STATIC_URL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $global_array_shops_cat[$catid]['alias'] . '/' . $data_content[NV_LANG_DATA . '_alias'] . '-' . $data_content['id'], $data_content['homeimgthumb']);

    // comment
    if (isset($site_mods['comment']) and isset($module_config[$module_name]['activecomm'])) {
        define('NV_COMM_ID', $data_content['id']); // ID bài viết hoặc
        define('NV_COMM_AREA', $module_info['funcs'][$op]['func_id']); // để đáp ứng comment ở bất cứ đâu không cứ là bài viết
        // check allow comemnt
        $allowed = $module_config[$module_name]['allowed_comm']; // tuy vào module để lấy cấu hình. Nếu là module news thì có cấu hình theo bài viết
        if ($allowed == '-1') {
            $allowed = $data_content['allowed_comm'];
        }
        define('NV_PER_PAGE_COMMENT', 5); // Số bản ghi hiển thị bình luận
        require_once NV_ROOTDIR . '/modules/comment/comment.php';
        $area = (defined('NV_COMM_AREA')) ? NV_COMM_AREA : 0;
        $checkss = md5($module_name . '-' . $area . '-' . NV_COMM_ID . '-' . $allowed . '-' . NV_CACHE_PREFIX);

        $content_comment = nv_comment_module($module_name, $checkss, $area, NV_COMM_ID, $allowed, 1);
    } else {
        $content_comment = '';
    }

    // hình ảnh sản phẩm
    if (!empty($data_content['otherimage'])) {
        $otherimage = explode('|', $data_content['otherimage']);
        foreach ($otherimage as $image) {
            $file = $thumb = '';
            if (file_exists(NV_ROOTDIR . '/' . NV_ASSETS_DIR . '/' . $module_upload . '/' . $image)) {
                $thumb = NV_STATIC_URL . NV_ASSETS_DIR . '/' . $module_upload . '/' . $image;
            }

            if (file_exists(NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $image)) {
                $file = NV_STATIC_URL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $image;
            }

            if (!empty($thumb) && !empty($file)) {
                array_push($array_images, array(
                    'thumb' => $thumb,
                    'file' => $file
                ));
            }
        }
    }

    $data_content['image'] = $array_images;
    unset($array_images, $data_content['homeimgfile'], $data_content['otherimage']);
    $data_content['full_link'] = NV_MY_DOMAIN . nv_url_rewrite(NV_STATIC_URL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $global_array_shops_cat[$data_content['listcatid']]['alias'] . '/' . $data_content[NV_LANG_DATA . '_alias'] . $global_config['rewrite_exturl'], true);

    $contents = nv_template_detail($data_content, $data_unit, $data_others, $array_other_view, $content_comment, $compare_id, $popup, $idtemplates, $array_keyword);
} else {
    $nv_redirect = NV_STATIC_URL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name;
    redict_link($lang_module['detail_no_permission'], $lang_module['redirect_to_back_shops'], $nv_redirect);
}

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents, $popup ? false : true);
include NV_ROOTDIR . '/includes/footer.php';
