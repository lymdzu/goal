<?php

/**
 * 文件名称:Score.php
 * 摘    要:
 * 修改日期: 2018/4/16
 * 作    者: liuyongming@shopex.cn
 */
class Score extends MY_Controller
{
    public function team()
    {
        $this->load->model("MatchModel", "match", true);
        $matchs = $this->match->get_matchs();
        foreach ($matchs as $match) {
            $sub_league = $match['sub_league'] > 0 ? "_" . $match['sub_league'] : "";
            $season = $match['con'] == 1 ? "2018" : "2017-2018";
            $score = $this->call("get", BASE_SOCRE . $season . "/s" . $match['league'] . $sub_league . ".js?version=" . date("YmdH"), array("Referer:" . BASE_COOKIE . $match['eng_name'] . "/" . $match['league'] . ".html"));
            $score_arr = explode(";\n", $score);
            foreach ($score_arr as $item) {
                if (strpos($item, "arrTeam") !== false) {
                    $team_arr = explode("=", $item);
                    $team_str = trim($team_arr[1]);
                    $remove_leftbra = trim($team_str, "[[");
                    $remove_rightbra = trim($remove_leftbra, "]]");
                    $teams = explode("],[", $remove_rightbra);
                    foreach ($teams as $team) {
                        list($team_code, $full_name, $yue_name, $eng_name, $yue_name, $pic, $status) = explode(",", $team);
                        $this->load->model("MatchModel", "match", true);
                        $this->match->insert_teams($match['league'], $match['sub_league'], $team_code, trim($full_name, "'"), trim($eng_name, "'"), trim($yue_name, "'"));
                    }
                }
            }
        }
    }

    public function match()
    {
        $this->load->model("MatchModel", "match", true);
        $matchs = $this->match->get_matchs();
        foreach ($matchs as $match) {
            $sub_league = $match['sub_league'] > 0 ? "_" . $match['sub_league'] : "";
            $season = $match['con'] == 1 ? "2018" : "2017-2018";
            $score = $this->call("get", BASE_SOCRE . $season . "/s" . $match['league'] . $sub_league . ".js?version=" . date("YmdH"), array("Referer:" . BASE_COOKIE . $match['eng_name'] . "/" . $match['league'] . ".html"));
            $score_arr = explode(";\n", $score);
            $scores = array();
            foreach ($score_arr as $item) {
                if (strpos($item, "jh") === 0) {
                    $scores[] = $item;
                }
            }
            foreach ($scores as $key => $data) {
                $turn = $key + 1;
                $score_arr = explode("=", $data);
                $score_str = trim($score_arr[1]);
                $remove_leftbra = trim($score_str, "[[");
                $remove_rightbra = trim($remove_leftbra, "]]");
                $match_scores = explode("],[", $remove_rightbra);
                foreach ($match_scores as $match_score) {
                    list($match_code, $league, $status, $match_time, $host, $guest, $total_score, $half_score, $host_rank, $guest_rank, $total_conced, $half_conced, $total_sum, $half_sum) = explode(",", $match_score);
                    if ($status == "-10") {
                        continue;
                    }
                    $host_team = $this->match->get_team_by_id($host);
                    $guest_team = $this->match->get_team_by_id($guest);
                    $match = $this->match->get_match_by_id($match_code);
                    $match_name = $host_team['full_name'] . "vs" . $guest_team['full_name'];
                    if (empty($match)) {
                        $this->match->insert_match($match_code, $match_name, $league, $status, trim($match_time, "'"), $host, $guest, trim($total_score, "'"), trim($half_score, "'"), trim($host_rank, "'"), trim($guest_rank, "'"), $total_conced, $half_conced, trim($total_sum, "'"), trim($half_sum, "'"), $turn, strtotime(trim($match_time, "'") . ":00"));
                    } else {
                        $this->match->update_match($match_code, $match_name, $league, $status, trim($match_time, "'"), $host, $guest, trim($total_score, "'"), trim($half_score, "'"), trim($host_rank, "'"), trim($guest_rank, "'"), $total_conced, $half_conced, trim($total_sum, "'"), trim($half_sum, "'"), $turn, strtotime(trim($match_time, "'") . ":00"));
                    }
                }
            }
        }
    }

    public function plan()
    {
        $this->load->model("MatchModel", "match", true);
        $matchs = $this->match->get_today_match();
        print_r($matchs);
    }
}