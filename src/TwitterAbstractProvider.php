<?php

namespace SocialiteProviders\Twitter;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Laravel\Socialite\Contracts\Provider as ProviderContract;
use Laravel\Socialite\One\AbstractProvider;

abstract class TwitterAbstractProvider extends AbstractProvider implements ProviderContract
{
    /**
     * Redirect the user to the authentication page for the provider.
     *
     * @return RedirectResponse
     */
    public function redirect()
    {
        if (!$this->isStateless()) {
            $this->request->getSession()->set(
                'oauth.temp', $temp = $this->server->getTemporaryCredentials()
            );
        } else {
            $temp = $this->server->getTemporaryCredentials();
            setcookie('oauth_temp', serialize($temp));
        }

        return new RedirectResponse($this->server->getAuthorizationUrl($temp));
    }

    /**
     * Get the token credentials for the request.
     *
     * @return \League\OAuth1\Client\Credentials\TokenCredentials
     */
    protected function getToken()
    {
        if (!$this->isStateless()) {
            $temp = $this->request->getSession()->get('oauth.temp');

            return $this->server->getTokenCredentials(
                $temp, $this->request->get('oauth_token'), $this->request->get('oauth_verifier')
            );
        } else {
            $temp = unserialize($_COOKIE['oauth_temp']);

            return $this->server->getTokenCredentials(
                $temp, $this->request->get('oauth_token'), $this->request->get('oauth_verifier')
            );
        }
    }

    /**
     * Indicates if the session state should be utilized.
     *
     * @var bool
     */
    protected $stateless = false;

    /**
     * Determine if the provider is operating as stateless.
     *
     * @return bool
     */
    protected function isStateless()
    {
        return $this->stateless;
    }

    /**
     * Indicates that the provider should operate as stateless.
     *
     * @return $this
     */
    public function stateless()
    {
        $this->stateless = true;

        return $this;
    }
}
