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
            $score = $this->call("get", BASE_SOCRE . "2017-2018/s" . $match['league'] . $sub_league . ".js?version=" . date("YmdH"), array("Referer:" . BASE_COOKIE . $match['eng_name'] . "/" . $match['league'] . ".html"));
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
}