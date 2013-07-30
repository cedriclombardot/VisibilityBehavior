
<?php foreach ($hydrates as $hydrate) : ?>
 /**
 * @return <?php echo $objectClass ?> reload current object but with null if the field is not visible for '<?php echo $hydrate['visibilityLabel'] ?>'
 * @throws PropelException
 */
public function <?php echo $hydrate['methodName'] ?>()
{
    $this->reload();

    <?php foreach ($hydrate['isVisibleMethods'] as $field => $method) : ?>if (!$this-><?php echo $method ?>()) {
        $this-><?php echo $hydrate['fieldsSetter'][$field] ?>(null);
    }
    
    <?php endforeach; ?>

    $this->resetModified();

    return $this;
}

<?php endforeach; ?>