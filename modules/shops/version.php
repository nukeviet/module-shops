<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2017 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 04/18/2017 09:47
 */
if (!defined('NV_ADMIN') or !defined('NV_MAINFILE')) {
    die('Stop!!!');
}

$module_version = array(
    'name' => 'Shops', // Tieu de module
    'modfuncs' => 'main,viewcat,detail,search,cart,order,payment,complete,history,group,search_result,compare,wishlist,tag,point,shippingajax,download, blockcat,ajax',
    'is_sysmod' => 0, // 1:0 => Co phai la module he thong hay khong
    'virtual' => 1, // 1:0 => Co cho phep ao hao module hay khong
    'version' => '4.5.01', // Module Shops 4 Release Candidate 1
    'date' => 'Fri, 14 January 2022 00:00:00 GMT', // Ngay phat hanh phien ban
    'author' => 'VINADES <contact@vinades.vn>', // Tac gia
    'note' => '', // Ghi chu
    'uploads_dir' => array(
        $module_upload,
        $module_upload . '/temp_pic',
        $module_upload . '/' . date('Y_m'),
        $module_upload . '/files'
    ),
    'files_dir' => array(
        $module_upload . '/files_tpl'
    )
);
