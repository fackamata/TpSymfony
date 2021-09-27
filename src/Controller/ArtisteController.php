<?php

namespace App\Controller;

use App\Entity\Artiste;
use App\Form\ArtisteType;
use App\Repository\ArtisteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/artiste')]
class ArtisteController extends AbstractController
{
    #[Route('/', name: 'artiste_index', methods: ['GET'])]
    public function index(ArtisteRepository $artisteRepository): Response
    {
        return $this->render('admin/artiste/index.html.twig', [
            'artistes' => $artisteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'artiste_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface   $sluggerInterface): Response
    {
        $artiste = new Artiste();
        $form = $this->createForm(ArtisteType::class, $artiste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $artiste = $form->getData();
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
                $artiste->setImage($newFile);
            }
            $em->persist($artiste);
            $em->flush();

            return $this->redirectToRoute('artiste_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/artiste/new.html.twig', [
            'artiste' => $artiste,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'artiste_show', methods: ['GET'])]
    public function show(Artiste $artiste): Response
    {
        return $this->render('admin/artiste/show.html.twig', [
            'artiste' => $artiste,
        ]);
    }

    #[Route('/{id}/edit', name: 'artiste_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Artiste $artiste, SluggerInterface   $sluggerInterface): Response
    {
        $form = $this->createForm(ArtisteType::class, $artiste);
        $previousImage = $artiste->getImage();
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
                $artiste->setImage($newFile);
            }

            $this->getDoctrine()->getManager()->flush();
            $newImage = $artiste->getImage();

            if ($previousImage != $newImage && $previousImage !="") {
                $racine = $this->getParameter('files');
                // dd($this->getParameter('files'));
                $completePath = $racine .'/'. $previousImage;
                unlink($completePath);
            }

            return $this->redirectToRoute('artiste_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/artiste/edit.html.twig', [
            'artiste' => $artiste,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'artiste_delete', methods: ['POST'])]
    public function delete(Request $request, Artiste $artiste): Response
    {
        if ($this->isCsrfTokenValid('delete'.$artiste->getId(), $request->request->get('_token'))) {
            $completePath = $this->getParameter('files').'/'. $artiste->getImage();

            if (is_file($completePath)) {

                unlink($completePath);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($artiste);
            $entityManager->flush();
        }

        return $this->redirectToRoute('artiste_index', [], Response::HTTP_SEE_OTHER);
    }
}
