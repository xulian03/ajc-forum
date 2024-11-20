<?php
    session_start();

    include 'db.php';

    if($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: home.php");
        exit();
    }

    if(!isset($_POST['id'])) {
        header("Location: home.php");
        exit();
    }

    function validate($str) {
        return htmlspecialchars(stripslashes(trim($str)));
    }

    if(!isset($_SESSION['id'])) {
        echo 0;
        exit();
    }

    $id = $connection -> real_escape_string(validate($_POST['id']));
    $vote = $connection -> real_escape_string(validate($_POST['vote']));
    $user = $_SESSION['id'];

    

    $result = mysqli_query($connection, "SELECT 
      s.votes AS survey_votes,
      s.multi_select AS survey_multi_select,
      u.surveys AS user_surveys
  FROM 
      survey s
  JOIN 
      user u ON u.id = '$user'
  WHERE
      s.id = '$id'");

      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $votes = json_decode($row["survey_votes"], true);
        $votes = isset($votes) ? $votes : array();

        $surveys = json_decode($row["user_surveys"], true);
        $surveys = isset($surveys) ? $surveys : array();

        $multiselect = $row["survey_multi_select"];

        if(array_key_exists($vote, $votes)) {
            if($multiselect) {
                if (array_key_exists($id, $surveys) && in_array($vote, $surveys[$id])) {
                    $votes[$vote] -= 1;
                    unset($surveys[$id][array_search($vote, $surveys[$id])]);
                } else {
                    $votes[$vote] += 1;
                    $surveys[$id][] = $vote;
                }
            } else {
                $same = 0;
                if (array_key_exists($id, $surveys)) {
                    if($surveys[$id] == $vote) {
                        $same = 1;
                    }
                    $votes[$surveys[$id]] -= 1;
                    unset($surveys[$id]);
                }
                
                if (!isset($surveys[$id]) && !$same) {
                    $votes[$vote] += 1;
                    $surveys[$id] = $vote;
                }
                
            }
        }

        $votes_encoded = json_encode($votes);
        $surveys_encoded = json_encode($surveys);

        $survey_query = mysqli_query($connection, "UPDATE survey SET votes='{$votes_encoded}' WHERE id='{$id}'");
        
        $user_query = mysqli_query($connection, "UPDATE user SET surveys='{$surveys_encoded}' WHERE id='{$user}'");

        $votes_num = array_sum($votes);
        foreach($votes as $key => $value) {
        $selected = array_key_exists($id, $surveys) && ($surveys[$id] == $key || ($multiselect == 1 && in_array($key, $surveys[$id])));
      ?>
        
        <div class="<?php echo $selected ? 'option selected' : 'option'; ?>"  onclick="vote(this, '<?php echo $id; ?>', '<?php echo $key; ?>', <?php echo $multiselect; ?>)">
        
            <div class="box">
                <div class="progress" style="max-width: <?php echo $votes_num == 0 ? 0 : round(($value * 100) / $votes_num, 2); ?>%;"></div>
                <p><?php echo $key ?></p><span><?php echo $votes_num == 0 ? 0 : round(($value * 100) / $votes_num, 2); ?></span>
                
                <span class="percentage">%</span>
            </div>
              
        </div>

        <?php

        }

        ?>

          <?php
            if($votes_num > 1 || $votes_num == 0) {
              ?>
              <p class="votes"><?php echo $votes_num . ' votos' ?></p>
              <?php
            } else if($votes_num == 1) {
              ?>
              <p class="votes"><?php echo $votes_num . ' voto' ?></p>
              <?php

            }
      }


?>