<?php

namespace App\Controller;

use App\Entity\AdminUser;
use App\Form\AdminUserType;
use App\Repository\AdminUserRepository;
use App\Security\AdminUserVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/admin/user')]
class AdminUserController extends AbstractController
{
    #[Route('/', name: 'app_admin_user_index', methods: ['GET'])]
    public function index(AdminUserRepository $adminUserRepository): Response
    {
        return $this->render('admin_user/index.html.twig', [
            'admin_users' => $adminUserRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AdminUserRepository $adminUserRepository): Response
    {
        $adminUser = new AdminUser();
        $form = $this->createForm(AdminUserType::class, $adminUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adminUserRepository->save($adminUser, true);

            $this->addFlash('success', 'User created successfully');

            return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_user/new.html.twig', [
            'admin_user' => $adminUser,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_user_show', methods: ['GET'])]
    public function show(AdminUser $adminUser): Response
    {
        return $this->render('admin_user/show.html.twig', [
            'admin_user' => $adminUser,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AdminUser $adminUser, AdminUserRepository $adminUserRepository): Response
    {
        $form = $this->createForm(AdminUserType::class, $adminUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adminUser->setUpdatedAt(new \DateTimeImmutable());
            $adminUserRepository->save($adminUser, true);

            $this->addFlash('success', 'User updated successfully');

            return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_user/edit.html.twig', [
            'admin_user' => $adminUser,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, AdminUser $adminUser, AdminUserRepository $adminUserRepository, Security $security): Response
    {
        if ($this->isCsrfTokenValid('delete'.$adminUser->getId(), $request->request->get('_token'))) {
            if (!$security->isGranted(AdminUserVoter::DELETE, $adminUser)) {
                throw new AccessDeniedHttpException();
            }

            $adminUserRepository->remove($adminUser, true);

            $this->addFlash('success', 'User deleted successfully');
        }

        return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
