<?php

namespace App\Controller;

use DateTime;
use App\Entity\Posts;
use App\Form\TodoType;
use App\Repository\PostsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TodoController extends AbstractController
{
    #[Route('/', name: 'app_todo')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(EntityManagerInterface $entityManager, Request $request, PostsRepository $posts): Response
    {
        $form = $this->createForm(TodoType::class, new Posts);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $post->setAuthor($this->getUser());
            $post->setCreated(new DateTime());
            $entityManager->persist($post);
            $entityManager->flush();


            //Flash messages
            $this->addFlash('success', 'Post success sent.');

            //Redirect after sent
            return $this->redirectToRoute('app_todo');
        }

        return $this->render('todo/index.html.twig', [
            'form' => $form,
            'posts' => $posts->findAllByAuthor($this->getUser())
        ]);
    }

    #[Route('/done/{id}', name: 'app_todo_done')]
    public function done(EntityManagerInterface $entityManager, Posts $post, PostsRepository $posts): Response
    {
        $entityManager->remove($post);
        $entityManager->flush();

        $form = $this->createForm(TodoType::class, new Posts);

        return $this->redirectToRoute('app_todo');
    }
}
