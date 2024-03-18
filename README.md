# wpmvc

## App

Initialize application.

```php
$config = array(
    'components' => array(
        'request' => array(
            'class' => Request::class,
        ),
    ),
);

( new App( $config ) )->init();
```

Application and it's componenets will be availavable:

```php
App::$app
App::$app->request->post();
```

## Models

### Post Models

```php
class Event extends \wpmvc\models\Post_Model {

    public $post_type = 'event';
    
    // Example of custom attributes with default values.
    public $event_date     = 1707422767;
    public $event_location = 'Bratislava';
    
    public function registry() : array{
        return array(
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            ...
        );
    }
    
    public function registry_labels() : array {
        return array(
            'name' => __( 'Event' ),
            ...
        );
    }

}
```

Ways to use models. Set attributes, save or delete.

```php
$event = Event::find_one( 24 );
$event = new Event();

$event->set_attribute( 'post_title', 'Great Event' );
$event->set_attribute( 'event_location', 'Bratislava' );

$event->set_attributes( array( 
    'post_title'     => 'Great Event',
    'event_location' => 'Bratislava',
) );

$event->load( array( 
    'Event' => array(
        'post_title'     => 'Great Event',
        'event_location' => 'Bratislava',
    ),
) );

$event->save();
$event->delete();
```

#### Register custom post type with post model

Register custom post type using `init` WordPres action.

```php
Event::register( array(
    'labels' => array(
        'name' => __( 'Events' ),
        ...
    ),
) );
```

### Taxonomy Models

```php
class Event_Category extends \wpmvc\models\Taxonomy_Model {

    public $taxonomy = 'event-category';

    public function registry() : array {
        return array(
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'show_in_rest'      => true,
        );
    }

    public function registry_labels() : array {
        return array(
            'name' => __( 'Event Category' ),
            ...
        );
    }

    public function registry_object_type() : array {
        return array( 'event' );
    }

}
```

Search posts by taxonomy.

```php
$events = Event::find()
    ->where_taxonomy( Event_Category::class, array( 'slug' => 'taxonomy_slug' ) )
    ->all();
```

## Controllers

```php
class Site_Controller extends \wpmvc\web\Controller {

    public function action_index() {
        // Action logic.
    }

}
```

Register controller.

```php
App::$app->router->add_route( 'site', array( Site_Controller::class, 'action_index' ) );
```

The registered action will be available at `{host}/site/`.

## Meta boxes

Defines meta box controller.

```php
class Event_Options_Controller extends \wpmvc\web\Meta_Box_Controller {

    public function on_action( $model ) {
        echo View::render( 'views/events/meta-box', array(
            'model' => $model,
        ) );
    }

    public function on_save( $model ) {
        if ( $model->load( App::$app->request->post() ) ) {
            $model->save();
        }
    }

}
```

Define meta box model.

```php
class Event_Options_Meta_Box extends \wpmvc\models\Meta_Box_Model {

    public $id         = 'event_options';
    public $controller = Event_Options_Controller::class;

    public function init() {
        $this->set_title( __( 'Options' ) );
    }

}
```

Add meta box model to the post model.

```php
class Event extends \wpmvc\models\Post_Model {

    public $post_type = 'event';
    
    public function init() {
        $this->add_meta_box( Event_Options_Meta_Box::class );
    }

}
```


