<?php
  (function () use (&$_mysqli) {
    require_once(SCRIPTS_PATH . 'includes/shift_constants.php');

    $stats = array_fill_keys(array_merge(['all'], array_keys(SHIFT_GAMES)), array_fill_keys(array_keys(SHIFT_DATES), 0));

    foreach (SHIFT_DATES as $type => $stmt) {
      $where = (function () use ($stmt) {
        $str = '';

        $str .= $stmt;

        // if (defined("PAGE_SETTINGS") && PAGE_SETTINGS['shift']['owner']) {
        //   $str .= " AND owner_id='" . PAGE_SETTINGS['shift']['owner'] . "'";
        // }

        return $str;
      })();
      $query = collapseWhitespace("SELECT 
                                      scd.game_id,
                                      COUNT(scd.game_id) AS 'count'
                                   FROM shift_codes AS sc
                                   LEFT JOIN shift_code_data
                                      AS scd
                                      ON sc.code_id = scd.code_id
                                   WHERE 
                                      sc.code_state = 'active'
                                        AND ({$where})
                                   GROUP BY scd.game_id");
      $result = $_mysqli->query($query);

      foreach ($result as &$row) {
        $game = $row['game_id'];

        $stats['all'][$type] += $row['count'];
        $stats[$game][$type] += $row['count'];
      }
    }

    /**
     * Current SHiFT Code statistics
     */
    define("SHIFT_STATS", $stats);
  })();
?>