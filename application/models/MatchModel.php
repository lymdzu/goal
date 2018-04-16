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
}