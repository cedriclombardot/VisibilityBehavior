<?php

/**
 * @author Cedric LOMBARDOT <cedric.lombardot@gmail.com>
 */
class VisibilityBehavior extends Behavior
{
    /**
     * @var array
     */
    protected $parameters = array(
        'visibilities'        => array(),
        'default_visibility'  => null,
        'hierarchy'           => array(),
        'apply_to'            => array(),
    );

    /**
     * @var array
     */
    protected $visibilities;

    /**
     * @var array
     */
    protected $hierarchies;

    /**
     * @var array
     */
    protected $applyToFields;

    /**
     * @var VisibilityBehaviorObjectBuilderModifier
     */
    protected $objectBuilderModifier;

    /**
     * {@inheritdoc}
     */
    public function addParameter($attribute)
    {
        if ('hierarchy' === $attribute['name']) {
            $values = explode('|', $attribute['value']);

            if (1 < count($values)) {
                $this->parameters['hierarchy'] = $values;
            } else {
                $this->parameters['hierarchy'][] = $attribute['value'];
            }
        } else {
            parent::addParameter($attribute);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        $parameters  = parent::getParameters();
        $parameters['hierarchy'] = implode($parameters['hierarchy'], '|');

        return $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyTable()
    {
        $visibilities = $this->getVisibilities();
        $defaultValue = array_search($this->getDefaultVisibility(), $visibilities);

        foreach ($this->getApplyToFields() as $field) {
            if (!$this->getTable()->containsColumn($field.'_visibility')) {
                $column = array(
                    'name'          => $field.'_visibility',
                    'type'          => 'INTEGER',
                    'defaultValue'  => $defaultValue,
                );

                if ('true' === $this->getParameter('with_description')) {
                    $column['description'] = $this->generateVisibilityColumnComment();
                }

                $this->getTable()->addColumn($column);
            }
        }
    }

    /**
     * Generate a column comment containing a value-to-visibility map.
     *
     * @return string
     */
    protected function generateVisibilityColumnComment()
    {
        $comment = '';

        foreach ($this->getVisibilities() as $colValue => $visibility) {
            $comment .= sprintf("%d: %s\n", $colValue, $this->humanize($visibility));
        }

        return $comment;
    }

    public function getDefaultVisibility()
    {
        return strtolower($this->getParameter('default_visibility'));
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectBuilderModifier()
    {
        if (null === $this->objectBuilderModifier) {
            $this->objectBuilderModifier = new VisibilityObjectBuilderModifier($this);
        }

        return $this->objectBuilderModifier;
    }

    public function getVisibilities()
    {
        if (null === $this->visibilities) {
            $visibilities = array();
            foreach (explode(',', $this->getParameter('visibilities')) as $visibility) {
                $visibilities[] = strtolower(trim($visibility));
            }

            $this->visibilities = $visibilities;
        }

        return $this->visibilities;
    }

    public function getApplyToFields()
    {
        if (null === $this->applyToFields) {
            $fields = array();
            foreach (explode(',', $this->getParameter('apply_to')) as $field) {
                $fields[] = strtolower(trim($field));
            }

            $this->applyToFields = $fields;
        }

        return $this->applyToFields;
    }

    public function getHierarchies()
    {
        if (null === $this->hierarchies) {
            foreach ($this->getVisibilities() as $visibility) {
                $this->hierarchies[$visibility] = array($visibility);
            }

            foreach ($this->getParameter('hierarchy') as $hierarchy) {
                if (preg_match('#when it\'s visible for (?P<large>\w+) it\'s visible for (?P<short>\w+)#', $hierarchy, $matches)) {
                    $this->hierarchies[$matches['short']][] = $matches['large'];
                }
            }
        }

        return $this->hierarchies;
    }

    public function getVisibilityHierarchy($visibility)
    {
        $all = $this->getHierarchies();

        return $all[$visibility];
    }

    public function camelize($string)
    {
        return ucfirst(str_replace(' ', '', ucwords(strtr($string, '_-', '  '))));
    }

    public function humanize($string)
    {
        return ucwords(str_replace('_', ' ', $string));
    }
}