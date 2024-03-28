<?php

/**
 * Helper creation of models.
 */
class Application_Model_App
{
    private readonly object $_model;

    /**
     * @static
     * @param string $modelName
     * @return  object
     */
    public static function getModel($modelName)
    {
        $self = new self($modelName);

        return $self->_model;
    }

    /**
     * @param $modelName
     */
    private function __construct(private $_modelName)
    {
        $_modelName = 'Application_Model_' . $_modelName;
        $this->_model = new $_modelName();
        $this->_applyObservers();

        return $this->_model;
    }

    /**
     * @return Application_Model_App
     */
    private function _applyObservers()
    {
        if ($this->_model instanceof Application_Model_Base_Entity) {
            $this->_setObservers();
        }

        return $this;
    }

    /**
     * Attaches observers
     *
     * @return Application_Model_App
     */
    private function _setObservers()
    {
        if ($this->_getObservers()) {
            foreach ($this->_getObservers() as $key => $observerName) {
                $observer = 'Application_Model_Observes_' . $observerName;
                $this->_model->attach(new $observer($this->_model, $key));
            }
        }

        return $this;
    }

    /**
     * Gets an array of model observers
     *
     * @return null|array - An array of observers
     */
    private function _getObservers()
    {
        $modelName = $this->_modelName;
        $relationshipConfig = new Zend_Config_Yaml(
            APPLICATION_PATH . '/configs/observingRelationship.yaml'
        );
        if (isset($relationshipConfig->$modelName)) {
            return $relationshipConfig->$modelName->toArray();
        }

        return null;
    }
}
