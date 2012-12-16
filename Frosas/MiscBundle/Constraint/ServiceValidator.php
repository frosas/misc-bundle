<?php

namespace Frosas\MiscBundle\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\ConstraintValidator;

class ServiceValidator extends ConstraintValidator
{
    private $container;

    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    function validate($object, Constraint $constraint)
    {
        foreach ($this->prepareCallbacks($constraint->callbacks) as $callback) {
            call_user_func($callback, $object, $this->context);
        }
    }

    private function prepareCallbacks(array $callbacks)
    {
        foreach ($callbacks as & $callback) {
            // If only the service name is specified use the default method name
            if (! is_array($callback)) $callback = array($callback, 'validate');

            $callback[0] = $this->container->get($callback[0]);
        }

        return $callbacks;
    }
}
