<?php

    namespace App\Controller;

    use App\Entity\User;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\Form\Extension\Core\Type\EmailType;
    use Symfony\Component\Form\Extension\Core\Type\PasswordType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Security\Core\User\UserInterface;
    use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
    use Symfony\Component\Validator\Constraints\Email;
    use Symfony\Component\Validator\Constraints\NotBlank;


    class SecurityController extends Controller
    {
        /**
         * @Route("/login", name="login")
         */
        public function login(Request $request, AuthenticationUtils $authUtils)
        {
            // get the login error if there is one
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_ANONYMOUSLY');
            $error = $authUtils->getLastAuthenticationError();

            return $this->render('security/login.html.twig', array(
                'error' => $error,
            ));
        }

        /**
         * @Route("/panel-admin", name="admin")
         */
        public function admin(UserInterface $user = null)
        {
            //Redirection vers 403 si celui qui accede a cette page n'a pas le role admin.
            //C'est une protection
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Accès interdit !');
            $user = $this->getUser();
            $name = $user->getUsername();

            return $this->render('admin/home.html.twig', [
                'user' => $name,
            ]);
        }

        /**
         * @Route("/panel-admin/new-session", name="admin_new-session")
         */
        public function adminSession()
        {
            //Redirection vers 403 si celui qui accede a cette page n'a pas le role admin.
            //C'est une protection
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Accès interdit !');
            return $this->render('admin/session.html.twig');
        }

        /**
         * @Route("/panel-admin/change-password", name="change_password")
         */
        public function changePassword(Request $request, UserInterface $user = null, UserPasswordEncoderInterface $encoder)
        {

            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Accès interdit !');
            $name = $user->getUsername();

            $form = $this->createFormBuilder()
                ->add('password', PasswordType::class, [
                    'label' => 'Ancien mdp : ',
                    'constraints' => [
                        new NotBlank(),
                    ],
                ])
                ->add('firstnewpassword', PasswordType::class, [
                    'label' => 'Nouveau mdp : ',
                    'constraints' => [
                        new NotBlank(),
                    ],
                ])
                ->add('secondnewpassword', PasswordType::class, [
                    'label' => 'Retaper le nouveau mdp : ',
                    'constraints' => [
                        new NotBlank(),
                    ],
                ])
                ->add('save', SubmitType::class, ['label' => 'Envoyer'])
                ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();

                if ($data['firstnewpassword'] == $data['secondnewpassword']){
                    $user = $this->getUser();
                    $newpass = $data['firstnewpassword'];
                    $encoded = $encoder->encodePassword($user, $newpass);
                    $user->setPassword($encoded);

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();

                    $change = 'exist';
                    return $this->render('formChangePassword/change-password.html.twig', [
                        'formChange' => $form->createView(),
                        'change' => $change,
                    ]);
                }
                else{
                    $notchange = 'exist';
                    return $this->render('formChangePassword/change-password.html.twig', [
                        'formChange' => $form->createView(),
                        'notchange' => $notchange,
                    ]);
                }
            }
            return $this->render('formChangePassword/change-password.html.twig', [
                'user' => $name,
                'formChange' => $form->createView(),
            ]);
        }

    }