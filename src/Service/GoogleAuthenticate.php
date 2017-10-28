<?php

namespace App\Service;


use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class GoogleAuthenticate {

    const SESSION_KEY = 'auth_token';
    /** @var  \Google_Client */
    protected $client;

    public function __construct( RouterInterface $router, SessionInterface $session ) {
        $client = new \Google_Client();
        $client->setClientId( getenv( 'GOOGLE_OAUTH_CLIENT_ID' ) );
        $client->setClientSecret( getenv( 'GOOGLE_OAUTH_CLIENT_SECRET' ) );
        $client->addScope( \Google_Service_Drive::DRIVE );
        $client->setRedirectUri( $router->generate( 'google_auth', [], UrlGeneratorInterface::ABSOLUTE_URL ) );

        if ( $session->has( self::SESSION_KEY ) ) {
            $client->setAccessToken( $session->get( self::SESSION_KEY ) );
        }

        $this->client = $client;
    }

    public function isAuthenticated(): bool {
        return ! $this->client->isAccessTokenExpired();
    }

    public function getClient(): \Google_Client {
        return $this->client;
    }

}