<?php

class Application_Form_Element_Money extends Zend_Form_Element_Text implements Stringable
{
    public function __construct($spec, $options = null)
    {
        parent::__construct($spec, $options);

        $this->addFilter(new Application_Model_Filter_DeleteCommas())->addValidator(
            'Float',
            true,
            ['messages' => 'Entered value is invalid']
        )->setAttrib('class', 'mask-money');

        if (isset($options['long']) && $options['long']) {
            $this->addValidator('between', false, [
                'min' => -1_000_000_000_000,
                'max' => 1_000_000_000_000,
                'messages' => 'Entered value should be between -1,000,000,000,000 and 1,000,000,000,000',
            ]);
        } else {
            $this->addValidator('between', false, [
                'min' => -1_000_000,
                'max' => 1_000_000,
                'messages' => 'Entered value should be between -1,000,000 and 1,000,000',
            ]);
        }

        return $this;
    }

    public function __toString(): string
    {
        $this->setValue(number_format((float)str_replace(',', '', (string) $this->getValue()), 2));

        return parent::__toString();
    }
}
