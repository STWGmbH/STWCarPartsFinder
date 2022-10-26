<?php

namespace d2gPmPluginCarPartsFinder\Validators;

use Plenty\Validation\Validator;

class CarModelValidator extends Validator
{
    protected function defineAttributes()
    {
        $this->addString('name', true);
    }
}