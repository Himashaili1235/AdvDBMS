<?php
// NEVER expose this key in public code or GitHub
define('ENCRYPTION_KEY', 'My$3cureK3y@ForGrades2025!encrypt#'); // 32 characters

function encryptGrade($plaintext) {
    $key = ENCRYPTION_KEY;
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $ciphertext = openssl_encrypt($plaintext, 'aes-256-cbc', $key, 0, $iv);
    return base64_encode($iv . '::' . $ciphertext);
}

function decryptGrade($encrypted) {
    $key = ENCRYPTION_KEY;
    list($iv, $ciphertext) = explode('::', base64_decode($encrypted), 2);
    return openssl_decrypt($ciphertext, 'aes-256-cbc', $key, 0, $iv);
}
?>
