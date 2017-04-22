<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\Partie;
use AppBundle\Entity\Carte;
use AppBundle\Entity\Chat;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 * @package AppBundle\Controller
 * @Route("ajax")
 */

class ChatController extends Controller
{

    /**
     * @Route("/get_chat/{id}", name="get_chat")
     */
    public function getChatAction($id)
    {
        $user = $this->getUser();

        $find = $this->getDoctrine()->getRepository('AppBundle:Chat')->findOneBy(array('partie' => $id),array('id' => 'desc'));

//        dump($find);
        $response = new Response();
        $response->setContent(json_encode(array(
//            'id' => $find->getId(),
            'message' => $find->getMessage(),
            'joueur' => $find->getjoueur()->getUsername(),
            'joueurId' => $find->getjoueur()->getId(),
        )));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/get_last_chat/{id}", name="get_last_id_chat")
     */
    public function getLastIdChatAction($id)
    {
        $user = $this->getUser();

        $find = $this->getDoctrine()->getRepository('AppBundle:Chat')->findOneBy(array('partie' => $id),array('id' => 'desc'));

        if ($find == null){
            $response = new Response();
            $response->setContent(json_encode(array(
                'last_id' => null,
//
            )));
            $response->headers->set('Content-Type', 'application/json');
        }else{
            $response = new Response();
            $response->setContent(json_encode(array(
                'last_id' => $find->getId(),
//            'message' => $find->getMessage(),
            )));
            $response->headers->set('Content-Type', 'application/json');
        }
//        dump($find);


        return $response;
    }

    /**
     *
     * @param Partie $partie
     *
     * @Route("/add_chat/{partie}", name="add_chat")
     */
    public function addChatAction(Request $request, Partie $partie)
    {
        $user = $this->getUser();

//        $find = $this->getDoctrine()->getRepository('AppBundle:User')->findBy(array(),array('userBestScore' => 'DESC'));

//        $message = $request->request->get('text');
        $message = $request->query->get('text');


        $chat = new Chat();

        $chat->setPartie($partie);

        $chat->setJoueur($user);

        $chat->setMessage($message);

        $em = $this->getDoctrine()->getManager();
        $em->persist($chat);
        $em->flush();

//        return $this->render("AppBundle:joueurs:classement.html.twig", ['user' => $user, 'classement'=> $find]);

        $response = new Response();
        $response->setContent(json_encode(array(
            'id' => $partie->getId(),
//            'message' => $find->getMessage(),
        )));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

}
