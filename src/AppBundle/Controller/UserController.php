<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\UserRegistrationForm;
use AppBundle\Entity\User;
use AppBundle\Security\LoginFormAuthenticator;

class UserController extends Controller
{
    /**
    * @Route("/register", name="user_register")
    */
    public function registerAction(Request $request, LoginFormAuthenticator $authenticator)
    {
        $form = $this->createForm(UserRegistrationForm::class);

        $form->handleRequest($request);

        if ($form->isValid()) {
          $user = $form->getData();
          //dump($user);
          //die;

          $em = $this->getDoctrine()->getManager();
          $em->persist($user);
          $em->flush();

          $this->addFlash('success', 'Welcome '.$user->getEmail());

          //return $this->redirectToRoute('homepage');
          return $this->get('security.authentication.guard_handler')
                ->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $authenticator,
                    'main'
                );
        }

        return $this->render('user/register.html.twig', [
              'form' => $form->createView()
        ]);

    }
}
