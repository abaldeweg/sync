<?php

namespace App\Request\ParamConverter;

use App\Entity\Item;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ItemParamConverter implements ParamConverterInterface
{
    public function __construct(private TokenStorageInterface $token, private EntityManagerInterface $em)
    {
    }

    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $name = $configuration->getName();
        $object = $this->em->getRepository(Item::class)->findOneBy([
            'user' => $this->token->getToken()->getUser(),
            'name' => $request->attributes->get('item'),
        ]);

        if (null === $object) {
            return false;
        }

        $request->attributes->set($name, $object);

        return true;
    }

    public function supports(ParamConverter $configuration): bool
    {
        if (Item::class === $configuration->getClass() && 'item' === $configuration->getName()) {
            return true;
        }

        return false;
    }
}
