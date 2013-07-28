
/**
 * @return array
 */
public static function getAvailableVisibilities()
{
    return array(
<?php foreach ($visibilities as $visibility) : ?>
        static::VISIBILITY_<?php echo strtoupper($visibility) ?>,
<?php endforeach; ?>
    );
}
