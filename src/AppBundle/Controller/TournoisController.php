<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\Partie;
use AppBundle\Entity\Carte;
use AppBundle\Entity\Tournois;
use AppBundle\Form\TournoisType;
use AppBundle\Controller\Joueur;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
* Class DefaultController
 * @package AppBundle\Controller
* @Route("joueur/tournois")
*/

class TournoisController extends Controller
{

    /**
     * @Route("/", name="tournois_homepage")
     */
    public function indexAction()
    {
        $user = $this->getUser();

        return $this->render('AppBundle:tournois:index.html.twig', ['user'=> $user]);
    }


    /**
     * @Route("/liste", name="afficher_liste_tournois")
     */
    public function afficherTournoisAction()
    {
        $user = $this->getUser();

        $tournois = $this->getDoctrine()->getRepository('AppBundle:Tournois')->findAll();

        return $this->render('AppBundle:tournois:afficherListeTournois.html.twig', ['user'=> $user, 'tournois' => $tournois ]);
    }

    /**
     * @Route("/mes-tournois", name="afficher_mes_tournois")
     */
    public function mesTournoisAction()
    {
        $user = $this->getUser();

        return $this->render('AppBundle:tournois:mesTournois.html.twig', ['user'=> $user]);
    }

    /**
     * @Route("/historique", name="historique_mes_tournois")
     */
    public function historiqueAction()
    {
        $user = $this->getUser();

//        $em = $this->getDoctrine()->getManager();


//        $tournoi = $em->getRepository('AppBundle:User')->findOneBy(array(),array('id' => 'DESC'));

        return $this->render('AppBundle:tournois:historique.html.twig', ['user'=> $user]);
    }

    /**
     * @param Tournois $id
     *
     * @Route("/historique/{id}", name="historique_tournoi")
     */
    public function historiqueTournoisAction(Tournois $id)
    {
        $user = $this->getUser();

        $tournoi = $id;
//        $em = $this->getDoctrine()->getManager();
//        $tournoi = $em->getRepository('AppBundle:Tournois')->findOneBy(array(),array('id' => 'DESC'));

        $partie = $this->getDoctrine()->getRepository('AppBundle:Partie')->findOneBy(array('tournois' => $id),array('id' => 'DESC'));;

        $scoreJ1 = $partie->getPartieScoreJoueur1();
        $scoreJ2 = $partie->getPartieScoreJoueur2();

        if ($scoreJ1>$scoreJ2){
            $gagnant = $partie->getJoueur1();
        }else{
            $gagnant = $partie->getJoueur2();
        }


        return $this->render('AppBundle:tournois:historiqueTournoi.html.twig', ['user'=> $user, 'tournoi'=>$tournoi, 'gagnant'=>$gagnant]);
    }

    /**
     * @Route("/add", name="creer_tournois")
     */
    public function creerTournoisAction(Request $request)
    {
        $user = $this->getUser();

        $session = new Session();


        $tournois = new Tournois;

        $form = $this->get('form.factory')->create(tournoisType::class, $tournois);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $task = $form->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
             $em = $this->getDoctrine()->getManager();

//             $tournois->addUser($user);

             $em->persist($tournois);
             $em->flush();

            $tournoi = $em->getRepository('AppBundle:Tournois')->findOneBy(array(),array('id' => 'DESC'));

            $user->addTournois($tournoi);

            $em->flush();

            $url = $this->generateUrl(
                'afficher_tournois',
                array('id' => $tournoi->getId())
            );


            $session->getFlashBag()->add('success', '<a href="'.$url.'"> Tournoi créé</a>');

            return $this->redirectToRoute('creer_tournois');
        }


        return $this->render('AppBundle:tournois:addTournois.html.twig', ['user'=> $user, 'form' => $form->createView()]);
    }


    /**
     * @param Tournois/= $id
     *
     * @Route("/{id}", name="afficher_tournois")
     */
    public function tournoisAction(Request $request, Tournois $id)
    {
        $user = $this->getUser();

        $session = new Session();

//        $Tournois = new Tournois();

        $em = $this->getDoctrine()->getManager();

        $tournois = $em->getRepository('AppBundle:Tournois')->find($id);


        $participants = $tournois->getUsers();
        $nbParticipants = count($participants);
        $participe = false;


        foreach ($tournois->getUsers() as $participant)
        {
            if ($participant->getId() == $user->getId())
            {
                $participe = true;
            }
        }

        $parties=null;
        $bloc =null;
        $match = array();
        if ($tournois->getTournoisNombreJoueurs() == $nbParticipants){
            $isComplet = true;

//            creation des parties et de la pyramide
//            match 1

            $parties = $this->getDoctrine()->getRepository('AppBundle:Partie')->findBy(array('tournois' => $id));


            $nbPartie = count($parties);
            $totalParties = $nbParticipants-1;
            $totalmatchdebut = $nbParticipants/2;

//            $nbPartiesRestante = $totalParties - $nbPartie;


            $nbPartiesRestante = $totalParties - $totalmatchdebut - 1;


            if ($nbPartie < $totalParties) {
                $c = 0;
                $j1 = null;
                $j2 = null;
                for ($i = 0; $i < $totalParties * 2; $i += 2) {
                    if ($totalmatchdebut > $c) {

                        $this->createPartie($id, $participants[$i], $participants[$i + 1]);
                    } else {
                        $this->createPartie($id, $j1, $j2);
                    }
                    $c++;
                }
            }

            $bloc = array();

            $bloc['bloc1'][] = array();
            $bloc['bloc2'][] = array();
            $bloc['final'][] = array();

            $lastIdMatch = end($parties);
            $lastIdMatch = $lastIdMatch->getId();
//            dump($lastIdMatch);
            for ($i=0;$i < $nbPartie;$i++)
            {
                if ($i < $totalmatchdebut)
                {
                    $bloc['bloc1'][] = $parties[$i];

                    if ($parties[$i]->getPartieDateFin() != null)
                    {
                        array_push($bloc['bloc1'][0], $parties[$i]) ;
                    }

                }elseif($lastIdMatch == $parties[$i]->getId()){

                    $bloc['final'][] = $parties[$i];
                }else{

                    if ($i < $totalmatchdebut+($totalmatchdebut/2))
                    {
//                        $bloc['bloc2'][] = array();
                        $bloc['bloc2'][] = $parties[$i];

                        if ($parties[$i]->getPartieDateFin() != null)
                        {
                            array_push($bloc['bloc2'][0], $parties[$i]) ;
                        }

                    }elseif ($i > $totalmatchdebut+($totalmatchdebut/2)){
                        $bloc['bloc3'][] = $parties[$i];
                    }

                }
            }

//            dump($totalParties, $totalmatchdebut, $nbPartiesRestante, $parties, $bloc);



            if ($bloc['final'][1]->getPartieDateFin()!=null){
                $partieFinie = true;

                $Fj1 = $bloc['final'][1]->getPartieScoreJoueur1();
                $Fj2 = $bloc['final'][1]->getPartieScoreJoueur1();

                if ($Fj1>$Fj2){
                    $victoire = $bloc['final'][1]->getJoueur1();
                }else{
                    $victoire = $bloc['final'][1]->getJoueur2();
                }


            }else{
                $partieFinie = false;
                $victoire = null;
            }
            $tabPartieAJouer = array();

            foreach ($bloc as $match) {
//                dump($match);


                foreach ($match as $key => $item) {

                    if ($key != 0) {

//                        $fin = $item->getPartieDateFin();

                        if ($item->getPartieDateFin() != null) {
                            $scoreJ1 = $item->getPartieScoreJoueur1();
                            $scoreJ2 = $item->getPartieScoreJoueur2();

                            if ($scoreJ1 > $scoreJ2) {
                                $gagnant = $item->getJoueur1();
                            } else {
                                $gagnant = $item->getJoueur2();
                            }

//                            dump($gagnant);

                            if ($totalmatchdebut == 2) {
                                $lastPartie = $bloc['final'][1];

//                                foreach ($final as $partie) {
//                                    $lastPartie = $partie;
//                                }


                                if ($lastPartie->getJoueur1() === null) {

                                    $lastPartie->setJoueur1($gagnant);

                                } elseif ($lastPartie->getJoueur2() === null) {

                                    $lastPartie->setJoueur2($gagnant);
                                }

                                $em->flush();

//                                dump($lastPartie);

//                            }
                            } elseif ($totalmatchdebut / 2 == 2) {
//
                                $bloc2 = $bloc['bloc2'];
                                $partieFinal = $bloc['final'][1];

                                $premierePartie = $bloc2[1];
                                $dernierePartie = end($bloc2);


                                if ($premierePartie->getPartieDateFin() === null && $premierePartie->getJoueur1() === null || $premierePartie->getJoueur2() === null )
                                {
//                                    dump('partie 1');
                                    $tabPart = array();
                                    for ($i=1; $i < 3;$i++)
                                    {
                                        $getpartie = $bloc['bloc1'][$i];

                                        array_push($tabPart, $getpartie);
                                    }

                                    foreach ($tabPart as $partie)
                                    {
                                        $sJ1 = $partie->getPartieScoreJoueur1();
                                        $sJ2 = $partie->getPartieScoreJoueur2();

                                        if ($sJ1>$sJ2)
                                        {
                                            if ($dernierePartie->getJoueur1() === null){
                                                $dernierePartie->setJoueur1($partie->getJoueur1());
                                                $em->flush();
                                            }else{
                                                $dernierePartie->setJoueur2($partie->getJoueur1());
                                                $em->flush();
                                            }
//                                            dump('set j1');
                                        }else{
                                            if ($dernierePartie->getJoueur1() === null){
                                                $dernierePartie->setJoueur1($partie->getJoueur2());
//                                                dump('set j2 en j1');
                                                $em->flush();
                                            }else{
                                                $dernierePartie->setJoueur2($partie->getJoueur2());
//                                                dump('set j2 en j2');
                                                $em->flush();
                                            }


                                        }
                                    }
                                }

                                if ($dernierePartie->getPartieDateFin() === null && $dernierePartie->getJoueur1() === null || $dernierePartie->getJoueur2() === null )
                                {
//                                    dump('partie 2');
                                    $tabPart = array();
                                    for ($i=3; $i < 5;$i++)
                                    {
                                        $getpartie = $bloc['bloc1'][$i];

                                        array_push($tabPart, $getpartie);
                                    }

                                    foreach ($tabPart as $partie)
                                    {
                                        $sJ1 = $partie->getPartieScoreJoueur1();
                                        $sJ2 = $partie->getPartieScoreJoueur2();

                                        if ($sJ1>$sJ2)
                                        {
                                            if ($dernierePartie->getJoueur1() === null){
                                                $dernierePartie->setJoueur1($partie->getJoueur1());
                                                $em->flush();
                                            }else{
                                                $dernierePartie->setJoueur2($partie->getJoueur1());
                                                $em->flush();
                                            }
//                                            dump('set j1');
                                        }else{
                                            if ($dernierePartie->getJoueur1() === null){
                                                $dernierePartie->setJoueur1($partie->getJoueur2());
//                                                dump('set j2 en j1');
                                                $em->flush();
                                            }else{
                                                $dernierePartie->setJoueur2($partie->getJoueur2());
//                                                dump('set j2 en j2');
                                                $em->flush();
                                            }


                                        }
                                    }
                                }

                                if($partieFinal->getPartieDateFin()===null){

                                    if ($premierePartie->getPartieDateFin() != null && $dernierePartie->getPartieDateFin() != null)
                                    {
                                        $p1sJ1 = $premierePartie->getPartieScoreJoueur1();
                                        $p1sJ2 = $premierePartie->getPartieScoreJoueur2();

                                        if ($p1sJ1 > $p1sJ2){
                                            $partieFinal->setJoueur1($premierePartie->getJoueur1());
                                            $em->flush();
                                        }else{
                                            $partieFinal->setJoueur1($premierePartie->getJoueur2());
                                            $em->flush();
                                        }
                                        $p2sJ1 = $dernierePartie->getPartieScoreJoueur1();
                                        $p2sJ2 = $dernierePartie->getPartieScoreJoueur2();

                                        if ($p2sJ1 > $p2sJ2){
                                            $partieFinal->setJoueur2($dernierePartie->getJoueur1());
                                            $em->flush();
                                        }else{
                                            $partieFinal->setJoueur2($dernierePartie->getJoueur2());
                                            $em->flush();
                                        }
//                                        die();
                                    }
                                }else{
                                    $tournois->setTournoisDateFin(new \DateTime("now"));
                                    $em->flush();
                                }

//                                foreach ($bloc2 as $key => $partie) {
//
//                                    $lastPartie='';
//                                    $tab2J1 =null;
//                                    $tab2J2=null;
////                                    dump($key);
//                                    if ($key != 0) {
////                                        dump($partie);
////                                        die();
//
//                                        if ($partie->getPartieDateFin() === null && $partie->getJoueur1() != null || $partie->getJoueur2() != null ) {
//                                            $tab2J1 = $partie->getJoueur1();
//                                            $tab2J2 = $partie->getJoueur2();
//
//                                            dump($partie);
////                                            die('prout');
//                                        }elseif ($partie->getPartieDateFin() === null && $partie->getJoueur1() === null || $partie->getJoueur2() === null) {
//
//                                            if ($partie->getJoueur1() != $gagnant || $partie->getJoueur2() != $gagnant) {
//                                                $lastPartie = $partie;
//
//
////                                                dump('prout '. $lastPartie->getId());
//
//                                                array_push($tabPartieAJouer, $partie);
//
////                                                dump($gagnant);
//                                            }
//
////                                            break;
//                                        }
////                                    }dump($partie);
//
//                                    }
//
//
////die();
//                                    if ($lastPartie) {
//
//                                        if ($lastPartie->getJoueur1() === null) {
//
//                                            if ($tab2J1 != $gagnant || $tab2J1 === null){
////                                                $lastPartie->setJoueur1($gagnant);
//
//                                                dump($gagnant->getId().' j1 ->'.$key);
////                                                $em->flush();
//                                            }
//
//
//                                        } elseif ($lastPartie->getJoueur2() === null) {
//
//                                            if ($tab2J2 != $gagnant || $tab2J1 === null){
////                                                $lastPartie->setJoueur2($gagnant);
//                                                dump($gagnant.' j2');
////                                                $em->flush();
//                                            }
//                                        } else {
//
//                                        }
//                                    }
//
//                                }
//                                break;
//                            dump($bloc['bloc2']);
                            }

                        }
                    }
                }
            }
//            dump($tabPartieAJouer);
//            die();

//            foreach ($parties as $partie) {
//                if ($partie->getPartieDateFin() != null)
//                {
//
////                    $index = current($parties);
//
//                    $idPartie = $partie->getId();
//
//                    $scoreJ1 = $partie->getPartieScoreJoueur1();
//                    $scoreJ2 = $partie->getPartieScoreJoueur2();
//
//                    if ($scoreJ1 > $scoreJ2 )
//                    {
//                        $gagnant = $partie->getJoueur1()->getId();
//                    }else{
//                        $gagnant = $partie->getJoueur2()->getId();
//                    }
//
////                    for ($i=0; ....;$i++)
////                    {
////
////                    }
//
//
//
//                    $toto = $totalmatchdebut/2;
//
//                    dump($gagnant);
//                    $i = 0;
//                    while ($value = current($parties)) {
////                        if ($value == $cartePoserId) {
////                            array_splice($mainJoueur1, $i, 1);
////                        }
////                        dump($i);
//                            if ($i > $totalmatchdebut-1)
//                            {
//                                dump($value->getId());
//
//                                $find = $this->getDoctrine()->getRepository('AppBundle:Partie')->find($value->getId());
//
//                                dump($find->getJoueur1());
//                            }
//
////                            dump($value->getId());
//                        next($parties);
//
//                        $i++;
//                    }
////                    die();
//                }
//            }
//            for ($i = 0; $i <= $nbParticipants/2;$i+=2)
//            {
//
//                $tab = array();
//                $tab['j1'] = $participants[$i];
//                $tab['j2'] = $participants[$i+1];
//
////                dump($participants);
////                $statPartie = $this->getDoctrine()->getRepository('AppBundle:Partie')->findOneBy(array('tournois' => $id));
//
//
//
//                $param =array('joueur1' => $participants[$i], 'joueur2' => $participants[$i+1], 'tournois' => 1 );
//
//                $find = $this->getDoctrine()->getRepository('AppBundle:Partie')->findOneBy($param);
//
//                $tab['partie'] = $find;
////                    dump($find);
//                if ($find->getPartieDateFin() != null)
//                {
//                    $scoreJ1 = $find->getPartieScoreJoueur1();
//                    $scoreJ2 = $find->getPartieScoreJoueur2();
//
//                    if ($scoreJ1 > $scoreJ2 )
//                    {
//                        $gagnant = 1;
//                    }else{
//                        $gagnant = 2;
//                    }
//                }
//
//                $query = $this->getDoctrine()->getRepository('AppBundle:Partie')->createQueryBuilder('p')
//                    ->where('p.joueur1 = :joueur1 and p.joueur2 = :joueur2 and p.tournois = :tournois')
////                    ->where('')
////                    ->where('p.tournois = :tournois')
//                    ->setParameters($param)
//                    ->getQuery();
//                $products = $query->getResult();
////                foreach ($find as $part){
////                    $test = $part->getId();
////                }
////               dump($products);
//                if ($find === null)
//                {
////                    dump('OK');
////                    die();
//
//                    $this->createPartie($id, $participants[$i], $participants[$i+1]);
//                }
//                $match[] = $tab;
//            }
//            die();


        }else{
            $isComplet = false;
        }


        if ($request->isMethod('POST'))
        {


            $participer = $request->request->get('participer');
            $quitter = $request->request->get('quitter');

            if ($participer)
            {

                $user->addTournois($tournois);

//                $em->persist($tournois);
                $em->flush();


                $session->getFlashBag()->add('success', 'Vous participer au tournois');

                return $this->redirectToRoute('afficher_tournois', array('id' => $id->getId()));
            }

            if ($quitter)
            {
                $user->removeTournois($tournois);

//                $em->persist($tournois);
                $em->flush();


                $session->getFlashBag()->add('success', 'Vous avez quitté le tournois');

                return $this->redirectToRoute('afficher_tournois', array('id' => $id->getId()));
            }

        }



        return $this->render('AppBundle:tournois:afficherTournois.html.twig', ['user'=> $user, 'tournois' => $tournois, 'nbParticipants' => $nbParticipants, 'isComplet' => $isComplet, 'participe' => $participe, 'match' => $match, 'part'=>$participants, 'parties'=>$parties, 'bloc'=>$bloc, 'partieFinie'=> $partieFinie, 'victoire'=>$victoire ]);
    }

//    creer une nouvelle partie dans le tournois
    private function createPartie($id, $joueur1, $joueur2){


        $partie = new Partie();

        $partie->setJoueur1($joueur1);
        $partie->setJoueur2($joueur2);

        $em = $this->getDoctrine()->getManager();
        $em->persist($partie);
        $em->flush();

        // récupérer les cartes
        $cartes = $this->getDoctrine()->getRepository('AppBundle:Carte')->findAll();

        //on mélange les cartes
        shuffle($cartes);

        $t = array();
        for ($i = 0; $i < 8; $i++) {
            $t[] = $cartes[$i]->getId();
        }
        $partie->setPartieMainJoueur1(json_encode($t));

        $t = array();
        for ($i = 8; $i < 16; $i++) {
            $t[] = $cartes[$i]->getId();
        }
        $partie->setPartieMainJoueur2(json_encode($t));


        $t = array();
        for ($i = 16; $i < count($cartes); $i++) {
            $t[] = $cartes[$i]->getId();
        }
        $partie->setPartiePioche(json_encode($t));

//            $tab = array();
        $tab_terrain = array('1'=> array(), '2'=> array(), '3'=> array(), '4'=> array(), '5'=> array());

        $partie->setPartieTerrainJoueur1(json_encode($tab_terrain));

        $partie->setPartieTerrainJoueur2(json_encode($tab_terrain));

        $partie->setPartieDefausse(json_encode($tab_terrain));

        $partie->setPartieCartePosee(0);

        $partie->setTournois($id);

        $em->persist($partie);
        $em->flush();

    }

    /**
     * @param Tournois $idTourois
     * @param Partie $id
     *
     * @Route("/{idTourois}/partie/{id}", name="afficher_tounois_partie")
     */
    public function tounoisPartieAction(Request $request,Tournois $idTourois, Partie $id){


        $response = $this->forward('AppBundle:Joueurs:Partie', array(
            'id'  => $id,
        ));

        return $response;
//        dump($response);
//        die();

//        return $this->render('AppBundle:Default:test.html.twig');
    }

}
