<?php

namespace d2gPmPluginCarPartsFinder\Validators;

use Plenty\Validation\Validator;

class CarValidator extends Validator
{
    protected function defineAttributes()
    {
        $this->addString('name', true);
        $this->addString('hsntsn', false);
        $this->addString('ktype', false);
    }
}