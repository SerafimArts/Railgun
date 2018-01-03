<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Parser\Runtime;

use Hoa\Iterator\Lookahead;
use Railt\Parser\Exception\Exception;
use Railt\Parser\Exception\RuleException;
use Railt\Parser\Exception\UnrecognizedToken;
use Railt\Parser\Lexer;
use Railt\Parser\Rule\Choice;
use Railt\Parser\Rule\Concatenation;
use Railt\Parser\Rule\Repetition;
use Railt\Parser\Rule\Token;

/**
 * Analyze rules and transform them into atomic rules operations.
 */
class Analyzer
{
    /**
     * PP lexemes.
     */
    protected static $ppLexemes = [
        'default' => [
            'skip'         => '\s',
            'or'           => '\|',
            'zero_or_one'  => '\?',
            'one_or_more'  => '\+',
            'zero_or_more' => '\*',
            'n_to_m'       => '\{[0-9]+,[0-9]+\}',
            'zero_to_m'    => '\{,[0-9]+\}',
            'n_or_more'    => '\{[0-9]+,\}',
            'exactly_n'    => '\{[0-9]+\}',
            'skipped'      => '::[a-zA-Z_][a-zA-Z0-9_]*(\[\d+\])?::',
            'kept'         => '<[a-zA-Z_][a-zA-Z0-9_]*(\[\d+\])?>',
            'named'        => '[a-zA-Z_][a-zA-Z0-9_]*\(\)',
            'node'         => '#[a-zA-Z_][a-zA-Z0-9_]*(:[mM])?',
            'capturing_'   => '\(',
            '_capturing'   => '\)',
        ],
    ];

    /**
     * Lexer iterator.
     * @var Lookahead
     */
    protected $lexer;

    /**
     * Tokens representing rules.
     * @var array
     */
    protected $tokens;

    /**
     * Rules.
     * @var array
     */
    protected $rules;

    /**
     * Parsed rules.
     * @var array
     */
    protected $parsedRules;

    /**
     * Counter to auto-name transitional rules.
     * @var int
     */
    protected $transitionalRuleCounter = 0;

    /**
     * Rule name being analyzed.
     * @var string
     */
    private $ruleName;

    /**
     * Constructor.
     *
     * @param array $tokens Tokens.
     */
    public function __construct(array $tokens)
    {
        $this->tokens = $tokens;
    }

    /**
     * Build the analyzer of the rules (does not analyze the rules).
     *
     * @param array $rules
     * @return array
     * @throws UnrecognizedToken
     * @throws RuleException
     */
    public function analyzeRules(array $rules)
    {
        if (empty($rules)) {
            throw new RuleException('No rules specified!', 0);
        }

        $this->parsedRules = [];
        $this->rules       = $rules;
        $lexer             = new Lexer();

        foreach ($rules as $key => $value) {
            $this->lexer = new Lookahead($lexer->lexMe($value, static::$ppLexemes));
            $this->lexer->rewind();

            $this->ruleName = $key;
            $nodeId         = null;

            if ('#' === $key[0]) {
                $nodeId = $key;
                $key    = \substr($key, 1);
            }

            $pNodeId = $nodeId;
            $rule    = $this->rule($pNodeId);

            if (null === $rule) {
                throw new Exception('Error while parsing rule %s.', 1, $key);
            }

            $zeRule = $this->parsedRules[$rule];
            $zeRule->setName($key);
            $zeRule->setPPRepresentation($value);

            if (null !== $nodeId) {
                $zeRule->setDefaultId($nodeId);
            }

            unset($this->parsedRules[$rule]);
            $this->parsedRules[$key] = $zeRule;
        }

        return $this->parsedRules;
    }

    /**
     * Implementation of “rule”.
     *
     * @return  mixed
     */
    protected function rule(&$pNodeId)
    {
        return $this->choice($pNodeId);
    }

    /**
     * Implementation of “choice”.
     *
     * @return  mixed
     */
    protected function choice(&$pNodeId)
    {
        $children = [];

        // concatenation() …
        $nNodeId = $pNodeId;
        $rule    = $this->concatenation($nNodeId);

        if (null === $rule) {
            return;
        }

        if (null !== $nNodeId) {
            $this->parsedRules[$rule]->setNodeId($nNodeId);
        }

        $children[] = $rule;
        $others     = false;

        // … ( ::or:: concatenation() )*
        while ('or' === $this->lexer->current()['token']) {
            $this->lexer->next();
            $others  = true;
            $nNodeId = $pNodeId;
            $rule    = $this->concatenation($nNodeId);

            if (null === $rule) {
                return;
            }

            if (null !== $nNodeId) {
                $this->parsedRules[$rule]->setNodeId($nNodeId);
            }

            $children[] = $rule;
        }

        $pNodeId = null;

        if (false === $others) {
            return $rule;
        }

        $name                     = $this->transitionalRuleCounter++;
        $this->parsedRules[$name] = new Choice($name, $children);

        return $name;
    }

    /**
     * Implementation of “concatenation”.
     *
     * @return  mixed
     */
    protected function concatenation(&$pNodeId)
    {
        $children = [];

        // repetition() …
        $rule = $this->repetition($pNodeId);

        if (null === $rule) {
            return;
        }

        $children[] = $rule;
        $others     = false;

        // … repetition()*
        while (null !== $r1 = $this->repetition($pNodeId)) {
            $children[] = $r1;
            $others     = true;
        }

        if (false === $others && null === $pNodeId) {
            return $rule;
        }

        $name                     = $this->transitionalRuleCounter++;
        $this->parsedRules[$name] = new Concatenation(
            $name,
            $children,
            null
        );

        return $name;
    }

    /**
     * Implementation of “repetition”.
     *
     * @param $pNodeId
     * @return int|mixed|void
     */
    protected function repetition(&$pNodeId)
    {
        // simple() …
        $children = $this->simple($pNodeId);

        if (null === $children) {
            return;
        }

        // … quantifier()?
        switch ($this->lexer->current()['token']) {
            case 'zero_or_one':
                $min = 0;
                $max = 1;
                $this->lexer->next();

                break;

            case 'one_or_more':
                $min = 1;
                $max = -1;
                $this->lexer->next();

                break;

            case 'zero_or_more':
                $min = 0;
                $max = -1;
                $this->lexer->next();

                break;

            case 'n_to_m':
                $handle = \trim($this->lexer->current()['value'], '{}');
                $nm     = \explode(',', $handle);
                $min    = (int)\trim($nm[0]);
                $max    = (int)\trim($nm[1]);
                $this->lexer->next();

                break;

            case 'zero_to_m':
                $min = 0;
                $max = (int)\trim($this->lexer->current()['value'], '{,}');
                $this->lexer->next();

                break;

            case 'n_or_more':
                $min = (int)\trim($this->lexer->current()['value'], '{,}');
                $max = -1;
                $this->lexer->next();

                break;

            case 'exactly_n':
                $handle = \trim($this->lexer->current()['value'], '{}');
                $min    = (int)$handle;
                $max    = $min;
                $this->lexer->next();

                break;
        }

        // … <node>?
        if ('node' === $this->lexer->current()['token']) {
            $pNodeId = $this->lexer->current()['value'];
            $this->lexer->next();
        }

        if (! isset($min)) {
            return $children;
        }

        if (-1 != $max && $max < $min) {
            throw new Exception(
                'Upper bound %d must be greater or ' .
                'equal to lower bound %d in rule %s.',
                2,
                [$max, $min, $this->ruleName]
            );
        }

        $name                     = $this->transitionalRuleCounter++;
        $this->parsedRules[$name] = new Repetition(
            $name,
            $min,
            $max,
            $children,
            null
        );

        return $name;
    }

    /**
     * Implementation of “simple”.
     *
     * @return  mixed
     * @throws  \Railt\Parser\Exception\Exception
     * @throws  RuleException
     */
    protected function simple(&$pNodeId)
    {
        if ($this->lexer->current()['token'] === 'capturing_') {
            $this->lexer->next();
            $rule = $this->choice($pNodeId);

            if ($rule === null) {
                return;
            }

            if ($this->lexer->current()['token'] !== '_capturing') {
                return;
            }

            $this->lexer->next();

            return $rule;
        }

        if ($this->lexer->current()['token'] === 'skipped') {
            $tokenName = \trim($this->lexer->current()['value'], ':');

            if (\substr($tokenName, -1) === ']') {
                $uId       = (int)\substr($tokenName, \strpos($tokenName, '[') + 1, -1);
                $tokenName = \substr($tokenName, 0, \strpos($tokenName, '['));
            } else {
                $uId = -1;
            }

            $exists = false;

            foreach ($this->tokens as $namespace => $tokens) {
                foreach ($tokens as $token => $value) {
                    if (
                        $token === $tokenName ||
                        \strpos($token, $tokenName) === 0
                    ) {
                        $exists = true;

                        break 2;
                    }
                }
            }

            if (false == $exists) {
                throw new Exception(
                    'Token ::%s:: does not exist in rule %s.',
                    3,
                    [$tokenName, $this->ruleName]
                );
            }

            $name                     = $this->transitionalRuleCounter++;
            $this->parsedRules[$name] = new Token(
                $name,
                $tokenName,
                null,
                $uId
            );
            $this->lexer->next();

            return $name;
        }

        if ($this->lexer->current()['token'] === 'kept') {
            $tokenName = \trim($this->lexer->current()['value'], '<>');

            if (\substr($tokenName, -1) === ']') {
                $uId       = (int)\substr($tokenName, \strpos($tokenName, '[') + 1, -1);
                $tokenName = \substr($tokenName, 0, \strpos($tokenName, '['));
            } else {
                $uId = -1;
            }

            $exists = false;

            foreach ($this->tokens as $namespace => $tokens) {
                foreach ($tokens as $token => $value) {
                    if (
                        $token === $tokenName ||
                        \substr($token, 0, (int)\strpos($token, ':')) === $tokenName
                    ) {
                        $exists = true;

                        break 2;
                    }
                }
            }

            if ($exists === false) {
                throw new Exception(
                    'Token <%s> does not exist in rule %s.',
                    4,
                    [$tokenName, $this->ruleName]
                );
            }

            $name  = $this->transitionalRuleCounter++;
            $token = new Token(
                $name,
                $tokenName,
                null,
                $uId,
                true
            );

            $this->parsedRules[$name] = $token;
            $this->lexer->next();

            return $name;
        }

        if ('named' === $this->lexer->current()['token']) {
            $tokenName = \rtrim($this->lexer->current()['value'], '()');

            $isEmptyRule = ! \array_key_exists($tokenName, $this->rules) &&
                ! \array_key_exists('#' . $tokenName, $this->rules);

            if ($isEmptyRule) {
                throw new RuleException(
                    'Cannot call rule %s() in rule %s because it does not exist.',
                    5,
                    [$tokenName, $this->ruleName]
                );
            }

            if (0 === $this->lexer->key() &&
                'EOF' === $this->lexer->getNext()['token']) {
                $name                     = $this->transitionalRuleCounter++;
                $this->parsedRules[$name] = new Concatenation(
                    $name,
                    [$tokenName],
                    null
                );
            } else {
                $name = $tokenName;
            }

            $this->lexer->next();

            return $name;
        }
    }
}
