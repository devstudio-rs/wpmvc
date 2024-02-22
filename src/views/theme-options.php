<?php

/**
 * @var array $options;
 * @var \wpmvc\models\Model $model;
 */

use wpmvc\helpers\Form;
use wpmvc\helpers\Html;

extract( $args );

?>

<div class="wrap" style="max-width: 600px;">
    <?php echo Form::start( array(
        'action' => 'options.php',
        'method' => 'post',
    ) ); ?>

    <?php settings_fields( $options['id'] ); ?>
    <?php do_settings_sections( $options['id'] ); ?>

    <h1><?php echo esc_html( $options['label'] ) ?></h1>

    <?php settings_errors(); ?>

    <div style="margin-top: 1rem;"></div>

    <?php foreach ( $options['items'] as $item ) : ?>
    <div class="form-group">
        <label for="<?php esc_attr( $item['name'] ); ?>" class="form-label">
            <?php echo $item['label']; ?>
        </label>

        <?php if ( in_array( $item['type'], array( 'text', 'password', 'email' ), true ) ) : ?>
            <?php echo Html::input( $item['type'], $item['name'], array(
                'id'    => $item['name'],
                'value' => $model->get_attribute( $item['name'] ),
                'class' => 'form-control',
            ) ); ?>
        <?php endif; ?>

        <?php if ( $item['type'] === 'textarea' ) : ?>
            <?php echo Html::textarea( $item['name'], $model->get_attribute( $item['name'] ), array(
                'id'    => $item['name'],
                'class' => 'form-control',
                'rows'  => 5,
            ) ); ?>
        <?php endif; ?>

        <?php if ( $item['type'] === 'select' ) : ?>
            <?php echo Html::select( $item['name'], $model->get_attribute( $item['name'] ), $item['options'], array(
                'id'    => $item['name'],
                'class' => 'form-control',
            ) ); ?>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>

    <?php submit_button( __( 'Save Options' ), 'primary', 'submit', false ); ?>

    <?php echo Form::end(); ?>
</div>
