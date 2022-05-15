<?php

namespace App\Controller;

use App\Composer\HistoryResponseComposer;
use App\Repository\ActionRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_USER")
 * @Route("/internal-api/history")
 */
class HistoryController extends AbstractController
{
    private HistoryResponseComposer $historyResponseComposer;
    private ActionRepository $actionRepository;

    public function __construct(
        HistoryResponseComposer $historyResponseComposer,
        ActionRepository $actionRepository
    ) {
        $this->historyResponseComposer = $historyResponseComposer;
        $this->actionRepository = $actionRepository;
    }

    /**
     * @Route("", name="app_api_history", methods={"GET"})
     */
    public function init(): JsonResponse
    {
        $actions = $this->actionRepository->findByUser($this->getUser());
        return $this->historyResponseComposer->composeListResponse($this->getUser(), $actions);
    }
}
