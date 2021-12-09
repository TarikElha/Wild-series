<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\ProgramRepository;


#[Route('/category', name: 'category_')]
class CategoryController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $categories = $this->getDoctrine()
                        ->getRepository(Category::class)
                        ->findAll();

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/{categoryName<^[a-z]+$>}", name="show")
     * @return Response
     */
    public function show(string $categoryName, CategoryRepository $categoryRepository, ProgramRepository $programRepository): Response
    {
        $category = $categoryRepository->findOneByName($categoryName);
        
        if (!$category) {
            throw $this->createNotFoundException(
                "$categoryName is not valid"
            );
        }

        $programs = $programRepository->findByCategory($category, ['id' => 'DESC'], '3');

        return $this->render(
            'category/show.html.twig',
            [
                'category' => $category,
                'programs' => $programs
            ]
        );
    }
}
