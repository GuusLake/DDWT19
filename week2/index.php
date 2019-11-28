<?php
/**
 * Controller
 * User: reinardvandalen
 * Date: 05-11-18
 * Time: 15:25
 */

include 'model.php';
/* Connect to DB */
$db = connect_db('localhost', 'ddwt19_week2', 'ddwt19','ddwt19');
/* template for navigation */
$template = Array(
    1 => Array(
        'name' => 'Home',
        'url' => '/DDWT19/week2/'
    ),
    2 => Array(
        'name' => 'Overview',
        'url' => '/DDWT19/week2/overview/'
    ),
    3 => Array(
        'name' => 'Add series',
        'url' => '/DDWT19/week2/add/'
    ),
    4 => Array(
        'name' => 'My Account',
        'url' => '/DDWT19/week2/myaccount/'
    ),
    5 => Array(
        'name' => 'Register',
        'url' => '/DDWT19/week2/register/'
    ),
);

$nbr_series = count_series($db);
$nbr_users = count_users($db);
$right_column = use_template('cards');

/* Landing page */
if (new_route('/DDWT19/week2/', 'get')) {

    /* Page info */
    $page_title = 'Home';
    $breadcrumbs = get_breadcrumbs([
        'DDWT19' => na('/DDWT19/', False),
        'Week 2' => na('/DDWT19/week2/', False),
        'Home' => na('/DDWT19/week2/', True)
    ]);
    $navigation = get_navigation($template, 1);

    /* Page content */
    $page_subtitle = 'The online platform to list your favorite series';
    $page_content = 'On Series Overview you can list your favorite series. You can see the favorite series of all Series Overview users. By sharing your favorite series, you can get inspired by others and explore new series.';

    /* Get error msg from POST route */
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    };

    /* Choose Template */
    include use_template('main');
}

/* Overview page */
elseif (new_route('/DDWT19/week2/overview/', 'get')) {

    /* Page info */
    $page_title = 'Overview';
    $breadcrumbs = get_breadcrumbs([
        'DDWT19' => na('/DDWT19/', False),
        'Week 2' => na('/DDWT19/week2/', False),
        'Overview' => na('/DDWT19/week2/overview', True)
    ]);
    $navigation = get_navigation($template, 2);

    /* Page content */
    $page_subtitle = 'The overview of all series';
    $page_content = 'Here you find all series listed on Series Overview.';
    $left_content = get_serie_table($db, get_series($db));

    /* Get error msg from POST route */
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    };

    /* Choose Template */
    include use_template('main');
}

/* Single Serie */
elseif (new_route('/DDWT19/week2/serie/', 'get')) {

    /* Get series from db */
    $serie_id = $_GET['serie_id'];
    $serie_info = get_serieinfo($db, $serie_id);

    /*Check if user is creator*/
    if (check_login()) {
        if ($_SESSION['user_id'] == $serie_info['user']) {
            $display_buttons = true;
        } else {
            $display_buttons = false;
        }
    }else{
        $display_buttons = false;
    }

    /* Page info */
    $page_title = $serie_info['name'];
    $breadcrumbs = get_breadcrumbs([
        'DDWT19' => na('/DDWT19/', False),
        'Week 2' => na('/DDWT19/week2/', False),
        'Overview' => na('/DDWT19/week2/overview/', False),
        $serie_info['name'] => na('/DDWT19/week2/serie/?serie_id='.$serie_id, True)
    ]);
    $navigation = get_navigation($template, 2);

    /* Page content */
    $page_subtitle = sprintf("Information about %s", $serie_info['name']);
    $page_content = $serie_info['abstract'];
    $nbr_seasons = $serie_info['seasons'];
    $creators = $serie_info['creator'];
    $added_by = get_user_name($db, $serie_info['user']);

    /* Get error msg from POST route */
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    };

    /* Choose Template */
    include use_template('serie');
}

/* Add serie GET */
elseif (new_route('/DDWT19/week2/add/', 'get')) {
    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/DDWT19/week2/login/');
    }

    /* Page info */
    $page_title = 'Add Series';
    $breadcrumbs = get_breadcrumbs([
        'DDWT19' => na('/DDWT19/', False),
        'Week 2' => na('/DDWT19/week2/', False),
        'Add Series' => na('/DDWT19/week2/new/', True)
    ]);
    $navigation = get_navigation($template, 3);

    /* Page content */
    $page_subtitle = 'Add your favorite series';
    $page_content = 'Fill in the details of you favorite series.';
    $submit_btn = "Add Series";
    $form_action = '/DDWT19/week2/add/';

    /* Get error msg from POST route */
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    };

    /* Choose Template */
    include use_template('new');
}

/* Add serie POST */
elseif (new_route('/DDWT19/week2/add/', 'post')) {
    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/DDWT19/week2/login/');
    }

    /* Add serie to database */
    $feedback = add_serie($db, $_POST);

    /* Redirect to serie GET route */
    redirect(sprintf('/DDWT19/week2/add/?error_msg=%s',
        json_encode($feedback)));
}

/* Edit serie GET */
elseif (new_route('/DDWT19/week2/edit/', 'get')) {
    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/DDWT19/week2/login/');
    }

    /* Get serie info from db */
    $serie_id = $_GET['serie_id'];
    $serie_info = get_serieinfo($db, $serie_id);

    /* Page info */
    $page_title = 'Edit Series';
    $breadcrumbs = get_breadcrumbs([
        'DDWT19' => na('/DDWT19/', False),
        'Week 2' => na('/DDWT19/week2/', False),
        sprintf("Edit Series %s", $serie_info['name']) => na('/DDWT19/week2/new/', True)
    ]);
    $navigation = get_navigation($template, 2);

    /* Page content */
    $page_subtitle = sprintf("Edit %s", $serie_info['name']);
    $page_content = 'Edit the series below.';
    $submit_btn = "Edit Series";
    $form_action = '/DDWT19/week2/edit/';

    /* Choose Template */
    include use_template('new');
}

/* Edit serie POST */
elseif (new_route('/DDWT19/week2/edit/', 'post')) {
    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/DDWT19/week2/login/');
    }

    /* Add serie to database */
    $feedback = update_serie($db, $_POST);

    /* Redirect to serie GET route */
    redirect(sprintf('/DDWT19/week2/serie/?error_msg=%s&serie_id='.$_POST['serie_id'],
        json_encode($feedback)));
}

/* Remove serie */
elseif (new_route('/DDWT19/week2/remove/', 'post')) {
    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/DDWT19/week2/login/');
    }

    /* remove serie to database */
    $feedback = remove_serie($db, $_POST['serie_id']);

    /* Redirect to overview route */
    redirect(sprintf('/DDWT19/week2/overview/?error_msg=%s&serie_id='.$_POST['serie_id'],
        json_encode($feedback)));
}

/* My account GET */
elseif (new_route('/DDWT19/week2/myaccount/', 'get')) {
    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/DDWT19/week2/login/');
    }

    /* Page info */
    $page_title = 'My account';
    $breadcrumbs = get_breadcrumbs([
        'DDWT19' => na('/DDWT19/', False),
        'Week 2' => na('/DDWT19/week2/', False),
        'My account' => na('/DDWT19/week2/myaccount/', True)
    ]);
    $navigation = get_navigation($template, 4);
    $user = get_user_name($db, $_SESSION['user_id']);
    /* Page content */
    $page_subtitle = $user;
    $page_content = 'Overview your account below.';

    /* Get error msg from POST route */
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    };

    /* Choose Template */
    include use_template('account');
}

/* Register GET */
elseif (new_route('/DDWT19/week2/register/', 'get')) {

    /* Page info */
    $page_title = 'Register';
    $breadcrumbs = get_breadcrumbs([
        'DDWT19' => na('/DDWT19/', False),
        'Week 2' => na('/DDWT19/week2/', False),
        'Register' => na('/DDWT19/week2/register/', True)
    ]);
    $navigation = get_navigation($template, 5);

    /* Page content */
    $page_subtitle = 'Create an account';
    $page_content = 'Submit your information below.';

    /* Get error msg from POST route */
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    };

    /* Choose Template */
    include use_template('register');
}

/* Register POST */
elseif (new_route('/DDWT19/week2/register/', 'post')) {
    /* Register user */
    $error_msg = register_user($db, $_POST);

    /* Redirect to homepage */
    redirect(sprintf('/DDWT19/week2/register/?error_msg=%s',
        json_encode($error_msg)));
}

/* Log in GET */
elseif (new_route('/DDWT19/week2/login/', 'get')) {
    /* Check if logged in */
    if ( check_login() ) {
        redirect('/DDWT19/week2/myaccount/');
    }
    /* Page info */
    $page_title = 'Log in';
    $breadcrumbs = get_breadcrumbs([
        'DDWT19' => na('/DDWT19/', False),
        'Week 2' => na('/DDWT19/week2/', False),
        'Log in' => na('/DDWT19/week2/login/', True)
    ]);
    $navigation = get_navigation($template, 1);

    /* Page content */
    $page_subtitle = 'Log into your account';
    $page_content = 'Submit your information below.';

    /* Get error msg from POST route */
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    };

    /* Choose Template */
    include use_template('login');
}

/* login POST */
elseif (new_route('/DDWT19/week2/login/', 'post')) {
    /* add user to database */
    $feedback = login_user($db, $_POST);

    /* Redirect to overview route */
    redirect(sprintf('/DDWT19/week2/overview/?error_msg=%s',
        json_encode($feedback)));
}
/* Log out GET */
elseif (new_route('/DDWT19/week2/logout/', 'get')) {
    /* add user to database */
    $feedback = logout_user();

    /* Redirect to overview route */
    redirect(sprintf('/DDWT19/week2/?error_msg=%s',
        json_encode($feedback)));
}

else {
    http_response_code(404);
}
