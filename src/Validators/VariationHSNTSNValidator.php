<?php

namespace d2gPmPluginCarPartsFinder\Validators;

use Plenty\Validation\Validator;

class VariationHSNTSNValidator extends Validator
{
    protected function defineAttributes()
    {
        $this->addInt('variationId', true);
    }
}
