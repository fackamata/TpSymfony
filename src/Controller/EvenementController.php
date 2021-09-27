<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/evenement')]
class EvenementController extends AbstractController
{
    #[Route('/', name: 'evenement_index', methods: ['GET'])]
    public function index(EvenementRepository $evenementRepository): Response
    {
        return $this->render('admin/evenement/index.html.twig', [
            'evenements' => $evenementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'evenement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SluggerInterface $sluggerInterface): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $evenement = $form->getData();
            $image = $form->get('image')->getData();

            if($image){
                $orginalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $newFile = $sluggerInterface->slug($orginalName). '-'.uniqid().'.'.$image->guessExtension();
                try{
                    $image->move(
                        $this->getParameter('files'),
                        $newFile
                    );

                }catch(FileException $e){
                    throw new \Exception($e);
                }
                $evenement->setImage($newFile);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenement);
            $entityManager->flush();

            return $this->redirectToRoute('evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/evenement/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'evenement_show', methods: ['GET'])]
    public function show(Evenement $evenement): Response
    {   
        return $this->render('admin/evenement/show.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    #[Route('/{id}/edit', name: 'evenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evenement $evenement, SluggerInterface $sluggerInterface): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $previousImage = $evenement->getImage();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

            if($image){
                $orginalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $newFile = $sluggerInterface->slug($orginalName). '-'.uniqid().'.'.$image->guessExtension();
                try{
                    $image->move(
                        $this->getParameter('files'),
                        $newFile
                    );

                }catch(FileException $e){
                    throw new \Exception($e);
                }
                $evenement->setImage($newFile);
            }

            $this->getDoctrine()->getManager()->flush();
            $newImage = $evenement->getImage();

            if ($previousImage != $newImage && $previousImage !="") {
                $racine = $this->getParameter('files');
                // dd($this->getParameter('files'));
                $completePath = $racine .'/'. $previousImage;
                unlink($completePath);
            }

            return $this->redirectToRoute('evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'evenement_delete', methods: ['POST'])]
    public function delete(Request $request, Evenement $evenement): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evenement->getId(), $request->request->get('_token'))) {
            $completePath = $this->getParameter('files').'/'. $evenement->getImage();
            if (is_file($completePath)) {

                unlink($completePath);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($evenement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('evenement_index', [], Response::HTTP_SEE_OTHER);
    }
}
