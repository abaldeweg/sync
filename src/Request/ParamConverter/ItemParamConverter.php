<?php

namespace App\Request\ParamConverter;

use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Entity\Item;

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
            'name' => $request->attributes->get('item')
        ]);

        if (null === $object) {
            return false;
        }

        $request->attributes->set($name, $object);

        return true;
    }

    public function supports(ParamConverter $configuration): bool
    {
        if ($configuration->getClass() === Item::class && $configuration->getName() === 'item') {
            return true;
        }

        return false;
    }
}
