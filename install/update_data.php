<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2012 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 2-10-2010 20:59
 */

if (!defined('NV_IS_UPDATE')) {
    die('Stop!!!');
}

$nv_update_config = array();

// Kieu nang cap 1: Update; 2: Upgrade
$nv_update_config['type'] = 1;

// ID goi cap nhat
$nv_update_config['packageID'] = 'NVUSHOPS4503';

// Cap nhat cho module nao, de trong neu la cap nhat NukeViet, ten thu muc module neu la cap nhat module
$nv_update_config['formodule'] = 'shops';

// Thong tin phien ban, tac gia, ho tro
$nv_update_config['id'] = 31;
$nv_update_config['author'] = 'VINADES.,JSC <contact@vinades.vn>';
$nv_update_config['note'] = '';
$nv_update_config['release_date'] = 1681442454;
$nv_update_config['support_website'] = 'https://github.com/nukeviet/module-shops/tree/to-4.5.03';
$nv_update_config['to_version'] = '4.5.02';
$nv_update_config['allow_old_version'] = array('4.3.00', '4.3.01', '4.4.00', '4.5.00', '4.5.01', '4.5.02');

// 0:Nang cap bang tay, 1:Nang cap tu dong, 2:Nang cap nua tu dong
$nv_update_config['update_auto_type'] = 1;

$nv_update_config['lang'] = array();
$nv_update_config['lang']['vi'] = array();

// Tiếng Việt
$nv_update_config['lang']['vi']['nv_up_f1'] = 'Cập nhật cấu hình CSDL bản 4.3.01';
$nv_update_config['lang']['vi']['nv_up_f2'] = 'Cập nhật cấu hình CSDL bản 4.4.00';
$nv_update_config['lang']['vi']['nv_up_f3'] = 'Cập nhật cấu hình CSDL bản 4.5.02';
$nv_update_config['lang']['vi']['nv_up_finish'] = 'Đánh dấu phiên bản mới';

$nv_update_config['tasklist'] = array();
$nv_update_config['tasklist'][] = array(
    'r' => '4.3.01',
    'rq' => 1,
    'l' => 'nv_up_f1',
    'f' => 'nv_up_f1'
);
$nv_update_config['tasklist'][] = array(
    'r' => '4.4.00',
    'rq' => 1,
    'l' => 'nv_up_f2',
    'f' => 'nv_up_f2'
);
$nv_update_config['tasklist'][] = array(
    'r' => '4.5.02',
    'rq' => 1,
    'l' => 'nv_up_f3',
    'f' => 'nv_up_f3'
);
$nv_update_config['tasklist'][] = array(
    'r' => '4.5.03',
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
            try {
                $db->query("INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (
                    lang, module, config_name, config_value
                ) VALUES 
                    ('" . $lang . "', '" . $module_info['module_title'] . "', 'home_data', 'all')
                ");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }

            try {
                $db->query("UPDATE " . NV_CONFIG_GLOBALTABLE . " SET config_value='viewgrid'
                WHERE lang='" . $lang . "' AND module = '" . $module_info['module_title'] . "' AND config_name = 'home_view'");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }

            try {
                $db->query("DROP TABLE " . $db_config['prefix'] . "_" . $module_info['module_data'] . "_payment ");
            } catch(PDOException $e) {
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
                $db->query(
                    "ALTER TABLE " . $db_config['prefix'] . "_" . $module_info['module_data'] . "_block_cat
                    ADD COLUMN " . $lang. "_bodytext TEXT NOT NULL AFTER " . $lang. "_keywords,
                    ADD COLUMN " . $lang. "_tag_title TEXT NOT NULL AFTER " . $lang. "_bodytext,
                    ADD COLUMN " . $lang. "_tag_description TEXT NOT NULL AFTER " . $lang. "_tag_title "
                );
            } catch(PDOException $e) {
                trigger_error($e->getMessage());
            }

            try {
                $db->query(
                    "ALTER TABLE " . $db_config['prefix'] . "_" . $module_info['module_data']. "_tags_".$lang
                    ." ADD bodytext TEXT NOT NULL AFTER description "
                );
            } catch(PDOException $e) {
                trigger_error($e->getMessage());
            }

            try {
                $db->query("INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (
                    lang, module, config_name, config_value
                ) VALUES 
                    ('" . $lang . "', '" . $module_info['module_title'] . "', 'saleprice_active', '0')
                ");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }

            try {
                $db->query(
                    "ALTER TABLE " . $db_config['prefix'] . "_" . $module_info['module_data']. "_rows
                        ADD saleprice DOUBLE UNSIGNED NOT NULL DEFAULT '0' AFTER price_config "
                );
            } catch(PDOException $e) {
                trigger_error($e->getMessage());
            }

            try {
                $db->query(
                    "ALTER TABLE " . $db_config['prefix'] . "_" . $module_info['module_data'] . "_catalogs
                    CHANGE form form VARCHAR(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' "
                );
            } catch(PDOException $e) {
                trigger_error($e->getMessage());
            }
        
            try {
                $db->query(
                    "ALTER TABLE " . $db_config['prefix'] . "_" . $module_info['module_data'] . "_template
                    ADD weight MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '1' AFTER alias "
                );
            } catch(PDOException $e) {
                trigger_error($e->getMessage());
            }
        }
    }
    return $return;
}

/**
 * nv_up_f3()
 *
 * @return
 *
 */
function nv_up_f3()
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
                $db->query("INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (
                    lang, module, config_name, config_value
                ) VALUES 
                    ('" . $lang . "', '" . $module_info['module_title'] . "', 'captcha_area_comm', '1'),
                    ('" . $lang . "', '" . $module_info['module_title'] . "', 'captcha_type_comm', 'captcha'),
                    ('" . $lang . "', '" . $module_info['module_title'] . "', 'captcha_type', 'captcha')
                ");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }

            try {
                $db->query(
                    "ALTER TABLE " . $db_config['prefix'] . "_" . $module_info['module_data'] . "_money_". $lang
                    ." CHANGE exchange exchange double NOT NULL DEFAULT '0' "
                );
            } catch(PDOException $e) {
                trigger_error($e->getMessage());
            }

            try {
                $db->query(
                    "ALTER TABLE " . $db_config['prefix'] . "_" . $module_info['module_data'] . "_weight_". $lang
                    ." CHANGE exchange exchange double NOT NULL DEFAULT '0' "
                );
            } catch(PDOException $e) {
                trigger_error($e->getMessage());
            }

            try {
                $db->query(
                    "ALTER TABLE " . $db_config['prefix'] . "_" . $module_info['module_data'] . "_block_cat
                    CHANGE adddefault adddefault tinyint(1) NOT NULL DEFAULT '0' "
                );
            } catch(PDOException $e) {
                trigger_error($e->getMessage());
            }
        
            try {
                $db->query(
                    "ALTER TABLE " . $db_config['prefix'] . "_" . $module_info['module_data'] . "_carrier_config_weight
                    CHANGE weight weight double UNSIGNED NOT NULL "
                );
            } catch(PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query(
                    "ALTER TABLE " . $db_config['prefix'] . "_" . $module_info['module_data'] . "_carrier_config_weight
                    CHANGE carrier_price carrier_price double NOT NULL "
                );
            } catch(PDOException $e) {
                trigger_error($e->getMessage());
            }
        
            try {
                $db->query(
                    "ALTER TABLE " . $db_config['prefix'] . "_" . $module_info['module_data'] . "_coupons
                    CHANGE discount discount double NOT NULL DEFAULT '0' "
                );
            } catch(PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query(
                    "ALTER TABLE " . $db_config['prefix'] . "_" . $module_info['module_data'] . "_coupons
                    CHANGE total_amount total_amount double NOT NULL DEFAULT '0' "
                );
            } catch(PDOException $e) {
                trigger_error($e->getMessage());
            }
        
            try {
                $db->query(
                    "ALTER TABLE " . $db_config['prefix'] . "_" . $module_info['module_data'] . "_coupons_history
                    CHANGE amount amount double NOT NULL DEFAULT '0' "
                );
            } catch(PDOException $e) {
                trigger_error($e->getMessage());
            }
        
            try {
                $db->query(
                    "ALTER TABLE " . $db_config['prefix'] . "_" . $module_info['module_data'] . "_group
                    CHANGE viewgroup viewgroup varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'viewgrid' "
                );
            } catch(PDOException $e) {
                trigger_error($e->getMessage());
            }
        
            try {
                $db->query(
                    "ALTER TABLE " . $db_config['prefix'] . "_" . $module_info['module_data'] . "_orders_id
                    CHANGE price price double NOT NULL DEFAULT '0' "
                );
            } catch(PDOException $e) {
                trigger_error($e->getMessage());
            }
        
            try {
                $db->query(
                    "ALTER TABLE " . $db_config['prefix'] . "_" . $module_info['module_data'] . "_orders_shipping
                    CHANGE weight weight double NOT NULL DEFAULT '0' "
                );
            } catch(PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query(
                    "ALTER TABLE " . $db_config['prefix'] . "_" . $module_info['module_data'] . "_orders_shipping
                    CHANGE ship_price ship_price double NOT NULL DEFAULT '0' "
                );
            } catch(PDOException $e) {
                trigger_error($e->getMessage());
            }
        
            try {
                $db->query(
                    "ALTER TABLE " . $db_config['prefix'] . "_" . $module_info['module_data'] . "_rows
                    CHANGE product_price product_price double NOT NULL DEFAULT '0' "
                );
            } catch(PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query(
                    "ALTER TABLE " . $db_config['prefix'] . "_" . $module_info['module_data'] . "_rows
                    CHANGE product_weight product_weight double NOT NULL DEFAULT '0' "
                );
            } catch(PDOException $e) {
                trigger_error($e->getMessage());
            }
        
            try {
                $db->query(
                    "ALTER TABLE " . $db_config['prefix'] . "_" . $module_info['module_data'] . "_transaction
                    CHANGE payment_amount payment_amount double NOT NULL DEFAULT '0' "
                );
            } catch(PDOException $e) {
                trigger_error($e->getMessage());
            }
        
            try {
                $db->query(
                    "ALTER TABLE " . $db_config['prefix'] . "_" . $module_info['module_data'] . "_warehouse_logs
                    CHANGE price price double NOT NULL DEFAULT '0' "
                );
            } catch(PDOException $e) {
                trigger_error($e->getMessage());
            }
        
            try {
                $db->query(
                    "ALTER TABLE " . $db_config['prefix'] . "_" . $module_info['module_data'] . "_warehouse_logs_group
                    CHANGE price price double NOT NULL DEFAULT '0' "
                );
            } catch(PDOException $e) {
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
                ". $nv_update_config['id'] .", 'module', '" . $nv_update_config['formodule'] . "', 0, 1,
                '" . $nv_update_config['formodule'] . "', '" . $nv_update_config['formodule'] . "', '" . $version . "', " . NV_CURRENTTIME . ", 
                '". $nv_update_config['author'] ."', '". $nv_update_config['note'] ."'
            )");
        } else {
            $db->query("UPDATE " . $db_config['prefix'] . "_setup_extensions SET
                id=". $nv_update_config['id'] .",
                version='" . $version . "',
                author='". $nv_update_config['author'] ."'
            WHERE basename='" . $nv_update_config['formodule'] . "' AND type='module'");
        }
    } catch (PDOException $e) {
        trigger_error($e->getMessage());
    }

    $nv_Cache->delAll(true);

    return $return;
}
