<?php

declare(strict_types=1);

namespace App\Match\UI\Controller\Admin;

use App\Match\Application\Facade\MatchFacade;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/import', name: 'admin_import_')]
final class ImportController extends AbstractController
{
    private const array COMPETITIONS = [
        'PL' => 'Premier League',
        'BL1' => 'Bundesliga',
        'PD' => 'La Liga',
        'SA' => 'Serie A',
    ];

    #[Route('', name: 'index')]
    public function index(): Response
    {
        return $this->render('admin/import/index.html.twig', [
            'competitions' => self::COMPETITIONS,
        ]);
    }

    #[Route('/run', name: 'run', methods: ['POST'])]
    public function run(Request $request, MatchFacade $facade): Response
    {
        $selected = $request->request->all('competitions');

        if (empty($selected)) {
            $this->addFlash('warning', 'No competitions selected.');

            return $this->redirectToRoute('admin_import_index');
        }

        $results = [];

        foreach ($selected as $code) {
            if (!\array_key_exists($code, self::COMPETITIONS)) {
                continue;
            }

            try {
                $result = $facade->importCompetition($code);
                $results[] = \sprintf(
                    '%s: %d teams, %d matches imported, %d updated',
                    $code,
                    $result->teamsImported,
                    $result->matchesImported,
                    $result->matchesUpdated,
                );
            } catch (\Throwable $e) {
                $this->addFlash('danger', \sprintf('Error importing %s: %s', $code, $e->getMessage()));
            }
        }

        if (!empty($results)) {
            $this->addFlash('success', 'Import completed: ' . implode(' | ', $results));
        }

        return $this->redirectToRoute('admin_import_index');
    }
}
