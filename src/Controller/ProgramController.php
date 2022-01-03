<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\Form\ProgramType;
use App\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Entity\Program;
use App\Entity\Season;
use App\Entity\Episode;
use App\Entity\Comment;
use App\Repository\SeasonRepository;
use App\Repository\EpisodeRepository;
use App\Repository\CommentRepository;
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
    public function new(Request $request, Slugify $slugify, MailerInterface $mailer)
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

            $email = (new Email())
                ->from($this->getParameter('mailer_from'))
                ->to('your_email@example.com')
                ->subject('Une nouvelle série vient d\'être publiée !')
                ->html($this->renderView('program/newProgramEmail.html.twig', ['program' => $program, ]));

            $mailer->send($email);

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
    public function showEpisode(Request $request, Program $program, Season $season, Episode $episode, CommentRepository $commentRepository):Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $comment->setUser($this->getUser());
            $comment->setEpisode($episode);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
        }

        $comments = $commentRepository->findBy(['episode' => $episode->getId()]);

        return $this->render('program/episode_show.html.twig', ['season' => $season, 'program' => $program, 'episode' => $episode, 'formView' => $form->createView(), 'comments' => $comments,]);
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