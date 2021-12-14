<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\Form\ProgramType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Program;
use App\Entity\Season;
use App\Entity\Episode;
use App\Repository\SeasonRepository;
use App\Repository\EpisodeRepository;

    /**
     * @Route("/program", name="program_")
     */
class ProgramController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $programs = $this->getDoctrine()
                        ->getRepository(Program::class)
                        ->findAll();
        
        return $this->render('program/index.html.twig', ['programs' => $programs]);
    }

    /**
     * @Route("/new", name="new")
     */
    public function new(Request $request)
    {
        $program = new Program();
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($program);
            $entityManager->flush();
            return $this->redirectToRoute('program_index');
        }

        return $this->render('program/new.html.twig', ['formView' => $form->createView(),]);
    }

    /**
     * @Route("/{program<^[0-9]+$>}", name="show")
     * @return Response
     */
    public function show(Program $program, SeasonRepository $seasonRepository): Response
    {
        $seasons = $seasonRepository->findBy(['program' => $program->getId()], ['number' => 'ASC']);

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : '.$id.' found in program\'s table.'
            );
        }
        return $this->render('program/show.html.twig', ['program' => $program, 'seasons' => $seasons]);
    }

    /**
     * @Route("/{program<^[0-9]+$>}/season/{season<^[0-9]+$>}", name="season_show")
     * @return Response
     */
    public function showSeason(Program $program, Season $season, EpisodeRepository $episodeRepository):Response
    {
        $episodes = $episodeRepository->findBy(['season' => $season->getId()]);

        return $this->render('program/season_show.html.twig', ['season' => $season, 'program' => $program, 'episodes' => $episodes]);
    }
        
    /**
     * @Route("/{program<^[0-9]+$>}/season/{season<^[0-9]+$>}/episode/{episode<^[0-9]+$>}", name="episode_show")
     * @return Response
     */
    public function showEpisode(Program $program, Season $season, Episode $episode):Response
    {
        return $this->render('program/episode_show.html.twig', ['season' => $season, 'program' => $program, 'episode' => $episode]);
    }
}