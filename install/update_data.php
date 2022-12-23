<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2012 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 2-10-2010 20:59
 */

if (!defined('NV_IS_UPDATE'))
    die('Stop!!!');

$nv_update_config = array();

// Kieu nang cap 1: Update; 2: Upgrade
$nv_update_config['type'] = 1;

// ID goi cap nhat
$nv_update_config['packageID'] = 'NVUSHOPS4500';

// Cap nhat cho module nao, de trong neu la cap nhat NukeViet, ten thu muc module neu la cap nhat module
$nv_update_config['formodule'] = 'shops';

// Thong tin phien ban, tac gia, ho tro
$nv_update_config['release_date'] = 1658966400;
$nv_update_config['author'] = 'VINADES.,JSC (contact@vinades.vn)';
$nv_update_config['support_website'] = 'https://github.com/nukeviet/module-shops/tree/to-4.5.00';
$nv_update_config['to_version'] = '4.5.00';
$nv_update_config['allow_old_version'] = array('4.0.25');

// 0:Nang cap bang tay, 1:Nang cap tu dong, 2:Nang cap nua tu dong
$nv_update_config['update_auto_type'] = 1;

$nv_update_config['lang'] = array();
$nv_update_config['lang']['vi'] = array();

// Tiếng Việt
$nv_update_config['lang']['vi']['nv_up_f1'] = 'Thay đổi cấu trúc bảng dữ liệu';
$nv_update_config['lang']['vi']['nv_up_f2'] = 'Cập nhật cấu hình CSDL bản 4.5.00';
$nv_update_config['lang']['vi']['nv_up_finish'] = 'Đánh dấu phiên bản mới';

$nv_update_config['tasklist'] = array();
$nv_update_config['tasklist'][] = array(
    'r' => '4.5.00',
    'rq' => 1,
    'l' => 'nv_up_f1',
    'f' => 'nv_up_f1'
);
$nv_update_config['tasklist'][] = array(
    'r' => '4.5.00',
    'rq' => 1,
    'l' => 'nv_up_f2',
    'f' => 'nv_up_f2'
);
$nv_update_config['tasklist'][] = array(
    'r' => '4.5.00',
    'rq' => 1,
    'l' => 'nv_up_finish',
    'f' => 'nv_up_finish'
);

// Danh sach cac function
/*
Chuan hoa tra ve:
array(
'status' =>
'complete' =>
'next' =>
'link' =>
'lang' =>
'message' =>
);
status: Trang thai tien trinh dang chay
- 0: That bai
- 1: Thanh cong
complete: Trang thai hoan thanh tat ca tien trinh
- 0: Chua hoan thanh tien trinh nay
- 1: Da hoan thanh tien trinh nay
next:
- 0: Tiep tuc ham nay voi "link"
- 1: Chuyen sang ham tiep theo
link:
- NO
- Url to next loading
lang:
- ALL: Tat ca ngon ngu
- NO: Khong co ngon ngu loi
- LangKey: Ngon ngu bi loi vi,en,fr ...
message:
- Any message
Duoc ho tro boi bien $nv_update_baseurl de load lai nhieu lan mot function
Kieu cap nhat module duoc ho tro boi bien $old_module_version
*/

$array_modlang_update = array();
$array_modtable_update = array();

// Lay danh sach ngon ngu
$result = $db->query("SELECT lang FROM " . $db_config['prefix'] . "_setup_language WHERE setup=1");
while (list($_tmp) = $result->fetch(PDO::FETCH_NUM)) {
    $array_modlang_update[$_tmp] = array("lang" => $_tmp, "mod" => array());

    // Get all module
    $result1 = $db->query("SELECT title, module_data FROM " . $db_config['prefix'] . "_" . $_tmp . "_modules WHERE module_file=" . $db->quote($nv_update_config['formodule']));
    while (list($_modt, $_modd) = $result1->fetch(PDO::FETCH_NUM)) {
        $array_modlang_update[$_tmp]['mod'][] = array("module_title" => $_modt, "module_data" => $_modd);
        $array_modtable_update[] = $db_config['prefix'] . "_" . $_tmp . "_" . $_modd;
    }
}

/**
 * nv_up_f1()
 *
 * @return
 *
 */
function nv_up_f1()
{
    global $nv_update_baseurl, $db, $db_config, $nv_Cache, $array_modlang_update;
    $return = array(
        'status' => 1,
        'complete' => 1,
        'next' => 1,
        'link' => 'NO',
        'lang' => 'NO',
        'message' => ''
    );
    foreach ($array_modlang_update as $lang => $array_mod) {
        foreach ($array_mod['mod'] as $module_info) {
            $table_prefix = $db_config['prefix'] . "_" . $module_info['module_data'];
            try {
                $db->query("ALTER TABLE " . $table_prefix . "_template ADD weight mediumint(8) unsigned NOT NULL DEFAULT '1' AFTER `alias`");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query("ALTER TABLE " . $table_prefix . "_group CHANGE `viewgroup` `viewgroup` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'viewgrid'");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query("ALTER TABLE " . $table_prefix . "_warehouse_logs CHANGE `price` `price` DOUBLE NOT NULL DEFAULT '0'");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query("ALTER TABLE " . $table_prefix . "_warehouse_logs_group CHANGE `price` `price` DOUBLE NOT NULL DEFAULT '0'");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query("ALTER TABLE " . $table_prefix . "_rows CHANGE `product_price` `product_price` DOUBLE NOT NULL DEFAULT '0', CHANGE `product_weight` `product_weight` DOUBLE NOT NULL DEFAULT '0'");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query("ALTER TABLE " . $table_prefix . "_rows ADD saleprice double NOT NULL DEFAULT '0' AFTER `price_config`, ADD " . $lang . "_tag_title VARCHAR(255) NOT NULL DEFAULT '', ADD " . $lang . "_tag_description mediumtext NOT NULL DEFAULT ''");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query("ALTER TABLE " . $table_prefix . "_catalogs ADD " . $lang . "_tag_description mediumtext NOT NULL DEFAULT ''");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query("ALTER TABLE " . $table_prefix . "_block_cat
                    ADD " . $lang . "_bodytext TEXT NULL DEFAULT NULL AFTER " . $lang . "_description,
                    ADD " . $lang . "_tag_title VARCHAR(255) NOT NULL DEFAULT '',
                    ADD " . $lang . "_tag_description mediumtext NOT NULL");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query("ALTER TABLE " . $table_prefix . "_orders_id CHANGE `price` `price` DOUBLE NOT NULL DEFAULT '0',
                    ADD listgroupid VARCHAR(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' AFTER `order_id`");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query("ALTER TABLE " . $table_prefix . "_orders_shipping CHANGE `id` `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
                    CHANGE `weight` `weight` double NOT NULL DEFAULT '0', CHANGE `ship_price` `ship_price` double NOT NULL DEFAULT '0'");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query("ALTER TABLE " . $table_prefix . "_money_" . $lang . " CHANGE `exchange` `exchange` double NOT NULL default '0', ADD symbol varchar(3) NOT NULL default '' AFTER `currency`");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query("UPDATE " . $table_prefix . "_money_" . $lang . " SET `symbol` = '$' WHERE `nv4_shops_money_vi`.`id` = 840;");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query("UPDATE " . $table_prefix . "_money_" . $lang . " SET `symbol` = 'đ' WHERE `nv4_shops_money_vi`.`id` = 704;");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query("DROP TABLE " . $table_prefix . "_payment");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query("ALTER TABLE " . $table_prefix . "_weight_" . $lang . " CHANGE `exchange` `exchange` double NOT NULL default '0'");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query("ALTER TABLE " . $table_prefix . "_tags_" . $lang . " ADD bodytext text NULL DEFAULT '' AFTER description");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query("ALTER TABLE " . $table_prefix . "_coupons CHANGE `discount` `discount` double NOT NULL DEFAULT '0', CHANGE `total_amount` `total_amount` double NOT NULL DEFAULT '0'");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query("ALTER TABLE " . $table_prefix . "_coupons_history CHANGE `amount` `amount` double NOT NULL DEFAULT '0'");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query("ALTER TABLE " . $table_prefix . "_carrier_config_weight CHANGE `weight` `weight` double unsigned NOT NULL, CHANGE `carrier_price` `carrier_price` double NOT NULL");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query("UPDATE " . $table_prefix . "_catalogs SET viewcat = 'viewlist' WHERE viewcat = 'viewcat_page_list'");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query("UPDATE " . $table_prefix . "_catalogs SET viewcat = 'viewgrid' WHERE viewcat = 'viewcat_page_gird'");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query("UPDATE " . $table_prefix . "_group SET viewgroup = 'viewlist' WHERE viewcat = 'viewcat_page_list'");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query("UPDATE " . $table_prefix . "_group SET viewgroup = 'viewgrid' WHERE viewcat = 'viewcat_page_gird'");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
        }
    }
    return $return;
}

/**
 * nv_up_f2()
 *
 * @return
 *
 */
function nv_up_f2()
{
    global $nv_update_baseurl, $db, $db_config, $nv_Cache, $array_modlang_update;
    $return = array(
        'status' => 1,
        'complete' => 1,
        'next' => 1,
        'link' => 'NO',
        'lang' => 'NO',
        'message' => ''
    );
    foreach ($array_modlang_update as $lang => $array_mod) {
        foreach ($array_mod['mod'] as $module_info) {
            try {
                $db->query("INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES
                    ('" . $lang . "', '" . $module_info['module_data'] . "', 'home_data', 'all'),
                    ('" . $lang . "', '" . $module_info['module_data'] . "', 'money_to_point', '0'),
                    ('" . $lang . "', '" . $module_info['module_data'] . "', 'saleprice_active', '0'),
                    ('" . $lang . "', '" . $module_info['module_data'] . "', 'sortdefault', '0'),
                    ('" . $lang . "', '" . $module_info['module_data'] . "', 'allowattachcomm', '0'),
                    ('" . $lang . "', '" . $module_info['module_data'] . "', 'alloweditorcomm', '0'),
                    ('" . $lang . "', '" . $module_info['module_data'] . "', 'captcha_area_comm', '1'),
                    ('" . $lang . "', '" . $module_info['module_data'] . "', 'captcha_type_comm', 'captcha'),
                    ('" . $lang . "', '" . $module_info['module_data'] . "', 'captcha_type', 'captcha')
                ");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query("UPDATE " . NV_CONFIG_GLOBALTABLE . " SET `config_value` = 'viewgrid' WHERE lang = '" . $lang . "' AND module = '" . $module_info['module_data'] . "' AND config_name = 'home_view'");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query("UPDATE " . NV_CONFIG_GLOBALTABLE . " SET `config_value` = 0 WHERE lang = '" . $lang . "' AND module = '" . $module_info['module_data'] . "' AND config_name = 'active_warehouse'");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
        }
    }
    return $return;
}


/**
 * nv_up_finish()
 *
 * @return
 *
 */
function nv_up_finish()
{
    global $nv_update_baseurl, $db, $db_config, $nv_Cache, $nv_update_config;

    $return = array(
        'status' => 1,
        'complete' => 1,
        'next' => 1,
        'link' => 'NO',
        'lang' => 'NO',
        'message' => ''
    );

    @nv_deletefile(NV_ROOTDIR . '/modules/shops/admin/.htaccess');
    @nv_deletefile(NV_ROOTDIR . '/modules/shops/admin/actpay.php');
    @nv_deletefile(NV_ROOTDIR . '/modules/shops/admin/changepay.php');
    @nv_deletefile(NV_ROOTDIR . '/modules/shops/admin/payport.php');
    @nv_deletefile(NV_ROOTDIR . '/modules/shops/blocks/.htaccess');
    @nv_deletefile(NV_ROOTDIR . '/modules/shops/funcs/.htaccess');
    @nv_deletefile(NV_ROOTDIR . '/modules/shops/funcs/complete.php');
    @nv_deletefile(NV_ROOTDIR . '/modules/shops/language/.htaccess');
    @nv_deletefile(NV_ROOTDIR . '/modules/shops/payment/.htaccess');
    @nv_deletefile(NV_ROOTDIR . '/modules/shops/payment/baokim.checkorders.php');
    @nv_deletefile(NV_ROOTDIR . '/modules/shops/payment/baokim.checkout_url.php');
    @nv_deletefile(NV_ROOTDIR . '/modules/shops/payment/baokim.class.php');
    @nv_deletefile(NV_ROOTDIR . '/modules/shops/payment/baokim.complete.php');
    @nv_deletefile(NV_ROOTDIR . '/modules/shops/payment/baokim.config.ini');
    @nv_deletefile(NV_ROOTDIR . '/modules/shops/payment/index.html');
    @nv_deletefile(NV_ROOTDIR . '/modules/shops/payment/onepaydomestic.checkorders.php');
    @nv_deletefile(NV_ROOTDIR . '/modules/shops/payment/onepaydomestic.checkout_url.php');
    @nv_deletefile(NV_ROOTDIR . '/modules/shops/payment/onepaydomestic.complete.php');
    @nv_deletefile(NV_ROOTDIR . '/modules/shops/payment/onepaydomestic.config.ini');
    @nv_deletefile(NV_ROOTDIR . '/modules/shops/payment/paypal_express_checkout.checkorders.php');
    @nv_deletefile(NV_ROOTDIR . '/modules/shops/payment/paypal_express_checkout.checkout_url.php');
    @nv_deletefile(NV_ROOTDIR . '/modules/shops/payment/paypal_express_checkout.complete.php');
    @nv_deletefile(NV_ROOTDIR . '/modules/shops/payment/paypal_express_checkout.config.ini');
    @nv_deletefile(NV_ROOTDIR . '/modules/shops/payment', true);
    @nv_deletefile(NV_ROOTDIR . '/themes/admin_default/modules/shops/.htaccess');
    @nv_deletefile(NV_ROOTDIR . '/themes/admin_default/modules/shops/payport.tpl');
    @nv_deletefile(NV_ROOTDIR . '/themes/default/images/shops/bl.png');
    @nv_deletefile(NV_ROOTDIR . '/themes/default/images/shops/br.png');
    @nv_deletefile(NV_ROOTDIR . '/themes/default/images/shops/pro_cat_header.png');
    @nv_deletefile(NV_ROOTDIR . '/themes/default/js/responsiveCarousel.min.js');
    @nv_deletefile(NV_ROOTDIR . '/themes/default/modules/shops/.htaccess');
    @nv_deletefile(NV_ROOTDIR . '/themes/default/modules/shops/main_procate.tpl');
    @nv_deletefile(NV_ROOTDIR . '/themes/default/modules/shops/main_product.tpl');
    @nv_deletefile(NV_ROOTDIR . '/themes/default/modules/shops/othersimg.tpl');
    @nv_deletefile(NV_ROOTDIR . '/themes/default/modules/shops/view_gird.tpl');
    @nv_deletefile(NV_ROOTDIR . '/themes/default/modules/shops/view_list.tpl');

    try {
        $num = $db->query("SELECT COUNT(*) FROM " . $db_config['prefix'] . "_setup_extensions WHERE basename='" . $nv_update_config['formodule'] . "' AND type='module'")->fetchColumn();
        $version = $nv_update_config['to_version'] . " " . $nv_update_config['release_date'];

        if (!$num) {
            $db->query("INSERT INTO " . $db_config['prefix'] . "_setup_extensions (
                id, type, title, is_sys, is_virtual, basename, table_prefix, version, addtime, author, note
            ) VALUES (
                31, 'module', 'faq', 0, 1, 'faq', 'faq', '" . $nv_update_config['to_version'] . " " . $nv_update_config['release_date'] . "', " . NV_CURRENTTIME . ", 'VINADES.,JSC (contact@vinades.vn)',
                'Module quản lý các câu hỏi thường gặp'
            )");
        } else {
            $db->query("UPDATE " . $db_config['prefix'] . "_setup_extensions SET
                id=28,
                version='" . $version . "',
                author='VINADES.,JSC (contact@vinades.vn)'
            WHERE basename='" . $nv_update_config['formodule'] . "' AND type='module'");
        }
    } catch (PDOException $e) {
        trigger_error($e->getMessage());
    }

    $nv_Cache->delAll(true);
    return $return;
}
