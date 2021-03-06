<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2018 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Sun, 07 Jan 2018 03:36:43 GMT
 */
if (!defined('NV_IS_MOD_WORKFORCE')) die('Stop!!!');

$row = array();
$error = array();
$row['id'] = $nv_Request->get_int('id', 'post,get', 0);

if ($row['id'] > 0) {
    $lang_module['workforce_add'] = $lang_module['workforce_edit'];
    $row = $db->query('SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . ' WHERE id=' . $row['id'])->fetch();
    if (empty($row)) {
        Header('Location: ' . NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
        die();
    }
} else {
    $row['id'] = 0;
    $row['first_name'] = '';
    $row['last_name'] = '';
    $row['gender'] = 1;
    $row['birthday'] = 0;
    $row['main_phone'] = '';
    $row['other_phone'] = '';
    $row['main_email'] = '';
    $row['other_email'] = '';
    $row['address'] = '';
    $row['knowledge'] = '';
    $row['image'] = '';
    $row['addtime'] = 0;
    $row['edittime'] = 0;
    $row['useradd'] = 0;
    $row['status'] = 1;
    $row['userid'] = 0;
    $row['jointime'] = 0;
    $row['salary'] = 0;
    $row['allowance'] = 0;
}

if ($nv_Request->isset_request('submit', 'post')) {
    $row['first_name'] = $nv_Request->get_title('first_name', 'post', '');
    $row['last_name'] = $nv_Request->get_title('last_name', 'post', '');
    $row['gender'] = $nv_Request->get_int('gender', 'post', 0);
    $row['salary'] = $nv_Request->get_string('salary', 'post', 0);
    $row['salary'] = preg_replace('/[^0-9]/', '', $row['salary']);
    $row['allowance'] = $nv_Request->get_string('allowance', 'post', 0);
    $row['allowance'] = preg_replace('/[^0-9]/', '', $row['allowance']);

    if (preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $nv_Request->get_string('birthday', 'post'), $m)) {
        $_hour = 23;
        $_min = 23;
        $row['birthday'] = mktime($_hour, $_min, 59, $m[2], $m[1], $m[3]);
    } else {
        $row['birthday'] = 0;
    }

    if (preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $nv_Request->get_string('jointime', 'post'), $m)) {
        $_hour = 23;
        $_min = 23;
        $row['jointime'] = mktime($_hour, $_min, 59, $m[2], $m[1], $m[3]);
    } else {
        $row['jointime'] = 0;
    }

    $row['main_phone'] = $nv_Request->get_title('main_phone', 'post', '');
    $row['other_phone'] = $nv_Request->get_title('other_phone', 'post', '');
    $row['main_email'] = $nv_Request->get_title('main_email', 'post', '');
    $row['other_email'] = $nv_Request->get_title('other_email', 'post', '');
    $row['address'] = $nv_Request->get_title('address', 'post', '');
    $row['knowledge'] = $nv_Request->get_string('knowledge', 'post', '');
    $row['image'] = $nv_Request->get_title('image', 'post', '');
    $row['userid'] = $nv_Request->get_int('userid', 'post', 0);

    if (is_file(NV_DOCUMENT_ROOT . $row['image'])) {
        $row['image'] = substr($row['image'], strlen(NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/'));
    } else {
        $row['image'] = '';
    }

    if (empty($row['userid'])) {
        $error[] = $lang_module['error_required_userid'];
    } elseif (empty($row['first_name'])) {
        $error[] = $lang_module['error_required_first_name'];
    } elseif (empty($row['last_name'])) {
        $error[] = $lang_module['error_required_last_name'];
    } elseif (empty($row['birthday'])) {
        $error[] = $lang_module['error_required_birthday'];
    } elseif (empty($row['main_phone'])) {
        $error[] = $lang_module['error_required_main_phone'];
    } elseif (empty($row['main_email'])) {
        $error[] = $lang_module['error_required_main_email'];
    }

    if (empty($error)) {
        try {
            if (empty($row['id'])) {
                $stmt = $db->prepare('INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . ' (userid, first_name, last_name, gender, birthday, main_phone, other_phone, main_email, other_email, address, knowledge, image, jointime, salary, allowance, addtime, edittime, useradd) VALUES (:userid, :first_name, :last_name, :gender, :birthday, :main_phone, :other_phone, :main_email, :other_email, :address, :knowledge, :image, :jointime, :salary, :allowance, ' . NV_CURRENTTIME . ', ' . NV_CURRENTTIME . ', ' . $user_info['userid'] . ')');
            } else {
                $stmt = $db->prepare('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . ' SET userid = :userid, first_name = :first_name, last_name = :last_name, gender = :gender, birthday = :birthday, main_phone = :main_phone, other_phone = :other_phone, main_email = :main_email, other_email = :other_email, address = :address, knowledge = :knowledge, image = :image, jointime = :jointime, salary = :salary, allowance = :allowance, edittime = ' . NV_CURRENTTIME . ' WHERE id=' . $row['id']);
            }
            $stmt->bindParam(':userid', $row['userid'], PDO::PARAM_INT);
            $stmt->bindParam(':first_name', $row['first_name'], PDO::PARAM_STR);
            $stmt->bindParam(':last_name', $row['last_name'], PDO::PARAM_STR);
            $stmt->bindParam(':gender', $row['gender'], PDO::PARAM_INT);
            $stmt->bindParam(':birthday', $row['birthday'], PDO::PARAM_INT);
            $stmt->bindParam(':main_phone', $row['main_phone'], PDO::PARAM_STR);
            $stmt->bindParam(':other_phone', $row['other_phone'], PDO::PARAM_STR);
            $stmt->bindParam(':main_email', $row['main_email'], PDO::PARAM_STR);
            $stmt->bindParam(':other_email', $row['other_email'], PDO::PARAM_STR);
            $stmt->bindParam(':address', $row['address'], PDO::PARAM_STR);
            $stmt->bindParam(':knowledge', $row['knowledge'], PDO::PARAM_STR, strlen($row['knowledge']));
            $stmt->bindParam(':image', $row['image'], PDO::PARAM_STR);
            $stmt->bindParam(':jointime', $row['jointime'], PDO::PARAM_INT);
            $stmt->bindParam(':salary', $row['salary'], PDO::PARAM_STR);
            $stmt->bindParam(':allowance', $row['allowance'], PDO::PARAM_STR);
            $exc = $stmt->execute();
            if ($exc) {
                $nv_Cache->delMod($module_name);
                Header('Location: ' . NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
                die();
            }
        } catch (PDOException $e) {
            trigger_error($e->getMessage());
        }
    }
}

$row['birthday'] = !empty($row['birthday']) ? date('d/m/Y', $row['birthday']) : '';
$row['jointime'] = !empty($row['jointime']) ? date('d/m/Y', $row['jointime']) : '';
$row['salary'] = !empty($row['salary']) ? $row['salary'] : '';
$row['allowance'] = !empty($row['allowance']) ? $row['allowance'] : '';

if (!empty($row['image']) and is_file(NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/' . $row['image'])) {
    $row['image'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $row['image'];
}

$userinfo = array();
if ($row['userid'] > 0) {
    $userinfo = $rows = $db->query('SELECT userid, first_name, last_name, username FROM ' . NV_USERS_GLOBALTABLE . ' WHERE userid=' . $row['userid'])->fetch();
    $userinfo['fullname'] = nv_show_name_user($userinfo['first_name'], $userinfo['last_name'], $userinfo['username']);
}

$xtpl = new XTemplate($op . '.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('MODULE_UPLOAD', $module_upload);
$xtpl->assign('OP', $op);
$xtpl->assign('ROW', $row);
$xtpl->assign('URL_USERS', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&get_user_json=1');

foreach ($array_gender as $index => $value) {
    $ck = $index == $row['gender'] ? 'checked="checked"' : '';
    $xtpl->assign('GENDER', array(
        'index' => $index,
        'value' => $value,
        'checked' => $ck
    ));
    $xtpl->parse('main.gender');
}

if (!empty($userinfo)) {
    $xtpl->assign('USER_INFO', $userinfo);
    $xtpl->parse('main.user_info');
}

if (!empty($error)) {
    $xtpl->assign('ERROR', implode('<br />', $error));
    $xtpl->parse('main.error');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

$page_title = $lang_module['workforce_add'];
$array_mod_title[] = array(
    'title' => $lang_module['workforce'],
    'link' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name
);
$array_mod_title[] = array(
    'title' => $page_title,
    'link' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op
);

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';