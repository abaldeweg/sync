<?php

namespace App\Controller;

use App\Entity\Item;
use App\Form\ItemType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/item")
 */
class ItemController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function index(): JsonResponse
    {
        return $this->json(
            $this->getDoctrine()->getRepository(Item::class)->findByUser(
                $this->getUser(),
            ),
        );
    }

    /**
     * @Route("/{id}", methods={"GET"})
     * @Security("is_granted('ROLE_USER') and item.getUser() === user")
     */
    public function show(Item $item): JsonResponse
    {
        return $this->json($item);
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
            json_decode(
                $request->getContent(),
                true
            )
        );
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();

            return $this->json($item);
        }

        return $this->json([
            'msg' => 'Please enter a valid item!',
        ], 400);
    }

    /**
     * @Route("/{id}", methods={"PUT"})
     * @Security("is_granted('ROLE_USER') and item.getUser() === user")
     */
    public function edit(Request $request, Item $item): JsonResponse
    {
        $form = $this->createForm(ItemType::class, $item);

        $form->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->json($item);
        }

        return $this->json([
            'msg' => 'Please enter a valid item!',
        ]);
    }

    /**
     * @Route("/{id}", methods={"DELETE"})
     * @Security("is_granted('ROLE_USER') and item.getUser() === user")
     */
    public function delete(Item $item): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($item);
        $em->flush();

        return $this->json([
            'msg' => 'The item was deleted successfully.',
        ]);
    }
}
