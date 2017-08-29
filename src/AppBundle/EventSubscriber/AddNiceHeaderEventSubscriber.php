<?php
namespace AppBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Psr\Log\LoggerInterface;
use AppBundle\Service\MessageManager;

class AddNiceHeaderEventSubscriber implements EventSubscriberInterface
{
    private $logger;
    private $messageManager;
    private $showDiscouragingMessage;

    public function __construct(LoggerInterface $logger, MessageManager $messageManager, $showDiscouragingMessage)
    {
        $this->logger = $logger;
        $this->messageManager = $messageManager;
        $this->showDiscouragingMessage = $showDiscouragingMessage;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $this->logger->info('Adding a nice header!');

        $message = $this->showDiscouragingMessage
            ? $this->messageManager->getDiscouragingMessage()
            : $this->messageManager->getEncouragingMessage();
        $event->getResponse()
            ->headers->set('X_NICE_MESSAGE', $message);
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse'
        ];
    }
}
