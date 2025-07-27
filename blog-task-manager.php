<?php
/*
Plugin Name: Blog Task Manager
Description: Permite crear tareas para organizar la creación de entradas del blog.
Version: 1.0
Author: Codex Agent % The CreActives
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Registrar el tipo de contenido personalizado "blog_task".
 */
function btm_register_task_post_type() {
    $labels = [
        'name'               => 'Tareas',
        'singular_name'      => 'Tarea',
        'add_new'            => 'Añadir Nueva',
        'add_new_item'       => 'Añadir Nueva Tarea',
        'edit_item'          => 'Editar Tarea',
        'new_item'           => 'Nueva Tarea',
        'view_item'          => 'Ver Tarea',
        'search_items'       => 'Buscar Tareas',
        'not_found'          => 'No se encontraron tareas',
        'not_found_in_trash' => 'No hay tareas en la papelera',
        'menu_name'          => 'Tareas de Blog'
    ];

    $args = [
        'labels'             => $labels,
        'public'             => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'capability_type'    => 'post',
        'supports'           => [ 'title', 'editor' ],
        'menu_position'      => 25,
        'menu_icon'          => 'dashicons-clipboard',
    ];

    register_post_type( 'blog_task', $args );
}
add_action( 'init', 'btm_register_task_post_type' );

/**
 * Añade un metabox para la fecha de vencimiento de la tarea.
 */
function btm_add_due_date_metabox() {
    add_meta_box(
        'btm_due_date',
        'Fecha de vencimiento',
        'btm_due_date_metabox_callback',
        'blog_task',
        'side'
    );
}
add_action( 'add_meta_boxes', 'btm_add_due_date_metabox' );

/**
 * Mostrar el campo de fecha en el metabox.
 */
function btm_due_date_metabox_callback( $post ) {
    $value = get_post_meta( $post->ID, '_btm_due_date', true );
    echo '<label for="btm_due_date">Vence el:</label> ';
    echo '<input type="date" id="btm_due_date" name="btm_due_date" value="' . esc_attr( $value ) . '" />';
}

/**
 * Guardar la fecha de vencimiento.
 */
function btm_save_due_date( $post_id ) {
    if ( array_key_exists( 'btm_due_date', $_POST ) ) {
        update_post_meta( $post_id, '_btm_due_date', sanitize_text_field( $_POST['btm_due_date'] ) );
    }
}
add_action( 'save_post_blog_task', 'btm_save_due_date' );

/**
 * Flush rewrite rules al activar el plugin.
 */
function btm_activate() {
    btm_register_task_post_type();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'btm_activate' );

/**
 * Flush rewrite rules al desactivar el plugin.
 */
function btm_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'btm_deactivate' );
