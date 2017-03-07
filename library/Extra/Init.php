<?php

/**
 * Class Extra_Init
 */
class Extra_Init
{
    /**
     * @return string
     */
    public function _setWebSitePath()
    {
        $registry = Zend_Registry::getInstance();
        $conDirApp = $registry->get('conDirApp');
        return $conDirApp->root;
    }

    /**
     * @return string
     */
    public function _getOwnerCode()
    {
        $registry = Zend_Registry::getInstance();
        $constPublic = $registry->get('ConstPublic');
        return $constPublic->ownercode;
    }

    /**
     * @return string
     */
    public function _setLibUrlPath()
    {
        return self::_setWebSitePath() . "public/";
    }

    /**
     * @return string
     */
    public function _setSeachrecordresultUrlPath()
    {
        return "/seachrecordresult";
    }

    /**
     * @return string
     */
    public function _setImageUrlPath()
    {
        return self::_setLibUrlPath() . "images/";
    }

    /**
     * @return string
     */
    public function _setAttachFileUrlPath()
    {
        return self::_setLibUrlPath() . "attach-file/";
    }

    /**
     * @return string
     */
    public function _setDvcAttachFileUrlPath()
    {
        return self::_setWebSitePath() . "dichvucong/io/attach-file/";
    }

    /**
     * Idea: Lay duong dan luu file XML
     *
     * @param $piLevel : Cap thu muc chua file XML
     */
    public function _setXmlFileUrlPath($piLevel)
    {
        switch ($piLevel) {
            //Duong dan toi thu muc chua cac file XML tinh tu thu muc goc
            case 0;
                return "xml/";
                break;
            //Duong dan toi thu muc chua file XML tu thu muc hien tai. Thu muc hien tai la thu muc cap 1
            case 1;
                return "./xml/";
                break;
            //Duong dan toi thu muc chua file XML tu thu muc hien tai. Thu muc hien tai la thu muc cap 2
            case 2;
                return "../../xml/";
                break;
            //Duong dan toi thu muc chua file XML tu thu muc hien tai. Thu muc hien tai la thu muc cap 3
            case 3;
                return "../../../xml/";
                break;
            default:
                return "";
                break;
        }
    }

    /**
     * @return string
     */
    public function _setJavaScriptPublicVariable()
    {
        $arrConst = $this->_setProjectPublicConst();
        $psHtml = "<script>\n";
        $psHtml = $psHtml . "_LIST_DELIMITOR='" . $arrConst['_CONST_LIST_DELIMITOR'] . "';\n";
        $psHtml = $psHtml . "_SUB_LIST_DELIMITOR='" . $arrConst['_CONST_SUB_LIST_DELIMITOR'] . "';\n";
        $psHtml = $psHtml . "_DECIMAL_DELIMITOR='" . $arrConst['_CONST_DECIMAL_DELIMITOR'] . "';\n";
        $psHtml = $psHtml . "_LIST_WORK_DAY_OF_WEEK='" . $arrConst['_CONST_LIST_WORK_DAY_OF_WEEK'] . "';\n";
        $psHtml = $psHtml . "_LIST_DAY_OFF_OF_YEAR='" . $arrConst['_CONST_LIST_DAY_OFF_OF_YEAR'] . "';\n";
        $psHtml = $psHtml . "_INCREASE_AND_DECREASE_DAY='" . $arrConst['_CONST_INCREASE_AND_DECREASE_DAY'] . "';\n";

        $psHtml = $psHtml . "_MODAL_DIALOG_MODE='" . $arrConst['_MODAL_DIALOG_MODE'] . "';\n";
        $psHtml = $psHtml . "_GET_HTTP_AND_HOST='" . $arrConst['_GET_HTTP_AND_HOST'] . "';\n";
        $psHtml = $psHtml . "_IMAGE_URL_PATH='" . $arrConst['_CONST_IMAGE_URL_PATH'] . "';\n";
        $psHtml = $psHtml . "_WEBSITE_PATH ='" . self::_setWebSitePath() . "';\n";
        $psHtml = $psHtml . "</script>\n";
        return $psHtml;
    }

    /**
     * @return string
     */
    public function _getCurrentHttpAndHost()
    {
        //
        $sCurrentHttpHost = 'http://' . $_SERVER['HTTP_HOST'] . self::_setWebSitePath();
        //
        return $sCurrentHttpHost;
    }

    /**
     * @return array
     */
    public function _setLeaderPostionGroup()
    {
        $arrPublicPosition = array("_CONST_MAIN_LEADER_POSITION_GROUP" => "CT,GD,TB,BT,CTH",
            "_CONST_SUB_LEADER_POSITION_GROUP" => "PCT,PGD,PTB,TT,PCTH",
            "_CONST_POSITION_GROUP" => "LANH_DAO_UB_TINH,LANH_DAO_SO,LANH_DAO_UB_QUAN_HUYEN,LANH_DAO_UB_PHUONG_XA,LANH_DAO_BO,LANH_DAO_CUC,LANH_DAO_VU,LANH_DAO_VAN_PHONG,LANH_DAO_PHONG_BAN",
            "_CONST_VAN_PHONG_GROUP" => "LANH_DAO_VAN_PHONG",
            "_CONST_PHONG_BAN_GROUP" => "LANH_DAO_PHONG_BAN",
            "_CONST_PHUONG_XA_GROUP" => "LANH_DAO_PHUONG_XA"

        );
        return $arrPublicPosition;
    }

    /**
     * @return string
     */
    public function _setUserLoginUrl()
    {
        return self::_getCurrentHttpAndHost() . "login/index/";
    }

    /**
     * @return string
     */
    public function _setDefaultUrl()
    {
        return self::_getCurrentHttpAndHost() . 'record/reminder/index/';
    }

    /**
     * @return string
     */
    public function _getUserDb()
    {
        $registry = Zend_Registry::getInstance();
        $connectSQL = $registry->get('connectSQL');
        $dblink = '[' . $connectSQL->db->config->userdbname . ']';
        return $dblink;
    }

    /**
     * @param int $iOption
     * @return string
     */
    public function _setPermisstionSystem($iOption = 0)
    {
        switch ($iOption) {
            case 1;    //Quyen quan tri toan he thong
                return "ADMIN_SYSTEM";
                break;
            case 2;    //Quyen quan tri cap mot don vi trien khai
                return "ADMIN_OWNER";
                break;
            default:
                return "";
                break;
        }
    }

    /**
     * @return string
     */
    public function _setUrlAjax()
    {
        return self::_getCurrentHttpAndHost() . "application/";
    }

    /**
     * @return array
     */
    public function _setProjectPublicConst()
    {
        $arrPublicConst = array("_CONST_LIST_DELIMITOR" => "!#~$|*",
            "_CONST_SUB_LIST_DELIMITOR" => "!~~!",
            "_CONST_DECIMAL_DELIMITOR" => ",",
            "_CONST_IMAGE_URL_PATH" => self::_setImageUrlPath(),
            //DInh nghia bien xac dinh cac ngay le nghi trong nam
            "_CONST_LIST_DAY_OFF_OF_YEAR" => "+/30/04,+/01/05,+/02/09,+/01/01,+/02/01,+/03/01,+/04/01,-/27/12,-/28/12,-/29/12,-/30/12,-/01/01,-/02/01,-/03/01,-/04/01,-/05/01,-/10/03",
            //Dinh nghia bien quy dinh cac ngay lam viec trong tuan
            "_CONST_LIST_WORK_DAY_OF_WEEK" => "2,3,4,5,6",
            //Dinh nghia hang so cho phep tang len hay giam di so ngay hien giai quyet
            //Neu = 1 thi viec tinh so ra so ngay giai quyet bat dau tu ngay hien thoi
            // = 0 thi tang len 01 ngay; = 2 thi luoi ngay giai quyet di 01 ngay,...
            "_CONST_INCREASE_AND_DECREASE_DAY" => "1",
            //Menu Top
            "_NHAC_VIEC" => "Nh&#7855;c vi&#7879;c",
            "_HS_QUA_MANG" => "H&#7891; s&#417; qua m&#7841;ng",
            "_TN_TKQ" => "TN&TKQ",
            "_THU_LY" => "Th&#7909; l&#253;",
            "_THUE" => "Thu&#7871;",
            "_KHO_BAC" => "Kho b&#7841;c",
            "_PHE_DUYET" => "Ph&#234; duy&#7879;t",
            "_TRA_CUU" => "Tra c&#7913;u",
            "_BAO_CAO" => "B&#225;o c&#225;o",
            "_CHAT" => "Sms",
            "_SMS" => "Chat",
            "_DANH_MUC" => "Danh m&#7909;c",
            "_HUONG_DAN" => "H&#432;&#7899;ng d&#7851;n",
            "_TRANG_CHU" => "Trang ch&#7911;",
            "_THOAT" => "Tho&#225;t",
            //Menu Left
            "_DM_TTHC" => "Danh m&#7909;c TTHC",
            "_DM_LOAI" => "Lo&#7841;i danh m&#7909;c",
            "_DM_DOITUONG" => "Danh m&#7909;c &#273;&#7889;i t&#432;&#7907;ng",
            "_DM_QUYEN" => "Danh m&#7909;c quy&#7873;n",
            "_SAOLUU_PHUCHOI" => "Sao l&#432;u Ph&#7909;c h&#7891;i DL",
            // Dang Nhap
            "_TEN_DANG_NHAP" => "T&#234;n &#273;&#259;ng nh&#7853;p",
            "_MAT_KHAU" => "M&#7853;t kh&#7849;u",
            //TTHC
            "_XAC_DINH_NOI_THU_LY" => "X&#225;c &#273;&#7883;nh n&#417;i th&#7909; l&#253;",
            "_DON_VI_CHUYEN" => "&#272;&#417;n v&#7883; chuy&#7875;n",
            "_HAN_GIAI_QUYET" => "H&#7841;n gi&#7843;i quy&#7871;t",
            "_TEN_TO_CHUC_CA_NHAN" => "T&#234;n t&#7893; ch&#7913;c c&#225; nh&#226;n",
            "_MOI_TIEP_NHAN" => "M&#7899;i ti&#7871;p nh&#7853;n",
            "_LOAI_HO_SO" => "Lo&#7841;i h&#7891; s&#417;",
            "_NGAY_HEN" => "Ng&#224;y h&#7865;n",
            "_TRANG_THAI_HIEN_TAI" => "Tr&#7841;ng th&#225;i hi&#7879;n t&#7841;i",
            "_NGAY_TIEP_NHAN" => "Ng&#224;y ti&#7871;p nh&#7853;n",
            "_MA_TTHC" => "M&#227; TTHC",
            "_TEN_TTHC" => "T&#234;n TTHC",
            "_LOAI_TTHC" => "Lo&#7841;i TTHC",
            "_HOAT_DONG" => "Ho&#7841;t &#273;&#7897;ng",
            "_PB_THU_LY" => "Ph&#242;ng ban th&#7909; l&#253; h&#7891; s&#417;",
            "_CB_TIEP_NHAN" => "C&#225;n b&#7897; ti&#7871;p nh&#7853;n",
            "_CB_THU_LY" => "C&#225;n b&#7897; th&#7909; l&#253; h&#7891; s&#417;",
            "_PHONG_BAN" => "Ph&#242;ng ban",
            "_SO_CAP_DUYET" => "S&#7889; c&#7845;p duy&#7879;t h&#7891; s&#417;",
            "_SO_NGAY_THU_LY_HS" => "S&#7889; ng&#224;y th&#7909; l&#253; h&#7891; s&#417; theo quy &#273;&#7883;nh",
            "_THOI_GIAN_GIAI_QUYET" => "Th&#7901;i gian gi&#7843;i quy&#7871;t",
            "_DANH_SACH_CONG_VIEC" => "Danh s&#225;ch c&#244;ng vi&#7879;c",
            "_HINH_THUC_VB_TRA_KQ" => "H&#236;nh th&#7913;c v&#259;n b&#7843;n tr&#7843; k&#7871;t qu&#7843;",
            "_LE_PHI_MOI" => "L&#7879; ph&#237; c&#7845;p m&#7899;i",
            "_LE_PHI_THAY_DOI" => "L&#7879; ph&#237; thay &#273;&#7893;i",
            "_STT_HS" => "S&#7889; TT h&#7891; s&#417; hi&#7879;n t&#7841;i",
            "_STT_GIAY_PHEP" => "S&#7889; TT gi&#7845;y ph&#233;p hi&#7879;n t&#7841;i",
            "_TRANG_THAI_LIEN_QUAN" => "Tr&#7841;ng th&#225;i li&#234;n quan",
            "_CHUC_VU" => "Ch&#7913;c v&#7909;",
            "_GIAI_DOAN" => "Giai &#273;o&#7841;n",
            "_DM_BAOCAO" => "Danh m&#7909;c b&#225;o c&#225;o",
            //Phe duyet-phan cong thu ly
            "_PHAN_CONG_THU_LY" => "Ph&#226;n c&#244;ng th&#7909; l&#253;",
            "_THONG_TIN_HS" => "TH&#212;NG TIN H&#7890; S&#416;",
            "_NOI_THU_LY" => "N&#417;i th&#7909; l&#253;",
            //Thu ly
            "_TIEN_DO" => "Ti&#7871;n &#273;&#7897;",
            "_CHUYEN_XU_LY" => "Chuy&#7875;n x&#7917; l&#253;",
            "_DON" => "&#272;&#417;n",
            "_CAP_NHAT_ND_GIAY_PHEP" => "C&#7853;p nh&#7853;t n&#7897;i dung gi&#7845;y ph&#233;p",
            "_KQ_GIAI_QUYET" => "K&#7871;t qu&#7843; gi&#7843;i quy&#7871;t",
            "_CAN_BO_THUC_HIEN" => "C&#225;n b&#7897; th&#7921;c hi&#7879;n",
            "_IN_TIEN_DO" => "In ti&#7871;n &#273;&#7897;",
            "_TRINH_LANH_DAO" => "Tr&#236;nh l&#227;nh &#273;&#7841;o",
            "_LANH_DAO_DONVI" => "L&#227;nh &#273;&#7841;o &#273;&#417;n v&#7883;",
            "_NGAY_CHUYEN" => "Ng&#224;y chuy&#7875;n",
            "_CHUYEN_DV_LIEN_THONG" => "Chuy&#7875;n &#273;&#417;n v&#7883; li&#234;n",
            "_BO_SUNG_HS" => "Y&#234;u c&#7847;u b&#7893; sung h&#7891; s&#417;",
            "_CHUYEN_THUE" => "Chuy&#7875;n thu&#7871;",
            "_CHUYEN_KHO_BAC" => "Chuy&#7875;n kho b&#7841;c",
            "_TU_CHOI_TKQ" => "T&#7915; ch&#7889;i tr&#7843; v&#7873; b&#7897; ph&#7853;n m&#7897;t c&#7917;a",
            "_KE_KHAI_THUE" => "K&#234; khai thu&#7871;",
            "_IN_THONG_BAO_THUE" => "In th&#244;ng b&#225;o thu&#7871;",
            "_TU_CHOI_THULY" => "T&#7915; ch&#7889;i tr&#7843; v&#7873; b&#7897; ph&#7853;n th&#7909; l&#253;",
            "_DONG_Y_KE_KHAI" => "&#272;&#7891;ng &#253; k&#234; khai",
            "_NOP_THUE" => "N&#7897;p thu&#7871;",
            "_HS_HOP_LE" => "H&#7891; s&#417; h&#7907;p l&#7879;",
            "_HS_KHONG_HOP_LE" => "H&#7891; s&#417; kh&#244;ng h&#7907;p l&#7879;",
            "_HAN_HOAN_THANH" => "H&#7841;n ho&#224;n th&#224;nh",
            //Phe duyet
            "_DUYET_HO_SO" => "Duy&#7879;t h&#7891; s&#417;",
            "_NGAY_THUC_HIEN" => "Ng&#224;y th&#7921;c hi&#7879;n",
            "_TRANG_THAI_PHE_DUYET" => "Tr&#7841;ng th&#225;i ph&#234; duy&#7879;t",
            //TKQ
            "_TKQ" => "Tr&#7843; k&#7871;t qu&#7843;",
            "_GUI_MAIL" => "G&#7917;i email th&#244;ng b&#225;o",
            "_GUI_SMS" => "G&#7917;i sms th&#244;ng b&#225;o",
            "_NGAY_TKQ" => "Ng&#224;y tr&#7843; k&#7871;t qu&#7843;",
            "_HO_TEN_CONG_DAN" => "H&#7885; v&#224; t&#234;n c&#244;ng d&#226;n, t&#7893; ch&#7913;c",
            "_GHI_CHU" => "Ghi ch&#250;",
            "_LE_PHI" => "L&#7879; ph&#237;",
            "_DIA_CHI" => "&#272;&#7883;a ch&#7881;",
            "_THONG_BAO" => "Th&#244;ng b&#225;o",
            //Tim kiem
            "_DA_TN" => "&#272;&#227; ti&#7871;p nh&#7853;n",
            "_HS_TON" => "HS t&#7891;n",
            "_MOI_TN" => "M&#7899;i ti&#7871;p nh&#7853;n",
            "_DANG_GIAI_QUYET" => "&#272;ang gi&#7843;i quy&#7871;t",
            "_CAP_PHEP_CHO_TKQ" => "C&#7845;p ph&#233;p ch&#7901; TKQ",
            "_CAP_PHEP_DA_TKQ" => "C&#7845;p ph&#233;p &#273;&#227; TKQ",
            "_TU_CHOI_CHO_TKQ" => "T&#7915; ch&#7889;i ch&#7901; TKQ",
            "_TU_CHOI_DA_TKQ" => "T&#7915; ch&#7889;i &#273;&#227; TKQ",
            "_DANG_QH" => "&#272;ang QH",
            "_QH_THUE" => "QH thu&#7871;",
            "_TONG_HS_XU_LY" => "T&#7893;ng h&#7891; s&#417; ph&#7843;i x&#7917; l&#253;",
            //Hang so trong ung dung
            "_IN_GIAY_BIEN_NHAN" => "In gi&#7845;y bi&#234;n nh&#7853;n",
            "_IN_GIAY_BIEN_NHAN_PHU" => "In gi&#7845;y bi&#234;n nh&#7853;n ph&#7909;",
            "_IN_PHIEU_BAN_GIAO" => "In phi&#7871;u b&#224;n giao",
            "_IN_PHIEU_BAN_GIAO_PHU" => "In phi&#7871;u b&#224;n giao ph&#7909;",
            "_IN_PHIEU_KIEM_SOAT" => "In phi&#7871;u ki&#7875;m so&#225;t",
            "_CHON" => "Ch&#7885;n",
            "_STT" => "STT",
            "_NAM" => "N&#259;n",
            "_NGAY_BAN_HANH" => "Ng&#224;y ban h&#224;nh",
            "_NOI_BAN_HANH" => "N&#417;i ban h&#224;nh",
            "_QUYEN_XEM" => "Quy&#7873;n xem",
            "_NGUOI_TAO_LAP" => "Ng&#432;&#7901;i t&#7841;o l&#7853;p",
            "_TAT_CA" => "T&#7845;t c&#7843;",
            "_KHAC" => "Kh&#225;c",
            "_TIEP_NHAN" => "Ti&#7871;p nh&#7853;n",
            "_MA_HO_SO" => "M&#227; h&#7891; s&#417;",
            "_TEN_HO_SO" => "T&#234;n lo&#7841;i h&#7891; s&#417;",
            "_HINH_THUC_XU_LY" => "Hình thức xử lý",
            "_FILE_DINH_KEM" => "File &#273;&#237;nh k&#232;m",
            "_LINH_VUC" => "L&#297;nh v&#7921;c",
            "_SO" => "S&#7889;",
            "_KY_HIEU" => "K&#253; hi&#7879;u",
            "_SO_BAN" => "S&#7889; bản",
            "_SO_TRANG" => "S&#7889; trang",
            "_CAP_KY_DUYET" => "C&#7845;p k&#253; duy&#7879;t",
            "_NGUOI_KY" => "Ng&#432;&#7901;i k&#253;",
            "_NOI_NHAN" => "N&#417;i nh&#7853;n",
            "_THONG_TIN_KHAC" => "Th&#244;ng tin kh&#225;c",
            "_CAN_BO_NHAN" => "C&#225;n b&#7897; nh&#7853;n",
            "_DON_VI_PHONG_BAN_NHAN" => "&#272;&#417;n v&#7883; ph&#242;ng ban nh&#7853;n",
            "_EXPORT_WEB" => "Web",
            "_EXPORT_WORD" => "Word",
            "_EXPORT_EXCEL" => "Excel",
            "_TU_NGAY" => "T&#7915; ng&#224;y",
            "_DEN_NGAY" => "&#272;&#7871;n ng&#224;y",
            "_Y_KIEN_CHI_DAO" => "&#221; ki&#7871;n ch&#7881; &#273;&#7841;o",
            "_CHUYEN_PHONG_BAN" => "Chuy&#7875;n ph&#242;ng ban",
            "_CHUYEN_CAN_BO" => "Chuy&#7875;n c&#225;n b&#7897;",
            "_HAN_XU_LY" => "H&#7841;n x&#7917; l&#253;",
            "_HAN_TRA_LOI" => "H&#7841;n tr&#7843; l&#7901;i",
            "_SO_NGAY" => "S&#7889; ng&#224;y",
            "_NGAY" => "Ng&#224;y",
            "_TRANG_THAI_XU_LY" => "Tr&#7841;ng th&#225;i x&#7917; l&#253;",
            "_DANG_XU_LY" => "&#272;ang x&#7917; l&#253;",
            "_TRA_LAI" => "Tr&#7843; l&#7901;i",
            "_LY_DO" => "L&#253; do",
            "_KET_THUC_XU_LY" => "K&#7871;t th&#7913;c x&#7917; l&#253;",
            "_Y_KIEN_LANH_DAO" => "&#221; ki&#7871;n l&#227;nh &#273;&#7841;o",
            "_KHOI_PHUC_XU_LY" => "Kh&#244;i ph&#7909;c x&#7917; l&#253;",
            "_Y_KIEN_LANH_DAO_PHONG" => "&#221; ki&#7871;n l&#227;nh &#273;&#7841;o ph&#242;ng ban",
            "_LANH_DAO_PHONG" => "L&#227;nh &#273;&#7841;o ph&#242;ng ban",
            "_LANH_DAO_UB" => "L&#227;nh &#273;&#7841;o &#7911;y ban",
            "_CAN_BO_XU_LY" => "C&#225;n b&#7897; x&#7917; l&#253;",
            "_DON_VI_TRINH" => "&#272;&#417;n v&#7883; tr&#236;nh",
            "_NOI_DUNG_TRINH" => "N&#7897;i dung tr&#236;nh",
            "_IN_PHIEU_TRINH_KY" => "In phi&#7871;u tr&#236;nh k&#253;",
            "_THEM_VB_KHAC" => "Th&#234;m v&#259;n b&#7843;n kh&#225;c",
            "_GHI_THEM_MOI" => "Ghi&Th&#234;m m&#7899;i",
            "_GHI_THEM_TIEP" => "Ghi&Thêm ti<U>ế</U>p",
            "_GHI_QUAY_LAI" => "Ghi&Quay l&#7841;i",
            "_GHI_TAM" => "<U>G</U>hi tạm",
            "_QUAY_LAI" => "Quay lại",
            "_THEM" => "Th&#234;m",
            "_SUA" => "S&#7917;a",
            "_DUYET" => "Duyệt",
            "_XOA" => "Xóa",
            "_XEM_CHI_TIET" => "Xem chi ti&#7871;t",
            "_IN" => "In",
            "_Y_KIEN" => "&#221; ki&#7871;n",
            "_KET_XUAT" => "K&#7871;t xu&#7845;t",
            "_TIM_KIEM" => "T&#236;m ki&#7871;m",
            "_TIM_KIEM_NANG_CAO" => "T&#236;m ki&#7871;m n&#226;ng cao",
            "_TRANG_THAI_HO_SO" => "Tr&#7841;ng th&#225;i h&#7891; s&#417;",
            "_GHI" => "Ghi",
            "_SAO_LUU" => "Sao l&#432;u d&#7919; li&#7879;u",
            "_PHUC_HOI" => "Ph&#7909;c h&#7891;i d&#7919; li&#7879;u",
            "_GUI_TIN_SMS" => "G&#7917;i tin SMS",
            "_CAP_NHAT_GUI_TIN_TU_DONG" => "C&#7853;p nh&#7853;t g&#7917;i tin t&#7921; &#273;&#7897;ng",
            "_TEN_CAN_BO" => "T&#234;n c&#225;n b&#7897;",
            "_DON_VI" => "&#272;&#417;n v&#7883;",
            "_CONG_VIEC" => "C&#244;ng vi&#7879;c",
            "_GUI_TIN_TU_DONG" => "G&#7917;i tin t&#7921; &#273;&#7897;ng",
            "_DIEN_THOAI" => "&#272;i&#7879;n tho&#7841;i",
            "_NOI_DUNG" => "N&#7897;i dung",
            "_THU_TU" => "Th&#7913; t&#7921;",
            "_DANG_NHAP" => "Đăng nhập",
            "_DANG_KY" => "Đăng ký",
            "_GUI_THONG_BAO" => "Gửi thông báo đến công dân",
            "_NGAY_HEN_MANG_HO_SO_GOC" => "Ng&#224;y h&#7865;n l&#7845;y k&#7871;t qu&#7843;",
            "_GET_HTTP_AND_HOST" => self::_getCurrentHttpAndHost(),
            "_MODAL_DIALOG_MODE" => "0");
        return $arrPublicConst;
    }

    /**
     * @return array
     */
    public function _setFontTitleExcell()
    {
        $arrInfor = array("name" => "Times New Roman",
            "bold" => true,
            "size" => 12
        );
        return $arrInfor;
    }

    /**
     * @return array
     */
    public function _setFontTitleColExcell()
    {
        $arrInfor = array("name" => "Times New Roman",
            "bold" => true,
            "size" => 11
        );
        return $arrInfor;
    }

    /**
     * @return array
     */
    public function _setStyleDataExcell()
    {
        $styleArray = array(
            'font' => array(
                'name' => 'Times New Roman',
                'size' => 11
            ),
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
                'shrinkToFit' => true,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        return $styleArray;
    }

    /**
     * @return array
     */
    public static function configMail()
    {
        $confMail = new Zend_Config_Ini('./config/config.ini', 'Mail');
        return array(
            'mail_name' => $confMail->acc,
            'mail_password' => $confMail->pass
        );
    }
}

