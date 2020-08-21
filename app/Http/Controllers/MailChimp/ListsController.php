<?php
declare(strict_types=1);

namespace App\Http\Controllers\MailChimp;

use App\Database\Entities\MailChimp\MailChimpList;
use App\Http\Controllers\Controller;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mailchimp\Mailchimp;

class ListsController extends Controller
{
    /**
     * @var \Mailchimp\Mailchimp
     */
    private $mailChimp;

    /**
     * ListsController constructor.
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
     * Create MailChimp list.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        // Instantiate entity
        $list = new MailChimpList($request->all());

        // Validate entity
        $validator = $this->getValidationFactory()->make($list->toMailChimpArray(), $list->getValidationRules());

        if ($validator->fails()) {
            // Return error response if validation failed
            return $this->errorResponse([
                'message' => 'Invalid data given',
                'errors' => $validator->errors()->toArray()
            ]);
        }

        try {
            // Save list into db
            $this->saveEntity($list);

            // Save list into MailChimp
            $response = $this->mailChimp->post('lists', $list->toMailChimpArray());

            // Set MailChimp id on the list and save list into db
            $this->saveEntity($list->setMailChimpId($response->get('id')));

        } catch (Exception $exception) {
            // Return error response if something goes wrong
            return $this->errorResponse(['message' => $exception->getMessage()]);
        }

        return $this->successfulResponse($list->toArray());
    }

    /**
     * Retrieve and return MailChimp lists.
     * As per requirements, the list data is just retrieved from DB and no API call to MailChimp
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function showLists(): JsonResponse
    {
        return $this->getListsResponse($this->getLists());
    }

     /**
     * Retrieve and return MailChimp List by ID
     * As per requirements, the list data is just retrieved from DB and no API call to MailChimp
     *
     * @param string $listId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function showList(string $listId): JsonResponse
    {
        return $this->getListResponse($this->getListbyId($listId), $listId);
    }      

    /**
     * Update MailChimp list.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $listId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $listId): JsonResponse
    {
        /** @var MailChimpList|null $list */
        $list = $this->getListbyId($listId);
        
        if ($list === null) {
            return $this->errorResponse(
                ['message' => \sprintf('MailChimpList[%s] not found', $listId)],
                404
            );
        }

        // Update list properties
        $list->fill($request->all());

        // Validate entity
        $validator = $this->getValidationFactory()->make($list->toMailChimpArray(), $list->getValidationRules());

        if ($validator->fails()) {
            // Return error response if validation failed
            return $this->errorResponse([
                'message' => 'Invalid data given',
                'errors' => $validator->errors()->toArray()
            ]);
        }

        $mailchimpId = $list->getMailChimpId();

        if (empty($mailchimpId)) {
            return $this->errorMailChimp($listId);
        }

        try {
            // Update list into database
            $this->saveEntity($list);

            // Update list into MailChimp
            $this->mailChimp->patch(\sprintf('lists/%s', $mailchimpId), $list->toMailChimpArray());

        } catch (Exception $exception) {
            return $this->errorResponse(['message' => $exception->getMessage()]);
        }

        return $this->successfulResponse($list->toArray());
    }
    
    /**
     * Remove MailChimp list.
     *
     * @param string $listId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove(string $listId): JsonResponse
    {
        /** @var MailChimpList|null $list */
        $list = $this->getListbyId($listId);

        if (is_null($list)) {
            return $this->errorList($listId);
        }

        try {
            // Remove list from database
            $this->removeEntity($list);

            // Remove list from MailChimp
            $mailchimpId = $list->getMailChimpId();

            if (empty($mailchimpId)) {
                return $this->errorMailChimp($listId);
            }

            $this->mailChimp->delete(\sprintf('lists/%s', $mailchimpId));

        } catch (Exception $exception) {
            return $this->errorResponse(['message' => $exception->getMessage()]);
        }

        return $this->successfulResponse([]);
    } 
}
