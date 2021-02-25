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

if (!nv_function_exists('nv_theme_products_column')) {

    /**
     * nv_block_config_theme_products_column_blocks()
     *
     * @param mixed $module
     * @param mixed $data_block
     * @param mixed $lang_block
     * @return
     */
    function nv_block_config_theme_products_column_blocks($module, $data_block, $lang_block)
    {
        global $nv_Cache, $db_config, $site_mods;

        $html = "<tr>";
        $html .= "	<td>" . $lang_block['blockid'] . "</td>";
        $html .= "	<td><select name=\"config_blockid\" class=\"form-control w200\">\n";
        $sql = "SELECT bid, " . NV_LANG_DATA . "_title," . NV_LANG_DATA . "_alias FROM " . $db_config['prefix'] . "_" . $site_mods[$module]['module_data'] . "_block_cat ORDER BY weight ASC";
        $list = $nv_Cache->db($sql, 'catid', $module);
        foreach ($list as $l) {
            $sel = ($data_block['blockid'] == $l['bid']) ? ' selected' : '';
            $html .= "<option value=\"" . $l['bid'] . "\" " . $sel . ">" . $l[NV_LANG_DATA . '_title'] . "</option>\n";
        }
        $html .= "	</select></td>\n";
        $html .= '<script type="text/javascript">';
        $html .= '	$("select[name=config_blockid]").change(function() {';
        $html .= '		$("input[name=title]").val($("select[name=config_blockid] option:selected").text());';
        $html .= '	});';
        $html .= '</script>';
        $html .= "</tr>";

        $html .= "<tr>";
        $html .= "	<td>" . $lang_block['numrow'] . "</td>";
        $html .= "	<td><input class=\"form-control w100\" type=\"text\" name=\"config_numrow\" size=\"5\" value=\"" . $data_block['numrow'] . "\"/></td>";
        $html .= "</tr>";

        $html .= "<tr>";
        $html .= "	<td>" . $lang_block['cut_num'] . "</td>";
        $html .= "	<td><input class=\"form-control w100\" type=\"text\" name=\"config_cut_num\" size=\"5\" value=\"" . $data_block['cut_num'] . "\"/></td>";
        $html .= "</tr>";

        $html .= "<tr>";
        $html .= "	<td>" . $lang_block['num_row'] . "</td>";
        $html .= "	<td><input class=\"form-control w100\" type=\"text\" name=\"config_num_row\" size=\"5\" value=\"" . $data_block['num_row'] . "\"/></td>";
        $html .= "</tr>";

        $array_template = array(
            'main_1' => $lang_block['style_1'],
            'main_2' => $lang_block['style_2']
        );

        $html .= "<tr>";
        $html .= "	<td>" . $lang_block['template'] . "</td>";
        $html .= "	<td><select class=\"form-control\" name=\"config_template\">";
        foreach ($array_template as $index => $value) {
            $sl = $index == $data_block['template'] ? 'selected="selected"' : '';
            $html .= "<option value=" . $index . " " . $sl . ">" . $value . "</option>";
        }
        $html .= "</select></td>";
        $html .= "</tr>";

        return $html;
    }

    /**
     * nv_block_config_theme_products_column_submit()
     *
     * @param mixed $module
     * @param mixed $lang_block
     * @return
     */
    function nv_block_config_theme_products_column_submit($module, $lang_block)
    {
        global $nv_Request;
        $return = array();
        $return['error'] = array();
        $return['config'] = array();
        $return['config']['blockid'] = $nv_Request->get_int('config_blockid', 'post', 0);
        $return['config']['numrow'] = $nv_Request->get_int('config_numrow', 'post', 0);
        $return['config']['cut_num'] = $nv_Request->get_int('config_cut_num', 'post', 0);
        $return['config']['template'] = $nv_Request->get_title('config_template', 'post', 'main_1');
        $return['config']['num_row'] = $nv_Request->get_int('config_num_row', 'post', 2);
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
     * nv_theme_products_column()
     *
     * @param mixed $block_config
     * @return
     */
    function nv_theme_products_column($block_config)
    {
        global $nv_Cache, $site_mods, $global_config, $lang_module, $module_config, $module_config, $module_name, $module_info, $global_array_shops_cat, $db_config, $my_head, $db, $pro_config, $money_config, $array_wishlist_id, $catid;

        $module = $block_config['module'];
        $mod_data = $site_mods[$module]['module_data'];
        $mod_file = $site_mods[$module]['module_file'];
        $mod_upload = $site_mods[$module]['module_upload'];

        if (file_exists(NV_ROOTDIR . '/themes/' . $global_config['site_theme'] . '/modules/' . $mod_file . '/block.shops_products_column.tpl')) {
            $block_theme = $global_config['site_theme'];
        } else {
            $block_theme = 'default';
        }

        if ($module != $module_name) {
            $sql = 'SELECT catid, parentid, lev, ' . NV_LANG_DATA . '_title AS title, ' . NV_LANG_DATA . '_alias AS alias, viewcat, numsubcat, subcatid, numlinks, ' . NV_LANG_DATA . '_description AS description, inhome, ' . NV_LANG_DATA . '_keywords AS keywords, groups_view, typeprice FROM ' . $db_config['prefix'] . '_' . $mod_data . '_catalogs ORDER BY sort ASC';
            $list = $nv_Cache->db($sql, 'catid', $module);
            foreach ($list as $row) {
                $global_array_shops_cat[$row['catid']] = array(
                    'catid' => $row['catid'],
                    'parentid' => $row['parentid'],
                    'title' => $row['title'],
                    'alias' => $row['alias'],
                    'link' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $row['alias'],
                    'viewcat' => $row['viewcat'],
                    'numsubcat' => $row['numsubcat'],
                    'subcatid' => $row['subcatid'],
                    'numlinks' => $row['numlinks'],
                    'description' => $row['description'],
                    'inhome' => $row['inhome'],
                    'keywords' => $row['keywords'],
                    'groups_view' => $row['groups_view'],
                    'lev' => $row['lev'],
                    'typeprice' => $row['typeprice']
                );
            }
            unset($list, $row);

            // Css
            if (file_exists(NV_ROOTDIR . '/themes/' . $block_theme . '/css/' . $mod_file . '.css')) {
                $my_head .= '<link rel="StyleSheet" href="' . NV_BASE_SITEURL . 'themes/' . $block_theme . '/css/' . $mod_file . '.css" type="text/css" />';
            }

            // Language
            if (file_exists(NV_ROOTDIR . '/modules/' . $mod_file . '/language/' . NV_LANG_DATA . '.php')) {
                require_once NV_ROOTDIR . '/modules/' . $mod_file . '/language/' . NV_LANG_DATA . '.php';
            }

            $pro_config = $module_config[$module];

            $sql = 'SELECT code, currency, symbol, exchange, round, number_format FROM ' . $db_config['prefix'] . '_' . $mod_data . '_money_' . NV_LANG_DATA;
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
                        'symbol' => $row['symbol'],
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

        $xtpl = new XTemplate('block.shops_products_column.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/' . $mod_file);
        $xtpl->assign('LANG', $lang_module);
        $xtpl->assign('BLOCK_TITLE', $block_config['title']);

        $sql_block = 'SELECT bid, ' . NV_LANG_DATA . '_title,' . NV_LANG_DATA . '_alias FROM ' . $db_config['prefix'] . '_' . $site_mods[$module]['module_data'] . '_block_cat WHERE bid= ' . $block_config['blockid'] . ' ORDER BY weight ASC';
        $Array_block = $nv_Cache->db($sql_block, 'catid', $module);
        foreach ($Array_block as $block) {
            $xtpl->assign('BLOCK_LINK', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $site_mods[$module]['alias']['blockcat'] . '/' . $block['' . NV_LANG_DATA . '_alias']);
        }

        $style = $block_config['template'];
        $db->sqlreset()
            ->select('t1.id,product_code,product_number, t1.listcatid, t1.' . NV_LANG_DATA . '_title AS title, t1.' . NV_LANG_DATA . '_hometext AS hometext, t1.' . NV_LANG_DATA . '_alias AS alias, t1.addtime, t1.homeimgfile, t1.homeimgthumb, t1.product_price, t1.money_unit, t1.discount_id, t1.showprice, t1.product_number, t1.discount_id')
            ->from($db_config['prefix'] . '_' . $mod_data . '_rows t1')
            ->join('INNER JOIN ' . $db_config['prefix'] . '_' . $mod_data . '_block t2 ON t1.id = t2.id')
            ->where('t2.bid= ' . $block_config['blockid'] . ' AND t1.status =1')
            ->order('t1.addtime DESC, t2.weight ASC')
            ->limit($block_config['numrow']);

        $list = $nv_Cache->db($db->sql(), 'id', $module);

        $array_data = array();

        if (!empty($list)) {
            $i = 1;
            $j = 1;
            foreach ($list as $row) {
                //                 var_dump($row);die;
                if ($row['homeimgthumb'] == 1) {
                    //image thumb
                    $row['imghome'] = NV_BASE_SITEURL . NV_FILES_DIR . '/' . $mod_upload . '/' . $row['homeimgfile'];
                } elseif ($row['homeimgthumb'] == 2) {
                    //image file
                    $row['imghome'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $mod_upload . '/' . $row['homeimgfile'];
                } elseif ($row['homeimgthumb'] == 3) {
                    //image url
                    $row['imghome'] = $row['homeimgfile'];
                } elseif (!empty($show_no_image)) {
                    //no image
                    $row['imghome'] = NV_BASE_SITEURL . $show_no_image;
                } else {
                    $row['imghome'] = '';
                }
                //                 if ($row['product_code']) {
                //                     $xtpl->parse('main.items.loop.price.discounts');
                //                     $xtpl->parse('main.items.loop.price.no_discounts');
                //                 }
                $row['price'] = nv_get_price_tmp($module, $mod_data, $mod_file, $row['id']);
                $row['link'] = $link . $global_array_shops_cat[$row['listcatid']]['alias'] . '/' . $row['alias'] . $global_config['rewrite_exturl'];
                $row['title0'] = nv_clean60($row['title'], $block_config['cut_num']);
                $row['src_img'] = $row['imghome'];
                $newday = $row['publtime'] + (86400 * $row['addtime']);
                if ($newday >= NV_CURRENTTIME) {
                    $xtpl->parse('main.' . $style . '.items.loop.new');
                }

                $array_data[$i][] = $row;
                if ($j == $block_config['num_row']) {
                    $i++;
                    $j = 0;
                }
                $j++;
            }
        }

        if (!empty($array_data)) {
            foreach ($array_data as $ls) {
                foreach ($ls as $data) {
                    $xtpl->assign('ROW', $data);
                    $price = nv_get_price($data['id'], $pro_config['money_unit']);
                    if ($pro_config['active_price'] == '1') {
                        if ($data['showprice'] == '1' && !empty($data['product_price'])) {
                            $xtpl->assign('PRICE', $price);
                            if ($data['discount_id'] and $price['discount_percent'] > 0) {
                                $xtpl->parse('main.' . $style . '.items.loop.price.discounts');
                                //                             die('1');
                            } else {
                                $xtpl->parse('main.' . $style . '.items.loop.price.no_discounts');
                            }
                            $xtpl->parse('main.' . $style . '.items.loop.price');
                        } else {
                            $xtpl->parse('main.' . $style . '.items.loop.contact');
                        }
                    }

                    if ($pro_config['active_order'] == '1' and $pro_config['active_order_non_detail'] == '1') {
                        if ($data['showprice'] == '1' && !empty($data['product_price'])) {
                            if ($data['product_number'] > 0) {
                                // Kiem tra nhom bat buoc chon khi dat hang
                                //                                 die('1');
                                //                                 $listgroupid = GetGroupID($data['id']);
                                //                                 var_dump($listgroupid);die;
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
                                $xtpl->parse('main.' . $style . '.items.loop.order');
                            } else {
                                $xtpl->parse('main.' . $style . '.items.loop.product_empty');
                            }
                        }
                    }
                    if (!empty($global_array_shops_cat)) {
                        foreach ($global_array_shops_cat as $catid => $cat) {
                            if (in_array($cat, $global_array_shops_cat)) {
                                $xtpl->assign('CAT_TITLE', $cat['title']);
                            }
                        }
                    }
                    if ($pro_config['active_wishlist']) {
                        if (!empty($array_wishlist_id)) {
                            if (!in_array($data['id'], $array_wishlist_id)) {
                                $xtpl->parse('main.' . $style . '.items.loop.wishlist');
                            }
                        }
                    }
                    if ($data['discount_id'] and $price['discount_percent'] > 0 and $data['showprice']) {
                        $xtpl->parse('main.' . $style . '.items.loop.discounts');
                    }

                    $xtpl->parse('main.' . $style . '.items.loop');
                }
                $xtpl->parse('main.' . $style . '.items');
            }
        }
        $xtpl->parse('main.' . $style);

        $xtpl->parse('main');
        return $xtpl->text('main');
    }
}

if (defined('NV_SYSTEM')) {
    $module = $block_config['module'];
    $content = nv_theme_products_column($block_config);
}