<?php

namespace Oro\Bundle\EntityExtendBundle\Tools;

use CG\Generator\PhpClass;

abstract class ExtendEntityGeneratorExtension
{
    /**
     * Check if generator extension supports configuration pre-processing or can generate code
     *
     * @param string $actionType pre-process or generate
     * @param array  $schemas    whole schemas when actionType is pre-process,
     *                           entity schema when actionType is generate
     *
     * @return bool
     */
    abstract public function supports($actionType, array $schemas);

    /**
     * Apply extension to entity configuration before it will be generated as PHP, YAML files
     *
     * @param array $schemas
     *
     * @return void
     */
    public function preProcessEntityConfiguration(array &$schemas)
    {
    }

    /**
     * @param array    $schema
     * @param PhpClass $class
     *
     * @return void
     */
    public function generate(array $schema, PhpClass $class)
    {
    }
}
