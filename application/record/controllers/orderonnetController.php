﻿<?php

/**
 * Class record_orderonnetController
 */
class record_orderonnetController extends Zend_Controller_Action
{
    public function init()
    {

        //Load cau hinh thu muc trong file config.ini
        $tempDirApp = Zend_Registry::get('conDirApp');
        $this->_dirApp = $tempDirApp->toArray();
        $this->view->dirApp = $tempDirApp->toArray();
        //Cau hinh cho Zend_layout
        Zend_Layout::startMvc(array(
            'layoutPath' => $this->_dirApp['layout'],
            'layout' => 'index'
        ));
        //Load ca thanh phan cau vao trang layout (index.phtml)
        $response = $this->getResponse();
        //Lay cac hang so su dung trong JS public
        $objConfig = new Extra_Init();
        $this->view->UrlAjax = $objConfig->_setUrlAjax();
        //Load cau hinh thu muc trong file config.ini de lay ca hang so dung chung
        $tempConstPublic = Zend_Registry::get('ConstPublic');
        $this->_ConstPublic = $tempConstPublic->toArray();

        //Lay so dong tren man hinh danh sach
        $this->view->NumberRowOnPage = $this->_ConstPublic['NumberRowOnPage'];

        //Ky tu dac biet phan tach giua cac phan tu
        $this->view->delimitor = $this->_ConstPublic['delimitor'];

        //Lay duong dan thu muc goc (path directory root)
        $this->view->baseUrl = $this->_request->getBaseUrl() . "/public/";

        //Goi lop modRecord
        Zend_Loader::loadClass('record_modReceiveonnet');
        $this->view->JSPublicConst = $objConfig->_setJavaScriptPublicVariable();

        // Load tat ca cac file Js va Css
        $this->view->LoadAllFileJsCss = Extra_Util::_getAllFileJavaScriptCss('', 'js', 'record.js', ',', 'js');

        //. Extra_Util::_getAllFileJavaScriptCss('','js/LibSearch','actb_search.js,common_search.js',',','js');

        //Lay tra tri trong Cookie
        $sGetValueInCookie = Extra_Util::_getCookie("showHideMenu");

        //Neu chua ton tai thi khoi tao
        if ($sGetValueInCookie == "" || is_null($sGetValueInCookie) || !isset($sGetValueInCookie)) {
            Extra_Util::_createCookie("showHideMenu", 1);
            Extra_Util::_createCookie("ImageUrlPath", $this->_request->getBaseUrl() . "/public/images/close_left_menu.gif");
            //Mac dinh hien thi menu trai
            $this->view->hideDisplayMeneLeft = 1;// = 1 : hien thi menu
            //Hien thi anh dong menu trai
            $this->view->ShowHideimageUrlPath = $this->_request->getBaseUrl() . "/public/images/close_left_menu.gif";
        } else {//Da ton tai Cookie
            /*
            Lay gia tri trong Cookie, neu gia tri trong Cookie = 1 thi hien thi menu, truong hop = 0 thi an menu di
            */
            if ($sGetValueInCookie != 0) {
                $this->view->hideDisplayMeneLeft = 1;// = 1 : hien thi menu
            } else {
                $this->view->hideDisplayMeneLeft = "";// = "" : an menu
            }
            //Lay dia chi anh trong Cookie
            $this->view->ShowHideimageUrlPath = Extra_Util::_getCookie("ImageUrlPath");
        }


        // Ham lay thong tin nguoi dang nhap hien thi tai Lefmenu
        $this->view->InforStaff = Extra_Util::_InforStaff();

        //Dinh nghia current modul code
        $this->view->currentModulCode = "ORDER-ON-NET";
        $pcurrentModulCodeForLeft = $this->_request->getParam('htn_leftModule', "");
        //
        $psshowModalDialog = $this->_request->getParam('showModalDialog', "");
        $this->view->showModelDialog = $psshowModalDialog;
        if ($psshowModalDialog != 1) {
            //Hien thi file template
            $response->insert('header', $this->view->renderLayout('header.phtml', './application/views/scripts/'));    //Hien thi header
            $response->insert('left', $this->view->renderLayout('left.phtml', './application/views/scripts/'));    //Hien thi header
            $response->insert('footer', $this->view->renderLayout('footer.phtml', './application/views/scripts/'));     //Hien thi footer
        }
    }

    /**
     * Creater : Tientk
     * Date : 16/05/2011
     * Idea : Tao phuong thuc hien thi danh sach cac HS cho tiep nhan so bo
     */

    public function indexAction()
    {
        $this->view->currentModulCodeForLeft = 'CHUADUYET';
        //Hien thi left menu
        $this->_response->insert('left', $this->view->renderLayout('left.phtml', './application/views/scripts/'));
        $this->view->titleBody = "DANH SÁCH ĐĂNG KÝ CHỜ DUYỆT";
        $objconfig = new Extra_Init();
        $objrecordfun = new Extra_Ecs();
        $objxml = new Extra_Xml();
        $this->view->arrConst = $objconfig->_setProjectPublicConst();
        $objReceiveonnet = new record_modReceiveonnet();//tao doi tuong cua lop mode va su dung cac function cua mode qua obj do.
        $arrRecordType = $_SESSION['arr_all_record_type'];

        $arrNetRecordType = $_SESSION['arr_all_record_type'];


        //Lay id cua loai ho so hien tai
        $sRecordTypeId = $this->_request->getParam('recordType');
        $NetRecordTypeId = $this->_request->getParam('recordType');
        if ($sRecordTypeId == "")
            $sRecordTypeId = $arrRecordType[0]['PK_RECORDTYPE'];
        //Lay id cua nguoi dang nhap hien thoi
        $iCurrentStaffId = $_SESSION['staff_id'];
        //lay ma don vi su dung cua nguoi dang nhap hien thoi
        $sOwnerCode = $_SESSION['OWNER_CODE'];
        $sfullTextSearch = '';
        $pUrl = $_SERVER['REQUEST_URI'];
        //lay gia tri cua chuoi gia tri tim kiem
        $sfullTextSearch = trim($this->_request->getParam('txtfullTextSearch', ''));
        //lay gia tri cua trang hien thoi
        $iPage = $this->_request->getParam('hdn_current_page', 0);
        if ($iPage <= 1) {
            $iPage = 1;
        }
        //lay so ban ghi tren mot trang
        $iNumberRecordPerPage = $this->_request->getParam('cbo_nuber_record_page', 0);
        if ($iNumberRecordPerPage == 0)
            $iNumberRecordPerPage = 15;
        //Neu ton tai gia tri tim kiem tron session thi lay trong session
        if (isset($_SESSION['seArrParameter'])) {
            $Parameter = $_SESSION['seArrParameter'];
            $sRecordTypeId = $Parameter['sRecordTypeId'];
            $sfullTextSearch = $Parameter['sfullTextSearch'];
            $iPage = $Parameter['iPage'];
            $iNumberRecordPerPage = $Parameter['iNumberRecordPerPage'];
            unset($_SESSION['seArrParameter']);
        }
        $sListStatus = 'CHO_TIEP_NHAN_SO_BO';
        $arrinfoRecordType = $objrecordfun->getinforRecordType($sRecordTypeId, $arrRecordType);
        $arrinfoNetRecordType = $objrecordfun->getinforNetRecordType($NetRecordTypeId, $arrNetRecordType);
        $sRecordTypeCode = $arrinfoRecordType['C_CODE'];
        $this->view->sRecordTypeCode = $sRecordTypeCode;
        $this->view->sRecordTypeId = $sRecordTypeId;
        //lay duong dan file xml mo ta hien thi man hinh danh sach
        $sxmlFileName = $objconfig->_setXmlFileUrlPath(1) . 'record/' . $sRecordTypeCode . '/danh_sach_dang_ky_qua_mang.xml';
        if (!file_exists($sxmlFileName)) {
            $sxmlFileName = $objconfig->_setXmlFileUrlPath(1) . 'record/other/danh_sach_dang_ky_qua_mang.xml';
        }
        if ($sRecordTypeCode == '')
            $sRecordTypeCode = '00';
        /*if(!is_file($sxmlFileName)){
            $sxmlFileName = $objconfig->_setXmlFileUrlPath(1).'record/other/danh_sach_hs_tiep_nhan_qua_mang.xml';
        }*/
        //Day gia tri tim kiem ra view
        $sfullTextSearch = trim($this->_request->getParam('txtfullTextSearch', ''));
        $arrInputfilter = array('fullTextSearch' => $sfullTextSearch, 'pUrl' => '../orderonnet/index', 'RecordTypeId' => $sRecordTypeId);
        //var_dump($arrInputfilter);exit;
        //Hien thi tieu chi tim kiem gom: loai ho so va chuoi ky tu tim kiem
        $this->view->filter_form = $objrecordfun->genEcsFilterFrom($iCurrentStaffId, 'TIEP_NHAN', $arrRecordType, $arrInputfilter);
        // Goi ham search de hien thi ra Complete Textbox
        $arrRecord = $objReceiveonnet->eCSNetOrderGetAll($sRecordTypeId, $sOwnerCode, $sfullTextSearch, $sListStatus, $iPage, $iNumberRecordPerPage);
        // var_dump($arrRecord);exit;
        //Hien thi thong tin ho so
        //echo $sxmlFileName;
        $this->view->genlist = $objxml->_xmlGenerateList($sxmlFileName, 'col', $arrRecord, "C_XML_DATA", "PK_NET_ORDER", $sfullTextSearch, false, false, '../viewrecord/');
        $iNumberRecord = $arrRecord[0]['C_TOTAL_RECORD'];
        $this->view->iNumberRecord = $iNumberRecord;
        $sdocpertotal = "Danh sách này không có hồ sơ nào";
        //Hien thi thong tin man hinh danh sach nay co bao nhieu ban ghi va hien thi Radio "Chon tat ca"; "Bo chon tat ca"
        $this->view->SelectDeselectAll = Extra_Util::_selectDeselectAll(sizeof($arrRecord), $iNumberRecord);
        if (count($arrRecord) > 0) {
            $this->view->sdocpertotal = "Danh sách có: " . sizeof($arrRecord) . '' . $iNumberRecord . " hồ sơ";
            //Sinh xau HTML mo ta so trang (Trang 1; Trang 2;...)
            $this->view->generateStringNumberPage = Extra_Util::_generateStringNumberPage($iNumberRecord, $iPage, $iNumberRecordPerPage, $pUrl);
            //Sinh chuoi HTML mo ta tong so trang (Trang 1; Trang 2;...) va quy dinh so record/page
            $this->view->generateHtmlSelectBoxPage = Extra_Util::_generateChangeRecordNumberPage($iNumberRecordPerPage, $this->view->getStatusLeftMenu);

        }
    }

    /**
     *
     * Idea Phuong thuc hien thi danh sach cac ho so da tiep nhan so bo
     * Nguoi tao: Tientk
     * Ngay tao: 20/05/2011
     * Enter desc/iption here ...
     */
    public function approvedAction()
    {
        $this->view->currentModulCodeForLeft = 'DADUYET';
        //Hien thi left menu
        $this->_response->insert('left', $this->view->renderLayout('left.phtml', './application/views/scripts/'));
        $this->view->titleBody = "DANH SÁCH ĐĂNG KÝ ĐÃ DUYỆT";
        $objconfig = new Extra_Init();
        $objrecordfun = new Extra_Ecs();
        $objxml = new Extra_Xml();
        $this->view->arrConst = $objconfig->_setProjectPublicConst();
        $objReceiveonnet = new record_modReceiveonnet();//tao doi tuong cua lop mode va su dung cac function cua mode qua obj do.
        $arrRecordType = $_SESSION['arr_all_record_type'];

        $arrNetRecordType = $_SESSION['arr_all_record_type'];


        //Lay id cua loai ho so hien tai
        $sRecordTypeId = $this->_request->getParam('recordType');
        $NetRecordTypeId = $this->_request->getParam('recordType');
        if ($sRecordTypeId == "")
            $sRecordTypeId = $arrRecordType[0]['PK_RECORDTYPE'];
        //Lay id cua nguoi dang nhap hien thoi
        $iCurrentStaffId = $_SESSION['staff_id'];
        //lay ma don vi su dung cua nguoi dang nhap hien thoi
        $sOwnerCode = $_SESSION['OWNER_CODE'];
        $sfullTextSearch = '';
        $pUrl = $_SERVER['REQUEST_URI'];
        //lay gia tri cua chuoi gia tri tim kiem
        $sfullTextSearch = trim($this->_request->getParam('txtfullTextSearch', ''));
        //lay gia tri cua trang hien thoi
        $iPage = $this->_request->getParam('hdn_current_page', 0);
        if ($iPage <= 1) {
            $iPage = 1;
        }
        //lay so ban ghi tren mot trang
        $iNumberRecordPerPage = $this->_request->getParam('cbo_nuber_record_page', 0);
        if ($iNumberRecordPerPage == 0)
            $iNumberRecordPerPage = 15;
        //Neu ton tai gia tri tim kiem tron session thi lay trong session
        if (isset($_SESSION['seArrParameter'])) {
            $Parameter = $_SESSION['seArrParameter'];
            $sRecordTypeId = $Parameter['sRecordTypeId'];
            $sfullTextSearch = $Parameter['sfullTextSearch'];
            $iPage = $Parameter['iPage'];
            $iNumberRecordPerPage = $Parameter['iNumberRecordPerPage'];
            unset($_SESSION['seArrParameter']);
        }
        $sListStatus = 'DUYET,TU_CHOI';
        $arrinfoRecordType = $objrecordfun->getinforRecordType($sRecordTypeId, $arrRecordType);
        $arrinfoNetRecordType = $objrecordfun->getinforNetRecordType($NetRecordTypeId, $arrNetRecordType);
        $sRecordTypeCode = $arrinfoRecordType['C_CODE'];
        $this->view->sRecordTypeCode = $sRecordTypeCode;
        $this->view->sRecordTypeId = $sRecordTypeId;
        //lay duong dan file xml mo ta hien thi man hinh danh sach
        $sxmlFileName = $objconfig->_setXmlFileUrlPath(1) . 'record/' . $sRecordTypeCode . '/danh_sach_dang_ky_qua_mang.xml';
        if (!file_exists($sxmlFileName)) {
            $sxmlFileName = $objconfig->_setXmlFileUrlPath(1) . 'record/other/danh_sach_dang_ky_qua_mang.xml';
        }
        if ($sRecordTypeCode == '')
            $sRecordTypeCode = '00';
        /*if(!is_file($sxmlFileName)){
            $sxmlFileName = $objconfig->_setXmlFileUrlPath(1).'record/other/danh_sach_hs_tiep_nhan_qua_mang.xml';
        }*/
        //Day gia tri tim kiem ra view
        $sfullTextSearch = trim($this->_request->getParam('txtfullTextSearch', ''));
        $arrInputfilter = array('fullTextSearch' => $sfullTextSearch, 'pUrl' => '../orderonnet/index', 'RecordTypeId' => $sRecordTypeId);
        //var_dump($arrInputfilter);exit;
        //Hien thi tieu chi tim kiem gom: loai ho so va chuoi ky tu tim kiem
        $this->view->filter_form = $objrecordfun->genEcsFilterFrom($iCurrentStaffId, 'TIEP_NHAN', $arrRecordType, $arrInputfilter);
        // Goi ham search de hien thi ra Complete Textbox
        $arrRecord = $objReceiveonnet->eCSNetOrderGetAll($sRecordTypeId, $sOwnerCode, $sfullTextSearch, $sListStatus, $iPage, $iNumberRecordPerPage);
        // var_dump($arrRecord);exit;
        //Hien thi thong tin ho so
        //echo $sxmlFileName;
        $this->view->genlist = $objxml->_xmlGenerateList($sxmlFileName, 'col', $arrRecord, "C_XML_DATA", "PK_NET_ORDER", $sfullTextSearch, false, false, '../viewrecord/');
        $iNumberRecord = $arrRecord[0]['C_TOTAL_RECORD'];
        $this->view->iNumberRecord = $iNumberRecord;
        $sdocpertotal = "Danh sách này không có hồ sơ nào";
        //Hien thi thong tin man hinh danh sach nay co bao nhieu ban ghi va hien thi Radio "Chon tat ca"; "Bo chon tat ca"
        $this->view->SelectDeselectAll = Extra_Util::_selectDeselectAll(sizeof($arrRecord), $iNumberRecord);
        if (count($arrRecord) > 0) {
            $this->view->sdocpertotal = "Danh sách có: " . sizeof($arrRecord) . '' . $iNumberRecord . " hồ sơ";
            //Sinh xau HTML mo ta so trang (Trang 1; Trang 2;...)
            $this->view->generateStringNumberPage = Extra_Util::_generateStringNumberPage($iNumberRecord, $iPage, $iNumberRecordPerPage, $pUrl);
            //Sinh chuoi HTML mo ta tong so trang (Trang 1; Trang 2;...) va quy dinh so record/page
            $this->view->generateHtmlSelectBoxPage = Extra_Util::_generateChangeRecordNumberPage($iNumberRecordPerPage, $this->view->getStatusLeftMenu);

        }
    }

    /**
     *
     */
    public function viewrecordAction()
    {
        $this->view->titleBody = "CHI TIẾT ĐĂNG KÝ GIAO DỊCH";
        $objconfig = new Extra_Init();
        $objrecordfun = new Extra_Ecs();
        $objxml = new Extra_Xml();
        $ojbEfyLib = new Extra_Util();
        $objReceiveonnet = new record_modReceiveonnet();

        //Lay tham so cau hinh
        $efyLibUrlPath = $objconfig->_setLibUrlPath();
        $this->view->arrConst = $objconfig->_setProjectPublicConst();
        //Lay thong tin cua loai ho so
        $arrRecordType = $objrecordfun->eCSRecordTypeGetAllByStaff($_SESSION['staff_id'], $_SESSION['OWNER_CODE']);
        //Lay id ho so tu trang index
        $sRecordTypeId = $this->_request->getParam('recordType');
        //tren hidden
        if ($sRecordTypeId == '')
            $sRecordTypeId = $this->_request->getParam('hdn_record_type_id');
        //tren Url
        if ($sRecordTypeId == '')
            $sRecordTypeId = $this->_request->getParam('r');
        $this->view->RecordTypeId = $sRecordTypeId;
        $arrinfoRecordType = $objrecordfun->getinforRecordType($sRecordTypeId, $arrRecordType);
        $sRecordTypeCode = $arrinfoRecordType['C_CODE'];
        If ($sRecordTypeCode == '')
            $sRecordTypeCode = '00';
        $sxmlFileName = $objconfig->_setXmlFileUrlPath(1) . 'record/' . $sRecordTypeCode . '/dang_ky_giao_dich.xml';//Thay doi file xml
        if (!file_exists($sxmlFileName)) {
            $sxmlFileName = $objconfig->_setXmlFileUrlPath(1) . 'record/other/dang_ky_giao_dich.xml';
        }

        //var_dump($sRecordTypeCode);exit;
        $this->view->RecodeTypeName = $arrinfoRecordType['C_NAME'];
        //Lay thong tin cua mot ho so
        $srecordId = $this->_request->getParam('hdn_object_id', '');//echo '$srecordId:'.$srecordId ;exit;
        $this->view->srecordId = $srecordId;

        $arrSingleRecord = $objReceiveonnet->eCSNetOrderGetSingle($srecordId);
        //var_dump($arrSingleRecord);exit;
        $this->view->arrSingleRecord = $arrSingleRecord;
        $this->view->RecodeCode = $arrSingleRecord[0]['C_CODE'];

        //Bien xac dinh sau khi update thi quay lai man hinh danh sach nao? Tiep nhan so bo hay Tiep nhan chinh thuc?...
        $sBackOption = trim($this->_request->getParam('hdn_back_option', ''));
        $this->view->sBackOption = $sBackOption;

        //Luu cac dieu kien tim kiem len session
        if (!isset($_SESSION['seArrParameter'])) {
            $sfullTextSearch = trim($this->_request->getParam('txtfullTextSearch', ''));
            $sRecordTypeId = $this->_request->getParam('recordType');
            $iPage = $this->_request->getParam('hdn_current_page', 0);
            $iNumberRecordPerPage = $this->_request->getParam('cbo_nuber_record_page', 0);
            if ($iPage <= 1) {
                $iPage = 1;
            }
            if ($iNumberRecordPerPage == 0)
                $iNumberRecordPerPage = 15;
            $arrParaSet = array("iPage" => $iPage, "iNumberRecordPerPage" => $iNumberRecordPerPage, "sfullTextSearch" => $sfullTextSearch, "sRecordTypeId" => $sRecordTypeId);
            $_SESSION['seArrParameter'] = $arrParaSet;
        }
        //lay gia tri ben view
        $option = $this->_request->getParam('optionduyet');
        $sMesage = $this->_request->getParam('NOIDUNG');
        $dDate = $this->_request->getParam('C_RECEIVED_DATE');
        if ($this->getRequest()->isPost() && $option) {
            $sStatus = '';
            if ($option == '1') {
                $sStatus = 'DUYET';
            } elseif ($option == 2) {
                $sStatus = 'TU_CHOI';
            }
            $arrParameter = array(
                'PK_NET_RECORD' => $srecordId,
                'C_RECEIVING_DATE' => $ojbEfyLib->_ddmmyyyyToYYyymmdd($dDate),
                'C_STATUS' => $sStatus,
                'C_MESSAGE' => $sMesage
            );

            $arrResult = $objReceiveonnet->eCSNetOrderApproveUpdate($arrParameter);
            /*//var_dump($arrResult);exit;
            $this->_redirect('../orderonnet/index');*/
        }

        if ($this->_request->getParam('hdh_option') == "QUAYLAI")
            $this->_redirect('record/orderonnet/index');
        $this->view->generateFormHtml = $objxml->_xmlGenerateFormfield($sxmlFileName, 'update_object/table_struct_of_update_form/update_row_list/update_row', 'update_object/update_formfield_list', 'C_XML_DATA', $arrSingleRecord, true, true);
    }

    /**
     *
     */
    Public function sendmailAction()
    {
        $this->view->titleBody = "GỬI EMAIL CHO CÔNG DÂN";
        $objconfig = new Extra_Init();
        $objrecordfun = new Extra_Ecs();
        $objReceiveonnet = new record_modReceiveonnet();
        $ojbEfyLib = new Extra_Util();

        $mailConfig = $objconfig->configMail();
        //Lay tham so cau hinh
        $this->view->arrConst = $objconfig->_setProjectPublicConst();
        //Lay thong tin cua loai ho so
        $arrRecordType = $objrecordfun->eCSRecordTypeGetAllByStaff($_SESSION['staff_id'], $_SESSION['OWNER_CODE']);
        //Lay id ho so tu trang index
        $sRecordTypeId = $this->_request->getParam('recordType');
        //tren hidden
        if ($sRecordTypeId == '')
            $sRecordTypeId = $this->_request->getParam('hdn_record_type_id');
        //tren Url
        if ($sRecordTypeId == '')
            $sRecordTypeId = $this->_request->getParam('r');
        $this->view->RecordTypeId = $sRecordTypeId;

        $arrinfoRecordType = $objrecordfun->getinforRecordType($sRecordTypeId, $arrRecordType);
        $sRecordTypeCode = $arrinfoRecordType['C_CODE'];
        If ($sRecordTypeCode == '')
            $sRecordTypeCode == '00';
        $this->view->RecodeCode = $objrecordfun->generateRecordCode($sRecordTypeCode);
        $this->view->RecodeTypeName = $arrinfoRecordType['C_NAME'];

        $srecordId = $this->_request->getParam('hdn_object_id', '');
        $this->view->srecordId = $srecordId;
        $arrSingleRecord = $objReceiveonnet->eCSNetReceiveRecordGetSingle($srecordId);

        $arrSingleUser = $objReceiveonnet->eCSNetUserGetSingle($arrSingleRecord[0]['FK_NET_ID']);
        $this->view->fullname = $arrSingleUser['C_FIRSTNAME'] . ' ' . $arrSingleUser['C_LASTNAME'];
        $this->view->email = $arrSingleUser['C_EMAIL'];
        $this->view->unitemail = $mailConfig['mail_name'];
        if ($this->_request->getParam('hdh_option') == 'GUI_MAIL') {
            $user_mail = $this->_request->getParam('useremail');
            $full_name = $this->_request->getParam('fullname');
            $v_tit = $this->_request->getParam('mailtitle');
            $v_message_text = $this->_request->getParam('emailcontent');
            $v_message_text = $objrecordfun->_isbreakcontent($v_message_text);

            if ($ojbEfyLib->smtpmailer($user_mail, $full_name, $mailConfig['mail_name'], $mailConfig['mail_password'], Extra_User::getInstance()->getIdentity()->C_UNIT_NAME, $v_tit, $v_message_text)) {
                $arrParameter = array(
                    'PK_NET_RECORD' => '',
                    'C_PRELIMINARY_DATE' => '',
                    'C_ORIGINAL_APPLICATION_DATE' => '',
                    'C_RECEIVING_DATE' => '',
                    'C_STATUS' => '',
                    'C_MESSAGE' => $v_message_text,
                    'FK_RECEIVER' => '',
                    'C_RECEIVER_POSITION_NAME' => '',
                );
                $arrResult = $objReceiveonnet->eCSNetReceiveRecordUpdate($arrParameter);
                echo "<script type='text/javascript'> alert('Gửi mail thành công')</script>";
            } else {
                echo "<script type='text/javascript'> alert('Gửi mail thất bại')</script>";
            }
        }
    }
}

?>