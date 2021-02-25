<?php

/**

 * @Project NUKEVIET 4.x

 * @Author VINADES.,JSC (contact@vinades.vn)

 * @Copyright (C) 2014 VINADES., JSC. All rights reserved

 * @License GNU/GPL version 2 or any later version

 * @Createdate 3/9/2010 23:25

 */
if (!defined('NV_MAINFILE')) {

    die('Stop!!!');
}

if (!nv_function_exists('nv_block_shops_main_cat')) {

    /**

     * nv_block_config_shops_main_cat()

     *

     * @param mixed $module

     * @param mixed $data_block

     * @param mixed $lang_block

     * @return

     *

     */
    function nv_block_config_shops_main_cat($module, $data_block, $lang_block)

    {
        global $nv_Cache, $nv_Request, $db_config, $site_mods, $global_config, $client_info;

        $module = $data_block['module_name'];
        $mod_upload = $site_mods[$module]['module_upload'];
        $mod_file = $site_mods[$module]['module_file'];

        if ($nv_Request->isset_request('getcat', 'post')) {
            $module = $nv_Request->get_title('module', 'post', 'shops');
            if (isset($site_mods)) {
                $_sql = 'SELECT catid, ' . NV_LANG_INTERFACE . '_title title FROM ' . $db_config['prefix'] . '_' . $site_mods[$module]['module_data'] . '_catalogs WHERE inhome=1 and parentid=0';
                $array_cat = $nv_Cache->db($_sql, 'catid', $module);
                $html = '';
                if (!empty($array_cat)) {
                    foreach ($array_cat as $cat) {
                        $sl = $cat['catid'] == $data_block['catid'] ? 'selected="selected"' : '';
                        $html .= '<option value="' . $cat['catid'] . '" ' . $sl . '>' . $cat['title'] . '</option>';
                    }
                }
            }
            die($html);
        }

        $sql = "SELECT title, custom_title, module_data FROM " . $db_config['prefix'] . "_" . NV_LANG_DATA . "_modules WHERE module_file = 'shops'";
        $array_module = $nv_Cache->db($sql, 'title', $module);
        if (file_exists(NV_ROOTDIR . '/themes/' . $global_config['site_theme'] . '/modules/' . $mod_file . '/global.shops_main_cat.tpl')) {
            $block_theme = $global_config['site_theme'];
        } else {
            $block_theme = 'default';
        }
        $data_block['description'] = htmlspecialchars(nv_editor_br2nl($data_block['description']));
        if (defined('NV_EDITOR')) {
            require_once NV_ROOTDIR . '/' . NV_EDITORSDIR . '/' . NV_EDITOR . '/nv.php';
        }
        if (defined('NV_EDITOR') and nv_function_exists('nv_aleditor')) {
            $data_block['description'] = nv_aleditor('description', '100%', '200px', $data_block['description'], 'Basic');
        } else {
            $data_block['description'] = "<textarea style=\"width: 100%\" name=\"description\" id=\"description\" cols=\"20\" rows=\"15\">" . $data_block['description'] . "</textarea>";
        }
        $data_block['icon_currentpath'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $mod_upload;

        if (!empty($data_block['icon']) and file_exists(NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $mod_upload . '/' . $data_block['icon'])) {
            $data_block['icon_currentpath'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $mod_upload . '/' . dirname($data_block['icon']);
            $data_block['icon'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $mod_upload . '/' . $data_block['icon'];
        }
        $data_block['featured_currentpath'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $mod_upload;

        if (!empty($data_block['featured']) and file_exists(NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $mod_upload . '/' . $data_block['featured'])) {
            $data_block['featured_currentpath'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $mod_upload . '/' . dirname($data_block['featured']);
            $data_block['featured'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $mod_upload . '/' . $data_block['featured'];
        }

        $xtpl = new XTemplate('global.shops_main_cat.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/' . $mod_file);
        $xtpl->assign('LANG', $lang_block);
        $xtpl->assign('DATA', $data_block);
        $xtpl->assign('SELFURL', $client_info['selfurl']);
        $xtpl->assign('MODULE_UPLOAD', $site_mods[$module]['module_upload']);

        if (!empty($array_module)) {
            foreach ($array_module as $module) {
                $module['selected'] = $module['title'] == $data_block['module'] ? 'selected="selected"' : '';
                $xtpl->assign('MODULE', $module);
                $xtpl->parse('config.module');
            }
        }
        $xtpl->parse('config');
        return $xtpl->text('config');
    }

    /**

     * nv_block_config_shops_main_cat_submit()

     *

     * @param mixed $module

     * @param mixed $lang_block

     * @return

     *

     */
    function nv_block_config_shops_main_cat_submit($module, $lang_block)
    {
        global $nv_Request, $site_mods;
        $return = array();
        $return['error'] = array();
        $return['config'] = array();
        $return['config']['module_name'] = $nv_Request->get_title('config_module', 'post', 'shops');
        $return['config']['catid'] = $nv_Request->get_int('config_catid', 'post', 0);
        $return['config']['numrow'] = $nv_Request->get_int('config_numrow', 'post', 6);
        $return['config']['text'] = $nv_Request->get_title('config_text', 'post', '');
        $return['config']['description'] = $nv_Request->get_editor('description', '', NV_ALLOWED_HTML_TAGS);
        $module = $return['config']['module_name'];
        $return['config']['icon'] = '';
        $icon = $nv_Request->get_title('config_icon', 'post', '');

        if (!empty($icon) and file_exists(NV_ROOTDIR . $icon)) {
            $lu = strlen(NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $site_mods[$module]['module_upload'] . '/');
            $return['config']['icon'] = substr($icon, $lu);
        }

        $return['config']['featured'] = '';
        $icon = $nv_Request->get_title('config_featured', 'post', '');
        if (!empty($icon) and file_exists(NV_ROOTDIR . $icon)) {
            $lu = strlen(NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $site_mods[$module]['module_upload'] . '/');
            $return['config']['featured'] = substr($icon, $lu);
        }
        return $return;
    }

    if (!nv_function_exists('nv_get_price_tmp')) {
        function nv_get_price_tmp($module_name, $module_data, $module_file, $pro_id)
        {
            global $nv_Cache, $db, $db_config, $module_config, $discounts_config;
            $price = array();
            $pro_config = $module_config[$module_name];
            require_once NV_ROOTDIR . '/modules/' . $module_file . '/site.functions.php';
            $price = nv_get_price($pro_id, $pro_config['money_unit'], 1, false, $module_name);
            return $price;
        }
    }

    /**

     * nv_block_shops_main_cat()

     *

     * @param mixed $block_config

     * @return

     *

     */
    function nv_block_shops_main_cat($block_config)
    {
        global $nv_Cache, $site_mods, $global_config, $lang_module, $module_config, $module_config, $module_name, $module_info, $global_array_shops_cat, $db_config, $my_head, $db, $pro_config, $money_config, $global_array_group;
        $module = $block_config['module_name'];
        $mod_data = $site_mods[$module]['module_data'];
        $mod_file = $site_mods[$module]['module_file'];
        $mod_upload = $site_mods[$module]['module_upload'];

        if ($mod_file != 'shops') {
            return '';
        }

        if (file_exists(NV_ROOTDIR . '/themes/' . $global_config['site_theme'] . '/modules/' . $mod_file . '/global.shops_main_cat.tpl')) {
            $block_theme = $global_config['site_theme'];
        } else {
            $block_theme = 'default';
        }

        if ($module != $module_name) {
            if (file_exists(NV_ROOTDIR . '/themes/' . $block_theme . '/css/' . $mod_file . '.css')) {
                $my_head .= '<link rel="StyleSheet" href="' . NV_BASE_SITEURL . 'themes/' . $block_theme . '/css/' . $mod_file . '.css" type="text/css" />';
            }
            require_once NV_ROOTDIR . '/modules/' . $mod_file . '/site.functions.php';
            require_once NV_ROOTDIR . '/modules/' . $mod_file . '/language/' . NV_LANG_DATA . '.php';
            $pro_config = $module_config[$module];

            // Lay ty gia ngoai te

            $sql = 'SELECT code, currency, exchange, round, number_format FROM ' . $db_config['prefix'] . '_' . $mod_data . '_money_' . NV_LANG_DATA;

            $cache_file = NV_LANG_DATA . '_' . md5($sql) . '_' . NV_CACHE_PREFIX . '.cache';

            if (($cache = $nv_Cache->getItem($module, $cache_file)) != false) {

                $money_config = unserialize($cache);
            } else {

                $money_config = array();
                $result = $db->query($sql);
                while ($row = $result->fetch()) {
                    $money_config[$row['code']] = array(
                        'code' => $row['code'],
                        'currency' => $row['currency'],
                        'exchange' => $row['exchange'],
                        'round' => $row['round'],
                        'number_format' => $row['number_format'],
                        'decimals' => $row['round'] > 1 ? $row['round'] : strlen($row['round']) - 2,
                        'is_config' => ($row['code'] == $pro_config['money_unit']) ? 1 : 0
                    );
                }
                $result->closeCursor();
                $cache = serialize($money_config);
                $nv_Cache->setItem($module, $cache_file, $cache);
            }
        }

        $link = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=';

        // Danh sach nhom thuoc chu de

        $db->sqlreset()
            ->select('t1.groupid, ' . NV_LANG_INTERFACE . '_title title, ' . NV_LANG_INTERFACE . '_alias alias')
            ->from($db_config['prefix'] . '_' . $mod_data . '_group t1')
            ->join('INNER JOIN ' . $db_config['prefix'] . '_' . $mod_data . '_group_cateid t2 ON t1.groupid = t2.groupid')
            ->where('t1.inhome=1 AND t2.cateid=' . $block_config['catid'])
            ->order('t1.weight ASC');

        $array_group = $nv_Cache->db($db->sql(), 'groupid', $module);
        $array_data = array();

        if (!empty($array_group)) {
            foreach ($array_group as $group_id => $group_info) {
                $array_group_id = GetGroupidInParent($group_id, 1, 1);
                if (!empty($array_group_id)) {
                    foreach ($array_group_id as $_group_id) {

                        $db->sqlreset()
                            ->select('t1.id, t1.listcatid, t1.' . NV_LANG_DATA . '_title AS title, t1.' . NV_LANG_DATA . '_alias AS alias, t1.addtime, t1.homeimgfile, t1.homeimgthumb, t1.product_price, t1.money_unit, t1.discount_id, t1.showprice, t1.product_number')
                            ->from($db_config['prefix'] . '_' . $mod_data . '_rows t1')
                            ->join('INNER JOIN ' . $db_config['prefix'] . '_' . $mod_data . '_group_items t2 ON t1.id = t2.pro_id')
                            ->where('t1.status=1 AND t2.group_id=' . $_group_id)
                            ->order('t1.addtime DESC')
                            ->limit($block_config['numrow']);

                        $array_data_tmp = $nv_Cache->db($db->sql(), 'id', $module);
                        if (!empty($array_data_tmp)) {
                            $array_data[$_group_id] = $array_data_tmp;

                        }
                    }
                }
            }
        }

        if (!empty($block_config['icon']) and file_exists(NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $mod_upload . '/' . $block_config['icon'])) {
            $global_array_shops_cat[$block_config['catid']]['icon'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $mod_upload . '/' . $block_config['icon'];
        }

        if (!empty($block_config['featured']) and file_exists(NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $mod_upload . '/' . $block_config['featured'])) {
            $global_array_shops_cat[$block_config['catid']]['featured'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $mod_upload . '/' . $block_config['featured'];
        }

        $xtpl = new XTemplate('global.shops_main_cat.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/' . $mod_file);
        $xtpl->assign('LANG', $lang_module);
        $xtpl->assign('CAT', $global_array_shops_cat[$block_config['catid']]);
        $xtpl->assign('CONFIG', $block_config);

        if (!empty($array_data)) {

            $subcatid = $global_array_shops_cat[$block_config['catid']]['subcatid'];
            if (!empty($subcatid)) {
                $subcatid = explode(',', $subcatid);
                $i = 1;
                foreach ($subcatid as $_subcatid) {
                    $xtpl->assign('SUBCAT', $global_array_shops_cat[$_subcatid]);
                    if ($i > 7) {
                        $xtpl->parse('main.subcatloop.other');
                    }
                    $xtpl->parse('main.subcatloop');
                    $i++;
                }
                if (sizeof($subcatid) > 7) {
                    $xtpl->parse('main.other_btn');
                }
            }
            $i = 0;

            foreach ($array_data as $group_id => $group_data) {
                if ($i == 0) {
                    $global_array_group[$group_id]['active'] = 'active';
                }
                $xtpl->assign('GROUP', $global_array_group[$group_id]);

                if (!empty($group_data)) {
                    foreach ($group_data as $row) {

                        // Danh gia - Phan hoi
                        $rating_total = 0;
                        $result = $db->query('SELECT rating FROM ' . $db_config['prefix'] . '_' . $mod_data . '_review WHERE product_id = ' . $row['id'] . ' AND status=1');
                        $rating_count = $result->rowCount();
                        if ($rating_count > 0) {
                            while (list ($rating) = $result->fetch(3)) {
                                $rating_total += $rating;
                            }
                        }
                        $row['rating_total'] = $rating_count;
                        $row['rating_point'] = $rating_total;
                        $row['rating_value'] = $rating_count > 0 ? round($rating_total / $rating_count) : 0;
                        if ($row['homeimgthumb'] == 1) {
                            $src_img = NV_BASE_SITEURL . NV_FILES_DIR . '/' . $site_mods[$module]['module_upload'] . '/' . $row['homeimgfile'];
                        } elseif ($row['homeimgthumb'] == 2) {
                            $src_img = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $site_mods[$module]['module_upload'] . '/' . $row['homeimgfile'];
                        } elseif ($row['homeimgthumb'] == 3) {
                            $src_img = $row['homeimgfile'];
                        } else {
                            $src_img = NV_BASE_SITEURL . 'themes/' . $global_config['site_theme'] . '/images/shops/no-image.jpg';
                        }

                        $xtpl->assign('ID', $row['id']);
                        $xtpl->assign('TITLE', $row['title']);
                        $xtpl->assign('LINK', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module . '&' . NV_OP_VARIABLE . '=' . $global_array_shops_cat[$row['listcatid']]['alias'] . '/' . $row['alias'] . $global_config['rewrite_exturl']);
                        $xtpl->assign('IMG_SRC', $src_img);
                        $xtpl->assign('RATING_PERCENT', ($row['rating_value'] * 100) / 5);

                        if ($pro_config['active_price'] == '1') {
                            if ($row['showprice'] == '1' and $row['product_price'] > 1) {
                                $price = nv_get_price_tmp($module, $mod_data, $mod_file, $row['id']);
                                $xtpl->assign('PRICE', $price);
                                if ($row['discount_id'] and $price['discount_percent'] > 0) {
                                    $xtpl->parse('main.tab_content.loop.price.discounts');
                                } else {
                                    $xtpl->parse('main.tab_content.loop.price.no_discounts');
                                }
                                $xtpl->parse('main.tab_content.loop.price');
                            } else {
                                $xtpl->parse('main.tab_content.loop.contact');
                            }
                        }
                        if ($pro_config['active_order'] == '1' and $pro_config['active_order_non_detail'] == '1') {
                            if ($row['showprice'] == '1') {
                                if ($row['product_number'] > 0) {
                                    // Kiem tra nhom bat buoc chon khi dat hang
//                                     $listgroupid = GetGroupID($row['id']);
                                    $group_requie = 0;
                                    if (!empty($listgroupid) and !empty($global_array_group)) {
                                        foreach ($global_array_group as $groupinfo) {
                                            if ($groupinfo['in_order']) {
                                                $group_requie = 1;
                                                break;
                                            }
                                        }
                                    }
                                    $group_requie = $pro_config['active_order_popup'] ? 1 : $group_requie;
                                    $xtpl->assign('GROUP_REQUIE', $group_requie);
                                    $xtpl->parse('main.tab_content.loop.order');
                                } else {
                                    $xtpl->parse('main.tab_content.loop.product_empty');
                                }
                            }
                        }
                        if ($pro_config['show_compare'] == 1) {
                            $xtpl->parse('main.tab_content.loop.compare');
                        }
                        if ($pro_config['active_wishlist']) {
                            $xtpl->parse('main.tab_content.loop.wishlist');
                        }
                        $xtpl->parse('main.tab_content.loop');
                    }
                }
                $xtpl->parse('main.tab_content');
                $xtpl->parse('main.tab_title');
                $i++;
            }
        } else {
            return '';
        }
        if (isset($global_array_shops_cat[$block_config['catid']]['icon'])) {
            $xtpl->parse('main.icon');
        }
        if (isset($global_array_shops_cat[$block_config['catid']]['featured'])) {
           $xtpl->parse('main.featured');
        }
        if (!defined('MODAL_LOADED')) {
            $xtpl->parse('main.modal_loaded');
            define('MODAL_LOADED', true);
        }
        $xtpl->parse('main');
        return $xtpl->text('main');
    }
}

if (defined('NV_SYSTEM')) {
    global $db, $db_config, $site_mods, $module_name, $global_array_shops_cat, $global_array_group;
    $module = $block_config['module_name'];
    if (isset($site_mods[$module])) {
        if ($module != $module_name) {
            $sql = 'SELECT catid, parentid, lev, ' . NV_LANG_DATA . '_title AS title, ' . NV_LANG_DATA . '_title_custom AS title_custom, ' . NV_LANG_DATA . '_alias AS alias, viewcat, numsubcat, subcatid, newday, typeprice, form, group_price, viewdescriptionhtml, numlinks, ' . NV_LANG_DATA . '_description AS description, ' . NV_LANG_DATA . '_descriptionhtml AS descriptionhtml, inhome, ' . NV_LANG_DATA . '_keywords AS keywords, groups_view, ad_block_cat, cat_allow_point, cat_number_point, cat_number_product, image FROM ' . $db_config['prefix'] . '_' . $site_mods[$module]['module_data'] . '_catalogs ORDER BY sort ASC';
            $global_array_shops_cat = $nv_Cache->db($sql, 'catid', $module);
            foreach ($global_array_shops_cat as $catid => $cat) {
                $global_array_shops_cat[$catid]['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $cat['alias'];
            }
            $sql = 'SELECT groupid, parentid, lev, ' . NV_LANG_DATA . '_title AS title, ' . NV_LANG_DATA . '_alias AS alias, viewgroup, numsubgroup, subgroupid, ' . NV_LANG_DATA . '_description AS description, inhome, indetail, in_order, ' . NV_LANG_DATA . '_keywords AS keywords, numpro, image, is_require FROM ' . $db_config['prefix'] . '_' . $site_mods[$module]['module_data'] . '_group ORDER BY sort ASC';
            $global_array_group = $nv_Cache->db($sql, 'groupid', $module);
        }
        $content = nv_block_shops_main_cat($block_config);
    }
}