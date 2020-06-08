<?php

declare(strict_types=1);

use example\Type\ExampleType;
use example\Query\ExampleQuery;
use example\Mutation\ExampleMutation;
use example\Type\ExampleRelationType;

return [

    // The prefix for routes
    'prefix' => 'graphql',

    // The routes to make GraphQL request. Either a string that will apply
    // to both query and mutation or an array containing the key 'query' and/or
    // 'mutation' with the according Route
    //
    // Example:
    //
    // Same route for both query and mutation
    //
    // 'routes' => 'path/to/query/{graphql_schema?}',
    //
    // or define each route
    //
    // 'routes' => [
    //     'query' => 'query/{graphql_schema?}',
    //     'mutation' => 'mutation/{graphql_schema?}',
    // ]
    //
    'routes' => '{graphql_schema?}',

    // The controller to use in GraphQL request. Either a string that will apply
    // to both query and mutation or an array containing the key 'query' and/or
    // 'mutation' with the according Controller and method
    //
    // Example:
    //
    // 'controllers' => [
    //     'query' => '\Rebing\GraphQL\GraphQLController@query',
    //     'mutation' => '\Rebing\GraphQL\GraphQLController@mutation'
    // ]
    //
    'controllers' => \Rebing\GraphQL\GraphQLController::class.'@query',

    // Any middleware for the graphql route group
    'middleware' => [],

    // Additional route group attributes
    //
    // Example:
    //
    // 'route_group_attributes' => ['guard' => 'api']
    //
    'route_group_attributes' => [],

    // The name of the default schema used when no argument is provided
    // to GraphQL::schema() or when the route is used without the graphql_schema
    // parameter.
    'default_schema' => 'default',

    // The schemas for query and/or mutation. It expects an array of schemas to provide
    // both the 'query' fields and the 'mutation' fields.
    //
    // You can also provide a middleware that will only apply to the given schema
    //
    // Example:
    //
    //  'schema' => 'default',
    //
    //  'schemas' => [
    //      'default' => [
    //          'query' => [
    //              'users' => 'App\GraphQL\Query\UsersQuery'
    //          ],
    //          'mutation' => [
    //
    //          ]
    //      ],
    //      'user' => [
    //          'query' => [
    //              'profile' => 'App\GraphQL\Query\ProfileQuery'
    //          ],
    //          'mutation' => [
    //
    //          ],
    //          'middleware' => ['auth'],
    //      ],
    //      'user/me' => [
    //          'query' => [
    //              'profile' => 'App\GraphQL\Query\MyProfileQuery'
    //          ],
    //          'mutation' => [
    //
    //          ],
    //          'middleware' => ['auth'],
    //      ],
    //  ]
    //
    'schemas' => [
        'default' => [
            'query' => [
                'user' => App\GraphQL\Query\UserQuery::class,
                'users' => App\GraphQL\Query\UsersQuery::class,
                'vsm' => App\GraphQL\Query\VsmQuery::class,
                'vars' => App\GraphQL\Query\VarsQuery::class,
                'login' => App\GraphQL\Query\User\LoginQuery::class,
                'logout' => App\GraphQL\Query\User\LogoutQuery::class,

                /**
                 * OTS
                 */
                /*Projects*/
                /*Project groups*/
                'OtsProjectGroups' => App\GraphQL\Query\Ots\Projects\GroupsQuery::class,
                /*Messages*/
                'otsMessages' => App\GraphQL\Query\Ots\Messages\MessagesQuery::class,
                'otsMessage' => App\GraphQL\Query\Ots\Messages\MessageQuery::class,
                'otsMessagesStatistic' => \App\GraphQL\Query\Ots\Messages\MessagesStatisticQuery::class,
            ],
            'mutation' => [
                'profileUser' => App\GraphQL\Mutation\ProfileUserMutation::class,
                'registerUser' => App\GraphQL\Mutation\RegisterUserMutation::class,
                /**
                 * OTS
                 */
                /*Messages*/
                'otsSendMessage' => \App\GraphQL\Mutation\Ots\Messages\SendMessageMutation::class,
                'otsSendEventMessage' => \App\GraphQL\Mutation\Ots\Messages\SendEventMessageMutation::class,
                'otsReplyToMessage' => \App\GraphQL\Mutation\Ots\Messages\ReplyToMessageMutation::class,
                /*Tutor*/
                'otsTutorAbout' => \App\GraphQL\Mutation\Ots\Tutors\AboutMutation::class,
                /*Project Groups*/
                'otsAddProjectGroupMutation' => \App\GraphQL\Mutation\Ots\Projects\Groups\AddMutation::class,
                'otsRemoveProjectGroupMutation' => \App\GraphQL\Mutation\Ots\Projects\Groups\RemoveMutation::class,
                'otsChangeProjectGroupMutation' => \App\GraphQL\Mutation\Ots\Projects\Groups\ChangeMutation::class,
                /*User*/
                'otsUserSubject' => \App\GraphQL\Mutation\Ots\Users\SubjectsRequestMutation::class,

                'usersAddTagsMutation' => \App\GraphQL\Mutation\Users\AddTagsMutation::class,
                'usersAddGroupsMutation' => \App\GraphQL\Mutation\Users\AddGroupsMutation::class,
                'usersRemoveTagsMutation' => \App\GraphQL\Mutation\Users\RemoveTagsMutation::class,
                'usersRemoveGroupsMutation' => \App\GraphQL\Mutation\Users\RemoveGroupsMutation::class,
                /*Event*/
                'otsEventRequestMutation' => \App\GraphQL\Mutation\Ots\Events\RequestMutation::class,
                'otsEventMutation' => \App\GraphQL\Mutation\Ots\Events\LessonMutation::class,
                'otsEventStatusMutation' => \App\GraphQL\Mutation\Ots\Events\Lessons\StatusMutation::class,
                /*Event request*/
                'otsRequestEventStatusMutation' => \App\GraphQL\Mutation\Ots\Events\Requests\StatusMutation::class,
                'otsConfirmLessonRequestMutation' => \App\GraphQL\Mutation\Ots\Events\Requests\ConfirmRequestMutation::class,
                'otsCompleteLessonMutation' => \App\GraphQL\Mutation\Ots\Events\Lessons\CompleteLessonMutation::class
            ],
            'middleware' => [Barryvdh\Debugbar\Middleware\InjectDebugbar::class],
            'method'     => ['get', 'post'],
        ],
    ],

    // The types available in the application. You can then access it from the
    // facade like this: GraphQL::type('user')
    //
    // Example:
    //
    // 'types' => [
    //     'user' => 'App\GraphQL\Type\UserType'
    // ]
    //
    'types' => [
        \Rebing\GraphQL\Support\UploadType::class,
        // 'example'           => ExampleType::class,
        // 'relation_example'  => ExampleRelationType::class,
        // \Rebing\GraphQL\Support\UploadType::class,
        App\GraphQL\Type\UserType::class,
        App\GraphQL\Type\UsersType::class,
        App\GraphQL\Type\User\UserAuthType::class,
        App\GraphQL\Type\VsmType::class,
        App\GraphQL\Type\VariableType::class,
        /**
         * OTS
         */
        /*Projects*/
        /*Project groups*/
        App\GraphQL\Type\Ots\Projects\Groups\GroupsType::class,
        App\GraphQL\Type\Ots\Projects\Groups\GroupType::class,
        /*Messages*/
        App\GraphQL\Type\Ots\Messages\MessagesType::class,
        App\GraphQL\Type\Ots\Messages\MessageType::class,
        App\GraphQL\Type\Ots\Messages\SimpleMessageType::class,
        App\GraphQL\Type\Ots\Messages\MessageAggregateType::class,
        App\GraphQL\Type\Ots\Messages\MessageUsersType::class,
        App\GraphQL\Type\Ots\Messages\MessagesStatisticType::class,
        /*Tutor*/
        \App\GraphQL\Type\Ots\Tutor\TutorAboutType::class,
        /*Event*/
        App\GraphQL\Type\Ots\Events\Lessons\LessonType::class,
        App\GraphQL\Type\Ots\Events\Lessons\EnterLessonType::class,
        App\GraphQL\Type\Ots\Events\EventType::class,
        App\GraphQL\Type\Ots\Events\EventsType::class,
        App\GraphQL\Type\Ots\Events\RequestType::class,
        App\GraphQL\Type\Ots\Events\EventUserType::class,
        /*User*/
        \App\GraphQL\Type\Ots\User\UserTagsType::class,
        \App\GraphQL\Type\Ots\User\UserSubjectsType::class,
        /*Pagination*/
        App\GraphQL\Type\PaginationType::class,
        /*Media*/
        App\GraphQL\Type\MediaType::class,
    ],

    // The types will be loaded on demand. Default is to load all types on each request
    // Can increase performance on schemes with many types
    // Presupposes the config type key to match the type class name property
    'lazyload_types' => false,

    // This callable will be passed the Error object for each errors GraphQL catch.
    // The method should return an array representing the error.
    // Typically:
    // [
    //     'message' => '',
    //     'locations' => []
    // ]
    'error_formatter' => [App\GraphQL\GraphQL::class, 'formatError'],
    /*
     * Custom Error Handling
     *
     * Expected handler signature is: function (array $errors, callable $formatter): array
     *
     * The default handler will pass exceptions to laravel Error Handling mechanism
     */
    'errors_handler' => ['\Rebing\GraphQL\GraphQL', 'handleErrors'],

    // You can set the key, which will be used to retrieve the dynamic variables
    'params_key'    => 'variables',

    /*
     * Options to limit the query complexity and depth. See the doc
     * @ https://github.com/webonyx/graphql-php#security
     * for details. Disabled by default.
     */
    'security' => [
        'query_max_complexity'  => null,
        'query_max_depth'       => null,
        'disable_introspection' => false,
    ],

    /*
     * You can define your own pagination type.
     * Reference \Rebing\GraphQL\Support\PaginationType::class
     */
    'pagination_type' => \Rebing\GraphQL\Support\PaginationType::class,

    /*
     * Config for GraphiQL (see (https://github.com/graphql/graphiql).
     */
    'graphiql' => [
        'prefix'     => '/graphiql',
        'controller' => \Rebing\GraphQL\GraphQLController::class.'@graphiql',
        'middleware' => [],
        'view'       => 'graphql::graphiql',
        'display'    => env('ENABLE_GRAPHIQL', true),
    ],

    /*
     * Overrides the default field resolver
     * See http://webonyx.github.io/graphql-php/data-fetching/#default-field-resolver
     *
     * Example:
     *
     * ```php
     * 'defaultFieldResolver' => function ($root, $args, $context, $info) {
     * },
     * ```
     * or
     * ```php
     * 'defaultFieldResolver' => [SomeKlass::class, 'someMethod'],
     * ```
     */
    'defaultFieldResolver' => null,

    /*
     * Any headers that will be added to the response returned by the default controller
     */
    'headers' => [],

    /*
     * Any JSON encoding options when returning a response from the default controller
     * See http://php.net/manual/function.json-encode.php for the full list of options
     */
    'json_encoding_options' => 0,
];
