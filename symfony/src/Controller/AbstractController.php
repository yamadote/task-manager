<?php

namespace App\Controller;

use App\Config\HeaderLinkConfig;
use App\Entity\HeaderLink;
use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AbstractController
 * @package App\Controller
 *
 * @method User getUser
 */
class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    protected const INDEX_ROUTE = 'app_index';

    private const LINK_REQUEST_FIELD = 'link';

    /** @var HeaderLinkConfig */
    private $headerLinkConfig;

    public function __construct(HeaderLinkConfig $headerLinkConfig)
    {
        $this->headerLinkConfig = $headerLinkConfig;
    }

    /**
     * @param Request $request
     * @return Response
     */
    protected function redirectBack(Request $request): Response
    {
        $referer = $request->server->get('HTTP_REFERER');
        if (!empty($referer)) {
            return $this->redirect($referer);
        }
        return $this->redirectToRoute(self::INDEX_ROUTE);
    }

    /**
     * @param HeaderLink $headerLink
     * @param Task|null $task
     * @return Response
     */
    protected function redirectToHeaderLink(HeaderLink $link, ?Task $task = null): Response
    {
        if ($link->hasParentLink() && !is_null($task) && !is_null($task->getParent())) {
            return $this->redirectToRoute($link->getParentRoute(), $link->getParentRouteParams($task->getParent()));
        }
        return $this->redirectToRoute($link->getRoute(), $link->getRouteParams());
    }

    /**
     * @param Request $request
     * @return HeaderLink
     */
    protected function getHeaderLinkFromRequest(Request $request): HeaderLink
    {
        $linkId = $request->get(self::LINK_REQUEST_FIELD);
        if (!$this->headerLinkConfig->isHeaderLinkIdExists($linkId)) {
            $linkId = $this->headerLinkConfig->getAllTasksLink();
        }
        return $this->headerLinkConfig->getLinkById($linkId);
    }
}
