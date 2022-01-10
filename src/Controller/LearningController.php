<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;

class LearningController extends AbstractController
{
    //instantiate requestStack for getting and setting session variables
    private $requestStack;
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function showMyName(Request $request): Response
    {
        $user = new User();
        $session = $this->requestStack->getSession();
        $name = $session->get('userName', 'unknown user');
        $user->setName($name);
        $form = $this->createForm(UserType::class,$user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            return $this->changeMyName($user);
        }

        return $this->renderForm('base.html.twig', [
            'page' => 'Home',
            'name' => $user->getName(),
            'form' => $form,
        ]);
    }

    //by adding methods=... I restrict access to this page to only POST methods
    /**
     * @route("/change", name="change", methods={"POST"})
     */
    public function changeMyName($user): Response
    {
        $session = $this->requestStack->getSession();
        // stores an attribute in the session for later reuse
        $session->set('userName', $user->getName());
        $this->render('learning/change.html.twig', [
            'name' => $session->get('userName'),
            'page' => 'Change',
        ]);

        return $this->redirectToRoute('homepage');
    }

    /**
     * @route("/about-me", name="about-me")
     */
    public function aboutme(): Response
    {
        $session = $this->requestStack->getSession();
        $name = $session->get('userName', 'unknown user');

        $ipsum = "But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes, or avoids pleasure itself, because it is pleasure, but because those who do not know how to pursue pleasure rationally encounter consequences that are extremely painful. Nor again is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but because occasionally circumstances occur in which toil and pain can procure him some great pleasure. To take a trivial example, which of us ever undertakes laborious physical exercise, except to obtain some advantage from it? But who has any right to find fault with a man who chooses to enjoy a pleasure that has no annoying consequences, or one who avoids a pain that produces no resultant pleasure?";
        return $this->render('learning/index.html.twig', [
            'name' => $name,
            'page' => 'About me',
            'text' => $ipsum,
        ]);
    }
}
