<?php

namespace App\Controller;

use App\Composer\HistoryResponseComposer;
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

    public function __construct(HistoryResponseComposer $historyResponseComposer)
    {
        $this->historyResponseComposer = $historyResponseComposer;
    }

    /**
     * @Route("", name="app_api_history", methods={"GET"})
     */
    public function init(): JsonResponse
    {
        return $this->historyResponseComposer->composeListResponse($this->getUser());
    }
}
