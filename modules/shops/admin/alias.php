<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 2-10-2010 18:49
 */

if( !defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$title = $nv_Request->get_title( 'title', 'post', '' );
$alias = change_alias( $title );

$id = $nv_Request->get_int( 'id', 'post', 0 );
$mod = $nv_Request->get_string( 'mod', 'post', '' );

if( $mod == 'content' )
{
	$stmt = $db->prepare( 'SELECT COUNT(*) FROM ' . $db_config['prefix'] . '_' . $module_data . '_rows WHERE id!=' . $id . ' AND ' . NV_LANG_DATA . '_alias= :alias' );
	$stmt->bindParam( ':alias', $alias, PDO::PARAM_STR );
	$stmt->execute();
	$nb = $stmt->fetchColumn();
	if( !empty( $nb ) )
	{
		if( $id )
		{
			$alias .= '-' . $id;
		}
		else
		{
			$nb = $db->query( 'SELECT MAX(id) FROM ' . $db_config['prefix'] . '_' . $module_data . '_rows' )->fetchColumn();
			$alias .= '-' . ( intval( $nb ) + 1 );
		}
	}
}

include NV_ROOTDIR . '/includes/header.php';
echo $alias;
include NV_ROOTDIR . '/includes/footer.php';