<?php
	class Page {
		private $total;
		private $listRows;
		private $limit;
		private $uri;
		private $pageNum;
		private $config=array("header"=>"条记录","prev"=>"上一页","next"=>"下一页","first"=>"首页","last"=>"尾页");
		private $listNum=5;
		
		public function __construct($total,$listRows=10,$pa='') {
			$this->total=$total;
			$this->listRows=$listRows;
		 	$this->uri=$this->getUri($pa);
			$this->page=!empty($_GET["page"])?$_GET["page"]:1;
			$this->limit=$this->setLimit();
		 	$this->pageNum=ceil($total/$listRows);
		}

		public function setLimit() {
			return "Limit".($this->page-1)*$this->listRows.",{$this->listRows}";
		}

		public function getUri($pa) {
			$url=$_SERVER["REQUEST_URI"].(strpos($_SERVER["REQUEST_URI"],"?")?'':"?").$pa;
			$parse=parse_url($url);

			if(isset($parse["query"])) {
				parse_str($parse["query"],$params);
				unset($params["page"]);
				$url=$parse["path"]."?".http_build_query($params);
			}

			return $url;
		}

		function __get($args) {
			if($args=="limit") {
				return $this->limit;
			} else {
				return null;
			}
		}

		private function start() {
			if ($this->total==0) {
				return 0;
			} else {
				return ($this->page-1)*$this->listRows+1;
			}
			
		}

		private function end() {
			return min($this->page*$this->listRows,$this->total);
		}

		private function first() {
			$html='';
			if($this->page==1) {
				$html.='';
			} else {
				$html.="&nbsp;&nbsp;<a href='javascript:f1(\"{$this->uri}&page=1\")'>{$this->config["first"]}</a>&nbsp;&nbsp;";
			}
			return $html;
		}

		private function prev() {
			$html='';
			if($this->page==1) {
				$html.='';
			} else {
				$html.="&nbsp;&nbsp;<a href='javascript:f1(\"{$this->uri}&page=".($this->page-1)."\")'>{$this->config["prev"]}</a>&nbsp;&nbsp;";
			}
			return $html;
		}

		public function pageSelect() {
			$linkPage="<select>";
			$inum=floor($this->listNum/2);

			for ($i=$inum; $i >= 1; $i--) { 
				$page=$this->page-$i;
				if($page<1) 
					continue;
				$linkPage.="<option onclick='f1(\"{$this->uri}&page={$page}\")'>&nbsp;第&nbsp;{$page}&nbsp;页&nbsp;</option>";
			}
			$linkPage.="<option selected='true'>&nbsp;"."第&nbsp;".$this->page."&nbsp;页"."&nbsp;</option>";

			for ($i=1; $i <= $inum; $i++) { 
				$page=$this->page+$i;
				if($page<=$this->pageNum) 
					$linkPage.="<option onClick='f1(\"{$this->uri}&page={$page}\")'>第&nbsp;{$page}&nbsp;页&nbsp;</option>";
				else
					break;
			}

			return $linkPage."</select>";

		}

		public function pageSelect1() {
			$linkPage="<select>";
			$inum=$this->pageNum;

			$linkPage.="<option selected='true'>&nbsp;"."第&nbsp;".$this->page."&nbsp;页"."&nbsp;</option>";

			for ($i=1; $i <= $inum; $i++) { 
				$page=$this->page+$i;
				if($page<=$this->pageNum) 
					$linkPage.="<option onClick='f1(\"{$this->uri}&page={$page}\")'>第&nbsp;{$page}&nbsp;页&nbsp;</option>";
				else
					break;
			}

			return $linkPage."</select>";

		}

		private function pageList() {
			$linkPage="";
			$inum=floor($this->listNum/2);

			for ($i=$inum; $i >= 1; $i--) { 
				$page=$this->page-$i;
				if($page<1) 
					continue;
				$linkPage.="&nbsp;<a href='javascript:f1(\"{$this->uri}&page={$page}\");'>{$page}</a>&nbsp;";
			}
			$linkPage.="&nbsp;".$this->page."&nbsp;";

			for ($i=1; $i <= $inum; $i++) { 
				$page=$this->page+$i;
				if($page<=$this->pageNum) 
					$linkPage.="&nbsp;<a href='javascript:f1(\"{$this->uri}&page={$page}\");'>{$page}</a>&nbsp;";
				else
					break;
			}

			return $linkPage;
		}

		private function next() {
			$html='';
			if($this->page==$this->pageNum) {
				$html.='';
			} else {
				$html.="&nbsp;&nbsp;<a href='javascript:f1(\"{$this->uri}&page=".($this->page+1)."\")'>{$this->config["next"]}</a>&nbsp;&nbsp;";
			}
			return $html;
		}

		private function last() {
			$html='';
			if($this->page==$this->pageNum) {
				$html.='';
			} else {
				$html.="&nbsp;&nbsp;<a href='javascript:f1(\"{$this->uri}&page=$this->pageNum\")'>{$this->config["last"]}</a>&nbsp;&nbsp;";
			}
			return $html;
		}

		private function goPage() {
			return '&nbsp;&nbsp;<input type="text" onkeydown="javascript:if(event.keyCode==13) {/^\d+$/.test(this.value)?this.value=this.value:this.value=1;var page=(this.value>'.$this->pageNum.')?'.$this->pageNum.':this.value;f1(\''.$this->uri.'&page=\'+page+\'\')}" value="'.$this->page.'" style="width: 25px"><input type="button" value="GO" onclick="javascript: /^\d+$/.test(this.previousSibling.value)?this.previousSibling.value=this.previousSibling.value:this.previousSibling.value=1;var page=(this.previousSibling.value>'.$this->pageNum.')?'.$this->pageNum.':this.previousSibling.value;f1(\''.$this->uri.'&page=\'+page+\'\')">&nbsp;&nbsp;';
		}

		function fpage($display=array(0,1,2,3,4,5,6,7,8,9,10)) {
			$html[0]="&nbsp;&nbsp;<b>{$this->total}</b>{$this->config["header"]}&nbsp;&nbsp;";
			$html[1]="&nbsp;&nbsp;每页显示<b>".$this->listRows."</b>条，本页显示({$this->start()}-{$this->end()})</b>条&nbsp;&nbsp;";
			$html[2]="&nbsp;&nbsp;<b>{$this->page}/{$this->pageNum}</b>页&nbsp;&nbsp;";
			$html[3]=$this->first();
			$html[4]=$this->prev();
			$html[5]=$this->pageList();
			$html[6]=$this->next();
			$html[7]=$this->last();
// 			$html[8]=$this->pageSelect();
			if($this->pageNum>1){
				$html[9]=$this->goPage();
			} else {
				$html[9]='';
			}
			
			$html[10]=$this->pageSelect1();
			$fpage='';
			foreach ($display as $index) {
				$fpage.=$html[$index];
			}

			return $fpage;
		}
	}