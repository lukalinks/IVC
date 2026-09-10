<?php
include("config.php");
include("functions.php");
include("partners.inc.php");

if (empty($_SESSION['uid'])) {
    header('Location: login.php?role=partner');
    exit();
}

$types = ivc_business_types();
$err = '';
$msg = '';
$uid = (int) $_SESSION['uid'];
$existing = ivc_user_latest_listing($uid);
$existingStatus = $existing ? strtolower((string) $existing['status']) : '';
$canSubmit = !$existing || $existingStatus === 'rejected';

$submissionsOpen = !function_exists('ivc_setting') || ivc_setting('directory_submissions_enabled', '1') === '1';

if ($_POST) {
    if (!$submissionsOpen) {
        $err = 'Partner registrations are temporarily closed.';
    } elseif (!$canSubmit) {
        $err = 'You already have a partner application on file. Please check your application status.';
    } else {
        $business_type = isset($_POST['business_type']) ? trim($_POST['business_type']) : '';
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $country = isset($_POST['country']) ? trim($_POST['country']) : '';
        $city = isset($_POST['city']) ? trim($_POST['city']) : '';
        $address = isset($_POST['address']) ? trim($_POST['address']) : '';
        $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $website = isset($_POST['website']) ? trim($_POST['website']) : '';

        if (!isset($types[$business_type])) {
            $err = 'Please select a valid business type.';
        } elseif ($name === '') {
            $err = 'Please enter the business name.';
        } elseif ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $err = 'Please enter a valid email address.';
        } elseif ($country === '') {
            $err = 'Please enter the country.';
        }

        if ($err === '') {
            if ($existing && $existingStatus === 'rejected') {
                $listingId = (int) $existing['id'];
                $stmt = $GLOBALS['mysqli']->prepare("UPDATE ivc_listings SET business_type=?, name=?, description=?, country=?, city=?, address=?, phone=?, email=?, website=?, status='pending', created_at=NOW() WHERE id=? AND uid=?");
                if (!$stmt) {
                    $err = 'Unable to save your registration. Please try again.';
                } else {
                    $stmt->bind_param('sssssssssii', $business_type, $name, $description, $country, $city, $address, $phone, $email, $website, $listingId, $uid);
                    if ($stmt->execute()) {
                        $msg = 'Your partner application was resubmitted. It will appear in the directory after IVC admin approval.';
                        $existing = ivc_user_latest_listing($uid);
                        $existingStatus = 'pending';
                        $canSubmit = false;
                    } else {
                        $err = 'Unable to save your registration. Please try again.';
                    }
                    $stmt->close();
                }
            } else {
                $stmt = $GLOBALS['mysqli']->prepare("INSERT INTO ivc_listings (uid, business_type, name, description, country, city, address, phone, email, website, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
                if (!$stmt) {
                    $err = 'Unable to save your registration. Please try again.';
                } else {
                    $stmt->bind_param('isssssssss', $uid, $business_type, $name, $description, $country, $city, $address, $phone, $email, $website);
                    if ($stmt->execute()) {
                        $msg = 'Your partner application was submitted. An IVC administrator will review it before you appear in the directory.';
                        $existing = ivc_user_latest_listing($uid);
                        $existingStatus = 'pending';
                        $canSubmit = false;
                    } else {
                        $err = 'Unable to save your registration. Please try again.';
                    }
                    $stmt->close();
                }
            }
        }
    }
}

$prefill = array(
    'business_type' => $existing ? $existing['business_type'] : 'travel_agent',
    'name' => $existing ? $existing['name'] : '',
    'description' => $existing ? $existing['description'] : '',
    'country' => $existing ? $existing['country'] : '',
    'city' => $existing ? $existing['city'] : '',
    'address' => $existing ? $existing['address'] : '',
    'phone' => $existing ? $existing['phone'] : '',
    'email' => $existing && $existing['email'] !== '' ? $existing['email'] : (isset($_SESSION['email']) ? $_SESSION['email'] : ''),
    'website' => $existing ? $existing['website'] : '',
);

include('header.php');
?>

<div class="row" style="max-width:800px; margin:0 auto; border: 1px #650B14 solid; padding: 20px; margin-top: 100px;">
    <div class="col-md-12" style="text-align:center;">
        <h2 style="color:#650B14;">Partner Registration</h2>
        <p style="color:#650B14;">Hotels, resorts, travel agencies, and other industry partners must register with a SafeZone account. Applications are reviewed by IVC before appearing in the partner directory.</p>
        <?php if (function_exists('ivc_session_pernum') && ivc_session_pernum() !== ''): ?>
            <p style="color:#650B14; font-size:14px;">Registering as Account # <?= htmlspecialchars(ivc_session_pernum()) ?></p>
        <?php endif; ?>
        <?php if ($err !== ''): ?>
            <p class="alert alert-danger"><?= htmlspecialchars($err) ?></p>
        <?php endif; ?>
        <?php if ($msg !== ''): ?>
            <p class="alert alert-success"><?= htmlspecialchars($msg) ?></p>
            <p><a href="partner_status.php" class="btn btn-primary" style="background:#650B14; border-color:#650B14;">VIEW APPLICATION STATUS</a></p>
        <?php elseif (!$submissionsOpen): ?>
            <p class="alert alert-warning">Partner registrations are temporarily closed.</p>
        <?php elseif ($existingStatus === 'pending'): ?>
            <p class="alert alert-warning">Your partner application is pending admin approval.</p>
            <p><a href="partner_status.php" class="btn btn-primary" style="background:#650B14; border-color:#650B14;">VIEW APPLICATION STATUS</a></p>
        <?php elseif ($existingStatus === 'approved'): ?>
            <p class="alert alert-success">You are an approved IVC partner.</p>
            <p><a href="partners.php" class="btn btn-primary" style="background:#650B14; border-color:#650B14;">VIEW PARTNER DIRECTORY</a></p>
            <p><a href="partner_status.php">View application details</a></p>
        <?php else: ?>
        <form method="post" action="partner_submit.php" style="text-align:left; max-width:520px; margin:0 auto;">
            <div class="form-group">
                <label>Business type</label>
                <select name="business_type" class="form-control" required>
                    <option value="">SELECT</option>
                    <?php foreach ($types as $key => $label): ?>
                        <option value="<?= htmlspecialchars($key) ?>"<?= $prefill['business_type'] === $key ? ' selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Business name</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($prefill['name']) ?>" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($prefill['description']) ?></textarea>
            </div>
            <div class="form-group">
                <label>Country</label>
                <input type="text" name="country" class="form-control" value="<?= htmlspecialchars($prefill['country']) ?>" required>
            </div>
            <div class="form-group">
                <label>City</label>
                <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($prefill['city']) ?>">
            </div>
            <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($prefill['address']) ?>">
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($prefill['phone']) ?>">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($prefill['email']) ?>" required>
            </div>
            <div class="form-group">
                <label>Website</label>
                <input type="text" name="website" class="form-control" value="<?= htmlspecialchars($prefill['website']) ?>" placeholder="https://">
            </div>
            <button type="submit" class="btn btn-success" style="background:#018EF2; border-color:#018EF2; width:100%;">SUBMIT FOR APPROVAL</button>
        </form>
        <?php endif; ?>
        <p style="margin-top:20px;"><a href="partners.php">Back to directory</a> · <a href="partner_status.php">Application status</a></p>
    </div>
</div>
<script>
var navbar = document.getElementById("navbar");
if (navbar) { navbar.classList.add("sticky"); }
</script>
<?php include('footer.php'); ?>
