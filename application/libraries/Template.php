<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Template {
  protected $_ci;
  function __construct()
  {
    $this->_ci =&get_instance();
  }

  function adminDisplay($template,$data=null)
  {
    $data['_adminContent']=$this->_ci->load->view($template,$data, true);
    $data['_adminHeader']=$this->_ci->load->view('admin/header',$data, true);
    $data['_adminSideBar']=$this->_ci->load->view('admin/sideBar',$data, true);
    $data['_adminFooter']=$this->_ci->load->view('admin/footer',$data, true);
    $this->_ci->load->view('templates/adminTemplate.php',$data);
  }

  function siteDisplay($template,$data=null)
  {
    $data['_siteContent']=$this->_ci->load->view($template,$data, true);
    $data['_siteHeader']=$this->_ci->load->view('site/header',$data, true);
    $data['_siteFooter']=$this->_ci->load->view('site/footer',$data, true);
    $this->_ci->load->view('templates/siteTemplate.php',$data);
  }
}
