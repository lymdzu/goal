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

    /**
     *
     * @param $team_id
     * @return mixed
     * @author liuyongming@shopex.cn
     */
    public function get_team_by_id($team_id)
    {
        $this->db->where("team_code", $team_id);
        $query = $this->db->get("t_teams");
        return $query->row_array();
    }

    /**
     *
     * @param $match_code
     * @param $match_name
     * @param $league
     * @param $season
     * @param $match_time
     * @param $host
     * @param $guest
     * @param $total_score
     * @param $half_score
     * @param $host_rank
     * @param $guest_rank
     * @param $total_conced
     * @param $half_conced
     * @param $total_sum
     * @param $half_sum
     * @param $turn
     * @return bool
     * @author liuyongming@shopex.cn
     */
    public function insert_match($match_code, $match_name, $league, $season, $match_time, $host, $guest, $total_score, $half_score, $host_rank, $guest_rank, $total_conced, $half_conced, $total_sum, $half_sum, $turn, $time)
    {
        $status = $this->db->insert("t_score", array("match_id" => $match_code, "match_name" => $match_name, "host_team" => $host, "guest_team" => $guest, "match_time" => $match_time, "league" => $league, "status" => $season, "total_score" => $total_score, "half_score" => $half_score, "host_rank" => $host_rank, "guest_rank" => $guest_rank, "total_conced" => $total_conced, "half_conced" => $half_conced, "total_sum" => $total_sum, "half_sum" => $half_sum, "turn" => $turn, "time" => $time));
        if ($status && $this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }


    public function update_match($match_code, $match_name, $league, $season, $match_time, $host, $guest, $total_score, $half_score, $host_rank, $guest_rank, $total_conced, $half_conced, $total_sum, $half_sum, $turn, $time)
    {
        $this->db->where("match_id", $match_code);
        $status = $this->db->update("t_score", array("match_name" => $match_name, "host_team" => $host, "guest_team" => $guest, "match_time" => $match_time, "league" => $league, "status" => $season, "total_score" => $total_score, "half_score" => $half_score, "host_rank" => $host_rank, "guest_rank" => $guest_rank, "total_conced" => $total_conced, "half_conced" => $half_conced, "total_sum" => $total_sum, "half_sum" => $half_sum, "turn" => $turn, "time" => $time));
        if ($status && $this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function get_match_by_id($match_id)
    {
        $this->db->where("match_id", $match_id);
        $query = $this->db->get("t_score");
        return $query->row_array();
    }
    public function get_team_all_match($team_id)
    {
        $query = $this->db->query("SELECT * FROM t_score WHERE status = -1 and (host_team = {$team_id} OR guest_team = {$team_id}) ORDER BY `time` DESC ");
        return $query->result_array();
    }

    public function get_today_match()
    {
        $this->db->where(array("s.time > " => time()-12800, "s.time<=" => time() + 43200));
        $this->db->order_by("s.time", "asc");
        $this->db->from("t_score s");
        $this->db->join("league l", "l.league=s.league");
        $query = $this->db->get();
        return $query->result_array();

    }
}