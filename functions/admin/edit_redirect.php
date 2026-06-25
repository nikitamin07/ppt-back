<?php


const INDEXED = 1;

$page_redirect = [
    'catalog' => [
        'table' => 'categories',
        'url' => 'catalogs',
    ],
    'product' => [
        'table' => 'product_list',
        'url' => 'products',
    ],
];

if ($_POST['href']) {
    require_once '../../settings.php';
    require_once '../../modules/core_includes/db.php';
    $return['success'] = false;
    preg_match_all('/\/([^\/]+)/', $_POST['href'], $matches);
    if (isset($matches[1][2]) && array_key_exists($matches[1][1],$page_redirect)) {
        $page_info = $page_redirect[$matches[1][1]];
        $page_url = end($matches[1]);
        // Если товар, убираем артикул с ссылки
        if ($matches[1][1] == 'product') {
            $page_url = preg_replace('/^(\d+-)/', '', $page_url);
        }
        $res = $db->query("SELECT id FROM {$page_info['table']} WHERE alias = '$page_url'");
        $return['sql'] = "";
        if ($page_id = $res->fetch_object()) {
            $return['url'] = 'admin/' . $page_info['url'] . '/' . $page_id->id;
            $return['success'] = true;
        } else {
            $return['error'] = 'Не доступно для редактирования!';
        }
    } else {
        $page_url = end($matches[1]);

        $res = $db->query("SELECT id FROM pages WHERE alias = '$page_url'");
        if ($page_id = $res->fetch_object()) {
            $return['url'] = 'admin/pages/' . $page_id->id;
            $return['success'] = true;
        } else {
            $return['error'] = 'Не доступно для редактирования!';
        }
    }
    print json_encode($return, JSON_UNESCAPED_UNICODE);
    die();
}
?>