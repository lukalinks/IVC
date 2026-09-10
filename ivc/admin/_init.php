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

function admin_nav_groups()
{
    return array(
        array(
            'label' => 'Overview',
            'items' => array(
                'dashboard' => array('Dashboard', 'index.php'),
            ),
        ),
        array(
            'label' => 'Members',
            'items' => array(
                'members' => array('Members', 'members.php'),
                'memberships' => array('Memberships', 'memberships.php'),
                'codes' => array('Invite codes', 'codes.php'),
            ),
        ),
        array(
            'label' => 'Travel',
            'items' => array(
                'bookings' => array('Resort bookings', 'bookings.php'),
                'resorts' => array('Resorts', 'resorts.php'),
                'vacations' => array('Vacation packages', 'vacations.php'),
                'reservations' => array('Vacation reservations', 'reservations.php'),
            ),
        ),
        array(
            'label' => 'Partners',
            'items' => array(
                'listings' => array('Applications', 'listings.php'),
            ),
        ),
        array(
            'label' => 'System',
            'items' => array(
                'settings' => array('Settings', 'settings.php'),
                'logs' => array('Activity log', 'logs.php'),
            ),
        ),
    );
}

function admin_nav_items()
{
    $items = array();
    foreach (admin_nav_groups() as $group) {
        foreach ($group['items'] as $key => $item) {
            $items[$key] = $item;
        }
    }
    return $items;
}

function admin_page_subtitle($navKey)
{
    $copy = array(
        'dashboard' => 'Members, travel, and partner applications at a glance',
        'members' => 'Search, create, and manage member accounts',
        'memberships' => 'Paid and pending club membership records',
        'codes' => 'Invitation codes for new member sign-up',
        'bookings' => 'Member requests for IVC resorts',
        'resorts' => 'Resort catalog shown on the member home page',
        'vacations' => 'Packaged trips and available seats',
        'reservations' => 'Seats booked against vacation packages',
        'listings' => 'Industry partners must be approved before they appear in the directory',
        'settings' => 'Platform switches and administrator access',
        'logs' => 'Record of changes made in this console',
    );
    return isset($copy[$navKey]) ? $copy[$navKey] : 'International Vacation Club platform control';
}

function admin_nav_badges()
{
    static $badges = null;
    if ($badges !== null) {
        return $badges;
    }
    $badges = array(
        'bookings' => admin_count("SELECT COUNT(*) c FROM ivc_bookings WHERE status='pending' OR status='' OR status IS NULL"),
        'listings' => admin_count("SELECT COUNT(*) c FROM ivc_listings WHERE status='pending'"),
    );
    return $badges;
}

function admin_layout_start($title, $navKey, $subtitle = '')
{
    $flash = admin_take_flash();
    $groups = admin_nav_groups();
    $badges = admin_nav_badges();
    if ($subtitle === '') {
        $subtitle = admin_page_subtitle($navKey);
    }
    $account = function_exists('ivc_session_pernum') ? ivc_session_pernum() : '';
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
    <aside class="admin-side" id="adminSide">
        <a class="admin-brand" href="index.php">
            <img src="../assets/img/Picture7.png" alt="IVC">
            <span>Admin Console</span>
        </a>
        <nav>
            <?php foreach ($groups as $group): ?>
                <p class="admin-nav-label"><?= admin_h($group['label']) ?></p>
                <?php foreach ($group['items'] as $key => $item): ?>
                    <a class="<?= $key === $navKey ? 'is-active' : '' ?>" href="<?= admin_h($item[1]) ?>">
                        <span><?= admin_h($item[0]) ?></span>
                        <?php if (!empty($badges[$key])): ?>
                            <em class="admin-nav-count"><?= (int) $badges[$key] ?></em>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </nav>
        <div class="admin-side-foot">
            <a href="../home.php">Member site</a>
            <a href="../partners.php">Partner directory</a>
            <a href="../logout.php">Log out</a>
        </div>
    </aside>
    <div class="admin-main">
        <header class="admin-top">
            <div>
                <button class="admin-menu-btn" type="button" onclick="document.getElementById('adminSide').classList.toggle('is-open')">Menu</button>
                <h1><?= admin_h($title) ?></h1>
                <p><?= admin_h($subtitle) ?></p>
            </div>
            <div class="admin-user">
                <strong><?= admin_h($GLOBALS['adminEmail'] !== '' ? $GLOBALS['adminEmail'] : ('UID ' . $GLOBALS['adminUid'])) ?></strong>
                <span>Administrator<?= $account !== '' ? ' · Account # ' . admin_h($account) : ' · UID ' . (int) $GLOBALS['adminUid'] ?></span>
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
