<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;

#[Route('/my-profile', name: 'profile_')]
class ProfileController extends AbstractController
{
    #[Route('/', name: 'show')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function show(UserRepository $userRepository): Response
    {
        $user = $userRepository->find(['id' => $this->getUser()->getId()]);

        return $this->render('profile/index.html.twig', [ 'user' => $user
        ]);
    }
}