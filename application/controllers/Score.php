<?php

/**
 * 文件名称:Score.php
 * 摘    要:
 * 修改日期: 2018/4/16
 * 作    者: liuyongming@shopex.cn
 */
class Score extends MY_Controller
{
    public function get()
    {
        $this->load->model("MatchModel", "match", true);
        $matchs = $this->match->get_matchs();
        foreach ($matchs as $match) {
            $sub_league = $match['sub_league'] > 0 ? "_" . $match['sub_league'] : "";
            $score = $this->call("get", BASE_SOCRE . "2017-2018/s" . $match['league'] . $sub_league . ".js?version=". date("YmdH"), array("Referer:". BASE_COOKIE . $match['eng_name'] . "/" .$match['league']. ".html"));
            $score_arr = explode(";\n", $score);
            foreach($score_arr as $item)
            {
                if(strpos($item, "arrTeam") !== false)
                {
                    $team_arr = explode("=", $item);
                    $team_str = trim($team_arr[1]);
                    $teams = json_decode($team_str, true);
                    echo $teams;
                    exit;
                }
            }
        }
    }
}