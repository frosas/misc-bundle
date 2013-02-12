<?php

namespace Frosas\MiscBundle;
 
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class FormHelper
{
    private $form;

    static function create(Form $form)
    {
        return new static($form);
    }

    function __construct(Form $form)
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
        if ($this->isSubmitted($request)) return $this->form->bind($request);
    }

    function isValid(Request $request)
    {
        if ($this->bind($request)) return $this->form->isValid();
    }
}