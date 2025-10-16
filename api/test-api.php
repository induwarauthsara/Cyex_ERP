<?php
/**
 * API Test Script
 * 
 * Simple script to test API endpoints
 * For development use only - remove in production
 */

// Configuration
$API_BASE = 'http://localhost/Srijaya/api/v1';
$TEST_USER = 'admin'; // Change to your test username
$TEST_PASS = 'admin'; // Change to your test password

// Test results
$results = [];
$token = null;

/**
 * Make API request
 */
function apiRequest($url, $method = 'GET', $data = null, $token = null) {
    $ch = curl_init($url);
    
    $headers = ['Content-Type: application/json'];
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    return [
        'http_code' => $httpCode,
        'response' => json_decode($response, true),
        'error' => $error
    ];
}

/**
 * Test authentication
 */
function testAuth($baseUrl, $username, $password) {
    global $token, $results;
    
    echo "🔐 Testing Authentication...\n\n";
    
    // Test login
    echo "1. Login Test\n";
    $result = apiRequest(
        $baseUrl . '/auth/login.php',
        'POST',
        ['username' => $username, 'password' => $password]
    );
    
    if ($result['http_code'] === 200 && $result['response']['success']) {
        $token = $result['response']['data']['token'];
        echo "   ✅ Login successful\n";
        echo "   Token: " . substr($token, 0, 30) . "...\n";
        $results['auth_login'] = 'PASS';
    } else {
        echo "   ❌ Login failed\n";
        echo "   Error: " . ($result['response']['message'] ?? 'Unknown error') . "\n";
        $results['auth_login'] = 'FAIL';
        return false;
    }
    
    // Test verify token
    echo "\n2. Verify Token Test\n";
    $result = apiRequest($baseUrl . '/auth/verify.php', 'GET', null, $token);
    
    if ($result['http_code'] === 200 && $result['response']['success']) {
        echo "   ✅ Token verification successful\n";
        $results['auth_verify'] = 'PASS';
    } else {
        echo "   ❌ Token verification failed\n";
        $results['auth_verify'] = 'FAIL';
    }
    
    echo "\n" . str_repeat("-", 60) . "\n\n";
    return true;
}

/**
 * Test product endpoints
 */
function testProducts($baseUrl, $token) {
    global $results;
    
    echo "📦 Testing Product Endpoints...\n\n";
    
    // Test search
    echo "1. Product Search Test\n";
    $result = apiRequest($baseUrl . '/products/search.php?q=a&limit=5', 'GET', null, $token);
    
    if ($result['http_code'] === 200 && $result['response']['success']) {
        $count = count($result['response']['data']);
        echo "   ✅ Search successful - Found $count products\n";
        $results['products_search'] = 'PASS';
    } else {
        echo "   ❌ Search failed\n";
        $results['products_search'] = 'FAIL';
    }
    
    // Test list
    echo "\n2. Product List Test\n";
    $result = apiRequest($baseUrl . '/products/list.php?page=1&per_page=10', 'GET', null, $token);
    
    if ($result['http_code'] === 200 && $result['response']['success']) {
        $total = $result['response']['meta']['pagination']['total'] ?? 0;
        echo "   ✅ List successful - Total: $total products\n";
        $results['products_list'] = 'PASS';
        
        // Get first product ID for details test
        if (!empty($result['response']['data'])) {
            $productId = $result['response']['data'][0]['id'];
            
            // Test details
            echo "\n3. Product Details Test\n";
            $result = apiRequest($baseUrl . '/products/details.php?id=' . $productId, 'GET', null, $token);
            
            if ($result['http_code'] === 200 && $result['response']['success']) {
                echo "   ✅ Details successful - Product ID: $productId\n";
                $results['products_details'] = 'PASS';
            } else {
                echo "   ❌ Details failed\n";
                $results['products_details'] = 'FAIL';
            }
        }
    } else {
        echo "   ❌ List failed\n";
        $results['products_list'] = 'FAIL';
    }
    
    echo "\n" . str_repeat("-", 60) . "\n\n";
}

/**
 * Test customer endpoints
 */
function testCustomers($baseUrl, $token) {
    global $results;
    
    echo "👥 Testing Customer Endpoints...\n\n";
    
    // Test search
    echo "1. Customer Search Test\n";
    $result = apiRequest($baseUrl . '/customers/search.php?q=077', 'GET', null, $token);
    
    if ($result['http_code'] === 200 && $result['response']['success']) {
        $count = count($result['response']['data']);
        echo "   ✅ Search successful - Found $count customers\n";
        $results['customers_search'] = 'PASS';
        
        // Get first customer for details test
        if (!empty($result['response']['data'])) {
            $customerId = $result['response']['data'][0]['id'];
            
            echo "\n2. Customer Details Test\n";
            $result = apiRequest($baseUrl . '/customers/details.php?id=' . $customerId, 'GET', null, $token);
            
            if ($result['http_code'] === 200 && $result['response']['success']) {
                echo "   ✅ Details successful - Customer ID: $customerId\n";
                $results['customers_details'] = 'PASS';
            } else {
                echo "   ❌ Details failed\n";
                $results['customers_details'] = 'FAIL';
            }
        }
    } else {
        echo "   ❌ Search failed\n";
        $results['customers_search'] = 'FAIL';
    }
    
    echo "\n" . str_repeat("-", 60) . "\n\n";
}

/**
 * Test invoice endpoints
 */
function testInvoices($baseUrl, $token) {
    global $results;
    
    echo "🧾 Testing Invoice Endpoints...\n\n";
    
    // Test list
    echo "1. Invoice List Test\n";
    $result = apiRequest($baseUrl . '/invoices/list.php?page=1&per_page=10', 'GET', null, $token);
    
    if ($result['http_code'] === 200 && $result['response']['success']) {
        $total = $result['response']['meta']['pagination']['total'] ?? 0;
        echo "   ✅ List successful - Total: $total invoices\n";
        $results['invoices_list'] = 'PASS';
    } else {
        echo "   ❌ List failed\n";
        $results['invoices_list'] = 'FAIL';
    }
    
    echo "\n" . str_repeat("-", 60) . "\n\n";
}

/**
 * Test attendance endpoints
 */
function testAttendance($baseUrl, $token) {
    global $results;
    
    echo "⏰ Testing Attendance Endpoints...\n\n";
    
    // Test status
    echo "1. Attendance Status Test\n";
    $result = apiRequest($baseUrl . '/attendance/status.php', 'GET', null, $token);
    
    if ($result['http_code'] === 200 && $result['response']['success']) {
        $status = $result['response']['data']['is_clocked_in'] ? 'Clocked In' : 'Clocked Out';
        echo "   ✅ Status check successful - Current: $status\n";
        $results['attendance_status'] = 'PASS';
    } else {
        echo "   ❌ Status check failed\n";
        $results['attendance_status'] = 'FAIL';
    }
    
    echo "\n   ⚠️  Clock In/Out test skipped (would affect actual attendance records)\n";
    
    echo "\n" . str_repeat("-", 60) . "\n\n";
}

/**
 * Print summary
 */
function printSummary($results) {
    echo "📊 TEST SUMMARY\n\n";
    
    $passed = 0;
    $failed = 0;
    
    foreach ($results as $test => $status) {
        $icon = $status === 'PASS' ? '✅' : '❌';
        echo "   $icon " . str_pad($test, 25) . " - $status\n";
        
        if ($status === 'PASS') {
            $passed++;
        } else {
            $failed++;
        }
    }
    
    $total = $passed + $failed;
    $percentage = $total > 0 ? round(($passed / $total) * 100, 1) : 0;
    
    echo "\n" . str_repeat("-", 60) . "\n";
    echo "Total Tests: $total | Passed: $passed | Failed: $failed | Success Rate: $percentage%\n";
    echo str_repeat("=", 60) . "\n";
}

// Run tests
echo "\n";
echo str_repeat("=", 60) . "\n";
echo "           SRIJAYA ERP API - TEST SUITE\n";
echo str_repeat("=", 60) . "\n\n";

echo "Base URL: $API_BASE\n";
echo "Test User: $TEST_USER\n";
echo "Started: " . date('Y-m-d H:i:s') . "\n\n";
echo str_repeat("=", 60) . "\n\n";

// Run all tests
if (testAuth($API_BASE, $TEST_USER, $TEST_PASS)) {
    testProducts($API_BASE, $token);
    testCustomers($API_BASE, $token);
    testInvoices($API_BASE, $token);
    testAttendance($API_BASE, $token);
    
    // Print summary
    printSummary($results);
} else {
    echo "❌ Authentication failed. Cannot proceed with other tests.\n";
    echo "Please check your username and password.\n\n";
}

echo "\nCompleted: " . date('Y-m-d H:i:s') . "\n";
echo str_repeat("=", 60) . "\n\n";
