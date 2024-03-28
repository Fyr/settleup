<?php

class Application_Form_File_Sample extends Application_Form_Base
{
    private int $timestamp;

    public function __construct(int $timestamp)
    {
        $this->timestamp = $timestamp;

        parent::__construct();
    }

    public function init()
    {
        $this->setName('file_import');
        parent::init();

        $fileStorageType = new Zend_Form_Element_Hidden('file_storage_type');

        $title = new Zend_Form_Element_Text('title');
        $title->setLabel('Title')->setRequired(true)->addFilter('StripTags')->addFilter('StringTrim');

        $file = new Zend_Form_Element_File('file');
        $file->setLabel('File')
            ->setDescription('Supported types: xls, xlsx')
            ->setRequired()
            ->setValueDisabled(true)
            ->setDestination(Application_Model_File::getStorage())
            ->addValidator('Size', false, 5_242_880)
            ->addValidator('Extension', false, 'xls, xlsx')
            ->addFilter(
                'Rename',
                [
                    'target' => $this->timestamp . '_' . Application_Model_File::getName($file->getFileName())
                        . '.' . Application_Model_File::getType($file->getFileName()),
                    'overwrite' => false,
                ]
            )
            ->setDecorators([
                'File',
                [
                    'Errors',
                    ['tag' => 'span', 'class' => 'help-inline error'],
                ],
                [
                    'Description',
                    ['tag' => 'p', 'class' => 'help-block'],
                ],
                [
                    ['data' => 'HtmlTag'],
                    ['tag' => 'div', 'class' => 'controls'],
                ],
                ['Label', ['class' => 'control-label']],
                [
                    ['row' => 'HtmlTag'],
                    ['tag' => 'div', 'class' => 'control-group'],
                ],
            ]);

        $sample = new Zend_Form_Element_Button('sample_file');
        $sample->setDescription('You can download a sample file so you can understand the expected format and structure for uploading data.')
            ->setAttrib('class', 'btn btn-info')
            ->setDecorators([
                'ViewHelper',
                [
                    'Description',
                    ['tag' => 'p', 'class' => 'label label-info'],
                ],
                [
                    ['row' => 'HtmlTag'],
                    ['tag' => 'div', 'class' => 'controls'],
                ],

            ]);

        $this->addElements([$fileStorageType, $title, $file]);
        $this->setDefaultDecorators(['title']);

        $this->addSubmit('Upload');
        $this->addElement($sample);
    }
}
