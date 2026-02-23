<?php

declare(strict_types=1);

namespace App\Match\UI\Controller\Admin;

use App\Match\Application\Facade\MatchFacade;
use App\Match\Domain\ValueObject\MatchStatus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/matches', name: 'admin_match_')]
final class MatchController extends AbstractController
{
    public function __construct(
        private readonly MatchFacade $facade,
    ) {
    }

    #[Route('', name: 'index')]
    public function index(Request $request): Response
    {
        $leagueId = $request->query->get('league');
        $statusRaw = $request->query->get('status');
        $dateFrom = $request->query->get('date_from');
        $dateTo = $request->query->get('date_to');

        $status = null;
        if (null !== $statusRaw && '' !== $statusRaw) {
            $status = MatchStatus::tryFrom($statusRaw);
        }

        $validDateFrom = null;
        if (null !== $dateFrom && '' !== $dateFrom) {
            try {
                new \DateTimeImmutable($dateFrom);
                $validDateFrom = $dateFrom;
            } catch (\DateMalformedStringException) {
                // ignore invalid date
            }
        }

        $validDateTo = null;
        if (null !== $dateTo && '' !== $dateTo) {
            try {
                new \DateTimeImmutable($dateTo);
                $validDateTo = $dateTo;
            } catch (\DateMalformedStringException) {
                // ignore invalid date
            }
        }

        $matches = $this->facade->getMatchesByFilters(
            leagueId: $leagueId ?: null,
            status: $status?->value,
            dateFrom: $validDateFrom,
            dateTo: $validDateTo,
        );

        $leagues = $this->facade->getAllLeagues();

        if ($request->headers->has('Turbo-Frame')) {
            return $this->render('admin/match/_list.html.twig', [
                'matches' => $matches,
            ]);
        }

        return $this->render('admin/match/index.html.twig', [
            'matches' => $matches,
            'leagues' => $leagues,
            'selectedLeague' => $leagueId,
            'selectedStatus' => $statusRaw,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    #[Route('/{id}', name: 'show')]
    public function show(string $id): Response
    {
        $match = $this->facade->getMatch($id);

        if (null === $match) {
            throw $this->createNotFoundException('Match not found.');
        }

        return $this->render('admin/match/show.html.twig', [
            'match' => $match,
        ]);
    }
}
