<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Hoa\Compiler\Llk\Rule;

use Hoa\File;
use Hoa\Compiler;

/**
 * Class \Hoa\Compiler\Llk\Rule\Token.
 *
 * The token rule.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Token extends Rule
{
    /**
     * LL(k) compiler of hoa://Library/Regex/Grammar.pp.
     *
     * @var \Hoa\Compiler\Llk\Parser
     */
    protected static $_regexCompiler = null;

    /**
     * Token name.
     *
     * @var string
     */
    protected $_tokenName = null;

    /**
     * Namespace.
     *
     * @var string
     */
    protected $_namespace = null;

    /**
     * Token representation.
     *
     * @var string
     */
    protected $_regex = null;

    /**
     * AST of the regex.
     *
     * @var \Hoa\Compiler\Llk\TreeNode
     */
    protected $_ast = null;

    /**
     * Token value.
     *
     * @var string
     */
    protected $_value = null;

    /**
     * Whether the token is kept or not in the AST.
     *
     * @var bool
     */
    protected $_kept = false;

    /**
     * Unification index.
     *
     * @var int
     */
    protected $_unification = -1;

    /**
     * Token offset.
     *
     * @var int
     */
    protected $_offset = 0;

    /**
     * Constructor.
     *
     * @param   string  $name           Name.
     * @param   string  $tokenName      Token name.
     * @param   string  $nodeId         Node ID.
     * @param   int     $unification    Unification index.
     * @param   bool    $kept           Whether the token is kept or not in the AST.
     */
    public function __construct(
        $name,
        $tokenName,
        $nodeId,
        $unification,
        $kept = false
    ) {
        parent::__construct($name, null, $nodeId);

        $this->_tokenName = $tokenName;
        $this->_unification = $unification;
        $this->setKept($kept);
    }

    /**
     * Get token name.
     *
     * @return  string
     */
    public function getTokenName()
    {
        return $this->_tokenName;
    }

    /**
     * Set token namespace.
     *
     * @param   string  $namespace    Namespace.
     * @return  string
     */
    public function setNamespace($namespace)
    {
        $old = $this->_namespace;
        $this->_namespace = $namespace;

        return $old;
    }

    /**
     * Get token namespace.
     *
     * @return  string
     */
    public function getNamespace()
    {
        return $this->_namespace;
    }

    /**
     * Set representation.
     *
     * @param   string  $regex    Representation.
     * @return  string
     */
    public function setRepresentation($regex)
    {
        $old = $this->_regex;
        $this->_regex = $regex;

        return $old;
    }

    /**
     * Get token representation.
     *
     * @return  string
     */
    public function getRepresentation()
    {
        return $this->_regex;
    }

    /**
     * Get AST of the token representation.
     *
     * @return  \Hoa\Compiler\Llk\TreeNode
     */
    public function getAST()
    {
        if (null === static::$_regexCompiler) {
            $stream = new File\Read('hoa://Library/Regex/Grammar.pp');
            $stream->rewind();

            static::$_regexCompiler = Compiler\Llk::load($stream);
        }

        if (null === $this->_ast) {
            $this->_ast = static::$_regexCompiler->parse(
                $this->getRepresentation()
            );
        }

        return $this->_ast;
    }

    /**
     * Set token value.
     *
     * @param   string  $value    Value.
     * @return  string
     */
    public function setValue($value)
    {
        $old = $this->_value;
        $this->_value = $value;

        return $old;
    }

    /**
     * Get token value.
     *
     * @return  string
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Set token offset.
     *
     * @param   int  $offset    Offset.
     * @return  int
     */
    public function setOffset($offset)
    {
        $old = $this->_offset;
        $this->_offset = $offset;

        return $old;
    }

    /**
     * Get token offset.
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->_offset;
    }

    /**
     * Set whether the token is kept or not in the AST.
     *
     * @param   bool  $kept    Kept.
     * @return  bool
     */
    public function setKept($kept)
    {
        $old = $this->_kept;
        $this->_kept = $kept;

        return $old;
    }

    /**
     * Check whether the token is kept in the AST or not.
     *
     * @return  bool
     */
    public function isKept()
    {
        return $this->_kept;
    }

    /**
     * Get unification index.
     *
     * @return  int
     */
    public function getUnificationIndex()
    {
        return $this->_unification;
    }
}
