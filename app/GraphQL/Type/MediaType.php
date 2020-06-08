<?php

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;

class MediaType extends BaseType
{
    /**
     * @var array
     */
    protected $attributes = [
        'model' => 'App\\Models\\Media',
        'name' => 'Media',
        'description' => 'A media type'
    ];

    /**
     * @return array
     */
    public function fields(): array
    {
        return $this->accessFilter([
            'id' => [
                'type' => Type::int()
            ],
            'file_name' => [
                'type' => Type::string(),
            ],
            'collection_name' => [
                'type' => Type::string(),
            ],
            'disk' => [
                'type' => Type::string(),
            ],
            'custom_properties' => [
                'type' => Type::string(),
            ],
            'model_type' => [
                'type' => Type::string(),
            ],
            'model_id' => [
                'type' => Type::int(),
            ],
            'responsive_image' => [
                'type' => Type::string(),
            ],
            'url' => [
                'type' => Type::string(),
                'selectable' => false,
            ],
            'thumbUrl' => [
                'type' => Type::string(),
                'selectable' => false
            ],
            'mime_type' => [
                'type' => Type::string(),
            ],
            'size' => [
                'type' => Type::int(),
            ],
            'created_at' => [
                'type' => Type::string(),
            ],
            'updated_at' => [
                'type' => Type::string(),
            ]
        ]);
    }

    /**
     * @param $root
     * @param $args
     * @return mixed
     */
    protected function resolveUrlField($root, $args)
    {
        return $root->getFullUrl();
    }

    /**
     * @param $root
     * @param $args
     * @return mixed
     */
    protected function resolveThumbUrlField($root, $args)
    {
        return $root->getFullUrl('thumb');
    }
}