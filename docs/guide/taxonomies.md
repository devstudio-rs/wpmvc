# Taxonomy Models

Taxonomies follow the same pattern as [post models](/guide/models), via
`\wpmvc\models\Taxonomy_Model`. The extra piece is
`registry_object_type()`, which returns the post types the taxonomy is
attached to:

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
        );
    }

    public function registry_object_type() : array {
        return array( 'event' );
    }

}
```

## Registering

Same as post types:

```php
add_action( 'init', array( Event_Category::class, 'register' ) );
```

## Querying posts by taxonomy

```php
$events = Event::find()
    ->where_taxonomy( Event_Category::class, array( 'slug' => 'conferences' ) )
    ->all();
```
