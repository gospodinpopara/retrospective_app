<?php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\Parameter;
use ApiPlatform\OpenApi\Model\RequestBody;
use ApiPlatform\OpenApi\Model\Response;
use ApiPlatform\OpenApi\OpenApi;

class UserNotificationsOpenApiFactory implements OpenApiFactoryInterface
{
    public function __construct(
        private OpenApiFactoryInterface $openApiFactory
    ) {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->openApiFactory->__invoke($context);

        // Context Controller/UserNotificationController::getNotVisitedCount
        $pathItem = $openApi->getPaths()->getPath('/api/user_notifications/not_visited_count/{userId}');
        if ($pathItem) {
            $operation = $pathItem->getGet();
            if ($operation) {
                $openApi->getPaths()->addPath('/api/user_notifications/not_visited_count/{userId}',
                    $pathItem->withGet($operation
                        ->withSummary('Gets the count of not visited notifications for a specific user.')
                        ->withDescription('Returns the count of not visited notifications for a user.')
                        ->withResponses([
                            '200' => new Response(
                                description: 'Returns the count of not visited notifications.',
                                content: new \ArrayObject([
                                    'application/json' => [
                                        'schema' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'notVisitedCount' => ['type' => 'integer', 'example' => 5],
                                            ],
                                        ],
                                    ],
                                ]),
                            ),
                            '404' => new Response(
                                description: 'User not found or no notifications for user.',
                            ),
                        ])
                        ->withParameters([
                            new Parameter(
                                name: 'userId',
                                in: 'path',
                                description: 'The ID of the user.',
                                required: true,
                                schema: ['type' => 'integer'],
                            ),
                        ]),
                    ),
                );
            }
        }

        // Context Controller/UserNotificationController::getLatestSiteNotifications
        $pathItem = $openApi->getPaths()->getPath('/api/user_notifications/latest_site_notifications/{userId}');
        if ($pathItem) {
            $operation = $pathItem->getGet();
            if ($operation) {
                $openApi->getPaths()->addPath('/api/user_notifications/latest_site_notifications/{userId}',
                    $pathItem->withGet($operation
                        ->withSummary('Gets the latest site notifications for a specific user.')
                        ->withDescription('Returns the latest site notifications for a user, including counts of not acknowledged and not visited notifications.')
                        ->withResponses([
                            '200' => new Response(
                                description: 'Returns the latest site notifications.',
                                content: new \ArrayObject([
                                    'application/json' => [
                                        'schema' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'notAckedCount' => ['type' => 'integer', 'example' => 3],
                                                'notVisitedCount' => ['type' => 'integer', 'example' => 2],
                                                'notifications' => [
                                                    'type' => 'array',
                                                    'items' => [
                                                        '$ref' => '#/components/schemas/UserNotification',                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ]),
                            ),
                            '404' => new Response(
                                description: 'User not found or no notifications for user.',
                            ),
                        ])
                        ->withParameters([
                            new Parameter(
                                name: 'userId',
                                in: 'path',
                                description: 'The ID of the user.',
                                required: true,
                                schema: ['type' => 'integer'],
                            ),
                        ]),
                    ),
                );
            }
        }

        // Context Controller/UserNotificationController::setAllAck
        $pathItem = $openApi->getPaths()->getPath('/api/user_notifications/set_all_as_ack');
        if ($pathItem) {
            $operation = $pathItem->getPost();
            if ($operation) {
                $openApi->getPaths()->addPath('/api/user_notifications/set_all_as_ack',
                    $pathItem->withPost($operation
                        ->withSummary('Sets all notifications for a user as acknowledged.')
                        ->withDescription('Acknowledges all notifications for a specific user.')
                        ->withRequestBody(
                            new RequestBody(
                                description: 'The request body containing the userId.',
                                content: new \ArrayObject([
                                    'application/json' => [
                                        'schema' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'userId' => ['type' => 'integer', 'example' => 123],
                                            ],
                                            'required' => ['userId'],
                                        ],
                                    ],
                                ]),
                            ),
                        )
                        ->withResponses([
                            '200' => new Response(
                                description: 'All notifications acknowledged successfully.',
                                content: new \ArrayObject([
                                    'application/json' => [
                                        'schema' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'ackedCount' => ['type' => 'integer', 'example' => 10],
                                            ],
                                        ],
                                    ],
                                ]),
                            ),
                            '400' => new Response(
                                description: 'Invalid or missing userId in the request body.',
                            ),
                        ]),
                    ),
                );
            }
        }

        return $openApi;
    }
}
