<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\Form\ProgramType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Entity\Program;
use App\Entity\Season;
use App\Entity\Episode;
use App\Repository\SeasonRepository;
use App\Repository\EpisodeRepository;
use App\Service\Slugify;

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
    public function new(Request $request, Slugify $slugify)
    {
        $program = new Program();
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $slug = $slugify->generate($program->getTitle());
            $program->setSlug($slug);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($program);
            $entityManager->flush();
            return $this->redirectToRoute('program_index');
        }

        return $this->render('program/new.html.twig', ['formView' => $form->createView(),]);
    }

    /**
     * @Route("/{slug}", name="show")
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
     * @Route("/{slug}/season/{season<^[0-9]+$>}", name="season_show")
     * @return Response
     */
    public function showSeason(Program $program, Season $season, EpisodeRepository $episodeRepository):Response
    {
        $episodes = $episodeRepository->findBy(['season' => $season->getId()]);

        return $this->render('program/season_show.html.twig', ['season' => $season, 'program' => $program, 'episodes' => $episodes]);
    }
        
    /**
     * @Route("/{program_slug}/season/{season<^[0-9]+$>}/episode/{episode_slug}", name="episode_show")
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"program_slug": "slug"}})
     * @ParamConverter("episode", class="App\Entity\Episode", options={"mapping": {"episode_slug": "slug"}})
     * @return Response
     */
    public function showEpisode(Program $program, Season $season, Episode $episode):Response
    {
        return $this->render('program/episode_show.html.twig', ['season' => $season, 'program' => $program, 'episode' => $episode]);
    }

    #[Route('/{id}', name: 'program_delete', methods: ['POST'])]
    public function delete(Request $request, Program $program, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$program->getId(), $request->request->get('_token'))) {
            $entityManager->remove($program);
            $entityManager->flush();
        }

        return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
    }
}