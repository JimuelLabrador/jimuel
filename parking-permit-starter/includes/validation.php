<?php
/**
 * validation.php
 * Regular-expression rules for the Vehicle Parking Permit activity.
 *
 * Each rule has:
 *   label   - human-friendly field name
 *   pattern - the regular expression the input must match   <-- YOU WRITE THESE
 *   hint    - the format shown to the user when the input is wrong
 *
 * Tip: test each pattern at https://regex101.com (choose the PCRE2 / PHP flavor)
 *      before pasting it here.
 */

const RULES = [
    'full_name' => [
        'label'   => 'Full Name',
        // TODO 1: 5-60 characters in total, and at least TWO words separated by single spaces.
        //         Each word starts with a letter, followed by letters, periods, apostrophes or hyphens.
        //         Must accept "Ma. Ñiña O'Neil"; must reject "Juan" and "Juan  Cruz" (two spaces).
        //         Hints: ( ...)+ repeats a group one or more times.
        //                (?=.{5,60}$) at the start checks the total length without consuming anything.
        //                \p{L} matches any letter but needs the /u flag.
        'pattern' => '/^(?=.{5,60}$)\p{L}[\p{L}\.\'-]*(?: \p{L}[\p{L}\.\'-]*)+$/u',
        'hint'    => 'At least a first and a last name separated by single spaces, 5-60 characters. e.g. Juan Dela Cruz, Ma. Clara Santos',
    ],
    'id_number' => [
        'label'   => 'School ID Number',
        // TODO 2: 4-digit year starting with 19 or 20, a hyphen, then exactly 5 digits.
        'pattern' => '/^(?:19|20)\d{2}-\d{5}$/',
        'hint'    => 'Format YYYY-NNNNN. e.g. 2023-00451',
    ],
    'email' => [
        'label'   => 'School Email',
        // TODO 3: letters, digits, periods or underscores, then exactly "@usjr.edu.ph" (any letter case).
        //         Must reject juan@gmail.com AND juan@usjrxedu.ph. (Why would the second one pass by mistake?)
        'pattern' => '/^[a-zA-A0-9._]+@usjr\.edu\.ph$/i',
        'hint'    => 'Your school email ending in @usjr.edu.ph. e.g. juan.delacruz@usjr.edu.ph',
    ],
    'mobile' => [
        'label'   => 'Mobile Number',
        // TODO 4: 09XX-XXX-XXXX : "09", 2 digits, hyphen, 3 digits, hyphen, 4 digits.
        'pattern' => '/^09\d{2}-\d{3}-\d{4}$/',
        'hint'    => 'Format 09XX-XXX-XXXX with hyphens. e.g. 0917-123-4567',
    ],
    'license_no' => [
        'label'   => "Driver's License Number",
        // TODO 5: one UPPERCASE letter + 2 digits, hyphen, 2 digits, hyphen, 6 digits.
        'pattern' => '/^[A-Z]\d{2}-\d{2}-\d{6}$/',
        'hint'    => 'Format A00-00-000000 (uppercase letter first). e.g. N01-23-456789',
    ],
    'license_expiry' => [
        'label'   => 'License Expiry Date',
        // TODO 6: MM/DD/YYYY. Month 01-12, day 01-31, year 2000-2099.
        //         The format itself contains "/", which is also the usual delimiter.
        //         Either escape every slash (\/) or use a different delimiter such as #...#
        //         (Whether the date really exists, e.g. 02/30/2028, is checked later with checkdate().)
        'pattern' => '#^(0[1-9]|1[0-2])/(0[1-9]|[12][0-9]|3[01])/(20\d{2})$#',
        'hint'    => 'Format MM/DD/YYYY. e.g. 03/15/2028',
    ],
    'plate_no' => [
        'label'   => 'Plate Number',
        // TODO 7: Uppercase only. Either a CAR plate: 3 letters, a space, 3 or 4 digits   (NBC 1234, NBC 123)
        //                         or a MOTORCYCLE plate: 3 digits, a space, 3 letters  (123 ABC)
        //         Hint: put both formats in ONE group with | and anchor the group: ^( ... | ... )$
        'pattern' => '/^([A-Z]{3} \d{3,4}|\d{3} [A-Z]{3})$/',
        'hint'    => 'Uppercase. Car: ABC 1234 or ABC 123. Motorcycle: 123 ABC.',
    ],
    'vehicle_model' => [
        'label'   => 'Make and Model',
        // TODO 8: 2-40 characters made of letters, digits, spaces or hyphens,
        //         AND at least one letter somewhere ("12345" must be rejected).
        //         Hint: a lookahead (?=.*[A-Za-z]) right after ^
        'pattern' => '/^(?=.*[A-Za-z])[A-Za-z0-9 -]{2,40}$/',
        'hint'    => '2-40 characters: letters, digits, spaces and hyphens, with at least one letter. e.g. Toyota Vios, Honda Click 125i',
    ],
];

/** The only values accepted from the Vehicle Type drop-down. */
const VEHICLE_TYPES = ['Car', 'Motorcycle'];

/** Photo upload rules */
const PHOTO_MAX_BYTES  = 2 * 1024 * 1024;   // 2 MB
// TODO 9: The ORIGINAL file name must end in .jpg, .jpeg, .png or .webp (any letter case).
//         Allowed characters before the extension: letters, digits, underscore, hyphen, period, space.
const PHOTO_NAME_PATTERN = '/^[a-zA-Z0-9_\-\. ]+\.(jpg|jpeg|png|webp)$/i';
const PHOTO_MIME_TYPES   = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

/**
 * Validates the text fields against RULES.
 * Returns an array of error messages keyed by field name (empty = all valid).
 */
function validate_fields(array $input): array
{
    $errors = [];

    foreach (RULES as $field => $rule) {
        $value = $input[$field] ?? '';

        // TODO 10: If $value is empty      -> $errors[$field] = "{$rule['label']} is required.";
        //          Else if it does NOT match $rule['pattern'] (use preg_match)
        //                                  -> $errors[$field] = "Invalid {$rule['label']}. {$rule['hint']}";
        if ($value === '') {
            $errors[$field] = "{$rule['label']} is required.";
        } elseif (!preg_match($rule['pattern'], $value)) {
            $errors[$field] = "Invalid {$rule['label']}. {$rule['hint']}";
        }

    }

    // TODO 11: A drop-down can be edited in the browser (try it with DevTools!), so check it here.
    //          If $input['vehicle_type'] is not one of VEHICLE_TYPES (use in_array with strict = true)
    //          -> $errors['vehicle_type'] = 'Please choose a valid vehicle type.';
    if (!in_array($input['vehicle_type'] ?? '', VEHICLE_TYPES, true)) {
        $errors['vehicle_type'] = 'Please choose a valid vehicle type.';
    }


    // Regex checks the SHAPE of the date; checkdate() checks it actually exists (rejects 02/30/2028).
    $parts = explode('/', $input['license_expiry'] ?? '');
    if (!isset($errors['license_expiry']) && count($parts) === 3) {
        [$m, $d, $y] = array_map('intval', $parts);
        if (!checkdate($m, $d, $y)) {
            $errors['license_expiry'] = 'License expiry is not a real calendar date.';
        } elseif (DateTime::createFromFormat('!m/d/Y', $input['license_expiry']) < new DateTime('today')) {
            $errors['license_expiry'] = 'Your driver\'s license has expired. Renew it before applying.';
        }
    }

    return $errors;
}

/**
 * Validates the uploaded vehicle photo ($_FILES['photo']).
 * Returns an error message, or null if the upload is acceptable.
 */
function validate_photo(?array $file): ?string
{
    if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return 'A photo of the vehicle is required.';
    }
    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
        return 'Photo is too large. Maximum size is 2 MB.';
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return 'The photo could not be uploaded. Please try again.';
    }

    // TODO 12: If $file['size'] is greater than PHOTO_MAX_BYTES
    //          -> return 'Photo is too large. Maximum size is 2 MB.';
    if ($file['size'] > PHOTO_MAX_BYTES) {
        return 'Photo is too large. Maximum size is 2 MB.';
    }


    // TODO 13: If $file['name'] does NOT match PHOTO_NAME_PATTERN
    //          -> return 'Photo must be a .jpg, .jpeg, .png or .webp file (file name: letters, digits, spaces, - _ . only).';
    if (!preg_match(PHOTO_NAME_PATTERN, $file['name'])) {
        return 'Photo must be a .jpg, .jpeg, .png or .webp file (file name: letters, digits, spaces, - _ . only).';
    }


    // Never trust the extension alone - check the real content type.
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!array_key_exists($mime, PHOTO_MIME_TYPES) || getimagesize($file['tmp_name']) === false) {
        return 'The uploaded file is not a valid JPG, PNG or WebP image.';
    }
    return null;
}
