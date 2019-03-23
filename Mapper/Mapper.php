<?php

namespace Mapper;

use PhpOption\None;
use PhpOption\Some;

class Mapper
{
    /**
     * @var Mapping[]
     */
    private $mappings = [];

    public function add($arrayKey, $propertyName)
    {
        $mapping = new Mapping();
        $mapping->setArrayKey($arrayKey);
        $mapping->setPropertyName($propertyName);
        array_push($this->mappings, $mapping);
        return $mapping;
    }

    public function map($array)
    {
        $obj = new $this();

        if (empty($this->mappings)) {
            return None::create();
        }

        foreach ($this->mappings as $mapping) {
            if (
                Some::ensure($mapping->getArrayKey())->isEmpty() ||
                Some::ensure($mapping->getPropertyName())->isEmpty()
            ) {
                continue;
            }

            $setter = sprintf("set%s", ucfirst($mapping->getPropertyName()));

            if (empty($mapping->getConditions()) && isset($array[$mapping->getArrayKey()])) {
                call_user_func($obj->$setter, $array[$mapping->getArrayKey()]);
                continue;
            }

            foreach ($mapping->getConditions() as $condition) {
                /**
                 * ['predicate' => predicate, 'action' => action]
                 */
                if ($this->checkCondition($condition, $mapping->getArrayKey(), $array)) {
                    $obj->$setter(
                        $this->shouldApplyAction($condition)
                            ? call_user_func($condition['action'], $array[$mapping->getArrayKey()])
                            : $array[$mapping->getArrayKey()]
                    );

                    // Break to only do the first match
                    break;
                }
            }

            // TODO: Check that all criteria matched
        }

        return $obj;
    }

    /**
     * @param array $condition
     * @return bool
     */
    private function checkCondition($condition, $key, $array)
    {
        if (
            empty($condition['predicate']) ||
            $condition['predicate'] instanceof None ||
            (
                !empty($condition['predicate']) &&
                call_user_func(function () use ($condition, $key, $array) {
                    return call_user_func($condition['predicate'], $array[$key]);
                })
            )
        ) {
            return true;
        }
    }

    public function shouldApplyAction($condition)
    {
        if (
            !empty($condition['action']) &&
            !$condition['action'] instanceof None
        ) {
            return true;
        }
    }

    /**
     * @return array
     */
    public function getMappings()
    {
        return $this->mappings;
    }
}

class Mapping
{
    private $propertyName, $arrayKey;

    private $conditions = [];

    public function addCondition($predicate, $action)
    {
        array_push(
            $this->conditions,
            [
                'predicate' => $predicate,
                'action' => $action,
            ]
        );
    }

    public function addPredicate($predicate)
    {
        array_push(
            $this->conditions,
            [
                'predicate' => $predicate,
                'action' => None::create(),
            ]
        );
    }

    public function addAction($action)
    {
        array_push(
            $this->conditions,
            [
                'predicate' => None::create(),
                'action' => $action,
            ]
        );
    }

    public function map($arrayKey, $propertyName)
    {
        $this->arrayKey = $arrayKey;
        $this->propertyName = $propertyName;
        return $this;
    }

    #region Setters and Getters

    /**
     * @return mixed
     */
    public function getPropertyName()
    {
        return $this->propertyName;
    }

    /**
     * @param mixed $propertyName
     */
    public function setPropertyName($propertyName)
    {
        $this->propertyName = $propertyName;
    }

    /**
     * @return mixed
     */
    public function getArrayKey()
    {
        return $this->arrayKey;
    }

    /**
     * @param mixed $arrayKey
     */
    public function setArrayKey($arrayKey)
    {
        $this->arrayKey = $arrayKey;
    }

    /**
     * @return array
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    #endregion
}