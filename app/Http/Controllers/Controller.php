<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Database\Entities\Entity;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Database\Entities\MailChimp\MailChimpList;
use App\Database\Entities\MailChimp\MailChimpMember;

abstract class Controller extends BaseController
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $entityManager;

    /**
     * The unique id for the list on remote Mailchimp server
     * @var string
     */
    protected $mailChimpId;
       
    /**
     * Controller constructor.
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Return error JSON response.
     *
     * @param array|null $data
     * @param int|null $status
     * @param array|null $headers
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse(?array $data = null, ?int $status = null, ?array $headers = null): JsonResponse
    {
        return \response()->json($data ?? [], $status ?? 400, $headers ?? []);
    }

    /**
     * Get error response
     * @param string $type error type info, generally class name
     * @param null|string $idsDesc Description of list id or member id, or their combination
     * @return JsonResponse
     */
    protected function errorResponseByType(string $type, ?string $idsDesc = '') 
    {
        return $this->errorResponse(['message' => self::getErrorMessage($type, $idsDesc)], 400);
    }

    /**
     * Get error message
     * @param string $type error type info, generally class name
     * @param null|string $idsDesc Description of list id or member id, or their combinations
     * @return string
     */
    public static function getErrorMessage($type, $idsDesc) : string 
    {
        return \sprintf('%s not found [%s]', $type, $idsDesc);
    }

    /**
     * Get error message when member's email address is changed
     * @param string $org_email
     * @param string $new_email
     * @return string
     */
    public static function getErrorMessageEmailChanged(string $org_email, string $new_email) : string {
        return "Member Email address is not allowed to change by this endpoint. Original: $org_email; New: $new_email";
    }

    /**
     * Remove entity from database.
     *
     * @param \App\Database\Entities\Entity $entity
     *
     * @return void
     */
    protected function removeEntity(Entity $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    /**
     * Save entity into database.
     *
     * @param \App\Database\Entities\Entity $entity
     *
     * @return void
     */
    protected function saveEntity(Entity $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    /**
     * Return successful JSON response.
     *
     * @param array|null $data
     * @param array|null $headers
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successfulResponse(?array $data = null, ?array $headers = null): JsonResponse
    {
        return \response()->json($data ?? [], 200, $headers ?? []);
    }

    /**
     * @param string $listId
     * @return MailChimpList|null
     */
    protected function getListbyId(string $listId) : ?MailChimpList 
    {
        //IDE will have warning if directly return method call
        /** @var MailChimpList|null $list */
        $list = $this->entityManager->getRepository(MailChimpList::class)->find($listId);

        return $list;
    }

    /**
     * @return MailChimpList[]|null
     */
    protected function getLists() : ?array 
    {
        /** @var MailChimpList[]|null $lists */
        return $this->entityManager->getRepository(MailChimpList::class)->findAll();
    }

    /**
     * Get JSON response of list object
     * @param MailChimpList|null $list
     * @return JsonResponse
     */
    protected function getListResponse(?MailChimpList $list, string $listId) {
        return is_null($list) ? $this->errorList($listId) : $this->successfulResponse($list->toArray());
    }

    /**
     * Get JSON response of lists object
     * @param MailChimpList[]|null $lists
     * @return JsonResponse
     */
    protected function getListsResponse(?array $lists) {
        $listData = array();
        foreach ($lists as $list) {
            $listData[$list->getId()] = $list->toArray();
        }
        return is_null($lists) ? $this->errorLists() : $this->successfulResponse($listData);
    }

    /**
     * @param string $listId
     * @return JsonResponse
     */
    protected function errorList(string $listId): JsonResponse
    {
        return $this->errorResponseByType(MailChimpList::class, self::idsDesc($listId));
    }

    /**
     * @return JsonResponse
     */
    protected function errorLists(): JsonResponse
    {
        return $this->errorResponseByType(MailChimpList::class);
    }

    /**
     * @param string|null $listId
     * @param string|null $memberId
     * @return JsonResponse
     */
    protected function errorMember(?string $listId, ?string $memberId = null): JsonResponse
    {
        return $this->errorResponseByType(MailChimpMember::class, Controller::idsDesc($listId,$memberId));
    }

    /**
     * @param string|null $listId
     * @param string|null $memberId
     * @return JsonResponse
     */
    public function errorMailChimp(?string $listId, ?string $memberId = null): JsonResponse
    {
        /** @noinspection PhpUndefinedClassConstantInspection */
        return $this->errorResponseByType($this->getErrorMailChimp(), self::idsDesc($listId, $memberId));
    }

    /**
     * Get mailchimp id of the list.
     *
     * @return null|string
     */
    public function getMailChimpId(): ?string
    {
        return $this->mailChimpId;
    }

    /**
     * wrapper of getListbyId()
     * @param string $listId
     * @return string
     */
    public function getMailChimpIdByListId(string $listId): string 
    {
        /** @var MailChimpList $list */
        $list = $this->getListbyId($listId);
        return !empty($list) ? $list->getMailChimpId() : '';
    }

    /**
     * Generate description by combining IDs and their name
     * example: List Id:yxb|Member Id:hnq
     * @param null|string $listId List Id
     * @param null|string $memberId Member Id
     * @param null|string $mailchimpListId List Mailchimp Id
     * @param null|string $mailchimpMemberId Member Mailchimp Id
     * @return string
     */
    public static function idsDesc(?string $listId, ?string $memberId = null, ?string $mailchimpListId = null, ?string $mailchimpMemberId = null): string 
    {
        $ids = [];
        if (!empty($listId)) {
            $ids['List Id'] = $listId;
        }

        if (!empty($memberId)) {
            $ids['Member Id'] = $memberId;
        }

        if (!empty($mailchimpListId)) {
            $ids['List Mailchimp Id'] = $mailchimpListId;
        }

        if (!empty($mailchimpMemberId)) {
            $ids['Member Mailchimp Id'] = $mailchimpMemberId;
        }

        $idsDesc = '';
        foreach ($ids as $desc => $id) {
            $idsDesc = !empty($idsDesc) ? "{$idsDesc}|" : '';
            $idsDesc .= "{$desc}:{$id}";
        }
        return $idsDesc;
    }

    /**
     * Get constant ERROR_MAILCHIMP from child class
     * @return string
     */
    public function getErrorMailChimp() { 
        return ''; 
    }
}
