<?php

/**
 * @author Cedric LOMBARDOT <cedric.lombardot@gmail.com>
 */
class VisibilityObjectBuilderModifier
{
    /**
     * @var VisibilityBehavior
     */
    private $behavior;

    public function __construct(Behavior $behavior)
    {
        $this->behavior = $behavior;
    }

    public function objectAttributes($builder)
    {
        return $this->behavior->renderTemplate('objectConstants', array(
            'visibilities'            => $this->behavior->getVisibilities(),
        ));
    }

    public function addGetAvailableVisibilities($builder)
    {
        return $this->behavior->renderTemplate('objectGetAvailableVisibilities', array(
            'visibilities'            => $this->behavior->getVisibilities(),
        ));
    }

    public function addGetNormalizedVisibilities($builder)
    {
        return $this->behavior->renderTemplate('objectGetNormalizedVisibilities', array(
            'visibilities'            => $this->behavior->getVisibilities(),
        ));
    }

    public function addIssers($builder)
    {
        $issers = array();

        foreach ($this->behavior->getApplyToFields() as $field) {
            foreach ($this->behavior->getVisibilities() as $visibility) {
                $constants = array();
                foreach ($this->behavior->getVisibilityHierarchy($visibility) as $accepted) {
                    $constants[] = $this->getVisibilityConstant($accepted);
                }

                $issers[] = array(
                    'methodName'       => $this->getVisibilityIsser($field, $visibility),
                    'visibilityMethod' => $this->getVisibilityGetterFor($field),
                    'constants'        => $constants,
                );
            }
        }

        return $this->behavior->renderTemplate('objectIssers', array(
            'issers'            => $issers,
        ));
    }

    public function addCopy($builder)
    {
        $copies = array();

        foreach ($this->behavior->getVisibilities() as $visibility) {
            $copies[] = array(
                'methodName'       => $this->getVisibilityCopier($visibility),
                'visibility'       => $this->getVisibilityConstant($visibility),
                'visibilityLabel'  => $visibility,
                'fieldsSetter'     => $this->getApplyToSetters(),
                'isVisibleMethods' => $this->getVisibilityIssers($visibility),
            );
        }

        return $this->behavior->renderTemplate('objectCopyAs', array(
            'copies'            => $copies,
            'objectClass'       => $this->behavior->getTable()->getPhpName(),
        ));
    }

    public function addHydrate($builder)
    {
        $hydrates = array();

        foreach ($this->behavior->getVisibilities() as $visibility) {
            $hydrates[] = array(
                'methodName'       => $this->getVisibilityHydrater($visibility),
                'visibility'       => $this->getVisibilityConstant($visibility),
                'visibilityLabel'  => $visibility,
                'fieldsSetter'     => $this->getApplyToSetters(),
                'isVisibleMethods' => $this->getVisibilityIssers($visibility),
            );
        }

        return $this->behavior->renderTemplate('objectHydrateAs', array(
            'hydrates'          => $hydrates,
            'objectClass'       => $this->behavior->getTable()->getPhpName(),
        ));
    }

    public function objectMethods($builder)
    {
        $script  = '';
        $script .= $this->addGetAvailableVisibilities($builder);
        $script .= $this->addGetNormalizedVisibilities($builder);
        $script .= $this->addIssers($builder);
        $script .= $this->addCopy($builder);
        $script .= $this->addHydrate($builder);

        return $script;
    }

    protected function getApplyToSetters()
    {
        $getters = array();

        foreach ($this->behavior->getApplyToFields() as $field) {
            $getters[$field] = 'set'.$this->behavior->getTable()->getColumn($field)->getPhpName();            
        }

        return $getters;
    }

    protected function getVisibilityIssers($visibility)
    {
        $issers = array();

        foreach ($this->behavior->getApplyToFields() as $field) {
            $issers[$field] = $this->getVisibilityIsser($field, $visibility);            
        }

        return $issers;
    }

    protected function getVisibilityIsser($field, $visibility)
    {
        return 'is'.$this->behavior->camelize($field).'VisibleFor' . $this->behavior->camelize($visibility);
    }

    protected function getVisibilityGetterFor($field)
    {
        return 'get'.$this->behavior->camelize($field).'Visibility';
    }

    protected function getVisibilityCopier($visibility)
    {
        return 'copyAsVisibleFor'. $this->behavior->camelize($visibility);
    }

    protected function getVisibilityHydrater($visibility)
    {
        return 'reloadAsVisibleFor'. $this->behavior->camelize($visibility);
    }

    protected function getVisibilityConstant($visibility)
    {
        return 'VISIBILITY_'.strtoupper($visibility);
    }
}