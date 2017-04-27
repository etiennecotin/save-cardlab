<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need

        $user = $this->getUser();

        if ($user != null){
            return $this->redirect( $this->generateUrl('joueur_partie') );
        }
        return $this->render('AppBundle:Default:index.html.twig');
    }

    /**
     * @Route("/home", name="home2page")
     */
    public function homeAction(){

        return $this->render('AppBundle:Default:index.html.twig');
    }

    /**
     * @Route("/test", name="testPage")
     */
    public function testAction(){

        //envoi invitation par mail
        $message = \Swift_Message::newInstance()
            ->setSubject('Invitation à joueur')
            ->setFrom('deamdecoy@gmail.com')
            ->setTo('etienne.cotin@gmail.com')
            ->setBody(
                $this->renderView(
                // app/Resources/views/Emails/registration.html.twig
                    'AppBundle::emails/invitation.html.twig',
                    array('name' => 'etienne')
                ),
                'text/html'
            )
            /*
             * If you also want to include a plaintext version of the message
            ->addPart(
                $this->renderView(
                    'Emails/registration.txt.twig',
                    array('name' => $name)
                ),
                'text/plain'
            )
            */
        ;
        $this->get('mailer')->send($message);

        return $this->render('AppBundle:Default:test.html.twig');
    }
}
