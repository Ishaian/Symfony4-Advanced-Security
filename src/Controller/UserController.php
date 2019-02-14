<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Services\MailManager;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    public function FlushUser($user)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
    }

    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_USER']);
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $this->FlushUser($user);

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            if ($form->get('password')->getData()) {
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }
        return $this->redirectToRoute('user_index');
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/{id}/desabled", name="user_desable")
     */
    public function AccDesable(User $user, \Swift_Mailer $mailer, MailManager $mailManager)
    {
        $user->setAccDesable(true);
        $user->removeRoles($user->getRoles())->setRoles(array('ROLE_DESABLE'));
        $this->FlushUser($user);

        $sender = $this->getParameter('mail_admin');
        $receiver = $user->getEmail();
        $subject = "Account Suppression";
        $content = $this->renderView('emails/delaccount.html.twig', ['user' => $user]);
        $mailManager->mailSender($mailer, $subject, $sender, $receiver, $content);

        return $this->redirectToRoute("app_logout");
        //TODO : Cron Configuration to delete in db all ROLE_DESABLE Account
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{id}/promote", name="user_to_admin")
     */
    public function UserToAdmin(User $user)
    {
        $user->removeRoles($user->getRoles())->setRoles(array('ROLE_ADMIN'));
        $this->FlushUser($user);
        return $this->redirectToRoute('user_index');
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{id}/demote", name="admin_to_user")
     */
    public function AdminToUser(User $user)
    {
        $user->removeRoles($user->getRoles())->setRoles(array('ROLE_USER'));
        $this->FlushUser($user);
        return $this->redirectToRoute('user_index');
    }
}
