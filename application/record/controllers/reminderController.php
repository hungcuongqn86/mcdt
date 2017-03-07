<?php
/**
 * Class Xu ly thong thong tin loai danh muc
 */
class Record_ReminderController extends  Zend_Controller_Action {
	public function init(){
        $tempDirApp = Zend_Registry::get('conDirApp');
		$this->_dirApp = $tempDirApp->toArray();
		$this->view->dirApp = $tempDirApp->toArray();
		//Cau hinh cho Zend_layoutasdfsdfsd
		Zend_Layout::startMvc(array(
			    'layoutPath' => $this->_dirApp['layout'],
			    'layout' => 'index'
        ));
		//Load ca thanh phan cau vao trang layout (index.phtml)
		$response = $this->getResponse();		
		//Load cau hinh thu muc trong file config.ini de lay ca hang so dung chung
        $tempConstPublic = Zend_Registry::get('ConstPublic');
		$this->_ConstPublic = $tempConstPublic->toArray();			
		//Ky tu dac biet phan tach giua cac phan tu
		$this->view->delimitor 			= $this->_ConstPublic['delimitor'];		
		//Lay duong dan thu muc goc (path directory root)
		$this->view->baseUrl = $this->_request->getBaseUrl() . "/public/";	
		//Goi lop Listxml_modProject
		Zend_Loader::loadClass('Record_modReminder');
		$objConfig = new Extra_Init();
		$this->view->JSPublicConst = $objConfig->_setJavaScriptPublicVariable();		
		/* Dung de load file Js va css		/*/
		// Goi lop public
		$objLibrary = new Extra_Util();
		//Dinh nghia current modul code
		$this->view->currentModulCode = "REMINDER";
		$this->view->currentModulCodeForLeft = "REMINDER";
		//Lay dia chi anh trong Cookie
		$this->view->ShowHideimageUrlPath = $objLibrary->_getCookie("ImageUrlPath");
        if($this->view->ShowHideimageUrlPath==''){
            $this->view->ShowHideimageUrlPath= $this->_request->getBaseUrl().'/public/images/open_left_menu.gif';
        }
		$this->view->hideDisplayMeneLeft = "";
		//Hien thi file template
		$response->insert('header', $this->view->renderLayout('header.phtml','./application/views/scripts/'));    //Hien thi header 
		$response->insert('left', $this->view->renderLayout('left.phtml','./application/views/scripts/'));    //Hien thi header 		    
        $response->insert('footer', $this->view->renderLayout('footer.phtml','./application/views/scripts/'));  	 //Hien thi footer
	}
	/**
	 * Idea : Phuong thuc hien thi danh sach
	 */
	public function indexAction(){
		$objReminder 	= new Record_modReminder();
		$objInitConfig 	= new Extra_Init();
		$objFunction 	= new Efy_Function_RecordFunctions();
		$this->view->bodyTitle = 'C&#193;C C&#212;NG VI&#7878;C C&#7846;N X&#7916; L&#221;';
		$iFkUnit = $objFunction->doc_get_all_unit_permission_form_staffIdList($_SESSION['staff_id']);
        $sOwnerCode	= $_SESSION['OWNER_CODE'];
        $sOwnerCodeRoot = $objInitConfig->_getOwnerCode();
		$arrRss = $objReminder->eCSPROMPTTHE($_SESSION['staff_id'],$iFkUnit,$sOwnerCode);
		$iCount=sizeof($arrRss);
		$htmlString='<table cellspacing="0" cellpadding="0" border="0" align="center" width="100%" style="border-right:#000E7B;padding-bottom=10px"><tr style="height:5px"><td></td></tr>';
		$v_url=$objInitConfig->_setWebSitePath();
		for($i=0;$i<$iCount;$i++){
			$iSum=0;
			//ma loai ho so
			$v_recordtype = $arrRss[$i]['PK_RECORDTYPE'];
			//Ten loai ho so
			$v_recordtypename = $arrRss[$i]['C_NAME'];
			//Ho so cho nhan so bo qua mang
			$v_recordnetpreliminary = $arrRss[$i]['C_RECORD_NET_PRELIMINARY'];
			//Ho so moi tiep nhan
			$v_recordnew= $arrRss[$i]['C_NEW_RECORD'];
            //Ho so lien thong cho nhan
            $v_transitionnew= $arrRss[$i]['C_TRANSITION_RECORD'];
			//Ho so cho bo sung cua can bo tiep nhan
			$v_recordadditional = $arrRss[$i]['C_ADDITIONAL_RECORD'];
			//Ho so cho tra ket qua
			$v_recordresult = $arrRss[$i]['C_RECORD_RESULT'];
			//Ho so bi tu choi
			$v_recordrefuser = $arrRss[$i]['C_REFUSER'];
			//Ho so cho thu ly chinh
			$v_recordhanlder = $arrRss[$i]['C_SOLVING_HANLDER'];
			//Ho so lanh dao tra lai
			$v_recordleaderreturn = $arrRss[$i]['C_RECORD_LEADER_RETURN'];
			//Ho so lanh dao duyet chuyen can bo thu ly
			$v_recordwaitbackongate = $arrRss[$i]['C_RECORD_APPROVAL_RETURN'];
			//Ho so cho phan cong
			$v_recordassignment = $arrRss[$i]['C_RECORD_ASSIGNMENT'];
			//Ho so cho phe duyet
			$v_recordapproval = $arrRss[$i]['C_RECORD_APPROVAL'];
			//Ho so cho phe duyet bi qua han
			$v_recordapprovalover = $arrRss[$i]['C_RECORD_APPROVAL_OVER'];
			if($i%2==0){
				$htmlString.='<tr><td width="50%" class="nhac_viec" valign="top" style="padding-top:4px;padding-right:4px;padding-bottom:5px;">'.								
										'<div style="background-repeat:repeat-x;font-family:Arial, Helvetica, sans-serif;color:#a90000; text-align:left;font-size:15px;font-weight: bold;padding:0 0 0 10px; width:auto;">'.$v_recordtypename.'</div>';
			}
			else{
				$htmlString.='<td width="50%" class="nhac_viec" valign="top" style="padding-top:4px;padding-bottom:5px;">'.
										'<div style="background-repeat:repeat-x;font-family:Arial, Helvetica, sans-serif;color:#a90000; text-align:left;font-size:15px;font-weight: bold;padding:0 0 0 10px; width:auto;">'.$v_recordtypename.'</div>';
			}
			$htmlString.='<div class="DocContent" style="padding-left:20px;">';

			if($v_recordnetpreliminary<>0){
				$url=$v_url.'record/receiveonnet/index/?recordType='.$v_recordtype;
				$sSpace='';
				if($iSum<>0){
					$sSpace=', ';
				}
				$htmlString.='<a href=' . $url . '><font  size = 2px; color="black">'.$sSpace.'[Có '.
								$v_recordnetpreliminary
								.' h&#7891; s&#417; ch&#432;a ti&#7871;p nh&#7853;n s&#417; b&#7897; qua m&#7841;ng]</font></a>';
				$iSum++;
			}

			if($v_recordnew<>0){
                if($sOwnerCode==$sOwnerCodeRoot){
                    $url=$v_url.'record/receive/index?recordType='.$v_recordtype;
                }else{
                    $url=$v_url.'record/wreceive/index?recordType='.$v_recordtype;
                }

				$sSpace='';
				if($iSum<>0){
					$sSpace=', ';
				}
				$htmlString.='<a href=' . $url . '><font  size = 2px; color="black">'.$sSpace.'[Có '.
								$v_recordnew
								.' h&#7891; s&#417; m&#7899;i ti&#7871;p nh&#7853;n]</font></a>';
				$iSum++;
			}

            if($v_transitionnew<>0){
                $url=$v_url.'record/receive/transition?recordType='.$v_recordtype;
                $sSpace='';
                if($iSum<>0){
                    $sSpace=', ';
                }
                $htmlString.='<a href=' . $url . '><font  size = 2px; color="black">'.$sSpace.'[Có '.
                    $v_transitionnew
                    .' h&#7891; s&#417; liên thông chờ nhận]</font></a>';
                $iSum++;
            }

			if($v_recordadditional<>0){
                if($sOwnerCode==$sOwnerCodeRoot){
                    $url=$v_url.'record/receive/additional?recordType='.$v_recordtype;
                }else{
                    $url=$v_url.'record/wreceive/additional?recordType='.$v_recordtype;
                }

				$sSpace='';
				if($iSum<>0){
					$sSpace=', ';
				}
				$htmlString.='<a href=' . $url . '><font  size = 2px; color="black">'.$sSpace.'[Có '.
								$v_recordadditional
								.' h&#7891; s&#417; ch&#7901; b&#7893; sung t&#7841;i &#273;&#417;n v&#7883;]</font></a>';
				$iSum++;
			}

			if($v_recordresult<>0){
                if($sOwnerCode==$sOwnerCodeRoot){
                    $url=$v_url.'record/receive/result?recordType='.$v_recordtype.'&reminder=CAP_PHEP';
                }else{
                    $url=$v_url.'record/wreceive/result?recordType='.$v_recordtype.'&reminder=CAP_PHEP';
                }

				$sSpace='';
				if($iSum<>0){
					$sSpace=', ';
				}
				$htmlString.='<a href=' . $url . '><font  size = 2px; color="black">'.$sSpace.'[Có '.
								$v_recordresult
								.' h&#7891; s&#417; ch&#7901; tr&#7843; k&#7871;t qu&#7843;]</font></a>';
				$iSum++;
			}

			if($v_recordrefuser<>0){
                if($sOwnerCode==$sOwnerCodeRoot){
                    $url=$v_url.'record/receive/result?recordType='.$v_recordtype.'&reminder=TU_CHOI';
                }else{
                    $url=$v_url.'record/wreceive/result?recordType='.$v_recordtype.'&reminder=TU_CHOI';
                }

				$sSpace='';
				if($iSum<>0){
					$sSpace=', ';
				}
				$htmlString.='<a href=' . $url . '><font  size = 2px; color="black">'.$sSpace.'[Có '.
								$v_recordrefuser
								.'h&#7891; s&#417; b&#225;o t&#7915; ch&#7889;i]</font></a>';
				$iSum++;
			}

			if($v_recordwaitbackongate<>0){
				$url=$v_url.'record/handle/result?recordType='.$v_recordtype;
				$sSpace='';
				if($iSum<>0){
					$sSpace=', ';
				}
				$htmlString.='<a href=' . $url . '><font  size = 2px; color="black">'.$sSpace.'[Có '.
								$v_recordwaitbackongate
								.'  h&#7891; s&#417; &#273;&#432;&#7907;c l&#227;nh &#273;&#7841;o ph&#234; duy&#7879;t]</font></a>';
				$iSum++;
			}

			if($v_recordhanlder<>0){
				$url=$v_url.'record/handle/index?recordType='.$v_recordtype;
				$str='';
				$open='</font><font  size = 2px; color="red"> (';
				$close=')</font><font  size = 2px; color="black">';
				//lanh dao tra lai
				if($v_recordleaderreturn<>0){					
					if($str<>''){
						$str.=', ';
					}
					$str.='l&#227;nh &#273;&#7841;o tr&#7843; l&#7901;i '.$v_recordleaderreturn.' h&#7891; s&#417;';
				}

				$sOver='';
				if($str<>''){
					$sOver=$open.'Trong đó '.$str.$close;
				}				
				$sSpace='';
				if($iSum<>0){
					$sSpace=', ';
				}
				$htmlString.='<a href=' . $url . '><font  size = 2px; color="black">'.$sSpace.'[Có '.
								$v_recordhanlder
								.' h&#7891; s&#417; ch&#7901; th&#7909; l&#253; ch&#237;nh'.$sOver.']</font></a>';
				$iSum++;
			}

			if($v_recordassignment<>0){
				$url=$v_url.'record/assign/index?recordType='.$v_recordtype;
				$sSpace='';
				if($iSum<>0){
					$sSpace=', ';
				}
				$htmlString.='<a href=' . $url . '><font  size = 2px; color="black">'.$sSpace.'[Có '.
								$v_recordassignment
								.' h&#7891; s&#417; ch&#7901; ph&#226;n c&#244;ng]</font></a>';
				$iSum++;
			}

			if($v_recordapproval<>0){
                if($sOwnerCode==$sOwnerCodeRoot){
                    $url=$v_url.'record/approve/index?recordType='.$v_recordtype;
                }else{
                    $url=$v_url.'record/wapprove/index?recordType='.$v_recordtype;
                }

				$sOver='';
				if($v_recordapprovalover<>0){
					$sOver='</font><font  size = 2px; color="red"> "qu&#225; h&#7841;n '.$v_recordapprovalover.' h&#7891; s&#417; "</font><font  size = 2px; color="black">';
				}
				$sSpace='';
				if($iSum<>0){
					$sSpace=', ';
				}
				$htmlString.='<a href=' . $url . '><font  size = 2px; color="black">'.$sSpace.'[Có '.
								$v_recordapproval
								.' h&#7891; s&#417; ch&#7901; ph&#234; duy&#7879;t'.$sOver.']</font></a>';
				$iSum++;
			}
			$htmlString.='</div></td>';
			//neu het cot thu nhat noi them 1 khoang trang
			if($i%2==0){
				$htmlString.='<td style="width:0.5%"></td>';
			}						
			if($i%2==1){
				$htmlString.='</tr><tr style="height:5px"><td></td></tr>';
			}
		}
		//neu so loai thu tuc le
		if($iCount%2==1){
			$htmlString.='<td width="50%" class="nhac_viec" valign="top" style="padding-top:4px;">&nbsp;</td></tr>';
		}
		$htmlString.='<tr><td style="padding-top:150px;">&nbsp;</td></tr></table>';
		$this->view->Rss=$htmlString;
	}
}
?>