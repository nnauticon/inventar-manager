<?php

namespace App\Controller;

use App\Entity\Inventar;
use App\Form\InventarType;
use App\Repository\InventarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/inventar')]
final class InventarController extends AbstractController
{
    #[Route(name: 'app_inventar_index', methods: ['GET'])]
    public function index(InventarRepository $inventarRepository): Response
    {
        return $this->render('inventar/index.html.twig', [
            'inventars' => $inventarRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_inventar_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $inventar = new Inventar();
        $form = $this->createForm(InventarType::class, $inventar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($inventar);
            $entityManager->flush();

            return $this->redirectToRoute('app_inventar_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('inventar/new.html.twig', [
            'inventar' => $inventar,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_inventar_show', methods: ['GET'])]
    public function show(Inventar $inventar): Response
    {
        return $this->render('inventar/show.html.twig', [
            'inventar' => $inventar,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_inventar_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Inventar $inventar, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InventarType::class, $inventar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_inventar_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('inventar/edit.html.twig', [
            'inventar' => $inventar,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_inventar_delete', methods: ['POST'])]
    public function delete(Request $request, Inventar $inventar, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$inventar->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($inventar);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_inventar_index', [], Response::HTTP_SEE_OTHER);
    }
}
