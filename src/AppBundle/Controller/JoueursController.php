<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\Partie;
use AppBundle\Entity\Carte;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class DefaultController
 * @package AppBundle\Controller
 * @Route("joueur")
 */

class JoueursController extends Controller
{

//    permet dactualiser si lautre joueur à joué
    /**
     * @Route("/partie/verif/{partie}/", name="verif_partie")
     */
    public function VerifPartieAction($partie){

        $em = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Partie')
        ;

        $game = $em->findOneBy(array('id' => $partie  ));

        $touractuel = $game->getPartieTourJoueur();



//            return new Response(
//                '',200
//            );

            $response = new Response();
            $response->setContent(json_encode(array(
                'tour' => $touractuel,
            )));
            $response->headers->set('Content-Type', 'application/json');

            return $response;


//            return new Response(
//                'CHANGEMENT DE TOUR'
//            );

    }



    /**
     * @Route("/", name="joueur_homepage")
     */
    public function indexAction()
    {
        $user = $this->getUser();

        return $this->render("AppBundle:joueurs:index.html.twig", ['user' => $user]);
    }

    /**
     * @Route("/classement", name="joueur_partie_classement")
     */
    public function classementAction()
    {
        $user = $this->getUser();

        $find = $this->getDoctrine()->getRepository('AppBundle:User')->findBy(array(),array('userBestScore' => 'DESC'));


        return $this->render("AppBundle:joueurs:classement.html.twig", ['user' => $user, 'classement'=> $find]);
    }

    /**
     * @Route("/historique", name="joueur_partie_historique")
     */
    public function historiqueAction()
    {
        $user = $this->getUser();

        return $this->render("AppBundle:joueurs:historique.html.twig", ['user' => $user]);
    }

    /**
     * @param Partie $id
     *
     * @Route("/historique/partie/{id}", name="afficher_partie_historique")
     */
    public function partieHistoriqueAction(Partie $id)
    {
        $user = $this->getUser();


        $find = $this->getDoctrine()->getRepository('AppBundle:Partie')->find($id);
        $user1 = $find->getJoueur1();
        $user2 = $find->getJoueur2();
//        $myId = $user->getid();

        if ($user1 == $user || $user2 == $user){
            $maPartie = true;
            $idPartie = $find->getId();
        }else{
            return $this->redirectToRoute('joueur_partie');
        }

        $em = $this->getDoctrine()->getManager();


        $partie = $em->getRepository('AppBundle:Partie')->find( $id);


        $myId = $user->getid();
        $joueur1Id = $partie->getJoueur1()->getId();
        $joueur2Id = $partie->getJoueur2()->getId();

        $scoreJ1 = $partie->getPartieScoreJoueur1();
        $scoreJ2 = $partie->getPartieScoreJoueur2();


        $tourJoueur = $partie->getPartieTourJoueur();

        $idPartie = $find->getId();

        $partieDateFin = $partie->getPartieDateFin();

        if($partieDateFin){
            $partieFin = true;
        }else{
            $partieFin = false;
        }

//        definition de la partie du joueur et de son adversaire
        if ($myId == $joueur1Id){

            $imJoueur = 1;

            if ($scoreJ2 > $scoreJ1){
                $gagnant = false;
            }else{
                $gagnant = true;
            }

            if ($tourJoueur == 1){
                $montour = true;
            }else{
                $montour = false;
            }

        }elseif ($myId == $joueur2Id){

            $imJoueur = 2;

            if ($scoreJ1 > $scoreJ2){
                $gagnant = false;
            }else{
                $gagnant = true;
            }


            if ($tourJoueur == 2){
                $montour = true;
            }else {
                $montour = false;
            }
        }

        $variablePartie = array('idPartie'=> $idPartie, 'imJoueur' => $imJoueur, 'montour' => $montour, 'partieFin' => $partieFin, 'gagnant' => $gagnant );

        return $this->render("AppBundle:joueurs:partieHistorique.html.twig", ['user' => $user, 'partie' => $partie , 'variablePartie' => $variablePartie]);
    }

    /**
     * @Route("/parties/", name="joueur_partie")
     */
    public function mesPartiesAction()
    {
        $user = $this->getUser();


        return $this->render("AppBundle:joueurs:mesParties.html.twig", ['user' => $user]);
    }


    /**
     * @Route("/parties/add", name="joueur_partie_add")
     */
    public function addPartieAction()
    {
        $user = $this->getUser();

        // récupérer tous les joueurs existants
        $joueurs = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();
        return $this->render("AppBundle:joueurs:addPartie.html.twig", ['user' => $user, 'joueurs' => $joueurs]);
    }

    /**
     * @param User $id
     *
     * @Route("/inviter/{joueur}", name="creer_partie")
     */
    public function creerPartieAction(User $joueur)
    {
        $user = $this->getUser();

        $find = $this->getDoctrine()->getRepository('AppBundle:Partie')->findBy(array('joueur1' => $user, 'joueur2' => $joueur));

        foreach ($find as $part){

                $idTrouver = $part->getId();

                $partieFini = $part->getPartieDateFin();
        }

        if ($find && !$partieFini) {
            return $this->redirectToRoute('afficher_partie', array('id' => $idTrouver));
        } else{
            $partie = new Partie();

            $partie->setJoueur1($user);
            $partie->setJoueur2($joueur);

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

            $em->persist($partie);
            $em->flush();


            $find2 = $this->getDoctrine()->getRepository('AppBundle:Partie')->findOneBy(array('joueur1' => $user, 'joueur2' => $joueur));

            //envoi invitation par mail
            $message = \Swift_Message::newInstance()
                ->setSubject('Invitation à joueur')
                ->setFrom($user->getEmail())
                ->setTo($joueur->getEmail())
                ->setBody(
                    $this->renderView(
                    // app/Resources/views/Emails/registration.html.twig
                        'AppBundle::emails/invitation.html.twig',
                        array('name' => $user->getUsername(), 'partie' => $find2)
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

            $find = $this->getDoctrine()->getRepository('AppBundle:Partie')->findBy(array('joueur1' => $user, 'joueur2' => $joueur));

                foreach ($find as $part){

                    return $this->redirectToRoute('afficher_partie', array('id' => $part->getId()));
                }



        }
    }

    /**
     * @param Parti $id
     *
     * @Route("/partie/{id}", name="afficher_partie")
     */
    public function PartieAction(Request $request,Partie $id){

        $user = $this->getUser();

        $find = $this->getDoctrine()->getRepository('AppBundle:Partie')->find($id);
        $user1 = $find->getJoueur1();
        $user2 = $find->getJoueur2();
//        $myId = $user->getid();

        if ($user1 == $user || $user2 == $user){
            $maPartie = true;
            $idPartie = $find->getId();
        }elseif($find->getTournois() != null){
            return $this->redirectToRoute('afficher_tournois', array('id'=>$find->getTournois()->getId()));
//            return $this->redirectToRoute('afficher_mes_tournois');
        }else{
            return $this->redirectToRoute('joueur_partie');
        }

        $em = $this->getDoctrine()->getManager();




        $partie = new Partie();
        $partie = $em->getRepository('AppBundle:Partie')->find( $id);

//        $cartes = $em->getRepository('AppBundle:Carte')->findAll();

        $pioche = array();

        $cartePioche = $partie->getPartiePioche();

//      recuperer cartes avec associations
        $cartes2 = $this->getDoctrine()->getRepository('AppBundle:Carte')->getAll();

//        foreach ($cartePioche as $carte)
//        {
//
//            foreach ($cartes as $t) {
//
//                $id = $t->getId();
//
//                if ($id == $carte)
//                {
//                    $pioche[$id] = array('id'=>$t->getId(), 'carteValeur' =>$t->getCarteValeur(), 'carteExtra' =>$t->getCarteExtra(), 'carteNom' =>$t->getCarteNom(), 'carteImage' =>$t->getCarteImage(), 'carteLibelle' =>$t->getCarteCategorie()->getLibelle());
//                }
//            }
//        }

//        $mainJoueur1 = array();
//        //foreach ($partie as $j) {
//
//            $mainJ1 = $partie->getPartieMainJoueur1();
//
//            foreach ($mainJ1 as $carte)
//            {
//
//                foreach ($cartes as $t) {
//
//                    $id = $t->getId();
//
//                    if ($id == $carte)
//                    {
//                        $mainJoueur1[$id] = array('id'=>$t->getId(), 'categorie'=>$t->getCarteCategorie(), 'carteValeur' =>$t->getCarteValeur(), 'carteExtra' =>$t->getCarteExtra(), 'carteNom' =>$t->getCarteNom(), 'carteImage' =>$t->getCarteImage(), 'carteLibelle' =>$t->getCarteCategorie()->getLibelle());
//
//                    }
//                }
//            }
////        }


        $myId = $user->getid();
        $joueur1Id = $partie->getJoueur1()->getId();
        $joueur2Id = $partie->getJoueur2()->getId();

        $joueur1Terrain = $partie->getPartieTerrainJoueur1();
        $joueur2Terrain = $partie->getPartieTerrainJoueur2();


        $partieDefausse = json_decode(json_encode($partie->getPartieDefausse()), true);

        $mainJoueur1 = $partie->getPartieMainJoueur1();
        $mainJoueur2 = $partie->getPartieMainJoueur2();

        $tourJoueur = $partie->getPartieTourJoueur();
        $cartePosee = $partie->getPartieCartePosee();

        $carteRestante = count($cartePioche);

        $idPartie = $find->getId();

        $partieDateFin = $partie->getPartieDateFin();

        $partieTournois = $partie->getTournois();


        if($partieDateFin){
            $partieFin = true;

        }else{
            $partieFin = false;
        }

//        definition de la partie du joueur et de son adversaire
        if ($myId == $joueur1Id){

            $mesCartes = $mainJoueur1;
            $monTerrain =json_decode(json_encode($joueur1Terrain), true);

            $i=1;
            $totalPoint = 0;
            $nbBonnus= 0;
            $nbCartes = 0;
            $scoreJ1 = 0;
            foreach ($monTerrain as $terrain){

                foreach ($terrain as $point){
//                    dump(end($point));
                    if (end($point) == 1){

                        $nbBonnus++;

                        if ($nbBonnus > 1 ){
                            $totalPoint =$totalPoint-20;
                        }else{
                            $totalPoint = -40;
                        }

                    }else{
                        if ($nbCartes > 0){
                            if ($nbBonnus > 0){
                                switch ($nbBonnus){
                                    case 1 :
                                        $totalPoint =$totalPoint + (end($point)*2);
                                        break;
                                    case 2 :
                                        $totalPoint =$totalPoint + (end($point)*3);
                                        break;
                                    case 3 :
                                        $totalPoint =$totalPoint + (end($point)*4);
                                        break;
                                }

                            }else{
                                $totalPoint =$totalPoint + end($point);
                            }

                        }else{
                            $totalPoint = -20 + end($point);
                        }

                    }

                    $nbCartes ++;

                }

                array_push($monTerrain[$i], array('totalPoint' => $totalPoint, 'nbBonnus'=> $nbBonnus, 'nbCartes'=> $nbCartes));
                $scoreJ1 = $scoreJ1 + $totalPoint ;
                $totalPoint = 0;
                $nbBonnus =0;
                $nbCartes = 0;
//                dump($terrain);
                $i++;

            }

            $partie->setPartieScoreJoueur1($scoreJ1);


            $adversaireCartes = $mainJoueur2;
            $adversaireTerrain =json_decode(json_encode($joueur2Terrain), true);

            $i=1;
            $totalPoint = 0;
            $nbBonnus= 0;
            $nbCartes = 0;
            $scoreJ2 = 0;
            foreach ($adversaireTerrain as $terrain){

                foreach ($terrain as $point){
//                    dump(end($point));
                    if (end($point) == 1){

                        $nbBonnus++;

                        if ($nbBonnus > 1 ){
                            $totalPoint =$totalPoint-20;
                        }else{
                            $totalPoint = -40;
                        }

                    }else{
                        if ($nbCartes > 0){
                            if ($nbBonnus > 0){
                                switch ($nbBonnus){
                                    case 1 :
                                        $totalPoint =$totalPoint + (end($point)*2);
                                        break;
                                    case 2 :
                                        $totalPoint =$totalPoint + (end($point)*3);
                                        break;
                                    case 3 :
                                        $totalPoint =$totalPoint + (end($point)*4);
                                        break;
                                }

                            }else{
                                $totalPoint =$totalPoint + end($point);
                            }

                        }else{
                            $totalPoint = -20 + end($point);
                        }

                    }

                    $nbCartes ++;

                }

                array_push($adversaireTerrain[$i], array('totalPoint' => $totalPoint, 'nbBonnus'=> $nbBonnus, 'nbCartes'=> $nbCartes));
                $scoreJ2 = $scoreJ2 + $totalPoint ;
                $totalPoint = 0;
                $nbBonnus =0;
                $nbCartes = 0;
//                dump($terrain);
                $i++;


            }

            $partie->setPartieScoreJoueur2($scoreJ2);

            $em->flush();

            $imJoueur = 1;

            $me = $partie->getJoueur1();

            $monScore = $scoreJ1;
            $adversaireScore = $scoreJ2;

            $adversaire = $partie->getJoueur2();

            if ($cartePosee == 0){
                $activePose = true;
            }elseif ($cartePosee == 1){

                $activePose = false;
            }

            if ($tourJoueur == 1){
                $montour = true;
            }else{
                $montour = false;
            }

            if ($partieFin == true){

                $bestScoreJ1 = $partie->getJoueur1()->getUserBestScore();
                $bestScoreJ2 = $partie->getJoueur2()->getUserBestScore();

                if ($bestScoreJ1 < $scoreJ1){

                    $partie->getJoueur1()->setUserBestScore($scoreJ1);

                    $em->flush();
                }
                if ($bestScoreJ2 < $scoreJ2){

                    $partie->getJoueur2()->setUserBestScore($scoreJ2);

                    $em->flush();
                }

            }

        }elseif ($myId == $joueur2Id){

            $mesCartes = $mainJoueur2;
            $monTerrain = json_decode(json_encode($joueur2Terrain), true);

            $i=1;
            $totalPoint = 0;
            $nbBonnus= 0;
            $nbCartes = 0;
            $scoreJ2 = 0;
            foreach ($monTerrain as $terrain){

                foreach ($terrain as $point){
//                    dump(end($point));
                    if (end($point) == 1){

                        $nbBonnus++;

                        if ($nbBonnus > 1 ){
                            $totalPoint =$totalPoint-20;
                        }else{
                            $totalPoint = -40;
                        }

                    }else{
                        if ($nbCartes > 0){
                            if ($nbBonnus > 0){
                                switch ($nbBonnus){
                                    case 1 :
                                        $totalPoint =$totalPoint + (end($point)*2);
                                        break;
                                    case 2 :
                                        $totalPoint =$totalPoint + (end($point)*3);
                                        break;
                                    case 3 :
                                        $totalPoint =$totalPoint + (end($point)*4);
                                        break;
                                }

                            }else{
                                $totalPoint =$totalPoint + end($point);
                            }

                        }else{
                            $totalPoint = -20 + end($point);
                        }

                    }

                    $nbCartes ++;

                }

                array_push($monTerrain[$i], array('totalPoint' => $totalPoint, 'nbBonnus'=> $nbBonnus, 'nbCartes'=> $nbCartes));
                $scoreJ2 = $scoreJ2 + $totalPoint ;
                $totalPoint = 0;
                $nbBonnus =0;
                $nbCartes = 0;
//                dump($terrain);
                $i++;


            }

            $partie->setPartieScoreJoueur2($scoreJ2);

            $em->flush();



            $adversaireCartes = $mainJoueur1;
            $adversaireTerrain = json_decode(json_encode($joueur1Terrain), true);

            $i=1;
            $totalPoint = 0;
            $nbBonnus= 0;
            $nbCartes = 0;
            $scoreJ1 = 0;
            foreach ($adversaireTerrain as $terrain){

                foreach ($terrain as $point){
//                    dump(end($point));
                    if (end($point) == 1){

                        $nbBonnus++;

                        if ($nbBonnus > 1 ){
                            $totalPoint =$totalPoint-20;
                        }else{
                            $totalPoint = -40;
                        }

                    }else{
                        if ($nbCartes > 0){
                            if ($nbBonnus > 0){
                                switch ($nbBonnus){
                                    case 1 :
                                        $totalPoint =$totalPoint + (end($point)*2);
                                        break;
                                    case 2 :
                                        $totalPoint =$totalPoint + (end($point)*3);
                                        break;
                                    case 3 :
                                        $totalPoint =$totalPoint + (end($point)*4);
                                        break;
                                }

                            }else{
                                $totalPoint =$totalPoint + end($point);
                            }

                        }else{
                            $totalPoint = -20 + end($point);
                        }

                    }

                    $nbCartes ++;

                }

                array_push($adversaireTerrain[$i], array('totalPoint' => $totalPoint, 'nbBonnus'=> $nbBonnus, 'nbCartes'=> $nbCartes));
                $scoreJ1 = $scoreJ1 + $totalPoint ;
                $totalPoint = 0;
                $nbBonnus =0;
                $nbCartes = 0;
//                dump($terrain);
                $i++;


            }

            $partie->setPartieScoreJoueur1($scoreJ1);

            $em->flush();

            $imJoueur = 2;

            $me = $partie->getJoueur2();

            $monScore = $scoreJ2;
            $adversaireScore = $scoreJ1;

            $adversaire = $partie->getJoueur1();

            if ($cartePosee == 0){
                $activePose = true;
            }elseif ($cartePosee == 1){

                $activePose = false;
            }

            if ($tourJoueur == 2){
                $montour = true;
            }else {
                $montour = false;
            }

            if ($partieFin == true){

                $bestScoreJ1 = $partie->getJoueur1()->getUserBestScore();
                $bestScoreJ2 = $partie->getJoueur2()->getUserBestScore();

                if ($bestScoreJ1 < $scoreJ1){

                    $partie->getJoueur1()->setUserBestScore($scoreJ1);

                    $em->flush();
                }
                if ($bestScoreJ2 < $scoreJ2){

                    $partie->getJoueur2()->setUserBestScore($scoreJ2);

                    $em->flush();
                }

            }
        }

        $chat = $this->getDoctrine()->getRepository('AppBundle:Chat')->findBy(array('partie' => $partie->getId()));

        $variablePartie = array('idPartie'=> $idPartie, 'imJoueur' => $imJoueur, 'pioche'=>$pioche, 'mesCartes'=>$mesCartes, 'monTerrain'=>$monTerrain, 'adversaireCartes'=> $adversaireCartes, 'adversaireTerrain' => $adversaireTerrain, 'montour' => $montour, 'partieDefausse' => $partieDefausse , 'activePose' => $activePose, 'carteRestante' => $carteRestante, 'partieFin' => $partieFin, 'scoreJ1' => $scoreJ1, 'scoreJ2' => $scoreJ2, 'tournois' => $partieTournois, 'monScore' => $monScore, 'adversaireScore' => $adversaireScore, 'me'=> $me, 'chat'=>$chat, 'adversaire' => $adversaire );

//      traiter les requetes
        if ($request->isMethod('POST'))
        {

//            die();
            $piocher = $request->request->get('piocher');

            $poser = $request->request->get('poser');

            $defausser = $request->request->get('defausser');

            $recuperer = $request->request->get('recuperer');


//      faire piocher ke joueur
            if($piocher) {

//                die();
                $partiePioche = $partie->getPartiePioche();

                $cartePiocher = array_pop($partiePioche);

                $partie->setPartiePioche(json_encode($partiePioche));

                $carteRestante = count($cartePioche);


                $partie->setPartieCartePosee(0);

                if ($myId == $joueur1Id) {

                    if ($carteRestante == 1) {
                        $partie->setPartieDateFin(new \DateTime("now"));;

                        $em->flush();
                    }

                    array_push($mainJoueur1, $cartePiocher);

                    $partie->setPartieMainJoueur1(json_encode($mainJoueur1));

                    $partie->setPartieTourJoueur(2);

                    $em->flush();


                } elseif ($myId == $joueur2Id) {

                    if ($carteRestante == 1) {
                        $partie->setPartieDateFin(new \DateTime("now"));;

                        $em->flush();
                    }

                    array_push($mainJoueur2, $cartePiocher);

                    $partie->setPartieMainJoueur2(json_encode($mainJoueur2));

                    $partie->setPartieTourJoueur(1);

                    $em->flush();

                }

                return $this->redirectToRoute('afficher_partie', array('id' => $idPartie));
            }

//            recuperer carte de la defausse
            if ($recuperer){


                $recupererId = $request->request->get('recupererId');
                $recupererCategorieId = $request->request->get('recupererCategorieId');

                $partieDefausse = json_decode(json_encode($partie->getPartieDefausse()), true);

//

                $partie->setPartieCartePosee(0);

                if ($myId == $joueur1Id){

                    array_push($mainJoueur1, $recupererId);

                    $partie->setPartieMainJoueur1(json_encode($mainJoueur1));


//                    $defausse = $partieDefausse[$recupererCategorieId];

//                    var_dump($defausse);
//                die();

                    $i=0;
                    while ($value = current($partieDefausse[$recupererCategorieId])) {
                        if ($value == $recupererId) {
                            array_splice($partieDefausse[$recupererCategorieId], $i, 1);
                        }
                        next($partieDefausse[$recupererCategorieId]);

                        $i++;
                    }

                    $partie->setPartieDefausse(json_encode($partieDefausse));


                    $partie->setPartieTourJoueur(2);

                    $em->flush();


                }elseif ($myId == $joueur2Id){

                    array_push($mainJoueur2, $recupererId);

                    $partie->setPartieMainJoueur2(json_encode($mainJoueur2));


//                    $defausse = $partieDefausse[$recupererCategorieId];

//                    var_dump($defausse);
//                die();

                    $i=0;
                    while ($value = current($partieDefausse[$recupererCategorieId])) {
                        if ($value == $recupererId) {
                            array_splice($partieDefausse[$recupererCategorieId], $i, 1);
                        }
                        next($partieDefausse[$recupererCategorieId]);

                        $i++;
                    }

                    $partie->setPartieDefausse(json_encode($partieDefausse));


                    $partie->setPartieTourJoueur(1);

                    $em->flush();

                }
                return $this->redirectToRoute('afficher_partie', array('id' => $idPartie));



            }

//            le joueur pose une carte
            if ($poser){

                $cartePoserId = $request->request->get('cartePoserId');
                $cartePoserValeur = $request->request->get('cartePoserValeur');
                $cartePoserCategorie = $request->request->get('cartePoserCategorie');
                $cartePoserExtra = $request->request->get('cartePoserExtra');


                if ($myId == $joueur1Id){

                    $partieTerrain = $partie->getPartieTerrainJoueur1();

                    $partie->setPartieCartePosee(1);


                    $tab=json_decode(json_encode($partieTerrain), true);

                    $tabCat = $tab[$cartePoserCategorie];

                    if (!empty($tabCat)) {
                        $t = end($tabCat);
                        $lastValeur = end($t);

//                        die($lastValeur);
//                        if ($cartePoserExtra == 1) {

                            if ($lastValeur > $cartePoserValeur && $cartePoserExtra != 1) {

                                echo 'mettez une carte avec une plus grande valeur';

                            } else {

                                if ($cartePoserValeur == 1 && $lastValeur == 1|| $cartePoserValeur > $lastValeur) {

//                                    die();
                                    $cat = array($cartePoserId, $cartePoserValeur);

                                    array_push($tab[$cartePoserCategorie], $cat);

                                    $i = 0;
                                    while ($value = current($mainJoueur1)) {
                                        if ($value == $cartePoserId) {
                                            array_splice($mainJoueur1, $i, 1);
                                        }
                                        next($mainJoueur1);

                                        $i++;
                                    }

                                    $partie->setPartieTerrainJoueur1(json_encode($tab));

                                    $partie->setPartieMainJoueur1(json_encode($mainJoueur1));


                                    $em->flush();
                                }
                            }
//                        }

                    }else{
                        $cat = array($cartePoserId, $cartePoserValeur);

                        array_push($tab[$cartePoserCategorie], $cat);

                        $i=0;
                        while ($value = current($mainJoueur1)) {
                            if ($value == $cartePoserId) {
                                array_splice($mainJoueur1, $i, 1);
                            }
                            next($mainJoueur1);

                            $i++;
                        }

                        $partie->setPartieTerrainJoueur1(json_encode($tab));

                        $partie->setPartieMainJoueur1(json_encode($mainJoueur1));



                        $em->flush();
                    }




                }elseif ($myId == $joueur2Id){

                    $partieTerrain = $partie->getPartieTerrainJoueur2();

                    $partie->setPartieCartePosee(1);


                    $tab=json_decode(json_encode($partieTerrain), true);

                    $tabCat = $tab[$cartePoserCategorie];

                    if (!empty($tabCat)) {
                        $t = end($tabCat);
                        $lastValeur = end($t);

//                        die($lastValeur);
//                        if ($cartePoserExtra == 1) {

                        if ($lastValeur > $cartePoserValeur && $cartePoserExtra != 1) {

                            echo 'mettez une carte avec une plus grande valeur';

                        } else {

                            if ($cartePoserValeur == 1 && $lastValeur == 1|| $cartePoserValeur > $lastValeur) {

//                                    die();
                                $cat = array($cartePoserId, $cartePoserValeur);

                                array_push($tab[$cartePoserCategorie], $cat);

                                $i = 0;
                                while ($value = current($mainJoueur2)) {
                                    if ($value == $cartePoserId) {
                                        array_splice($mainJoueur2, $i, 1);
                                    }
                                    next($mainJoueur2);

                                    $i++;
                                }

                                $partie->setPartieTerrainJoueur2(json_encode($tab));

                                $partie->setPartieMainJoueur2(json_encode($mainJoueur2));


                                $em->flush();
                            }
                        }
//                        }

                    }else{
                        $cat = array($cartePoserId, $cartePoserValeur);

                        array_push($tab[$cartePoserCategorie], $cat);

                        $i=0;
                        while ($value = current($mainJoueur2)) {
                            if ($value == $cartePoserId) {
                                array_splice($mainJoueur2, $i, 1);
                            }
                            next($mainJoueur2);

                            $i++;
                        }

                        $partie->setPartieTerrainJoueur2(json_encode($tab));

                        $partie->setPartieMainJoueur2(json_encode($mainJoueur2));


                        $em->flush();
                    }

                }
                return $this->redirectToRoute('afficher_partie', array('id' => $idPartie));
            }

//            Defausser une carte au joueur

            if ($defausser){

                $cartePoserId = $request->request->get('cartePoserId');
//                $cartePoserValeur = $request->request->get('cartePoserValeur');
                $cartePoserCategorie = $request->request->get('cartePoserCategorie');

                if ($myId == $joueur1Id){

                    $defausseTerrain = $partie->getPartieDefausse();

                    $partie->setPartieCartePosee(1);


                    $tab=json_decode(json_encode($defausseTerrain), true);


//                    $cat = array($cartePoserId, $cartePoserValeur);

                    array_push($tab[$cartePoserCategorie], $cartePoserId);

                    $i=0;
                    while ($value = current($mainJoueur1)) {
                        if ($value == $cartePoserId) {
                            array_splice($mainJoueur1, $i, 1);
                        }
                        next($mainJoueur1);

                        $i++;
                    }

                    $partie->setPartieDefausse(json_encode($tab));

                    $partie->setPartieMainJoueur1(json_encode($mainJoueur1));

                    $em->flush();

                }elseif ($myId == $joueur2Id){

                    $defausseTerrain = $partie->getPartieDefausse();

                    $partie->setPartieCartePosee(1);


                    $tab=json_decode(json_encode($defausseTerrain), true);


//                    $cat = array($cartePoserId, $cartePoserValeur);

                    array_push($tab[$cartePoserCategorie], $cartePoserId);

                    $i=0;
                    while ($value = current($mainJoueur2)) {
                        if ($value == $cartePoserId) {
                            array_splice($mainJoueur2, $i, 1);
                        }
                        next($mainJoueur2);

                        $i++;
                    }

                    $partie->setPartieDefausse(json_encode($tab));

                    $partie->setPartieMainJoueur2(json_encode($mainJoueur2));

                    $em->flush();

                }
                return $this->redirectToRoute('afficher_partie', array('id' => $idPartie));
            }
        }

        return $this->render('AppBundle:joueurs:partie.html.twig', ['cartes3'=>$cartes2, 'partie' => $partie, 'user'=> $user, 'variablePartie'=> $variablePartie]);
    }

}
