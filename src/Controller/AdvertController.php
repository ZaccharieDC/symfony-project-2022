<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Repository\AdvertRepository;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\WorkflowInterface;

#[Route('/admin/advert')]
class AdvertController extends AbstractController
{
    #[Route('/', name: 'app_advert_index', methods: ['GET'])]
    public function index(AdvertRepository $advertRepository, Request $request): Response
    {
        $queryBuilder = $advertRepository->createQueryBuilder('advert');

        $pager = new Pagerfanta(new QueryAdapter($queryBuilder));
        $pager->setMaxPerPage(30);
        $pager->setCurrentPage($request->get('page', 1));

        return $this->render('advert/index.html.twig', [
            'pager' => $pager,
        ]);
    }

    #[Route('/{id}', name: 'app_advert_show', methods: ['GET'])]
    public function show(Advert $advert): Response
    {
        return $this->render('advert/show.html.twig', [
            'advert' => $advert,
        ]);
    }

    #[Route('/{id}/publish', name: 'app_advert_publish')]
    public function publish(Advert $advert, WorkflowInterface $advertPublishingStateMachine, AdvertRepository $advertRepository): RedirectResponse
    {
        $advertPublishingStateMachine->apply($advert, 'publish');
        $advert->setPublishedAt(new \DateTimeImmutable());
        $advertRepository->save($advert, true);

        return $this->redirectToRoute('app_advert_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/reject', name: 'app_advert_reject')]
    public function reject(Advert $advert, WorkflowInterface $advertPublishingStateMachine, AdvertRepository $advertRepository): RedirectResponse
    {
        $advertPublishingStateMachine->apply($advert, 'reject');
        $advertRepository->save($advert, true);

        return $this->redirectToRoute('app_advert_index', [], Response::HTTP_SEE_OTHER);
    }
}
