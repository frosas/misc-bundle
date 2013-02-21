<?php

namespace Frosas\MiscBundle;
 
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Instead of
 *
 *     if ($request->isMethod('post') && $form->bind($request)->isValid()) ...
 *
 * you can do
 *
 *     if (FormHelper::create($form)->isValid($request)) ...
 *
 * Also, it knows which form was submitted in actions with multiple forms. Every
 * form has to have its name, for example:
 *
 *     $contactFormBuilder = $container->get('form.factory')->createNamedBuilder('contact', 'form');
 */
class FormHelper
{
    private $form;

    static function create(FormInterface $form)
    {
        return new static($form);
    }

    function __construct(FormInterface $form)
    {
        $this->form = $form;
    }

    function isSubmitted(Request $request)
    {
        $name = $this->form->getName();
        return $request->request->has($name) || $request->query->has($name);
    }

    function bind(Request $request)
    {
        if ($this->isSubmitted($request)) $this->form->bind($request);
        return $this;
    }

    function isValid(Request $request)
    {
        if (! $this->form->isBound()) $this->bind($request);
        if ($this->form->isBound()) return $this->form->isValid();
    }
}