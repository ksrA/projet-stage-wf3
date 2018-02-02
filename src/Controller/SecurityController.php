<?php

    namespace App\Controller;

    use App\Entity\User;
    use Symfony\Bridge\Doctrine\Form\Type\EntityType;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
    use Symfony\Component\Form\Extension\Core\Type\EmailType;
    use Symfony\Component\Form\Extension\Core\Type\PasswordType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
    use Symfony\Component\Security\Core\User\UserInterface;
    use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
    use Symfony\Component\Validator\Constraints\Email;
    use Symfony\Component\Validator\Constraints\Length;
    use Symfony\Component\Validator\Constraints\NotBlank;
    use Symfony\Component\Validator\Constraints\Regex;


    class SecurityController extends Controller
    {
        /**
         * @Route("/test-user", name="security_register_user")
         */
        public function registerUser(UserPasswordEncoderInterface $encoder)
        {
            $user = new User();

            $pass = 'flo';
            $passHash = $encoder->encodePassword($user, $pass); // password_hash
            $user->setPassword($passHash);
            $user->setUsername('flo');
            $user->setEmail('florentbarthelemy1@gmail.com');

            //Génération de 10 bytes aléatoires puis transformation en chaine hexadecimale
            $bytes = openssl_random_pseudo_bytes(30);
            $resethash = bin2hex($bytes);
            /*
            $str = $encoder->encodePassword($user, 'tag@gmail.com'); // password_hash
            $str = 'zd/k' . $str;
            $resethash = str_replace('/', '8r', $str);
*/
            $user->setResetHash($resethash);
            $user->setRoles(['ROLE_SUPERADMIN']);

            $em = $this->getDoctrine()->getManager();

            $em->persist($user); // INSERT INTO app_user
            $em->flush();

            return new Response('Utilisateur enregistré');
        }

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
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
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
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
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

        /**
         * @Route("/panel-admin/create-user", name="create_user")
         */
        public function createUser(Request $request, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer)
        {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $this->denyAccessUnlessGranted('ROLE_SUPERADMIN', null, 'Accès interdit !');
            $form = $this->createFormBuilder()
                ->add('username', TextType::class, [
                    'label' => 'User : ',
                    'constraints' => [
                        new NotBlank(),
                        new Length(['min' => 2, 'max' => 30]),
                    ],
                ])
                ->add('email', EmailType::class, [
                    'label' => 'Adresse mail : ',
                    'constraints' => [
                        new NotBlank(),
                        new Email([
                            "message" => "Veuilliez saisir une adresse mail valide",
                        ]),
                        new Regex('/^[a-zA-Z0-9.!#$%&’*+=?^_`{|}~-]{1,80}@[a-zA-Z0-9-]{1,40}(?:\.[a-zA-Z0-9-]{1,10})*$/'),
                        new Length(['min' => 2, 'max' => 255]),
                    ]])
                ->add('role',   ChoiceType::class, [
                    'label' => 'Role : ',
                    'placeholder' => 'Choisir le role : ',
                    'choices' => [
                        'ROLE_ADMIN' => 'ROLE_ADMIN',
                        'ROLE_SUPERADMIN' => 'ROLE_SUPERADMIN',
                    ],
                ])
                ->add('save', SubmitType::class, ['label' => 'Créer nouvel utilisateur'])
                ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $repository = $this->getDoctrine()->getRepository(User::class);
                // Si l'adresse mail existe déjà en bdd
                if(($personBdd = $repository->findOneBy(['email' => $data['email']])) != null){
                    $inputError[] = "Adresse mail déjà utilisée";
                }
                if(($personBdd = $repository->findOneBy(['username' => $data['username']])) != null){
                    $inputError[] = "Nom d'utilisateur déjà utilisée";
                }
                if (!isset($inputError)){
                    $user = new User();

                    $pass = base64_encode(random_bytes(10));
                    $passHash = $encoder->encodePassword($user, $pass); // password_hash
                    $user->setPassword($passHash);

                    $user->setUsername($data['username']);
                    $user->setEmail($data['email']);

                    //encodage de la chaine reset_hash
                    $bytes = openssl_random_pseudo_bytes(30);
                    $resethash = bin2hex($bytes);

                    /*$str = $encoder->encodePassword($user, $data['email']); // password_hash
                    $str = 'zd/k' . $str;
                    $resethash = str_replace('/', '8r', $str);*/

                    $user->setResetHash($resethash);
                    //Ecrire une phrase dans la vue qui précise ce que fait le ROLE_ADMIN et le ROLE_SUPERADMIN
                    if ($data['role'] == "ROLE_SUPERADMIN"){
                        $user->setRoles(['ROLE_SUPERADMIN']);
                    }
                    elseif ($data['role'] == "ROLE_ADMIN") {
                        $user->setRoles(['ROLE_ADMIN']);
                    }
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user); // INSERT INTO app_user
                    $em->flush();

                    //Envoi du mail avec les infos du compte
                    $message = new \Swift_Message('Nouveau compte');
                    $message
                        ->setFrom('helloworldwf3@gmail.com')
                        ->setBody($this->renderView('email/body-create-user.html.twig', [
                            'email' => $data['email'],
                            'username' => $data['username'],
                            'password' => $pass,
                            'role' => $data['role'],
                        ]), 'text/html')
                        ->setTo($data['email']);

                    $mailer->send($message);

                    $formOk = 'exist';
                    return $this->render('formCreateAdmin/create-admin.html.twig', [
                        'formCreateAdmin' => $form->createView(),
                        'formOk' => $formOk,
                        'mdp' => $pass,
                        'resethash' => $resethash,
                    ]);
                }
                else{
                    $formNotOk = 'exist';
                    return $this->render('formCreateAdmin/create-admin.html.twig', [
                        'formCreateAdmin' => $form->createView(),
                        'formNotOk' => $formNotOk,
                        'inputError' => $inputError,
                    ]);
                }
            }
            return $this->render('formCreateAdmin/create-admin.html.twig', [
                'formCreateAdmin' => $form->createView(),
            ]);
        }

        /**
         * @Route("/login/forgot-password", name="forgot_password")
         */
        public function forgotPassword(Request $request, \Swift_Mailer $mailer)
        {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_ANONYMOUSLY');
            $form = $this->createFormBuilder()
                ->add('username', TextType::class, [
                    'label' => 'Nom d\'utilisateur',
                    'constraints' => [
                        new NotBlank(),
                        new Length(['min' => 2, 'max' => 30]),
                    ],
                ])
                ->add('email', EmailType::class, [
                    'label' => 'Adresse e-mail',
                    'constraints' => [
                        new NotBlank(),
                        new Email([
                            "message" => "Veuilliez saisir une adresse mail valide",
                        ]),
                        new Regex('/^[a-zA-Z0-9.!#$%&’*+=?^_`{|}~-]{1,80}@[a-zA-Z0-9-]{1,40}(?:\.[a-zA-Z0-9-]{1,10})*$/'),
                        new Length(['min' => 2, 'max' => 255]),
                    ]])
                ->add('save', SubmitType::class, ['label' => 'Recupérer le mot de passe'])
                ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $repository = $this->getDoctrine()->getRepository(User::class);

                // Si l'adresse mail existe déjà en bdd
                if (($personBdd = $repository->findOneBy(['email' => $data['email']])) != null) {
                    $findUserMail = "existe";
                }
                if (($personBdd = $repository->findOneBy(['username' => $data['username']])) != null) {
                    $findUserName = "existe";
                }
                if (!empty($findUserName) && !empty($findUserMail)) {

                    $personBdd = $repository->findOneBy(['username' => $data['username']]);
                    $resethash = $personBdd->getResetHash();

                    $url = $this->generateUrl(
                        'forgot_password_create_new',
                        array('slug' => 'form-new-password', 'username' => $personBdd->getUsername(), 'str' => $resethash),
                        UrlGeneratorInterface::ABSOLUTE_URL
                    );

                    $message = new \Swift_Message('Lien temporaire changement mdp');
                    $message
                        ->setFrom('helloworldwf3@gmail.com')
                        ->setBody($this->renderView('email/body-forgot-password.html.twig', [
                            'email' => $data['email'],
                            'username' => $data['username'],
                            'url' => $url,
                        ]), 'text/html')
                        ->setTo($data['email']);

                    $mailer->send($message);
                    //go lui envoyer un mail avec lien temporaire routé vers
                    //formulaire quui permet de changer mdp redéfinit en bdd
                    $change = 'exist';
                    return $this->render('formForgotPassword/ask-email.html.twig', [
                        'formChange' => $form->createView(),
                        'change' => $change,
                    ]);
                }
                else {
                    $notchange = 'exist';
                    $userNotFound = 'exist';
                    return $this->render('formForgotPassword/ask-email.html.twig', [
                        'askEmail' => $form->createView(),
                        'notchange' => $notchange,
                        'userNotFound' => $userNotFound,
                    ]);
                }
            }
            $notchange = 'exist';
            return $this->render('formForgotPassword/ask-email.html.twig', [
                'askEmail' => $form->createView(),
                'notchange' => $notchange,
            ]);
        }

        /**
         * @Route("/forgot-password/{slug}/{username}/{str}", name="forgot_password_create_new")
         */
        public function forgotPasswordCreateNew($slug, $username, $str, Request $request, UserPasswordEncoderInterface $encoder)
        {
            $repository = $this->getDoctrine()->getRepository(User::class);
            if (($user = $repository->findOneBy(['username' => $username])) != null) {
                $resethash = $user->getResetHash();

                if ($resethash == $str) {
                    $form = $this->createFormBuilder()
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

                        if ($data['firstnewpassword'] == $data['secondnewpassword']) {
                            $newpass = $data['firstnewpassword'];
                            $encoded = $encoder->encodePassword($user, $newpass);
                            $user->setPassword($encoded);

                            $bytes = openssl_random_pseudo_bytes(30);
                            $resethash = bin2hex($bytes);

                           /* $str = $encoder->encodePassword($user, $user->getEmail()); // password_hash
                            $str = 'l/k' . $str;
                            $resethash = str_replace('/', 'k6', $str);*/

                            $user->setResetHash($resethash);

                            $em = $this->getDoctrine()->getManager();
                            $em->persist($user);
                            $em->flush();

                            $change = 'exist';
                            return $this->render('formChangePassword/change-password.html.twig', [
                                'formChange' => $form->createView(),
                                'change' => $change,
                            ]);
                        } else {
                            $notchange = 'exist';
                            return $this->render('formChangePassword/change-password.html.twig', [
                                'formChange' => $form->createView(),
                                'notchange' => $notchange,
                            ]);
                        }
                    }
                    return $this->render('formChangePassword/change-password.html.twig', [
                        'formChange' => $form->createView(),
                    ]);
                } else {
                    // APPELER LA PAGE ERREUR 404
                    return new Response('<h1>ERREUR 404 a styliser</h1>');
                }
            }
            else {
               // APPELER LA PAGE ERREUR 404
                return new Response('<h1>ERREUR 404 a styliser</h1>');
            }
        }

        /**
         * @Route("/panel-admin/change-email", name="change_email")
         */
        public function changeEmail(Request $request, UserPasswordEncoderInterface $encoder)
        {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Accès interdit !');

            $form = $this->createFormBuilder()
                ->add('email', EmailType::class, [
                    'label' => 'Adresse mail actuelle : ',
                    'constraints' => [
                        new NotBlank(),
                        new Email([
                            "message" => "Veuilliez saisir une adresse mail valide",
                        ]),
                        new Regex('/^[a-zA-Z0-9.!#$%&’*+=?^_`{|}~-]{1,80}@[a-zA-Z0-9-]{1,40}(?:\.[a-zA-Z0-9-]{1,10})*$/'),
                        new Length(['min' => 2, 'max' => 255]),
                    ]])
                ->add('newemail', EmailType::class, [
                    'label' => 'Nouvelle adresse mail : ',
                    'constraints' => [
                        new NotBlank(),
                        new Email([
                            "message" => "Veuilliez saisir une adresse mail valide",
                        ]),
                        new Regex('/^[a-zA-Z0-9.!#$%&’*+=?^_`{|}~-]{1,80}@[a-zA-Z0-9-]{1,40}(?:\.[a-zA-Z0-9-]{1,10})*$/'),
                        new Length(['min' => 2, 'max' => 255]),
                    ]])
                ->add('save', SubmitType::class, ['label' => 'Changer adresse mail'])
                ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();

                $user = $this->getUser();
                $repository = $this->getDoctrine()->getRepository(User::class);
                if(($personBdd = $repository->findOneBy(['email' => $data['newemail']])) != null){
                    $inputError[] = "Un utilisateur possède déjà cette adresse mail";
                }
                if ($user->getEmail() != $data['email']){
                    $inputError[] = "Adresse actuelle mal saisie";
                }

                if ($user->getEmail() == $data['email'] && empty($inputError)){
                    $user->setEmail($data['newemail']);

                   /*$str = $encoder->encodePassword($user, $user->getEmail()); // password_hash
                    $str = 'l/b' . $str;
                    $resethash = str_replace('/', 'e4', $str);*/
                    $bytes = openssl_random_pseudo_bytes(30);
                    $resethash = bin2hex($bytes);

                    $user->setResetHash($resethash);

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user); // INSERT INTO app_user
                    $em->flush();

                    $change = "existe";
                    return $this->render('formChangeEmail/change-email.html.twig', [
                        'formChangeEmail' => $form->createView(),
                        'change' => $change,
                        'email' => $user->getEmail(),
                    ]);
                }
                else{
                    $notchange = "existe";
                    return $this->render('formChangeEmail/change-email.html.twig', [
                        'formChangeEmail' => $form->createView(),
                        'inputError' => $inputError,
                        'notchange' => $notchange,
                    ]);
                }
            }
            $notchange = "existe";
            return $this->render('formChangeEmail/change-email.html.twig', [
                'formChangeEmail' => $form->createView(),
                'notchange' => $notchange,
            ]);
        }

        /**
         * @Route("/panel-admin/delete-user", name="delete_user")
         */
        public function editUser(Request $request)
        {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $this->denyAccessUnlessGranted('ROLE_SUPERADMIN', null, 'Accès interdit !');

            $form = $this->createFormBuilder()
                ->add('username', EntityType::class, [
                    'class' => User::class,
                    'choice_label' => 'username',
                    'placeholder' => 'Choisir l\'utilisateur',
                ])
                ->add('save', SubmitType::class, ['label' => 'Supprimer l\'utilisateur'])
                ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()){
                $data = $form->getData();

                $repository = $this->getDoctrine()->getRepository(User::class);
                $personBdd = $repository->findOneBy(['username' => $data['username']->getUsername()]);

                if ($personBdd != null){
                    $em = $this->getDoctrine()->getManager();
                    $em->remove($personBdd);
                    $em->flush();

                    $change = "exist";
                    return $this->render('formDeleteUser/delete-user.html.twig', [
                        'formDeleteUser' => $form->createView(),
                        'change' => $change,
                    ]);
                }
                else {
                    $notchange = "exist";
                    $notFound = "exist";
                    return $this->render('formDeleteUser/delete-user.html.twig', [
                        'formDeleteUser' => $form->createView(),
                        'notchange' => $notchange,
                        'notfound' => $notFound,
                    ]);
                }
            }
            $notchange = "exist";
            return $this->render('formDeleteUser/delete-user.html.twig', [
                'formDeleteUser' => $form->createView(),
                'notchange' => $notchange,
            ]);
        }

        /**
         * @Route("/panel-admin/change-role", name="change_role")
         */
        public function changeRole(Request $request)
        {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $this->denyAccessUnlessGranted('ROLE_SUPERADMIN', null, 'Accès interdit !');

            $form = $this->createFormBuilder()
                ->add('username', EntityType::class, [
                    'class' => User::class,
                    'choice_label' => 'username',
                    'placeholder' => 'Choisir l\'utilisateur',
                ])
                ->add('role', ChoiceType::class, [
                    'placeholder' => 'Choisir le nouveau rôle : ',
                    'choices' => [
                        'ROLE_ADMIN' => 'ROLE_ADMIN',
                        'ROLE_SUPERADMIN' => 'ROLE_SUPERADMIN',
                    ]
                ])
                ->add('save', SubmitType::class, ['label' => 'Changer rôle de l\'utilisateur'])
                ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()){
                $data = $form->getData();

                $repository = $this->getDoctrine()->getRepository(User::class);
                $personBdd = $repository->findOneBy(['username' => $data['username']->getUsername()]);

                if ($data['role'] == "ROLE_SUPERADMIN"){
                    $personBdd->setRoles(['ROLE_SUPERADMIN']);
                }
                elseif ($data['role'] == "ROLE_ADMIN") {
                    $personBdd->setRoles(['ROLE_ADMIN']);
                }

                $em = $this->getDoctrine()->getManager();
                $em->flush();

                $change = "exist";
                return $this->render('formChangeRole/change-role.html.twig', [
                    'formChangeRole' => $form->createView(),
                    'role' => $personBdd->getRoles(),
                    'change' => $change,
                ]);
            }
            $notchange = "exist";
            return $this->render('formChangeRole/change-role.html.twig', [
                'formChangeRole' => $form->createView(),
                'notchange' => $notchange,

            ]);
        }
    }