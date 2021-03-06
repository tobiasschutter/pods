<?php
$time_format = array(
    'h_mm_A' => 'h:mm:ss TT',
    'h_mm_ss_A' => 'h:mm TT',
    'hh_mm_A' => 'hh:mm TT',
    'hh_mm_ss_A' => 'hh:mm:ss TT',
    'h_mma' => 'h:mmtt',
    'hh_mma' => 'hh:mmtt',
    'h_mm' => 'h:mm',
    'h_mm_ss' => 'h:mm:ss',
    'hh_mm' => 'hh:mm',
    'hh_mm_ss' => 'hh:mm:ss'
);

wp_enqueue_script( 'jquery-ui-datepicker' );
wp_enqueue_script( 'jquery-ui-timepicker' );
wp_enqueue_style( 'jquery-ui' );
wp_enqueue_style( 'jquery-ui-timepicker' );

$attributes = array();

$type = 'text';

$date_type = 'time';

if ( 1 == pods_var( 'time_html5', $options ) )
    $type = $date_type;

$attributes[ 'type' ] = $type;
$attributes[ 'tabindex' ] = 2;

$format = PodsForm::field_method( 'time', 'format', $options );

$method = 'timepicker';

$args = array(
    'timeFormat' => $time_format[ pods_var( 'time_format', $options, 'h_mma', null, true ) ]
);

if ( false !== stripos( $args[ 'timeFormat' ], 'tt' ) )
    $args[ 'ampm' ] = true;

$html5_format = '\TH:i:s';

if ( 24 == pods_var( 'time_type', $options, 12 ) )
    $args[ 'ampm' ] = false;

$date = PodsForm::field_method( 'time', 'createFromFormat', $format, (string) $value );
$date_default = PodsForm::field_method( 'time', 'createFromFormat', 'H:i:s', (string) $value );

if ( 'text' != $type && ( 0 == pods_var( 'time_allow_empty', $options, 1 ) || !in_array( $value, array( '0000-00-00', '0000-00-00 00:00:00', '00:00:00' ) ) ) ) {
    $formatted_date = $value;

    if ( false !== $date )
        $value = $date->format( $html5_format );
    elseif ( false !== $date_default )
        $value = $date_default->format( $html5_format );
    elseif ( !empty( $value ) )
        $value = date_i18n( $html5_format, strtotime( (string) $value ) );
    else
        $value = date_i18n( $html5_format );
}

$args = apply_filters( 'pods_form_ui_field_time_args', $args, $type, $options, $attributes, $name, PodsForm::$field_type );

$attributes[ 'value' ] = $value;

$attributes = PodsForm::merge_attributes( $attributes, $name, PodsForm::$field_type, $options );
?>
<input<?php PodsForm::attributes( $attributes, $name, PodsForm::$field_type, $options ); ?> />
<script>
    jQuery( function () {
        var <?php echo pods_clean_name( $attributes[ 'id' ] ); ?>_args = <?php echo json_encode( $args ); ?>;

    <?php
    if ( 'text' != $type ) {
        ?>

        if ( 'undefined' == typeof pods_test_date_field_<?php echo $type; ?> ) {
            // Test whether or not the browser supports date inputs
            function pods_test_date_field_<?php echo $type; ?> () {
                var input = jQuery( '<input/>', {
                    'type' : '<?php echo $type; ?>',
                    css : {
                        position : 'absolute',
                        display : 'none'
                    }
                } );

                jQuery( 'body' ).append( input );

                var bool = input.prop( 'type' ) !== 'text';

                if ( bool ) {
                    var smile = ":)";
                    input.val( smile );

                    return (input.val() != smile);
                }
            }
        }

        if ( !pods_test_date_field_<?php echo $type; ?>() ) {
            jQuery( 'input#<?php echo $attributes[ 'id' ]; ?>' ).val( '<?php echo $formatted_date; ?>' );
            jQuery( 'input#<?php echo $attributes[ 'id' ]; ?>' ).<?php echo $method; ?>( <?php echo pods_clean_name( $attributes[ 'id' ] ); ?>_args );
        }

        <?php
    }
    else {
        ?>

        jQuery( 'input#<?php echo $attributes[ 'id' ]; ?>' ).<?php echo $method; ?>( <?php echo pods_clean_name( $attributes[ 'id' ] ); ?>_args );

        <?php
    }
    ?>
    } );
</script>
