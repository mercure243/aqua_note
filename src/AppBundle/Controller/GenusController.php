<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Genus;
use AppBundle\Entity\GenusNote;
use Doctrine\ORM\EntityRepository;
use AppBundle\Service\MarkdownTransformer;

class GenusController extends Controller
{
  /**
  * @Route("/genus/new")
  */
    public function newAction()
    {
      $genus = new Genus();
      $genus->setName('Octopus'.rand(1,100));
      $genus->setSubFamily('Octopodinae');
      $genus->setSpeciesCount(rand(100, 99999));

      $note = new GenusNote();
      $note->setUsername('AquaWeaver');
      $note->setUserAvatarFilename('ryan.jpeg');
      $note->setNote('I counted 8 legs... as they wrapped around me');
      $note->setCreatedAt(new \DateTime('-1 month'));
      $note->setGenus($genus);

      $em = $this->getDoctrine()->getManager();
      $em->persist($genus);
      $em->persist($note);
      $em->flush();

      return new Response('<html><body>Genus created</body></html>');
    }


    /**
    * @Route("/genus", name="genus_list")
    */
      public function listAction()
      {
          $em = $this->getDoctrine()->getManager();
          $genuses = $em->getRepository('AppBundle:Genus')->findAllPublishedOrderedByRecentlyActive();
          //dump($genuses);die;
          $gen = $em->getRepository('AppBundle:Genus');
          dump($gen);
          return $this->render('genus/list.html.twig', [
            'genuses' => $genuses
          ]);
          //return new Response('<html><body>Genu created</body></html>');
      }

    /**
    * @Route("/genus/{genusName}", name="genus_show")
    */
    public function showAction($genusName, MarkdownTransformer $markdownTransformer)
    {
        $funFact = 'Octopuses can change the color of their body in just *three-tenths* of a second!';
        $funFact = $this->get('markdown.parser')->transform($funFact);

        $em = $this->getDoctrine()->getManager();
        $genus = $em->getRepository('AppBundle:Genus')->findOneBy(['name' => $genusName]);

        if (!$genus) {
          throw $this->createNotFoundException('genus not found');
        }

        //$markdownParser = $this->get('app.markdown_transformer');
        //$markdownParser = $this->get(MarkdownTransformer::class);
        $funFact = $markdownTransformer->parse($genus->getFunFact());

        /*
        $recentNotes = $genus->getNotes()->filter(function(GenusNote $note) {
          return $note->getCreatedAt() > new \DateTime('-3 months');
        });
        */

        $recentNotes = $em->getRepository('AppBundle:GenusNote')->findAllRecentNotesForGenus($genus);

        /*
        $cache = $this->get('doctrine_cache.providers.my_markdown_cache');
        $key = md5($funFact);

        if ($cache->contains($key)) {
            $funFact = $cache->fetch($key);
        } else {
            sleep(1); // fake how slow this could be
            $funFact = $this->get('markdown.parser')
                ->transform($funFact);
            $cache->save($key, $funFact);
            }

        if ($cache->contains($key)) {
           $funFact = $cache->fetch($key);
       } else {
           sleep(1); // fake how slow this could be
           $funFact = $this->get('markdown.parser')
               ->transform($funFact);
           $cache->save($key, $funFact);
         }
         */

        return $this->render('genus/show.html.twig', array(
            'genus' => $genus,
            'recentNoteCount' => count($recentNotes),
            'funFact' => $funFact
));
    }

    /**
    * @Route("genus/{name}/notes", name="genus_show_notes")
    * @Method("GET")
    */
    public function getNotesAction(Genus $genus)
    {
      //dump($genus);
      $notes = [];
      foreach ($genus->getNotes() as $note) {
            //dump($note);
            $notes[] = [
                'id' => $note->getId(),
                'username' => $note->getUsername(),
                'avatarUri' => '/images/'.$note->getUserAvatarFilename(),
                'note' => $note->getNote(),
                'date' => $note->getCreatedAt()->format('M d, Y')
                ];
          }
      /*
      $notes = [
         ['id' => 1, 'username' => 'AquaPelham', 'avatarUri' => '/images/leanna.jpeg', 'note' => 'Octopus asked me a riddle, outsmarted me', 'date' => 'Dec. 10, 2015'],
         ['id' => 2, 'username' => 'AquaWeaver', 'avatarUri' => '/images/ryan.jpeg', 'note' => 'I counted 8 legs... as they wrapped around me', 'date' => 'Dec. 1, 2015'],
         ['id' => 3, 'username' => 'AquaPelham', 'avatarUri' => '/images/leanna.jpeg', 'note' => 'Inked!', 'date' => 'Aug. 20, 2015'],
        ];
      */

      $data = [
        'notes' => $notes,
      ];
      return new JsonResponse($data);
    }

}
