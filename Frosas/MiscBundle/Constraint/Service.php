<?php

namespace Frosas\MiscBundle\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Like the <a href="http://symfony.com/doc/current/reference/constraints/Callback.html">
 * Callback constraint</a> but using services and simpler than using <a href="http://symfony.com/doc/current/cookbook/validation/custom_constraint.html">
 * custom constraints</a>.
 *
 * @see ServiceValidator
 *
 * @Annotation
 */
class Service extends Constraint
{
    public $callbacks;

    function validatedBy()
    {
        return 'frosas_misc.service_validator';
    }

    function getRequiredOptions()
    {
        return array('callbacks');
    }

    function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
