<?php

namespace App\Controller;


use App\Service\GoogleAuthenticate;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller {

    /** @var GoogleAuthenticate */
    protected $auth;

    public function __construct( GoogleAuthenticate $authenticate ) {
        $this->auth = $authenticate;
    }

    /**
     * @Route("/", name="home")
     * @Template()
     */
    public function indexAction() {
        return [];
    }


    /**
     * @return Response
     *
     */
    public function authAndSyncButtonAction(): Response {

        if ( $this->auth->isAuthenticated() ) {
            return $this->render( 'default/syncButton.html.twig' );
        }

        return $this->render( 'default/authButton.html.twig', [
            'auth_url' => $this->auth->getClient()->createAuthUrl()
        ] );
    }

    /**
     * @Route("/auth", name="google_auth");
     * @param Request $request
     * @param SessionInterface $session
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function authAction( Request $request, SessionInterface $session ) {

        $client = $this->auth->getClient();

        if ( $request->get( 'code' ) ) {
            try {
                $token = $client->fetchAccessTokenWithAuthCode( $request->get( 'code' ) );
                $session->set( GoogleAuthenticate::SESSION_KEY, $token );
            } catch ( \Exception $e ) {
                $this->addFlash( 'error', $e->getMessage() );
            }
        }

        return $this->redirectToRoute( 'home' );
    }

    /**
     * @Route("/sync", name="sync");
     *
     */
    public function syncAction() {
        $client = $this->auth->getClient();


        if ( $this->auth->isAuthenticated() ) {

            $drive = new \Google_Service_Drive( $client );

            $folder_id = $drive->files->listFiles( [
                'q' => 'name contains \'MiBandMaster\''
            ] )->getFiles()[0]->getId();


            $db_file_id = $drive->files->listFiles( [
                'q'       => '\'' . $folder_id . '\' in parents and name contains \'db\'',
                'orderBy' => 'modifiedTime desc'
            ] )->getFiles()[0]->getId();


            $file = $drive->files->get( $db_file_id, [
                'alt' => 'media'
            ] );

            file_put_contents( $this->getParameter( 'kernel.project_dir' ) . '/var/db.sqlite', $file->getBody()
                                                                                                    ->getContents() );
        }

        return $this->redirectToRoute( 'home' );
    }
}
