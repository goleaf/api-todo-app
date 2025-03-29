<?php

namespace App\Services;

// Note: This service requires installation of the OneSignal PHP SDK
// composer require norkunas/onesignal-php-api
// This is a placeholder implementation that would be completed after installing the package

class OneSignalNotificationService
{
    protected $appId;
    protected $apiKey;

    public function __construct()
    {
        $this->appId = env('ONESIGNAL_APP_ID');
        $this->apiKey = env('ONESIGNAL_REST_API_KEY');
    }

    /**
     * Send a notification to a specific user
     *
     * @param int $userId User ID in your application
     * @param string $title Notification title
     * @param string $body Notification body
     * @param array $data Additional data to send with the notification
     * @return bool|array Success status or response data
     */
    public function sendToUser($userId, $title, $body, $data = [])
    {
        $playerIds = \App\Models\DeviceToken::where('user_id', $userId)
            ->pluck('player_id')
            ->toArray();

        if (empty($playerIds)) {
            return false;
        }

        return $this->sendToPlayerIds($playerIds, $title, $body, $data);
    }

    /**
     * Send a notification to specific player IDs
     *
     * @param array $playerIds Array of OneSignal player IDs
     * @param string $title Notification title
     * @param string $body Notification body
     * @param array $data Additional data to send with the notification
     * @return array Response from the OneSignal API
     */
    public function sendToPlayerIds($playerIds, $title, $body, $data = [])
    {
        // This is a placeholder implementation
        // In a real implementation, we would use the OneSignal PHP SDK
        
        $fields = [
            'app_id' => $this->appId,
            'include_player_ids' => $playerIds,
            'headings' => ['en' => $title],
            'contents' => ['en' => $body],
            'data' => $data
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://onesignal.com/api/v1/notifications');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            "Authorization: Basic {$this->apiKey}"
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if ($httpCode != 200) {
            \Log::error('OneSignal API Error: ' . $response);
            return ['error' => true, 'message' => 'Failed to send notification'];
        }
        
        return json_decode($response, true);
    }
} 