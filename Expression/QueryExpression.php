<?php
namespace Slince\Database\Expression;

use Slince\Database\Query;

class QueryExpression extends CompositeExpression
{
    const EQ = '=';
    const NEQ = '<>';
    const LT = '<';
    const LTE = '<=';
    const GT = '>';
    const GTE = '>=';

    protected $type;

    /**
     * @var Query
     */
    protected $query;

    function __construct(Query $query, $type = CompositeExpression::TYPE_AND, $expressions = array())
    {
        $this->query = $query;
        parent::__construct($type);
        $this->addMultiple($expressions);
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
        array_unshift($expressions, $this);
        $queryExpression = new self($this->query, CompositeExpression::TYPE_AND, $expressions);
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
        array_unshift($expressions, $this);
        $queryExpression = new self($this->query, CompositeExpression::TYPE_OR, $expressions);
        return $queryExpression;
    }

    function addMultiple(array $expressions)
    {
        foreach ($expressions as $key => $expression) {
            if (is_callable($expression)) {
                return call_user_func($expression, $this);
            }
            $this->add($expression);
        }
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
        $this->query->setParameter($y);
        return $this->comparison($x, self::EQ, '?');
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
        $this->query->setParameter($y);
        return $this->comparison($x, self::NEQ, '?');
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
        $this->query->setParameter($y);
        return $this->comparison($x, self::LT, '?');
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
        $this->query->setParameter($y);
        return $this->comparison($x, self::LTE, '?');
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
        $this->query->setParameter($y);
        return $this->comparison($x, self::GT, '?');
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
        $this->query->setParameter($y);
        return $this->comparison($x, self::GTE, '?');
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
        $this->query->setParameter($y);
        return $this->comparison($x, 'LIKE', '?');
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
        $this->query->setParameter($y);
        return $this->comparison($x, 'NOT LIKE', '?');
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
        $this->query->addParameters($y);
        $y = array_fill(0, count($y), '?');
        return $this->comparison($x, 'IN', '(' . implode(', ', $y) . ')');
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
        $this->query->addParameters($y);
        $y = array_fill(0, count($y), '?');
        return $this->comparison($x, 'NOT IN', '(' . implode(', ', (array)$y) . ')');
    }
}