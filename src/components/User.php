<?php

namespace wpmvc\components;

use wpmvc\base\Component;

/**
 * Class User
 *
 * Wraps the currently logged-in WordPress user. Available on every
 * application instance as `App::$app->user`. All state is resolved
 * live from WordPress (never cached), so it stays correct even when
 * the current user changes mid-request (e.g. `wp_set_current_user()`).
 *
 * @since 1.3.0
 * @package wpmvc\components
 *
 * @property-read bool $is_guest
 * @property-read int $id
 * @property-read \WP_User|null $identity
 * @property-read string|null $role
 * @property-read string[] $roles
 */

class User extends Component {

    /**
     * Expose getters as read-only virtual properties
     * (`$user->is_guest` -> `get_is_guest()`).
     *
     * @param string $name
     * @return mixed
     */
    public function __get( $name ) {
        $getter = 'get_' . $name;

        if ( method_exists( $this, $getter ) ) {
            return $this->{ $getter }();
        }

        return null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset( $name ) {
        return method_exists( $this, 'get_' . $name );
    }

    /**
     * @return bool
     */
    public function is_guest() : bool {
        return ! is_user_logged_in();
    }

    /**
     * @return bool
     */
    public function get_is_guest() : bool {
        return $this->is_guest();
    }

    /**
     * The currently logged-in user, or null for guests.
     *
     * @return \WP_User|null
     */
    public function get_identity() {
        return $this->is_guest() ? null : wp_get_current_user();
    }

    /**
     * ID of the current user, 0 for guests.
     *
     * @return int
     */
    public function get_id() : int {
        return get_current_user_id();
    }

    /**
     * Primary (first assigned) role of the current user.
     *
     * @return string|null
     */
    public function get_role() {
        $roles = $this->get_roles();

        return empty( $roles ) ? null : reset( $roles );
    }

    /**
     * All roles of the current user.
     *
     * @return string[]
     */
    public function get_roles() : array {
        $identity = $this->get_identity();

        return $identity instanceof \WP_User ?
            array_values( $identity->roles ) : array();
    }

    /**
     * @param string $role
     * @return bool
     */
    public function has_role( string $role ) : bool {
        return in_array( $role, $this->get_roles(), true );
    }

    /**
     * Whether the current user has a capability.
     *
     * @param string $capability
     * @param mixed ...$args Optional object ID or other args, as accepted by `current_user_can()`.
     * @return bool
     */
    public function can( string $capability, ...$args ) : bool {
        return current_user_can( $capability, ...$args );
    }

}
