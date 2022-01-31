<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
    */
    public function index(): Response
    {
        return $this->redirectToRoute('program_index');
    }

/*     public function languageChoice(Request $request): Response
    {
        $defaultData = ['message' => 'Type your message here'];
        $form = $this->createFormBuilder($defaultData)
            ->add('Langue', ChoiceType::class, [
                'choices' => [
                    'Français' => 'fr',
                    'English' => 'en',
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $langue = $form->get('Langue')->getViewData();
        }

        return $this->render('_navbarLang.html.twig', ['formNav' => $form->createView(),
                                                        'data' => $form->get('Langue')->getViewData()]);
    } */
}