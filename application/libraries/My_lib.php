<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class My_lib
{
  protected $_ci;
	var $key_jos = "asik-asik_jos";
  var $pattern = "/.gif|.jpg|.jpeg|.png|.mp3|.mp4|.mpeg|.ogg|.pdf|.GIF|.JPG|.JPEG|.PNG|.MP3|.MP4|.MPEG|.OGG|.PDF$/";
  var $uploadPath  = './assets/images/';
  var $allowedYypes = 'gif|jpg|jpeg|png|mp3|mp4|mpeg|ogg|pdf|GIF|JPG|JPEG|PNG|MP3|MP4|MPEG|OGG|PDF';

	function __construct()
  {
      $this->_ci =&get_instance();
  }

  function decryptText($text)
  {
    $this->_ci->load->library('encrypt');
    return $this->_ci->encrypt->decode($text, $this->key_jos);
  }

  function encryptText($text)
  {
  	$this->_ci->load->library('encrypt');
		return $this->_ci->encrypt->encode($text, $this->key_jos);
  }

  function login($user_email, $upass)
  {
    date_default_timezone_set('Asia/Jakarta');
    $this->_ci->db->select('users.*, users_group.group_name');
    $this->_ci->db->from('users');
    $this->_ci->db->join('users_group', 'users.group_id = users_group.id_user_group');
    $this->_ci->db->where('user_email', $user_email);
    $query = $this->_ci->db->get()->result();  
    if (count($query) != 0)
    {
      foreach ($query as $row)
      {
        $uid = $row->id_user;
        $userName = $row->user_name;
        $pass = $row->user_password;
        $gid = $row->group_id;
        $groupName = $row->group_name;
        $lastLogin = $row->last_login;
      }

			$decpass = $this->decryptText($pass);
			if ($upass == $decpass)
			{
				$data_session = array(
							'ses_uid'=>$uid,
							'ses_user_name'=>$userName,
              'ses_gid'=>$gid,
              'ses_group_name'=>$groupName,
							'ses_last_login'=>$lastLogin
						);
				$this->_ci->load->library('session');
				$this->_ci->session->set_userdata($data_session);

        /** update last  login **/
        $data = array(
              'last_login' => date("Y-m-d h:i:s")
            );
        $this->_ci->db->where('id_user', $uid);
        $this->_ci->db->update('users', $data);
        redirect('be/user');
			}else{
				return "Wrong user password ";
			}
    }else{
      return "Wrong user name";
    }
  }

  function logout(){
    $this->_ci->session->sess_destroy();
    redirect('be/login');
  }

  //cek user auth
  function cekAuth()
  {
    if ($this->_ci->session->userdata('ses_uid') != '')
    {
      return $this->_ci->session->userdata('ses_user_name');
    }else{
      redirect('be/login');
    }
  }

    // menu
	function menu_be()
	{
		$arrayName = array('viewed' => '1', 'master' => '0');
		$this->_ci->db->select('*');
		$this->_ci->db->where($arrayName);
		$this->_ci->db->order_by('menu_order ASC');
		return $this->_ci->db->get('menu')->result();
	}
	
	function sub_menu_be()
	{
		$this->_ci->db->select('*');
		$this->_ci->db->where('viewed = 1 and master != 0');
		$this->_ci->db->order_by('id ASC');
		return $this->_ci->db->get('menu')->result();
	}
    //end menu

  function unggah($nminput, $nmfile){
    $config['upload_path'] = $this->uploadPath;
    $config['allowed_types'] = $this->allowedYypes;
    $config['max_size']	= '30000';
    $config['file_name']  = $nmfile;
    $this->_ci->load->library('upload', $config);
    if ( ! $this->_ci->upload->do_upload($nminput))
    {
        return "0";
    }else{
         return "1";
    }
  }

  function uploadFile($inputFileName, $fileName, $i='')
  {
    date_default_timezone_set('Asia/Jakarta');
    $this->_ci->load->library('upload');
    $newname = "";
    if (preg_match($this->pattern, $fileName, $matches,PREG_OFFSET_CAPTURE)) 
    {
      $ext = $matches[0][0];
      $oldname = str_replace($ext, "", $fileName);
      $newname = date("Ymd_His")."-".$i.$ext;
      
      $config = array(
                      'file_name'     => $newname,
                      'upload_path'   => $this->uploadPath,
                      'allowed_types' => $this->allowedYypes,
                      'overwrite'     => 1
                  );
      $this->_ci->upload->initialize($config);
      if ( ! $this->_ci->upload->do_upload($inputFileName)) 
      {
        array('error' => $this->_ci->upload->display_errors());
        return "0";
      } else {
        // Continue processing the uploaded data
        $this->_ci->upload->data();
        return $newname;
      }
    } else {
        return "0";
    }
  }
}