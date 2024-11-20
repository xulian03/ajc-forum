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

  public function getNewsPaginated($page) {
    include 'db.php';
    $page_results = 20;
    if($page <= 0) {
      return [];
    }
    $offset = ($page - 1) * $page_results;

    $result = mysqli_query($connection, "SELECT 
    n.id AS new_id, 
    n.title AS new_title, 
    n.content AS new_content, 
    n.images AS new_images, 
    n.date AS new_date,
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
    new n
JOIN 
    user u ON n.user = u.id
ORDER BY
    n.date DESC
LIMIT $page_results OFFSET $offset");

    $news = [];
    $currentNew = null;

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        if (!$currentNew || $currentNew->id != $row['new_id']) {
            $images = json_decode($row["new_images"], true);

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

            $currentNew = new NewsItem(
              $row['new_id'], 
              $user, 
              $row['new_title'], 
              $row['new_content'], 
              isset($images) ? $images : array(), 
              $row['new_date']
            );
            $news[$row['new_id']] = $currentNew;
        }
    }

    return $news;
  }

  public function getMaxNewsPaginated() {
    include 'db.php';
    
    $max_pages = 1;
    $result = mysqli_query($connection, "SELECT COUNT(*) AS total FROM new");
    $total_rows = mysqli_fetch_assoc($result)['total'];
    $max_pages = ceil($total_rows / 20);

    return $max_pages;
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
    <title>Noticias</title>
  </head>
  <body>
    <?php
      $repository = new Repository();

      $user = $repository->parseUser($_SESSION['id']);
    ?>
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
      <?php
        if($user->verified == 1) {
      ?>
      <div class="create-new" title="Crea una nueva noticia">
        <a href="create-new.php">
          <p>Crear noticia</p>
          <svg xmlns="http://www.w3.org/2000/svg" version="1.0" viewBox="0 0 320.000000 320.000000" preserveAspectRatio="xMidYMid meet">
            <g transform="translate(0.000000,320.000000) scale(0.100000,-0.100000)" stroke="none">
              <path d="M345 2921 c-11 -5 -31 -21 -45 -36 l-25 -27 -3 -1244 c-3 -1376 -7 -1283 61 -1324 31 -19 60 -20 1267 -20 1207 0 1236 1 1267 20 68 41 64 -52 61 1325 l-3 1245 -33 32 -32 33 -1248 2 c-686 1 -1256 -2 -1267 -6z m2320 -1326 l0 -1060 -1065 0 -1065 0 -3 1050 c-1 578 0 1056 3 1063 3 10 222 12 1067 10 l1063 -3 0 -1060z"></path>
              <path d="M880 2253 c-19 -10 -45 -33 -57 -52 -23 -33 -23 -39 -23 -335 0 -420 -20 -396 337 -396 351 0 336 -20 331 416 -4 400 15 378 -323 382 -205 2 -234 1 -265 -15z"></path>
              <path d="M1814 2256 c-91 -40 -107 -151 -33 -222 l30 -29 242 -3 c220 -2 245 -1 276 16 99 54 90 193 -16 238 -45 20 -454 19 -499 0z"></path>
              <path d="M1799 1715 c-85 -46 -84 -186 2 -231 42 -21 487 -21 530 1 89 47 89 183 0 230 -42 21 -492 22 -532 0z"></path>
              <path d="M871 1182 c-99 -53 -89 -193 16 -238 48 -21 1378 -21 1426 0 105 45 115 185 16 238 -32 17 -75 18 -729 18 -654 0 -697 -1 -729 -18z"></path>
            </g>
          </svg>
        </a>
      </div>
      <?php
        }
      ?>
    <div class="create" title="Crea un nuevo hilo">
      <a href="create-thread.php">
        <p>Crear</p>
        <svg
          xmlns="http://www.w3.org/2000/svg"
          version="1.0"
          viewBox="0 0 563.000000 563.000000"
          preserveAspectRatio="xMidYMid meet"
          focusable="false"
        >
          <g transform="translate(0.000000,563.000000) scale(0.100000,-0.100000)" stroke="none">
            <path d="M4275 5146 c-96 -22 -171 -54 -248 -106 -53 -36 -319 -296 -1008 -987 -514 -516 -958 -967 -986 -1003 -155 -199 -260 -423 -311 -661 -35 -162 -83 -509 -76 -548 15 -81 65 -145 144 -180 70 -32 60 -33 422 45 219 47 326 80 448 136 87 40 215 118 303 184 34 26 498 482 1030 1013 718 716 981 985 1017 1039 29 42 61 107 77 155 23 72 27 98 27 217 1 115 -3 146 -22 210 -36 115 -86 195 -181 291 -96 95 -157 135 -276 175 -98 33 -264 42 -360 20z m251 -484 c110 -57 155 -195 99 -308 -16 -32 -75 -99 -175 -199 l-150 -150 -168 168 -167 168 160 158 c178 176 207 193 306 187 30 -2 73 -13 95 -24z m-718 -825 l163 -163 -618 -615 c-340 -338 -649 -638 -688 -667 -110 -82 -265 -157 -388 -188 -59 -15 -110 -25 -112 -23 -2 3 7 54 20 115 40 193 117 345 251 499 100 115 1184 1205 1198 1205 6 0 84 -73 174 -163z"/>
            <path d="M1485 4920 c-214 -7 -275 -17 -371 -59 -160 -71 -307 -230 -363 -394 -46 -132 -46 -141 -46 -1652 0 -1511 0 -1520 46 -1652 64 -186 235 -355 424 -418 57 -19 111 -28 222 -35 193 -12 2643 -12 2836 0 172 11 265 37 365 102 161 104 270 262 309 444 15 70 17 163 17 789 1 678 0 712 -18 748 -58 117 -192 168 -306 116 -56 -25 -86 -53 -113 -104 -22 -40 -22 -47 -27 -745 -5 -688 -6 -706 -26 -750 -25 -53 -78 -103 -131 -121 -31 -12 -293 -14 -1488 -14 -1375 0 -1452 1 -1497 18 -57 22 -112 81 -130 139 -19 64 -19 2903 0 2966 17 55 86 126 138 143 28 8 224 13 685 18 l646 6 49 30 c37 23 56 45 78 87 76 149 -4 309 -170 337 -66 12 -774 12 -1129 1z"/>
          </g>
        </svg>
      </a>
    </div>
    <div class="profile">
          
        <a href="<?php echo 'profile.php?id=' . $user->id; ?>">
          <img alt="<?php echo $user->id . '\'s icon' ?>" src="<?php echo isset($user->icon) ? $user->icon : 'profile-none.jpg'; ?>" />
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

        
        
        <a><div class="news selected">
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
          <?php
            
            $repository = new Repository();
            $threads = $repository->getThreads();

            foreach($threads as $thread) {
              $id = $thread->id;
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
                  <img alt="<?php echo $thread->user->id . '\'s icon' ?>" src="<?php echo isset($thread->user->icon) ? $thread->user->icon : 'profile-none.jpg'; ?>" />
                </div>
                <div class="user">
                  <p><?php echo $thread->user->id ?></p>
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
    <div class="pop-up-container">
        <div class="pop-up">
          <h1>Eliminar?</h1>
          <p>Esto no se puede deshacer, y se eliminará para todo el mundo.</p>
          <div class="buttons">
            <div class="delete">
              <p>Eliminar</p>
            </div>
            <div class="cancel">
              <p>Cancelar</p>
            </div>
          </div>
        </div>
    </div>
    <div class="options-list">
        <div class="edit" title="Edita el hilo">
          <svg xmlns="http://www.w3.org/2000/svg" version="1.0" viewBox="0 0 640.000000 640.000000" preserveAspectRatio="xMidYMid meet" focusable="false">
            <g transform="translate(0.000000,640.000000) scale(0.100000,-0.100000)" stroke="none">
              <path d="M4833 5726 c-65 -21 -95 -46 -305 -254 l-197 -196 90 -42 c335 -160 654 -479 813 -814 l42 -90 206 208 c213 215 242 254 254 343 12 91 -65 310 -152 433 -146 204 -352 351 -568 406 -91 23 -124 24 -183 6z"></path>
              <path d="M2573 3516 c-759 -759 -1394 -1401 -1410 -1426 -17 -25 -44 -72 -60 -105 -43 -86 -391 -1012 -399 -1061 -18 -119 104 -242 220 -220 47 9 997 366 1066 401 30 15 75 41 100 58 60 40 2800 2777 2800 2797 0 43 -140 288 -227 397 -66 83 -196 215 -281 285 -108 89 -276 192 -393 241 l-36 15 -1380 -1382z"></path>
            </g>
          </svg>
          <p>Editar</p>
        </div>
        <div class="delete" title="Elimina el hilo">
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
    
    <script src="js/sidebar.js"></script>
    <script src="js/modal.js"></script>
    <script>
      var selectedElement = "";
    </script>
    <div class="new-content" id="feed">
        <?php
            
            $repository = new Repository();
            $page = isset($_GET['page']) ? $_GET['page'] : 1;
            $news = $repository->getNewsPaginated($page);

            if(count($news) == 0) {
              ?>
              </div>
                <div class="none">
                  <p>No encontrado.</p>
                </div>
              <?php

              return;
            }

            foreach($news as $key => $new) {
            ?>

            <div class="<?php echo $new->id; ?>">
            <div class="post-account">
                <img alt="<?php echo $new->user->id . '\'s icon' ?>" src="<?php echo isset($new->user->icon) ? $new->user->icon : 'profile-none.jpg'; ?>" />
            </div>
            <div class="post-text">
                <div class="account">
                  <div class="profile">
                    <div class="name">
                            <p><?php echo $new->user->name ?></p>
                        </div>
                        <div class="verified">
                            <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 122.88 116.87"><defs><style>.cls-1{fill-rule:evenodd;}</style></defs><title>verify</title><path class="cls-1" d="M61.37,8.24,80.43,0,90.88,17.78l20.27,4.54-2,20.53,13.73,15.58L109.2,73.87l2,20.68L91,99,80.43,116.87l-18.92-8.25-19.06,8.25L32,99.08,11.73,94.55l2-20.54L0,58.43,13.68,43,11.73,22.32l20.15-4.45L42.45,0,61.37,8.24ZM37.44,64.55c-6.07-6.53,3.25-16.26,10-10.1,2.38,2.17,5.84,5.34,8.24,7.49L74.18,39.18C80.62,32.53,90.79,42.3,84.43,49L61.2,76.72a7.13,7.13,0,0,1-9.91.44C47.35,73.41,41.57,68,37.44,64.55Z"/></svg>
                        </div>
                        <div class="user">
                            <p>@<?php echo $new->user->id ?></p>
                        </div>
                    </div>
                    <div class="space">
                        <span>-</span>
                    </div>
                    <div class="date">
                        <p><?php echo epochTime($new->date, true) ?></p>
                    </div>
                    
                </div>
                <div class="content">
                <h1><?php echo $new->title ?></h1>
                <p><?php echo $new->content ?></p>

                <?php 

                  $images_encoded = json_encode($new->images);


                  if(count($new->images) != 0) {
                ?>

                <div class="<?php echo count($new->images) > 1 ? "images multiple" : "images" ?>">

                <?php

                  if(count($new->images) > 1) {

                ?>
                    <div class="primary">
                        <img src="<?php echo $new->images[0] ?>"
                        data-images='<?php echo htmlspecialchars($images_encoded, ENT_QUOTES, 'UTF-8'); ?>'
                        onclick="openModal('<?php echo $new->images[0] ?>', JSON.parse(this.dataset.images), true)">
                    </div>
                    <div class="secondary">
                        <?php if(count($new->images) > 2) {  ?>
                          <img src="<?php echo $new->images[1] ?>"
                        data-images='<?php echo htmlspecialchars($images_encoded, ENT_QUOTES, 'UTF-8'); ?>'
                        onclick="openModal('<?php echo $new->images[1] ?>', JSON.parse(this.dataset.images), true)"
                        class="first">
                        <img src="<?php echo $new->images[2] ?>"
                        data-images='<?php echo htmlspecialchars($images_encoded, ENT_QUOTES, 'UTF-8'); ?>'
                        onclick="openModal('<?php echo $new->images[2] ?>', JSON.parse(this.dataset.images), true)"
                        class="second">
                        <?php } else { ?>
                          <img src="<?php echo $new->images[1] ?>"
                        data-images='<?php echo htmlspecialchars($images_encoded, ENT_QUOTES, 'UTF-8'); ?>'
                        onclick="openModal('<?php echo $new->images[1] ?>', JSON.parse(this.dataset.images), true)">
                        <?php }
                        if(count($new->images) >= 4) {   
                            ?>
                          <div class="overlay" src="<?php echo $new->images[2] ?>" data-images='<?php echo htmlspecialchars($images_encoded, ENT_QUOTES, 'UTF-8'); ?>'
                          onclick="openModal('<?php echo $new->images[2] ?>', JSON.parse(this.dataset.images), true)"></div>
                            <p
                            onclick="openModal('<?php echo $new->images[2] ?>', JSON.parse(this.dataset.images), true)"><?php echo "+" . (count($new->images) - 2); ?></p>
                            <?php
                            
                          }
                          ?>
                            
                            </div>


                <?php
                  } else {

                    foreach($new->images as $img) {

                    ?>
                    
                        <img src="<?php echo $img ?>" onclick="openModal('<?php echo $img ?>', ['<?php echo $img ?>'], true)">


                    <?php
                    }
                  }
                ?>
                  </div>

                  <?php

                  }

                  ?>
            </div>
            </div>

            <?php 
              if($user->id == $new->user->id || $user->verified == 1) {
            ?>

            <div class="options">
              <div class="button" onclick="
                  event.stopPropagation();
                  const rect = this.getBoundingClientRect();
          
                  let top = rect.bottom + window.scrollY;
                  let left = rect.right - optionsList.offsetWidth;

                  if (top + optionsList.offsetHeight > window.innerHeight + window.scrollY) {
                      top = rect.top + window.scrollY - optionsList.offsetHeight;
                  }
          
                  optionsList.style.top = `${top}px`;
                  optionsList.style.left = `${left}px`;
                  if(selectedElement == '<?php echo $new->id ?>' || selectedElement == '') {
                      optionsList.classList.toggle('show');
                  }
                  selectedElement = '<?php echo $new->id; ?>'
                  
                  ">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.0" viewBox="0 0 640.000000 640.000000" preserveAspectRatio="xMidYMid meet" focusable="false">
                  <g transform="translate(0.000000,640.000000) scale(0.100000,-0.100000)" stroke="none">
                  <path d="M3050 6284 c-47 -13 -132 -47 -190 -76 -92 -45 -116 -62 -195 -142 -76 -76 -99 -107 -137 -186 -96 -194 -120 -364 -77 -533 62 -242 198 -422 398 -526 125 -65 197 -83 352 -88 127 -5 144 -4 230 21 284 81 489 302 558 602 30 130 28 217 -9 348 -34 121 -63 183 -127 273 -106 149 -266 254 -466 307 -128 34 -222 34 -337 0z"/>
                  <path d="M3078 3979 c-184 -27 -390 -163 -509 -335 -125 -182 -169 -427 -114 -634 80 -297 283 -497 586 -577 90 -23 292 -23 379 0 153 42 302 141 406 270 110 138 184 341 184 505 0 132 -61 322 -141 439 -171 249 -478 378 -791 332z"/>
                  <path d="M3018 1651 c-161 -45 -304 -138 -405 -265 -80 -102 -127 -198 -158 -322 -35 -141 -34 -236 6 -373 65 -225 175 -371 364 -487 102 -62 284 -114 399 -114 81 0 225 36 322 80 169 76 313 222 389 394 40 91 75 241 75 324 0 155 -84 379 -189 508 -89 109 -248 210 -396 250 -94 25 -322 28 -407 5z"/>
                  </g>
                </svg>
              </div>
            </div>
           
            <?php

              }

              ?>
              </div>
              <?php

              }
            ?>
      

      <script src="js/new.js"></script>
      <script src="js/options-menu.js"></script>

        
            
    </div>
    <div class="bottom">
      <div class="paginated">
        <?php
          $repository = new Repository();
          $max_pages = $repository->getMaxNewsPaginated();

          $page = isset($_GET['page']) ? $_GET['page'] : 1;


          if($page - 2 > 0) {
            ?>
            <a href="news.php?page=1">
                <p>&lt;&lt;</p>
            </a>
            <?php
          }
          if($page - 1 > 0) {
            ?>
            <a href=<?php echo "news.php?page=" . $page - 1; ?>>
                <p>&lt;</p>
            </a>
            <?php
          }
        ?>
        <?php

        if($page - 1 > 0) {
          
        ?>
        <a href=<?php echo "news.php?page=" . $page - 1; ?>>
            <p><?php echo $page - 1; ?></p>
        </a>

        <?php
        }
        
        ?>
        <a href= <?php echo "news.php?page=" . $page; ?> class="current">
            <p><?php echo $page; ?></p>
        </a>
        
        <?php
          if($page + 1 <= $max_pages) {

        ?>
        <a href=<?php echo "news.php?page=" . $page + 1; ?>>
            <p><?php echo $page + 1; ?></p>
        </a>

        <?php

          }
          if($page + 2 <= $max_pages) {

          ?>
          <a href=<?php echo "news.php?page=" . $page + 2; ?>>
              <p><?php echo $page + 2; ?></p>
          </a>
  
          <?php
  
            }
          
        if($page + 1 <= $max_pages) {
            ?>
        <a href=<?php echo "news.php?page=" . $page + 1; ?>>
          <p>&gt;</p>
        </a>
        <?php 
        }

        if($page + 2 < $max_pages) {
        ?>
        <a href=<?php echo "news.php?page=" . $max_pages; ?>>
            <p>&gt;&gt;</p>
        </a>
        <?php
        }
        ?>
      </div>
    </div>
  </body>
</html>
