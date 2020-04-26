<?php

namespace App\Controller;

use App\Service\EmailManager;
use App\Service\UnavailabilityManager;
use Doctrine\ORM\Configuration;
use App\Entity\Room;
use App\Form\RoomType;
use App\Provider\FeaturesProvider;
use App\Repository\RoomRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\LogicException;

class RoomController extends AbstractController
{
    /**
     * Liste de toutes les salles.
     * @Route("/liste-des-salles.html", name="room_index", methods={"GET"})
     * @IsGranted("ROLE_EMPLOYEE")
     * @param RoomRepository $roomRepository
     * @param FeaturesProvider $featuresProvider
     * @return Response
     */
    public function index(RoomRepository $roomRepository,
                          FeaturesProvider $featuresProvider): Response
    {
        $features = $featuresProvider->getFeatures();
        return $this->render('room/index.html.twig', [
            'rooms' => $roomRepository->findAll(),
            'features' => $features
        ]);
    }

    /**
     * Permet à l'admin de créer une nouvelle salle.
     * @Route("admin/nouvelle-salle.html", name="room_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $room = new Room();

        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        # Si le formulaire est soumis et qu'il est valide
        if ($form->isSubmitted() && $form->isValid()) {

            // Traitement de l'upload de l'image

            /** @var UploadedFile $picture */
            $picture = $room->getPicture();

            if (null !== $picture) {
                $fileName = 'salle-' . uniqid()
                    . '.' . $picture->guessExtension();

                try {
                    $picture->move(
                        $this->getParameter('rooms_assets_dir'),
                        $fileName
                    );
                } catch (FileException $e) {
                    // TODO: ??
                }

                $room->setPicture($fileName);

                try {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($room);
                    $em->flush();

                    $this->addFlash('notice',"La salle a été enregistrée.");
                    return $this->redirectToRoute('room_index');

                } catch (LogicException $e) {
                    $this->addFlash('error',
                        $e->getMessage());
                }

            } else {
                $this->addFlash('error',
                    "N'oubliez pas de choisir une image d'illustration");
            }
        }

        return $this->render('room/new.html.twig', [
            'room' => $room,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Affiche les caractéristiques d'une salle.
     * @Route("/salle-{id}.html", name="room_show", methods={"GET"})
     * @Security("room != null and room.getDeletedAt() == null", statusCode=404,
     *     message="Cette salle n'existe plus ou n'a jamais existé.")
     * @IsGranted("ROLE_GUEST")
     * @param Room $room
     * @return Response
     */
    public function show(Room $room = null): Response
    {
        return $this->render('room/show.html.twig', [
            'room' => $room
        ]);
    }

    /**
     * Permet à l'admin de modifier les caractéristiques d'une salle.
     * @Route("/admin/modifier/salle-{id}.html",
     *     name="room_edit",
     *     methods={"GET","POST"}))
     * @Security("room != null and room.getDeletedAt() == null", statusCode=404,
     *     message="Cette salle n'existe plus ou n'a jamais existé.")
     * @param Request $request
     * @param Room $room
     * @param Packages $packages
     * @return Response
     */
    public function edit(Request $request,
                         Room $room,
                         Packages $packages): Response
    {
        $options = [
            'image_url' => $packages->getUrl('images/room/'
                . $room->getPicture())
        ];

        $pictureName = $room->getPicture();

        try {
            $room->setPicture(
                new File($this->getParameter('rooms_assets_dir')
                    . '/' . $pictureName)
            );
        } catch (\Exception $e) {

        }


        $form = $this->createForm(RoomType::class, $room, $options)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $picture */
            $picture = $room->getPicture();

            if (null !== $picture) {

                $fileName = 'salle-' . uniqid()
                    . '.' . $picture->guessExtension();

                try {
                    $picture->move(
                        $this->getParameter('rooms_assets_dir'),
                        $fileName
                    );
                } catch (FileException $e) {

                }

                $room->setPicture($fileName);

            } else {
                $room->setPicture($pictureName);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($room);
            $em->flush();

            $this->addFlash('notice',"La salle a été mise à jour.");
            return $this->redirectToRoute('room_index');

        }

        return $this->render('room/edit.html.twig', [
            'form' => $form->createView(),
            'room' => $room
        ]);
    }

    /**
     * Permet à l'admin de supprimer une salle.
     * @Route("/admin/supprimer/salle-{id}.html", name="room_delete", methods={"DELETE"})
     * @Security("room != null and room.getDeletedAt() == null", statusCode=404, message="Cette salle n'existe plus ou n'a jamais existé.")
     * @param Request $request
     * @param Room $room
     * @param UnavailabilityManager $unavailabilityManager
     * @param \Swift_Mailer $mailer
     * @param EmailManager $emailManager
     * @return Response
     */
    public function delete(Request $request,
                           Room $room,
                           UnavailabilityManager $unavailabilityManager,
                           \Swift_Mailer $mailer,
                           EmailManager $emailManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$room->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->getFilters()->enable('softdeleteable');

            // Si l'utilisateur est l'organisateur de réunions à venir, on supprime ces réunions.
            if ($room->hasUpcomingUnavailabilities()) {
                //                                          /!\ Modal de confirmation
                $unavailabilityManager->deleteUpcomingUnavailabilityByRoom($emailManager, $room, $mailer);
            }

            $entityManager->remove($room);
            $entityManager->flush();

            if (empty($room->getUnavailabilities())) {
                // Si aucune réunion n'est organisée dans la salle, on la supprime.
                $entityManager->remove($room);
                $entityManager->flush();
            } else {
                $entityManager->persist($room);
                $entityManager->flush();
            }
        }
        $this->addFlash('notice',"La salle a été suppirmée.");

        return $this->redirectToRoute('room_index');
    }
}
