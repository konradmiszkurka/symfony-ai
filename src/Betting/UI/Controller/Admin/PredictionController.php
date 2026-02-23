<?php

declare(strict_types=1);

namespace App\Betting\UI\Controller\Admin;

use App\Betting\Application\Facade\BettingFacade;
use App\Match\Application\Facade\MatchFacade;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/predictions', name: 'admin_prediction_')]
final class PredictionController extends AbstractController
{
    public function __construct(
        private readonly BettingFacade $bettingFacade,
        private readonly MatchFacade $matchFacade,
    ) {}

    #[Route('', name: 'index')]
    public function index(Request $request): Response
    {
        $leagueCode = $request->query->get('league_code');
        $predictions = $this->bettingFacade->listPredictions($leagueCode ?: null);
        $leagues = $this->matchFacade->getAllLeagues();

        return $this->render('admin/prediction/index.html.twig', [
            'predictions' => $predictions,
            'leagues' => $leagues,
            'selectedLeagueCode' => $leagueCode,
        ]);
    }

    #[Route('/generate-all', name: 'generate_all', methods: ['POST'])]
    public function generateAll(): Response
    {
        $count = $this->bettingFacade->generateAllPredictions();
        $this->addFlash('success', \sprintf('Generated %d predictions.', $count));

        return $this->redirectToRoute('admin_prediction_index');
    }

    #[Route('/generate-league/{leagueId}', name: 'generate_league', methods: ['POST'])]
    public function generateLeague(string $leagueId): Response
    {
        $count = $this->bettingFacade->generatePredictionsForLeague($leagueId);
        $this->addFlash('success', \sprintf('Generated %d predictions for league.', $count));

        return $this->redirectToRoute('admin_prediction_index');
    }

    #[Route('/{matchId}', name: 'show')]
    public function show(string $matchId): Response
    {
        $prediction = $this->bettingFacade->getPredictionForMatch($matchId);

        if (null === $prediction) {
            throw $this->createNotFoundException('Prediction not found for this match.');
        }

        return $this->render('admin/prediction/show.html.twig', [
            'prediction' => $prediction,
        ]);
    }
}
