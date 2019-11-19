<?php
/**
 * Model
 * User: reinardvandalen
 * Date: 05-11-18
 * Time: 15:25
 */

/* Enable error reporting */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * Check if the route exist
 * @param string $route_uri URI to be matched
 * @param string $request_type request method
 * @return bool
 *
 */
function new_route($route_uri, $request_type){
    $route_uri_expl = array_filter(explode('/', $route_uri));
    $current_path_expl = array_filter(explode('/',parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
    if ($route_uri_expl == $current_path_expl && $_SERVER['REQUEST_METHOD'] == strtoupper($request_type)) {
        return True;
    }
}

/**
 * Creates a new navigation array item using url and active status
 * @param string $url The url of the navigation item
 * @param bool $active Set the navigation item to active or inactive
 * @return array
 */
function na($url, $active){
    return [$url, $active];
}

/**
 * Creates filename to the template
 * @param string $template filename of the template without extension
 * @return string
 */
function use_template($template){
    $template_doc = sprintf("views/%s.php", $template);
    return $template_doc;
}

/**
 * Creates breadcrumb HTML code using given array
 * @param array $breadcrumbs Array with as Key the page name and as Value the corresponding url
 * @return string html code that represents the breadcrumbs
 */
function get_breadcrumbs($breadcrumbs) {
    $breadcrumbs_exp = '<nav aria-label="breadcrumb">';
    $breadcrumbs_exp .= '<ol class="breadcrumb">';
    foreach ($breadcrumbs as $name => $info) {
        if ($info[1]){
            $breadcrumbs_exp .= '<li class="breadcrumb-item active" aria-current="page">'.$name.'</li>';
        }else{
            $breadcrumbs_exp .= '<li class="breadcrumb-item"><a href="'.$info[0].'">'.$name.'</a></li>';
        }
    }
    $breadcrumbs_exp .= '</ol>';
    $breadcrumbs_exp .= '</nav>';
    return $breadcrumbs_exp;
}

/**
 * Creates navigation HTML code using given array
 * @param array $navigation Array with as Key the page name and as Value the corresponding url
 * @return string html code that represents the navigation
 */
function get_navigation($navigation){
    $navigation_exp = '<nav class="navbar navbar-expand-lg navbar-light bg-light">';
    $navigation_exp .= '<a class="navbar-brand">Series Overview</a>';
    $navigation_exp .= '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">';
    $navigation_exp .= '<span class="navbar-toggler-icon"></span>';
    $navigation_exp .= '</button>';
    $navigation_exp .= '<div class="collapse navbar-collapse" id="navbarSupportedContent">';
    $navigation_exp .= '<ul class="navbar-nav mr-auto">';
    foreach ($navigation as $name => $info) {
        if ($info[1]){
            $navigation_exp .= '<li class="nav-item active">';
            $navigation_exp .= '<a class="nav-link" href="'.$info[0].'">'.$name.'</a>';
        }else{
            $navigation_exp .= '<li class="nav-item">';
            $navigation_exp .= '<a class="nav-link" href="'.$info[0].'">'.$name.'</a>';
        }

        $navigation_exp .= '</li>';
    }
    $navigation_exp .= '</ul>';
    $navigation_exp .= '</div>';
    $navigation_exp .= '</nav>';
    return $navigation_exp;
}

/**
 * Pritty Print Array
 * @param $input
 */
function p_print($input){
    echo '<pre>';
    print_r($input);
    echo '</pre>';
}

/**
 * Creates HTML alert code with information about the success or failure
 * @param array $feedback Array with keys 'type' and 'message'.
 * @return string
 */
function get_error($feedback){
    $error_exp = '
        <div class="alert alert-'.$feedback['type'].'" role="alert">
            '.$feedback['message'].'
        </div>';
    return $error_exp;
}

/**
 * connects to the database
 * @param string host
 * @param string database
 * @param string username
 * @param string password
 * @return DPO object
 */
function connect_db($host, $db, $user, $pass){
    $charset = 'utf8mb4';
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, ];
    try { $pdo = new PDO($dsn, $user, $pass, $options); }
    catch (\PDOException $e) {
        echo sprintf("Failed to connect. %s",$e->getMessage()); }
        return $pdo;
}

/**
* Creates an array of all the series listed
* @param PDO object
* @return array
*/
function get_series($pdo){
    $stmt = $pdo->prepare('SELECT * FROM series');
    $stmt->execute();
    $series= $stmt->fetchAll();
    $series_exp = Array();

    foreach ($series as $key => $value){
        foreach ($value as $user_key => $user_input){
            $series_exp[$key][$user_key] = htmlspecialchars($user_input);
        }
    }
    return $series_exp;
}
/**
 * Creates a table of all series from the array from get_series()
 * @param array the array of series listed on the website
 * @return string html code that represents a table
 */
function get_serie_table($series){
    $table_exp = '
    <table class="table table-hover">
    <thead>
    <tr>
    <th scope="col">Series</th>
    <th scope="col"></th> </tr>
    </thead>
    <tbody>';
    foreach($series as $key => $value){
        $table_exp .= ' 
        <tr>
        <th scope="row">'.$value['name'].'</th>
        <td>
        <a href="/DDWT19/week1/serie/?serie_id='.$value['id'].'" role="button" class="btn btn-primary">More info</a>
        </td>
        </tr> ';
    }
    $table_exp .= '
    </tbody>
    </table> ';
    return $table_exp;
}

/**
 * Fetches info on a serie from the database
 * @param PDO object
 * @param int the id of the series
 * @return array containing all information about a series
 */
function get_series_info($pdo, $id){
    $stmt = $pdo->prepare("SELECT * FROM series WHERE id=?");
    $stmt->execute([$id]);
    $series= $stmt->fetch();

    return $series;
}

/**
 * Adds a series to the database
 * @param PDO object
 * @param string name of the series
 * @param string name of the creator
 * @param int number of seasons
 * @param string abstract of the series
 * @return array of error or success messages
 */
function add_series($pdo, $name, $creator, $seasons, $abstract){
    /* Check data type */
    if (!is_numeric($seasons)) {
        return [
            'type' => 'danger',
            'message' => 'There was an error. You should enter a number in the field Seasons.'
        ];
    }
    /* Check if all fields are set */
    if (
        empty($name) or
        empty($creator) or
        empty($seasons) or
        empty($abstract)
    ) {
        return [
            'type' => 'danger',
            'message' => 'There was an error. Not all fields were filled in.'
        ];
    }

    /* Check if serie already exists */
    $stmt = $pdo->prepare('SELECT * FROM series WHERE name = ?');
    $stmt->execute([$name]);
    $serie = $stmt->rowCount();
    if ($serie){
        return [
            'type' => 'danger',
            'message' => 'This series was already added.'
        ];
    }

    $stmt = $pdo->prepare("INSERT INTO series (name, creator, seasons, abstract) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $creator, $seasons, $abstract]);
    $inserted = $stmt->rowCount();
    if ($inserted == 1){
        return[
            'type'=> 'success',
            'message' => sprintf("Series '%s' added to Series Overview.", $name)
        ];
    }else{
        return[
            'type'=> 'danger',
            'message' => 'There was an error. The series was not added. Try it again.'
        ];
    }
}

/**
 * Edits a series in the database
 * @param PDO object
 * @param int id of the series
 * @param string name of the series
 * @param string name of the creator
 * @param int number of seasons
 * @param string abstract of the series
 * @return array of error or success messages
 */
function update_series($pdo, $id, $name, $creator, $seasons, $abstract){
    /* Check data type */
    if (!is_numeric($seasons)) {
        return [
            'type' => 'danger',
            'message' => 'There was an error. You should enter a number in the field Seasons.'
        ];
    }
    /* Check if all fields are set */
    if (
        empty($name) or
        empty($creator) or
        empty($seasons) or
        empty($abstract)
    ) {
        return [
            'type' => 'danger',
            'message' => 'There was an error. Not all fields were filled in.'
        ];
    }

    /* Check if serie already exists */
    $stmt = $pdo->prepare('SELECT * FROM series WHERE (name = ? AND id != ?)');
    $stmt->execute([$name, $id]);
    $serie = $stmt->rowCount();
    if ($serie){
        return [
            'type' => 'danger',
            'message' => 'This name was already added.'
        ];
    }

    $stmt = $pdo->prepare("UPDATE series SET name=?, creator=?, seasons=?, abstract=? WHERE id = ?");
    $stmt->execute([$name, $creator, $seasons, $abstract, $id]);
    $inserted = $stmt->rowCount();
    if ($inserted == 1){
        return[
            'type'=> 'success',
            'message' => sprintf("Series '%s' has been changed.", $name)
        ];
    }else{
        return[
            'type'=> 'danger',
            'message' => 'There was an error. The series was not added. Try it again.'
        ];
    }
}

/**
 * Removes a series from the database
 * @param PDO object
 * @param int id of the series
 * @return array of error or success messages
 */
function remove_serie($pdo, $id){
    /* Get series info */
    $serie_info = get_series_info($pdo, $id);

    /* Delete series*/
    $stmt = $pdo->prepare("DELETE FROM series WHERE id = ?");
    $stmt->execute([$id]);
    $deleted = $stmt->rowCount();
    if ($deleted ==  1) {
        return [
            'type' => 'success',
            'message' => sprintf("Series '%s' was removed!", $serie_info['name'])
        ];
    } else {
        return [
            'type' => 'warning',
            'message' => 'An error occurred. The series was not removed.'
        ];
    }
}