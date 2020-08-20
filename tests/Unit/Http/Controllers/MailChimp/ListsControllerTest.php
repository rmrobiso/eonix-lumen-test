<?php
declare(strict_types=1);

namespace Tests\App\Unit\Http\Controllers\MailChimp;

use App\Http\Controllers\MailChimp\ListsController;
use Tests\App\TestCases\MailChimp\ListTestCase;

class ListsControllerTest extends ListTestCase
{
    /**
     * Test controller returns error response when exception is thrown during create MailChimp request.
     *
     * @return void
     */
    public function testCreateListMailChimpException(): void
    {
        /** @noinspection PhpParamsInspection Mock given on purpose */
        $controller = new ListsController($this->entityManager, $this->mockMailChimpForException('post'));

        $this->assertMailChimpExceptionResponse($controller->create($this->getRequest(static::$listData)));
    }

    /**
     * Test controller returns error response when there's no Mailchimp ID during remove MailChimp list request.
     *
     * @return void
     */
    public function testRemoveListNoMailChimpError(): void
    {
        $list = $this->createList(static::$listData, false);
        // If there is no list id, skip
        if (!$this->validateList($list)) {
            return;
        }

        $listId = $list->getId();
        /** @noinspection PhpParamsInspection Mock given on purpose */
        $controller = new ListsController($this->entityManager, $this->mockMailChimpForError('delete'));
        $this->assertMailChimpErrorResponse($controller->remove($listId), $listId);
    }

    /**
     * Test controller returns error response when exception is thrown by MailChimp server during sending remove request to MailChimp server.
     *
     * @return void
     */
    public function testRemoveListMailChimpException(): void
    {
        $list = $this->createList(static::$listData, true);
        // If there is no list id, skip
        if (!$this->validateList($list)) {
            return;
        }

        /** @noinspection PhpParamsInspection Mock given on purpose */
        $controller = new ListsController($this->entityManager, $this->mockMailChimpForException('delete'));
        $this->assertMailChimpExceptionResponse($controller->remove($list->getId()));
    }

    /**
     * Test controller returns error 404 response when making remove MailChimp request without providing Mailchimp ID.
     *
     * @return void
     */
    public function testRemoveListMailChimpNotFound(): void
    {
        $list = $this->createList(static::$listData, false);
        // If there is no list id, skip
        if (!$this->validateList($list)) {
            return;
        }

        $listId = $list->getId();

        /** @noinspection PhpParamsInspection Mock given on purpose */
        $controller = new ListsController($this->entityManager, $this->mockMailChimp());
        $this->assertMailChimpNotFoundResponse($controller->remove($listId), $this->getMailChimpResponseError($listId));
    }

    /**
     * Test controller returns success response when making remove MailChimp request.
     *
     * @return void
     */
    public function testRemoveListMailChimpSuccess(): void
    {
        $list = $this->createList(static::$listData, true);
        // If there is no list id, skip
        if (!$this->validateList($list)) {
            return;
        }

        $listId = $list->getId();

        /** @noinspection PhpParamsInspection Mock given on purpose */
        $controller = new ListsController($this->entityManager, $this->mockMailChimpSuccess('delete'));
        $this->assertRemoveSuccessResponse($controller->remove($listId));
    }

    /**
     * Test controller returns error response when exception is thrown during update MailChimp request.
     *
     * @return void
     */
    public function testUpdateListMailChimpException(): void
    {
        $list = $this->createList(static::$listData, true);
        // If there is no list id, skip
        if (!$this->validateList($list)) {
            return;
        }

        /** @noinspection PhpParamsInspection Mock given on purpose */
        $controller = new ListsController($this->entityManager, $this->mockMailChimpForException('patch'));
        $response = $controller->update($this->getRequest(), $list->getId());
        $this->assertMailChimpExceptionResponse($response);
    }

    /**
     * Test controller returns error 404 response when making update MailChimp request without providing Mailchimp ID.
     *
     * @return void
     */
    public function testUpdateListMailChimpNotFound(): void
    {
        $list = $this->createList(static::$listData, false);
        // If there is no list id, skip
        if (!$this->validateList($list)) {
            return;
        }


        /** @noinspection PhpParamsInspection Mock given on purpose */
        $controller = new ListsController($this->entityManager, $this->mockMailChimp());
        $this->assertMailChimpNotFoundResponse($controller->update($this->getRequest(), $list->getId()), $this->getMailChimpResponseError($list->getId()));
    }

    /**
     * Test controller returns success response when making update request to MailChimp.
     *
     * @return void
     */
    public function testUpdateListMailChimpSuccess(): void
    {
        $list = $this->createList(static::$listData, true);
        // If there is no list id, skip
        if (!$this->validateList($list)) {
            return;
        }

        /** @noinspection PhpParamsInspection Mock given on purpose */
        $controller = new ListsController($this->entityManager, $this->mockMailChimpSuccess('patch'));
        $this->assertSuccessResponse($controller->update($this->getRequest(), $list->getId()));
    }
}
