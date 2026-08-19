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

$module_version = [
    'name' => 'Shops',
    'modfuncs' => 'main,viewcat,detail,search,cart,order,payment,complete,history,group,compare,wishlist,tag,point,shippingajax,download, blockcat,ajax',
    'is_sysmod' => 0,
    'virtual' => 1,
    'version' => '4.6.00',
    'date' => 'Wednesday, August 19, 2026 at 8:38:36 AM UTC+07:00',
    'author' => 'VINADES <contact@vinades.vn>',
    'note' => '',
    'uploads_dir' => [
        $module_upload,
        $module_upload . '/temp_pic',
        $module_upload . '/' . date('Y_m'),
        $module_upload . '/files'
    ],
    'files_dir' => [
        $module_upload . '/files_tpl'
    ]
];
