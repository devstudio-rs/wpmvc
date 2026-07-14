# Options

The `options` component builds admin settings pages from configuration and
provides typed access to `get_option()` / `update_option()`.

## Component setup

```php
'components' => array(
    'options' => array(
        'class' => \wpmvc\web\Options::class,
        'id'    => 'theme-options',
        'label' => __( 'Theme Options' ),
    ),
),
```

## Defining pages

Pages and their fields are defined via the `wpmvc_options` filter (or the
component's `config` property). Each entry becomes a submenu page under
the component's admin menu; each item becomes a registered setting with a
rendered field:

```php
add_filter( 'wpmvc_options', function ( $config ) {
    $config[] = array(
        'id'    => 'general',
        'label' => __( 'General' ),
        'items' => array(
            array(
                'name'    => 'company_name',
                'label'   => __( 'Company Name' ),
                'type'    => 'text',            // text, password, email, textarea, select
                'default' => '',
            ),
            array(
                'name'    => 'contact_mode',
                'label'   => __( 'Contact Mode' ),
                'type'    => 'select',
                'options' => array(
                    'email' => __( 'Email' ),
                    'phone' => __( 'Phone' ),
                ),
            ),
        ),
    );

    return $config;
} );
```

Individual items can be adjusted late via the per-option filter
`wpmvc_options_{name}`.

## Reading and writing

```php
use wpmvc\web\Options;

App::$app->options->get( 'company_name' );
App::$app->options->get( 'company_name', 'Acme' );                     // with default
App::$app->options->get( 'max_items', 10, Options::TYPE_INT );         // cast to int
App::$app->options->set( 'company_name', 'Acme d.o.o.' );
```

Available casts: `TYPE_STRING`, `TYPE_INT`, `TYPE_FLOAT`, `TYPE_BOOL`.
