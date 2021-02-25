<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2017 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 04/18/2017 09:47
 */
if (!defined('NV_MAINFILE')) {
    die('Stop!!!');
}

if (!nv_function_exists('nv_theme_relates_product')) {

    /**
     * nv_block_config_new_blocks()
     *
     * @param mixed $module
     * @param mixed $data_block
     * @param mixed $lang_block
     * @return
     *
     */
    function nv_block_config_new_blocks($module, $data_block, $lang_block)
    {
        global $nv_Cache, $db_config, $site_mods;

        $html = "<div class=\"form-group\">";
        $html .= "  <label class=\"control-label col-sm-6\">" . $lang_block['blockid'] . "</label>";
        $html .= "  <td><select name=\"config_blockid\" class=\"form-control w200\">\n";
        $sql = "SELECT bid, " . NV_LANG_DATA . "_title," . NV_LANG_DATA . "_alias FROM " . $db_config['prefix'] . "_" . $site_mods[$module]['module_data'] . "_block_cat ORDER BY weight ASC";
        $list = $nv_Cache->db($sql, 'catid', $module);
        foreach ($list as $l) {
            $sel = ($data_block['blockid'] == $l['bid']) ? ' selected' : '';
            $html .= "<option value=\"" . $l['bid'] . "\" " . $sel . ">" . $l[NV_LANG_DATA . '_title'] . "</option>\n";
        }
        $html .= "  </select></div>\n";
        $html .= '<script type="text/javascript">';
        $html .= '  $("select[name=config_blockid]").change(function() {';
        $html .= '      $("input[name=title]").val($("select[name=config_blockid] option:selected").text());';
        $html .= '  });';
        $html .= '</script>';
        $html .= "</div>";

        $html .= "<div class=\"form-group\">";
        $html .= "  <label class=\"control-label col-sm-6\">" . $lang_block['numrow'] . "</label>";
        $html .= "  <div class=\"col-sm-18\"><input class=\"form-control w100\" type=\"text\" name=\"config_numrow\" size=\"5\" value=\"" . $data_block['numrow'] . "\"/></div>";
        $html .= "</div>";

        $html .= "<div class=\"form-group\">";
        $html .= "  <label class=\"control-label col-sm-6\">" . $lang_block['cut_num'] . "</label>";
        $html .= "  <div class=\"col-sm-18\"><input class=\"form-control w100\" type=\"text\" name=\"config_cut_num\" size=\"5\" value=\"" . $data_block['cut_num'] . "\"/></div>";
        $html .= "</div>";

        $html .= '<div class="form-group">';
        $html .= '	<label class="control-label col-sm-6">' . $lang_block['text'] . ':</label>';
        $html .= '	<div class="col-sm-18">';
        $html .= '<input type="text" class="form-control" name="config_text" value="' . $data_block['text'] . '"/>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * nv_block_config_new_blocks_submit()
     *
     * @param mixed $module
     * @param mixed $lang_block
     * @return
     *
     */
    function nv_block_config_new_blocks_submit($module, $lang_block)
    {
        global $nv_Request;
        $return = array();
        $return['error'] = array();
        $return['config'] = array();
        $return['config']['blockid'] = $nv_Request->get_int('config_blockid', 'post', 0);
        $return['config']['numrow'] = $nv_Request->get_int('config_numrow', 'post', 0);
        $return['config']['cut_num'] = $nv_Request->get_int('config_cut_num', 'post', 0);
        $return['config']['text'] = $nv_Request->get_string('config_text', 'post', '');
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
     * nv_theme_relates_product()
     *
     * @param mixed $block_config
     * @return
     *
     */
    function nv_theme_relates_product($block_config)
    {
        global $nv_Cache, $nv_Cache, $site_mods, $global_config, $lang_module, $module_config, $module_config, $module_name, $module_info, $global_array_shops_cat, $db_config, $my_head, $db, $pro_config, $money_config, $array_wishlist_id, $groupid, $global_array_group;

        $module = $block_config['module'];
        $mod_data = $site_mods[$module]['module_data'];
        $mod_file = $site_mods[$module]['module_file'];

        if (file_exists(NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $mod_file . '/block.shops_products.tpl')) {
            $block_theme = $global_config['module_theme'];
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

        $xtpl = new XTemplate('block.shops_products.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/' . $mod_file);
        $xtpl->assign('LANG', $lang_module);
        $xtpl->assign('CONFIG', $block_config);
        $xtpl->assign('TEMPLATE', $block_theme);

        $sql_block = 'SELECT bid, ' . NV_LANG_DATA . '_title as title,' . NV_LANG_DATA . '_alias as alias  FROM ' . $db_config['prefix'] . '_' . $site_mods[$module]['module_data'] . '_block_cat WHERE bid= ' . $block_config['blockid'] . ' ORDER BY weight ASC';
        $Array_block = $nv_Cache->db($sql_block, 'catid', $module);
        foreach ($Array_block as $block) {
            $xtpl->assign('BLOCK_LINK', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $site_mods[$module]['alias']['blockcat'] . '/' . $block['alias']);
            $xtpl->assign('BLOCK', $block);
        }

        $db->sqlreset()
            ->select('t1.id, t1.listcatid, t1.' . NV_LANG_DATA . '_title AS title, t1.' . NV_LANG_DATA . '_alias AS alias, t1.publtime, t1.addtime, t1.homeimgfile, t1.homeimgthumb, t1.product_price, t1.money_unit, t1.discount_id, t1.showprice, t1.product_number, t1.product_code,t1.vi_hometext,t1.discount_id,t1.showprice')
            ->from($db_config['prefix'] . '_' . $mod_data . '_rows t1')
            ->join('INNER JOIN ' . $db_config['prefix'] . '_' . $mod_data . '_block t2 ON t1.id = t2.id')
            ->where('t2.bid= ' . $block_config['blockid'] . ' AND t1.status =1')
            ->order('t1.addtime DESC, t2.weight ASC')
            ->limit($block_config['numrow']);

        $list = $nv_Cache->db($db->sql(), 'id', $module);
        //         var_dump($list);die;

        $i = 1;
        $cut_num = $block_config['cut_num'];


        foreach ($list as $row) {
            if ($row['homeimgthumb'] == 1) {
                // image thumb

                $src_img = NV_BASE_SITEURL . NV_FILES_DIR . '/' . $site_mods[$module]['module_upload'] . '/' . $row['homeimgfile'];
            } elseif ($row['homeimgthumb'] == 2) {
                // image file

                $src_img = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $site_mods[$module]['module_upload'] . '/' . $row['homeimgfile'];
            } elseif ($row['homeimgthumb'] == 3) {
                // image url

                $src_img = $row['homeimgfile'];
            } else {
                // no image

                $src_img = NV_BASE_SITEURL . 'themes/' . $block_theme . '/images/shops/no-image.jpg';
            }
            $xtpl->assign('ID_OWL', $row['listcatid']);
            $xtpl->assign('HOMETEXT', $row['vi_hometext']);
            $xtpl->assign('id', $row['id']);
            $xtpl->assign('link', $link . $global_array_shops_cat[$row['listcatid']]['alias'] . '/' . $row['alias'] . $global_config['rewrite_exturl']);
            $xtpl->assign('title', nv_clean60($row['title'], $cut_num));
            $xtpl->assign('src_img', $src_img);
            $xtpl->assign('time', nv_date('d-m-Y h:i:s A', $row['addtime']));

            $newday = $row['publtime'] + (86400 * $row['addtime']);
            if ($newday >= NV_CURRENTTIME) {
                $xtpl->parse('main.loop.new');
            }
            if ($row['product_code']) {
                $xtpl->assign('PRODUCT_CODE', $row['product_code']);
                $xtpl->parse('main.loop.product_code');
            }
            if ($row['vi_hometext']) {
                $xtpl->assign('VI_HOMETEXT', $row['vi_hometext']);
                $xtpl->parse('main.loop.hometext');
            }




            if ($pro_config['active_order'] == '1' and $pro_config['active_order_non_detail'] == '1') {
                if ($row['showprice'] == '1') {
                    if ($row['product_number'] > 0) {
                        // Kiem tra nhom bat buoc chon khi dat hang
                        $listgroupid = GetGroupID($row['id']);
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
                        $xtpl->parse('main.loop.order');
                    } else {
                        $xtpl->parse('main.loop.product_empty');
                    }
                }
            }


            if ($pro_config['active_price'] == '1') {
                if ($row['showprice'] == '1') {
                    $price = nv_get_price_tmp($module, $mod_data, $mod_file, $row['id']);
                    $xtpl->assign('PRICE', $price);
                    if ($row['discount_id'] and $price['discount_percent'] > 0) {
                        $xtpl->parse('main.loop.price.discounts');
                        $xtpl->parse('main.loop.price');
                    } else {
                        $xtpl->parse('main.loop.price.no_discounts');
                    }
                    $xtpl->parse('main.loop.price');
                } else {
                    $xtpl->parse('main.loop.contact');
                }
            }

            // San pham yeu thich
            if ($pro_config['active_wishlist']) {
                if (!empty($array_wishlist_id)) {
                    if (in_array($row['id'], $array_wishlist_id)) {
                        $xtpl->parse('main.loop.wishlist.disabled');
                    }
                }
                $xtpl->parse('main.loop.wishlist');
            }

            $xtpl->parse('main.loop');
            ++$i;
        }

        $xtpl->parse('main');
        return $xtpl->text('main');
    }
}

if (defined('NV_SYSTEM')) {
    $content = nv_theme_relates_product($block_config);
}
