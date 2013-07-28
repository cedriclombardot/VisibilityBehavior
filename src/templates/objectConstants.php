<?php foreach ($visibilities as $i => $visibility) : ?>
/**
 * This constant represents the actual database value for the '<?php echo $visibility ?>' visibility.
 *
 * @var int
 */
const VISIBILITY_<?php echo strtoupper($visibility) ?> = <?php echo $i ?>;

/**
 * This constant represents the named visibility for the '<?php echo $visibility ?>' visibility.
 *
 * @var string
 */
const VISIBILITY_NORMALIZED_<?php echo strtoupper($visibility) ?> = "<?php echo $visibility ?>";

<?php endforeach;
