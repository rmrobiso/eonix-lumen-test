<?php
declare(strict_types=1);

/** @var \Laravel\Lumen\Routing\Router $router */

// MailChimp group
$router->group(['prefix' => 'mailchimp', 'namespace' => 'MailChimp'], function () use ($router) {
    // Lists group
    $router->group(['prefix' => 'lists'], function () use ($router) {
        
        // MailChimp List API endpoints
        $router->post('/', 'ListsController@create');
        $router->get('/', 'ListsController@showLists');
        $router->get('/{listId}', 'ListsController@showList');
        $router->put('/{listId}', 'ListsController@update');
        $router->delete('/{listId}', 'ListsController@remove');

        // MailChimp List Member(s) API endpoints
        $router->post('/{list_id}/members', 'MembersController@create');
        $router->get('/{list_id}/members', 'MembersController@showListMembers');
        $router->get('/{list_id}/members/{member_id}', 'MembersController@showListMember');
        $router->put('/{list_id}/members/{member_id}', 'MembersController@update');
        $router->delete('/{list_id}/members/{member_id}', 'MembersController@remove');
    });
});
