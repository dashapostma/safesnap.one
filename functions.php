<?php
// Connect to the SQLite database
function connectDb() {
    return new SQLite3('database/safesnap.db');
}

// Generate a random ID for each secret
function generateId() {
    return bin2hex(random_bytes(8));
}

// Insert the secret into the database with all required fields
function insertSecret($secret, $userId = null, $passphraseHash = null, $burnAfterRead = false, $expiryDatetime = null) {
    $db = connectDb();
    $id = generateId();
    $createdAt = date('Y-m-d H:i:s');

    $stmt = $db->prepare("INSERT INTO secrets 
        (id, encrypted_text, created_at, user_id, passphrase_hash, burn_after_read, expiry_datetime, updated_at) 
        VALUES 
        (:id, :secret, :created_at, :user_id, :passphrase_hash, :burn_after_read, :expiry_datetime, :updated_at)");

    $stmt->bindValue(':id', $id, SQLITE3_TEXT);
    $stmt->bindValue(':secret', $secret, SQLITE3_TEXT);
    $stmt->bindValue(':created_at', $createdAt, SQLITE3_TEXT);
    $stmt->bindValue(':user_id', $userId, SQLITE3_INTEGER);
    $stmt->bindValue(':passphrase_hash', $passphraseHash, SQLITE3_TEXT);
    $stmt->bindValue(':burn_after_read', $burnAfterRead ? 1 : 0, SQLITE3_INTEGER);
    $stmt->bindValue(':expiry_datetime', $expiryDatetime, SQLITE3_TEXT);
    $stmt->bindValue(':updated_at', $createdAt, SQLITE3_TEXT);

    try {
        $stmt->execute();
        return $id;
    } catch (Exception $e) {
        error_log("Database Error: " . $e->getMessage());
        return false;
    }
}

// Retrieve a secret and check if it's expired or needs to be deleted
function getSecretById($id) {
    $db = connectDb();
    
    // Check if the secret exists
    $stmt = $db->prepare("SELECT * FROM secrets WHERE id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_TEXT);
    $result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

    if (!$result) {
        return null; // Secret not found
    }

    // Check if the secret has expired
    if ($result['expiry_datetime'] && strtotime($result['expiry_datetime']) < time()) {
        deleteSecretById($id);
        return null; // Secret is expired
    }

    // If burn_after_read is enabled, delete after retrieval
    if ($result['burn_after_read']) {
        deleteSecretById($id);
    }

    return $result;
}

// Delete a secret after it has been viewed
function deleteSecretById($id) {
    $db = connectDb();
    $stmt = $db->prepare("DELETE FROM secrets WHERE id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_TEXT);
    $stmt->execute();
}

// Delete expired secrets
function deleteExpiredSecrets() {
    $db = connectDb();
    $stmt = $db->prepare("DELETE FROM secrets WHERE expiry_datetime IS NOT NULL AND expiry_datetime < :current_time");
    $stmt->bindValue(':current_time', date('Y-m-d H:i:s'), SQLITE3_TEXT);
    $stmt->execute();
}
?>
