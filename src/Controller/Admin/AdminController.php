<?php

namespace App\Controller\Admin;

use App\Entity\Users;
use App\Form\EditUsersType;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="admin_")
 * Class AdminController
 * @package App\Admin\Controller
 */
class AdminController extends AbstractController
{
    /**
     *
     * @Route("/", name="admin")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/users", name="liste_users")
     * @param UsersRepository $usersRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function usersList(UsersRepository $usersRepository)
    {
        return $this->render('admin/users/list.html.twig', [
            'users' => $usersRepository->findAll()
        ]);
    }

    /**
     * @Route("/users/edit/{id}", name="edit_users")
     * @param Users $users
     * @param EntityManagerInterface $manager
     * @param Request $request
     */
    public function editUsers(Users $users, EntityManagerInterface $manager, Request $request)
    {
        $form  = $this->createForm(EditUsersType::class,  $users);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $manager->persist($users);
            $manager->flush();
            $this->addFlash('message', 'Les informations de l\'utilisateur sont mis à jour');
            return $this->redirectToRoute("admin_liste_users");
        }
        return $this->render('admin/users/edit.html.twig', [
           'form' => $form->createView()
        ]);
    }
}