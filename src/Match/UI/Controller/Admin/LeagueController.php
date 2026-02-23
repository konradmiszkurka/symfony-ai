<?php

declare(strict_types=1);

namespace App\Match\UI\Controller\Admin;

use App\Match\Application\Facade\MatchFacade;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/leagues', name: 'admin_league_')]
final class LeagueController extends AbstractController
{
    public function __construct(
        private readonly MatchFacade $facade,
    ) {
    }

    #[Route('', name: 'index')]
    public function index(): Response
    {
        return $this->render('admin/league/index.html.twig', [
            'leagues' => $this->facade->getAllLeagues(),
        ]);
    }

    #[Route('/{id}', name: 'show')]
    public function show(string $id): Response
    {
        $league = $this->facade->getLeague($id);

        if (null === $league) {
            throw $this->createNotFoundException('League not found.');
        }

        $matches = $this->facade->getMatchesByFilters(leagueId: $id);

        return $this->render('admin/league/show.html.twig', [
            'league' => $league,
            'matches' => $matches,
        ]);
    }
}
