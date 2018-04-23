<?php

/**
 * 文件名称:Match.php
 * 摘    要:
 * 修改日期: 21/4/18
 */
class Match extends PublicController
{
    public function show()
    {
        $this->load->model("MatchModel", "match", true);
        $matchs = $this->match->get_today_match();
        foreach ($matchs as $match) {
            $host_matches = $this->match->get_team_all_match($match['host_team']);
            $guest_matches = $this->match->get_team_all_match($match['guest_team']);
            $host_total = 0;
            $guest_total = 0;
            $host_score_arr = array();
            $guest_score_arr = array();
            $guest_scorea_arr = array();
            $host_trend = array();
            $guest_trend = array();
            foreach ($host_matches as $host_match) {
                list($host_score, $guest_score) = explode("-", $host_match['total_score']);
                $host_score_arr[] = $host_match['host_team'] == $match['host_team'] ? $host_score : $guest_score;
                $score_diff = $host_match['host_team'] == $match['host_team'] ? ($host_score - $guest_score) : ($guest_score - $host_score);
                $rank_diff = $host_match['host_team'] == $match['host_team'] ? ($host_match['host_rank'] - $host_match['guest_rank']) : ($host_match['guest_rank'] - $host_match['host_rank']);
                if ($host_match['host_team'] == $match['host_team']) {
                    if ($host_match['host_rank'] > $host_match['guest_rank']) {//主队做主
                        if ($score_diff > 0) {
                            $host_trend[] = bcmul($score_diff * $rank_diff, 1.3, 2);//队伍坐镇主场排名小于客,若胜方法1.3,若负正常
                        } elseif ($score_diff == 0) {
                            $host_trend[] = 1.1;
                        } else {
                            $lose_factor = bcdiv(3, $rank_diff, 2);
                            $host_trend[] = bcmul($score_diff, $lose_factor, 2);
                        }

                    } else {
                        if ($score_diff > 0) {
                            $host_trend[] = $score_diff * $rank_diff;//队伍坐镇主场排名大于客,若胜正常,若负放大输
                        } elseif ($score_diff == 0) {
                            $host_trend[] = -1.3;
                        } else {
                            $host_trend[] = bcmul($score_diff * -$rank_diff, 1.7, 2);
                        }
                    }
                } else {
                    //主队做客
                    if ($host_match['guest_rank'] > $host_match['host_rank']) {
                        if ($score_diff > 0) {
                            $host_trend[] = bcmul($score_diff * $rank_diff, 1.7, 2);//队伍坐镇客场排名小于主,若负正常,胜放大
                        } elseif ($score_diff == 0) {
                            $host_trend[] = 1.3;
                        } else {
                            $lose_factor = bcdiv(3, $rank_diff, 2);
                            $host_trend[] = bcmul($score_diff, $lose_factor, 2);
                        }

                    } else {
                        if ($score_diff > 0) {
                            $host_trend[] = bcmul($score_diff * -$rank_diff, 1.3, 2);//队伍坐镇客场排名大于主,若胜正常,若负放大输
                        } elseif ($score_diff == 0) {
                            $host_trend[] = bcdiv($rank_diff, 3, 2);
                        } else {
                            $host_trend[] = bcmul($score_diff * -$rank_diff, 1.5, 2);//失败放大
                        }
                    }
                }
            }
            //客队的比赛
            foreach ($guest_matches as $guest_match) {
                list($host_score, $guest_score) = explode("-", $guest_match['total_score']);
                $guest_score_arr[] = $guest_match['host_team'] == $match['guest_team'] ? $host_score : $guest_score;
                $score_diff = $guest_match['guest_team'] == $match['guest_team'] ? ($guest_score - $host_score) : ($host_score - $guest_score);
                $rank_diff = $guest_match['guest_team'] == $match['guest_team'] ? ($guest_match['guest_rank'] - $guest_match['host_rank']) : ($guest_match['host_rank'] - $guest_match['guest_rank']);
                if ($guest_match['guest_team'] == $match['guest_team']) {//客队做客
                    if ($guest_match['host_rank'] > $guest_match['guest_rank']) {
                        if ($score_diff > 0) {
                            $guest_trend[] = bcmul($score_diff * -$rank_diff, 1.3, 2);//队伍客场排名大于主,若胜方法1.3,若负正常
                        } elseif ($score_diff == 0) {
                            $guest_trend[] = -1.1;
                        } else {
                            $guest_trend[] = bcmul($score_diff * -$rank_diff, 1.3, 2);
                        }

                    } else {
                        if ($score_diff > 0) {
                            $guest_trend[] = bcmul($score_diff * -$rank_diff, 1.7, 2);//队伍坐镇主场排名大于客,若胜正常,若负放大输
                        } elseif ($score_diff == 0) {
                            $guest_trend[] = 1.3;
                        } else {
                            $lose_factor = bcdiv(3, -$rank_diff, 2);
                            $guest_trend[] = bcmul($score_diff, $lose_factor, 2);
                        }
                    }
                } else {
                    if ($guest_match['host_rank'] > $guest_match['guest_rank']) {//客队做主
                        if ($score_diff > 0) {
                            $guest_trend[] = bcmul($score_diff * $rank_diff, 1.7, 2);//队伍坐镇客场排名小于主,若负正常,胜放大
                        } elseif ($score_diff == 0) {
                            $guest_trend[] = 1.3;
                        } else {
                            $lose_factor = bcdiv(3, -$rank_diff, 2);
                            $guest_trend[] = bcmul($score_diff, $lose_factor, 2);
                        }
                    } else {
                        if ($score_diff > 0) {
                            $victor_factor = bcdiv(3, $rank_diff, 2);
                            $guest_trend[] = bcmul($score_diff, $victor_factor, 2);
                        } elseif ($score_diff == 0) {
                            $guest_trend[] = -1.1;
                        } else {
                            $guest_trend[] = bcmul($score_diff * -$rank_diff, 1.7, 2);//失败放大
                        }
                    }
                }
            }
            foreach ($guest_matches as $guest_match) {
                list($host_score, $guest_score) = explode("-", $guest_match['total_score']);
                $guest_score_arr[] = $guest_match['guest_team'] == $match['guest_team'] ? $guest_score : $host_score;
            }
            $host_trend_latest = array_slice($host_trend, 3);
            $host_trend_str = array_sum($host_trend_latest);
            $guest_trend_latest = array_slice($guest_trend, 3);
            $guest_trend_str = array_sum($guest_trend_latest);
            $host_total = array_sum($host_score_arr);
            $host_ave = bcdiv($host_total, count($host_score_arr), 2);
            $guest_total = array_sum($guest_score_arr);
            $guest_ave = bcdiv($guest_total, count($guest_score_arr), 2);
            $host_score_latest = array_slice($host_score_arr, 0, 3);
            $host_ave_latest = bcdiv(array_sum($host_score_latest), count($host_score_latest), 2);
            $guest_score_latest = array_slice($guest_score_arr, 0, 3);
            $guest_ave_latest = bcdiv(array_sum($guest_score_latest), count($guest_score_latest), 2);
            echo $match['match_time'] . $match['match_name'] . "主平均:" . $host_ave, "&nbsp;&nbsp;&nbsp;&nbsp;客平均:" . $guest_ave . "&nbsp;&nbsp;&nbsp;&nbsp;主最近:" . $host_ave_latest . "&nbsp;&nbsp;&nbsp;&nbsp;客最近:" . $guest_ave_latest . "&nbsp;&nbsp;&nbsp;&nbsp;主趋势:" . $host_trend_str . "&nbsp;&nbsp;&nbsp;&nbsp;客趋势:" . $guest_trend_str . "<br/>";

        }
        //        $this->page("match/admin_list.html");
    }
}