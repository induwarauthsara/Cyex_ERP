<?php
/**
 * Srijaya ERP API - Index/Welcome Page
 * 
 * Provides API information and health check
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// API Information
$response = [
    'success' => true,
    'message' => 'Welcome to Srijaya ERP API',
    'data' => [
        'name' => 'Srijaya ERP Mobile POS API',
        'version' => '1.0',
        'status' => 'operational',
        'server_time' => date('Y-m-d H:i:s'),
        'documentation' => '/api/API_DOCUMENTATION.md',
        'endpoints' => [
            'authentication' => [
                'login' => 'POST /api/v1/auth/login',
                'logout' => 'POST /api/v1/auth/logout',
                'verify' => 'GET /api/v1/auth/verify'
            ],
            'products' => [
                'search' => 'GET /api/v1/products/search',
                'list' => 'GET /api/v1/products/list',
                'details' => 'GET /api/v1/products/details'
            ],
            'customers' => [
                'search' => 'GET /api/v1/customers/search',
                'details' => 'GET /api/v1/customers/details',
                'add' => 'POST /api/v1/customers/add'
            ],
            'invoices' => [
                'submit' => 'POST /api/v1/invoices/submit',
                'list' => 'GET /api/v1/invoices/list'
            ],
            'attendance' => [
                'clock' => 'POST /api/v1/attendance/clock',
                'status' => 'GET /api/v1/attendance/status'
            ]
        ],
        'features' => [
            'JWT Authentication',
            'Secure API Endpoints',
            'Standardized Responses',
            'Error Handling',
            'Input Validation',
            'Pagination Support',
            'CORS Enabled'
        ],
        'support' => [
            'email' => 'support@srijayaprint.com',
            'phone' => '0714730996',
            'address' => 'FF26, Megacity, Athurugiriya'
        ]
    ],
    'meta' => [
        'timestamp' => date('Y-m-d H:i:s'),
        'php_version' => PHP_VERSION,
        'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'
    ]
];

echo json_encode($response, JSON_PRETTY_PRINT);
