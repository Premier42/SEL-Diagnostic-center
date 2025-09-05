<?php
// Application Configuration

return [
    'app' => [
        'name' => 'Pathology Laboratory Management System',
        'version' => '1.0.0',
        'environment' => 'development', // development, production
        'debug' => true,
        'timezone' => 'Asia/Dhaka',
        'locale' => 'en_BD',
        'currency' => 'BDT',
        'currency_symbol' => '৳'
    ],
    
    'database' => [
        'default' => 'mysql',
        'connections' => [
            'mysql' => [
                'driver' => 'mysql',
                'host' => $_ENV['DB_HOST'] ?? 'localhost',
                'port' => $_ENV['DB_PORT'] ?? '3306',
                'database' => $_ENV['DB_NAME'] ?? 'pathology_lab',
                'username' => $_ENV['DB_USER'] ?? 'root',
                'password' => $_ENV['DB_PASS'] ?? '',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'options' => [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            ]
        ]
    ],
    
    'session' => [
        'lifetime' => 120, // minutes
        'expire_on_close' => false,
        'encrypt' => false,
        'files' => storage_path('sessions'),
        'connection' => null,
        'table' => 'sessions',
        'store' => null,
        'lottery' => [2, 100],
        'cookie' => 'lab_session',
        'path' => '/',
        'domain' => null,
        'secure' => false,
        'http_only' => true,
        'same_site' => 'lax'
    ],
    
    'security' => [
        'password_min_length' => 8,
        'password_require_uppercase' => true,
        'password_require_lowercase' => true,
        'password_require_numbers' => true,
        'password_require_symbols' => false,
        'max_login_attempts' => 5,
        'lockout_duration' => 15, // minutes
        'csrf_protection' => true,
        'session_regenerate' => true
    ],
    
    'logging' => [
        'default' => 'file',
        'channels' => [
            'file' => [
                'driver' => 'file',
                'path' => storage_path('logs/app.log'),
                'level' => 'debug'
            ],
            'database' => [
                'driver' => 'database',
                'table' => 'system_logs',
                'level' => 'info'
            ]
        ]
    ],
    
    'mail' => [
        'default' => 'smtp',
        'mailers' => [
            'smtp' => [
                'transport' => 'smtp',
                'host' => $_ENV['MAIL_HOST'] ?? 'localhost',
                'port' => $_ENV['MAIL_PORT'] ?? 587,
                'encryption' => $_ENV['MAIL_ENCRYPTION'] ?? 'tls',
                'username' => $_ENV['MAIL_USERNAME'] ?? null,
                'password' => $_ENV['MAIL_PASSWORD'] ?? null
            ]
        ],
        'from' => [
            'address' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@lab.com',
            'name' => $_ENV['MAIL_FROM_NAME'] ?? 'Lab System'
        ]
    ],
    
    'sms' => [
        'default' => 'textbelt',
        'providers' => [
            'textbelt' => [
                'api_url' => 'https://textbelt.com/text',
                'api_key' => 'textbelt',
                'daily_limit' => 1,
                'country_code' => '+880'
            ]
        ]
    ],
    
    'features' => [
        'audit_logging' => true,
        'sms_notifications' => true,
        'email_notifications' => false,
        'data_export' => true,
        'inventory_management' => true,
        'multi_language' => false,
        'api_access' => true
    ],
    
    'pagination' => [
        'default_per_page' => 25,
        'max_per_page' => 100
    ],
    
    'file_uploads' => [
        'max_size' => '10M',
        'allowed_types' => ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'],
        'storage_path' => storage_path('uploads')
    ]
];

function storage_path($path = '') {
    $base = dirname(__DIR__) . '/storage';
    return $path ? $base . '/' . ltrim($path, '/') : $base;
}
?>
