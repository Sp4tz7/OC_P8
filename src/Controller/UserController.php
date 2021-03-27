<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Manager\UserManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/users", name="user_list")
     */
    public function list(): response
    {
        return $this->render(
            'user/list.html.twig',
            ['users' => $this->getDoctrine()->getRepository('App:User')->findAll()]
        );
    }

    /**
     * @Route("/users/create", name="user_create")
     */
    public function create(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $avatarFile = $form['avatar']->getData();
            if ($avatarFile) {
                $filename = md5($user->getEmail()).'.'.$avatarFile->guessExtension();
                $avatarFile->move(
                    $this->getParameter('app.user.avatar_dir'),
                    $filename
                );
                $user->setAvatar($filename);
            }

            $password      = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $user->setRoles($form->get('roles')->getData());
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', "L'utilisateur a bien été ajouté.");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/users/{id}/edit", name="user_edit")
     */
    public function edit(User $user, Request $request, UserPasswordEncoderInterface $encoder, CacheManager $imagineCacheManager, UserManager $userManager)
    {
        $form = $this->createForm(UserType::class, $user);
        $password = $user->getPassword();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($password);
            if ($user->getPassword()) {
                $password = $encoder->encodePassword($user, $user->getPassword());
                $user->setPassword($password);
            }

            $avatarFile = $form['avatar']->getData();
            if ($avatarFile) {
                $filename = md5($user->getEmail()).'.'.$avatarFile->guessExtension();
                $avatarFile->move(
                    $this->getParameter('app.user.avatar_dir'),
                    $filename
                );
                $user->setAvatar($filename);
                $imagineCacheManager->remove('img/profile/'.$filename);
            }

            $user->setMobileNumber($userManager->formatMobileNumber($user->getMobileNumber()));
            $user->setRoles($form->get('roles')->getData());
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }

    /**
     * @Route("/users/{id}/delete", name="user_delete")
     */
    public function delete(User $user, EntityManagerInterface $entityManager)
    {
        if ($user == $this->getUser()) {
            $this->addFlash('danger', "Vous ne pouvez pas vous supprimer vous-même!");

            return $this->redirectToRoute('user_list');
        }

        if ($user->getRoles()[0] == 'ROLE_ANONYMOUS') {
            $this->addFlash('danger', "Vous ne pouvez pas supprimer l'utilisateur Anonyme!");

            return $this->redirectToRoute('user_list');
        }

        $entityManager->remove($user);
        $entityManager->flush();
        $this->addFlash('success', "L'utilisateur a bien été supprimé");

        return $this->redirectToRoute('user_list');
    }
}
