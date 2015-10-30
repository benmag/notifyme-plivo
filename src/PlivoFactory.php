<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NotifyMeHQ\Plivo;

use GuzzleHttp\Client;
use NotifyMeHQ\Contracts\FactoryInterface;
use NotifyMeHQ\NotifyMe\Arr;

class PlivoFactory implements FactoryInterface
{
    /**
     * Create a new plivo gateway instance.
     *
     * @param string[] $config
     *
     * @return \NotifyMeHQ\Plivo\PlivoGateway
     */
    public function make(array $config)
    {
        Arr::requires($config, ['from', 'auth_id', 'auth_token']);

        $client = new Client();

        return new PlivoGateway($client, $config);
    }
}
