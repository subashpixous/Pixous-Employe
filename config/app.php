<?php
/**
 * Application Configuration
 */

// Base URL — change to match your setup
define('BASE_URL', 'https://pixous-employe.onrender.com');

// App info
define('APP_NAME', 'Pixous HR Portal');
define('APP_VERSION', '1.0.0');

// File uploads
define('UPLOAD_DIR', __DIR__ . '/../assets/uploads/');
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_DOC_TYPES', ['application/pdf', 'image/jpeg', 'image/png']);

// Session
define('SESSION_LIFETIME', 3600); // 1 hour

// Security
define('CSRF_TOKEN_NAME', 'csrf_token');

// Pagination
define('PER_PAGE', 15);
