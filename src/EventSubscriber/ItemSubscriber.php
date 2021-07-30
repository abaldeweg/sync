<?php

namespace App\EventSubscriber;

use App\Entity\Item;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ItemSubscriber implements EventSubscriberInterface
{
    public function __construct(private TokenStorageInterface $token, private EntityManagerInterface $em)
    {
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $item = $args->getObject();

        if (!$item instanceof Item) {
            return;
        }

        $item->setUser(
            $this->token->getToken()->getUser()
        );
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
        ];
    }
}
