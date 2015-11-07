<?php
/**
 * Project: zaralab
 * Filename: MemberController.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 05.11.15
 */

namespace Zaralab\Controller\Api;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zaralab\Entity\Member;
use Zaralab\Exception\NotFoundException;
use Zaralab\Framework\Controller\ApplicationController;
use Zaralab\Service\MemberManager;

/**
 * Class MemberController
 */
class MemberController extends ApplicationController
{
    /**
     * List members action - 'api_member_list' name
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     *
     * @return \Psr\Http\Message\MessageInterface
     */
    public function listAction(ServerRequestInterface $request, ResponseInterface $response)
    {
        /** @var MemberManager $manager */
        $manager = $this->get('member.manager');
        /** @var Serializer $serializer */
        $serializer = $this->get('serializer');
        $context = SerializationContext::create()->setGroups(array('Default'));

        $members = $manager->getAllByActive();

        return $this->jsonResponse($response)
            ->write($serializer->serialize($members, 'json', $context));
    }

    /**
     * Show member action - 'api_member_show'
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param int $id member id
     *
     * @return \Psr\Http\Message\MessageInterface
     *
     * @throws NotFoundException
     */
    public function showAction(ServerRequestInterface $request, ResponseInterface $response, $id)
    {
        /** @var MemberManager $manager */
        $manager = $this->get('member.manager');
        /** @var Serializer $serializer */
        $serializer = $this->get('serializer');

        $member = $manager->getByActive($id);

        if (!$member) {
            $this->getMonolog()->warning('Member not found', ['id' => $id]);
            throw new NotFoundException('Member not found');
        }
        $context = SerializationContext::create()->setGroups(array('Default'));

        return $this->jsonResponse($response)
            ->write($serializer->serialize($member, 'json', $context));
    }

    /**
     * Create member action - 'api_member_create'
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     *
     * @return \Psr\Http\Message\MessageInterface
     */
    public function createAction(ServerRequestInterface $request, ResponseInterface $response)
    {
        // Under Construction
    }

    /**
     * Update member action - 'api_member_update'
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param $id member id
     *
     * @return \Psr\Http\Message\MessageInterface
     *
     * @throws NotFoundException
     */
    public function updateAction(ServerRequestInterface $request, ResponseInterface $response, $id)
    {
        // Under Construction
    }

    /**
     * Patch member action - 'api_member_patch'
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param $id member id
     *
     * @return \Psr\Http\Message\MessageInterface
     *
     * @throws NotFoundException
     */
    public function patchAction(ServerRequestInterface $request, ResponseInterface $response, $id)
    {
        // Under Construction
    }

    /**
     * Update member action - 'api_member_update_delete'
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param $id member id
     *
     * @return \Psr\Http\Message\MessageInterface
     *
     * @throws NotFoundException
     */
    public function deleteAction(ServerRequestInterface $request, ResponseInterface $response, $id)
    {
        // Under Construction
    }

}