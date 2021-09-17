<?php

namespace App\Controller;

use App\Entity\Item;
use App\Form\ItemType;
use Baldeweg\Bundle\ApiBundle\AbstractApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/item")
 */
class ItemController extends AbstractApiController
{
    private $fields = ['id', 'name', 'body'];

    /**
     * @Route("/", methods={"GET"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function index(): JsonResponse
    {
        return $this->setResponse()->collection(
            $this->fields,
            $this->getDoctrine()->getRepository(Item::class)->findByUser(
                $this->getUser(),
            )
        );
    }

    /**
     * @Route("/{item}", methods={"GET"})
     * @Security("is_granted('ROLE_USER') and item.getUser() === user")
     */
    public function show(Item $item): JsonResponse
    {
        return $this->setResponse()->single($this->fields, $item);
    }

    /**
     * @Route("/new", methods={"POST"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function new(Request $request): JsonResponse
    {
        $item = new Item();
        $form = $this->createForm(ItemType::class, $item);

        $form->submit(
            $this->submitForm($request)
        );
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();

            return $this->setResponse()->single($this->fields, $item);
        }

        return $this->setResponse()->invalid();
    }

    /**
     * @Route("/{item}", methods={"PUT"})
     * @Security("is_granted('ROLE_USER') and item.getUser() === user")
     */
    public function edit(Request $request, Item $item): JsonResponse
    {
        $form = $this->createForm(ItemType::class, $item);

        $form->submit(
            $this->submitForm($request)
        );
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->setResponse()->single($this->fields, $item);
        }

        return $this->setResponse()->invalid();
    }

    /**
     * @Route("/{item}", methods={"DELETE"})
     * @Security("is_granted('ROLE_USER') and item.getUser() === user")
     */
    public function delete(Item $item): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($item);
        $em->flush();

        return $this->setResponse()->deleted();
    }
}
