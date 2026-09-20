<?php

namespace App\Services;

use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Model\SendSmtpEmail;
use GuzzleHttp\Client;

class BrevoMailService
{
    protected TransactionalEmailsApi $api;

    public function __construct()
    {
        $config = Configuration::getDefaultConfiguration()
            ->setApiKey('api-key', config('services.brevo.key'));

        $this->api = new TransactionalEmailsApi(
            new Client([
                'connect_timeout' => 10,    // DNS resolve + TCP connect timeout
                'timeout'         => 30,    // total response timeout
            ]),
            $config
        );
    }

    public function send(string $to, string $subject, string $html)
    {
        try {
            $email = new SendSmtpEmail([
                'subject' => $subject,
                'htmlContent' => $html,
                'sender' => [
                    'email' => config('mail.from.address'),
                    'name'  => config('mail.from.name'),
                ],
                'to' => [
                    ['email' => $to],
                ],
            ]);

            $response = $this->api->sendTransacEmail($email);

            //  Brevo success check
            if (!$response || empty($response->getMessageId())) {
                throw new \Exception('Brevo mail sending failed (no messageId)');
            }

            return $response;

        } catch (\Throwable $e) {
            //  force throw so controller catch works
            throw new \Exception('Brevo Error: ' . $e->getMessage(), 500);
        }
    }
}
