<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Actor;
use App\Repository\ActorRepository;

/**
* @Route("/actor", name="actor_")
*/
class ActorController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @return Response
     */
    public function index(): Response
    {
        $actors = $this->getDoctrine()
                        ->getRepository(Actor::class)
                        ->findAll();

        return $this->render('actor/index.html.twig', ['actors' => $actors]);
    }


    /**
     * @Route("/{actor<^[0-9]+$>}", name="show")
     * @return Response
     */
    public function show(Actor $actor): Response
    {
        if (!$actor) {
            throw $this->createNotFoundException(
                'No actor with id : '.$id.' found in actor\'s table.'
            );
        }

        return $this->render('actor/show.html.twig', ['actor' => $actor, 'programs' => $actor->getPrograms()]);
    }

}