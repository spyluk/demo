<?php
namespace App\GraphQL\TypeDefination;

use GraphQL\Type\Definition\ScalarType;
use GraphQL\Error\InvariantViolation;
use GraphQL\Utils\Utils;
use Illuminate\Http\UploadedFile;

/**
 * Class StringType
 * @package GraphQL\Type\Definition
 */
class UploadType extends ScalarType
{
    /**
     * @var string
     */
    public $name = 'Upload';
    /**
     * @var string
     */
    public $description = 'The `Upload` special type represents a file to be uploaded in the same HTTP request.';

    /**
     * ScalarType constructor.
     */
    public function __construct($name = null)
    {
        if($name) {
            $this->name = $name;
        }

        parent::__construct();
    }

    /**
     * Serializes an internal value to include in a response.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function serialize($value)
    {
        throw new InvariantViolation('`Upload` cannot be serialized');
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function parseValue($value)
    {
        if (!$value instanceof UploadedFile) {
            throw new InvariantViolation("Value not file: " . Utils::printSafe($value));
        }
        return $value;
    }

    /**
     * Parses an externally provided literal value (hardcoded in GraphQL query) to use as an input
     *
     * @param \GraphQL\Language\AST\Node $valueNode
     * @param array|null $variables
     * @throws \Exception
     */
    public function parseLiteral($valueNode, array $variables = null)
    {
        throw new \Exception('`Upload` cannot be hardcoded in query, be sure to conform to GraphQL multipart request specification. Instead got: ' . $valueNode->kind, $valueNode);
    }
}
