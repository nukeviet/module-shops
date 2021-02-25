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

if (!function_exists('nv_product_top_catalogs')) {

    if (!function_exists('nv_resize_crop_images')) {

        function nv_resize_crop_images($img_path, $width, $height, $module_name = '', $id = 0)
        {
            $new_img_path = str_replace(NV_ROOTDIR, '', $img_path);
            if (file_exists($img_path)) {
                $imginfo = nv_is_image($img_path);
                $basename = basename($img_path);
                $basename = preg_replace('/^\W+|\W+$/', '', $basename);
                $basename = preg_replace('/[ ]+/', '_', $basename);
                $basename = strtolower(preg_replace('/\W-/', '', $basename));
                if ($imginfo['width'] > $width or $imginfo['height'] > $height) {
                    $basename = preg_replace('/(.*)(\.[a-zA-Z]+)$/', $module_name . '_' . $id . '_\1_' . $width . '-' . $height . '\2', $basename);
                    if (file_exists(NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $basename)) {
                        $new_img_path = NV_BASE_SITEURL . NV_TEMP_DIR . '/' . $basename;
                    } else {
                        $img_path = new NukeViet\Files\Image($img_path, NV_MAX_WIDTH, NV_MAX_HEIGHT);

                        $thumb_width = $width;
                        $thumb_height = $height;
                        $maxwh = max($thumb_width, $thumb_height);
                        if ($img_path->fileinfo['width'] > $img_path->fileinfo['height']) {
                            $width = 0;
                            $height = $maxwh;
                        } else {
                            $width = $maxwh;
                            $height = 0;
                        }

                        $img_path->resizeXY($width, $height);
                        $img_path->cropFromCenter($thumb_width, $thumb_height);
                        $img_path->save(NV_ROOTDIR . '/' . NV_TEMP_DIR, $basename);
                        if (file_exists(NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $basename)) {
                            $new_img_path = NV_BASE_SITEURL . NV_TEMP_DIR . '/' . $basename;
                        }
                    }
                }
            }
            return $new_img_path;
        }
    }

    /**
     * nv_block_config_product_top_catalogs_blocks()
     *
     * @param mixed $module
     * @param mixed $data_block
     * @param mixed $lang_block
     * @return
     */
    function nv_block_config_product_top_catalogs_blocks($module, $data_block, $lang_block)
    {
        global $nv_Cache, $db_config, $site_mods;
        $array_style = array(
            'main_1' => $lang_block['style_1'],
            'main_2' => $lang_block['style_2'],
            'main_3' => $lang_block['style_3'],
            'main_4' => $lang_block['style_4']
        );
        $module_data = $site_mods[$module]['module_data'];

        $html = "";

        $html = '<div class="form-group">';
        $html .= '<label class="control-label col-sm-6">' . $lang_block['catid'] . ':</label>';

        $sql = 'SELECT catid, parentid, lev, ' . NV_LANG_DATA . '_title AS title, ' . NV_LANG_DATA . '_alias AS alias, viewcat, numsubcat, subcatid, numlinks, ' . NV_LANG_DATA . '_description AS description, inhome, ' . NV_LANG_DATA . '_keywords AS keywords, groups_view, sort FROM ' . $db_config['prefix'] . '_' . $module_data . '_catalogs ORDER BY sort ASC';

        $list = $nv_Cache->db($sql, '', $module);

        $html .= '<div class="col-sm-18">';
        foreach ($list as $l) {
            if ($l['inhome'] == 1) {
                $xtitle_i = '';

                if ($l['lev'] > 0) {
                    for ($i = 1; $i <= $l['lev']; ++$i) {
                        $xtitle_i .= '&nbsp;&nbsp;&nbsp;&nbsp;';
                    }
                }
                $html .= $xtitle_i . '<label><input type="checkbox" name="config_catid[]" value="' . $l['catid'] . '" ' . ((is_array($data_block['catid']) and in_array($l['catid'], $data_block['catid'])) ? ' checked="checked"' : '') . '</input>' . $l['title'] . '</label><br />';
            }
        }
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="form-group">';
        $html .= '	<label class="control-label col-sm-6">' . $lang_block['style'] . ':</label>';
        $html .= '	<div class="col-sm-9">	<select name="config_style" class="form-control">';
        foreach ($array_style as $index => $value) {
            $sl = $index == $data_block['style'] ? 'selected="selelcted"' : '';
            $html .= '<option value="' . $index . '" ' . $sl . '>' . $value . '</option>';
        }
        $html .= '		</select></div>';
        $html .= '</div>';

        $html .= "<div class=\"form-group\">";
        $html .= "	<label class=\"control-label col-sm-6\">" . $lang_block['cut_num'] . "</label>";
        $html .= "	<div class=\"col-sm-18\"><input class=\"form-control w150\" type=\"text\" name=\"config_cut_num\" size=\"5\" value=\"" . $data_block['cut_num'] . "\"/></div>";
        $html .= "</div>";
        $html .= "<div class=\"form-group\">";
        $html .= "	<label class=\"control-label col-sm-6\">" . $lang_block['item_perline'] . "</label>";
        $html .= "	<div class=\"col-sm-18\"><input class=\"form-control w150\" type=\"text\" name=\"config_item_perline\" size=\"5\" value=\"" . $data_block['item_perline'] . "\"/></div>";
        $html .= "</div>";
        $html .= "<div class=\"form-group\">";
        $html .= "	<label class=\"control-label col-sm-6\">" . $lang_block['img_width'] . "</label>";
        $html .= "	<div class=\"col-sm-18\"><input class=\"form-control w150\" type=\"text\" name=\"config_img_width\" size=\"5\" value=\"" . $data_block['img_width'] . "\"/></div>";
        $html .= "</div>";
        $html .= "<div class=\"form-group\">";
        $html .= "	<label class=\"control-label col-sm-6\">" . $lang_block['img_height'] . "</label>";
        $html .= "	<div class=\"col-sm-18\"><input class=\"form-control w150\" type=\"text\" name=\"config_img_height\" size=\"5\" value=\"" . $data_block['img_height'] . "\"/></div>";
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
     * nv_block_config_product_top_catalogs_blocks_submit()
     *
     * @param mixed $module
     * @param mixed $lang_block
     * @return
     */
    function nv_block_config_product_top_catalogs_blocks_submit($module, $lang_block)
    {
        global $nv_Request;
        $return = array();
        $return['error'] = array();
        $return['config'] = array();
        $return['config']['catid'] = $nv_Request->get_array('config_catid', 'post', array());
        $return['config']['cut_num'] = $nv_Request->get_int('config_cut_num', 'post', 0);
        $return['config']['item_perline'] = $nv_Request->get_int('config_item_perline', 'post', 4);
        $return['config']['img_width'] = $nv_Request->get_int('config_img_width', 'post', 180);
        $return['config']['img_height'] = $nv_Request->get_int('config_img_height', 'post', 180);
        $return['config']['style'] = $nv_Request->get_title('config_style', 'post', 'main_1');
        $return['config']['text'] = $nv_Request->get_string('config_text', 'post', '');
        return $return;
    }

    /**
     * nv_product_top_catalogs()
     *
     * @param mixed $block_config
     * @return
     */
    function nv_product_top_catalogs($block_config)
    {
        global $nv_Cache, $site_mods, $global_config, $module_config, $module_name, $module_info, $global_array_shops_cat, $db, $db_config, $array_cat_shops;

        $module = $block_config['module'];
        $mod_data = $site_mods[$module]['module_data'];
        $mod_file = $site_mods[$module]['module_file'];
        $mod_upload = $site_mods[$module]['module_upload'];
        $pro_config = $module_config[$module];
        $array_cat_shops = array();
        $catid = implode(',', $block_config['catid']);
        $style = $block_config['style'];
        $cut_num = $block_config['cut_num'];
        $html = "";
        $i = 0;

        if (file_exists(NV_ROOTDIR . "/themes/" . $global_config['module_theme'] . "/modules/" . $mod_file . "/block.shops_catalogs.tpl")) {
            $block_theme = $global_config['module_theme'];
        } else {
            $block_theme = "default";
        }

        $xtpl = new XTemplate('block.shops_catalogs.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/' . $mod_file);
        $xtpl->assign('TEMPLATE', $block_theme);
        $xtpl->assign('DATA', $block_config);

        $sql = "SELECT catid, parentid, lev, " . NV_LANG_DATA . "_title AS title, " . NV_LANG_DATA . "_alias AS alias, viewcat, numsubcat, subcatid, numlinks, " . NV_LANG_DATA . "_description AS description, inhome, " . NV_LANG_DATA . "_keywords AS keywords, groups_view, image FROM " . $db_config['prefix'] . "_" . $mod_data . "_catalogs WHERE catid IN ($catid) ORDER BY sort ASC";
        $array_cat_shops = $nv_Cache->db($sql, "catid", $module);

        foreach ($array_cat_shops as $cat) {

            if ($cat['inhome'] == '1') {

                if ($i == 0) {

                    $cat['title0'] = nv_clean60($cat['title'], $block_config['cut_num']);
                    if (!empty($cat['image']) && file_exists(NV_ROOTDIR . '/' . NV_ASSETS_DIR . '/' . $mod_upload . '/' . $cat['image'])) {
                        $cat['image'] = nv_resize_crop_images(NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $mod_upload . '/' . $cat['image'], $block_config['img_width'], $block_config['img_height'], $module);
                    } else {
                        $cat['image'] = NV_BASE_SITEURL . 'themes/' . $block_theme . '/images/shops/no-image.jpg';
                    }

                    $xtpl->assign('CAT', $cat);
                    $xtpl->parse('main.' . $style . '.temp_main');
                } else {

                    $cat['title0'] = nv_clean60($cat['title'], $block_config['cut_num']);
                    if (!empty($cat['image']) && file_exists(NV_ROOTDIR . '/' . NV_ASSETS_DIR . '/' . $mod_upload . '/' . $cat['image'])) {
                        $cat['image'] = nv_resize_crop_images(NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $mod_upload . '/' . $cat['image'], $block_config['img_width'], $block_config['img_height'], $module);
                    } else {
                        $cat['image'] = NV_BASE_SITEURL . 'themes/' . $block_theme . '/images/shops/no-image.jpg';
                    }
                    $xtpl->assign('CAT', $cat);
                    $xtpl->parse('main.' . $style . '.temp_other');
                }
            }

            $i++;
        }
        $xtpl->parse('main.' . $style);
        $xtpl->parse('main');
        return $xtpl->text('main');
    }

/**
 * html_viewsub()
 *
 * @param mixed $list_sub
 * @param mixed $block_config
 * @return
 */
}

if (defined('NV_SYSTEM')) {
    $content = nv_product_top_catalogs($block_config);
}
