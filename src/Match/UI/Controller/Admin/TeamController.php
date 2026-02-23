<?php

declare(strict_types=1);

namespace App\Match\UI\Controller\Admin;

use App\Match\Application\Facade\MatchFacade;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/teams', name: 'admin_team_')]
final class TeamController extends AbstractController
{
    public function __construct(
        private readonly MatchFacade $facade,
    ) {
    }

    #[Route('', name: 'index')]
    public function index(): Response
    {
        return $this->render('admin/team/index.html.twig', [
            'teams' => $this->facade->getAllTeams(),
        ]);
    }

    #[Route('/{id}', name: 'show')]
    public function show(string $id): Response
    {
        $team = $this->facade->getTeam($id);

        if (null === $team) {
            throw $this->createNotFoundException('Team not found.');
        }

        return $this->render('admin/team/show.html.twig', [
            'team' => $team,
        ]);
    }
}
