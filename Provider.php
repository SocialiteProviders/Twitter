<?php

namespace SocialiteProviders\Twitter;

use SocialiteProviders\Manager\OAuth1\AbstractProvider;
use SocialiteProviders\Manager\OAuth1\User;

class Provider extends AbstractProvider
{
    /**
     * Unique Provider Identifier.
     */
    const IDENTIFIER = 'TWITTER';

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user['extra'])->map([
             'id'       => $user['id'],
             'nickname' => $user['nickname'],
             'name'     => $user['name'],
             'email'    => $user['email'],
             'avatar'   => $user['avatar'],
        ]);
    }
    
    /**
     * @return $this
     */
    public function applyConfig()
    {
        $tokenCredentials = $this->server->getClientCredentials();
        $config = $this->config;

        $tokenCredentials->setIdentifier($config->clientId);
        $tokenCredentials->setSecret($config->clientSecret);

        return $this;
    }
}
