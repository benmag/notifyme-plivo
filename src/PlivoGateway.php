<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NotifyMeHQ\Plivo;

use GuzzleHttp\Client;
use NotifyMeHQ\Contracts\GatewayInterface;
use NotifyMeHQ\NotifyMe\HttpGatewayTrait;
use NotifyMeHQ\NotifyMe\Response;

class PlivoGateway implements GatewayInterface
{
    use HttpGatewayTrait;

    /**
     * The api endpoint.
     *
     * @var string
     */
    protected $endpoint = 'https://api.plivo.com';

    /**
     * The api version.
     *
     * @var string
     */
    protected $version = 'v1';

    /**
     * Create a new plivo gateway instance.
     *
     * @param \GuzzleHttp\Client $client
     * @param string[]           $config
     */
    public function __construct(Client $client, array $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * Send a notification.
     *
     * @param string $to
     * @param string $message
     *
     * @return \NotifyMeHQ\Contracts\ResponseInterface
     */
    public function notify($to, $message)
    {
        $params = [
            'src' => $this->config['from'],
            'dst'   => $to,
            'text' => $message,
        ];

        return $this->send($this->buildUrlFromString('Account/'.$this->config['auth_id'].'/Message/'), $params);
    }

    /**
     * Send the notification over the wire.
     *
     * @param string   $url
     * @param string[] $params
     *
     * @return \NotifyMeHQ\Contracts\ResponseInterface
     */
    protected function send($url, array $params)
    {
        $success = false;

        $rawResponse = $this->client->post($url, [
            'exceptions'      => false,
            'timeout'         => '80',
            'connect_timeout' => '30',
            'verify'          => true,
            'auth'            => [
                $this->config['auth_id'],
                $this->config['auth_token'],
            ],
            'headers' => [
                'Accept'         => 'application/json',
                'Accept-Charset' => 'utf-8',
                'Content-Type'   => 'application/json',
            ],
            'json' => $params,
        ]);

        if (substr((string) $rawResponse->getStatusCode(), 0, 1) === '2') {
            $response = $rawResponse->json();
            $success = true;
        } else {
            $response = $this->responseError($rawResponse);
        }

        return $this->mapResponse($success, $response);
    }

    /**
     * Map the raw response to our response object.
     *
     * @param bool  $success
     * @param array $response
     *
     * @return \NotifyMeHQ\Contracts\ResponseInterface
     */
    protected function mapResponse($success, $response)
    {
        return (new Response())->setRaw($response)->map([
            'success' => $success,
            'message' => $success ? 'Message sent' : $response['message'],
        ]);
    }

    /**
     * Get the default json response.
     *
     * @param \GuzzleHttp\Message\ResponseInterface $rawResponse
     *
     * @return array
     */
    protected function jsonError($rawResponse)
    {
        $msg = 'API Response not valid.';
        $msg .= " (Raw response API {$rawResponse->getBody()})";

        return [
            'message' => $msg,
        ];
    }

    /**
     * Get the request url.
     *
     * @return string
     */
    protected function getRequestUrl()
    {
        return $this->endpoint.'/'.$this->version;
    }
}
