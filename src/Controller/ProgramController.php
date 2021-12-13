<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Program;
use App\Repository\ProgramRepository;
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
     * @Route("/{id<^[0-9]+$>}", name="show")
     * @return Response
     */
    public function show(int $id, ProgramRepository $programRepository, SeasonRepository $seasonRepository): Response
    {
        $program = $programRepository->findOneBy(['id' => $id]);
        $seasons = $seasonRepository->findBy(['program' => $id], ['number' => 'ASC']);

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : '.$id.' found in program\'s table.'
            );
        }
        return $this->render('program/show.html.twig', ['program' => $program, 'seasons' => $seasons]);
    }

    /**
     * @Route("/{programId<^[0-9]+$>}/seasons/{seasonId<^[0-9]+$>}", name="season_show")
     * @return Response
     */
    public function showSeason(int $programId, int $seasonId, EpisodeRepository $episodeRepository, SeasonRepository $seasonRepository, ProgramRepository $programRepository):Response
    {
        $season = $seasonRepository->findOneBy(['id' => $seasonId]);
        $program = $programRepository->findOneBy(['id' => $programId]);
        $episodes = $episodeRepository->findBy(['season' => $seasonId]);

        return $this->render('program/season_show.html.twig', ['season' => $season, 'program' => $program, 'episodes' => $episodes]);
    }
}