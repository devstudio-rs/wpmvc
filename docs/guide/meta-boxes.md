# Meta Boxes

Meta boxes are declared on the post model and implemented as controllers.
`register()` wires everything — rendering, nonce, capability checks and
saving — automatically.

## The controller

In the common case only `on_action()` (render) is needed — the inherited
`on_save()` already loads request data into the model, validates it and
saves, showing validation errors as an admin notice after the redirect:

```php
use wpmvc\base\View;
use wpmvc\web\Meta_Box_Controller;

class Event_Options_Controller extends Meta_Box_Controller {

    public function on_action( $model ) {
        echo View::render( 'views/events/options', array(
            'model' => $model,
        ) );
    }

}
```

Override `on_save( $model )` only when custom persistence logic is needed.

## Declaring on the model

```php
class Event extends \wpmvc\models\Post_Model {

    public $post_type = 'event';

    protected function meta_boxes() : array {
        return array(
            array( Event_Options_Controller::class, __( 'Options' ) ),
        );
    }

}
```

Each `meta_boxes()` item is
`array( Controller_Class::class, $title, $args )` — title and args are
optional. When the title is omitted it is derived from the controller
class name (`Event_Options_Controller` → “Event Options”).

With `add_action( 'init', array( Event::class, 'register' ) )` in place,
the post type and its meta boxes are fully wired.

## Dynamic registration

For dynamic cases, meta boxes can still be added from the model's
`init()`:

```php
$this->add_meta_box( $controller, $title, $args );
```

## How saving works

Meta box IDs are deterministic (derived from the controller class name).
The save handler runs on `wp_after_insert_post` because
`Post_Model::save()` suppresses that hook — preventing recursion when the
handler itself saves the model. Validation errors are stashed and shown
as an admin notice after the post-save redirect.

## The view

Meta box views typically use the [Form helper](/guide/helpers) so field
names line up with `$model->load()`:

```php
<?php
/** @var \theme\models\Event $model */

echo \wpmvc\helpers\Form::field( $model, 'event_location' )->input_text();
echo \wpmvc\helpers\Form::field( $model, 'event_notes' )->textarea();
```
