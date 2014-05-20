<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Category extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('url');
		$this->load->model('report_model');

	}

	public function index()
	{
		try
		{
			$arr = array();
			$temp = $this->getlist(0,1);
			foreach ($temp as $key=>$value) {
				$list = $this->getlist($value->id,1);
				$temp ='';
				foreach ($list as $value1) {
					$cateId = $value1->id;
					$this->db->from('report');
					$this->db->where('scId2',$cateId);	
					$count = $this->db->count_all_results();

					$temp[] = array("name"=>$value1->cateName,"url"=>"/category/catelist/1_".$cateId."_1","count"=>$count);
				}
				$arr[] = array('name'=>$value->cateName,'list'=>$temp );
			}
			//print_r($arr);
			$data['title'] = '公務員出國考察追蹤網';
			$data['list'] = $arr;	
	
			$this->load->view('templates/header', $data);
			$this->load->view('category/catetype1', $data);
			$this->load->view('templates/footer');	    
		}
		catch (Exception $err)
		{
		    log_message("error", $err->getMessage());
		    return show_error($err->getMessage());
		}
	}
	public function catetype($set=1)
	{
		try
		{
			$arr = array();
			$temp = $this->getlist($set,2);
			foreach ($temp as $key=>$value) {
				$list = $this->getlist($value->id,2);
				if(count($list)>0):
					$temp =null;
					foreach ($list as $value1) 
					{
						$cateId = $value1->id;
						$this->db->from('report');
						$this->db->where('pcId',$cateId);
						$count = $this->db->count_all_results();	
						$temp[] = array('name'=>$value1->cateName,"url"=>"/category/catelist/2_".$cateId."_1","count"=>$count);
					}

					$cateId = $value->id;
					$this->db->from('report');
					$this->db->where('pcId',$cateId);
					$count = $this->db->count_all_results();	

					$arr[] = array('id'=>$value->id,'name'=>$value->cateName,'count','list'=>$temp,'url'=>"/category/catelist/2_".$cateId."_1","count"=>$count );
					//print_r($arr);exit;
				endif;
			}
			$data['title'] = '公務員出國考察追蹤網';
			$data['cateList'] = $this->getlist(0,2);
			$data['list'] = $arr;	
	
			$this->load->view('templates/header', $data);
			$this->load->view('category/catetype2', $data);
			$this->load->view('templates/footer');	    
		}
		catch (Exception $err)
		{
		    log_message("error", $err->getMessage());
		    return show_error($err->getMessage());
		}
	}

	public function catelist($set="1_1",$page="1")
	{
		$this->load->library('pagination');
		$this->load->library('app/paginationlib');		
		try
		{
			$limit = 50;
			$temp = explode('_', $set);
			$cateType = $temp[0];
			$cateId = $temp[1];
			$page = ($page)?$page:'1';

			$this->db->from('category');
			$this->db->where('id =',$cateId);
			$query = $this->db->get();	
			$cateName = $query->result();

			$this->db->from('category');
			$this->db->where('id =',$cateName[0]->cateBid);
			$query = $this->db->get();	
			$tcateName = $query->result();

			$this->db->from('report');
			if($cateType==1)
				$this->db->where('scId2',$cateId);
			else
				$this->db->where('pcId',$cateId);
			
			$count = $this->db->count_all_results();
			
			$start = ($page-1)*$limit;

			$this->db->select('report.*,authority.name as authority');
			$this->db->from('report');
			$this->db->join('authority', 'report.authority=authority.aId');			
			if($cateType==1)
				$this->db->where('report.scId2',$cateId);
			else
				$this->db->where('report.pcId',$cateId);

			$this->db->order_by('report.reportDate','desc');
			$this->db->limit($limit,$start);
			$query = $this->db->get();
			
			$data['list'] = $query->result();

			//print_r($data['list']);

			$data['title'] = '公務員出國考察追蹤網';
			
			$this->paginationlib->initPagination("category/catelist/{$cateType}_{$cateId}",$count,$page);
			$data['pageList']   = $this->pagination->create_links();


			$data['cateName'] = $cateName;
			$data['tcateName'] = $tcateName;

	
			$this->load->view('templates/header', $data);
			$this->load->view('category/list', $data);
			$this->load->view('templates/footer');	    
		}
		catch (Exception $err)
		{
		    log_message("error", $err->getMessage());
		    return show_error($err->getMessage());
		}
	}

	public function gethtml($url){
		// 初始化一個 cURL 對象
		$ch = @curl_init();
		$options = array(
						CURLOPT_URL => $url,// 設置你需要抓取的URL
						//CURLOPT_REFERER => $referer,
						CURLOPT_HEADER => false,// 設置header
						CURLOPT_RETURNTRANSFER => true,// 設置cURL 參數，要求結果保存到字符串中還是輸出到屏幕上。
						CURLOPT_USERAGENT => "Google Bot",
						CURLOPT_FOLLOWLOCATION => true,	
						//CURLOPT_CONNECTTIMEOUT  => $timeout,	
						//CURLOPT_COOKIE => $cookie
				   );
		curl_setopt_array($ch, $options);
		return $Contents = curl_exec($ch);
	}

	public function getlist($id=0,$cateType=1){
		$this->db->from('category');
		$this->db->where('cateType =',$cateType);
		$this->db->where('cateBid =',$id);
		$this->db->order_by('id','asc');
		$query = $this->db->get();	
		return $query->result();
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */