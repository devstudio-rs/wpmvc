# Current User

Every application has a built-in `user` component wrapping the currently
logged-in WordPress user — available out of the box, no configuration
required:

```php
App::$app->user->is_guest;             // bool (also callable: is_guest())
App::$app->user->id;                   // int, 0 for guests
App::$app->user->identity;             // WP_User|null
App::$app->user->role;                 // primary role, null for guests
App::$app->user->roles;                // string[]
App::$app->user->has_role( 'editor' );
App::$app->user->can( 'edit_post', $post_id );  // current_user_can()
```

## Typical usage

```php
public function before_action() {
    if ( Theme::$app->user->is_guest ) {
        wp_safe_redirect( wp_login_url() );
        exit;
    }
}
```

```php
if ( Theme::$app->user->can( 'manage_options' ) ) {
    // admin-only branch
}
```

## Behavior notes

- Properties are read-only virtual attributes resolved via `get_*()`
  getters (`$user->is_guest` → `get_is_guest()`).
- All state is read live from WordPress — never cached — so it stays
  correct even when the current user changes mid-request
  (`wp_set_current_user()`).
- Like any component, it can be overridden per application via the
  `components` config key.
