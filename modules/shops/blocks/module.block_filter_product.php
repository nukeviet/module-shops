<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2017 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 04/18/2017 09:47
 */

if (!defined('NV_MAINFILE')) {
    die('Stop!!!');
}

if (!function_exists('nv_filter_product')) {

    /**
     * nv_block_config_filter_product()
     *
     * @param mixed $module
     * @param mixed $data_block
     * @param mixed $lang_block
     * @return
     */
    function nv_block_config_filter_product($module, $data_block, $lang_block)
    {
        $html = '';
        $html .= "<div class=\"form-group\">";
        $html .= "	<label class=\"control-label col-sm-6\">" . $lang_block['group_price'] . "</label>";
        $ck = $data_block['display_group_price'] ? 'checked="checked"' : '';
        $html .= "	<div class=\"col-sm-18\"><input type=\"checkbox\" class=\"form-control\" name=\"config_group_price\" value=\"1\" " . $ck . " /></div>";
        $html .= "</div>";

        return $html;
    }

    /**
     * nv_block_config_filter_product_submit()
     *
     * @param mixed $module
     * @param mixed $lang_block
     * @return
     */
    function nv_block_config_filter_product_submit($module, $lang_block)
    {
        global $nv_Request;
        $return = [];
        $return['config'] = [];
        $return['config']['display_group_price'] = $nv_Request->get_int('config_group_price', 'post', 0);
        return $return;
    }

    /**
     * nv_filter_product()
     *
     * @return
     */
    function nv_filter_product($block_config)
    {
        global $nv_Request, $module_name, $lang_module, $module_info, $module_file, $module_upload, $db, $module_data, $db_config, $id, $catid, $pro_config, $global_config, $global_array_group, $global_array_shops_cat, $pro_config, $catid, $crypt;

        $module = $block_config['module'];

        $array_id = $group_price = [];
        $filter = $nv_Request->get_string('filter', 'get', '');
        if (!empty($filter)) {
            $array_id = json_decode($crypt->decrypt($filter), true);
            $array_id = array_map('intval', $array_id);
        }

        if ($block_config['display_group_price']) {
            $groupprice = $nv_Request->get_string('group_price', 'get', '');
            if (!empty($groupprice)) {
                $group_price = json_decode($crypt->decrypt($groupprice), true);
            }
        }

        $xtpl = new XTemplate('block.filter_product.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
        $xtpl->assign('LANG', $lang_module);
        $xtpl->assign('MONEY_UNIT', $pro_config['money_unit']);

        foreach ($global_array_group as $arr_group) {
            $space = '';

            if (!empty($arr_group['image'])) {
                $arr_group['image'] = NV_STATIC_URL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $arr_group['image'];
            }

            if ($arr_group['lev'] > 0) {
                if ($global_array_group[$arr_group['parentid']]['inhome'] and $arr_group['inhome']) {
                    for ($i = 1; $i <= $arr_group['lev']; $i ++) {
                        $space .= '&nbsp;&nbsp;&nbsp;';
                    }
                    $xtpl->assign('DATA', [
                        'id' => $arr_group['groupid'],
                        'title' => $arr_group['title'],
                        'numpro' => $arr_group['numpro'],
                        'space' => $space,
                        'image' => $arr_group['image'],
                        'checked' => in_array($arr_group['groupid'], $array_id) ? 'checked="checked"' : ''
                    ]);
                    $xtpl->parse('main.loop.sub_group.checkbox');

                    if (!empty($arr_group['image'])) {
                        $xtpl->parse('main.loop.sub_group.image');
                    }

                    $xtpl->parse('main.loop.sub_group');
                } else {
                    $global_array_group[$arr_group['groupid']]['inhome'] = 0;
                }
            } elseif ($arr_group['inhome']) {
                $xtpl->assign('DATA', [
                    'title' => $arr_group['title'],
                    'image' => $arr_group['image']
                ]);

                if (!empty($arr_group['image'])) {
                    $xtpl->parse('main.loop.main_group.image');
                }

                $xtpl->parse('main.loop.main_group');
            }

            $xtpl->parse('main.loop');
        }

        if (!empty($pro_config['group_price']) and $block_config['display_group_price']) {
            if (!empty($catid) and isset($global_array_shops_cat[$catid]) and !empty($global_array_shops_cat[$catid]['group_price'])) {
                $price_value = explode(PHP_EOL, $global_array_shops_cat[$catid]['group_price']);
            } else {
                $price_value = explode(PHP_EOL, $pro_config['group_price']);
            }

            $price_value = array_map('floatval', $price_value);
            if (!empty($price_value)) {
                $price1 = $price2 = 0;
                $i = 0;
                foreach ($price_value as $value) {
                    $price1 = $value;
                    if (isset($price_value[$i + 1])) {
                        $price2 = $price_value[$i + 1];
                        $price_key = $price1 . '-' . $price2;
                        $price = [
                            'price_key' => $price_key,
                            'price_value' => nv_number_format($price1) . ' - ' . nv_number_format($price2),
                            'checked' => in_array($price_key, $group_price) ? 'checked="checked"' : ''
                        ];
                    } else {
                        $price2 = 0;
                        $price_key = $price1 . '-' . $price2;
                        $price = [
                            'price_key' => $price_key,
                            'price_value' => sprintf($lang_module['filter_price_from'], nv_number_format($price1)),
                            'checked' => in_array($price_key, $group_price) ? 'checked="checked"' : ''
                        ];
                    }
                    $xtpl->assign('PRICE', $price);
                    $xtpl->parse('main.group_price.loop');
                    $i ++;
                }
                $xtpl->parse('main.group_price');
            }
        }

        if ($nv_Request->isset_request('filter', 'post')) {
            $array_id = $nv_Request->get_array('group_id', 'post', []);
            $ext = '';
            if (!empty($array_id)) {
                $array_id = $crypt->encrypt(json_encode($array_id));
                $ext .= '&filter=' . $array_id;
            }

            if ($block_config['display_group_price']) {
                $array_price = $nv_Request->get_array('group_price', 'post', []);
                if (!empty($array_price)) {
                    $array_price = $crypt->encrypt(json_encode($array_price));
                    $ext .= '&group_price=' . $array_price;
                }
            }
            nv_redirect_location(NV_STATIC_URL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=search_result' . $ext);
        }

        $xtpl->parse('main');
        return $xtpl->text('main');
    }
}

$content = nv_filter_product($block_config);
