<?php

require __DIR__.'/vendor/autoload.php';

// Create a simple API test script to test the Category endpoints
echo "=== Testing Category API Endpoints ===\n";

// Base URL for the API
$baseUrl = 'https://todo.prus.dev/api';

// 1. Get the API token
$loginData = [
    'email' => 'admin@example.com',
    'password' => 'password',
];

echo "1. Logging in to get API token...\n";

$ch = curl_init("{$baseUrl}/login");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Skip SSL verification for testing
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    echo "Login failed with HTTP code: {$httpCode}\n";
    echo "Response: {$response}\n";
    exit(1);
}

$data = json_decode($response, true);
if (!isset($data['data']['token'])) {
    echo "Failed to get token from response\n";
    echo "Response: {$response}\n";
    exit(1);
}

$token = $data['data']['token'];
echo "Login successful. Token received.\n\n";

// 2. List all categories
echo "2. Listing all categories...\n";

$ch = curl_init("{$baseUrl}/categories");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer '.$token,
    'Accept: application/json',
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Skip SSL verification for testing
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    echo "List categories failed with HTTP code: {$httpCode}\n";
    echo "Response: {$response}\n";
    exit(1);
}

$categoriesData = json_decode($response, true);
if (!isset($categoriesData['data']) || !is_array($categoriesData['data'])) {
    echo "Failed to get categories data\n";
    echo "Response: {$response}\n";
    exit(1);
}

$categories = $categoriesData['data'];
$categoryCount = count($categories);
echo "Retrieved {$categoryCount} categories.\n";

if ($categoryCount > 0) {
    echo "First category: {$categories[0]['name']} (ID: {$categories[0]['id']})\n\n";
}

// 3. Create a new category
echo "3. Creating a new category...\n";

$newCategory = [
    'name' => 'Test Category '.date('Y-m-d H:i:s'),
    'color' => '#'.str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT),
    'icon' => 'test-icon',
];

$ch = curl_init("{$baseUrl}/categories");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($newCategory));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer '.$token,
    'Accept: application/json',
    'Content-Type: application/json',
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Skip SSL verification for testing
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 201) {
    echo "Create category failed with HTTP code: {$httpCode}\n";
    echo "Response: {$response}\n";
    exit(1);
}

$categoryData = json_decode($response, true);
if (!isset($categoryData['data']['id'])) {
    echo "Failed to get new category ID\n";
    echo "Response: {$response}\n";
    exit(1);
}

$categoryId = $categoryData['data']['id'];
echo "Category created successfully with ID: {$categoryId}\n\n";

// 4. Get the category details
echo "4. Getting category details...\n";

$ch = curl_init("{$baseUrl}/categories/{$categoryId}");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer '.$token,
    'Accept: application/json',
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Skip SSL verification for testing
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    echo "Get category details failed with HTTP code: {$httpCode}\n";
    echo "Response: {$response}\n";
    exit(1);
}

$categoryData = json_decode($response, true);
if (!isset($categoryData['data']['name'])) {
    echo "Failed to get category details\n";
    echo "Response: {$response}\n";
    exit(1);
}

echo "Category Name: {$categoryData['data']['name']}\n";
echo "Category Color: {$categoryData['data']['color']}\n";
echo "Category Icon: {$categoryData['data']['icon']}\n\n";

// 5. Update the category
echo "5. Updating the category...\n";

$updateData = [
    'name' => 'Updated: '.$categoryData['data']['name'],
    'color' => '#'.str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT),
];

$ch = curl_init("{$baseUrl}/categories/{$categoryId}");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($updateData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer '.$token,
    'Accept: application/json',
    'Content-Type: application/json',
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Skip SSL verification for testing
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    echo "Update category failed with HTTP code: {$httpCode}\n";
    echo "Response: {$response}\n";
    exit(1);
}

$updatedCategoryData = json_decode($response, true);
echo "Category updated successfully.\n";
echo "New Name: {$updatedCategoryData['data']['name']}\n";
echo "New Color: {$updatedCategoryData['data']['color']}\n\n";

// 6. Get task counts by category
echo "6. Getting task counts by category...\n";

$ch = curl_init("{$baseUrl}/categories/task-counts");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer '.$token,
    'Accept: application/json',
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Skip SSL verification for testing
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    echo "Get task counts failed with HTTP code: {$httpCode}\n";
    echo "Response: {$response}\n";
    exit(1);
}

$taskCountsData = json_decode($response, true);
if (!isset($taskCountsData['data']) || !is_array($taskCountsData['data'])) {
    echo "Failed to get task counts data\n";
    echo "Response: {$response}\n";
    exit(1);
}

$taskCounts = $taskCountsData['data'];
echo "Retrieved task counts for ".count($taskCounts)." categories.\n\n";

// 7. Delete the category
echo "7. Deleting the category...\n";

$ch = curl_init("{$baseUrl}/categories/{$categoryId}");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer '.$token,
    'Accept: application/json',
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Skip SSL verification for testing
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 204) {
    echo "Delete category failed with HTTP code: {$httpCode}\n";
    echo "Response: {$response}\n";
    exit(1);
}

echo "Category deleted successfully.\n\n";

// 8. Verify the category is gone
echo "8. Verifying the category is gone...\n";

$ch = curl_init("{$baseUrl}/categories/{$categoryId}");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer '.$token,
    'Accept: application/json',
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Skip SSL verification for testing
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 404) {
    echo "Verification successful: Category no longer exists.\n\n";
} else {
    echo "Verification failed with HTTP code: {$httpCode}\n";
    echo "Response: {$response}\n";
    exit(1);
}

echo "=== All Category API tests completed successfully! ===\n"; 