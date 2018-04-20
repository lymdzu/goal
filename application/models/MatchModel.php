<?php

/**
 * 文件名称:MatchModel.php
 * 摘    要:
 * 修改日期: 2018/4/16
 * 作    者: liuyongming@shopex.cn
 */
class MatchModel extends MY_Model
{
    /**
     *
     * @return mixed
     * @author liuyongming@shopex.cn
     */
    public function get_matchs()
    {
        $query = $this->db->get("league");
        return $query->result_array();
    }

    public function insert_teams($league, $sub_league, $team_code, $full_name, $eng_name, $yue_name)
    {
        $status = $this->db->insert("t_teams", array("team_code" => $team_code, "full_name" => $full_name, "eng_name" => $eng_name, "yue_name" => $yue_name, "league" => $league, "sub_league" => $sub_league));
        if ($status && $this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
}