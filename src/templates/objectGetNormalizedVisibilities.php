/**
 * @return array
 */
public static function getNormalizedVisibilities()
{
    return array(
<?php foreach ($visibilities as $visibility) : ?>
        static::VISIBILITY_NORMALIZED_<?php echo strtoupper($visibility) ?>,
<?php endforeach; ?>
    );
}
