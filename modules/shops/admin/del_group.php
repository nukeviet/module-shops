<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2017 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 04/18/2017 09:47
 */

if (! defined('NV_IS_FILE_ADMIN')) {
    die('Stop!!!');
}

$groupid = $nv_Request->get_int('groupid', 'post, get', 0);
$contents = "NO_" . $groupid;

list($groupid, $parentid, $title) = $db->query("SELECT groupid, parentid, " . NV_LANG_DATA . "_title FROM " . $db_config['prefix'] . "_" . $module_data . "_group WHERE groupid=" . $groupid)->fetch(3);

if ($groupid > 0) {
    $delallcheckss = $nv_Request->get_string('delallcheckss', 'post', "");

    $check_parentid = $db->query("SELECT count(*) FROM " . $db_config['prefix'] . "_" . $module_data . "_group WHERE parentid=" . $groupid)->fetchColumn();

    if (intval($check_parentid) > 0) {
        $contents = "ERR_CAT_" . sprintf($lang_module['delgroup_msg_group'], $check_parentid);
    } else {
        $check_rows = $db->query("SELECT count(*) FROM " . $db_config['prefix'] . "_" . $module_data . "_group_items WHERE group_id='" . $groupid . "'")->fetchColumn();

        if (intval($check_rows) > 0) {
            if ($delallcheckss == md5($groupid . session_id() . $global_config['sitekey'])) {
                $delgroupandrows = $nv_Request->get_string('delgroupandrows', 'post', "");
                $movegroup = $nv_Request->get_string('movegroup', 'post', "");
                $groupidnews = $nv_Request->get_int('groupidnews', 'post', 0);

                if (empty($delgroupandrows) and empty($movegroup)) {
                    $sql = "SELECT groupid, " . NV_LANG_DATA . "_title, lev FROM " . $db_config['prefix'] . "_" . $module_data . "_group WHERE groupid!='" . $groupid . "' ORDER BY sort ASC";
                    $result = $db->query($sql);
                    $array_group_list = array();
                    $array_group_list[0] = $lang_module['delgroup_no_group'];
                    while (list($groupid_i, $title_i, $lev_i) = $result->fetch(3)) {
                        $xtitle_i = "";
                        if ($lev_i > 0) {
                            $xtitle_i .= "&nbsp;&nbsp;&nbsp;|";
                            for ($i = 1; $i <= $lev_i; $i++) {
                                $xtitle_i .= "---";
                            }
                            $xtitle_i .= ">&nbsp;";
                        }
                        $xtitle_i .= $title_i;
                        $array_group_list[$groupid_i] = $xtitle_i;
                    }

                    $xtpl = new XTemplate("group_delete.tpl", NV_ROOTDIR . "/themes/" . $global_config['module_theme'] . "/modules/" . $module_file);
                    $xtpl->assign('LANG', $lang_module);
                    $xtpl->assign('GLANG', $lang_global);
                    $xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
                    $xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
                    $xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
                    $xtpl->assign('MODULE_NAME', $module_name);
                    $xtpl->assign('OP', $op);

                    $xtpl->assign('GROUPID', $groupid);
                    $xtpl->assign('DELALLCHECKSS', $delallcheckss);
                    $xtpl->assign('INFO', sprintf($lang_module['delgroup_msg_rows_select'], $title, $check_rows));

                    while (list($groupid_i, $title_i) = each($array_group_list)) {
                        $xtpl->assign('GROUP_ID', $groupid_i);
                        $xtpl->assign('GROUP_TITLE', $title_i);
                        $xtpl->parse('main.grouploop');
                    }

                    $xtpl->parse('main');
                    $contents = $xtpl->text('main');
                } elseif (! empty($delgroupandrows)) {
                    $result = $db->query("SELECT pro_id FROM " . $db_config['prefix'] . "_" . $module_data . "_group_items WHERE group_id='" . $groupid . "'");
                    while ($row = $result->fetch()) {
                        // Xoa san pham
                        nv_del_content_module($row['pro_id']);
                    }

                    // Xoa nhom
                    nv_del_group($groupid);

                    // Xoa cua loai san pham
                    $db->query("DELETE FROM " . $db_config['prefix'] . "_" . $module_data . "_group_cateid WHERE groupid=" . $groupid);

                    nv_fix_group_order();
                    $nv_Cache->delMod($module_name);

                    nv_redirect_location(NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=group&parentid=" . $parentid);
                } elseif (! empty($movegroup) and $groupidnews > 0 and $groupidnews != $groupid) {
                    $groupidnews = $db->query("SELECT groupid FROM " . $db_config['prefix'] . "_" . $module_data . "_group WHERE groupid=" . $groupidnews)->fetchColumn();

                    if ($groupidnews > 0) {
                        $result = $db->query("SELECT pro_id FROM " . $db_config['prefix'] . "_" . $module_data . "_group_items WHERE group_id='" . $groupid . "'");
                        while ($row = $result->fetch()) {
                            $count = $db->query('SELECT COUNT(*) FROM ' . $db_config['prefix'] . '_' . $module_data . '_group_items WHERE group_id=' . $groupidnews . ' AND pro_id=' . $row['pro_id'])->fetchColumn();
                            if ($count == 0) {
                                $stmt = $db->prepare("UPDATE " . $db_config['prefix'] . "_" . $module_data . "_group_items SET group_id=:group_id WHERE pro_id=" . $row['pro_id'] . ' AND group_id=' . $groupid);
                                $stmt->bindParam(':group_id', $groupidnews, PDO::PARAM_STR);
                                $stmt->execute();
                            } else {
                                $db->query("DELETE FROM " . $db_config['prefix'] . "_" . $module_data . "_group_items WHERE pro_id=" . $row['pro_id'] . ' AND group_id=' . $groupid);
                            }
                        }

                        nv_del_group($groupid);

                        nv_fix_group_order();
                        nv_fix_group_count([$groupidnews]);
                        $nv_Cache->delMod($module_name);

                        nv_redirect_location(NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=group&parentid=" . $parentid);
                    }
                }
            } else {
                $contents = "ERR_ROWS_" . $groupid . "_" . md5($groupid . session_id() . $global_config['sitekey']) . "_" . sprintf($lang_module['delgroup_msg_rows'], $check_rows);
            }
        }
    }

    if ($contents == "NO_" . $groupid) {
        $sql = "DELETE FROM " . $db_config['prefix'] . "_" . $module_data . "_group_items WHERE group_id=" . $groupid;
        $db->exec($sql);

        $sql = "DELETE FROM " . $db_config['prefix'] . "_" . $module_data . "_group WHERE groupid=" . $groupid;
        $db->query("DELETE FROM " . $db_config['prefix'] . "_" . $module_data . "_group_cateid WHERE groupid=" . $groupid);
        if ($db->exec($sql)) {
            nv_fix_group_order();
            $contents = "OK_" . $parentid;
            nv_insert_logs(NV_LANG_DATA, $module_name, $lang_module['delgroupandrows'], $title, $admin_info['userid']);
        }

        $nv_Cache->delMod($module_name);
    }
}

if (defined('NV_IS_AJAX')) {
    include NV_ROOTDIR . '/includes/header.php';
    echo $contents;
    include NV_ROOTDIR . '/includes/footer.php';
} else {
    nv_redirect_location(NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=group");
}
