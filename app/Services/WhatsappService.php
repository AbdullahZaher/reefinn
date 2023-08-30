<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsappService {
    protected $access_token;

    public function __construct()
    {
        $this->base_url = config('whatsapp.base_url');
        $this->access_token = config('whatsapp.access_token');
    }

    public function getBaseUrl()
    {
        return $this->base_url;
    }

    public function getAccessToken()
    {
        return $this->access_token;
    }

    public function sendWelcomeMessage(string $number, array $data): bool
    {
        $url = $this->getBaseUrl() . '122097174200008954/messages';

        $response = Http::withHeaders([
                            'Authorization' =>  'Bearer ' . $this->getAccessToken(),
                            'Content-Type' => 'application/json',
                        ])->post($url, [
                            "messaging_product" => "whatsapp",
                            "to" => $number,
                            "type" => "template",
                            "template" => [
                                "name" => "welcome_message",
                                "language" => [
                                    "code" => "ar",
                                ],
                                "components" => [
                                    [
                                        "type" => "body",
                                        "parameters" => [
                                            [
                                                "type" => "text",
                                                "text" => $data['hotel_name'],
                                            ],
                                            [
                                                "type" => "text",
                                                "text" => $data['hotel_phone'],
                                            ],
                                            [
                                                "type" => "text",
                                                "text" => $data['checkin_date'],
                                            ],
                                            [
                                                "type" => "text",
                                                "text" => $data['apartment_name'],
                                            ],
                                            [
                                                "type" => "text",
                                                "text" => number_format($data['total_price'], 2) . ' ' . __('SAR'),
                                            ],
                                            [
                                                "type" => "text",
                                                "text" => $data['invoice_url'],
                                            ],
                                        ],
                                    ],
                                ],
                            ]
                        ]);

        if ($response->failed()) return false;

        return true;
    }
}