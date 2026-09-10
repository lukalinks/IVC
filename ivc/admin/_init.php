<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/functions.php';
require_once dirname(__DIR__) . '/admin.inc.php';
require_once dirname(__DIR__) . '/partners.inc.php';

ivc_ensure_admin_schema();

if (empty($_SESSION['uid']) || !ivc_is_admin($_SESSION['uid'])) {
    header('Location: ../login.php');
    exit;
}

$adminUid = (int) $_SESSION['uid'];
$adminEmail = isset($_SESSION['email']) ? $_SESSION['email'] : '';

if (empty($_SESSION['ivc_admin_csrf'])) {
    $_SESSION['ivc_admin_csrf'] = bin2hex(random_bytes(16));
}

function admin_h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function admin_csrf_field()
{
    echo '<input type="hidden" name="csrf" value="' . admin_h($_SESSION['ivc_admin_csrf']) . '">';
}

function admin_posted()
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        return false;
    }
    $token = isset($_POST['csrf']) ? (string) $_POST['csrf'] : '';
    return hash_equals($_SESSION['ivc_admin_csrf'], $token);
}

function admin_flash($type, $message)
{
    $_SESSION['admin_flash'] = array('type' => $type, 'msg' => $message);
}

function admin_take_flash()
{
    $flash = isset($_SESSION['admin_flash']) ? $_SESSION['admin_flash'] : null;
    unset($_SESSION['admin_flash']);
    return $flash;
}

function admin_go($path)
{
    header('Location: ' . $path);
    exit;
}

function admin_bind(mysqli_stmt $stmt, $types, array $params)
{
    if ($types === '' || count($params) === 0) {
        return;
    }
    $refs = array();
    $refs[] = $types;
    foreach ($params as $key => $value) {
        $refs[] = &$params[$key];
    }
    call_user_func_array(array($stmt, 'bind_param'), $refs);
}

function admin_query($sql, $types = '', array $params = array())
{
    $stmt = $GLOBALS['mysqli']->prepare($sql);
    if (!$stmt) {
        return false;
    }
    admin_bind($stmt, $types, $params);
    if (!$stmt->execute()) {
        $stmt->close();
        return false;
    }

    $meta = $stmt->result_metadata();
    if (!$meta) {
        $stmt->close();
        return array();
    }

    $fields = array();
    $row = array();
    $refs = array();
    while ($field = $meta->fetch_field()) {
        $fields[] = $field->name;
        $row[$field->name] = null;
        $refs[] = &$row[$field->name];
    }
    call_user_func_array(array($stmt, 'bind_result'), $refs);

    $rows = array();
    while ($stmt->fetch()) {
        $copy = array();
        foreach ($fields as $fieldName) {
            $copy[$fieldName] = $row[$fieldName];
        }
        $rows[] = $copy;
    }

    $meta->close();
    $stmt->close();
    return $rows;
}

function admin_one($sql, $types = '', array $params = array())
{
    $rows = admin_query($sql, $types, $params);
    if (!$rows) {
        return null;
    }
    return $rows[0];
}

function admin_exec($sql, $types = '', array $params = array())
{
    $stmt = $GLOBALS['mysqli']->prepare($sql);
    if (!$stmt) {
        return false;
    }
    admin_bind($stmt, $types, $params);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

function admin_count($sql, $types = '', array $params = array())
{
    $row = admin_one($sql, $types, $params);
    if (!$row) {
        return 0;
    }
    return (int) reset($row);
}

function admin_page($total, $per = 25)
{
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    if ($page < 1) {
        $page = 1;
    }
    $pages = max(1, (int) ceil($total / $per));
    if ($page > $pages) {
        $page = $pages;
    }
    return array($page, $per, ($page - 1) * $per, $pages);
}

function admin_pager($page, $pages, $query)
{
    if ($pages <= 1) {
        return;
    }
    echo '<nav class="admin-pager">';
    for ($i = 1; $i <= $pages; $i++) {
        $q = $query;
        $q['page'] = $i;
        $href = '?' . http_build_query($q);
        $cls = $i === $page ? ' is-active' : '';
        echo '<a class="admin-page' . $cls . '" href="' . admin_h($href) . '">' . $i . '</a>';
    }
    echo '</nav>';
}

function admin_nav_items()
{
    return array(
        'dashboard' => array('Dashboard', 'index.php'),
        'members' => array('Members', 'members.php'),
        'bookings' => array('Bookings', 'bookings.php'),
        'listings' => array('Partners', 'listings.php'),
        'resorts' => array('Resorts', 'resorts.php'),
        'memberships' => array('Memberships', 'memberships.php'),
        'vacations' => array('Vacations', 'vacations.php'),
        'reservations' => array('Reservations', 'reservations.php'),
        'codes' => array('Invite codes', 'codes.php'),
        'settings' => array('Settings', 'settings.php'),
        'logs' => array('Activity log', 'logs.php'),
    );
}

function admin_layout_start($title, $navKey)
{
    $flash = admin_take_flash();
    $items = admin_nav_items();
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= admin_h($title) ?> · IVC Admin</title>
<link rel="stylesheet" href="admin.css">
</head>
<body>
<div class="admin-app">
    <aside class="admin-side">
        <a class="admin-brand" href="index.php">
            <img src="../assets/img/Picture7.png" alt="IVC">
            <span>Admin Console</span>
        </a>
        <nav>
            <?php foreach ($items as $key => $item): ?>
                <a class="<?= $key === $navKey ? 'is-active' : '' ?>" href="<?= admin_h($item[1]) ?>"><?= admin_h($item[0]) ?></a>
            <?php endforeach; ?>
        </nav>
        <div class="admin-side-foot">
            <a href="../home.php">Member site</a>
            <a href="../logout.php">Log out</a>
        </div>
    </aside>
    <div class="admin-main">
        <header class="admin-top">
            <div>
                <h1><?= admin_h($title) ?></h1>
                <p>International Vacation Club platform control</p>
            </div>
            <div class="admin-user">
                <strong><?= admin_h($GLOBALS['adminEmail'] !== '' ? $GLOBALS['adminEmail'] : ('UID ' . $GLOBALS['adminUid'])) ?></strong>
                <span>Administrator · <?= (int) $GLOBALS['adminUid'] ?></span>
            </div>
        </header>
        <?php if ($flash): ?>
            <div class="flash flash-<?= admin_h($flash['type']) ?>"><?= admin_h($flash['msg']) ?></div>
        <?php endif; ?>
    <?php
}

function admin_layout_end()
{
    ?>
    </div>
</div>
</body>
</html>
    <?php
}

function admin_status_class($status)
{
    $status = strtolower((string) $status);
    if (in_array($status, array('confirmed', 'approved', 'paid', 'active', 'completed'), true)) {
        return 'ok';
    }
    if (in_array($status, array('pending', ''), true)) {
        return 'wait';
    }
    if (in_array($status, array('cancelled', 'rejected', 'blocked', 'expired', 'hidden'), true)) {
        return 'bad';
    }
    return 'wait';
}
