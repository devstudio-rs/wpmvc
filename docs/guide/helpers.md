# Form & Html Helpers

## Form

`Form::field()` builds model-bound fields whose names line up with
`$model->load()` — fields are namespaced by the model's short class name
(`Event[event_location]`):

```php
use wpmvc\helpers\Form;

echo Form::start( array( 'method' => 'post' ) );

echo Form::field( $model, 'post_title' )->input_text();
echo Form::field( $model, 'event_location' )->input_text();
echo Form::field( $model, 'event_notes' )->textarea();
echo Form::field( $model, 'event_type' )->select( array(
    'conference' => __( 'Conference' ),
    'meetup'     => __( 'Meetup' ),
) );
echo Form::field( $model, 'event_public' )->checkbox();

echo Form::end();
```

Chainable field API:

| Method                          | Renders                          |
| ------------------------------- | -------------------------------- |
| `label( $label, $options )`     | custom `<label>`                 |
| `input( $type, $options )`      | arbitrary `<input>`              |
| `input_text( $options )`        | text input                       |
| `textarea( $options )`          | textarea                         |
| `select( $items, $options )`    | select from `value => label`     |
| `checkbox( $options )`          | checkbox                         |

Each field renders its label automatically, and validation errors for the
attribute appear with the field. Attribute labels come from the model's
`attribute_labels()`:

```php
public function attribute_labels() : array {
    return array(
        'event_location' => __( 'Location' ),
    );
}
```

On the receiving side, the controller loads the same structure back into
the model:

```php
$model->load( Theme::$app->request->post() );
$model->validate();
```

## Html

Lower-level tag builders, useful outside a model context:

```php
use wpmvc\helpers\Html;

echo Html::tag( 'div', 'content', array( 'class' => 'box' ) );
echo Html::input( 'text', 'company', array( 'value' => $value ) );
echo Html::textarea( 'notes', $value, array( 'rows' => 5 ) );
echo Html::select( 'mode', $selected, array( 'a' => 'A', 'b' => 'B' ) );
echo Html::checkbox( 'active', 1, $checked );
```

Each also has a model-bound `active_*` variant
(`Html::active_input( $model, $attribute, ... )`), which the Form helper
uses under the hood.
