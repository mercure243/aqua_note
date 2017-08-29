<?php
namespace AppBundle\Controller\Admin;
use AppBundle\Form\GenusFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Genus;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Service\MessageManager;

/**
 * @Route("/admin")
 * @Security("is_granted('ROLE_MANAGE_GENUS')")
 */
class GenusAdminController extends Controller
{
    /**
     * @Route("/genus", name="admin_genus_list")
     */
    public function indexAction()
    {
        //$this->denyAccessUnlessGranted('ROLE_ADMIN');
      /*
      if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
          throw $this->createAccessDeniedException('GET OUT!');
          }
        */
        /*
        $genuses = $this->getDoctrine()
            ->getRepository('AppBundle:Genus')
            ->findAll();
            */
        $em = $this->getDoctrine()->getManager();
        $genuses = $em->getRepository('AppBundle:Genus')
                      ->findAllPublishedOrderedByRecentlyActive();
        return $this->render('admin/genus/list.html.twig', array(
            'genuses' => $genuses
        ));
    }

    /**
     * @Route("/genus/new", name="admin_genus_new")
     */
    public function newAction(Request $request)
    {
        $form = $this->createForm(GenusFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $genus = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($genus);
            $em->flush();

        $this->addFlash(
              'success',
              sprintf('Genus created by you: %s!', $this->getUser()->getEmail())
              );
        return $this->redirectToRoute('admin_genus_list');
            //dump($form->getData());
            //die;
        }

        return $this->render('admin/genus/new.html.twig', [
            'genusForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/genus/{id}/edit", name="admin_genus_edit")
     */
    public function editAction(Request $request, Genus $genus, MessageManager $messageManager)
    {
        $form = $this->createForm(GenusFormType::class, $genus);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $genus = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($genus);
            $em->flush();

        $this->addFlash(
            'success',
            //$this->get('app.encouraging_message_generator')->getMessage()
            //$this->get(MessageManager::class)->getEncouragingMessage()
            $messageManager->getEncouragingMessage()
        );

        return $this->redirectToRoute('admin_genus_list', [
            'id' => $genus->getId()
        ]);
            //dump($form->getData());
            //die;
        } elseif ($form->isSubmitted()) {
            $this->addFlash(
                'error',
                //$this->get('app.discouraging_message_generator')->getMessage()
                //$this->get(MessageManager::class)->getDiscouragingMessage()
                $messageManager->getDiscouragingMessage()
            );
        }

        return $this->render('admin/genus/edit.html.twig', [
            'genusForm' => $form->createView()
        ]);
    }
}
