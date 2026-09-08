<?php
include("config.php");
include("functions.php");
include("partners.inc.php");

$types = ivc_business_types();
$err = '';
$msg = '';
$uid = isset($_SESSION['uid']) ? (int) $_SESSION['uid'] : 0;

$submissionsOpen = !function_exists('ivc_setting') || ivc_setting('directory_submissions_enabled', '1') === '1';

if ($_POST) {
    if (!$submissionsOpen) {
        $err = 'Directory submissions are temporarily closed.';
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
        $stmt = $GLOBALS['mysqli']->prepare("INSERT INTO ivc_listings (uid, business_type, name, description, country, city, address, phone, email, website, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
        if (!$stmt) {
            $err = 'Unable to save your listing. Please try again.';
        } else {
            $stmt->bind_param('isssssssss', $uid, $business_type, $name, $description, $country, $city, $address, $phone, $email, $website);
            if ($stmt->execute()) {
                $msg = 'Your listing was submitted. It will appear in the directory after IVC approval.';
            } else {
                $err = 'Unable to save your listing. Please try again.';
            }
            $stmt->close();
        }
    }
    }
}

include('header.php');
?>

<div class="row" style="max-width:800px; margin:0 auto; border: 1px #650B14 solid; padding: 20px; margin-top: 100px;">
    <div class="col-md-12" style="text-align:center;">
        <h2 style="color:#650B14;">List Your Business</h2>
        <p style="color:#650B14;">Hotels, resorts, travel agencies, car rentals, and tour services can apply to be shown to IVC members.</p>
        <?php if ($err !== ''): ?>
            <p class="alert alert-danger"><?= htmlspecialchars($err) ?></p>
        <?php endif; ?>
        <?php if ($msg !== ''): ?>
            <p class="alert alert-success"><?= htmlspecialchars($msg) ?></p>
            <p><a href="partners.php" class="btn btn-primary" style="background:#650B14; border-color:#650B14;">VIEW DIRECTORY</a></p>
        <?php elseif (!$submissionsOpen): ?>
            <p class="alert alert-warning">Directory submissions are temporarily closed.</p>
        <?php else: ?>
        <form method="post" action="partner_submit.php" style="text-align:left; max-width:520px; margin:0 auto;">
            <div class="form-group">
                <label>Business type</label>
                <select name="business_type" class="form-control" required>
                    <option value="">SELECT</option>
                    <?php foreach ($types as $key => $label): ?>
                        <option value="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Business name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="4"></textarea>
            </div>
            <div class="form-group">
                <label>Country</label>
                <input type="text" name="country" class="form-control" required>
            </div>
            <div class="form-group">
                <label>City</label>
                <input type="text" name="city" class="form-control">
            </div>
            <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" class="form-control">
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" class="form-control">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Website</label>
                <input type="text" name="website" class="form-control" placeholder="https://">
            </div>
            <button type="submit" class="btn btn-success" style="background:#018EF2; border-color:#018EF2; width:100%;">SUBMIT LISTING</button>
        </form>
        <?php endif; ?>
        <p style="margin-top:20px;"><a href="partners.php">Back to directory</a></p>
    </div>
</div>
<script>
var navbar = document.getElementById("navbar");
if (navbar) { navbar.classList.add("sticky"); }
</script>
<?php include('footer.php'); ?>
