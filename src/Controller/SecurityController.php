<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Throwable;

class SecurityController extends AbstractController
{
    /**
     * @Route("/signup", name="app_signup")
     */
    public function signup(Request $request, ManagerRegistry $doctrine)
    {
        if ($request->isMethod('POST')) {
            $repository = $doctrine->getRepository(User::class);
            $user = $repository->findOneBy(['email' => $request->request->get("email")]);

            if (!$user) {
                if ($request->request->get('password') == $request->request->get('password_confirmation')) {
                    try {
                        $user = new User;
                        $user->setEmail($request->request->get('email'));
                        // $plaintextPassword = $request->request->get("_password");
                        // $hashedPassword = $passwordHasher->hashPassword(
                        //     $user,
                        //     $plaintextPassword
                        // );
                        // $user->setPassword($hashedPassword);
                        $user->setRoles(['user']);
            
                        $entityManager = $doctrine->getManager();
                        $entityManager->persist($user);
                        $entityManager->flush();
                    } catch (Throwable $th) {
                        //throw $th;
                        $this->addFlash('error', 'Re-try again.');
                    }
                } else {
                    $this->addFlash('error', 'Password isn\'t the same.');
                }
            } else {
                $this->addFlash('error', 'Email already exists.');
            }
        }

        return $this->render('security/signup.html.twig', [
        ]);
    }

    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
