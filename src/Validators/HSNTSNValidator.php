<?php

namespace d2gPmPluginCarPartsFinder\Validators;

use Plenty\Validation\Validator;

class HSNTSNValidator extends Validator
{
    protected function defineAttributes()
    {
        $this->addString('hsn', true);
        $this->addString('tsn', true);
    }
}
