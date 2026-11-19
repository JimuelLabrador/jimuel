<?php
/**
 * index.php
 * Displays the vehicle parking permit application form.
 * If apply.php sent us back with errors, they are shown beside each field
 * and the previously typed values are restored (sticky form).
 */
session_start();
require_once __DIR__ . '/includes/validation.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/database/db.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parking Permit Application</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<main class="container">
    <header class="page-header">
        <p class="eyebrow">Campus Security Office</p>
        <h1>Vehicle Parking Permit Application</h1>
        <p>Fill in every field using the format shown. All fields are required.</p>
    </header>

    <?php if (!empty($_SESSION['errors'])): ?>
        <div class="alert alert-error">
            Please correct the <?= count($_SESSION['errors']) ?> highlighted field(s) below.
        </div>
    <?php endif; ?>

    <!--
        The form has NO validation of its own: every rule is checked in PHP (apply.php).
        enctype="multipart/form-data" is REQUIRED for file uploads.
    -->
    <form action="apply.php" method="post" enctype="multipart/form-data" class="card form">

        <fieldset>
            <legend>Owner Information</legend>

                <div class="field">
                    <label for="id_number">School ID No. <span class="format">YYYY-NNNNN</span></label>
                    <input type="text" id="id_number" name="id_number" class="<?= invalid_class('id_number') ?>"
                           value="<?= old('id_number') ?>" placeholder="2023-00451">
                    <?= error_for('id_number') ?>
                </div>

            <div class="row">
                <div class="field grow-2">
                    <label for="first_name">First Name <span class="format">First Name</span></label>
                    <input type="text" id="first_name" name="first_name" class="<?= invalid_class('first_name') ?>"
                           value="<?= old('first_name') ?>" placeholder="Juan">
                    <?= error_for('first_name') ?>
                </div>
                <div class="field grow-2">
                    <label for="last_name">Last Name <span class="format">Last Name</span></label>
                    <input type="text" id="last_name" name="last_name" class="<?= invalid_class('last_name') ?>"
                           value="<?= old('last_name') ?>" placeholder="Dela Cruz">
                    <?= error_for('last_name') ?>
                </div>
            </div>

            <div class="row">
                <div class="field grow-2">
                    <label for="email">School Email <span class="format">@usjr.edu.ph only</span></label>
                    <input type="text" id="email" name="email" class="<?= invalid_class('email') ?>"
                           value="<?= old('email') ?>" placeholder="juan.delacruz@usjr.edu.ph">
                    <?= error_for('email') ?>
                </div>
                <div class="field">
                    <label for="mobile">Mobile No. <span class="format">09XX-XXX-XXXX</span></label>
                    <input type="text" id="mobile" name="mobile" class="<?= invalid_class('mobile') ?>"
                           value="<?= old('mobile') ?>" placeholder="0917-123-4567">
                    <?= error_for('mobile') ?>
                </div>
            </div>
        </fieldset>

        <fieldset>
            <legend>Driver's License</legend>

            <div class="row">
                <div class="field">
                    <label for="license_no">License No. <span class="format">A00-00-000000</span></label>
                    <input type="text" id="license_no" name="license_no" class="<?= invalid_class('license_no') ?>"
                           value="<?= old('license_no') ?>" placeholder="N01-23-456789">
                    <?= error_for('license_no') ?>
                </div>
                <div class="field">
                    <label for="license_expiry">Expiry Date <span class="format">MM/DD/YYYY</span></label>
                    <input type="text" id="license_expiry" name="license_expiry" class="<?= invalid_class('license_expiry') ?>"
                           value="<?= old('license_expiry') ?>" placeholder="03/15/2028">
                    <?= error_for('license_expiry') ?>
                </div>
            </div>
        </fieldset>

        <fieldset>
            <legend>Vehicle</legend>

            <div class="row">
                <div class="field">
                    <label for="vehicle_type">Vehicle Type</label>
                    <select id="vehicle_type" name="vehicle_type" class="<?= invalid_class('vehicle_type') ?>">
                        <option value="">-- Select --</option>
                        <?php foreach (VEHICLE_TYPES as $row=>$type): ?>
                            <option value="<?= e($type['vtypeid']) ?>"<?= selected('vehicle_type', $type
                            ['vtypeid']) ?>><?= e($type['vtypename']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?= error_for('vehicle_type') ?>
                </div>
                <div class="field">
                    <label for="plate_no">Plate No. <span class="format">ABC 1234 / 123 ABC</span></label>
                    <input type="text" id="plate_no" name="plate_no" class="<?= invalid_class('plate_no') ?>"
                           value="<?= old('plate_no') ?>" placeholder="NBC 1234">
                    <?= error_for('plate_no') ?>
                </div>
            </div>

            <div class="field">
                <label for="vehicle_model">Make and Model</label>
                <input type="text" id="vehicle_model" name="vehicle_model" class="<?= invalid_class('vehicle_model') ?>"
                       value="<?= old('vehicle_model') ?>" placeholder="Toyota Vios">
                <?= error_for('vehicle_model') ?>
            </div>
        </fieldset>

        <fieldset>
            <legend>Vehicle Photo</legend>
            <div class="field">
                <label for="photo">Upload Photo <span class="format">JPG, PNG or WebP, max 2 MB, plate visible</span></label>
                <input type="hidden" name="MAX_FILE_SIZE" value="<?= PHOTO_MAX_BYTES ?>">
                <input type="file" id="photo" name="photo" class="<?= invalid_class('photo') ?>">
                <?= error_for('photo') ?>
                <p class="help-text">For security, you must choose the photo again if the form has errors.</p>
            </div>
        </fieldset>

        <div class="actions">
            <button type="reset" class="btn btn-secondary">Clear</button>
            <button type="submit" class="btn btn-primary">Submit Application</button>
        </div>
    </form>
</main>
</body>
</html>
<?php
// Errors and old input are shown once only.
unset($_SESSION['errors'], $_SESSION['old']);
