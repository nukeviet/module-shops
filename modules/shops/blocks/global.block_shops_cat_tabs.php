<?php

/**
 * @Project NUKEVIET 4.x
 * @Author NV SYSTEMS (tuantmsh@gmail.com)
 * @License GNU/GPL version 2 or any later version
 * @Createdate Sat, 08 Oct 2017 06:46:54 GMT
 */
if (!defined('NV_MAINFILE')) die('Stop!!!');

if (!nv_function_exists('nv_block_shops_block_cat_tabs')) {

    function nv_tabs_viewsub($list_sub, $block_config, $nvs_array_cat_shops, $data_block)
    {
        $cut_num = $block_config['title_length'];
        if (empty($list_sub)) {
            return "";
        } else {
            $html = "";
            $list = explode(",", $list_sub);
            foreach ($list as $catid) {
                if ($nvs_array_cat_shops[$catid]['inhome'] == '1') {
                    $html .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                    $html .= '<label><input type="checkbox" name="config_blockid[]" value="' . $catid . '" ' . ((in_array($catid, $data_block)) ? ' checked="checked"' : '') . '</input>' . nv_clean60($nvs_array_cat_shops[$catid]['title'], $cut_num) . '</label><br />';
                    if (!empty($nvs_array_cat_shops[$catid]['subcatid'])) {
                        $html .= nv_tabs_viewsub($nvs_array_cat_shops[$catid]['subcatid'], $block_config);
                    }
                    $html .= "";
                }
            }
            $html .= "";
            return $html;
        }
    }

    function nv_block_config_shops_block_cat_tabs($module, $data_block, $lang_block)
    {
        global $db, $db_config, $site_mods, $nv_Request;

        // Xuất nội dung khi có chọn module
        if ($nv_Request->isset_request('loadajaxdata', 'get')) {
            $module = $nv_Request->get_title('loadajaxdata', 'get', '');
            $module_data = $site_mods[$module]['module_data'];

            $html .= '<div class="form-group">';

            $html .= '	<label class="control-label col-sm-6">' . $lang_block['blockid'] . ':</label>';

            $sql = 'SELECT catid, parentid, lev, ' . NV_LANG_DATA . '_title AS title, ' . NV_LANG_DATA . '_alias AS alias, viewcat, numsubcat, subcatid, numlinks, ' . NV_LANG_DATA . '_description AS description, inhome, ' . NV_LANG_DATA . '_keywords AS keywords, groups_view FROM ' . $db_config['prefix'] . '_' . $module_data . '_catalogs ORDER BY weight ASC';
            $list = $db->query($sql);
            $html .= '	<div class="col-sm-18">';
            while ($l = $list->fetch()) {
                $nvs_array_cat_shops[$l['catid']] = array(
                    "catid" => $l['catid'],
                    "parentid" => $l['parentid'],
                    "title" => $l['title'],
                    "alias" => $l['alias'],
                    "viewcat" => $l['viewcat'],
                    "numsubcat" => $l['numsubcat'],
                    "subcatid" => $l['subcatid'],
                    "numlinks" => $l['numlinks'],
                    "description" => $l['description'],
                    "inhome" => $l['inhome'],
                    "keywords" => $l['keywords'],
                    "groups_view" => $l['groups_view'],
                    'lev' => $l['lev']
                );
            }
            foreach ($nvs_array_cat_shops as $cat) {
                if ($cat['parentid'] == 0) {
                    if ($cat['inhome'] == '1') {
                        $html .= "";
                        $html .= '<label><input type="checkbox" name="config_blockid[]" value="' . $cat['catid'] . '" ' . ((in_array($cat['catid'], $data_block['blockid'])) ? ' checked="checked"' : '') . '</input>' . nv_clean60($cat['title'], $cut_num) . '</label><br />';
                        if (!empty($cat['subcatid'])) {
                            $html .= "<span class=\"fa arrow expand\"></span>";
                            $html .= nv_tabs_viewsub($cat['subcatid'], $block_config, $nvs_array_cat_shops, $data_block['blockid']);
                        }
                        $html .= "";
                    }
                }
            }

            $html .= '</div>';
            $html .= '</div>';

            $html .= '<div class="form-group">';
            $html .= '	<label class="control-label col-sm-6">' . $lang_block['numrow'] . ':</label>';
            $html .= '	<div class="col-sm-18">';
            $html .= '<input type="text" class="form-control w200" name="config_numrow" size="5" value="' . $data_block['numrow'] . '"/>';
            $html .= '</div>';
            $html .= '</div>';

            $html .= '<div class="form-group">';
            $html .= '	<label class="control-label col-sm-6">' . $lang_block['title_length'] . ':</label>';
            $html .= '	<div class="col-sm-18">';
            $html .= '<input type="text" class="form-control w200" name="config_title_length" size="5" value="' . $data_block['title_length'] . '"/>';
            $html .= '</div>';
            $html .= '</div>';

            $html .= '<div class="form-group">';
            $html .= '	<label class="control-label col-sm-6">' . $lang_block['display_type'] . ':</label>';
            $html .= '	<div class="col-sm-18">';
            $html .= '<input type="checkbox" value="1" name="config_display_type" ' . ($data_block['display_type'] == 1 ? 'checked="checked"' : '') . ' />';
            $html .= '</div>';
            $html .= '</div>';

            $html .= '<div class="form-group">';
            $html .= '	<label class="control-label col-sm-6">' . $lang_block['blockwidth'] . ':</label>';
            $html .= '	<div class="col-sm-18">';
            $html .= '<input class="form-control w200"  type="text" value="' . $data_block['blockwidth'] . '" name="config_blockwidth"  />';
            $html .= '</div>';
            $html .= '</div>';

            $html .= '<div class="form-group">';
            $html .= '	<label class="control-label col-sm-6">' . $lang_block['blockheight'] . ':</label>';
            $html .= '	<div class="col-sm-18">';
            $html .= '<input class="form-control w200"  type="text" value="' . $data_block['blockheight'] . '" name="config_blockheight"  />';
            $html .= '</div>';
            $html .= '</div>';

            $html .= '<div class="form-group">';
            $html .= '	<label class="control-label col-sm-6">' . $lang_block['show_no_image'] . ':</label>';
            $html .= '	<div class="col-sm-18">';
            $html .= '<input class="form-control w200"  type="text" value="' . $data_block['show_no_image'] . '" name="config_show_no_image"  />';
            $html .= '</div>';
            $html .= '</div>';

            nv_htmlOutput($html);
        }
        $html .= '<div class="form-group">';
        $html .= '	<label class="control-label col-sm-6">' . $lang_block['text'] . ':</label>';
        $html .= '	<div class="col-sm-18">';
        $html .= '<input type="text" class="form-control" name="config_text" value="' . $data_block['text'] . '"/>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="form-group">';
        $html .= '	<label class="control-label col-sm-6">' . $lang_block['selectmod'] . ':</label>';
        $html .= '	<div class="col-sm-18">';
        $html .= '<select name="config_selectmod" class="form-control w300">';
        $html .= '<option value="">--</option>';

        foreach ($site_mods as $title => $mod) {
            if ($mod['module_file'] == $module) {
                $html .= '<option value="' . $title . '"' . ($title == $data_block['selectmod'] ? ' selected="selected"' : '') . '>' . $mod['custom_title'] . '</option>';
            }
        }

        $html .= '</select>';

        $html .= '
        <script type="text/javascript">
        $(\'[name="config_selectmod"]\').change(function() {
            var mod = $(this).val();
            var file_name = $("select[name=file_name]").val();
            var module_type = $("select[name=module_type]").val();
            var blok_file_name = "";
            if (file_name != "") {
                var arr_file = file_name.split("|");
                if (parseInt(arr_file[1]) == 1) {
                    blok_file_name = arr_file[0];
                }
            }
            if (mod != "") {
                $.get(script_name + "?" + nv_name_variable + "=" + nv_module_name + \'&\' + nv_lang_variable + "=" + nv_lang_data + "&" + nv_fc_variable + "=block_config&bid=" + bid + "&module=" + module_type + "&selectthemes=" + selectthemes + "&file_name=" + blok_file_name + "&loadajaxdata=" + mod + "&nocache=" + new Date().getTime(), function(theResponse) {
        			$("#block_config").append(theResponse);
        		});
            }
        });
        $(function() {
            $(\'[name="config_selectmod"]\').change();
        });
        </script>
        ';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    function nv_block_config_shops_block_cat_tabs_submit($module, $lang_block)
    {
        global $nv_Request;
        $return = array();
        $return['error'] = array();
        $return['config'] = array();
        $return['config']['selectmod'] = $nv_Request->get_title('config_selectmod', 'post', '');
        $return['config']['blockid'] = $nv_Request->get_array('config_blockid', 'post', array());
        $return['config']['numrow'] = $nv_Request->get_int('config_numrow', 'post', 0);
        $return['config']['title_length'] = $nv_Request->get_string('config_title_length', 'post', 0);
        $return['config']['display_type'] = $nv_Request->get_int('config_display_type', 'post', 0);
        $return['config']['blockwidth'] = $nv_Request->get_string('config_blockwidth', 'post', '100px');
        $return['config']['blockheight'] = $nv_Request->get_string('config_blockheight', 'post', '100px');
        $return['config']['show_no_image'] = $nv_Request->get_string('config_show_no_image', 'post', '');
        $return['config']['text'] = $nv_Request->get_string('config_text', 'post', '');
        return $return;
    }
    if (!nv_function_exists('nv_systems_get_price')) {

        function nv_systems_get_price($pro_id, $currency_convert, $number = 1, $per_pro = false, $module = '')
        {

            global $nv_Cache, $db, $db_config, $site_mods, $module_data, $nv_systems_shops_array_group, $module_config;
            $pro_config = $module_config[$module];
            $module_data = $site_mods[$module]['module_data'];
            // Groups
            $sql = 'SELECT catid, parentid, lev, ' . NV_LANG_DATA . '_title AS title, ' . NV_LANG_DATA . '_title_custom AS title_custom, ' . NV_LANG_DATA . '_alias AS alias, viewcat, numsubcat, subcatid, newday, typeprice, form, group_price, viewdescriptionhtml, numlinks, ' . NV_LANG_DATA . '_description AS description, ' . NV_LANG_DATA . '_descriptionhtml AS descriptionhtml, inhome, ' . NV_LANG_DATA . '_keywords AS keywords, ' . NV_LANG_DATA . '_tag_description AS tag_description, groups_view, cat_allow_point, cat_number_point, cat_number_product, image FROM ' . $db_config['prefix'] . '_' . $module_data .'_catalogs ORDER BY sort ASC';
            $nv_systems_shops_array_cat_sql = $db->query($sql);
            while ($row = $nv_systems_shops_array_cat_sql->fetch()) {
                $nv_systems_shops_array_cat[$row['catid']] = $row;
            }

            // Lay ty gia ngoai te
            $sql = 'SELECT code, currency,symbol, exchange, round, number_format FROM ' . $db_config['prefix'] . '_' . $module_data .'_money_' . NV_LANG_DATA;
            $nvs_money_config = array();
            $result = $db->query($sql);
            while ($row = $result->fetch()) {
                $nvs_money_config[$row['code']] = array(
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
            // Lay Giam Gia
            $sql = 'SELECT did, title, begin_time, end_time, config FROM ' . $db_config['prefix'] . '_' . $module_data .'_discounts';
            $nvs_discounts_config = array();
            $result = $db->query($sql);
            while ($row = $result->fetch()) {
                $nvs_discounts_config[$row['did']] = array(
                    'title' => $row['title'],
                    'begin_time' => $row['begin_time'],
                    'end_time' => $row['end_time'],
                    'config' => unserialize($row['config'])
                );
            }
            $result->closeCursor();
            $return = array();
            $discount_percent = 0;
            $discount_unit = '';
            $discount = 0;

            $module_data = !empty($module) ? $site_mods[$module]['module_data'] : $module_data;
            $module_file = !empty($module) ? $site_mods[$module]['module_file'] : $module_file;
            $product = $db->query('SELECT listcatid, product_price, money_unit, price_config, discount_id FROM ' . $db_config['prefix'] . '_' . $module_data . '_rows WHERE id = ' . $pro_id)->fetch();
            $price = $product['product_price'];

            if (!$per_pro) {
                $price = $price * $number;
            }

            $r = $nvs_money_config[$product['money_unit']]['round'];
            $decimals = nv_systems_get_decimals($currency_convert);
            //die($decimals."/");
            if ($r > 1) {
                $price = round($price / $r) * $r;
            } else {
                $price = round($price, $decimals);
            }
            if ($nv_systems_shops_array_cat[$product['listcatid']]['typeprice'] == 2) {
                $_price_config = unserialize($product['price_config']);
                if (!empty($_price_config)) {
                    foreach ($_price_config as $_p) {
                        if ($number <= $_p['number_to']) {
                            $price = $_p['price'] * (!$per_pro ? $number : 1);
                            break;
                        }
                    }
                }
            } elseif ($nv_systems_shops_array_cat[$product['listcatid']]['typeprice'] == 1) {
                if (isset($nvs_discounts_config[$product['discount_id']])) {
                    $_config = $nvs_discounts_config[$product['discount_id']];
                    if ($_config['begin_time'] < NV_CURRENTTIME and ($_config['end_time'] > NV_CURRENTTIME or empty($_config['end_time']))) {
                        foreach ($_config['config'] as $_d) {
                            if ($_d['discount_from'] <= $number and $_d['discount_to'] >= $number) {
                                $discount_percent = $_d['discount_number'];
                                if ($_d['discount_unit'] == 'p') {
                                    $discount_unit = '%';
                                    $discount = ($price * ($discount_percent / 100));
                                } else {
                                    $discount_unit = ' ' . $pro_config['money_unit'];
                                    $discount = $discount_percent * $number;
                                }
                                break;
                            }
                        }
                    }
                }
            }

            $price = nv_systems_currency_conversion($price, $product['money_unit'], $currency_convert);

            $return['price'] = $price; // Giá sản phẩm chưa format
            $return['price_format'] = nv_systems_number_format($price, $decimals); // Giá sản phẩm đã format

            $return['discount'] = $discount; // Số tiền giảm giá sản phẩm chưa format
            $return['discount_format'] = nv_systems_number_format($discount, $decimals); // Số tiền giảm giá sản phẩm đã format
            $return['discount_percent'] = $discount_unit == '%' ? $discount_percent : nv_systems_number_format($discount_percent, $decimals); // Giảm giá theo phần trăm
            $return['discount_unit'] = $discount_unit; // Đơn vị giảm giá

            $return['sale'] = $price - $discount; // Giá bán thực tế của sản phẩm
            $return['sale_format'] = nv_systems_number_format($return['sale'], $decimals); // Giá bán thực tế của sản phẩm đã format
            $return['unit'] = $nvs_money_config[$currency_convert]['symbol'];

            return $return;
        }

        function nv_systems_currency_conversion($price, $currency_curent, $currency_convert)
        {
            global $nv_Cache, $db, $db_config, $module_config, $module_data, $module_name;
            $pro_config = $module_config[$module_name];

            // Lay ty gia ngoai te
            $sql = 'SELECT code, currency,symbol, exchange, round, number_format FROM ' . $db_config['prefix'] . '_' . $module_data .'_money_' . NV_LANG_DATA;
            $nvs_money_config = array();
            $result = $db->query($sql);
            while ($row = $result->fetch()) {
                $nvs_money_config[$row['code']] = array(
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

            if ($currency_curent == $pro_config['money_unit']) {
                $price = $price / $nvs_money_config[$currency_convert]['exchange'];
            } elseif ($currency_convert == $pro_config['money_unit']) {
                $price = $price * $nvs_money_config[$currency_curent]['exchange'];
            }

            return $price;
        }

        function nv_systems_number_format($number, $decimals = 0)
        {
            global $nv_Cache, $db, $db_config, $module_config, $module_data, $module_name;
            $pro_config = $module_config[$module_name];
            // Lay ty gia ngoai te
            $sql = 'SELECT code, currency,symbol, exchange, round, number_format FROM ' . $db_config['prefix'] . '_' . $module_data .'_money_' . NV_LANG_DATA;
            $nvs_money_config = array();
            $result = $db->query($sql);
            while ($row = $result->fetch()) {
                $nvs_money_config[$row['code']] = array(
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
            $number_format = explode('||', $nvs_money_config[$pro_config['money_unit']]['number_format']);
            $str = number_format($number, $decimals, $number_format[0], $number_format[1]);

            return $str;
        }

        function nv_systems_get_decimals($currency_convert)
        {
            global $nv_Cache, $db, $db_config, $module_config, $module_data, $module_name;
            $pro_config = $module_config[$module_name];

            // Lay ty gia ngoai te
            $sql = 'SELECT code, currency,symbol, exchange, round, number_format FROM ' . $db_config['prefix'] . '_' . $module_data .'_money_' . NV_LANG_DATA;
            $nvs_money_config = array();
            $result = $db->query($sql);
            while ($row = $result->fetch()) {
                $nvs_money_config[$row['code']] = array(
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

            $r = $nvs_money_config[$currency_convert]['round'];
            $decimals = 0;
            if ($r <= 1) {
                $decimals = $nvs_money_config[$currency_convert]['decimals'];
            }
            return $decimals;
        }
    }
    if (!nv_function_exists('nv_get_price_tmp')) {

        function nv_get_price_tmp($module_name, $module_data, $module_file, $pro_id)
        {
            global $nv_Cache, $db, $db_config, $module_config;

            $price = array();
            $pro_config = $module_config[$module_name];

            $price = nv_systems_get_price($pro_id, $pro_config['money_unit'], 1, false, $module_name);

            return $price;
        }
    }
//     if (!nv_function_exists('NVSGetGroupID')) {

//         function NVSGetGroupID($pro_id, $group_by_parent = 0, $module)
//         {
//             global $db, $db_config, $module_data ;

//             $sql = 'SELECT groupid, parentid, lev, ' . NV_LANG_DATA . '_title AS title, ' . NV_LANG_DATA . '_alias AS alias, viewgroup, numsubgroup, subgroupid, ' . NV_LANG_DATA . '_description AS description, inhome, indetail, in_order, ' . NV_LANG_DATA . '_keywords AS keywords, numpro, image, is_require FROM ' . $db_config['prefix'] . '_shops_group ORDER BY sort ASC';

//             $nv_systems_shops_array_group_sql = $db->query($sql);

//             while ($row = $nv_systems_shops_array_group_sql->fetch()) {
//                 $nv_systems_shops_array_group[$row['groupid']] = $row;
//             }
//             $data = array();
//             $result = $db->query('SELECT group_id FROM ' . $db_config['prefix'] . '_shops_group_items where pro_id=' . $pro_id);
//             while ($row = $result->fetch()) {
//                 if ($group_by_parent) {
//                     $parentid = $nv_systems_shops_array_group[$row['group_id']]['parentid'];
//                     $data[$parentid][] = $row['group_id'];
//                 } else {
//                     $data[] = $row['group_id'];
//                 }
//             }
//             return $data;
//         }
//     }

    function nv_block_shops_block_cat_tabs($block_config)
    {
        global $nv_Cache, $shops_array_cat, $module_info, $site_mods, $module_config, $global_config, $db, $db_config, $lang_module;

        $module = $block_config['module'];

        $module_data = $site_mods[$module]['module_data'];
        $module_file = $site_mods[$module]['module_file'];

        if (file_exists(NV_ROOTDIR . '/modules/' . $module_file . '/language/vi.php')) {
            require_once NV_ROOTDIR . '/modules/' . $module_file . '/language/vi.php';
        }
        // Groups
        $sql = 'SELECT groupid, parentid, lev, ' . NV_LANG_DATA . '_title AS title, ' . NV_LANG_DATA . '_alias AS alias, viewgroup, numsubgroup, subgroupid, ' . NV_LANG_DATA . '_description AS description, inhome, indetail, in_order, ' . NV_LANG_DATA . '_keywords AS keywords, numpro, image, is_require FROM ' . $db_config['prefix'] . '_' . $module_data . '_group ORDER BY sort ASC';

        $nv_systems_shops_array_group_sql = $db->query($sql);
        while ($row = $nv_systems_shops_array_group_sql->fetch()) {
            $nv_systems_shops_array_group[] = $row;
        }
        $pro_config = $module_config[$module];

        $show_no_image = $block_config['show_no_image'];
        $blockwidth = $block_config['blockwidth'];
        $blockheight = $block_config['blockheight'];
        if (empty($block_config['blockid'])) return '';

        $blockid = implode(',', $block_config['blockid']);
        if (file_exists(NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file . '/global.block_shops_cat_tabs.tpl')) {
            $block_theme = $global_config['module_theme'];
        } else {
            $block_theme = 'default';
        }
        $xtpl = new XTemplate('global.block_shops_cat_tabs.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/' . $module_file);
        $xtpl->assign('TEMPLATE', $block_theme);
        $xtpl->assign('LANG', $lang_module);
        $xtpl->assign('CONFIG', $block_config);
        $xtpl->assign('MODULE_LINK', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=shops');

        $n = 0;
        $sql = 'SELECT catid,image, ' . NV_LANG_DATA . '_title as title, ' . NV_LANG_DATA . '_descriptionhtml as descript, ' . NV_LANG_DATA . '_alias FROM ' . $db_config['prefix'] . '_' . $module_data . '_catalogs WHERE catid IN ( ' . $blockid . ' ) ORDER BY weight ASC';
        $result = $db->query($sql);
        while ($data = $result->fetch()) {
            $n++;
            if ($n == 1) {
                $data['active'] = 'active';
            }
            $data['image'] = NV_BASE_SITEURL . NV_FILES_DIR . '/' . $site_mods[$module]['module_upload'] . '/' . $data['image'];

            $xtpl->assign('BLOCK_INFO', $data);
            $xtpl->parse('main.group_info');
            $xtpl->parse('grid.group_info');

            if (!empty($data['descript'])) {
                $xtpl->parse('main.group_content.descriptionhtml');
                $xtpl->parse('grid.group_content.descriptionhtml');
            }
            $db->sqlreset()
                ->select('t1.id, t1.listcatid, t1.' . NV_LANG_DATA . '_title as title, t1.' . NV_LANG_DATA . '_alias as alias, t1.homeimgfile, t1.homeimgthumb,t1.' . NV_LANG_DATA . '_hometext as hometext,t1.publtime, t1.showprice, t1.discount_id, t1.product_number, t1.money_unit, t1.product_code, t3.newday')
                ->from($db_config['prefix'] . '_' . $module_data . '_rows t1')
                ->join('INNER JOIN ' . $db_config['prefix'] . '_' . $module_data . '_catalogs t3 ON t3.catid = t1.listcatid')
                ->where('t3.catid= ' . $data['catid'] . ' AND t1.status= 1')
                ->order('t1.id DESC')
                ->limit($block_config['numrow']);

            $list = $db->query($db->sql());

            if (!empty($list)) {
                while ($l = $list->fetch()) {

                    $l['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $data[NV_LANG_DATA . '_alias'] . '/' . $l['alias'] . $global_config['rewrite_exturl'];

                    if ($l['homeimgthumb'] == 1) {
                        $l['thumb'] = NV_BASE_SITEURL . NV_FILES_DIR . '/' . $site_mods[$module]['module_upload'] . '/' . $l['homeimgfile'];
                    } elseif ($l['homeimgthumb'] == 2) {
                        $l['thumb'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $site_mods[$module]['module_upload'] . '/' . $l['homeimgfile'];
                    } elseif ($l['homeimgthumb'] == 3) {
                        $l['thumb'] = $l['homeimgfile'];
                    } elseif (!empty($show_no_image)) {
                        $l['thumb'] = NV_BASE_SITEURL . $show_no_image;
                    } else {
                        $l['thumb'] = '';
                    }
                    $l['catid'] = $data['catid'];
                    $l['blockwidth'] = $blockwidth;
                    $l['blockheight'] = $blockheight;
                    $l['title'] = nv_clean60($l['title'], $block_config['title_length']);

                    $xtpl->assign('ROW', $l);

                    if (!empty($l['thumb'])) $xtpl->parse('main.group_content.loop.img');
                    if (!empty($l['thumb'])) $xtpl->parse('grid.group_content.loop.img');
                    $newday = $l['publtime'] + (86400 * $l['newday']);
                    if ($newday >= NV_CURRENTTIME) {
                        $xtpl->parse('main.group_content.loop.new');
                        $xtpl->parse('grid.group_content.loop.new');
                    }
                    if (!empty($pro_config['show_product_code']) and !empty($l['product_code'])) {
                        $xtpl->parse('main.group_content.loop.product_code');
                        $xtpl->parse('grid.group_content.loop.product_code');
                    }
                    if ($pro_config['active_order'] == '1' and $pro_config['active_order_non_detail'] == '1') {
                        if ($l['showprice'] == '1') {
                            if ($l['product_number'] > 0) {
                                // Kiem tra nhom bat buoc chon khi dat hang
//                                 $listgroupid = NVSGetGroupID($l['id']);
                                $group_requie = 0;
                                if (!empty($listgroupid) and !empty($nv_systems_shops_array_group)) {
                                    foreach ($nv_systems_shops_array_group as $groupinfo) {
                                        if ($groupinfo['in_order']) {
                                            $group_requie = 1;
                                            break;
                                        }
                                    }
                                }
                                $group_requie = $pro_config['active_order_popup'] ? 1 : $group_requie;
                                $xtpl->assign('GROUP_REQUIE', $group_requie);
                                $xtpl->parse('main.group_content.loop.order');
                                $xtpl->parse('grid.group_content.loop.order');
                            } else {
                                $xtpl->parse('main.group_content.loop.product_empty');
                                $xtpl->parse('grid.group_content.loop.product_empty');
                            }
                        }
                    }

                    if ($pro_config['active_price'] == '1') {
                        if ($l['showprice'] == '1') {
                            $price = nv_get_price_tmp($module, $module_data, $module_file, $l['id']);
                            $xtpl->assign('PRICE', $price);
                            $xtpl->assign('discount_id', $l['discount_id']);
                            $xtpl->assign('discount_percent', $price['discount_percent']);
                            if ($l['discount_id'] and $price['discount_percent'] > 0) {
                                $xtpl->parse('main.group_content.loop.price.discounts');
                                $xtpl->parse('main.group_content.loop.discounts');
                                $xtpl->parse('grid.group_content.loop.price.discounts');
                                $xtpl->parse('grid.group_content.loop.discounts');
                            } else {
                                $xtpl->parse('main.group_content.loop.price.no_discounts');
                                $xtpl->parse('grid.group_content.loop.price.no_discounts');
                            }
                            $xtpl->parse('main.group_content.loop.price');
                            $xtpl->parse('grid.group_content.loop.price');
                        } else {
                            $xtpl->parse('main.group_content.loop.contact');
                            $xtpl->parse('grid.group_content.loop.contact');
                        }
                    }
                    // San pham yeu thich
                    if ($pro_config['active_wishlist']) {
                        if (!empty($array_wishlist_id)) {
                            if (in_array($l['id'], $array_wishlist_id)) {
                                $xtpl->parse('main.group_content.loop.wishlist.disabled');
                                $xtpl->parse('grid.group_content.loop.wishlist.disabled');
                            }
                        }
                        $xtpl->parse('main.group_content.loop.wishlist');
                        $xtpl->parse('grid.group_content.loop.wishlist');
                    }
                    $xtpl->parse('main.group_content.loop');
                    $xtpl->parse('grid.group_content.loop');
                }
                $xtpl->parse('main.group_content');
                $xtpl->parse('grid.group_content');
            }
        }

        if ($block_config['display_type'] == 1) {
            $xtpl->parse('grid');
            return $xtpl->text('grid');
        } else {
            $xtpl->parse('main');
            return $xtpl->text('main');
        }
    }
}
if (defined('NV_SYSTEM')) {
    global $nv_Cache, $site_mods, $module_name, $global_array_cat, $shops_array_cat;
    $content = nv_block_shops_block_cat_tabs($block_config);
}
