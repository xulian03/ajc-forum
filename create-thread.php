<?php
  session_start();
  date_default_timezone_set('America/Bogota');

  class Thread {
    public $id;
    public $user;
    public $title;
    public $content;
    public $survey;
    public $images;
    public $likes;
    public $date;
    public $open;

    public function __construct(
      $id, 
      $user, 
      $title,
      $content,
      $survey,
      $images,
      $likes,
      $date,
      $open
    ) {
        $this->id = $id;
        $this->user = $user;
        $this->title = $title;
        $this->content = $content;
        $this->survey = $survey;
        $this->images = $images;
        $this->likes = $likes;
        $this->date = $date;
        $this->open = $open;
    }
}

  class User {
      public $id;
      public $document;
      public $name;
      public $icon;
      public $cover;
      public $description;
      public $verified;
      public $likes = array();
      public $surveys = array(); 
      public $threads = array();

      public function __construct(
        $id, 
        $document, 
        $name,
        $icon,
        $cover,
        $description,
        $verified,
        $likes,
        $surveys,
        $threads
      ) {
          $this->id = $id;
          $this->document = $document;
          $this->name = $name;
          $this->icon = $icon;
          $this->cover = $cover;
          $this->description = $description;
          $this->verified = $verified;
          $this->likes = $likes;
          $this->surveys = $surveys;
          $this->threads = $threads;
      }

      public function addLike($id) {
        $this->likes[] = $id;
      }

      public function removeLike($id) {
        if(in_array($id, $this->likes)) {
          unset($this->likes[$like]);
          $this->likes = array_values($this->likes);
        }
      }

      public function isLiked($id) {
        return in_array($id, $this->likes);
      }
  }

  class Post {
      public $id;
      public $user;
      public $content;
      public $images;
      public $likes;
      public $date;
      public $parent;

      public function __construct(
        $id, 
        $user, 
        $content,
        $images,
        $likes,
        $date,
        $parent
      ) {
          $this->id = $id;
          $this->user = $user;
          $this->content = $content;
          $this->images = $images;
          $this->likes = $likes;
          $this->date = $date;
          $this->parent = $parent;
      }
  }
  

  class Survey {
    public $thread;
    public $title;
    public $votes;
    public $multi_select;

    public function __construct(
      $thread, 
      $title,
      $votes,
      $multi_select
    ) {
        $this->thread = $thread;
        $this->title = $title;
        $this->votes = $votes;
        $this->multi_select = $multi_select;
    }
}

class NewsItem {
  public $id;
  public $user;
  public $title;
  public $content;
  public $images;
  public $date;

  public function __construct(
    $id, 
    $user, 
    $title,
    $content, 
    $images,
    $date
  ) {
      $this->id = $id;
      $this->user = $user;
      $this->title = $title;
      $this->content = $content;
      $this->images = $images;
      $this->date = $date;
  }
}

  class Repository {
    public function getThreads() {
      include 'db.php';
      $result = mysqli_query($connection, "SELECT 
      t.id AS thread_id, 
      t.title AS thread_title, 
      t.content AS thread_content, 
      t.survey AS thread_survey, 
      t.images AS thread_images, 
      t.likes AS thread_likes, 
      t.date AS thread_date, 
      t.open AS thread_open,
      u.id AS user_id, 
      u.document AS user_doc, 
      u.name AS user_name, 
      u.icon AS user_icon, 
      u.cover AS user_cover,
      u.description AS user_description,
      u.verified AS user_verified, 
      u.threads AS user_threads,
      u.likes AS user_likes,
      u.surveys AS user_surveys
  FROM 
      thread t
  JOIN 
      user u ON t.user = u.id
  ORDER BY
      t.date DESC");
      
      $threads = [];
      $currentThread = null;

        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            if (!$currentThread || $currentThread->id != $row['thread_id']) {
                $images = json_decode($row["thread_images"], true);

                $likes = json_decode($row["user_likes"], true);
                $surveys = json_decode($row["user_surveys"], true);
                $uthreads = json_decode($row["user_threads"], true);
                $user = new User(
                  $row['user_id'], 
                  $row['user_doc'], 
                  $row['user_name'], 
                  $row['user_icon'], 
                  $row['user_cover'],
                  $row['user_description'],
                  $row['user_verified'], 
                  isset($likes) ? $likes : array(),
                  isset($surveys) ? $surveys : array(),
                  isset($uthreads) ? $uthreads : array()
                );

                $currentThread = new Thread(
                  $row['thread_id'], 
                  $user, $row['thread_title'], 
                  $row['thread_content'], 
                  $row['thread_survey'], 
                  isset($images) ? $images : array(), 
                  $row['thread_likes'], 
                  $row['thread_date'], 
                  $row['thread_open']
                );
                $threads[$row['thread_id']] = $currentThread;
            }
        }

        return $threads;
    }

    public function getposts($parent) {
      include 'db.php';
      $result = mysqli_query($connection, "SELECT 
      p.id AS post_id, 
      p.content AS post_content, 
      p.images AS post_images, 
      p.likes AS post_likes, 
      p.date AS post_date, 
      u.id AS user_id, 
      u.document AS user_doc, 
      u.name AS user_name, 
      u.icon AS user_icon, 
      u.cover AS user_cover,
      u.description AS user_description,
      u.verified AS user_verified, 
      u.threads AS user_threads,
      u.likes AS user_likes,
      u.surveys AS user_surveys,
      u.threads AS user_threads
  FROM 
      post p
  JOIN 
      user u ON p.user = u.id
  WHERE
      p.parent = '$parent->id'
  ORDER BY
      p.likes DESC,
      p.date DESC");

      $posts = [];
      $currentpost = null;
      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
          if (!$currentpost || $currentpost->id != $row['post_id']) {
            $images = json_decode($row["post_images"], true);
            $likes = json_decode($row["user_likes"], true);
            $surveys = json_decode($row["user_surveys"], true);
            $threads = json_decode($row["user_threads"], true);
            $user = new User(
                $row['user_id'], 
                $row['user_doc'], 
                $row['user_name'], 
                $row['user_icon'], 
                $row['user_cover'],
                $row['user_description'],
                $row['user_verified'], 
                isset($likes) ? $likes : array(),
                isset($surveys) ? $surveys : array(),
                isset($threads) ? $threads : array()
            );
            $currentpost = new Post(
              $row['post_id'], 
              $user, 
              $row['post_content'], 
              isset($images) ? $images : array(),
              $row['post_likes'], 
              $row['post_date'], 
              $parent
            );
          }

          $posts[$row['post_id']] = $currentpost;
      }
      return $posts;
    }

    public function parseThread($id) {
        include 'db.php';
        $result = mysqli_query($connection, "SELECT 
      t.id AS thread_id, 
      t.title AS thread_title, 
      t.content AS thread_content, 
      t.survey AS thread_survey, 
      t.images AS thread_images, 
      t.likes AS thread_likes, 
      t.date AS thread_date, 
      t.open AS thread_open,
      u.id AS user_id, 
      u.document AS user_doc, 
      u.name AS user_name, 
      u.icon AS user_icon, 
      u.cover AS user_cover,
      u.description AS user_description,
      u.verified AS user_verified, 
      u.likes AS user_likes,
      u.surveys AS user_surveys,
      u.threads AS user_threads
  FROM 
      thread t
  JOIN 
      user u ON t.user = u.id
  WHERE
      t.id = '$id'
  ORDER BY
      t.date DESC");
        $currentThread = null;
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $images = json_decode($row["thread_images"], true);

            $likes = json_decode($row["user_likes"], true);
            $surveys = json_decode($row["user_surveys"], true);
            $threads = json_decode($row["user_threads"], true);
            $user = new User(
                $row['user_id'], 
                $row['user_doc'], 
                $row['user_name'], 
                $row['user_icon'], 
                $row['user_cover'],
                $row['user_description'],
                $row['user_verified'], 
                isset($likes) ? $likes : array(),
                isset($surveys) ? $surveys : array(),
                isset($threads) ? $threads : array()
            );
            
            $currentThread = new Thread(
              $row['thread_id'], 
              $user, $row['thread_title'], 
              $row['thread_content'], 
              $row['thread_survey'], 
              isset($images) ? $images : array(), 
              $row['thread_likes'], 
              $row['thread_date'], 
              $row['thread_open']
            );
        }
        return $currentThread;
    }

    public function parsePost($id) {
      include 'db.php';
      $result = mysqli_query($connection, "SELECT 
    p.id AS post_id, 
    p.content AS post_content, 
    p.images AS post_images, 
    p.likes AS post_likes, 
    p.date AS post_date,
    p.parent AS post_parent,
    u.id AS user_id, 
    u.document AS user_doc, 
    u.name AS user_name, 
    u.icon AS user_icon, 
    u.cover AS user_cover,
    u.description AS user_description,
    u.verified AS user_verified, 
    u.likes AS user_likes,
    u.surveys AS user_surveys,
    u.threads AS user_threads
FROM 
    post p
JOIN 
    user u ON p.user = u.id
WHERE
    p.id = '$id'");
      $currentPost = null;
      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
          $images = json_decode($row["post_images"], true);

          $likes = json_decode($row["user_likes"], true);
          $surveys = json_decode($row["user_surveys"], true);
          $threads = json_decode($row["user_threads"], true);
          $user = new User(
              $row['user_id'], 
              $row['user_doc'], 
              $row['user_name'], 
              $row['user_icon'], 
              $row['user_cover'],
              $row['user_description'],
              $row['user_verified'], 
              isset($likes) ? $likes : array(),
              isset($surveys) ? $surveys : array(),
              isset($threads) ? $threads : array()
          );
          $currentPost = new Post(
            $row['post_id'], 
            $user,
            $row['post_content'], 
            isset($images) ? $images : array(), 
            $row['post_likes'], 
            $row['post_date'],
            $row['post_parent']
          );
      }
      return $currentPost;
    }

    public function parseUser($id) {
      include 'db.php';
      $result = mysqli_query($connection, "SELECT 
    u.id AS user_id, 
    u.document AS user_doc, 
    u.name AS user_name, 
    u.icon AS user_icon, 
    u.cover AS user_cover,
    u.description AS user_description,
    u.verified AS user_verified, 
    u.threads AS user_threads,
    u.likes AS user_likes,
    u.surveys AS user_surveys,
    u.threads AS user_threads
FROM 
    user u
WHERE
    u.id = '$id'");
      $user = null;
      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
          $likes = json_decode($row["user_likes"], true);
          $surveys = json_decode($row["user_surveys"], true);
          $threads = json_decode($row["user_threads"], true);

          $user = new User(
            $row['user_id'], 
            $row['user_doc'], 
            $row['user_name'], 
            $row['user_icon'], 
            $row['user_cover'],
            $row['user_description'],
            $row['user_verified'], 
            isset($likes) ? $likes : array(),
            isset($surveys) ? $surveys : array(),
            isset($threads) ? $threads : array()
          );
      }
      return $user;
  }

  public function parseSurvey($thread) {
    include 'db.php';
    $result = mysqli_query($connection, "SELECT 
    s.id AS survey_id, 
    s.title AS survey_title,
    s.votes AS survey_votes, 
    s.multi_select AS survey_multiselect
FROM 
    survey s
WHERE
    s.id = '$thread->id'
    ");

    $survey = null;
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
      $votes = json_decode($row["survey_votes"], true);
      $survey = new Survey(
        $thread,
        $row['survey_title'],
        isset($votes) ? $votes : array(),
        $row['survey_multiselect']
      );
    }
    return $survey;
  }
  

  public function getThread($post) {
    if($post == null) {
      return null;
    }
    $thread = $this->parseThread($post->parent);
    while ($thread == null) {
      $parent = $thread != null ? $thread : parsePost($post->parent);
      $thread = $this->parseThread($parent);
    }
    return $thread;
  }
  }

  function epochTime($epoch, $exact) {
    $now = time();
    $diff = $now - $epoch;
    $minute = 60;
    $hour = 60 * $minute;
    $day = 24 * $hour;
    $week = 7 * $day;
    $month = 4 * $week;
    $year = 365 * $day;

    if ($diff < $year * 2) {
      if($diff < $year) {
        if($diff > $month * 2) {
          $months = floor($diff / $month);
          return 'hace ' . $months . ' meses';
        } else if($diff > 4 * $week && $diff < $month * 2) {
          return 'hace un mes';
        } else if($diff > 7 * $day) {
          $weeks = floor($diff / $week);
          return $weeks . 'sem';
        } else if($diff > 24 * $hour) {
          $days = floor($diff / $day);
          return $days . 'd';
        } else if($diff > 60 * $minute) {
          $hours = floor($diff / $hour);
          return $hours . 'h';
        } else if($diff > 60) {
          $minutes = floor($diff / $minute);
          return $minutes . 'm';
        } else {
          return 'Justo ahora';
        }
      }
      return 'hace un año';
    } else {
      $year = date("Y", $epoch); 
      $month = date("m", $epoch);
      $day = date("d", $epoch); 
      $hour = date("H", $epoch);
      $minutes = date("i", $epoch);

      if($exact) {
        return $day . '/' . $month . '/' . $year . ' ' . $hour . ':' . $minutes;
      } else {
        return $day . '/' . $month . '/' . $year;
      }
    }
  }
  

  if (!isset($_SESSION['logged'])) {
    $_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
    header("Location: index.php"); 
    exit;
  } else {
    $time_elap = time() - $_SESSION['logged'];
    $timeout = 24 * 3600;
    $_SESSION['logged'] = time();

    error_log($time_elap);

      if($time_elap > $timeout) {
        session_unset();
        session_destroy();
        header("Location: index.php?error=La sesion ha expirado.");
        exit;
      }
  }
?>

<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" type="text/css" href="./css/styles.css" />
    <link
      href="https://ieti-camacho.edu.co/images/iconos/icono.png"
      rel="shortcut icon"
    />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <title>Crear hilo</title>
  </head>
  <body>
    <div id="modal" class="modal">
      <span class="close">&times;</span>
      <img id="modal-image" class="modal-content" />
      <div class="roll">
      </div>
    </div>
    <nav>
      <div class="options">
        <svg
          viewBox="0 0 24 24"
          preserveAspectRatio="xMidYMid meet"
          focusable="false"
        >
          <g>
            <path
              d="M21,6H3V5h18V6z M21,11H3v1h18V11z M21,17H3v1h18V17z"
            ></path>
          </g>
        </svg>
      </div>

      <div class="camacho-icon">
        <a href="/web">
          <img src="ieti-logo-small.png" alt="ieti-camacho" />
        </a>
      </div>
    <div class="create" title="Crea un nuevo hilo">
      <a href="create-thread.php">
        <p>Crear</p>
        <svg
          xmlns="http://www.w3.org/2000/svg"
          version="1.0"
          viewBox="0 0 563.000000 563.000000"
          preserveAspectRatio="xMidYMid meet"
          focusable="false"
          class="home-icon"
        >
          <g transform="translate(0.000000,563.000000) scale(0.100000,-0.100000)" stroke="none">
            <path d="M4275 5146 c-96 -22 -171 -54 -248 -106 -53 -36 -319 -296 -1008 -987 -514 -516 -958 -967 -986 -1003 -155 -199 -260 -423 -311 -661 -35 -162 -83 -509 -76 -548 15 -81 65 -145 144 -180 70 -32 60 -33 422 45 219 47 326 80 448 136 87 40 215 118 303 184 34 26 498 482 1030 1013 718 716 981 985 1017 1039 29 42 61 107 77 155 23 72 27 98 27 217 1 115 -3 146 -22 210 -36 115 -86 195 -181 291 -96 95 -157 135 -276 175 -98 33 -264 42 -360 20z m251 -484 c110 -57 155 -195 99 -308 -16 -32 -75 -99 -175 -199 l-150 -150 -168 168 -167 168 160 158 c178 176 207 193 306 187 30 -2 73 -13 95 -24z m-718 -825 l163 -163 -618 -615 c-340 -338 -649 -638 -688 -667 -110 -82 -265 -157 -388 -188 -59 -15 -110 -25 -112 -23 -2 3 7 54 20 115 40 193 117 345 251 499 100 115 1184 1205 1198 1205 6 0 84 -73 174 -163z"/>
            <path d="M1485 4920 c-214 -7 -275 -17 -371 -59 -160 -71 -307 -230 -363 -394 -46 -132 -46 -141 -46 -1652 0 -1511 0 -1520 46 -1652 64 -186 235 -355 424 -418 57 -19 111 -28 222 -35 193 -12 2643 -12 2836 0 172 11 265 37 365 102 161 104 270 262 309 444 15 70 17 163 17 789 1 678 0 712 -18 748 -58 117 -192 168 -306 116 -56 -25 -86 -53 -113 -104 -22 -40 -22 -47 -27 -745 -5 -688 -6 -706 -26 -750 -25 -53 -78 -103 -131 -121 -31 -12 -293 -14 -1488 -14 -1375 0 -1452 1 -1497 18 -57 22 -112 81 -130 139 -19 64 -19 2903 0 2966 17 55 86 126 138 143 28 8 224 13 685 18 l646 6 49 30 c37 23 56 45 78 87 76 149 -4 309 -170 337 -66 12 -774 12 -1129 1z"/>
          </g>
        </svg>
      </a>
    </div>
    <div class="profile">
          <?php
            $repository = new Repository();

            $profile = $repository->parseUser($_SESSION['id']);
          ?>
        <a href="<?php echo 'profile.php?id=' . $profile->id; ?>">
          <img alt="<?php echo $profile->id . '\'s icon' ?>" src="<?php echo isset($profile->icon) ? $profile->icon : 'profile-none.jpg'; ?>" />
        </a>
      </div>
    </nav>
    <div class="sidebar">
      <div class="feeds">Feeds</div>
      <div class="feeds-list">
        <a href="home.php"><div class="home"><svg xmlns="http://www.w3.org/2000/svg" version="1.0" viewBox="0 0 390.000000 347.000000" preserveAspectRatio="xMidYMid meet" focusable="false" class="home-icon">
            <g transform="translate(0.000000,347.000000) scale(0.100000,-0.100000)" stroke="none">
              <path d="M2599 3411 c-55 -11 -102 -46 -129 -93 -22 -39 -25 -58 -30 -196 l-5 -153 -190 170 c-104 94 -208 180 -231 191 -47 23 -120 26 -167 7 -49 -21 -1776 -1593 -1784 -1625 -4 -19 -1 -30 17 -44 20 -16 44 -18 256 -18 l233 0 3 -712 3 -713 27 -45 c16 -28 43 -55 70 -70 l43 -25 395 0 395 0 5 510 c6 569 3 545 77 602 l36 28 286 3 c157 2 296 0 308 -3 27 -6 89 -59 106 -89 9 -15 13 -159 17 -536 l5 -515 395 0 c381 0 396 1 435 21 25 13 51 39 70 69 l30 48 3 714 3 713 229 0 c126 0 240 4 253 9 34 13 42 40 19 71 -10 14 -152 148 -315 298 l-297 273 0 492 0 493 -29 45 c-48 77 -73 84 -306 86 -110 1 -216 -2 -236 -6z"></path>
            </g>
          </svg><p>Principal</p>
        </div></a>

        
        
        <a href="news.php"><div class="news">
          <svg viewBox="0 0 512 512" preserveAspectRatio="xMidYMid meet" focusable="false" class="news-icon">
            <g>
              <g>
                <g>
                  <path d="M94.432,139.492H19.231C8.61,139.492,0,148.103,0,158.723v138.71
				c0,10.62,8.61,19.231,19.231,19.231h75.201c9.733,0,17.756-7.238,19.033-16.621L231,335.009v-213.86l-117.535,34.965
				C112.188,146.729,104.165,139.492,94.432,139.492z"></path>
                  <path d="M349.482,78.436h-60.79c-10.621,0-19.231,8.61-19.231,19.231v260.822
				c0,10.621,8.61,19.231,19.231,19.231h60.79c10.621,0,19.231-8.61,19.231-19.231V97.667
				C368.713,87.046,360.103,78.436,349.482,78.436z"></path>
                  <path d="M492.769,208.847h-75.828c-10.621,0-19.231,8.61-19.231,19.231
				c0,10.621,8.61,19.231,19.231,19.231h75.828c10.62,0,19.231-8.61,19.231-19.231C512,217.458,503.391,208.847,492.769,208.847z"></path>
                  <path d="M428.687,172.483l53.619-53.619c7.51-7.51,7.51-19.686,0-27.196c-7.509-7.51-19.686-7.51-27.196,0
				l-53.619,53.619c-7.51,7.51-7.51,19.687,0,27.196C409,179.994,421.178,179.995,428.687,172.483z"></path>
                  <path d="M428.687,283.673c-7.509-7.51-19.686-7.51-27.196,0s-7.51,19.686,0,27.196l53.619,53.619
				c7.509,7.51,19.686,7.512,27.196,0c7.51-7.51,7.51-19.686,0-27.196L428.687,283.673z"></path>
                  <path d="M127.82,414.333c0,10.621,8.61,19.231,19.231,19.231h57.459c10.621,0,19.231-8.61,19.231-19.231
				v-41.356l-95.921-28.536V414.333z"></path>
                </g>
              </g>
            </g>
          </svg>
          <p>Noticias</p>
        </div></a>
        <a href="specialties.php"><div class="specialties">
          <svg xmlns="http://www.w3.org/2000/svg" version="1.0" viewBox="0 0 557.000000 566.000000" preserveAspectRatio="xMidYMid meet" focusable="false" class="specialties-icon">
            <g transform="translate(0.000000,566.000000) scale(0.100000,-0.100000)" stroke="none">
              <path d="M506 5061 l-318 -318 481 -642 481 -641 313 0 312 0 531 -531 531 -531 -23 -56 c-94 -235 -60 -507 90 -716 25 -34 325 -341 668 -682 715 -713 672 -678 838 -678 158 0 160 2 526 368 332 332 344 348 366 465 14 72 1 156 -33 229 -17 36 -171 196 -668 692 -709 708 -693 694 -876 756 -151 50 -342 42 -496 -20 l-56 -24 -532 532 -531 531 0 312 0 313 -640 480 c-352 264 -641 480 -642 480 -2 0 -146 -143 -322 -319z"></path>
              <path d="M3730 5374 c-253 -30 -470 -110 -682 -250 -92 -61 -278 -245 -347 -344 -161 -230 -251 -494 -255 -747 l-1 -112 408 -409 408 -410 87 14 c168 27 343 11 508 -47 170 -59 277 -131 457 -308 l138 -135 77 38 c492 242 807 785 778 1341 -8 165 -37 300 -71 336 -32 32 -81 44 -121 29 -16 -6 -198 -180 -409 -391 l-381 -380 -341 57 -340 57 -57 340 -57 341 385 385 c411 413 412 414 383 482 -23 54 -50 71 -150 91 -89 18 -340 31 -417 22z"></path>
              <path d="M1099 2073 l-797 -798 -41 -85 c-126 -259 -81 -542 119 -741 88 -87 177 -137 304 -169 146 -37 294 -18 446 57 71 35 106 68 732 691 l656 655 -19 54 c-37 110 -52 205 -52 343 0 74 5 155 11 180 l11 45 -282 283 c-155 155 -284 282 -287 282 -3 0 -364 -359 -801 -797z m-179 -954 c83 -37 134 -106 147 -194 13 -96 -46 -197 -141 -241 -226 -105 -444 184 -278 368 73 81 182 108 272 67z"></path>
            </g>
          </svg>
          <p>Talleres</p>
        </div></a>
      </div>
      <div class="threads">Hilos</div>
        <div class="threads-list">
        <div class="creating selected">
            <svg viewBox="0 0 512 512" preserveAspectRatio="xMidYMid meet" focusable="false" class="thread-icon">
              <g>
                <g>
                  <g>
                    <path d="M446.103,143.719H261.788c-36.02,0-65.322,29.309-65.322,65.335v150.491
          c0,36.02,29.303,65.322,65.322,65.322h27.266l106.628,82.999c13.342,10.383,32.772-0.097,31.348-16.993l-5.566-66.006h24.64
          c36.018,0,65.322-29.303,65.322-65.322V209.055C511.425,173.028,482.122,143.719,446.103,143.719z M301.357,301.516
          c-9.333,0-16.897-7.565-16.897-16.897c0-9.333,7.565-16.897,16.897-16.897c9.333,0,16.897,7.565,16.897,16.897
          C318.255,293.95,310.69,301.516,301.357,301.516z M353.945,301.516c-9.333,0-16.897-7.565-16.897-16.897
          c0-9.333,7.565-16.897,16.897-16.897s16.897,7.565,16.897,16.897C370.843,293.95,363.278,301.516,353.945,301.516z
          M406.533,301.516c-9.333,0-16.897-7.565-16.897-16.897c0-9.333,7.565-16.897,16.897-16.897c9.333,0,16.897,7.565,16.897,16.897
          C423.431,293.95,415.866,301.516,406.533,301.516z"></path>
                    <path d="M309.047,104.797v-0.001c3.582,0,6.487-2.905,6.487-6.487V65.322
          C315.534,29.246,286.289,0,250.212,0H65.897C29.82,0,0.575,29.246,0.575,65.322v150.505c0,36.077,29.246,65.322,65.322,65.322
          h24.638l-5.566,66.006c-1.427,16.914,18.04,27.351,31.348,16.993l38.722-30.141c1.579-1.23,2.503-3.118,2.503-5.12V209.056
          c0-57.581,46.678-104.259,104.259-104.259H309.047z"></path>
                  </g>
                </g>
              </g>
            </svg>
            <div class="content">
              <div class="text-container" title="...">
                <a>...</a>
              </div>
              <div class="data">
                <div class="icon">
                  <img alt="<?php echo $profile->id . '\'s icon' ?>" src="<?php echo isset($profile->icon) ? $profile->icon : 'profile-none.jpg'; ?>" />
                </div>
                <div class="user">
                  <p><?php echo $profile->id ?></p>
                </div>
                <div class="space">
                </div>
                
                <div class="space">
                </div>
                
              </div>
            </div>

          </div>
          <?php
            
            $repository = new Repository();
            $threads = $repository->getThreads();

            foreach($threads as $thread) {
              $id = $thread->id;
              $user = $thread->user;
              $title = $thread->title;
              $content = $thread->content;
              $survey = $thread->survey;
              $images = $thread->images;
              $likes = $thread->likes;
              $date = $thread->date;
              $open = $thread->open;

            ?>
            <a href="thread.php?id=<?php echo $id ?>">
            <div class="<?php echo $id; ?>" onclick="
            this.classList.add('selected');
            ">
            <svg
              viewBox="0 0 512 512"
              preserveAspectRatio="xMidYMid meet"
              focusable="false"
              class="thread-icon"
            >
              <g>
                <g>
                  <g>
                    <path
                      d="M446.103,143.719H261.788c-36.02,0-65.322,29.309-65.322,65.335v150.491
          c0,36.02,29.303,65.322,65.322,65.322h27.266l106.628,82.999c13.342,10.383,32.772-0.097,31.348-16.993l-5.566-66.006h24.64
          c36.018,0,65.322-29.303,65.322-65.322V209.055C511.425,173.028,482.122,143.719,446.103,143.719z M301.357,301.516
          c-9.333,0-16.897-7.565-16.897-16.897c0-9.333,7.565-16.897,16.897-16.897c9.333,0,16.897,7.565,16.897,16.897
          C318.255,293.95,310.69,301.516,301.357,301.516z M353.945,301.516c-9.333,0-16.897-7.565-16.897-16.897
          c0-9.333,7.565-16.897,16.897-16.897s16.897,7.565,16.897,16.897C370.843,293.95,363.278,301.516,353.945,301.516z
          M406.533,301.516c-9.333,0-16.897-7.565-16.897-16.897c0-9.333,7.565-16.897,16.897-16.897c9.333,0,16.897,7.565,16.897,16.897
          C423.431,293.95,415.866,301.516,406.533,301.516z"
                    />
                    <path
                      d="M309.047,104.797v-0.001c3.582,0,6.487-2.905,6.487-6.487V65.322
          C315.534,29.246,286.289,0,250.212,0H65.897C29.82,0,0.575,29.246,0.575,65.322v150.505c0,36.077,29.246,65.322,65.322,65.322
          h24.638l-5.566,66.006c-1.427,16.914,18.04,27.351,31.348,16.993l38.722-30.141c1.579-1.23,2.503-3.118,2.503-5.12V209.056
          c0-57.581,46.678-104.259,104.259-104.259H309.047z"
                    />
                  </g>
                </g>
              </g>
            </svg>
            <div class="content">
              <div class="text-container">
                <p><?php echo $title ?></p>
              </div>
              <div class="data">
                <div class="icon">
                  <img alt="<?php echo $user->id . '\'s icon' ?>" src="<?php echo isset($user->icon) ? $user->icon : 'profile-none.jpg'; ?>" />
                </div>
                <div class="user">
                  <p><?php echo $user->id ?></p>
                </div>
                <div class="space">
                </div>
                <div class="date">
                  <p><?php echo epochTime($date, false) ?></p>
                </div>
                <div class="space">
                </div>
                <div class="<?php echo $open == 1 ? 'state' : 'state closed'; ?>">
                  <p><?php echo $open == 1 ? 'Abierto' : 'Cerrado'; ?></p>
                </div>
              </div>
            </div>
            
          </div>
            </a>
            
            <?php
            }
          ?>
        </div>
    </div>  
    <div class="options-list">
        <div class="edit">
          <svg xmlns="http://www.w3.org/2000/svg" version="1.0" viewBox="0 0 640.000000 640.000000" preserveAspectRatio="xMidYMid meet" focusable="false">
            <g transform="translate(0.000000,640.000000) scale(0.100000,-0.100000)" stroke="none">
              <path d="M4833 5726 c-65 -21 -95 -46 -305 -254 l-197 -196 90 -42 c335 -160 654 -479 813 -814 l42 -90 206 208 c213 215 242 254 254 343 12 91 -65 310 -152 433 -146 204 -352 351 -568 406 -91 23 -124 24 -183 6z"/>
              <path d="M2573 3516 c-759 -759 -1394 -1401 -1410 -1426 -17 -25 -44 -72 -60 -105 -43 -86 -391 -1012 -399 -1061 -18 -119 104 -242 220 -220 47 9 997 366 1066 401 30 15 75 41 100 58 60 40 2800 2777 2800 2797 0 43 -140 288 -227 397 -66 83 -196 215 -281 285 -108 89 -276 192 -393 241 l-36 15 -1380 -1382z"/>
            </g>
          </svg>
          <p>Editar</p>
        </div>
        <div class="delete">
          <svg xmlns="http://www.w3.org/2000/svg" version="1.0" viewBox="0 0 640.000000 640.000000" preserveAspectRatio="xMidYMid meet" focusable="false">
            <g transform="translate(0.000000,640.000000) scale(0.100000,-0.100000)" stroke="none">
              <path d="M2303 5916 c-23 -8 -57 -23 -76 -35 -77 -47 -108 -98 -337 -558 l-224 -452 -336 -3 -335 -3 -56 -26 c-266 -125 -272 -484 -9 -608 50 -23 69 -26 198 -29 l142 -4 0 -1472 c0 -985 3 -1492 11 -1532 65 -368 345 -648 713 -713 82 -15 2330 -15 2412 0 368 65 648 345 713 713 8 40 11 547 11 1532 l0 1472 143 4 c128 3 147 6 197 29 263 124 257 483 -9 608 l-56 26 -335 3 -336 3 -229 460 c-204 408 -235 466 -278 505 -26 25 -69 55 -95 67 l-47 22 -870 2 c-696 1 -878 -1 -912 -11z m1590 -841 c53 -107 97 -197 97 -200 0 -3 -355 -5 -790 -5 -434 0 -790 2 -790 5 0 3 44 93 97 200 l98 195 595 0 595 0 98 -195z m577 -2311 c0 -1014 -3 -1448 -11 -1479 -16 -65 -69 -121 -131 -140 -78 -23 -2178 -23 -2256 0 -62 19 -115 75 -131 140 -8 31 -11 465 -11 1479 l0 1436 1270 0 1270 0 0 -1436z"></path>
              <path d="M2575 3787 c-91 -30 -168 -95 -205 -172 -39 -81 -41 -127 -38 -987 l3 -833 26 -56 c125 -266 483 -272 608 -9 l26 55 0 880 0 880 -26 55 c-37 79 -81 125 -155 161 -74 37 -173 47 -239 26z"></path>
              <path d="M3639 3785 c-69 -22 -140 -74 -177 -129 -65 -97 -63 -63 -60 -1012 l3 -859 26 -55 c125 -263 483 -257 608 9 l26 56 3 833 c2 543 -1 853 -8 890 -29 159 -152 270 -309 278 -40 2 -84 -2 -112 -11z"></path>
            </g>
          </svg>
          <p>Eliminar</p>
        </div>
    </div>
    <script>
        /**
         * @param {HTMLElement} element
         */
        function toggleIcon(element, table, id) {
            element.classList.toggle('clicked');
            var text = element.parentElement.querySelector('.text');
            $.ajax({
                  type: 'POST',
                  url: 'like.php',
                  async: true,
                  data: {
                    table: table,
                    id: id
                  },
                  success: function(data) {
                    console.log(id);
                  }
                });
            if(element.classList.contains('clicked')) {
                element.innerHTML = '<svg aria-label="Ya no me gusta" role="img" viewBox="0 0 48 48"><title>Ya no me gusta</title><path d="M34.6 3.1c-4.5 0-7.9 1.8-10.6 5.6-2.7-3.7-6.1-5.5-10.6-5.5C6 3.1 0 9.6 0 17.6c0 7.3 5.4 12 10.6 16.5.6.5 1.3 1.1 1.9 1.7l2.3 2c4.4 3.9 6.6 5.9 7.6 6.5.5.3 1.1.5 1.6.5s1.1-.2 1.6-.5c1-.6 2.8-2.2 7.8-6.8l2-1.8c.7-.6 1.3-1.2 2-1.7C42.7 29.6 48 25 48 17.6c0-8-6-14.5-13.4-14.5z"></path></svg>';
                text.querySelector('p').innerHTML = toInt(text.querySelector('p').innerHTML, 0) + 1;

            } else {
                element.innerHTML = '<svg aria-label="Me gusta" role="img" viewBox="0 0 24 24"><title>Me gusta</title><path d="M16.792 3.904A4.989 4.989 0 0 1 21.5 9.122c0 3.072-2.652 4.959-5.197 7.222-2.512 2.243-3.865 3.469-4.303 3.752-.477-.309-2.143-1.823-4.303-3.752C5.141 14.072 2.5 12.167 2.5 9.122a4.989 4.989 0 0 1 4.708-5.218 4.21 4.21 0 0 1 3.675 1.941c.84 1.175.98 1.763 1.12 1.763s.278-.588 1.11-1.766a4.17 4.17 0 0 1 3.679-1.938m0-2a6.04 6.04 0 0 0-4.797 2.127 6.052 6.052 0 0 0-4.787-2.127A6.985 6.985 0 0 0 .5 9.122c0 3.61 2.55 5.827 5.015 7.97.283.246.569.494.853.747l1.027.918a44.998 44.998 0 0 0 3.518 3.018 2 2 0 0 0 2.174 0 45.263 45.263 0 0 0 3.626-3.115l.922-.824c.293-.26.59-.519.885-.774 2.334-2.025 4.98-4.32 4.98-7.94a6.985 6.985 0 0 0-6.708-7.218Z"></path></svg>';
                text.querySelector('p').innerHTML = toInt(text.querySelector('p').innerHTML, 0) - 1;

            }
            
            
        }
          
        function toInt(value, fallback) {
            let parsed = parseInt(value, 10);
            return isNaN(parsed) ? fallback : parsed; 
        }
       </script>
    <script src="js/sidebar.js"></script>
    <div class="create-thread-content" id="feed">
      <div class="form-container">
          <form action="upload-thread.php" method="post" enctype="multipart/form-data">
            <h1>Crear hilo</h1>
            <div class="title">
                <textarea name="title" minlength="3" maxlength="200"></textarea>
                <span class="label">
                    <span class="name">Titulo</span>
                    <span class="asterisk">*</span>
                </span>
            </div>
            <div class="title-field">
                <span class="complete">Este campo es obligatorio</span>
                <span class="characters">0/200</span>
            </div>
            <div class="content">
                <textarea name="content" minlength="3" maxlength="1000"></textarea>
                <span class="label">
                    <span class="name">Contenido</span>
                    <span class="asterisk">*</span>
                </span>
            </div>
            <div class="content-field">
                <span class="complete">Este campo es obligatorio</span>
                <span class="characters">0/1000</span>
            </div> 
            <div class="drop-image">
              <div class="container">
                <span class="text">Arrastra y suelta tu imagen aqui o</span>
                <label for="fileUpload" class="icon">
                  <svg xmlns="http://www.w3.org/2000/svg" version="1.0" viewBox="0 0 1024.000000 1024.000000" preserveAspectRatio="xMidYMid meet">
                    <g transform="translate(0.000000,1024.000000) scale(0.100000,-0.100000)" stroke="none">
                    <path d="M4845 8519 c-129 -13 -337 -57 -473 -99 -900 -276 -1574 -1020 -1762 -1945 -11 -55 -24 -102 -28 -106 -4 -3 -47 -14 -95 -23 -197 -38 -495 -155 -690 -270 -317 -186 -633 -498 -816 -803 -163 -273 -271 -577 -318 -892 -24 -163 -24 -504 0 -661 66 -421 237 -813 498 -1142 87 -109 293 -307 413 -397 338 -254 761 -421 1170 -460 60 -6 252 -11 426 -11 292 0 322 2 376 20 152 52 260 178 286 335 35 208 -90 409 -297 478 -16 5 -187 13 -380 17 -304 6 -364 10 -455 29 -452 95 -824 371 -1033 769 -59 114 -127 302 -146 409 -69 380 5 772 203 1081 185 290 462 509 776 615 200 68 349 87 696 87 l211 0 6 288 c3 196 10 322 22 397 104 671 608 1225 1264 1389 154 39 245 50 421 50 176 0 267 -11 421 -50 584 -146 1050 -599 1218 -1184 50 -173 62 -277 68 -597 l6 -293 211 0 c347 0 496 -19 696 -87 428 -145 778 -495 923 -923 139 -411 102 -830 -107 -1214 -170 -311 -477 -567 -821 -684 -189 -64 -265 -74 -650 -82 -192 -4 -364 -12 -380 -17 -158 -53 -269 -181 -297 -341 -35 -203 86 -404 284 -471 58 -20 83 -21 378 -21 174 0 366 5 426 11 687 66 1331 459 1717 1044 184 280 310 611 364 955 24 157 24 498 0 661 -60 405 -216 776 -460 1099 -168 221 -451 469 -695 609 -199 113 -480 220 -699 264 -78 16 -69 0 -108 183 -149 707 -641 1352 -1295 1697 -290 153 -620 254 -939 286 -118 13 -418 12 -536 0z"></path>
                    <path d="M4995 5953 c-28 -10 -73 -31 -100 -47 -71 -40 -1394 -1364 -1425 -1425 -98 -195 -59 -408 100 -540 131 -109 300 -129 460 -54 45 21 111 81 358 326 l302 301 0 -1189 c0 -665 4 -1214 9 -1245 26 -154 103 -261 240 -328 72 -36 79 -37 181 -37 102 0 109 1 182 38 134 66 214 175 239 327 5 31 9 580 9 1244 l0 1190 303 -302 c331 -329 348 -342 483 -365 274 -47 527 205 483 483 -5 36 -23 94 -40 130 -28 60 -82 116 -712 746 -664 664 -684 683 -761 720 -70 34 -87 38 -170 41 -68 3 -103 -1 -141 -14z"></path>
                    </g>
                  </svg>
                  <input type="file" id="fileUpload" accept="image/jpeg,image/png" multiple>
                </label>
                <div class="images">
                  
                </div>
              </div>
            </div>
            <div class="drop-image-field">
              <span class="error">Ha ocurrido un error.</span>
            </div>
            <details class="survey">
              <summary>
                <svg xmlns="http://www.w3.org/2000/svg" version="1.0" viewBox="0 0 1280.000000 1280.000000" preserveAspectRatio="xMidYMid meet">
                  <g transform="translate(0.000000,1280.000000) scale(0.100000,-0.100000)" stroke="none">
                    <path d="M2220 12793 c-596 -44 -1093 -277 -1522 -710 -394 -398 -600 -815 -680 -1378 -19 -134 -19 -8474 0 -8620 67 -510 277 -955 619 -1307 427 -441 846 -659 1448 -755 104 -17 356 -18 4330 -18 3978 0 4227 1 4334 18 444 69 797 222 1136 493 108 86 331 310 412 414 268 341 424 712 484 1155 20 143 21 8482 1 8620 -81 570 -295 997 -699 1397 -191 189 -325 291 -531 408 -234 131 -482 215 -797 267 -92 16 -420 17 -4295 18 -2307 1 -4215 0 -4240 -2z m8370 -1615 c105 -32 170 -59 222 -95 179 -122 302 -279 362 -463 l21 -65 3 -4103 c2 -3426 0 -4119 -12 -4195 -23 -157 -84 -281 -201 -406 -124 -132 -284 -217 -454 -240 -117 -15 -8124 -16 -8237 0 -276 38 -516 211 -629 452 -70 150 -65 -212 -65 4301 0 2842 3 4098 11 4152 13 93 54 212 100 290 42 72 163 196 247 253 104 70 219 113 352 131 30 4 1893 7 4140 6 3791 -1 4089 -2 4140 -18z"></path>
                    <path d="M6210 9580 c-151 -41 -254 -104 -371 -226 -79 -83 -119 -141 -165 -237 -65 -137 -64 -121 -64 -1069 l0 -858 -857 0 c-824 0 -861 -1 -942 -20 -273 -66 -521 -296 -586 -545 -38 -143 -28 -352 24 -502 73 -212 247 -397 450 -479 116 -47 113 -47 1034 -51 l877 -4 0 -853 c0 -969 -3 -924 84 -1101 150 -303 462 -468 806 -426 55 7 125 21 155 31 260 89 467 319 531 590 16 68 18 153 21 917 l4 842 857 4 c928 4 900 2 1035 59 144 60 296 185 381 310 132 197 152 497 51 747 -75 183 -238 348 -415 418 -151 60 -98 57 -1042 60 l-867 4 -4 862 c-4 955 -1 910 -71 1062 -42 90 -95 162 -177 241 -165 157 -338 230 -563 240 -91 3 -125 1 -186 -16z"></path>
                  </g>
                </svg>
                <p>Agregar encuesta</p>
              </summary>
              <div>
                <div class="title">
                  <input type="text" name="title-survey" minlength="3" maxlength="100"></textarea>
                  <label>Pregunta</label>
                </div>
                <div class="option">
                  <input type="text" name="option-1" maxlength="50" placeholder="Opción 1">
                  <label>Opciones</label>
                  <input type="text" name="option-2" maxlength="50" placeholder="Opción 2">
                </div>
                <div class="multiple-selection">
                  <input type="checkbox" name="multi-select" id="multi-select" value="0">
                  <label for="multi-select">Seleccion multiple</label>
                </div>
              </div>
            </details>
            <div class="submit">
                <input type="submit" value="Subir">
            </div>
          </form>
      </div>
    </div>
    <script src="js/modal.js"></script>
    <script>
        function preventDefaults(event) {
            event.preventDefault()
            event.stopPropagation()
        }

        function isImage(file) {
          return file && file['type'].split('/')[0] === 'image';
        }

        const dropField = document.querySelector(".create-thread-content .form-container .drop-image");
        const fileUpload = document.getElementById("fileUpload");
        const imageContainer = document.querySelector(".create-thread-content .form-container .drop-image .container .images");
        const dropFieldErrorMessage = document.querySelector(".create-thread-content .form-container .drop-image-field .error");
        const form = document.querySelector(".create-thread-content .form-container form");

        var images = [];
        var done = false;
        const MAX_IMAGES = 10;

        ;['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropField.addEventListener(eventName, preventDefaults, false);
        });

        ;['dragenter', 'dragover'].forEach(eventName => {
            dropField.addEventListener(eventName, () => dropField.classList.add('selected'), false);
        })

        ;['dragleave', 'drop'].forEach(eventName => {
            dropField.addEventListener(eventName, () => dropField.classList.remove('selected'), false);
        })

        fileUpload.addEventListener('change', function () {
          handleFiles(this.files);
        });

        dropField.addEventListener('click', (event) => {
          if(dropField.classList.contains('uploaded') && !event.target.closest('.image-item')) {
            fileUpload.click();
          }
        });

        dropField.addEventListener('drop', function(event) {
            var dataTransfer = event.dataTransfer;
            var files = dataTransfer.files;
            handleFiles(files)
        }, false);

        form.addEventListener('submit', function(event) {
            event.preventDefault();
            if(done == true) {
              return;
            }
            done = true;
            var formData = new FormData(form);
            images.forEach((image, index) => {
              formData.append('files[]', image);
            });

            $.ajax({
              type: 'POST',
              url: 'upload-thread.php',
              async: false,
              data: formData,
              processData: false,
              contentType: false,
              success: function(data) {
                if(data === 'error') {
                  dropFieldErrorMessage.innerHTML = "Ha ocurrido un error.";
                  dropField.classList.add('error');
                  return;
                }
                if(data.redirect) {
                  window.location.href = data.redirect;
                }
              },
              error: function(jqXHR, textStatus, errorThrown) {
                  console.log('Error al subir el hilo: ' + textStatus);
              }
            });
        });

        function handleFiles(files) {
            dropField.classList.remove('error');
                            

            files = [...files];
            var notImage = false;

            files.forEach(file => {
              if(!isImage(file)) {
                dropField.classList.add('error');
                dropFieldErrorMessage.innerHTML = "Solo puedes subir imagenes o gifs.";
                notImage = true;
              }
            });

            if(notImage) {
              return;
            }
            
            if (images.length + files.length > MAX_IMAGES) {
                  dropField.classList.add('error');
                  dropFieldErrorMessage.innerHTML = "No puedes subir más de " + MAX_IMAGES + " imagenes.";
                  return;
            }
            
            images = images.concat(files);
            renderImages();
        }

        function deleteImage(i) {
          images.splice(i, 1);
          if(images.length == 0)  {
            dropField.classList.remove('uploaded');
            return;
          }
          renderImages();
        }

        async function renderImages() {
          dropField.classList.add('uploaded');
          imageContainer.innerHTML = '';

          var imagesCopy = [...images];

          let imagesURL = [];

          for (let i = 0; i < imagesCopy.length; i++) {
            let image = imagesCopy[i];
            const imageUrl = await new Promise((resolve, reject) => { 
                let reader = new FileReader();
                reader.readAsDataURL(image);
                reader.onloadend = () => {
                    resolve(reader.result);
                };
                reader.onerror = reject; 
            });

            imagesURL.push(imageUrl);
          }

          for(let i = 0; i < imagesURL.length; i++) {
            let url = imagesURL[i];
            let div = document.createElement('div');
            let img = document.createElement('img');
            div.className = 'image-item';
            div.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" version="1.0" viewBox="0 0 640.000000 640.000000" preserveAspectRatio="xMidYMid meet" focusable="false">
                    <g transform="translate(0.000000,640.000000) scale(0.100000,-0.100000)" stroke="none">
                      <path d="M2303 5916 c-23 -8 -57 -23 -76 -35 -77 -47 -108 -98 -337 -558 l-224 -452 -336 -3 -335 -3 -56 -26 c-266 -125 -272 -484 -9 -608 50 -23 69 -26 198 -29 l142 -4 0 -1472 c0 -985 3 -1492 11 -1532 65 -368 345 -648 713 -713 82 -15 2330 -15 2412 0 368 65 648 345 713 713 8 40 11 547 11 1532 l0 1472 143 4 c128 3 147 6 197 29 263 124 257 483 -9 608 l-56 26 -335 3 -336 3 -229 460 c-204 408 -235 466 -278 505 -26 25 -69 55 -95 67 l-47 22 -870 2 c-696 1 -878 -1 -912 -11z m1590 -841 c53 -107 97 -197 97 -200 0 -3 -355 -5 -790 -5 -434 0 -790 2 -790 5 0 3 44 93 97 200 l98 195 595 0 595 0 98 -195z m577 -2311 c0 -1014 -3 -1448 -11 -1479 -16 -65 -69 -121 -131 -140 -78 -23 -2178 -23 -2256 0 -62 19 -115 75 -131 140 -8 31 -11 465 -11 1479 l0 1436 1270 0 1270 0 0 -1436z"></path>
                      <path d="M2575 3787 c-91 -30 -168 -95 -205 -172 -39 -81 -41 -127 -38 -987 l3 -833 26 -56 c125 -266 483 -272 608 -9 l26 55 0 880 0 880 -26 55 c-37 79 -81 125 -155 161 -74 37 -173 47 -239 26z"></path>
                      <path d="M3639 3785 c-69 -22 -140 -74 -177 -129 -65 -97 -63 -63 -60 -1012 l3 -859 26 -55 c125 -263 483 -257 608 9 l26 56 3 833 c2 543 -1 853 -8 890 -29 159 -152 270 -309 278 -40 2 -84 -2 -112 -11z"></path>
                    </g>
                  </svg>`;
            div.querySelector('svg').onclick = function () {
              deleteImage(i);
            };
            img.src = url;
            img.onclick = function() {
                openModal(this.src, imagesURL, true);
              }
            div.appendChild(img);
            imageContainer.appendChild(div);
          }
          
        }

      function uploadFiles(files) {
          var formData = new FormData();
          formData.append('files', files);
          $.ajax({
            type: 'POST',
            url: 'upload-image.php',
            async: true,
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
              if(data === 'error') {
                dropFieldErrorMessage.innerHTML = "Ha ocurrido un error.";
                dropField.classList.add('error');
                return;
              }
              
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.log('Error al subir el hilo: ' + textStatus);
            }
        });
      }

      
    </script>
    <script src="js/thread-writing.js"></script>

    
  </body>
</html>
