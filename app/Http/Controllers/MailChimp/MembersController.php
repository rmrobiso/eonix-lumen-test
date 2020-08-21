<?php
declare(strict_types=1);

namespace App\Http\Controllers\MailChimp;

use App\Database\Entities\MailChimp\MailChimpMember;
use App\Database\Entities\MailChimp\MailChimpList;
use App\Http\Controllers\Controller;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mailchimp\Mailchimp;

class MembersController extends Controller
{
    /**
     * @var \Mailchimp\Mailchimp
     */
    private $mailChimp;

    /**
     * MembersController constructor.
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \Mailchimp\Mailchimp $mailchimp
     */
    public function __construct(EntityManagerInterface $entityManager, Mailchimp $mailchimp)
    {
        parent::__construct($entityManager);

        $this->mailChimp = $mailchimp;
    }

    /**
     * Create MailChimp member.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request, string $listId): JsonResponse
    {
        $list = $this->getListbyId($listId);
        if (is_null($list)) {
            return $this->errorList($listId);
        }

        $mailchimpId = $list->getMailChimpId();
        if (empty($mailchimpId)) {
            return $this->errorMailChimp($listId);
        }

        // Aggregate  data from request parameters
        $requestData = $request->all();

        //list id could be included in request body
        if (!isset($requestData['list_id']) || empty($requestData['list_id'])) {
            $requestData['list_id'] = $listId;
        }

        //vip is boolean while HTTP input is string
        if (array_key_exists('vip', $requestData)) {
            $requestData['vip'] = (bool)$requestData['vip'];
        }

        $errorResponse = $this->checkDuplicateEmail($listId, $requestData['email_address']);        
		if (!empty($errorResponse)) {
			return $errorResponse;
		}

        // Instantiate entity
        $member = new MailChimpMember($requestData);

        // Validate entity
        $mailchimpData = $member->toMailChimpArray();
        $validator = $this->getValidationFactory()->make($mailchimpData, $member->getValidationRules());

        if ($validator->fails()) {

            // Return error response if validation failed
            return $this->errorResponse([
                'message' => 'Invalid data given',
                'errors' => $validator->errors()->toArray()
            ]);
        }

        try {
            // Save List new member into MailChimp
            $response = $this->mailChimp->post("lists/{$mailchimpId}/members", $mailchimpData);

            // Set MailChimp id of the member
            $member->setMailChimpId($response->get('id'));

            // Save new List member into DB
            $this->saveEntity($member);

        } catch (Exception $exception) {

            // Return error response if something goes wrong
            return $this->errorResponse(['message' => $exception->getMessage()]);
        }

        return $this->successfulResponse($member->toArray());
    }    

    /**
     * Retrieve and return MailChimp List member.
     *
     * @param string $memberId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function showListMember(string $listId, string $memberId): JsonResponse
    {
        /** @var MailChimpList|null $list */
        $list = $this->getListbyId($listId);
        if (is_null($list)) {
            return $this->errorList($listId);
        }

        $members = $this->getMembers(['memberId' => $memberId, 'listId' => $listId]);
        if (empty($members)) {
            return $this->errorMember($listId,$memberId);
        }

        return $this->successfulResponse($members[0]->toArray());
    }

    /**
     * Retrieve and return MailChimp List members.
     *
     * @param string $listId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function showListMembers(string $listId): JsonResponse
    {
        /** @var MailChimpList|null $list */
        $list = $this->getListbyId($listId);
        if (is_null($list)) {
            return $this->errorList($listId);
        }

        /** @var MailChimpMember|null $members */
        $members = $this->entityManager->getRepository(MailChimpMember::class)->findBy(['listId' => $listId]);
        if ($members === null) {
            return $this->errorMember($listId);
        }

        $membersData = array();

        /** @var MailChimpMember $member */
        foreach ($members as $member) {
            $membersData[] = $member->toArray();
        }

        return $this->successfulResponse($membersData);
    }

    /**
     * Update MailChimp member.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $memberId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $listId, string $memberId): JsonResponse
    {
        /** @var MailChimpList|null $list */
        $list = $this->getListbyId($listId);
        if (is_null($list)) {
            return $this->errorList($listId);
        }

        $members = $this->getMembers(['memberId' => $memberId, 'listId' => $listId]);
        if (empty($members)) {
            return $this->errorMember($listId,$memberId);
        }

        $member = $members[0];
        $org_email = trim(strtolower($member->getEmailAddress()));
        $new_email = trim(strtolower($request->get('email_address') ?? ''));
        
        if ($new_email && $new_email != $org_email) {
            return $this->errorResponse(
                ['message' => $this->getErrorMessageEmailChanged($org_email, $new_email)],
                static::HTTP_STATUS_BAD_REQUEST
            );
        }

        // Update member properties
        $member->fill($request->all());

        // Validate entity
        $validator = $this->getValidationFactory()->make($member->toMailChimpArray(), $member->getValidationRules());

        if ($validator->fails()) {
            // Return error response if validation failed
            return $this->errorResponse([
                'message' => 'Invalid data given',
                'errors' => $validator->errors()->toArray()
            ]);
        }

        try {
            if (empty($list->getMailChimpId()) || empty($member->getMailChimpId())) {   
                return $this->errorMailChimp($listId, $memberId);
            }

            // Update member into MailChimp server ( PUT )
            $response = $this->mailChimp->put("lists/{$list->getMailChimpId()}/members/{$member->getMailChimpId()}", $member->toMailChimpArray());

            //Check for member ID on Mailchimp server, if changed, update the DB
            if ($response->get('id') != $member->getMailChimpId()) {
                $member->setMailChimpId($response->get('id'));
            }

            // Update member info into DB 
            $this->saveEntity($member);

        } catch (Exception $exception) {

            return $this->errorResponse(['message' => $exception->getMessage()]);
        }

        return $this->successfulResponse($member->toArray());
    }

    /**
     * Remove MailChimp member.
     *
     * @param string $memberId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove(string $listId, string $memberId): JsonResponse
    {
        /** @var MailChimpList|null $list */
        $list = $this->getListbyId($listId);
        if (is_null($list)) {
            return $this->errorList($listId);
        }

        $members = $this->getMembers(['memberId' => $memberId, 'listId' => $listId]);
        if (empty($members)) {
            return $this->errorMember($listId,$memberId);
        }

		$member = $members[0];
        try {
                if (empty($list->getMailChimpId()) || empty($member->getMailChimpId())) {

                return $this->errorMailChimp($listId, $memberId);
            }

            // Remove member from MailChimp ( DELETE )
            $this->mailChimp->delete(\sprintf('lists/%s/members/%s', $list->getMailChimpId(), $member->getMailChimpId()));

            // Delete member from the DB after successful removal from Mailchimp server
            $this->removeEntity($member);

        } catch (Exception $exception) {

            return $this->errorResponse(['message' => $exception->getMessage()]);
        }

        return $this->successfulResponse([]);
    }

	/**
	 * Check if Email is already existing
	 * @param string $email
	 * @return bool
	 */
    private function emailExisting(string $email): bool
	{
		return !empty($this->getMembers(['emailAddress' => $email]));
	}

	/**
	 * Validate duplicate Email and get error response
	 * @param string $listId
	 * @param string $emailAddress
	 * @return JsonResponse|null $errorResponse
	 */
	private function checkDuplicateEmail(string $listId, string $emailAddress): ?JsonResponse
	{
		$errorResponse = null;
		if ($this->emaiDuplicateByList($listId, $emailAddress)) {
			$errorResponse = $this->errorResponse(['message' => self::getEmailDuplicateError($emailAddress, $listId)],
				static::HTTP_STATUS_BAD_REQUEST
			);
		}
		
		return $errorResponse;
	}
	
	/**
	 * Check the existing member by criteria
	 * @param array $criteria
	 * @return MailChimpMember[]|null
	 */
	private function getMembers(array $criteria): ?array
	{
		/** 
		 * @var MailChimpMember[]|null $member
		 */
        $members = $this->entityManager->getRepository(MailChimpMember::class)->findBy($criteria);
        
		return $members;
	}

	/**
	 * Check if email exists under a list
	 * @param string $listId
	 * @param string $emailAddress
	 * @return bool
	 */
	private function emaiDuplicateByList(string $listId, string $emailAddress): bool
	{
		return count($this->getMembers(['listId' => $listId, 'emailAddress' => $emailAddress])) > 0;
	}

	/**
	 * Display error message for duplicate email address under one list
	 * @param string $emailAddress
	 * @param string $listId
	 * @return string
	 */
	public static function getEmailDuplicateError(string $emailAddress, string $listId): string
	{
		return "A list cannot have duplicate Emails address. [Email: $emailAddress] [List ID: $listId]";
	}

	/**
	 * Display error message when max number of signup has exceeded for the same email address
	 * @param string $emailAddress
	 * @return string
	 */
	public static function getEmailLimitExceededError(string $emailAddress): string
	{
		return "The maximum allowance has exceed for this Email address. [Email: $emailAddress]";
	}

}
