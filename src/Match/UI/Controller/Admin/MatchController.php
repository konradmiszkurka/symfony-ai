<?php

declare(strict_types=1);

namespace App\Match\UI\Controller\Admin;

use App\Match\Application\Facade\MatchFacade;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/matches', name: 'admin_match_')]
final class MatchController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(Request $request, MatchFacade $facade): Response
    {
        $leagueId = $request->query->get('league');
        $status = $request->query->get('status');
        $dateFrom = $request->query->get('date_from');
        $dateTo = $request->query->get('date_to');

        $matches = $facade->getMatchesByFilters(
            leagueId: $leagueId ?: null,
            status: $status ?: null,
            dateFrom: $dateFrom ?: null,
            dateTo: $dateTo ?: null,
        );

        $leagues = $facade->getAllLeagues();

        if ($request->headers->has('Turbo-Frame')) {
            return $this->render('admin/match/_list.html.twig', [
                'matches' => $matches,
            ]);
        }

        return $this->render('admin/match/index.html.twig', [
            'matches' => $matches,
            'leagues' => $leagues,
            'selectedLeague' => $leagueId,
            'selectedStatus' => $status,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    #[Route('/{id}', name: 'show')]
    public function show(string $id, MatchFacade $facade): Response
    {
        $match = $facade->getMatch($id);

        if (null === $match) {
            throw $this->createNotFoundException('Match not found.');
        }

        return $this->render('admin/match/show.html.twig', [
            'match' => $match,
        ]);
    }
}
