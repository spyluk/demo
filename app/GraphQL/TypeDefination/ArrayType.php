<?php
namespace App\GraphQL\TypeDefination;

use GraphQL\Language\AST\BooleanValueNode;
use GraphQL\Language\AST\EnumValueNode;
use GraphQL\Language\AST\FloatValueNode;
use GraphQL\Language\AST\IntValueNode;
use GraphQL\Language\AST\ListValueNode;
use GraphQL\Language\AST\NodeList;
use GraphQL\Language\AST\ObjectFieldNode;
use GraphQL\Language\AST\ObjectValueNode;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Error\InvariantViolation;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Utils\Utils;

/**
 * Class StringType
 * @package GraphQL\Type\Definition
 */
class ArrayType extends ScalarType
{
    /**
     * @var string
     */
    public $name = 'array';

    /**
     * @var string
     */
    public $description = 'Array value';

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
     * @param mixed $value
     * @return mixed|string
     */
    public function serialize($value)
    {
        if (!is_array($value)) {
            throw new InvariantViolation("Value nt array: " . Utils::printSafe($value));
        }
        return $value;
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function parseValue($value)
    {
        if (!is_array($value)) {
            throw new InvariantViolation("Value nt array: " . Utils::printSafe($value));
        }
        return $value;
    }

    /**
     * @param \GraphQL\Language\AST\Node $valueNode
     * @param array|null|null $variables
     * @return array|string
     */
    public function parseLiteral($valueNode, ?array $variables = NULL)
    {
        return $this->parse($valueNode);
    }

    /**
     * @param $val
     * @return array|string
     */
    protected function parse($val)
    {
        $result = [];
        if ($val instanceof StringValueNode || $val instanceof IntValueNode || $val instanceof BooleanValueNode
        || $val instanceof EnumValueNode || $val instanceof FloatValueNode) {
            $result = $val->value;
        } elseif($val instanceof ObjectValueNode) {
            if($val->fields) {
                $result = $this->parse($val->fields);
            }
        } elseif($val instanceof NodeList && $val->count()) {
            for ($i = 0; $i < $val->count(); $i++) {
                    $vals = $val->offsetGet($i);
                    if($vals instanceof ObjectFieldNode) {
                        $result = array_merge($result, $this->parse($val->offsetGet($i)));
                    } else {
                        $result[] = $this->parse($val->offsetGet($i));
                    }
            }
        } elseif($val instanceof ObjectFieldNode) {
            $result[$val->name->value] = $this->parse($val->value);
        } elseif($val instanceof ListValueNode) {
            $result = $this->parse($val->values);
        }

        return $result;
    }
}
