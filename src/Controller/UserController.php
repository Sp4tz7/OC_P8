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
    public function create(Request $request, UserPasswordEncoderInterface $encoder, UserManager $userManager)
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
            $user->setDateOfBirth($userManager->formatBirthDate($user->getDateOfBirth()));
            $user->setMobileNumber($userManager->formatMobileNumber($user->getMobileNumber()));
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
        if ('ROLE_ANONYMOUS' === $user->getRoles()[0]) {
            $this->addFlash('danger', "L'utilisateur Anonyme ne peut pas être modifié");

            return $this->redirectToRoute('user_list');
        }

        $form = $this->createForm(UserType::class, $user);
        $password = $user->getPassword();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->getPassword()) {
                $password = $encoder->encodePassword($user, $user->getPassword());
            }
            $user->setPassword($password);

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
            $user->setDateOfBirth($userManager->formatBirthDate($user->getDateOfBirth()));
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
    public function delete(User $user, EntityManagerInterface $entityManager, UserManager $userManager)
    {
        if (!$userManager->hasRightToDeleteUser($this->getUser(), $user)) {
            $this->addFlash('danger', "Vous n'avez pas le droit de supprimer cet utilisateur");

            return $this->redirectToRoute('user_list');
        }

        $entityManager->remove($user);
        $entityManager->flush();
        $this->addFlash('success', "L'utilisateur a bien été supprimé");

        return $this->redirectToRoute('user_list');
    }
}
