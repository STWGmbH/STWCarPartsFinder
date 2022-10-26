<?php

namespace d2gPmPluginCarPartsFinder\Validators;

use Plenty\Validation\Validator;

class CarPlatformValidator extends Validator
{
    protected function defineAttributes()
    {
        $this->addString('name', true);
    }
}