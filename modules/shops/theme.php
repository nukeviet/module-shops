<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2017 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 04/18/2017 09:47
 */

if (!defined('NV_IS_MOD_SHOPS')) {
    die('Stop!!!');
}

/**
 * redict_link()
 *
 * @param mixed $lang_view
 * @param mixed $lang_back
 * @param mixed $nv_redirect
 * @return
 */
function redict_link($lang_view, $lang_back, $nv_redirect)
{
    global $global_config;
    $nv_redirect = nv_url_rewrite($nv_redirect, true);
    $contents = "<div class=\"alert alert-info frame\">";
    $contents .= $lang_view . "<br /><br />\n";
    $contents .= "<img border=\"0\" src=\"" . NV_STATIC_URL . NV_ASSETS_DIR . "/images/load_bar.gif\"><br /><br />\n";
    $contents .= "<a href=\"" . $nv_redirect . "\">" . $lang_back . "</a>";
    $contents .= "</div>";
    $contents .= "<meta http-equiv=\"refresh\" content=\"2;url=" . $nv_redirect . "\" />";
    include NV_ROOTDIR . '/includes/header.php';
    echo nv_site_theme($contents);
    include NV_ROOTDIR . '/includes/footer.php';
}

/**
 * draw_option_select_number()
 *
 * @param integer $select
 * @param integer $begin
 * @param integer $end
 * @param integer $step
 * @return
 */
function draw_option_select_number($select = -1, $begin = 0, $end = 100, $step = 1)
{
    $html = '';
    for ($i = $begin; $i < $end; $i = $i + $step) {
        if ($i == $select) {
            $html .= "<option value=\"" . $i . "\" selected=\"selected\">" . $i . "</option>";
        } else {
            $html .= "<option value=\"" . $i . "\">" . $i . "</option>";
        }
    }
    return $html;
}

/**
 * nv_template_view_home()
 *
 * @param mixed $array_data
 * @param mixed $compare_id
 * @param string $pages
 * @param string $sort
 * @param string $viewtype
 * @return
 */
function nv_template_view_home($array_data, $compare_id, $pages = '', $sort = 0, $viewtype = 'viewgrid')
{
    global $module_info, $lang_module, $module_name, $module_file, $pro_config, $array_wishlist_id, $global_array_shops_cat, $global_array_group, $my_head;

    $xtpl = new XTemplate('main.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('MODULE_NAME', $module_name);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('TEMPLATE', $module_info['template']);

    if ($pro_config['home_data'] == 'all') {
        if (function_exists('nv_template_' . $viewtype)) {
            $xtpl->assign('CONTENT', call_user_func('nv_template_' . $viewtype, $array_data, $pages));
        }
        $xtpl->parse('main.viewall');
    } elseif ($pro_config['home_data'] == 'cat' || $pro_config['home_data'] == 'group') {
        $xtpl->assign('CONTENT', nv_template_main_cat($array_data, $pages, $viewtype));
        $xtpl->parse('main.viewcat');
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * @param array $array_data
 * @param string $pages
 * @param string $viewtype
 * @return string
 */
function nv_template_main_cat($array_data, $pages = '', $viewtype = 'viewgrid')
{
    global $module_info, $module_file, $lang_module, $lang_global, $global_array_shops_cat;

    $xtpl = new XTemplate('main_cat.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('GLANG', $lang_global);
    $xtpl->assign('LANG', $lang_module);

    if (!empty($array_data)) {
        foreach ($array_data as $data_row) {
            if ($data_row['num_pro'] > 0) {
                $xtpl->assign('TITLE_CATALOG', $data_row['title']);
                $xtpl->assign('LINK_CATALOG', $data_row['link']);
                $xtpl->assign('NUM_PRO', $data_row['num_pro']);

                if (function_exists('nv_template_' . $viewtype)) {
                    $xtpl->assign('CONTENT', call_user_func('nv_template_' . $viewtype, $data_row['data'], $pages));
                }

                if (!empty($data_row['subcatid'])) {
                    $data_row['subcatid'] = explode(',', $data_row['subcatid']);
                    foreach ($data_row['subcatid'] as $subcatid) {
                        $items = $global_array_shops_cat[$subcatid];
                        if ($items['inhome']) {
                            $xtpl->assign('SUBCAT', $global_array_shops_cat[$subcatid]);
                            $xtpl->parse('main.loop.subcatloop');
                        }
                    }
                }

                if ($data_row['num_pro'] > $data_row['num_link']) {
                    $xtpl->parse('main.loop.view_next');
                }
                $xtpl->parse('main.loop');
            }
        }
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * nv_template_view_blockcat()
 *
 * @param mixed $data_content
 * @param string $html_pages
 * @return
 */
function nv_template_view_blockcat($array_data, $data_content, $html_pages = '', $viewtype = 'viewgrid')
{
    global $module_info, $lang_module, $module_name, $module_file, $pro_config, $array_wishlist_id, $global_array_shops_cat, $global_array_blockcat, $my_head;

    $xtpl = new XTemplate('blockcat.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('TEMPLATE', $module_info['template']);
    $xtpl->assign('MODULE_NAME', $module_name);
    $xtpl->assign('TITLE', $array_data['title']);
    $xtpl->assign('DESCRIPTION', $array_data['description']);

    if (!empty($array_data['bodytext'])) {
        $xtpl->assign('BODYTEXT', $array_data['bodytext']);
        $xtpl->parse('main.bodytext');
    }

    if (!empty($array_data['image'])) {
        $xtpl->assign('IMAGE', $array_data['image']);
        $xtpl->parse('main.image');
    }

    if (function_exists('nv_template_' . $viewtype)) {
        $xtpl->assign('CONTENT', call_user_func('nv_template_' . $viewtype, $data_content, $html_pages));
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * view_search_all()
 *
 * @param mixed $data_content
 * @param string $html_pages
 * @return
 */
function view_search_all($data_content, $compare_id, $html_pages = '', $viewtype = 'viewgrid')
{
    global $module_info, $lang_module, $module_file, $pro_config, $array_wishlist_id, $global_array_shops_cat, $global_array_group;

    $xtpl = new XTemplate('search_all.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('TEMPLATE', $module_info['template']);

    if (function_exists('nv_template_' . $viewtype)) {
        $xtpl->assign('CONTENT', call_user_func('nv_template_' . $viewtype, $data_content, $html_pages));
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * @param array $data_content
 * @param array $data_unit
 * @param array $data_others
 * @param array $array_other_view
 * @param string $content_comment
 * @param integer $compare_id
 * @param boolean $popup
 * @param array $idtemplates
 * @param array $array_keyword
 * @return string
 */
function nv_template_detail($data_content, $data_unit, $data_others, $array_other_view, $content_comment, $compare_id, $popup, $idtemplates, $array_keyword)
{
    global $module_info, $lang_module, $module_file, $module_name, $module_upload, $pro_config, $global_config, $global_array_group, $array_wishlist_id, $client_info, $global_array_shops_cat, $meta_property, $pro_config, $user_info, $discounts_config, $my_head, $my_footer;

    $link = NV_STATIC_URL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=';
    $link2 = NV_STATIC_URL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=';

    $xtpl = new XTemplate('detail.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('GLANG', $lang_module);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('MODULE', $module_name);
    $xtpl->assign('MODULE_FILE', $module_file);
    $xtpl->assign('TEMPLATE', $module_info['template']);
    $xtpl->assign('SELFURL', $client_info['selfurl']);
    $xtpl->assign('POPUP', $popup);

    $xtpl->assign('LINK_LOAD', NV_STATIC_URL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=loadcart');
    $xtpl->assign('THEME_URL', NV_STATIC_URL . 'themes/' . $module_info['template']);
    $xtpl->assign('LINK_PRINT', NV_STATIC_URL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=print_pro&id=' . $data_content['id']);

    if (!empty($data_content)) {
        $xtpl->assign('proid', $data_content['id']);
        $xtpl->assign('CAT_TITLE', $global_array_shops_cat[$data_content['listcatid']]['title']);
        $xtpl->assign('SRC_PRO_FULL', $global_config['site_url'] . $data_content['homeimgthumb']);
        $xtpl->assign('TITLE', $data_content[NV_LANG_DATA . '_title']);

        if (!empty($data_content['product_weight'])) {
            $xtpl->assign('PRODUCT_WEIGHT', $data_content['product_weight']);
            $xtpl->assign('WEIGHT_UNIT', $data_content['weight_unit']);
            $xtpl->parse('main.product_weight');
        }

        $xtpl->assign('PRO_FULL_LINK', $data_content['full_link']);
        $xtpl->assign('NUM_VIEW', $data_content['hitstotal']);
        $xtpl->assign('DATE_UP', $lang_module['detail_dateup'] . ' ' . nv_date('d-m-Y h:i:s A', $data_content['publtime']));
        $xtpl->assign('PRICEVALIDUNTIL', nv_date('Y-m-d', $data_content['publtime']));
        $xtpl->assign('DETAIL', $data_content[NV_LANG_DATA . '_bodytext']);
        $xtpl->assign('LINK_ORDER', $link2 . 'setcart&id=' . $data_content['id']);
        $price = nv_get_price($data_content['id'], $pro_config['money_unit']);
        $xtpl->assign('PRICE', $price);
        $xtpl->assign('PRODUCT_CODE', $data_content['product_code']);
        $xtpl->assign('PRODUCT_NUMBER', $data_content['product_number']);
        $xtpl->assign('pro_unit', $data_unit['title']);

        if ($data_content['product_number'] < 1) {
            $xtpl->assign('AVAILABILITY', 'https://schema.org/OutOfStock');
        } else {
            $xtpl->assign('AVAILABILITY', 'https://schema.org/InStock');
        }
        if (empty($data_content['homeimgalt'])) {
            $data_content['homeimgalt'] = $data_content[NV_LANG_DATA . '_title'];
        }
        $xtpl->assign('DATA', $data_content);

        // Xuất ảnh sản phẩm
        $num_images = sizeof($data_content['image']);
        if ($num_images == 1) {
            // Sản phẩm có 1 ảnh
            $xtpl->assign('IMAGE', current($data_content['image']));
            $xtpl->parse('main.oneimage');
        } elseif ($num_images > 1) {
            // Gallery ảnh
            $stt = 0;
            foreach ($data_content['image'] as $image) {
                $xtpl->assign('IMAGE_STT', $stt++);
                $xtpl->assign('IMAGE', $image);
                $xtpl->parse('main.image.loop');
                if ($stt == 1) {
                    $xtpl->parse('main.image.loop1.active');
                }
                $xtpl->parse('main.image.loop1');
            }
            $xtpl->parse('main.image');
        }

        if ($pro_config['active_gift'] and !empty($data_content[NV_LANG_DATA . '_gift_content']) and NV_CURRENTTIME >= $data_content['gift_from'] and NV_CURRENTTIME <= $data_content['gift_to']) {
            $xtpl->assign('gift_content', $data_content[NV_LANG_DATA . '_gift_content']);
            $xtpl->parse('main.gift');
        }

        // Hien thi du lieu tuy bien o phan gioi thieu
        if (!empty($data_content['array_custom']) and !empty($data_content['array_custom_lang'])) {
            $custom_data = nv_custom_tpl('tab-introduce' . '.tpl', $data_content['array_custom'], $data_content['array_custom_lang'], $idtemplates);
            $xtpl->assign('CUSTOM_DATA', $custom_data);
            $xtpl->parse('main.custom_data');
        }

        // San pham yeu thich
        if ($pro_config['active_wishlist']) {
            if (!empty($array_wishlist_id)) {
                if (in_array($data_content['id'], $array_wishlist_id)) {
                    $xtpl->parse('main.wishlist.disabled');
                }
            }
            $xtpl->parse('main.wishlist');
        }

        $exptime = ($data_content['exptime'] != 0) ? date('d-m-Y', $data_content['exptime']) : 'N/A';
        $xtpl->assign('exptime', $exptime);
        $xtpl->assign('height', $pro_config['homeheight']);
        $xtpl->assign('width', $pro_config['homewidth']);

        if ($pro_config['active_showhomtext'] == '1') {
            $xtpl->assign('hometext', $data_content[NV_LANG_DATA . '_hometext']);
            $xtpl->parse('main.hometext');
        }

        if (!$popup) {
            // Hien thi tabs
            if (!empty($data_content['tabs'])) {
                $i = 0;
                foreach ($data_content['tabs'] as $tabs_id => $tabs_value) {
                    $tabs_content = '';
                    $tabs_key = $tabs_value['content'];

                    if ($tabs_key == 'content_detail') {
                        // Chi tiết sản phẩm
                        $tabs_content = $data_content[NV_LANG_DATA . '_bodytext'];
                    } elseif ($tabs_key == 'content_download' and $pro_config['download_active'] == 1) {
                        // Download tài liệu
                        $download_content = nv_download_content($data_content, $tabs_key . '-' . $tabs_id);
                        $tabs_content = !empty($download_content) ? $download_content : '';
                    } elseif ($tabs_key == 'content_comments') {
                        // Bình luận
                        $tabs_content = $content_comment;
                    } elseif ($tabs_key == 'content_rate') {
                        // Đánh giá sản phẩm
                        if (!empty($data_content['allowed_rating']) and !empty($pro_config['review_active'])) {
                            $tabs_content = nv_review_content($data_content);
                        }
                    } elseif ($tabs_key == 'content_customdata') {
                        // Dữ liệu tùy biến
                        if (!empty($data_content['array_custom']) and !empty($data_content['array_custom_lang'])) {
                            if (sizeof($data_content['template']) > 1) {
                                // Tab tùy biến theo nhóm (dạng mới)
                                $tabs_content = nv_custom_tab_fields($data_content);
                            } else {
                                // Tab tùy biến theo danh sách chỉ có một nhóm (dạng cũ)
                                $tabs_content = nv_custom_tpl('tab-' . strtolower(change_alias($data_content['tabs'][$tabs_id][NV_LANG_DATA . '_title'])) . '.tpl', $data_content['array_custom'], $data_content['array_custom_lang'], $idtemplates);
                            }
                        }
                    }

                    if (!empty($tabs_content)) {
                        $xtpl->assign('TABS_TITLE', $tabs_value[NV_LANG_DATA . '_title']);
                        $xtpl->assign('TABS_ID', $tabs_id);
                        $xtpl->assign('TABS_KEY', $tabs_key);

                        if (!empty($tabs_value['icon'])) {
                            $xtpl->assign('TABS_ICON', NV_STATIC_URL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $tabs_value['icon']);
                            $xtpl->parse('main.product_detail.tabs.tabs_title.icon');
                        } else {
                            $xtpl->parse('main.product_detail.tabs.tabs_title.icon_default');
                        }

                        $xtpl->assign('TABS_CONTENT', $tabs_content);
                        if ($i == 0) {
                            $xtpl->parse('main.product_detail.tabs.tabs_title.active');
                            $xtpl->parse('main.product_detail.tabs.tabs_content.active');
                        }
                        $xtpl->parse('main.product_detail.tabs.tabs_title');
                        $xtpl->parse('main.product_detail.tabs.tabs_content');
                    }
                    $i++;
                }
                $xtpl->parse('main.product_detail.tabs');
            }

            if (!empty($array_keyword)) {
                $t = sizeof($array_keyword) - 1;
                foreach ($array_keyword as $i => $value) {
                    $xtpl->assign('KEYWORD', $value['keyword']);
                    $xtpl->assign('LINK_KEYWORDS', NV_STATIC_URL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=tag/' . urlencode($value['alias']));
                    $xtpl->assign('SLASH', ($t == $i) ? '' : ', ');
                    $xtpl->parse('main.product_detail.keywords.loop');
                }
                $xtpl->parse('main.product_detail.keywords');
            }

            if (!empty($data_others)) {
                $html = call_user_func('nv_template_viewgrid', $data_others);
                $xtpl->assign('OTHER', $html);
                $xtpl->parse('main.product_detail.other');
            }
            if (!empty($array_other_view)) {
                $html = call_user_func('nv_template_viewgrid', $array_other_view);
                $xtpl->assign('OTHER_VIEW', $html);
                $xtpl->parse('main.product_detail.other_view');
            }

            if (defined('NV_IS_MODADMIN')) {
                $xtpl->assign('ADMINLINK', nv_link_edit_page($data_content['id']) . '&nbsp;&nbsp;' . nv_link_delete_page($data_content['id']));
                $xtpl->parse('main.adminlink');
            }

            if ($data_content['rating_total'] > 0 and $data_content['rating_point'] > 0) {
                $xtpl->assign('RATE_TOTAL', $data_content['rating_total']);
                $xtpl->assign('RATE_VALUE', $data_content['rating_point']);
                $xtpl->parse('main.allowed_rating_snippets');
            }

            $xtpl->parse('main.product_detail');
            $xtpl->parse('main.social_icon');

            if (!empty($data_content['homeimgfile'])) {
                $xtpl->parse('main.imagemodal');
            }
        } else {
            $xtpl->parse('main.popup');
            $xtpl->parse('main.popupid');
        }

        if (!empty($pro_config['show_product_code']) and !empty($data_content['product_code'])) {
            $xtpl->parse('main.product_code');
        }
    }

    // Nhom san pham
    $listgroupid = GetGroupID($data_content['id'], 1);
    if (!empty($listgroupid) and !empty($global_array_group)) {
        $have_group = 0;
        foreach ($listgroupid as $gid => $subid) {
            $parent_info = $global_array_group[$gid];
            if ($parent_info['in_order']) {
                $have_group = 1;
                $xtpl->assign('GROUPID', $parent_info['groupid']);
                $xtpl->assign('HEADER', $parent_info['title']);
                $xtpl->parse('main.group.items.header');
                if (!empty($subid)) {
                    foreach ($subid as $sub_gr_id) {
                        $sub_info = $global_array_group[$sub_gr_id];
                        if ($sub_info['in_order']) {
                            $xtpl->assign('GROUP', $sub_info);
                            if (sizeof($subid) == 1) {
                                $xtpl->parse('main.group.items.loop.active');
                                $xtpl->parse('main.group.items.loop.checked');
                            }
                            $xtpl->parse('main.group.items.loop');
                        }
                    }
                }
                $xtpl->parse('main.group.items');
            }
        }
        if ($have_group) {
            $xtpl->parse('main.group');
        }
    }

    // Hien thi danh sach nhom san pham
    $i = 0;
    foreach ($listgroupid as $gid => $subid) {
        $parent_info = $global_array_group[$gid];
        if ($parent_info['indetail']) {
            $xtpl->assign('MAINTITLE', $parent_info['title']);
            $xtpl->parse('main.group_detail.loop.maintitle');

            if (!empty($subid)) {
                foreach ($subid as $sub_gr_id) {
                    $sub_info = $global_array_group[$sub_gr_id];
                    if ($sub_info['indetail']) {
                        $xtpl->assign('SUBTITLE', $sub_info['title']);
                        $xtpl->parse('main.group_detail.loop.subtitle.loop');
                    }
                }
                $xtpl->parse('main.group_detail.loop.subtitle');
            }
            $i++;
        }

        if ($i > 0) {
            $xtpl->parse('main.group_detail.loop');
        }
    }

    if ($i > 0) {
        $xtpl->parse('main.group_detail');
    }

    if ($global_array_shops_cat[$data_content['listcatid']]['typeprice'] == 2) {
        $price_config = unserialize($data_content['price_config']);
        if (!empty($price_config) and sizeof($price_config) > 1) {
            $before = 1;
            foreach ($price_config as $items) {
                $items['number_from'] = $before;
                $items['price'] = nv_currency_conversion($items['price'], $data_content['money_unit'], $pro_config['money_unit']);
                $items['price'] = nv_number_format($items['price'], nv_get_decimals($pro_config['money_unit']));
                $xtpl->assign('ITEMS', $items);
                $xtpl->parse('main.typepeice.items');
                $before = $items['number_to'] + 1;
            }

            $xtpl->assign('money_unit', $price['unit']);
            $xtpl->parse('main.typepeice');
        }
    }

    if ($pro_config['active_price'] == '1') {
        if ($data_content['showprice'] == '1' && !empty($data_content['product_price'])) {
            if ($price['discount_percent'] > 0) {
                $xtpl->parse('main.price.discounts');
            } else {
                $xtpl->parse('main.price.no_discounts');
            }
            $xtpl->parse('main.price');
            $xtpl->parse('main.price1');
        } else {
            $xtpl->parse('main.contact');
        }
    }

    if ($pro_config['active_order'] == '1') {
        if ($data_content['showprice'] == '1') {
            if ($data_content['product_number'] > 0 or $pro_config['active_order_number']) {
                if (!$pro_config['active_order_number']) {
                    $xtpl->parse('main.order_number.product_number');
                    $xtpl->parse('main.order_number_limit');
                    $xtpl->parse('main.check_price');
                }
                $xtpl->parse('main.order_number');
                $xtpl->parse('main.order');
            } else {
                $xtpl->parse('main.product_empty');
            }
        }
    }

    if (!empty($data_content['allowed_send'])) {
        $xtpl->parse('main.allowed_send');
    }

    if (!empty($data_content['allowed_print'])) {
        $xtpl->parse('main.allowed_print');
        $xtpl->parse('main.allowed_print_js');
    }

    if (!empty($data_content['allowed_save'])) {
        $xtpl->parse('main.allowed_save');
    }

    if (!defined('FACEBOOK_JSSDK')) {
        $lang = (NV_LANG_DATA == 'vi') ? 'vi_VN' : 'en_US';
        $facebookappid = $pro_config['facebookappid'];
        $xtpl->assign('FACEBOOK_LANG', $lang);
        $xtpl->assign('FACEBOOK_APPID', $facebookappid);
        $xtpl->parse('main.facebookjssdk');
        if (!empty($facebookappid)) {
            $meta_property['fb:app_id'] = $facebookappid;
        }
        define('FACEBOOK_JSSDK', true);
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * print_product()
 *
 * @param mixed $data_content
 * @param mixed $data_unit
 * @param mixed $page_title
 * @return
 */
function print_product($data_content, $data_unit, $page_title)
{
    global $module_info, $lang_module, $module_file, $global_config, $module_name, $pro_config, $global_array_shops_cat;

    $xtpl = new XTemplate('print_pro.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('TEMPLATE', $module_info['template']);
    $xtpl->assign('NV_STATIC_URL', NV_STATIC_URL);

    if (!empty($data_content)) {
        $xtpl->assign('proid', $data_content['id']);
        $data_content['money_unit'] = ($data_content['money_unit'] != '') ? $data_content['money_unit'] : 'N/A';
        $data_content[NV_LANG_DATA . '_address'] = ($data_content[NV_LANG_DATA . '_address'] != '') ? $data_content[NV_LANG_DATA . '_address'] : 'N/A';
        $xtpl->assign('SRC_PRO', $data_content['homeimgthumb']);
        $xtpl->assign('TITLE', $data_content[NV_LANG_DATA . '_title']);
        $xtpl->assign('NUM_VIEW', $data_content['hitstotal']);
        $xtpl->assign('DATE_UP', $lang_module['detail_dateup'] . date(' d-m-Y ', $data_content['addtime']) . $lang_module['detail_moment'] . date(" H:i'", $data_content['addtime']));
        $xtpl->assign('PRICE', nv_get_price($data_content['id'], $pro_config['money_unit']));
        $xtpl->assign('money_unit', $pro_config['money_unit']);
        $xtpl->assign('pro_unit', $data_unit['title']);
        $xtpl->assign('address', $data_content[NV_LANG_DATA . '_address']);
        $xtpl->assign('product_number', $data_content['product_number']);
        $exptime = ($data_content['exptime'] != 0) ? date('d-m-Y', $data_content['exptime']) : 'N/A';
        $xtpl->assign('exptime', $exptime);
        $xtpl->assign('height', $pro_config['homeheight']);
        $xtpl->assign('width', $pro_config['homewidth']);

        $xtpl->assign('site_name', $global_config['site_name']);
        $xtpl->assign('url', $global_config['site_url']);
        $xtpl->assign('contact', $global_config['site_email']);
        $xtpl->assign('page_title', $page_title);
    }

    if (!empty($pro_config['active_price'])) {
        $xtpl->parse('main.price');
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * cart_product()
 *
 * @param mixed $data_content
 * @param mixed $coupons_code
 * @param mixed $array_error_number
 * @return
 */
function cart_product($data_content, $coupons_code, $order_info, $array_error_number)
{
    global $module_info, $lang_module, $module_config, $module_file, $module_name, $pro_config, $money_config, $global_array_group, $global_array_shops_cat;

    $xtpl = new XTemplate('cart.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('TEMPLATE', $module_info['template']);
    $xtpl->assign('NV_STATIC_URL', NV_STATIC_URL);
    $xtpl->assign('C_CODE', $coupons_code);

    $array_group_main = array();
    if (!empty($global_array_group)) {
        foreach ($global_array_group as $array_group) {
            if ($array_group['indetail'] and $array_group['lev'] == 0) {
                $array_group_main[] = $array_group['groupid'];
                $xtpl->assign('MAIN_GROUP', $array_group);
                $xtpl->parse('main.main_group');
            }
        }
    }

    $price_total = 0;
    $point_total = 0;
    if (!empty($data_content)) {
        $j = 1;
        foreach ($data_content as $data_row) {
            $xtpl->assign('stt', $j);
            $xtpl->assign('id', $data_row['id']);
            $xtpl->assign('title_pro', $data_row['title']);
            $xtpl->assign('link_pro', $data_row['link_pro']);
            $xtpl->assign('img_pro', $data_row['homeimgthumb']);

            $price = nv_get_price($data_row['id'], $pro_config['money_unit'], $data_row['num'], true);
            $xtpl->assign('PRICE', $price);
            $price = nv_get_price($data_row['id'], $pro_config['money_unit'], $data_row['num']);
            $xtpl->assign('PRICE_TOTAL', $price);
            $xtpl->assign('pro_num', $data_row['num']);
            $xtpl->assign('link_remove', $data_row['link_remove']);
            $xtpl->assign('product_unit', $data_row['product_unit']);
            $xtpl->assign('list_group', $data_row['group']);
            $xtpl->assign('list_group_id', str_replace(',', '_', $data_row['group']));

            // Tinh diem tich luy
            if ($pro_config['point_active'] and $global_array_shops_cat[$data_row['listcatid']]['cat_allow_point'] and ($global_array_shops_cat[$data_row['listcatid']]['cat_number_product'] == 0 or $data_row['num'] >= $global_array_shops_cat[$data_row['listcatid']]['cat_number_product'])) {
                $cat_number_point = $global_array_shops_cat[$data_row['listcatid']]['cat_number_point'];
                $point_total += intval($cat_number_point * $data_row['num']);
            }

            // Group của sản phẩm
            foreach ($array_group_main as $group_main_id) {
                $array_sub_group = GetGroupID($data_row['id']);
                for ($i = 0; $i < count($array_group_main); $i++) {
                    foreach ($array_sub_group as $sub_group_id) {
                        $item = $global_array_group[$sub_group_id];
                        if ($item['parentid'] == $group_main_id) {
                            $data = array(
                                'title' => $item['title'],
                                'link' => $item['link']
                            );
                            $xtpl->assign('SUB_GROUP', $data);
                            $xtpl->parse('main.rows.sub_group.loop');
                        }
                    }
                }
                $xtpl->parse('main.rows.sub_group');
            }

            // Group thuộc tính khách hàng chọn khi đặt hàng
            if (!empty($data_row['group'])) {
                $data_row['group'] = explode(',', $data_row['group']);
                foreach ($data_row['group'] as $groupid) {
                    $items = $global_array_group[$groupid];
                    $items['parent_title'] = $global_array_group[$items['parentid']]['title'];
                    $xtpl->assign('group', $items);
                    $xtpl->parse('main.rows.display_group.group');
                }
                $xtpl->parse('main.rows.display_group');
            }

            if ($pro_config['active_price'] == '1') {
                $xtpl->parse('main.rows.price2');
                $xtpl->parse('main.rows.price5');
            }

            $xtpl->parse('main.rows');
            $price_total = $price_total + $price['sale'];
            $j++;
        }

        // Hien thi thong bao so diem sau khi hoan tat don hang
        if ($pro_config['point_active']) {
            $point_total += intval($pro_config['point_new_order']);
            if (defined('NV_IS_USER')) {
                $xtpl->assign('point_note', sprintf($lang_module['point_cart_note_user'], $point_total));
            } else {
                $redirect = NV_STATIC_URL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=cart';
                $login = NV_STATIC_URL . 'index.php?' . NV_NAME_VARIABLE . '=users&' . NV_OP_VARIABLE . '=login&nv_redirect=' . nv_redirect_encrypt($redirect);
                $xtpl->assign('point_note', sprintf($lang_module['point_cart_note_guest'], $point_total, $login));
            }
            $xtpl->parse('main.point_note');
        }
    }

    if (!empty($array_error_number)) {
        foreach ($array_error_number as $title_error) {
            $xtpl->assign('ERROR_NUMBER_PRODUCT', $title_error);
            $xtpl->parse('main.errortitle.errorloop');
        }
        $xtpl->parse('main.errortitle');
    }

    $xtpl->assign('price_total', nv_number_format($price_total, nv_get_decimals($pro_config['money_unit'])));
    $xtpl->assign('unit_config', $money_config[$pro_config['money_unit']]['symbol']);
    $xtpl->assign('LINK_DEL_ALL', NV_STATIC_URL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=remove');
    $xtpl->assign('LINK_CART', NV_STATIC_URL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=cart');
    $xtpl->assign('LINK_PRODUCTS', NV_STATIC_URL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '');
    $xtpl->assign('link_order_all', NV_STATIC_URL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=order');

    if ($pro_config['active_price'] == '1') {
        $xtpl->parse('main.price1');
        $xtpl->parse('main.price3');
        $xtpl->parse('main.price4');
        $xtpl->parse('main.price6');
    }

    if (!empty($order_info)) {
        $xtpl->assign('EDIT_ORDER', sprintf($lang_module['cart_edit_warning'], $order_info['order_url'], $order_info['order_code'], $order_info['order_edit']));
        $xtpl->parse('main.edit_order');
    } else {
        if ($module_config[$module_name]['use_coupons']) {
            $xtpl->parse('main.coupons_code');
            $xtpl->parse('main.coupons_javascript');
        }
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * uers_order()
 *
 * @param mixed $data_content
 * @param mixed $data_order
 * @param mixed $total_coupons
 * @param mixed $error
 * @return
 */
function uers_order($data_content, $data_order, $total_coupons, $order_info)
{
    global $module_info, $lang_module, $lang_global, $module_config, $module_data, $module_file, $module_name, $pro_config, $money_config, $global_array_group, $shipping_data;

    $xtpl = new XTemplate('order.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('TEMPLATE', $module_info['template']);
    $xtpl->assign('NV_STATIC_URL', NV_STATIC_URL);
    $xtpl->assign('MODULE_FILE', $module_file);
    $xtpl->assign('NV_LANG_DATA', NV_LANG_DATA);

    $array_group_main = array();
    if (!empty($global_array_group)) {
        foreach ($global_array_group as $array_group) {
            if ($array_group['indetail'] and $array_group['lev'] == 0) {
                $array_group_main[] = $array_group['groupid'];
                $xtpl->assign('MAIN_GROUP', $array_group);
                $xtpl->parse('main.main_group');
            }
        }
    }

    $price_total = 0;
    $j = 1;
    if (!empty($data_content)) {
        foreach ($data_content as $data_row) {
            $xtpl->assign('id', $data_row['id']);
            $xtpl->assign('title_pro', $data_row['title']);
            $xtpl->assign('link_pro', $data_row['link_pro']);

            foreach ($array_group_main as $group_main_id) {
                $array_sub_group = GetGroupID($data_row['id']);
                for ($i = 0; $i < count($array_group_main); $i++) {
                    foreach ($array_sub_group as $sub_group_id) {
                        $item = $global_array_group[$sub_group_id];
                        if ($item['parentid'] == $group_main_id) {
                            $data = array(
                                'title' => $item['title'],
                                'link' => $item['link']
                            );
                            $xtpl->assign('SUB_GROUP', $data);
                            $xtpl->parse('main.rows.sub_group.loop');
                        }
                    }
                }
                $xtpl->parse('main.rows.sub_group');
            }

            if (!empty($data_row['group'])) {
                $data_row['group'] = explode(',', $data_row['group']);
                foreach ($data_row['group'] as $groupid) {
                    $items = $global_array_group[$groupid];
                    $items['parent_title'] = $global_array_group[$items['parentid']]['title'];
                    $xtpl->assign('group', $items);
                    $xtpl->parse('main.rows.display_group.group');
                }
                $xtpl->parse('main.rows.display_group');
            }

            $price = nv_get_price($data_row['id'], $pro_config['money_unit'], $data_row['num'], true);
            $xtpl->assign('PRICE', $price);
            $price = nv_get_price($data_row['id'], $pro_config['money_unit'], $data_row['num']);
            $xtpl->assign('PRICE_TOTAL', $price);
            $xtpl->assign('pro_no', $j);
            $xtpl->assign('pro_num', $data_row['num']);
            $xtpl->assign('product_unit', $data_row['product_unit']);
            if ($pro_config['active_price'] == '1') {
                $xtpl->parse('main.rows.price2');
                $xtpl->parse('main.rows.price5');
            }
            $xtpl->parse('main.rows');
            $price_total = $price_total + $price['sale'];
            ++$j;
        }
    }

    $xtpl->assign('price_coupons', nv_number_format($total_coupons, nv_get_decimals($pro_config['money_unit'])));
    $xtpl->assign('price_total', nv_number_format($price_total - $total_coupons, nv_get_decimals($pro_config['money_unit'])));
    $xtpl->assign('unit_config', $money_config[$pro_config['money_unit']]['symbol']);
    $xtpl->assign('weight_unit', $pro_config['weight_unit']);
    $xtpl->assign('DATA', $data_order);
    $xtpl->assign('LINK_CART', NV_STATIC_URL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=cart');
    if (isset($_SESSION[$module_data . '_coupons']['code'])) {
        $xtpl->assign('COUPONS_CODE', $_SESSION[$module_data . '_coupons']['code']);
    }

    if ($pro_config['active_price'] == '1') {
        $xtpl->parse('main.price1');
        if ($total_coupons > 0) {
            $xtpl->parse('main.price3.total_coupons');
        }
        $xtpl->parse('main.price3');
        $xtpl->parse('main.price4');
        $xtpl->parse('main.price6');
    }

    if ($module_config[$module_name]['use_shipping']) {
        if (!empty($shipping_data['list_location'])) {
            foreach ($shipping_data['list_location'] as $rows_i) {
                $rows_i['selected'] = ($data_order['shipping']['ship_location_id'] == $rows_i['id']) ? ' selected="selected"' : '';
                $xtpl->assign('LOCATION', $rows_i);
                $xtpl->parse('main.shipping.location_loop');
            }
        }

        if (!empty($shipping_data['list_shops'])) {
            $i = 0;
            foreach ($shipping_data['list_shops'] as $rows_i) {
                $rows_i['location_string'] = (!empty($rows_i['address']) ? $rows_i['address'] . ', ' : '') . $shipping_data['list_location'][$rows_i['location']]['title'];
                while ($shipping_data['list_location'][$rows_i['location']]['parentid'] > 0) {
                    $items = $shipping_data['list_location'][$shipping_data['list_location'][$rows_i['location']]['parentid']];
                    $rows_i['location_string'] .= ', ' . $items['title'];
                    $shipping_data['list_location'][$rows_i['location']]['parentid'] = $items['parentid'];
                }
                $rows_i['location_string'] = str_replace('&nbsp;', '', $rows_i['location_string']);
                $rows_i['checked'] = ($data_order['shipping']['ship_shops_id'] == $rows_i['id'] or $i == 0) ? ' checked="checked"' : '';
                $xtpl->assign('SHOPS', $rows_i);
                $xtpl->parse('main.shipping.shops_loop');
                $i++;
            }
        }

        if (!empty($shipping_data['list_carrier'])) {
            $i = 0;
            foreach ($shipping_data['list_carrier'] as $rows_i) {
                $rows_i['checked'] = ($data_order['shipping']['ship_carrier_id'] == $rows_i['id'] or $i == 0) ? ' checked="checked"' : '';
                $xtpl->assign('CARRIER', $rows_i);
                $xtpl->parse('main.shipping.carrier_loop');
                $i++;
            }
        }
        $xtpl->parse('main.shipping');
        $xtpl->parse('main.shipping_javascript');

        $array_yes_no = array(
            $lang_global['no'],
            $lang_global['yes']
        );
        foreach ($array_yes_no as $key => $value) {
            $xtpl->assign('IS_SHIPPING', array(
                'key' => $key,
                'value' => $value,
                'checked' => ($key == $data_order['order_shipping']) ? 'checked="checked"' : ''
            ));
            $xtpl->parse('main.shipping_chose.shipping_loop');
        }
        $xtpl->parse('main.shipping_chose');
    } else {
        $xtpl->parse('main.order_address');
    }

    if (!empty($order_info)) {
        $xtpl->assign('EDIT_ORDER', sprintf($lang_module['cart_edit_warning'], $order_info['order_url'], $order_info['order_code'], $order_info['order_edit']));
        $xtpl->parse('main.edit_order');
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * payment()
 *
 * @param mixed $data_content
 * @param mixed $data_pro
 * @param mixed $payment_supported
 * @param mixed $intro_pay
 * @return
 */
function payment($data_content, $data_pro, $data_shipping, $payment_supported, $intro_pay, $point)
{
    global $module_info, $lang_module, $module_data, $module_file, $global_config, $module_name, $pro_config, $money_config, $global_array_group, $client_info, $array_location, $array_shops;

    $money = $point * $pro_config['point_conversion'];
    $money = nv_number_format($money, nv_get_decimals($pro_config['money_unit']));
    $lang_module['point_payment_info'] = sprintf($lang_module['point_payment_info'], $point, $money, $pro_config['money_unit']);

    $xtpl = new XTemplate('payment.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('dateup', date('d-m-Y', $data_content['order_time']));
    $xtpl->assign('moment', date("H:i' ", $data_content['order_time']));
    $xtpl->assign('DATA', $data_content);
    $xtpl->assign('order_id', $data_content['order_id']);
    $xtpl->assign('cancel_url', $client_info['selfurl'] . '&cancel=1');
    $xtpl->assign('checkss', md5($client_info['session_id'] . $global_config['sitekey'] . $data_content['order_id']));

    $array_group_main = array();
    if (!empty($global_array_group)) {
        foreach ($global_array_group as $array_group) {
            if ($array_group['indetail'] and $array_group['lev'] == 0) {
                $array_group_main[] = $array_group['groupid'];
                $xtpl->assign('MAIN_GROUP', $array_group);
                $xtpl->parse('main.main_group');
            }
        }
    }

    $j = 0;
    foreach ($data_pro as $pdata) {
        $xtpl->assign('product_name', $pdata['title']);
        $xtpl->assign('product_number', $pdata['product_number']);
        $xtpl->assign('product_price', nv_number_format($pdata['product_price'], nv_get_decimals($pro_config['money_unit'])));
        $xtpl->assign('product_price_total', nv_number_format($pdata['product_price'] * $pdata['product_number'], nv_get_decimals($pro_config['money_unit'])));
        $xtpl->assign('money_unit', $money_config[$pdata['money_unit']]['symbol']);
        $xtpl->assign('product_unit', $pdata['product_unit']);
        $xtpl->assign('link_pro', $pdata['link_pro']);
        $xtpl->assign('pro_no', $j + 1);

        // Nhóm thuộc tính sản phẩm khách hàng chọn khi đặt hàng
        if (!empty($pdata['product_group'])) {
            foreach ($pdata['product_group'] as $groupid) {
                $items = $global_array_group[$groupid];
                $items['parent_title'] = $global_array_group[$items['parentid']]['title'];
                $xtpl->assign('group', $items);
                $xtpl->parse('main.loop.display_group.group');
            }
            $xtpl->parse('main.loop.display_group');
        }

        // Nhóm của sản phẩm
        foreach ($array_group_main as $group_main_id) {
            $array_sub_group = GetGroupID($pdata['id']);
            for ($i = 0; $i < count($array_group_main); $i++) {
                foreach ($array_sub_group as $sub_group_id) {
                    $item = $global_array_group[$sub_group_id];
                    if ($item['parentid'] == $group_main_id) {
                        $data = array(
                            'title' => $item['title'],
                            'link' => $item['link']
                        );
                        $xtpl->assign('SUB_GROUP', $data);
                        $xtpl->parse('main.loop.sub_group.loop');
                    }
                }
            }
            $xtpl->parse('main.loop.sub_group');
        }

        if ($pro_config['active_price'] == '1') {
            $xtpl->parse('main.loop.price2');
            $xtpl->parse('main.loop.price5');
        }

        $xtpl->parse('main.loop');
        ++$j;
    }

    // Thong tin van chuyen
    if ($pro_config['use_shipping']) {
        if ($data_shipping) {
            $data_shipping['ship_price'] = nv_number_format($data_shipping['ship_price'], nv_get_decimals($data_shipping['ship_price_unit']));
            $data_shipping['ship_location_title'] = $array_location[$data_shipping['ship_location_id']]['title'];
            while ($array_location[$data_shipping['ship_location_id']]['parentid'] > 0) {
                $items = $array_location[$array_location[$data_shipping['ship_location_id']]['parentid']];
                $data_shipping['ship_location_title'] .= ', ' . $items['title'];
                $array_location[$data_shipping['ship_location_id']]['parentid'] = $items['parentid'];
            }
            $data_shipping['ship_shops_title'] = $array_shops[$data_shipping['ship_shops_id']]['name'];
            $xtpl->assign('DATA_SHIPPING', $data_shipping);
            $xtpl->parse('main.data_shipping');
        }
    } else {
        $xtpl->parse('main.order_address');
    }

    if (!empty($data_content['order_note'])) {
        $xtpl->parse('main.order_note');
    }
    $xtpl->assign('order_coupons', nv_number_format($data_content['coupons']['amount'], nv_get_decimals($pro_config['money_unit'])));
    $xtpl->assign('order_total', nv_number_format($data_content['order_total'], nv_get_decimals($pro_config['money_unit'])));
    $xtpl->assign('unit', $money_config[$data_content['unit_total']]['symbol']);

    if ($data_content['transaction_status'] == 0 and $pro_config['active_payment'] == '1' and $pro_config['active_order'] == '1' and $pro_config['active_price'] == '1' and $pro_config['active_order_number'] == '0') {
        if (!empty($payment_supported)) {
            $xtpl->assign('PAYMENT_SUPPORTED', $payment_supported);
            $xtpl->parse('main.actpay.payment_supported');
        } else {
            $xtpl->parse('main.actpay.payment_notsupported');
        }

        if ($pro_config['point_active']) {
            $xtpl->parse('main.actpay.payment_point1');
            $xtpl->parse('main.actpay.payment_point2');
        }

        $xtpl->parse('main.actpay');
    }

    if ($data_content['transaction_status'] == -1 or $data_content['transaction_status'] == 0) {
        $action = empty($_SESSION[$module_data . '_order_info']) ? 'edit' : 'unedit';
        $xtpl->assign('url_action', NV_STATIC_URL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=payment&' . $action . '&order_id=' . $data_content['order_id'] . '&checkss=' . md5($data_content['order_id'] . $global_config['sitekey'] . session_id()));
        $xtpl->parse('main.order_action');
    }

    $xtpl->assign('url_finsh', NV_STATIC_URL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name);
    $xtpl->assign('url_print', NV_STATIC_URL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=print&order_id=' . $data_content['order_id'] . '&checkss=' . md5($data_content['order_id'] . $global_config['sitekey'] . session_id()));

    if (!empty($intro_pay)) {
        $xtpl->assign('intro_pay', $intro_pay);

        if ($data_content['transaction_status'] == 1) {
            $xtpl->parse('main.intro_pay.cancel_url');
        }
        $xtpl->parse('main.intro_pay');
    }

    if ($pro_config['active_price'] == '1') {
        $xtpl->parse('main.price1');
        if ($data_content['coupons'] and $data_content['coupons']['amount'] > 0) {
            $xtpl->parse('main.price3.total_coupons');
        }
        $xtpl->parse('main.price3');
        $xtpl->parse('main.price4');
        $xtpl->parse('main.price6');
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * print_pay()
 *
 * @param mixed $data_content
 * @param mixed $data_pro
 * @return
 */
function print_pay($data_content, $data_pro)
{
    global $module_info, $lang_module, $module_file, $pro_config, $money_config, $global_array_group;

    $xtpl = new XTemplate('print.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('dateup', date('d-m-Y', $data_content['order_time']));
    $xtpl->assign('moment', date("H:i' ", $data_content['order_time']));
    $xtpl->assign('DATA', $data_content);
    $xtpl->assign('order_id', $data_content['order_id']);

    $array_group_main = array();
    if (!empty($global_array_group)) {
        foreach ($global_array_group as $array_group) {
            if ($array_group['indetail'] and $array_group['lev'] == 0) {
                $array_group_main[] = $array_group['groupid'];
                $xtpl->assign('MAIN_GROUP', $array_group);
                $xtpl->parse('main.main_group');
            }
        }
    }

    $i = 0;
    foreach ($data_pro as $pdata) {
        $xtpl->assign('product_name', $pdata['title']);
        $xtpl->assign('product_number', $pdata['product_number']);
        $xtpl->assign('product_price', nv_number_format($pdata['product_price'], nv_get_decimals($pro_config['money_unit'])));
        $xtpl->assign('product_price_total', nv_number_format($pdata['product_price'] * $pdata['product_number'], nv_get_decimals($pro_config['money_unit'])));
        $xtpl->assign('product_unit', $pdata['product_unit']);
        $xtpl->assign('link_pro', $pdata['link_pro']);
        $xtpl->assign('pro_no', $i + 1);

        foreach ($array_group_main as $group_main_id) {
            $array_sub_group = GetGroupID($pdata['id']);
            for ($i = 0; $i < count($array_group_main); $i++) {
                $title = '';
                foreach ($array_sub_group as $sub_group_id) {
                    $item = $global_array_group[$sub_group_id];
                    if ($item['parentid'] == $group_main_id) {
                        $title = $item['title'];
                    }
                }
                $xtpl->assign('SUB_GROUP', $title);
            }
            $xtpl->parse('main.loop.sub_group');
        }

        if ($pro_config['active_price'] == '1') {
            $xtpl->parse('main.loop.price2');
            $xtpl->parse('main.loop.price5');
        }

        $xtpl->parse('main.loop');
        ++$i;
    }
    if (!empty($data_content['order_note'])) {
        $xtpl->parse('main.order_note');
    }
    $xtpl->assign('order_total', nv_number_format($data_content['order_total'], nv_get_decimals($pro_config['money_unit'])));
    $xtpl->assign('unit', $data_content['unit_total']);

    $payment = '';
    if ($data_content['transaction_status'] == 4) {
        $payment = $lang_module['history_payment_yes'];
    } elseif ($data_content['transaction_status'] == 3) {
        $payment = $lang_module['history_payment_cancel'];
    } elseif ($data_content['transaction_status'] == 2) {
        $payment = $lang_module['history_payment_check'];
    } elseif ($data_content['transaction_status'] == 1) {
        $payment = $lang_module['history_payment_send'];
    } elseif ($data_content['transaction_status'] == 0) {
        $payment = $lang_module['history_payment_no'];
    } elseif ($data_content['transaction_status'] == -1) {
        $payment = $lang_module['history_payment_wait'];
    } else {
        $payment = 'ERROR';
    }
    $xtpl->assign('payment', $payment);
    if ($pro_config['active_price'] == '1') {
        $xtpl->parse('main.price1');
        $xtpl->parse('main.price3');
        $xtpl->parse('main.price4');
        $xtpl->parse('main.price6');
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * history_order()
 *
 * @param mixed $data_content
 * @return
 */
function history_order($data_content)
{
    global $module_info, $lang_module, $module_file, $module_name, $pro_config, $money_config;

    $xtpl = new XTemplate('history_order.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $i = 0;

    foreach ($data_content as $data_row) {
        $xtpl->assign('order_code', $data_row['order_code']);
        $xtpl->assign('history_date', date('d-m-Y', $data_row['order_time']));
        $xtpl->assign('history_moment', date("H:i' ", $data_row['order_time']));
        $xtpl->assign('history_total', nv_number_format($data_row['order_total'], nv_get_decimals($pro_config['money_unit'])));
        $xtpl->assign('unit_total', $data_row['unit_total']);
        $xtpl->assign('note', $data_row['order_note']);
        $xtpl->assign('URL_DEL_BACK', NV_STATIC_URL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=history');

        if (intval($data_row['transaction_status']) <= 1) {
            $xtpl->assign('link_remove', $data_row['link_remove']);
            $xtpl->parse('main.rows.remove');
        } else {
            $xtpl->parse('main.rows.no_remove');
        }
        $xtpl->assign('link', $data_row['link']);

        /* transaction_status: Trang thai giao dich:
         0 - Giao dich moi tao
         1 - Chua thanh toan;
         2 - Da thanh toan, dang bi tam giu;
         3 - Giao dich bi huy;
         4 - Giao dich da hoan thanh thanh cong (truong hop thanh toan ngay hoac thanh toan tam giu nhung nguoi mua da phe chuan)
         */
        if ($data_row['transaction_status'] == 4) {
            $history_payment = $lang_module['history_payment_yes'];
        } elseif ($data_row['transaction_status'] == 3) {
            $history_payment = $lang_module['history_payment_cancel'];
        } elseif ($data_row['transaction_status'] == 2) {
            $history_payment = $lang_module['history_payment_check'];
        } elseif ($data_row['transaction_status'] == 1) {
            $history_payment = $lang_module['history_payment_send'];
        } elseif ($data_row['transaction_status'] == 0) {
            $history_payment = $lang_module['history_payment_no'];
        } elseif ($data_row['transaction_status'] == -1) {
            $history_payment = $lang_module['history_payment_wait'];
        } else {
            $history_payment = 'ERROR';
        }

        $xtpl->assign('history_payment', $history_payment);
        $bg = ($i % 2 == 0) ? 'class="bg"' : '';
        $xtpl->assign('bg', $bg);
        $xtpl->assign('TT', $i + 1);
        if ($pro_config['active_price'] == '1') {
            $xtpl->parse('main.rows.price2');
        }

        if (isAllowedUpdateOrder($data_row['transaction_status'])) {
            $xtpl->assign('CHECK_ID', $data_row['order_id']);
            $xtpl->assign('CHECK_SESS', $data_row['checkss']);
            $xtpl->parse('main.rows.checkorder');
        }

        $xtpl->parse('main.rows');
        ++$i;
    }
    if ($pro_config['active_price'] == '1') {
        $xtpl->parse('main.price1');
    }
    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * search_theme()
 *
 * @param mixed $key
 * @param mixed $check_num
 * @param mixed $date_array
 * @param mixed $array_cat_search
 * @return
 */
function search_theme($key, $check_num, $date_array, $array_cat_search)
{
    global $module_name, $module_info, $module_file, $lang_module, $module_name;

    $xtpl = new XTemplate("search.tpl", NV_ROOTDIR . "/themes/" . $module_info['template'] . "/modules/" . $module_file);

    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('MODULE_NAME', $module_name);
    $xtpl->assign('TO_DATE', $date_array['to_date']);
    $xtpl->assign('FROM_DATE', $date_array['from_date']);
    $xtpl->assign('KEY', $key);
    $xtpl->assign('OP_NAME', 'search');

    foreach ($array_cat_search as $search_cat) {
        $xtpl->assign('SEARCH_CAT', $search_cat);
        $xtpl->parse('main.search_cat');
    }
    for ($i = 0; $i <= 3; $i++) {
        if ($check_num == $i) {
            $xtpl->assign('CHECK' . $i, "selected=\"selected\"");
        } else {
            $xtpl->assign('CHECK' . $i, "");
        }
    }
    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * search_result_theme()
 *
 * @param mixed $key
 * @param mixed $numRecord
 * @param mixed $per_pages
 * @param mixed $pages
 * @param mixed $array_content
 * @param mixed $url_link
 * @param mixed $catid
 * @return
 */
function search_result_theme($key, $numRecord, $per_pages, $pages, $array_content, $url_link, $catid)
{
    global $module_file, $module_info, $lang_module, $global_array_shops_cat, $pro_config;

    $xtpl = new XTemplate("search.tpl", NV_ROOTDIR . "/themes/" . $module_info['template'] . "/modules/" . $module_file);

    $xtpl->assign('NV_STATIC_URL', NV_STATIC_URL);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('KEY', $key);

    $xtpl->assign('TITLE_MOD', $lang_module['search_modul_title']);

    if (!empty($array_content)) {
        foreach ($array_content as $value) {
            $listcatid = explode(",", $value['listcatid']);
            $catid_i = ($catid > 0) ? $catid : end($listcatid);
            $url = $global_array_shops_cat[$catid_i]['link'] . '/' . $value['alias'] . "-" . $value['id'];

            $value['hometext'] = nv_clean60($value['hometext'], 170);

            $xtpl->assign('LINK', $url);
            $xtpl->assign('TITLEROW', BoldKeywordInStr($value['title'], $key));
            $xtpl->assign('CONTENT', BoldKeywordInStr($value['hometext'], $key) . "...");
            $xtpl->assign('height', $pro_config['homeheight']);
            $xtpl->assign('width', $pro_config['homewidth']);

            $xtpl->assign('IMG_SRC', $value['homeimgthumb']);
            $xtpl->parse('results.result.result_img');

            if (defined('NV_IS_MODADMIN')) {
                $xtpl->assign('ADMINLINK', nv_link_edit_page($value['id']) . "&nbsp;&nbsp;" . nv_link_delete_page($value['id']));
                $xtpl->parse('results.result.adminlink');
            }

            $xtpl->parse('results.result');
        }
    }
    if ($numRecord == 0) {
        $xtpl->assign('KEY', $key);
        $xtpl->assign('INMOD', $lang_module['search_modul_title']);
        $xtpl->parse('results.noneresult');
    }
    if ($numRecord > $per_pages) {
        // show pages

        $url_link = $_SERVER['REQUEST_URI'];
        $in = strpos($url_link, '&page');
        if ($in != 0) {
            $url_link = substr($url_link, 0, $in);
        }
        $generate_page = nv_generate_page($url_link, $numRecord, $per_pages, $pages);
        $xtpl->assign('VIEW_PAGES', $generate_page);
        $xtpl->parse('results.pages_result');
    }
    $xtpl->assign('MY_DOMAIN', NV_MY_DOMAIN);
    $xtpl->assign('NUMRECORD', $numRecord);
    $xtpl->parse('results');
    return $xtpl->text('results');
}

/**
 * email_new_order()
 *
 * @param mixed $content
 * @param mixed $data_content
 * @param mixed $data_pro
 * @param mixed $data_table
 * @return
 */
function email_new_order($content, $data_content, $data_pro, $data_table = false)
{
    global $module_info, $lang_module, $module_file, $pro_config, $global_config, $money_config;

    if ($data_table) {
        $xtpl = new XTemplate("email_new_order.tpl", NV_ROOTDIR . "/themes/" . $module_info['template'] . "/modules/" . $module_file);
        $xtpl->assign('LANG', $lang_module);
        $xtpl->assign('DATA', $data_content);

        $i = 0;
        foreach ($data_pro as $pdata) {
            $xtpl->assign('product_name', $pdata['title']);
            $xtpl->assign('product_number', $pdata['product_number']);
            $xtpl->assign('product_price', nv_number_format($pdata['product_price'], nv_get_decimals($pro_config['money_unit'])));
            $xtpl->assign('product_unit', $pdata['product_unit']);
            $xtpl->assign('pro_no', $i + 1);

            $bg = ($i % 2 == 0) ? " style=\"background:#f3f3f3;\"" : "";
            $xtpl->assign('bg', $bg);

            if ($pro_config['active_price'] == '1') {
                $xtpl->parse('data_product.loop.price2');
            }
            $xtpl->parse('data_product.loop');
            ++$i;
        }

        if (!empty($data_content['order_note'])) {
            $xtpl->parse('data_product.order_note');
        }

        $xtpl->assign('order_total', nv_number_format($data_content['order_total'], nv_get_decimals($pro_config['money_unit'])));
        $xtpl->assign('unit', $data_content['unit_total']);

        if ($pro_config['active_price'] == '1') {
            $xtpl->parse('data_product.price1');
            $xtpl->parse('data_product.price3');
        }

        $xtpl->parse('data_product');
        return $xtpl->text('data_product');
    }

    $xtpl = new XTemplate("email_new_order.tpl", NV_ROOTDIR . "/themes/" . $module_info['template'] . "/modules/" . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('CONTENT', $content);

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * compare()
 *
 * @param mixed $data_pro
 * @return
 */
function compare($data_pro)
{
    global $lang_module, $lang_global, $module_file, $module_info, $pro_config, $global_array_group, $my_head;

    $xtpl = new XTemplate("compare.tpl", NV_ROOTDIR . "/themes/" . $module_info['template'] . "/modules/" . $module_file);
    $xtpl->assign('GLANG', $lang_global);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('module_name', $module_file);
    $xtpl->assign('NV_STATIC_URL', NV_STATIC_URL);
    $xtpl->assign('NV_LANG_VARIABLE', NV_LANG_VARIABLE);
    $xtpl->assign('NV_LANG_DATA', NV_LANG_DATA);
    $xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
    $xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);

    foreach ($data_pro as $data_row) {
        $xtpl->assign('title_pro', $data_row['title']);
        $xtpl->assign('link_pro', $data_row['link_pro']);
        $xtpl->parse('main.title');
        $xtpl->assign('link_pro', $data_row['link_pro']);
        $xtpl->assign('img_pro', $data_row['homeimgthumb']);
        $xtpl->parse('main.homeimgthumb');
        $xtpl->assign('intro', $data_row['hometext']);
        $xtpl->parse('main.hometext');
        $xtpl->assign('bodytext', nv_clean60($data_row['bodytext'], 400));
        $xtpl->parse('main.bodytext');
        $xtpl->assign('id', $data_row['id']);

        if ($pro_config['active_order'] == '1' and $pro_config['active_order_non_detail'] == '1') {
            if ($data_row['showprice'] == '1' && !empty($data_row['product_price'])) {
                if ($data_row['product_number'] > 0 or !empty($pro_config['active_order_number'])) {
                    // Kiem tra nhom bat buoc chon khi dat hang
                    $listgroupid = GetGroupID($data_row['id']);
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

                    $xtpl->parse('main.button.order');
                } else {
                    $xtpl->parse('main.button.product_empty');
                }
            }
        }
        $xtpl->parse('main.button');

        $price = nv_get_price($data_row['id'], $pro_config['money_unit']);
        if ($pro_config['active_price'] == '1') {
            if ($data_row['showprice'] == '1' && !empty($data_row['product_price'])) {
                $xtpl->assign('PRICE', $price);
                if ($price['discount_percent'] > 0 and $data_row['showprice']) {
                    $xtpl->parse('main.price.discounts');
                } else {
                    $xtpl->parse('main.price.no_discounts');
                }
                $xtpl->parse('main.price');
            } else {
                $xtpl->parse('main.contact');
            }
        }
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * coupons_info()
 *
 * @param mixed $data_content
 * @param mixed $error
 * @return
 */
function coupons_info($data_content, $coupons_check, $error)
{
    global $module_info, $lang_module, $lang_global, $module_data, $module_file, $pro_config, $op;

    $xtpl = new XTemplate('coupons_info.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('GLANG', $lang_global);
    $xtpl->assign('MONEY_UNIT', $pro_config['money_unit']);
    $xtpl->assign('COUPONS_CHECK', $coupons_check ? 'checked="checked"' : '');

    if (!empty($data_content)) {
        $data_content['date_start'] = !empty($data_content['date_start']) ? nv_date('d/m/Y', $data_content['date_start']) : 'N/A';
        $data_content['date_end'] = !empty($data_content['date_end']) ? nv_date('d/m/Y', $data_content['date_end']) : $lang_module['coupons_end_time_ulimit'];
        $data_content['discount_text'] = $data_content['type'] == 'p' ? '%' : ' ' . $pro_config['money_unit'];
        $xtpl->assign('DATA', $data_content);
        if (!empty($data_content['total_amount'])) {
            $xtpl->parse('main.content.total_amount');
        }

        $xtpl->parse('main.content');
    }

    if (!empty($error)) {
        $xtpl->assign('ERROR', $error);
        $xtpl->parse('main.error');
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * point_info()
 *
 * @param mixed $data_content
 * @param mixed $generate_page
 * @return
 */
function point_info($data_content, $generate_page)
{
    global $module_info, $lang_module, $lang_global, $module_data, $module_file, $pro_config, $op;

    $xtpl = new XTemplate('point.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('DATA', $data_content);

    if (!empty($data_content['history'])) {
        foreach ($data_content['history'] as $history) {
            $history['time'] = nv_date('H:i d/m/Y', $history['time']);
            $xtpl->assign('HISTORY', $history);
            $xtpl->parse('main.history.loop');
        }

        if (!empty($generate_page)) {
            $xtpl->assign('PAGE', $generate_page);
            $xtpl->parse('main.history.generate_page');
        }

        $xtpl->parse('main.history');
    } else {
        $xtpl->parse('main.point_empty');
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * nv_review_content
 *
 * @param mixed $data_content
 * @return
 */
function nv_review_content($data_content)
{
    global $module_info, $lang_module, $lang_global, $module_name, $module_data, $module_file, $pro_config, $op, $user_info, $global_config, $module_config;

    $xtpl = new XTemplate('review_content.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('LINK_REVIEW', NV_STATIC_URL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=review&id=' . $data_content['id'] . '&1');

    if (!empty($user_info)) {
        $user_info['full_name'] = nv_show_name_user($user_info['first_name'], $user_info['last_name'], $user_info['username']);
        $xtpl->assign('SENDER', !empty($user_info['full_name']) ? $user_info['full_name'] : $user_info['username']);
    }
    $xtpl->assign('RATE_TOTAL', $data_content['rating_total']);
    $xtpl->assign('RATE_VALUE', $data_content['rating_point']);

    $reCaptchaPass = (!empty($global_config['recaptcha_sitekey']) and !empty($global_config['recaptcha_secretkey']) and ($global_config['recaptcha_ver'] == 2 or $global_config['recaptcha_ver'] == 3));

    if ($pro_config['review_captcha']) {
        if ($module_config[$module_name]['captcha_type'] == 'recaptcha' and $reCaptchaPass and $global_config['recaptcha_ver'] == 3) {
            $xtpl->parse('main.recaptcha3');
        } elseif ($module_config[$module_name]['captcha_type'] == 'recaptcha' and $reCaptchaPass and $global_config['recaptcha_ver'] == 2) {
            $xtpl->assign('RECAPTCHA_ELEMENT', 'recaptcha' . nv_genpass(8));
            $xtpl->assign('N_CAPTCHA', $lang_global['securitycode1']);
            $xtpl->parse('main.recaptcha');
        } elseif ($module_config[$module_name]['captcha_type'] == 'captcha') {
            $xtpl->assign('N_CAPTCHA', $lang_global['securitycode']);
            $xtpl->assign('CAPTCHA_REFRESH', $lang_global['captcharefresh']);
            $xtpl->assign('GFX_WIDTH', NV_GFX_WIDTH);
            $xtpl->assign('GFX_HEIGHT', NV_GFX_HEIGHT);
            $xtpl->assign('CAPTCHA_REFR_SRC', NV_STATIC_URL . NV_ASSETS_DIR . '/images/refresh.png');
            $xtpl->assign('SRC_CAPTCHA', NV_BASE_SITEURL . 'index.php?scaptcha=captcha&t=' . NV_CURRENTTIME);
            $xtpl->assign('GFX_MAXLENGTH', NV_GFX_NUM);
            $xtpl->parse('main.captcha');
        }
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * nv_download_content
 *
 * @param mixed $data_content
 * @param mixed $linktab
 * @return
 */
function nv_download_content($data_content)
{
    global $module_info, $lang_module, $lang_global, $module_name, $module_data, $module_file, $pro_config, $op;

    $xtpl = new XTemplate('download_content.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('proid', $data_content['id']);

    if (!empty($data_content['files'])) {
        $login = 0;
        foreach ($data_content['files'] as $files) {
            if (file_exists(NV_ROOTDIR . '/themes/' . $module_info['template'] . '/images/' . $module_file . '/icon_files/' . $files['extension'] . '.png')) {
                $files['extension_icon'] = NV_STATIC_URL . 'themes/' . $module_info['template'] . '/images/' . $module_file . '/icon_files/' . $files['extension'] . '.png';
            } else {
                $files['extension_icon'] = NV_STATIC_URL . 'themes/' . $module_info['template'] . '/images/' . $module_file . '/icon_files/document.png';
            }
            $xtpl->assign('FILES', $files);

            if ($files['download_groups'] == '-1') {
                $files['download_groups'] = $pro_config['download_groups'];
            }
            if (!nv_user_in_groups($files['download_groups'])) {
                $xtpl->assign('NOTE', $lang_module['download_file_no']);
                $xtpl->parse('main.files_content.loop.disabled');
            } else {
                $xtpl->assign('NOTE', $lang_module['download_file']);
            }
            $xtpl->parse('main.files_content.loop');
        }

        if ($login > 0) {
            $link_login = NV_STATIC_URL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=users&amp;' . NV_OP_VARIABLE . '=login&amp;nv_redirect=' . nv_redirect_encrypt($client_info['selfurl'] . '#' . $linktab);
            $xtpl->assign('DOWNLOAD_LOGIN', '<a title="' . $lang_global['loginsubmit'] . '" href="' . $link_login . '">' . $lang_module['download_login'] . '</a>');
            $xtpl->parse('main.form_login');
        }

        $xtpl->parse('main.files_content');
        $xtpl->parse('main');
        return $xtpl->text('main');
    }
}

/**
 * nv_template_viewgrid
 *
 * @param mixed $array_data
 * @return
 */
function nv_template_viewgrid($array_data, $page = '')
{
    global $module_info, $lang_module, $lang_global, $module_name, $module_data, $module_file, $module_upload, $pro_config, $op, $compareid, $global_array_shops_cat;

    $xtpl = new XTemplate('viewgird.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('MODULE_NAME', $module_name);

    if (!empty($array_data)) {
        $i = 1;
        $xtpl->assign('NUM', 24 / $pro_config['per_row']);
        $xtpl->assign('SUM', count($array_data));
        $xtpl->assign('HEIGHT', $pro_config['homeheight']);
        $xtpl->assign('WIDTH', $pro_config['homewidth']);

        foreach ($array_data as $data_row) {
            $xtpl->assign('ROW', $data_row);

            $newday = $data_row['publtime'] + (86400 * $data_row['newday']);
            if ($newday >= NV_CURRENTTIME) {
                $xtpl->parse('main.loop.new');
            }

            $price = nv_get_price($data_row['id'], $pro_config['money_unit']);
            if ($pro_config['active_price'] == '1') {
                if ($data_row['showprice'] == '1' && !empty($data_row['product_price'])) {
                    $xtpl->assign('PRICE', $price);
                    if ($price['discount_percent'] > 0) {
                        $xtpl->parse('main.loop.price.discounts');
                        $xtpl->parse('main.loop.price.discounts.standard');
                    } else {
                        $xtpl->parse('main.loop.price.no_discounts');
                    }
                    $xtpl->parse('main.loop.price');
                } else {
                    $xtpl->parse('main.loop.contact');
                }
            }

            if ($pro_config['active_order'] == '1' and $pro_config['active_order_non_detail'] == '1') {
                if ($data_row['showprice'] == '1' && !empty($data_row['product_price'])) {
                    if ($data_row['product_number'] > 0 or !empty($pro_config['active_order_number'])) {
                        // Kiem tra nhom bat buoc chon khi dat hang
                        $listgroupid = GetGroupID($data_row['id']);
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
            if ($pro_config['active_tooltip'] == 1) {
                $xtpl->parse('main.loop.tooltip_js');
            }

            if (!empty($pro_config['show_product_code']) and !empty($data_row['product_code'])) {
                $xtpl->parse('main.loop.product_code');
            }

            if (defined('NV_IS_MODADMIN')) {
                $xtpl->assign('ADMINLINK', nv_link_edit_page($data_row['id']) . '&nbsp;&nbsp;' . nv_link_delete_page($data_row['id']));
                $xtpl->parse('main.loop.adminlink');
            }

            // Qua tang
            if ($pro_config['active_gift'] and !empty($data_row['gift_content']) and NV_CURRENTTIME >= $data_row['gift_from'] and NV_CURRENTTIME <= $data_row['gift_to']) {
                $xtpl->parse('main.loop.gift');
            }

            // So sanh san pham
            if ($pro_config['show_compare'] == 1) {
                if (!empty($compare_id)) {
                    $ch = (in_array($data_row['id'], $compare_id)) ? ' checked="checked"' : '';
                    $xtpl->assign('ch', $ch);
                }
                $xtpl->parse('main.loop.compare');
            }

            // San pham yeu thich
            if ($pro_config['active_wishlist']) {
                if (!empty($array_wishlist_id)) {
                    if (in_array($data_row['id'], $array_wishlist_id)) {
                        $xtpl->parse('main.loop.wishlist.disabled');
                    }
                }
                $xtpl->parse('main.loop.wishlist');
            }

            if ($price['discount_percent'] > 0 and $data_row['showprice']) {
                $xtpl->parse('main.loop.discounts');
            }

            // Hien thi bieu tuong tich luy diem
            if ($pro_config['point_active'] and $global_array_shops_cat[$data_row['listcatid']]['cat_allow_point'] and !empty($global_array_shops_cat[$data_row['listcatid']]['cat_number_point'])) {
                $xtpl->assign('point', $global_array_shops_cat[$data_row['listcatid']]['cat_number_point']);
                $xtpl->assign('point_note', sprintf($lang_module['point_product_note'], $global_array_shops_cat[$data_row['listcatid']]['cat_number_point']));
                $xtpl->parse('main.loop.point');
            }

            $xtpl->parse('main.loop');
            ++$i;
        }
    }

    if (!empty($page)) {
        $xtpl->assign('PAGE', $page);
        $xtpl->parse('main.page');
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * nv_template_viewlist
 *
 * @param mixed $otherimage
 * @return
 */
function nv_template_viewlist($array_data, $page)
{
    global $module_info, $lang_module, $lang_global, $module_name, $module_data, $module_file, $module_upload, $pro_config, $op, $compareid;

    $xtpl = new XTemplate('viewlist.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);

    if (!empty($array_data)) {
        $i = 1;
        $xtpl->assign('SUM', count($array_data));
        $xtpl->assign('HEIGHT', $pro_config['homeheight']);
        $xtpl->assign('WIDTH', $pro_config['homewidth']);

        foreach ($array_data as $data_row) {
            $xtpl->assign('ROW', $data_row);

            $newday = $data_row['publtime'] + (86400 * $data_row['newday']);
            if ($newday >= NV_CURRENTTIME) {
                $xtpl->parse('main.loop.new');
            }

            $price = nv_get_price($data_row['id'], $pro_config['money_unit']);

            if ($pro_config['active_price'] == '1') {
                if ($data_row['showprice'] == '1' && !empty($data_row['product_price'])) {
                    $xtpl->assign('PRICE', $price);
                    if ($price['discount_percent'] > 0) {
                        $xtpl->parse('main.loop.price.discounts');
                        $xtpl->parse('main.loop.price.discounts.standard');
                    } else {
                        $xtpl->parse('main.loop.price.no_discounts');
                    }
                    $xtpl->parse('main.loop.price');
                } else {
                    $xtpl->parse('main.loop.contact');
                }
            }

            if ($pro_config['active_order'] == '1' and $pro_config['active_order_non_detail'] == '1') {
                if ($data_row['showprice'] == '1' && !empty($data_row['product_price'])) {
                    if ($data_row['product_number'] > 0 or !empty($pro_config['active_order_number'])) {
                        // Kiem tra nhom bat buoc chon khi dat hang
                        $listgroupid = GetGroupID($data_row['id']);
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

            if (!empty($pro_config['show_product_code']) and !empty($data_row['product_code'])) {
                $xtpl->parse('main.loop.product_code');
            }

            if (defined('NV_IS_MODADMIN')) {
                $xtpl->assign('ADMINLINK', nv_link_edit_page($data_row['id']) . '&nbsp;&nbsp;' . nv_link_delete_page($data_row['id']));
                $xtpl->parse('main.loop.adminlink');
            }

            // Qua tang
            if ($pro_config['active_gift'] and !empty($data_row['gift_content']) and NV_CURRENTTIME >= $data_row['gift_from'] and NV_CURRENTTIME <= $data_row['gift_to']) {
                $xtpl->parse('main.loop.gift');
            }

            // So sanh san pham
            if ($pro_config['show_compare'] == 1) {
                if (!empty($compare_id)) {
                    $ch = (in_array($data_row['id'], $compare_id)) ? ' checked="checked"' : '';
                    $xtpl->assign('ch', $ch);
                }
                $xtpl->parse('main.loop.compare');
            }

            // San pham yeu thich
            if ($pro_config['active_wishlist']) {
                if (!empty($array_wishlist_id)) {
                    if (in_array($data_row['id'], $array_wishlist_id)) {
                        $xtpl->parse('main.loop.wishlist.disabled');
                    }
                }
                $xtpl->parse('main.loop.wishlist');
            }

            if ($price['discount_percent'] > 0 and $data_row['showprice']) {
                $xtpl->parse('main.loop.discounts');
            }

            // Hien thi bieu tuong tich luy diem
            if ($pro_config['point_active'] and $global_array_shops_cat[$data_row['listcatid']]['cat_allow_point'] and !empty($global_array_shops_cat[$data_row['listcatid']]['cat_number_point'])) {
                $xtpl->assign('point', $global_array_shops_cat[$data_row['listcatid']]['cat_number_point']);
                $xtpl->assign('point_note', sprintf($lang_module['point_product_note'], $global_array_shops_cat[$data_row['listcatid']]['cat_number_point']));
                $xtpl->parse('main.loop.point');
            }

            $xtpl->parse('main.loop');
            ++$i;
        }
    }

    if (!empty($page)) {
        $xtpl->assign('PAGE', $page);
        $xtpl->parse('main.page');
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * nv_template_viewcat()
 *
 * @param mixed $data_content
 * @param mixed $pages
 * @return
 */
function nv_template_viewcat($data_content, $compare_id, $pages, $sort = 0, $viewtype = 'viewgrid')
{
    global $module_info, $lang_module, $module_file, $module_upload, $module_name, $pro_config, $array_displays, $array_wishlist_id, $op, $global_array_shops_cat, $global_array_group, $my_head, $page;

    $xtpl = new XTemplate('viewcat.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('MODULE_NAME', $module_name);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('ALIAS', $data_content['alias']);
    $xtpl->assign('CATID', $data_content['id']);
    $xtpl->assign('CAT_NAME', $data_content['title']);
    $xtpl->assign('COUNT', $data_content['count']);

    // Hiển thị phần giới thiệu loại sản phẩm
    if ($op != 'group') {
        if (($global_array_shops_cat[$data_content['id']]['viewdescriptionhtml'] and $page == 1) or $global_array_shops_cat[$data_content['id']]['viewdescriptionhtml'] == 2) {
            $xtpl->assign('DESCRIPTIONHTML', $global_array_shops_cat[$data_content['id']]['descriptionhtml']);
            if (!empty($data_content['image'])) {
                $image = NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/' . $data_content['image'];
                if (!empty($data_content['image']) and file_exists($image)) {
                    $xtpl->assign('IMAGE', NV_STATIC_URL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $data_content['image']);
                    $xtpl->parse('main.viewdescriptionhtml.image');
                }
            }
            $xtpl->parse('main.viewdescriptionhtml');
        }
        $image = NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/' . $data_content['image'];

        if (!empty($data_content['image']) and file_exists($image)) {
            $xtpl->assign('IMAGE', NV_STATIC_URL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $data_content['image']);
            $xtpl->parse('main.image');
        }
    }

    /*
     * Hiển thị phần sắp xếp sản phẩm nếu cấu hình bật
     * - Trên nhóm sản phẩm
     * - Trên loại sản phẩm khi không xem theo từng loại con
     */
    if (!empty($pro_config['show_displays']) and ($op == 'group' or $global_array_shops_cat[$data_content['id']]['viewcat'] != 'view_home_cat')) {
        foreach ($array_displays as $k => $array_displays_i) {
            $se = '';
            $xtpl->assign('value', $array_displays_i);
            $xtpl->assign('key', $k);
            $se = ($sort == $k) ? 'selected="selected"' : '';
            $xtpl->assign('se', $se);
            $xtpl->parse('main.displays.sorts');
        }

        $array_viewtype = array(
            'viewgrid' => array(
                'title' => $lang_module['view_page_gird'],
                'icon' => 'th-large'
            ),
            'viewlist' => array(
                'title' => $lang_module['view_page_list'],
                'icon' => 'th-list'
            )
        );
        foreach ($array_viewtype as $index => $value) {
            $value['active'] = $index == $viewtype ? 'active' : '';
            $value['index'] = $index;
            $xtpl->assign('VIEWTYPE', $value);
            $xtpl->parse('main.displays.viewtype');
        }

        $xtpl->parse('main.displays');
    }

    if ($op != 'group' and $global_array_shops_cat[$data_content['id']]['viewcat'] == 'view_home_cat') {
        $xtpl->assign('CONTENT', nv_template_main_cat($data_content['data']));
    } elseif (function_exists('nv_template_' . $viewtype)) {
        $xtpl->assign('CONTENT', call_user_func('nv_template_' . $viewtype, $data_content['data'], $pages));
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * nv_template_wishlist()
 *
 * @param mixed $data_content
 * @param mixed $pages
 * @return
 */
function nv_template_wishlist($array_data, $pages, $viewtype = 'viewgrid')
{
    global $module_info, $lang_module, $module_file, $module_upload, $module_name, $pro_config, $array_displays, $array_wishlist_id, $op, $global_array_shops_cat, $global_array_group, $my_head, $page;

    $xtpl = new XTemplate('wishlist.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('TITLE', $module_info['funcs']['wishlist']['func_custom_name']);

    if (function_exists('nv_template_' . $viewtype)) {
        $xtpl->assign('CONTENT', call_user_func('nv_template_' . $viewtype, $array_data, $pages));
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * nv_template_tag()
 *
 * @param mixed $array_data
 * @param mixed $pages
 * @param mixed $viewtype
 * @return
 */
function nv_template_tag($array_data, $bodytext, $pages = '', $sort = 0, $viewtype = 'viewgrid')
{
    global $module_info, $lang_module, $module_file, $op, $page_title, $pro_config, $array_displays;

    $xtpl = new XTemplate('tag.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('TITLE', $page_title);

    if (function_exists('nv_template_' . $viewtype)) {
        $xtpl->assign('CONTENT', call_user_func('nv_template_' . $viewtype, $array_data, $pages));
    }

    if (!empty($bodytext)) {
        $xtpl->assign('BODYTEXT', $bodytext);
        $xtpl->parse('main.bodytext');
    }

    if ($pro_config['show_displays'] == 1) {
        foreach ($array_displays as $k => $array_displays_i) {
            $se = '';
            $xtpl->assign('value', $array_displays_i);
            $xtpl->assign('key', $k);
            $se = ($sort == $k) ? 'selected="selected"' : '';
            $xtpl->assign('se', $se);
            $xtpl->parse('main.displays.sorts');
        }

        $array_viewtype = array(
            'viewgrid' => array(
                'title' => $lang_module['view_page_gird'],
                'icon' => 'th-large'
            ),
            'viewlist' => array(
                'title' => $lang_module['view_page_list'],
                'icon' => 'th-list'
            )
        );
        foreach ($array_viewtype as $index => $value) {
            $value['active'] = $index == $viewtype ? 'active' : '';
            $value['index'] = $index;
            $xtpl->assign('VIEWTYPE', $value);
            $xtpl->parse('main.displays.viewtype');
        }

        $xtpl->parse('main.displays');
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * nv_template_loadcart()
 *
 * @param mixed $array_data
 * @return
 */
function nv_template_loadcart($array_data, $array_products = array())
{
    global $lang_tmp, $module_name, $module_file, $pro_config, $module_info;

    $xtpl = new XTemplate("block.cart.tpl", NV_ROOTDIR . "/themes/" . $module_info['template'] . "/modules/" . $module_file);
    $xtpl->assign('LANG', $lang_tmp);
    $xtpl->assign('TEMPLATE', $module_info['template']);
    $xtpl->assign('LINK_VIEW', NV_STATIC_URL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=cart");
    $xtpl->assign('WISHLIST', NV_STATIC_URL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=wishlist");
    $xtpl->assign('TOTAL', $array_data['total']);

    if (!empty($array_products)) {
        foreach ($array_products as $product) {
            $product['price'] = nv_get_price($product['id'], $pro_config['money_unit']);
            $xtpl->assign('PRODUCT', $product);
            $xtpl->parse('main.product.loop');
        }
        $xtpl->parse('main.product');
    }

    if (defined('NV_IS_USER')) {
        $xtpl->assign('LINK_HIS', NV_STATIC_URL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=history");

        if ($pro_config['active_wishlist']) {
            $xtpl->assign('NUM_ID', $array_data['wishlist']);
            $xtpl->parse('main.wishlist');
        }

        // Diem tich luy
        if ($pro_config['point_active']) {
            $xtpl->assign('POINT', $array_data['point']);
            $xtpl->parse('main.point');
        }
    }

    $xtpl->assign('MONEY_UNIT', $pro_config['money_unit']);
    $xtpl->assign('NUM', $array_data['num']);

    if ($pro_config['active_price'] == '1') {
        $xtpl->parse('main.enable.price');
    }

    if ($pro_config['active_order'] == '1') {
        $xtpl->parse('main.enable');
    } else {
        $xtpl->parse('main.disable');
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * @param array $data_content
 * @return string
 */
function nv_custom_tab_fields($data_content)
{
    global $module_file, $module_info, $lang_global, $lang_module;

    $xtpl = new XTemplate('custom_tab_fields.tpl', NV_ROOTDIR . "/themes/" . $module_info['template'] . "/modules/" . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('LANG', $lang_global);

    foreach ($data_content['template'] as $template) {
        if (!empty($data_content['array_custom_template'][$template['id']])) {
            $xtpl->assign('TEMPLATE_NAME', $template[NV_LANG_DATA . '_title']);

            foreach ($data_content['array_custom_template'][$template['id']] as $key => $val) {
                $xtpl->assign('ROW_NAME', isset($data_content['array_custom_lang'][$key]) ? $data_content['array_custom_lang'][$key] : $key);
                $xtpl->assign('ROW_VAL', $val);
                $xtpl->parse('main.template.loop');
            }

            $xtpl->parse('main.template');
        }
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}
