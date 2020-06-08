<?php

namespace App\GraphQL;

use App\GraphQL\Error\ValidationError;
use GraphQL\Error\Error;
use Rebing\GraphQL\GraphQL as GraphQLBase;


class GraphQL extends GraphQLBase
{
    /**
     * @param Error $e
     * @return array
     */
    public static function formatError(Error $e): array
    {
        $error = [
            'message' => $e->getMessage()
        ];

        $locations = $e->getLocations();
        if (!empty($locations)) {
            $error['locations'] = array_map(function ($loc) {
                return $loc->toArray();
            }, $locations);
        }

        $previous = $e->getPrevious();
        if ($previous && $previous instanceof ValidationError) {
            $error['validation'] = $previous->getValidatorMessages();
        }

        return $error;
    }
}
