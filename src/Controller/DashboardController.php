<?php
/**
 * Created by PhpStorm.
 * User: remigrassian
 * Date: 2019-01-19
 * Time: 00:10
 */

namespace App\Controller;


use App\Entity\Unavailability;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class DashboardController extends AbstractController
{
    /**
     * @Route("/pagers/unavailabilitiesasorganiser/{page}", name="unavailability_as_organiser")
     * @param $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showUnavailabilitiesAsOrganiser($page)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $organiserQueryBuilder = $entityManager->createQueryBuilder()
            ->select('u')
            ->from(Unavailability::class, 'u')
            ->where('u.organiser = :organiser')
            ->setParameter('organiser', $this->getUser())
            ->orderBy('u.startDate', 'DESC');
        $organiserAdapter = new DoctrineORMAdapter($organiserQueryBuilder);
        $unavailabilitiesAsOrganiser_pagerfanta = new Pagerfanta($organiserAdapter);

        $unavailabilitiesAsOrganiserPaged = $unavailabilitiesAsOrganiser_pagerfanta
            ->setMaxPerPage(5)
            ->setCurrentPage($page)
            ->getCurrentPageResults();

        return $this->render('user/_dashboard_organiser.html.twig', [
            'unavailabilitiesAsOrganiser_pager' => $unavailabilitiesAsOrganiser_pagerfanta,
            'unavailabilitiesAsOrganiserPaged' => $unavailabilitiesAsOrganiserPaged
        ]);
    }

    /**
     * @Route("/pagers/unavailabilitiesasguest/{page}", name="unavailability_as_guest")
     * @param $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showUnavailabilitiesAsGuest($page)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $guestQueryBuilder = $entityManager->createQueryBuilder()
            ->select('u')
            ->from(Unavailability::class, 'u')
            ->join('u.guests', 'g')
            ->join('u.room', 'r')
            ->addSelect('r')
            ->where('g = :guest')
            ->setParameter('guest', $this->getUser())
            ->orderBy('u.startDate', 'DESC');
        $guestAdapter = new DoctrineORMAdapter($guestQueryBuilder);
        $unavailabilitiesAsGuest_pagerfanta = new Pagerfanta($guestAdapter);

        $unavailabilitiesAsGuestPaged = $unavailabilitiesAsGuest_pagerfanta
            ->setMaxPerPage(5)
            ->setCurrentPage($page)
            ->getCurrentPageResults();

        return $this->render('user/_dashboard_guest.html.twig', [
            'unavailabilitiesAsGuest_pager' => $unavailabilitiesAsGuest_pagerfanta,
            'unavailabilitiesAsGuestPaged' => $unavailabilitiesAsGuestPaged
        ]);
    }
}