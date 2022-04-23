<?php

namespace App\Controller;

use App\Composer\SettingsResponseComposer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_USER")
 * @Route("/internal-api/settings")
 */
class SettingsController extends AbstractController
{
    private SettingsResponseComposer $settingsResponseComposer;

    public function __construct(SettingsResponseComposer $settingsResponseComposer)
    {
        $this->settingsResponseComposer = $settingsResponseComposer;
    }

    /**
     * @Route("", name="app_api_settings", methods={"GET"})
     */
    public function init(): JsonResponse
    {
        return $this->settingsResponseComposer->composeListResponse($this->getUser());
    }
}
