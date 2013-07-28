
<?php foreach ($issers as $isser) : ?>
/**
 * @return boolean
 */
public function <?php echo $isser['methodName'] ?>()
{
    $acceptedVisibilities = array(<?php foreach ($isser['constants'] as $constant) :?>static::<?php echo $constant ?>, <?php endforeach ?>);

    return in_array($this-><?php echo $isser['visibilityMethod'] ?>(), $acceptedVisibilities);
}

<?php endforeach; ?>