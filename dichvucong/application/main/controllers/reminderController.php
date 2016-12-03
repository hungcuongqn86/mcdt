<?php

/**
 * Nguoi tao: Truongdv
 * Ngay tao: 12/06/2013
 * Y nghia: Module nhac viec
 */
class main_reminderController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->baseUrl = $this->_request->getBaseUrl() . "/public/";
        if (!$this->_request->isXmlHttpRequest()) {
//            
            //Cau hinh cho Zend_layout
            Zend_Layout::startMvc(array(
                'layoutPath' => G_Global::getInstance()->layoutPath,
                'layout' => 'index'
            ));
            //Load cac thanh phan cau vao trang layout (index.phtml)
            $response = $this->getResponse();

            //Lay cac hang so su dung trong JS public
            $objConfig = new G_Global();
            $objConst = new G_Const();
            $this->view->JSPublicConst = $objConfig->_setJavaScriptPublicVariable();
//            
            $arrConst = $objConst->_setProjectPublicConst();
            $this->view->arrConst = $arrConst;
            $params = $this->_request->getParams();
            $this->view->currentResource = $params['module'] . '_' . $params['controller'] . '_' . $params['action'];
            $leftmenu = $this->getRequest()->getCookie('lm', '1');
            setcookie('lm', $leftmenu, null, '/', '');
            $this->view->leftmenu = $leftmenu;
            $response->insert('menu', $this->view->renderLayout('menu.phtml', './application/layout/scripts/'));
            $response->insert('footer', $this->view->renderLayout('footer.phtml', './application/layout/scripts/'));
        }
    }

    /*
     * @author: 
     * @see: 20/06/2013
     * @todo: Hien thi danh sach nhac viec
     * Enter description here ...
     */
    public function indexAction()
    {
        $dbConnect = new G_Db();
        $objGen = new G_Gen();
        $auth = Zend_Auth::getInstance()->getIdentity();
        $sStaffName = $auth->sPositionCode . ' - ' . $auth->sName;
        $sUnitId = $auth->FkUnit;
        $sUserID = $auth->PkStaff;
        $sOwnerCode = (isset($auth->sDistrictWardProcess) && $auth->sDistrictWardProcess !='' ? $auth->sDistrictWardProcess : $auth->sOwnerCode);

        $sFromDate = $this->_request->getParam('fromDate');
        $sToDate = $this->_request->getParam('toDate');


        // $arrRss = $dbConnect->_querySql(array('sUserID' => $sUserID, 'sUnitId' => $sUnitId, 'sOwnerCode' => $sOwnerCode), 'sp_KntcGetAllReminder', false, false);
        $htmlRss = '<div id="form-reminder-left" style="float: left;"><div id="reminder-content"><div class="label_reminder">XỬ LÝ ĐƠN</div>';

        $notify = $this->getNotify();
        for ($i = 0; $i < sizeof($notify); $i++) {
            $htmlRss .= '<div class="item_reminder"><a href="' . $notify[$i]['href'] . '" onclick="' . $notify[$i]['click'] . '" >' . $notify[$i]['text'] . '</a></div>';
        }
        $htmlRss .= '</div></div>';
        $htmlRss .= '<div id="form-reminder-right" style="float: right;"><div id="reminder-content-right"><div class="label_reminder">NHỮNG VỤ VIỆC ĐÃ RÚT ĐƠN</div>';
        $htmlRss .= '<div style="padding-bottom:2%;padding-top:2%">';
        $htmlRss .= '<label class="normal_label" for="txtFromDate" style="width:20%; padding-left:2%; font-weight:bold">Từ ngày </label>';
        $htmlRss .= '<input id="txtFromDate" class="normal_textbox" type="text" isdate="true" date="isdate" style="width:15%" onkeyup="DateOnkeyup(this,event)" onchange="reminderCancel()">';
        $htmlRss .= '<label class="normal_label" for="txtToDate" style="width:20%; padding-left:10%; font-weight:bold">Đến ngày </label>';
        $htmlRss .= '<input id="txtToDate" class="normal_textbox" type="text" isdate="true" date="isdate" style="width:15%" onkeyup="DateOnkeyup(this,event)" onchange="reminderCancel()"> ';
        $htmlRss .= '</div>';
        $arrCancel = $dbConnect->_querySql(array('sFromDate' => $sFromDate, 'sToDate' => $sToDate, 'sOwnerCode' => $sOwnerCode), 'sp_KntcGetAllPetitionCancel', true, false);

        if ($arrCancel) {
            $htmlRss .= '<div id="reminder_cancel">';
            $htmlRss .= '<table id="table-data-cancel" class="list-table-data" cellspacing="0" cellpadding="0" border="0" align="center">';
            $htmlRss .= '<col width="7%"><col width="35%"><col width="25%"><col width="25%"><col width="14%">';
            $htmlRss .= '<tr class="header">';
            $htmlRss .= '<td>STT</td>';
            $htmlRss .= '<td>Tổ chức/ cá nhân</td>';
            $htmlRss .= '<td>Ngày nhận đơn</td>';
            $htmlRss .= '<td>Ngày rút đơn</td>';
            $htmlRss .= '<td>#</td>';
            $htmlRss .= '</tr>';
            for ($j = 0; $j < sizeof($arrCancel); $j++) {
                $htmlRss .= '<tr>';
                $htmlRss .= '<td align="center">' . ($j + 1) . '<input type="hidden" value="' . $arrCancel[$j]['PkRecord'] . '" name="chk_item_id" onclick="{selectrow(this);}">' . '</td>';
                $htmlRss .= '<td align="left">' . $arrCancel[$j]['sRegistorName'] . '</td>';
                $htmlRss .= '<td align="center">' . G_Convert::_yyyymmddToDDmmyyyy($arrCancel[$j]['dReceivedDate']) . '</td>';
                $htmlRss .= '<td align="center">' . G_Convert::_yyyymmddToDDmmyyyy($arrCancel[$j]['dSignApproveDate']) . '</td>';
                $htmlRss .= '<td align="center"><label class="normal_label" style="cursor: pointer;color:#0000CC;padding-right:2%" onclick="viewtempo_cancel(this)">Xem</label></td>';
                $htmlRss .= '</tr>';
            }
            $htmlRss .= '</div>';
            $htmlRss .= '</table>';
        }
        $htmlRss .= '</div></div>';
        $this->view->sStaffName = $sStaffName;
        $this->view->htmlRss = $htmlRss;
    }

    public function getnotifyAction()
    {
        $type = $this->_request->getParam('t', 0);
        if ($type == 1) {
            $notify = $this->getNotify();
            $result = array('data' => $notify, 'type' => 1);
        } else {
            $notify = $this->notifyGetAll(0);
            $count = ($notify ? $notify[0]['C_COUNT'] : 0);
            $result = array('data' => $count, 'type' => 0);
        }
        echo json_encode($result);
        die;
    }

    private function notifyGetAll($type)
    {
        $dbConnect = new G_Db();
        $auth = Zend_Auth::getInstance()->getIdentity();
        
        if (isset($auth->sDistrictWardProcess) && $auth->sDistrictWardProcess !='') {
            $typeOwner = 'PX';
            $sOwnerCode = $auth->sDistrictWardProcess;
        } else {
            $typeOwner = 'QH';
            $sOwnerCode = $auth->sOwnerCode;
        }
        
        $arrPermission = $auth->PERMISSIONS;
        $arrAction = array();
        $arrGet = array();
        for ($i = 0; $i < sizeof($arrPermission); $i++) {
            array_push($arrAction, $arrPermission[$i]['sPackageCode'] . ':' . $arrPermission[$i]['sModuleCode']);
            //. ':' . $arrPermission[$i]['sEventCode']
        }
        // echo '<pre>';
        // Quan huyen
        if (in_array('receive:record', $arrAction)) array_push($arrGet, 'MOI_TIEP_NHAN_10');
        if (in_array('handle:process', $arrAction)) array_push($arrGet, 'THU_LY_10');
        if (in_array('handle:asign', $arrAction)) array_push($arrGet, 'CHO_PHAN_CONG_10');
        if (in_array('approve:approve1', $arrAction)) array_push($arrGet, 'TRINH_KY_20');
        if (in_array('approve:approve2', $arrAction)) array_push($arrGet, 'TRINH_KY_40');
        if (in_array('classify:pending1', $arrAction)) array_push($arrGet, 'TRINH_DUYET_THAM_MUU_30');
        if (in_array('classify:pending2', $arrAction)) array_push($arrGet, 'TRINH_DUYET_THAM_MUU_40');
        // Phuong xa
        if (in_array('wreceive:wrso', $arrAction)) {
            array_push($arrGet, 'W_CHUYEN_PHUONG_XA_10');
            array_push($arrGet, 'W_CHO_PHAN_LOAI_10');
        }
        if (in_array('wreceive:wrecord', $arrAction)) array_push($arrGet, 'W_MOI_TIEP_NHAN_10');

        if (in_array('whandle:process', $arrAction)) array_push($arrGet, 'W_THU_LY_10');
        if (in_array('wapprove:wapprove', $arrAction)) array_push($arrGet, 'W_TRINH_KY_40');
        if (in_array('wapprove:wpending', $arrAction)) array_push($arrGet, 'W_TRINH_DUYET_THAM_MUU_40');
        

        $arrInput = array('sUserID' => $auth->PkStaff,
            'sUnitId' => $auth->FkUnit,
            'sOwnerCode' => $sOwnerCode,
            'typeOwner' => $typeOwner,            
            'sListTask' => implode(',', $arrGet),
            'optionReturn' => $type
        );
        $arrRss = $dbConnect->_querySql($arrInput, 'sp_SysReminderGetAll', 1, 0);
        // echo $arrRss; die;
        return $arrRss;
    }

    private function getNotify()
    {
        $notify = array();
        $arrRss = $this->notifyGetAll(1);
        if ($arrRss)
            $notify = $this->parserInfo($arrRss);
        return $notify;
    }

    private function parserInfo($arrRss)
    {
        $arrOutput = array();
        foreach ($arrRss as $key => $value) {
            $iCount = $value['C_COUNT'];
            if ((int)$iCount > 0) {
                $text = '';
                $params = array();
                switch ($value['C_TASK']) {
                    case 'MOI_TIEP_NHAN_10':
                        $urlRe = $this->view->baseUrl . '../receive/record';
                        $resource = 'receive_record_index';
                        $text = 'Có ' . $iCount . ' đơn thư mới tiếp nhận';
                        break;
                    case 'TRINH_DUYET_THAM_MUU_30':
                        $urlRe = $this->view->baseUrl . '../classify/pending1';
                        $resource = 'classify_pending1_index';
                        $text = 'Có ' . $iCount . ' đơn thư chờ duyệt phân loại';
                        break;
                    case 'TRINH_DUYET_THAM_MUU_40':
                        $urlRe = $this->view->baseUrl . '../classify/pending2';
                        $resource = 'classify_pending2_index';
                        $text = 'Có ' . $iCount . ' đơn thư chờ duyệt phân loại';
                        break;
                    case 'TQ_CHO_CHUYEN_DV_10':
                        $urlRe = $this->view->baseUrl . '../receive/classified?type=TTQ';
                        $resource = 'receive_classified_index';
                        $text = 'Có ' . $iCount . ' đơn thư thuộc thẩm quyền chờ chuyển đơn vị';
                        break;
                    case 'KTTQ_CHO_CHUYEN_DV_10':
                        $urlRe = $this->view->baseUrl . '../receive/classified?type=KTTQ';
                        $resource = 'receive_classified_index';
                        $text = 'Có ' . $iCount . ' đơn thư không thuộc thẩm quyền chờ chuyển đơn vị';
                        break;
                    case 'THU_LY_10':
                        $urlRe = $this->view->baseUrl . '../handle/process';
                        $resource = 'handle_process_index';
                        $text = 'Có ' . $iCount . ' đơn thư chờ thụ lý';
                        break;
                    case 'TRINH_KY_20':
                        $urlRe = $this->view->baseUrl . '../approve/approve1';
                        $resource = 'approve_approve1_index';
                        $text = 'Có ' . $iCount . ' đơn thư chờ duyệt thụ lý';
                        break;
                    case 'TRINH_KY_40':
                        $urlRe = $this->view->baseUrl . '../approve/approve2';
                        $resource = 'approve_approve2_index';
                        $text = 'Có ' . $iCount . ' đơn thư chờ duyệt thụ lý';
                        break;
                    case 'CHO_PHAN_CONG_10':
                        $urlRe = $this->view->baseUrl . '../handle/asign';
                        $resource = 'handle_asign_index';
                        $text = 'Có ' . $iCount . ' đơn thư chờ phân công';
                        break;
                    // Phuong xa
                    case 'W_MOI_TIEP_NHAN_10':
                        $urlRe = $this->view->baseUrl . '../wreceive/wrecord';
                        $resource = 'wreceive_wrecord_index';
                        $text = 'Có ' . $iCount . ' đơn thư mới tiếp nhận';
                        break;
                    case 'W_THU_LY_10':
                        $urlRe = $this->view->baseUrl . '../whandle/process';
                        $resource = 'whandle_process_index';
                        $text = 'Có ' . $iCount . ' đơn thư chờ thụ lý';
                        break;
                    case 'W_TRINH_DUYET_THAM_MUU_40':
                        $urlRe = $this->view->baseUrl . '../wapprove/wpending';
                        $resource = 'wapprove_wpending_index';
                        $text = 'Có ' . $iCount . ' đơn thư chờ duyệt phân loại';
                        break;
                    case 'W_TRINH_KY_40':
                        $urlRe = $this->view->baseUrl . '../wapprove/wapprove';
                        $resource = 'wapprove_wapprove_index';
                        $text = 'Có ' . $iCount . ' đơn thư chờ duyệt thụ lý';
                        break;
                    case 'W_CHUYEN_PHUONG_XA_10':
                        $urlRe = $this->view->baseUrl . '../wreceive/wrso';
                        $resource = 'wreceive_wrso_index';
                        $text = 'Có ' . $iCount . ' đơn thư quận/huyện gửi chờ nhận';
                        break;
                    case 'W_CHO_PHAN_LOAI_10':
                        $urlRe = $this->view->baseUrl . '../wreceive/wrso';
                        $resource = 'wreceive_wrso_index';
                        $params = array('status' => 'DA_NHAN');
                        $text = 'Có ' . $iCount . ' đơn thư quận/huyện gửi chờ phân loại';
                        break;

                }
                if ($text) {
                    array_push($arrOutput, array(
                        'href' => $urlRe,
                        'resource' => $resource,
                        'text' => $text,
                        'params' => base64_encode(json_encode($params)),
                        'number' => $iCount
                    ));
                }
            }
        }
        return $arrOutput;
    }
}