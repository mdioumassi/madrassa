<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\EditProfileType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UsersController extends AbstractController
{
    /**
     * @Route("/users", name="users-profil")
     */
    public function index()
    {
        return $this->render('users/index.html.twig');
    }

    /**
     * @Route("/users/profil/edit", name="edit-profile-user")
     * @param Request $request
     */
    public function editProfile(Request $request, EntityManagerInterface $manager)
    {
        $user = $this->getUser();
        $form = $this->createForm(EditProfileType::class, $this->getUser());
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user);
            $manager->flush();
            $this->addFlash('message', 'Profil mis à jour');
            return $this->redirectToRoute('users-profil');
        }

        return $this->render('users/editprofile.html.twig', [
           'form' => $form->createView()
        ]);
    }

    /**
     *
     * @Route("/users/password/edti", name="users_passwd_edit")
     */
    public function editpas(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $manager)
    {
        if($request->isMethod('POST')) {
            $user = $this->getUser();

            //On vérifie si les 2 mots de passe sont identiques
            if($request->request->get('pass') == $request->request->get('pass2')) {
                $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('pass')));
                $manager->flush();
                $this->addFlash('message', 'Mot de passe mis à jour avec succés');
                return $this->redirectToRoute('users-profil');
            } else {
                $this->addFlash('error', 'Les deux mots de passe  ne sont pas identiques');
            }
        }
        return $this->render('users/editpass.html.twig');
    }
}
