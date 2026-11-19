<?php
/**
 * apply.php
 * Processes the parking permit application:
 *   1. Validates every text field with a regular expression (and the drop-down with in_array)
 *   2. Validates and saves the uploaded vehicle photo
 *   3. On error  -> sends the user back to index.php with messages
 *      On success -> stores the permit in the session and redirects to permit.php
 *
 * The redirect after a successful POST (Post/Redirect/Get) prevents the
 * form from being re-submitted when the user refreshes the permit page.
 */
session_start();
date_default_timezone_set('Asia/Manila');
require_once __DIR__ . '/includes/validation.php';
require_once __DIR__ . '/includes/db.php';

// Only accept form submissions.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;

}

// Collect and trim every input.
$fields = ['full_name', 'id_number', 'email', 'mobile', 'license_no', 'license_expiry',
           'vehicle_type', 'plate_no', 'vehicle_model'];
$input  = [];
foreach ($fields as $field) {
    $input[$field] = trim($_POST[$field] ?? '');
}

// Step 1 + 2: validate.
$errors     = validate_fields($input);
$photoError = validate_photo($_FILES['photo'] ?? null);
if ($photoError !== null) {
    $errors['photo'] = $photoError;
}

// Step 3a: errors -> back to the form.
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old']    = $input;
    header('Location: index.php');
    exit;
}

// Save the photo with a NEW, unique name so users cannot overwrite each
// other's files or upload something like "../index.php".
$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}
$mime      = (new finfo(FILEINFO_MIME_TYPE))->file($_FILES['photo']['tmp_name']);
$extension = PHOTO_MIME_TYPES[$mime];

// TODO 14: Build a unique file name such as  vehicle_3f9a1c0b7d2e4f61.jpg
//          Hint: 'vehicle_' . bin2hex(random_bytes(8)) . '.' . $extension
$photoName = 'vehicle_' . bin2hex(random_bytes(8)) . '.' . $extension;

// TODO 15: Move the temporary upload into the uploads folder with move_uploaded_file().
//          Source: $_FILES['photo']['tmp_name']   Destination: $uploadDir . $photoName
//          If it fails, set $_SESSION['errors'] = ['photo' => 'The photo could not be saved on the server.'];
//          set $_SESSION['old'] = $input; redirect to index.php and exit.
if (!move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . $photoName)) {
    $_SESSION['errors'] = ['photo' => 'The photo could not be saved on the server.'];
    $_SESSION['old']    = $input;
    header('Location: index.php');
    exit;
}


// The permit is valid for one year, but never longer than the driver's license.
$licenseExpiry = DateTime::createFromFormat('!m/d/Y', $input['license_expiry']);
$oneYear       = new DateTime('today +1 year');
$validUntil    = $licenseExpiry ? min($licenseExpiry, $oneYear) : $oneYear;

// Step 3b: success. Keep only what the permit page needs.
$_SESSION['permit'] = [
    'permit_no'      => 'PKG-' . date('Y') . '-' . str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT),
    'first_name'      => $input['first_name'],
    'last_name'      => $input['last_name'],
    // TODO 16: Add id_number, email, mobile, license_no, license_expiry,
    //          vehicle_type, plate_no and vehicle_model from $input.
    'id_number'      => $input['id_number'],
    'email'          => $input['email'],
    'mobile'         => $input['mobile'],
    'license_no'     => $input['license_no'],
    'license_expiry' => $input['license_expiry'],
    'vehicle_type'   => $input['vehicle_type'],
    'plate_no'       => $input['plate_no'],
    'vehicle_model'  => $input['vehicle_model'],

    'photo'          => 'uploads/' . $photoName,
    'issued_at'      => date('F j, Y'),
    'valid_until'    => $validUntil->format('F j, Y'),
];

//student
$studentEntrySQL = "INSERT INTO students VALUES (?,?,?,?,?)";

$studentEntryStatement =  $pdo->prepare($studentEntrySQL);

$studentEntryStatement->bindParam(1,$input['id_number'],PDO::PARAM_STR);
$studentEntryStatement->bindParam(1,$input['first_name'],PDO::PARAM_STR);
$studentEntryStatement->bindParam(1,$input['last_name'],PDO::PARAM_STR);
$studentEntryStatement->bindParam(1,$input['mobile'],PDO::PARAM_STR);
$studentEntryStatement->bindParam(1,$input['email'],PDO::PARAM_STR);  

try {
    $studentEntryStatement->execute();

    if($studentEntryStatement->rowCount() <= 0){
        $error++;
    }
}catch (PDOException $error){
    die('Error '.$error->getCode().' '.$error->getMessage());    
}

//license

$licenseEntrySQL = "INSERT INTO licenses VALUES (?, ?, ?)";

$licenseEntryStatement = $pdo->prepare($licenseEntrySQL);

$licenseEntryStatement->bindParam(1, $input['license_number'],PDO::PARAM_STR);
$licenseEntryStatement->bindParam(2, $input['license_expiry'],PDO::PARAM_STR);
$licenseEntryStatement->bindParam(3, $input['id_number'],PDO::PARAM_STR);

try {
    $licenseEntryStatement->execute();

    if($licenseEntryStatement->rowCount() <= 0){
        $error++;
    }
}catch (PDOException $error){
    die('Error '.$error->getCode().' '.$error->getMessage());    
}

//vehicle

$vehicleEntrySQL = "INSERT INTO vehicles VALUES (null,?,?,?,?)";

$vehicleEntryStatement = $pdo->prepare($vehicleEntrySQL);

$vehicleEntryStatement->bindParam(1, $input['plate_no'],PDO::PARAM_STR);
$vehicleEntryStatement->bindParam(2, $input['vehicle_model'],PDO::PARAM_STR);
$vehicleEntryStatement->bindParam(3, $input['vehicle_type'],PDO::PARAM_STR);
$vehicleEntryStatement->bindParam(4, $input['student_id'],PDO::PARAM_STR);

try {
    $vehicleStatement->execute();
    if($vehicleEntryStatement->rowCount() <= 0){
        $errors++;
    }
} catch(PDOException $e) {
    die('Error '.$error->getCode().' '.$error->getMessage());    
}

//vehicle upload

$vehicleUploadSQL = "INSERT INTO vphotouploads VALUES (null,?,?,?,?,?)";

$vehicleUploadStatement = $pdo->prepare($vehicleUploadSQL);

$vehicleUploadStatement->bindParam(1, $mime, PDO::PARAM_STR);
$vehicleUploadStatement->bindParam(2, $uploadDir, PDO::PARAM_STR);
$vehicleUploadStatement->bindParam(3, date('Y-m-d', time()), PDO::PARAM_STR);
$vehicleUploadStatement->bindParam(4, date('h:i:s', time()), PDO::PARAM_STR);
$vehicleUploadStatement->bindParam(5, $input['student_id'],PDO::PARAM_STR);


try {
    $vehicleUploadStatement->execute();

    if($vehicleUploadStatement->rowCount() <= 0){
        $error++;
    }
}catch (PDOException $e){
    die('Error '.$error->getCode().' '.$error->getMessage());    
}

if($errors === 0) {
    header('Location: permit.php');
}else{
    header('Location: .php');
}

exit;
