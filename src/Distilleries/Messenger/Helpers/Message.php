<?php

namespace Distilleries\Messenge\Helpers;

/**
 * Created by PhpStorm.
 * User: mfrancois
 * Date: 31/07/2016
 * Time: 19:50
 */

use Distilleries\Messenge\Exceptions\ConfigException;
use Distilleries\Messenger\Exceptions\MessengerException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class Message
{

    protected $config = [];

    /**
     * Message constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        if ($this->checkConfig($config)) {
            $this->config = $config;
        } else {
            throw new ConfigException(trans('messenger::errors.config_not_valid'));
        }

    }

    protected function checkConfig(array $config)
    {
        return (empty($config['uri_bot']) || empty($config['page_access_token'])) ? false : true;
    }


    public function sendTextMessage($recipientId, $messageText)
    {
        $messageData = [
            'recipient' => ['id' => $recipientId],
            'message'   => ['text' => $messageText]
        ];

        return $this->callSendAPI($messageData);
    }


    public function sendImageMessage($recipientId, $picture)
    {

        $messageData = [
            'recipient' => ['id' => $recipientId],
            'message'   => [
                'attachment' => [
                    'type'    => 'image',
                    'payload' =>
                        [
                            'url' => $picture
                        ]
                ]
            ]
        ];

        return $this->callSendAPI($messageData);
    }

    public function sendCard($recipientId, $card)
    {

        $messageData = [
            'recipient' => ['id' => $recipientId],
            'message'   => [
                'attachment' => [
                    'type'    => 'template',
                    'payload' => $card
                ]
            ]
        ];

        return $this->callSendAPI($messageData);
    }


    public function callSendAPI($messageData)
    {
        $client = new Client();
        try {
            $res = $client->request('POST', $this->config['uri_bot'], [
                'query' => ['access_token' => $this->config['page_access_token']],
                'json'  => $messageData
            ]);

            return $res->getBody();

        } catch (ClientException $e) {
            throw new MessengerException(trans('messenger::errors.unable_send_message'), 0, $e);
        }
    }


    public function persistMenu($menu)
    {

        $messageData = [
            "setting_type"    => "call_to_actions",
            "thread_state"    => "existing_thread",
            "call_to_actions" => $menu
        ];

        return $this->callSendAPI($messageData);
    }

    public function getCurrentUserProfile($uid, $fields = null)
    {
        if (empty($fields)) {
            return (new FBUser($this->config))->getProfile($uid);
        }

        return (new FBUser($this->config))->getProfile($uid, $fields);

    }
}