<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2017 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 04/18/2017 09:47
 */

if (!defined('NV_IS_FILE_ADMIN')) {
    die('Stop!!!');
}

$title = $nv_Request->get_title('title', 'post', '');
$alias = change_alias($title);
if ($module_config[$module_name]['alias_lower']) {
    $alias = strtolower($alias);
}

$id = $nv_Request->get_int('id', 'post', 0);
$mod = $nv_Request->get_string('mod', 'post', '');

if ($mod == 'content') {
    $stmt = $db->prepare('SELECT COUNT(*) FROM ' . $db_config['prefix'] . '_' . $module_data . '_rows WHERE id!=' . $id . ' AND ' . NV_LANG_DATA . '_alias= :alias');
    $stmt->bindParam(':alias', $alias, PDO::PARAM_STR);
    $stmt->execute();
    $nb = $stmt->fetchColumn();
    if (!empty($nb)) {
        if ($id) {
            $alias .= '-' . $id;
        } else {
            $nb = $db->query('SELECT MAX(id) FROM ' . $db_config['prefix'] . '_' . $module_data . '_rows')->fetchColumn();
            $alias .= '-' . (intval($nb) + 1);
        }
    }
}

if ($mod == 'cat') {
    list($parentid) = $db->query('SELECT parentid FROM ' . $db_config['prefix'] . '_' . $module_data . '_catalogs WHERE catid = ' . $id)->fetch(3);

    $stmt = $db->prepare('SELECT count(*) FROM ' . $db_config['prefix'] . '_' . $module_data . '_catalogs WHERE catid!=' . $id . ' AND ' . NV_LANG_DATA . '_alias= :alias');
    $stmt->bindParam(':alias', $alias, PDO::PARAM_STR);
    $stmt->execute();
    $check_alias = $stmt->fetchColumn();

    if ($check_alias and $parentid > 0) {
        $parentid_alias = $db->query('SELECT ' . NV_LANG_DATA . '_alias FROM ' . $db_config['prefix'] . '_' . $module_data . '_catalogs WHERE catid=' . $parentid)->fetchColumn();
        $alias = $parentid_alias . '-' . $alias;

        // Tiếp tục kiểm tra có trùng alias của loại sản phẩm bên trong hay không
        $stmt = $db->prepare('SELECT count(*) FROM ' . $db_config['prefix'] . '_' . $module_data . '_catalogs WHERE catid!=' . $id . ' AND ' . NV_LANG_DATA . '_alias= :alias');
        $stmt->bindParam(':alias', $alias, PDO::PARAM_STR);
        $stmt->execute();
        $check_alias = $stmt->fetchColumn();
    }
    
    // Vẫn tiếp tục bị trùng alias thì thêm number vào phía sau
    if ($check_alias) {
        $rows_id = $db->query('SELECT MAX(catid) FROM ' . $db_config['prefix'] . '_' . $module_data . '_catalogs')->fetchColumn();
        $rows_id = intval($rows_id) + 1;
        $alias .= '-' . $rows_id;
    }
}

include NV_ROOTDIR . '/includes/header.php';
echo $alias;
include NV_ROOTDIR . '/includes/footer.php';
