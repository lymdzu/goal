<?php

/**
 * 文件名称:MY_Controller.php
 * 摘    要:
 * 修改日期: 2018/4/16
 * 作    者: liuyongming@shopex.cn
 */
class MY_Controller extends CI_Controller
{
    function call($method, $url, $header, $params = array())
    {
        $options = array(
            CURLOPT_HEADER         => 0,
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT        => 120,
            CURLOPT_USERAGENT      => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36",
            CURLOPT_HTTPHEADER     => $header,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_AUTOREFERER    => true,
            CURLOPT_CONNECTTIMEOUT => 120,
            CURLOPT_MAXREDIRS      => 10,
        );

        $param_string = http_build_query($params);
        switch (strtolower($method)) {
            case 'post':
                $options += array(CURLOPT_POST       => 1,
                                  CURLOPT_POSTFIELDS => $param_string);
                break;
            case 'put':
                $options += array(CURLOPT_PUT        => 1,
                                  CURLOPT_POSTFIELDS => $param_string);
                break;
            case 'delete':
                $options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
                if ($param_string) {
                    $options[CURLOPT_URL] .= '?' . $param_string;
                }
                break;
            default:
                if ($param_string) {
                    $options[CURLOPT_URL] .= '?' . $param_string;
                }
        }

        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (!$result) {
            log_message("error", "get data error:" . curl_error($ch) . ", http_code:{$http_code}");
        }
        curl_close($ch);
        return $result;
    }
}

class PublicController extends MY_Controller
{
    public $layout;

    public function __construct()
    {
        parent::__construct();
        $this->layout = "admin/layout.html";
        $this->load->helper('url');
        $this->load->library('smarty/Smarty');
        $this->load->library('session');
        $this->smarty->setCompileDir(APPPATH . '/cache/tpl');
        $this->smarty->setTemplateDir(VIEWPATH);
        $this->smarty->left_delimiter = '{{';
        $this->smarty->right_delimiter = '}}';
        $this->smarty->registerPlugin('function', 'site_url', array($this, 'smarty_modifier_site_url'));
        $this->smarty->registerPlugin('function', 'base_url', array($this, 'smarty_modifier_base_url'));
        $this->smarty->registerPlugin('modifier', 'site_url', array($this, 'smarty_modifier_site_url'));
        $this->smarty->registerPlugin('modifier', 'base_url', array($this, 'smarty_modifier_base_url'));
        $this->vars['title'] = "goal";
    }
    /**
     * 显示页面
     * @param $view
     */
    public function display($view)
    {
        echo $this->fetch($view);
        exit();
    }

    /**
     * 加载页面
     * @param $view
     */
    public function page($view)
    {
        $this->vars['__PAGE__'] = $view;
        if ((isset($_SESSION['message']) && $_SESSION['message']) && !$this->vars['message']) {

            $this->vars['error_message'] = $_SESSION['message'] . '';
            $this->vars['message_code'] = intval($_SESSION['message_code']);
            unset($_SESSION['message_code']);
            unset($_SESSION['message']);
        }
        $this->display($this->layout);
        exit();
    }

    public function fetch($view)
    {
        $this->vars['BASE_URL'] = $this->config->base_url();
        $this->vars['SITE_URL'] = $this->config->site_url();
        foreach ($this->vars as $k => $v) {
            $this->smarty->assign($k, $v);
        }
        return $this->smarty->fetch($view);
    }

    public function smarty_modifier_base_url($s)
    {
        return $this->config->base_url($s);
    }


    public function smarty_modifier_site_url($s)
    {
        return $this->config->site_url($s);
    }

    /**
     * 格式化json输出
     * @param $code
     * @param $result
     * @param $emsg
     */
    public function json_result($code, $result, $emsg = "")
    {
        ob_end_clean();
        $result = array(
            'ecode'  => intval($code),
            'result' => $result,
            'emsg'   => $emsg
        );
        header('Content-Type: text/json;charset=utf8');
        echo json_encode($result);
        exit();
    }
}