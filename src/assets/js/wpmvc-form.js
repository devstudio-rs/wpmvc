( function( $, document ) {

    $( document ).on( 'ready', function() {
        $( '.wpmvc-form' ).wpmvc_form();
    } );

    $.fn.wpmvc_form = function( options ) {
        let _self   = this;

        this.options = $.extend( {
            action: '',
            method: '',
            ajax: false,
            field_error_class:     'has-error',
            field_help_class:      'help-block',
            success_message_class: 'success-message',
        }, options );

        this.init = function() {
            this.options.action = $( this ).attr( 'action' );
            this.options.method = $( this ).attr( 'method' );

            this.events();
            this.loaded();
        };

        this.events = function () {
            $( this ).on( 'submit', this.on_submit );
            $( '[data-attribute] :input', this ).on( 'change', this.on_input_change );
        }

        this.loaded = function () {
            $( this ).removeAttr( 'onsubmit' );
        };

        this.on_submit = function ( e ) {
            e.preventDefault();

            _self.reset_form();
            _self.toggle_submit_button( true );

            let data  = {};

            $.each( $( this ).serializeArray(), function( i, field ) {
                data[ field.name ] = field.value;
            } );

            $.ajax( {
                method:   _self.options.method,
                url:      _self.options.action,
                data:     data,
                dataType: "json"
            } )
                .done( _self.process_response )
                .always( function() {
                    _self.process_always();
                    _self.toggle_submit_button( false );
                } );

            return true;
        }

        this.on_input_change = function( e ) {
            let attribute_elem = $( this )
                .parents( '[data-attribute]' );

            attribute_elem
                .removeClass( _self.options.field_error_class );

            $( '.' + _self.options.field_help_class, attribute_elem )
                .remove();
        }

        this.process_response = function( response ) {
            _self.trigger( 'response', response );

            if ( response.success ) {
                _self.process_success_data( response.data );
                _self.toggle_success_message( true );
            }

            if ( ! response.success ) {
                _self.process_error_data( response.data );
            }
        }

        this.process_always = function() {
            _self.trigger( 'onsubmit_always' );
        }

        this.process_success_data = function ( data ) {}

        this.process_error_data = function ( data ) {
            $.each( data, function ( i, item ) {
                if ( typeof item.attribute === 'undefined' ||
                     typeof item.messages === 'undefined' ) {
                    return;
                }

                let field = $( '[data-attribute="' + item.attribute + '"]', _self );

                field
                    .addClass( _self.options.field_error_class );

                field.append(
                    $( '<div>', { class: _self.options.field_help_class } )
                        .html( item.messages[0] )
                );
            } );
        }

        this.reset_form = function () {
            _self.reset_field_error_classes();
            _self.reset_field_help_messages();
            _self.toggle_success_message( false );
        }

        this.reset_field_error_classes = function () {
            $( '[data-attribute]', _self )
                .removeClass( _self.options.field_error_class );
        }

        this.reset_field_help_messages = function () {
            $( '.' + _self.options.field_help_class, _self )
                .remove();
        }

        this.toggle_success_message = function ( show ) {
            let success_message = $( '.' + _self.options.success_message_class, _self );

            show ?
                success_message.show() :
                success_message.hide();
        }

        this.toggle_submit_button = function ( enabled ) {
            $( '[type="submit"]', _self )
                .prop( 'disabled', enabled )
        }

        if ( this.length > 1 ){
            this.each( function () {
                $( this ).wpmvc_form();
            } );

            return this;
        }

        return this.init();
    };

} )( jQuery, document );
