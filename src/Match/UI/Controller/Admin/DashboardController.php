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
    #[Route('', name: 'dashboard')]
    public function index(MatchFacade $facade): Response
    {
        return $this->render('admin/dashboard/index.html.twig', [
            'leagueCount' => $facade->getLeagueCount(),
            'teamCount' => $facade->getTeamCount(),
            'matchCount' => $facade->getMatchCount(),
            'scheduledCount' => $facade->getMatchCountByStatus(MatchStatus::Scheduled),
            'finishedCount' => $facade->getMatchCountByStatus(MatchStatus::Finished),
            'inProgressCount' => $facade->getMatchCountByStatus(MatchStatus::InProgress),
            'latestMatches' => $facade->getLatestMatches(5),
        ]);
    }
}
