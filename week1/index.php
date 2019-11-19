<?php
/**
 * Controller
 * User: reinardvandalen
 * Date: 05-11-18
 * Time: 15:25
 */

include 'model.php';

$pdo = connect_db('localhost', 'ddwt19_week1', 'ddwt19','ddwt19');


function count_series($pdo)
{
    $stmt = $pdo->prepare('SELECT * FROM series');
    $stmt->execute();
    $stmt->fetchAll();
    $result = $stmt->rowCount();
    return $result;
}

/* Landing page */
if (new_route('/DDWT19/week1/', 'get')) {
    /* Page info */
    $page_title = 'Home';
    $breadcrumbs = get_breadcrumbs([
        'DDWT19' => na('/DDWT19/', False),
        'Week 1' => na('/DDWT19/week1/', False),
        'Home' => na('/DDWT19/week1/', True)
    ]);
    $navigation = get_navigation([
        'Home' => na('/DDWT19/week1/', True),
        'Overview' => na('/DDWT19/week1/overview/', False),
        'Add Series' => na('/DDWT19/week1/add/', False)
    ]);

    /* Page content */
    $right_column = use_template('cards');
    $page_subtitle = 'The online platform to list your favorite series';
    $page_content = 'On Series Overview you can list your favorite series. You can see the favorite series of all Series Overview users. By sharing your favorite series, you can get inspired by others and explore new series.';

    /* Choose Template */
    include use_template('main');
}

/* Overview page */
elseif (new_route('/DDWT19/week1/overview/', 'get')) {
    /* Page info */
    $page_title = 'Overview';
    $breadcrumbs = get_breadcrumbs([
        'DDWT19' => na('/DDWT19/', False),
        'Week 1' => na('/DDWT19/week1/', False),
        'Overview' => na('/DDWT19/week1/overview', True)
    ]);
    $navigation = get_navigation([
        'Home' => na('/DDWT19/week1/', False),
        'Overview' => na('/DDWT19/week1/overview', True),
        'Add Series' => na('/DDWT19/week1/add/', False)
    ]);

    /* Page content */

    $right_column = use_template('cards');
    $page_subtitle = 'The overview of all series';
    $page_content = 'Here you find all series listed on Series Overview.';
    $left_content = get_serie_table(get_series($pdo));

    /* Choose Template */
    include use_template('main');
}

/* Single Serie */
elseif (new_route('/DDWT19/week1/serie/', 'get')) {
    /* Get series from db */
    $serie_id = $_GET['serie_id'];
    $series = get_series_info($pdo, $serie_id);
    $serie_name = $series['name'];
    $serie_abstract = $series['abstract'];
    $nbr_seasons = $series['seasons'];
    $creators = $series['creator'];

    /* Page info */
    $page_title = $serie_name;
    $breadcrumbs = get_breadcrumbs([
        'DDWT19' => na('/DDWT19/', False),
        'Week 1' => na('/DDWT19/week1/', False),
        'Overview' => na('/DDWT19/week1/overview/', False),
        $serie_name => na('/DDWT19/week1/serie/', True)
    ]);
    $navigation = get_navigation([
        'Home' => na('/DDWT19/week1/', False),
        'Overview' => na('/DDWT19/week1/overview', True),
        'Add Series' => na('/DDWT19/week1/add/', False)
    ]);

    /* Page content */
    $right_column = use_template('cards');
    $page_subtitle = sprintf("Information about %s", $serie_name);
    $page_content = $serie_abstract;

    /* Choose Template */
    include use_template('serie');
}

/* Add serie GET */
elseif (new_route('/DDWT19/week1/add/', 'get')) {
    /* Page info */
    $page_title = 'Add Series';
    $breadcrumbs = get_breadcrumbs([
        'DDWT19' => na('/DDWT19/', False),
        'Week 1' => na('/DDWT19/week1/', False),
        'Add Series' => na('/DDWT19/week1/new/', True)
    ]);
    $navigation = get_navigation([
        'Home' => na('/DDWT19/week1/', False),
        'Overview' => na('/DDWT19/week1/overview', False),
        'Add Series' => na('/DDWT19/week1/add/', True)
    ]);

    /* Page content */
    $right_column = use_template('cards');
    $page_subtitle = 'Add your favorite series';
    $page_content = 'Fill in the details of you favorite series.';
    $submit_btn = "Add Series";
    $form_action = '/DDWT19/week1/add/';


    /* Choose Template */
    include use_template('new');
}

/* Add serie POST */
elseif (new_route('/DDWT19/week1/add/', 'post')) {
    /* Page info */
    $page_title = 'Add Series';
    $breadcrumbs = get_breadcrumbs([
        'DDWT19' => na('/DDWT19/', False),
        'Week 1' => na('/DDWT19/week1/', False),
        'Add Series' => na('/DDWT19/week1/add/', True)
    ]);
    $navigation = get_navigation([
        'Home' => na('/DDWT19/week1/', False),
        'Overview' => na('/DDWT19/week1/overview', False),
        'Add Series' => na('/DDWT19/week1/add/', True)
    ]);

    /* Page content */
    $right_column = use_template('cards');
    $page_subtitle = 'Add your favorite series';
    $page_content = 'Fill in the details of you favorite series.';
    $submit_btn = "Add Series";
    $form_action = '/DDWT19/week1/add/';

    /* Add new series*/
    $result = add_series($pdo, $_POST['Name'], $_POST['Creator'], $_POST['Seasons'], $_POST['Abstract']);
    $error_msg='<div class="alert alert-'.$result['type'].'" role="alert">'.$result['message'].'</div>';

    include use_template('new');
}

/* Edit serie GET */
elseif (new_route('/DDWT19/week1/edit/', 'get')) {
    /* Get serie info from db */
    /* Get series from db */
    $serie_id = $_GET['serie_id'];
    $series = get_series_info($pdo, $serie_id);
    $serie_name = $series['name'];
    $serie_abstract = $series['abstract'];
    $nbr_seasons = $series['seasons'];
    $creators = $series['creator'];

    /* Page info */
    $page_title = 'Edit Series';
    $breadcrumbs = get_breadcrumbs([
        'DDWT19' => na('/DDWT19/', False),
        'Week 1' => na('/DDWT19/week1/', False),
        sprintf("Edit Series %s", $serie_name) => na('/DDWT19/week1/new/', True)
    ]);
    $navigation = get_navigation([
        'Home' => na('/DDWT19/week1/', False),
        'Overview' => na('/DDWT19/week1/overview', False),
        'Add Series' => na('/DDWT19/week1/add/', False)
    ]);

    /* Page content */
    $right_column = use_template('cards');
    $page_subtitle = sprintf("Edit %s", $serie_name);
    $page_content = 'Edit the series below.';
    $submit_btn = "Edit Series";
    $form_action = '/DDWT19/week1/edit/';

    /* Choose Template */
    include use_template('new');
}

/* Edit serie POST */
elseif (new_route('/DDWT19/week1/edit/', 'post')) {
    /* Get serie info from db */
    /* Get series from db */
    $serie_id = $_POST['Id'];
    $serie_name = $_POST['Name'];
    $serie_abstract = $_POST['Abstract'];
    $nbr_seasons = $_POST['Seasons'];
    $creators = $_POST['Creator'];

    /* Page info */
    $page_title = $serie_name;
    $breadcrumbs = get_breadcrumbs([
        'DDWT19' => na('/DDWT19/', False),
        'Week 1' => na('/DDWT19/week1/', False),
        'Overview' => na('/DDWT19/week1/overview/', False),
        $serie_name => na('/DDWT19/week1/serie/', True)
    ]);
    $navigation = get_navigation([
        'Home' => na('/DDWT19/week1/', False),
        'Overview' => na('/DDWT19/week1/overview', False),
        'Add Series' => na('/DDWT19/week1/add/', False)
    ]);

    /* Page content */
    $right_column = use_template('cards');
    $page_subtitle = sprintf("Information about %s", $serie_name);
    $page_content = $serie_abstract;

    /* Update entry*/
    $result = update_series($pdo, $_POST['Id'],$_POST['Name'], $_POST['Creator'], $_POST['Seasons'], $_POST['Abstract']);
    $error_msg='<div class="alert alert-'.$result['type'].'" role="alert">'.$result['message'].'</div>';

    /* Choose Template */
    include use_template('serie');

    $series = get_series_info($pdo, $serie_id);
}

/* Remove serie */
elseif (new_route('/DDWT19/week1/remove/', 'post')) {
    /* Remove serie in database */
    $serie_id = $_POST['serie_id'];
    $result = remove_serie($pdo, $serie_id);
    $error_msg='<div class="alert alert-'.$result['type'].'" role="alert">'.$result['message'].'</div>';

    /* Page info */
    $page_title = 'Overview';
    $breadcrumbs = get_breadcrumbs([
        'DDWT19' => na('/DDWT19/', False),
        'Week 1' => na('/DDWT19/week1/', False),
        'Overview' => na('/DDWT19/week1/overview', True)
    ]);
    $navigation = get_navigation([
        'Home' => na('/DDWT19/week1/', False),
        'Overview' => na('/DDWT19/week1/overview', True),
        'Add Series' => na('/DDWT19/week1/add/', False)
    ]);

    /* Page content */
    $right_column = use_template('cards');
    $page_subtitle = 'The overview of all series';
    $page_content = 'Here you find all series listed on Series Overview.';
    $left_content = get_serie_table(get_series($pdo));

    /* Choose Template */
    include use_template('main');
}

else {
    http_response_code(404);
}
