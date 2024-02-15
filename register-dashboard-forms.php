<?php

use Models\User;

/**
 * Helper function - checks if role exists
 * @param $role
 * @return bool
 */
function role_exists( $role ) {
  if( ! empty( $role ) ) {
    return $GLOBALS['wp_roles']->is_role( $role );
  }
  
  return false;
}

/**
 * Helper function - send mail
 */
function send_mail($email, $subject, $body) {
  $headers = array('Content-Type: text/html; charset=UTF-8');
  $message = '
    <html>
      <head>
        <title>' . $subject . '</title>
      </head>
      <body>
        ' . $body . '
      </body>
    </html>
';

  wp_mail($email, $subject, $message, $headers);
}

add_action( 'admin_post_create_user', 'dashboard_create_user' );

/**
 * Handles the creation of a user from the dashboard
 * @return void
 */
function dashboard_create_user() {
    echo '<pre>';
    echo print_r($_POST);
    echo '</pre>';

    $login = $_POST['login'];
    $password = $_POST['password1'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    if ($_POST['password1'] != $_POST['password2']) {
        wp_redirect(
            add_query_arg(
                array(
                    'message' => 'danger',
                    'message_content' => 'Les mots de passe ne correspondent pas.'
                ),
                home_url('/users/create')
            )
        );

        exit;
    }
    
    if ($_POST['login'] == '') {
        wp_redirect(
            add_query_arg(
                array(
                    'message' => 'danger',
                    'message_content' => 'Le login est vide.'
                ),
                home_url('/users/create')
            )
        );

        exit;
    }

    if ($_POST['email'] == '') {
        wp_redirect(
            add_query_arg(
                array(
                    'message' => 'danger',
                    'message_content' => 'L\'email est vide.'
                ),
                home_url('/users/create')
            )
        );

        exit;
    }

    if ($_POST['role'] == '') {
        wp_redirect(
            add_query_arg(
                array(
                    'message' => 'danger',
                    'message_content' => 'Le rôle est vide.'
                ),
                home_url('/users/create')
            )
        );

        exit;
    }

    if (username_exists($login)) {
        wp_redirect(
            add_query_arg(
                array(
                    'message' => 'danger',
                    'message_content' => 'Le login est déjà utilisé.'
                ),
                home_url('/users/create')
            )
        );

        exit;
    }

    if (email_exists($email)) {
        wp_redirect(
            add_query_arg(
                array(
                    'message' => 'danger',
                    'message_content' => 'L\'email est déjà utilisé.'
                ),
                home_url('/users/create')
            )
        );

        exit;
    }

    if (is_email($email) == false) {
        wp_redirect(
            add_query_arg(
                array(
                    'message' => 'danger',
                    'message_content' => 'L\'email n\'est pas valide.'
                ),
                home_url('/users/create')
            )
        );

        exit;
    }

    if (!role_exists($role)) {
        wp_redirect(
            add_query_arg(
                array(
                    'message' => 'danger',
                    'message_content' => 'Le rôle n\'existe pas.'
                ),
                home_url('/users/create')
            )
        );

        exit;
    }

    $id = wp_create_user( $login, $password, $email );
    $user_id_role = new WP_User($id);
    $user_id_role->set_role($role);

    if (is_wp_error($id)) {
        wp_redirect(
            add_query_arg(
                array(
                    'message' => 'danger',
                    'message_content' => $id->get_error_message()
                ),
                home_url('/users/create')
            )
        );

        exit;
    }

    wp_redirect(
        add_query_arg(
            array(
                'message' => 'success',
                'message_content' => 'User created successfully'
            ),
            home_url('/creer-utilisateur')
        )
    );
}


add_action( 'admin_post_delete_me', 'dashboard_delete_me' );

/**
 * Handles the removal of the current user from the database (at his own request)
 * @return void
 */
function dashboard_delete_me() {
    $user = wp_get_current_user();
    $user_id = $user->ID;

    $code = $_POST['code'];
    $user_code = get_user_meta($user_id, 'delete_me_code', true);

    if ($code != $user_code) {
        wp_redirect(
            add_query_arg(
                array(
                    'message' => 'danger',
                    'message_content' => 'Le code est incorrect.'
                ),
                home_url('/users/delete-me')
            )
        );

        exit;
    }

    wp_delete_user($user_id);

    wp_redirect(
        add_query_arg(
            array(
                'message' => 'success',
                'message_content' => 'Votre compte a bien été supprimé.'
            ),
            home_url('/me')
        )
    );
}

add_action( 'admin_post_generate_deletion_codes', 'dashboard_generate_deletion_codes' );

/**
 * Handles the generation of deletion codes for the current user
 * @return void
 */
function dashboard_generate_deletion_codes() {
    $current_user = wp_get_current_user();

    if (empty($current_user)) {
        wp_redirect(
            add_query_arg(
                array(
                    'message' => 'danger',
                    'message_content' => 'Vous n\'êtes pas connecté.'
                ),
                home_url('/')
            )
        );

        exit;
    }

    if (!wp_check_password($_POST['password'], $current_user->user_pass, $current_user->ID)) {
        wp_redirect(
            add_query_arg(
                array(
                    'message' => 'danger',
                    'message_content' => 'Le mot de passe est incorrect.'
                ),
                home_url('/me')
            )
        );

        exit;
    }

    $code = wp_generate_password();

    /**
     * Adding the deletion code to the user meta, using WordPress API
     * @see https://codex.wordpress.org/Function_Reference/add_user_meta
     * @author Thomas Cardon
     */
    add_user_meta( $current_user->ID, 'delete_me_code', $code, true );

    send_mail(
        $current_user->user_email,
        'Écrans connectés | Supprimer votre compte: code de confirmation',
        '<p>Bonjour,</p>
        <p>Vous avez demandé à supprimer votre compte sur le site <a href="' . get_bloginfo('url') . '">' . get_bloginfo('name') . '</a>.</p>
        <p>Votre code de suppression est le suivant : <strong>' . $code . '</strong></p>
        <p>Vous pouvez le saisir sur la page <a href="' . get_bloginfo('url') . '/me">Mon compte</a>.</p>
        <p>Cordialement,</p>
        <p>L\'équipe ' . get_bloginfo('name') . '</p>'
    );

    wp_redirect(
        add_query_arg(
            array(
                'message' => 'success',
                'message_content' => 'Un code de suppression a été envoyé à votre adresse email.'
            ),
            home_url('/me')
        )
    );

    exit;
}

add_action( 'admin_post_modify_my_account', 'dashboard_modify_my_account' );

/**
 * Handles the modification of the current user's account
 */
function dashboard_modify_my_account() {
    $current_user = wp_get_current_user();

    if (empty($current_user)) {
        wp_redirect(
            add_query_arg(
                array(
                    'message' => 'danger',
                    'message_content' => 'Vous n\'êtes pas connecté.'
                ),
                home_url('/')
            )
        );

        exit;
    }

    if (!wp_check_password($_POST['old_password'], $current_user->user_pass, $current_user->ID)) {
        wp_redirect(
            add_query_arg(
                array(
                    'message' => 'danger',
                    'message_content' => 'Le mot de passe est incorrect.'
                ),
                home_url('/me')
            )
        );

        exit;
    }

    $user_id = $current_user->ID;

    $user_data = array(
        'ID' => $user_id,
        'user_pass' => $_POST['new_password']
    );

    wp_update_user($user_data);

    wp_redirect(
        add_query_arg(
            array(
                'message' => 'success',
                'message_content' => 'Votre compte a bien été modifié.'
            ),
            home_url('/me')
        )
    );
}

add_action( 'admin_post_modify_my_codes', 'dashboard_modify_my_codes' );

/**
 * Handles the modification of the current user's schedule
 */
function dashboard_modify_my_codes() {
    $codes = $_POST['codes'];
    
    $current_user = wp_get_current_user();
    $user = User::getById($current_user->ID);

    $user->deleteRelatedCodes();
    $user->addCodes($codes);

    wp_redirect(
        add_query_arg(
            array(
                'message' => 'success',
                'message_content' => 'Votre emploi du temps à bien été modifié.'
            ),
            home_url('/me')
        )
    );

    exit;
}