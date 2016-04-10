<?php
namespace Slince\Database\Expression;

class QueryExpression
{
    const EQ = '=';
    const NEQ = '<>';
    const LT = '<';
    const LTE = '<=';
    const GT = '>';
    const GTE = '>=';

    /**
     * @var CompositeExpression
     */
    protected $expressions;

    protected $type;

    function __construct($type = CompositeExpression::TYPE_AND, $expressions = array())
    {
        $this->type = $type;
        $this->expressions = new CompositeExpression($type, $expressions);
    }

    function __toString()
    {
        return (string)$this->expressions;
    }

    protected function prepareExpressions(array $expressions)
    {
        return array_map(function($expression){
            if (is_callable($expression)) {
                $expression = call_user_func($expression, $this);
            }
            return $expression;
        }, $expressions);
    }

    function getType()
    {
        return $this->type;
    }

    function andX($expressions = null)
    {
        if (!is_array($expressions)) {
            $expressions = func_get_args();
        }
        if ($this->type == CompositeExpression::TYPE_AND) {
            return $this->addMultiple($expressions);
        }
        array_unshift($expressions, $this->expressions);
        $queryExpression = new self(CompositeExpression::TYPE_AND, $expressions);
        return $queryExpression;
    }

    function orX($expressions = null)
    {
        if (!is_array($expressions)) {
            $expressions = func_get_args();
        }
        if ($this->type == CompositeExpression::TYPE_OR) {
            return $this->addMultiple($expressions);
        }
        array_unshift($expressions, $this->expressions);
        $queryExpression = new self(CompositeExpression::TYPE_OR, $expressions);
        return $queryExpression;
    }

    function add($expression)
    {
        $this->expressions->add($expression);
        return $this;
    }

    function addMultiple($expressions)
    {
        $this->expressions->addMultiple($expressions);
        return $this;
    }

    /**
     * Creates a comparison expression.
     *
     * @param mixed $x The left expression.
     * @param string $operator One of the ExpressionBuilder::* constants.
     * @param mixed $y The right expression.
     *
     * @return string
     */
    public function comparison($x, $operator, $y)
    {
        $expression = $x . ' ' . $operator . ' ' . $y;
        return $this->add($expression);
    }

    /**
     * Creates an equality comparison expression with the given arguments.
     *
     * First argument is considered the left expression and the second is the right expression.
     * When converted to string, it will generated a <left expr> = <right expr>. Example:
     *
     *     [php]
     *     // u.id = ?
     *     $expr->eq('u.id', '?');
     *
     * @param mixed $x The left expression.
     * @param mixed $y The right expression.
     *
     * @return string
     */
    public function eq($x, $y)
    {
        return $this->comparison($x, self::EQ, $y);
    }

    /**
     * Creates a non equality comparison expression with the given arguments.
     * First argument is considered the left expression and the second is the right expression.
     * When converted to string, it will generated a <left expr> <> <right expr>. Example:
     *
     *     [php]
     *     // u.id <> 1
     *     $q->where($q->expr()->neq('u.id', '1'));
     *
     * @param mixed $x The left expression.
     * @param mixed $y The right expression.
     *
     * @return string
     */
    public function neq($x, $y)
    {
        return $this->comparison($x, self::NEQ, $y);
    }

    /**
     * Creates a lower-than comparison expression with the given arguments.
     * First argument is considered the left expression and the second is the right expression.
     * When converted to string, it will generated a <left expr> < <right expr>. Example:
     *
     *     [php]
     *     // u.id < ?
     *     $q->where($q->expr()->lt('u.id', '?'));
     *
     * @param mixed $x The left expression.
     * @param mixed $y The right expression.
     *
     * @return string
     */
    public function lt($x, $y)
    {
        return $this->comparison($x, self::LT, $y);
    }

    /**
     * Creates a lower-than-equal comparison expression with the given arguments.
     * First argument is considered the left expression and the second is the right expression.
     * When converted to string, it will generated a <left expr> <= <right expr>. Example:
     *
     *     [php]
     *     // u.id <= ?
     *     $q->where($q->expr()->lte('u.id', '?'));
     *
     * @param mixed $x The left expression.
     * @param mixed $y The right expression.
     *
     * @return string
     */
    public function lte($x, $y)
    {
        return $this->comparison($x, self::LTE, $y);
    }

    /**
     * Creates a greater-than comparison expression with the given arguments.
     * First argument is considered the left expression and the second is the right expression.
     * When converted to string, it will generated a <left expr> > <right expr>. Example:
     *
     *     [php]
     *     // u.id > ?
     *     $q->where($q->expr()->gt('u.id', '?'));
     *
     * @param mixed $x The left expression.
     * @param mixed $y The right expression.
     *
     * @return string
     */
    public function gt($x, $y)
    {
        return $this->comparison($x, self::GT, $y);
    }

    /**
     * Creates a greater-than-equal comparison expression with the given arguments.
     * First argument is considered the left expression and the second is the right expression.
     * When converted to string, it will generated a <left expr> >= <right expr>. Example:
     *
     *     [php]
     *     // u.id >= ?
     *     $q->where($q->expr()->gte('u.id', '?'));
     *
     * @param mixed $x The left expression.
     * @param mixed $y The right expression.
     *
     * @return string
     */
    public function gte($x, $y)
    {
        return $this->comparison($x, self::GTE, $y);
    }

    /**
     * Creates an IS NULL expression with the given arguments.
     *
     * @param string $x The field in string format to be restricted by IS NULL.
     *
     * @return string
     */
    public function isNull($x)
    {
        return $x . ' IS NULL';
    }

    /**
     * Creates an IS NOT NULL expression with the given arguments.
     *
     * @param string $x The field in string format to be restricted by IS NOT NULL.
     *
     * @return string
     */
    public function isNotNull($x)
    {
        return $x . ' IS NOT NULL';
    }

    /**
     * Creates a LIKE() comparison expression with the given arguments.
     *
     * @param string $x Field in string format to be inspected by LIKE() comparison.
     * @param mixed $y Argument to be used in LIKE() comparison.
     *
     * @return string
     */
    public function like($x, $y)
    {
        return $this->comparison($x, 'LIKE', $y);
    }

    /**
     * Creates a NOT LIKE() comparison expression with the given arguments.
     *
     * @param string $x Field in string format to be inspected by NOT LIKE() comparison.
     * @param mixed $y Argument to be used in NOT LIKE() comparison.
     *
     * @return string
     */
    public function notLike($x, $y)
    {
        return $this->comparison($x, 'NOT LIKE', $y);
    }

    /**
     * Creates a IN () comparison expression with the given arguments.
     *
     * @param string $x The field in string format to be inspected by IN() comparison.
     * @param string|array $y The placeholder or the array of values to be used by IN() comparison.
     *
     * @return string
     */
    public function in($x, $y)
    {
        return $this->comparison($x, 'IN', '(' . implode(', ', (array)$y) . ')');
    }

    /**
     * Creates a NOT IN () comparison expression with the given arguments.
     *
     * @param string $x The field in string format to be inspected by NOT IN() comparison.
     * @param string|array $y The placeholder or the array of values to be used by NOT IN() comparison.
     *
     * @return string
     */
    public function notIn($x, $y)
    {
        return $this->comparison($x, 'NOT IN', '(' . implode(', ', (array)$y) . ')');
    }
}