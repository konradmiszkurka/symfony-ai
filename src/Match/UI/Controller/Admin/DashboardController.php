<?php

declare(strict_types=1);

namespace App\Match\UI\Controller\Admin;

use App\Match\Application\Facade\MatchFacade;
use App\Match\Domain\ValueObject\MatchStatus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'admin_')]
final class DashboardController extends AbstractController
{
    public function __construct(
        private readonly MatchFacade $facade,
    ) {
    }

    #[Route('', name: 'dashboard')]
    public function index(): Response
    {
        return $this->render('admin/dashboard/index.html.twig', [
            'leagueCount' => $this->facade->getLeagueCount(),
            'teamCount' => $this->facade->getTeamCount(),
            'matchCount' => $this->facade->getMatchCount(),
            'scheduledCount' => $this->facade->getMatchCountByStatus(MatchStatus::Scheduled),
            'finishedCount' => $this->facade->getMatchCountByStatus(MatchStatus::Finished),
            'inProgressCount' => $this->facade->getMatchCountByStatus(MatchStatus::InProgress),
            'latestMatches' => $this->facade->getLatestMatches(5),
        ]);
    }
}
