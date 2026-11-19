<?php
/**
 * permit.php
 * Renders the approved parking permit with the applicant's details and vehicle photo.
 */
session_start();
date_default_timezone_set('Asia/Manila');
require_once __DIR__ . '/includes/helpers.php';

// Nobody has applied yet -> go to the form.
if (empty($_SESSION['permit'])) {
    header('Location: index.php');
    exit;
}

$p      = $_SESSION['permit'];
$expiry = DateTime::createFromFormat('!m/d/Y', $p['license_expiry'] ?? '');

/*
 * IMPORTANT: Every value typed by the user must be printed through e()
 *            e.g.  <?= e($p['email']) ?>
 *            e() calls htmlspecialchars() so <script> tags cannot run (XSS).
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parking Permit</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<main class="container">
    <div class="alert alert-success">
        Application approved! Print this permit and display it on your dashboard.
    </div>

    <article class="card permit">
        <header class="permit-header">
            <div>
                <p class="permit-office">Campus Security Office</p>
                <h1 class="permit-title">Parking Permit</h1>
            </div>
            <div class="permit-no"><?= e($p['permit_no']) ?></div>
        </header>

        <div class="permit-body">
            <div class="permit-vehicle">
                <figure class="vehicle-photo">
                    <!-- TODO 17: Display the uploaded photo. src = $p['photo'],
                                  alt = "Photo of <make and model> with plate <plate number>" -->
                    <img src="<?= e($p['photo']) ?>" alt="Photo of <?= e($p['vehicle_model']) ?> with plate <?= e($p['plate_no']) ?>">

                </figure>

                <!-- TODO 18: The plate number inside <div class="plate"> -->
                    <div class="plate"><?= e($p['plate_no']) ?></div>

                <!-- TODO 19: "<vehicle type> · <make and model>" inside <p class="vehicle-caption">
                              (use &middot; for the dot) -->
                    <p class="vehicle-caption"><?= e($p['vehicle_type']) ?> &middot; <?= e($p['vehicle_model']) ?></p>

            </div>

            <div class="permit-details">
                <h2 class="owner-name"><?= e($p['full_name']) ?></h2>
                <p class="owner-id">ID No. <!-- TODO 20 -->ID No. <?= e($p['id_number']) ?></p>

                <table class="details-table">
                    <tr><th>Email</th>           <td><!-- TODO 21 --><?= e($p['email']) ?></td></tr>
                    <tr><th>Mobile</th>          <td><!-- TODO 22 --><?= e($p['mobile']) ?></td></tr>
                    <tr><th>License No.</th>     <td><!-- TODO 23 --><?= e($p['license_no']) ?></td></tr>
                    <!-- License expiry is shown as e.g. "March 15, 2028". -->
                    <tr><th>License Expiry</th>  <td><?= $expiry ? e($expiry->format('F j, Y')) : '' ?></td></tr>
                    <tr><th>Issued</th>          <td><?= e($p['issued_at']) ?></td></tr>
                </table>
            </div>

            <span class="stamp">Approved</span>
        </div>

        <footer class="permit-footer">
            <span>Valid until</span>
            <strong><?= e($p['valid_until']) ?></strong>
        </footer>
    </article>

    <div class="actions center">
        <a href="index.php" class="btn btn-secondary">Submit Another Application</a>
    </div>
</main>
</body>
</html>
