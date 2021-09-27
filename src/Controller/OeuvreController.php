<?php

namespace App\Controller;

use App\Entity\Oeuvre;
use App\Form\OeuvreType;
use App\Repository\OeuvreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class OeuvreController extends AbstractController
{
    #[Route('/admin/oeuvre', name: 'oeuvre_index', methods: ['GET'])]
    public function index(OeuvreRepository $oeuvreRepository): Response
    {
        return $this->render('admin/oeuvre/index.html.twig', [
            'oeuvres' => $oeuvreRepository->findAll(),
        ]);
    }
    
    #[Route('/oeuvres', name: 'oeuvre_json_index', methods: ['GET'])]
    public function indexJson(OeuvreRepository $oeuvreRepository): Response
    {   
        $oeuvres = $oeuvreRepository->findAll();

        $res = json_decode(json_encode($oeuvres));
 
        return $this->json($oeuvres);
    }

    #[Route('/admin/oeuvre/new', name: 'oeuvre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SluggerInterface   $sluggerInterface): Response
    {
        $oeuvre = new Oeuvre();
        $form = $this->createForm(OeuvreType::class, $oeuvre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $oeuvre = $form->getData();
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
                $oeuvre->setImage($newFile);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($oeuvre);
            $entityManager->flush();

            return $this->redirectToRoute('oeuvre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/oeuvre/new.html.twig', [
            'oeuvre' => $oeuvre,
            'form' => $form,
        ]);
    }

    #[Route('admin/oeuvre/{id}', name: 'oeuvre_show', methods: ['GET'])]
    public function show(Oeuvre $oeuvre): Response
    {
        return $this->render('admin/oeuvre/show.html.twig', [
            'oeuvre' => $oeuvre,
        ]);
    }

    #[Route('/oeuvre/{id}', name: 'oeuvre_json_show', methods: ['GET'])]
    public function showJson(Oeuvre $oeuvre): Response
    {

        $res = json_decode(json_encode($oeuvre));
 
        return $this->json($oeuvre);
    }

    #[Route('/admin/oeuvre/{id}/edit', name: 'oeuvre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Oeuvre $oeuvre, SluggerInterface $sluggerInterface): Response
    {
        $form = $this->createForm(OeuvreType::class, $oeuvre);
        $previousImage = $oeuvre->getImage();
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
                $oeuvre->setImage($newFile);
            }

            $this->getDoctrine()->getManager()->flush();
            $newImage = $oeuvre->getImage();

            if ($previousImage != $newImage && $previousImage !="") {
                $racine = $this->getParameter('files');
                // dd($this->getParameter('files'));
                $completePath = $racine .'/'. $previousImage;
                unlink($completePath);
            }

            return $this->redirectToRoute('admin/oeuvre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/oeuvre/edit.html.twig', [
            'oeuvre' => $oeuvre,
            'form' => $form,
        ]);
    }

    #[Route('/admin/oeuvre/{id}', name: 'oeuvre_delete', methods: ['POST'])]
    public function delete(Request $request, Oeuvre $oeuvre): Response
    {
        if ($this->isCsrfTokenValid('delete'.$oeuvre->getId(), $request->request->get('_token'))) {
            $completePath = $this->getParameter('files').'/'. $oeuvre->getImage();

            if (is_file($completePath)) {

                unlink($completePath);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($oeuvre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('oeuvre_index', [], Response::HTTP_SEE_OTHER);
    }
}
