<?php

namespace Frosas\MiscBundle\Tests;
 
use Frosas\MiscBundle\FormHelper;
use Symfony\Component\HttpFoundation\Request;

class FormHelperTest extends \PHPUnit_Framework_TestCase
{
    function testSubmittedIsSubmitted()
    {
        $form = $this->createFormMock();
        $request = new Request(array('name' => array()));
        $this->assertTrue((boolean) FormHelper::create($form)->isSubmitted($request));
    }

    function testNotSubmittedIsNotSubmitted()
    {
        $form = $this->createFormMock();
        $request = new Request;
        $this->assertFalse((boolean) FormHelper::create($form)->isSubmitted($request));
    }

    function testSubmittedIsBound()
    {
        $form = $this->createFormMock();
        $form->expects($this->once())->method('bind');
        $request = new Request(array('name' => array()));
        FormHelper::create($form)->bind($request);
    }

    function testNotSubmittedIsNotBound()
    {
        $form = $this->createFormMock();
        $form->expects($this->never())->method('bind');
        FormHelper::create($form)->bind(new Request);
    }

    function testValidBoundIsValid()
    {
        $form = $this->createFormMock();
        $form->expects($this->any())->method('isBound')->will($this->returnValue(true));
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $this->assertTrue((boolean) FormHelper::create($form)->isValid(new Request));
    }

    function testUnboundIsNotValid()
    {
        $form = $this->createFormMock();
        $form->expects($this->any())->method('isBound')->will($this->returnValue(false));
        $form->expects($this->never())->method('isValid');
        $this->assertFalse((boolean) FormHelper::create($form)->isValid(new Request));
    }

    private function createFormMock()
    {
        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->any())->method('getName')->will($this->returnValue('name'));
        return $form;
    }
}