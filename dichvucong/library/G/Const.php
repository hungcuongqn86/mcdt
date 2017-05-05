<?php

class G_Const
{
    protected static $_instance = null;

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Idea: Tao phuong thuc khoi tao cac hang so dung chung
     *
     * @return Mang luu thong tin cac hang so dung chung
     */
    public static function _setProjectPublicConst($module = '')
    {
        $globals = new G_Global();
        $arrPublicConst = array();
        $arrPublicConst = array(//Dinh nghia ma don vi su dung
            "_CONST_LIST_DELIMITOR" => "!#~$|*",
            "_CONST_SUB_LIST_DELIMITOR" => "!~~!",
            "_CONST_DECIMAL_DELIMITOR" => ",",
            "_CONST_IMAGE_URL_PATH" => $globals->dirImage,
            //DInh nghia bien xac dinh cac ngay le nghi trong nam
            "_CONST_LIST_DAY_OFF_OF_YEAR" => "+/30/04,+/01/05,+/02/09,+/01/01,-/28/12,-/29/12,-/30/12,-/01/01,-/02/01,-/03/01,-/10/03",
            //Dinh nghia bien quy dinh cac ngay lam viec trong tuan
            "_CONST_LIST_WORK_DAY_OF_WEEK" => "2,3,4,5,6",
            //Dinh nghia hang so quy dinh thoi gian lam viec trong ngay
            "_CONST_LIST_WORK_TIME_OF_DAY" => "7,8,9,10,11,13,14,15,16",
            //Dinh nghia hang so cho phep tang len hay giam di so ngay hien giai quyet
            //Neu = 1 thi viec tinh so ra so ngay giai quyet bat dau tu ngay hien thoi
            // = 0 thi tang len 01 ngay; = 2 thi luoi ngay giai quyet di 01 ngay,...
            //HANG SO DUNG CHUNG CHO TAT CA PHAN MEM
            "_CONST_INCREASE_AND_DECREASE_DAY" => "1",
            //Menu Top
            "_NHAC_VIEC" => "Nhắc việc",
            "_TRA_CUU" => "Tra cứu",
            "_BAO_CAO" => "Báo cáo",
            "_DANH_MUC" => "Danh mục",
            "_HUONG_DAN" => "Hướng dẫn",
            "_TRANG_CHU" => "Trang chủ",
            "_THOAT" => "Thoát",
            //Menu Left
            "_DM_LOAI" => "Loại danh mục",
            "_DM_DOITUONG" => "Danh mục đối tượng",
            "_DM_QUYEN" => "Danh mục quyền",
            "_QUYEN_GQ_LOAI_HS" => "Quyền giải quyết HS",
            "_SAOLUU_PHUCHOI" => "Sao lưu – Phục hồi DL",
            "_TRANG_THAI_LIEN_QUAN" => "Trạng thái liên quan",
            //Nut
            "_GHI_THEM_MOI" => "Ghi&Thêm mới",
            "_GHI_THEM_TIEP" => "Ghi&Thêm tiếp",
            "_GHI_QUAY_LAI" => "Ghi&Quay lại",
            "_GHI_TAM" => "Ghi tạm",
            "_QUAY_LAI" => "Quay lại",
            "_THEM" => "Thêm",
            "_SUA" => "Sửa",
            "_XOA" => "Xóa",
            "_HUY" => "Hủy",
            "_XEM_CHI_TIET" => "Xem chi tiết",
            "_IN" => "In",
            "_TIM_KIEM" => "Tìm kiếm",
            "_GHI" => "Ghi",
            "_GUI_TIN_SMS" => "Gửi tin SMS",
            "_CAP_NHAT_GUI_TIN_TU_DONG" => "Cập nhật gửi tin tự động",
            "_CAP_NHAT" => "Cập nhật",
            "_TU_KHOA_TIM_KIEM" => "Từ khóa tìm kiếm",
            "_SO_NGAY" => "Số ngày",
            "_HAN_XU_LY" => "Hạn xử lý",
            "_NGAY" => "Ngày",
            "_FILE_DINH_KEM" => "File đính kèm",
            "_GHI_CHU" => "Ghi chú",
            "_NGAY_THUC_HIEN" => "Ngày thực hiện",
            "_DANG_GIAI_QUYET" => "Đang giải quyết",
            "_DA_GIAI_QUYET" => "Đã giải quyết",
            "_NAM" => "Năm",
            "_THANG" => "Tháng",
            "_ngay" => "ngày",
            "_gio" => "giờ",
            "_DANH_SACH_CO" => "Danh sách có: ",
            "_NGAY_TIEP_NHAN" => "Ngày tiếp nhận",
            "_KET_QUA_GIAI_QUYET" => "Kết quả giải quyết",
            "_FAX" => "Fax",
            "_TAI_LIEU_KEM_THEO" => "Tài liệu kèm theo",
            "_TAI_LIEU_KHAC" => "Tài liệu khác",
            "_DIA_CHI" => "Địa chỉ",
            "_NOI_DUNG" => "Nội dung",
            "_DIEN_THOAI" => "Điện thoại",
            "_THU_TU" => "Thứ tự",
            "_DUYET" => "Duyệt",
            "_TRA_LAI" => "Trả lại",
            "_NGAY_THUC_HIEN" => "Ngày thực hiện",
            "_GHI_CHU" => "Ghi chú",
            "_CAN_BO_THUC_HIEN" => "Cán bộ thực hiện",
            "_TEN_CONG_TY" => "CÔNG TY CỔ PHẦN TIN HỌC G4TECH VIỆT NAM<br>",
			
            "_LOAI_DON_THU" => "Loại đơn thư",
            "_SO_BIEN_BAN" => "Số biên bản",
            "_NHOM_HO_SO" => "Nhóm hồ sơ",
            "_LOAI_HO_SO" => "Loại hồ sơ",
            "_NGUOI_NOP_HO_SO" => "Người nộp hồ sơ",
            "_SO_LUONG_HO_SO" => "Số lượng hồ sơ",
            "_DON_VI" => "Đơn vị",
            "_THOI_GIAN_GIAI_QUYET" => "Thời gian giải quyết",
            "_DON_VI" => "Đơn vị",
            "_NGAY_HEN_TRA" => "Ngày hẹn trả",
            "_NOI_NHAN_KET_QUA" => "Nơi nhận kết quả",
            "_CAN_BO_XU_LY_HO_SO" => "Cán bộ xử lý hồ sơ",
            "_SO_LUONG" => "Số lượng",
            "_BIEN_BAN_TONG_HOP" => "Biên bản tổng hợp",
            "_BIEN_BAN_TH_CT" => "Biên bản tổng hợp + chi tiết",
            "_GIAY_HEN_TONG_HOP" => "Giấy hẹn tổng hợp",
            "_DU_KIEN_PHONG_CHUYEN_HS" => "Dự kiến phòng chuyển hồ sơ",
            "_SO_LUONG" => "Số lượng",
            "_NGAY_NHAN" => "Ngày nhận",
            "_THOI_HAN_GIAI_QUYET" => "Thời hạn giải quyết",
            "_HO_SO_GOM_CO" => "Hồ sơ gồm có",
            "_TEN_GIAY_TO" => "Tên giấy tờ",
            "_SO_LUONG" => "Số lượng",
            "_THEM_BIEN_BAN" => "Thêm biên bản",
            "_THEM_HO_SO" => "Thêm hồ sơ",
            "_GHI_LAI" => "Ghi lại",
            "_TEN_HO_SO" => "Tên hồ sơ",
            "_SO_SO_BHXH" => "Số sổ BHXH",
            "_CAN_BO_XL_HO_SO" => "Cán bộ xử lý hồ sơ",
            "_PHONG_BAN_NHAN" => "Phòng ban nhận",
            "_NOI_DANG_KY_KCB" => "Nơi đăng ký KCB",
            "_SO_THE_BHYT" => "Số thẻ BHYT",
            "_CHE_DO" => "Chế độ",
            "_CAN_BO_CHUYEN_QUAN" => "Cán bộ chuyên quản",
            "_GET_HTTP_AND_HOST" => $globals->_getCurrentHttpAndHost(),
            '_TEN_DANG_NHAP' => 'Tên đăng nhập',
            '_GHI' => 'Ghi',
            "_MODAL_DIALOG_MODE" => "0");
        $arrSystemConst = array(
            "_MA_MODULE" => "Mã module",
            "_TEN_MODULE" => "Tên module",
            "_THU_TU" => "Thứ tự",
            "_DON_VI_TRIEN_KHAI" => "Đơn vị triển khai",
            "_TRANG_THAI" => "Trạng thái",
            "_TU_KHOA_TIM_KIEM" => "Từ khóa tìm kiếm",
            "_THUOC_MODULE" => "Thuộc module",
            "_MA_CHUC_NANG" => "Mã chức năng ",
            "_TEN_CHUC_NANG" => "Tên chức năng",
            "_TEN_TABLE" => "Tên Table",
            "_THUOC_TABLE" => "Thuộc Table",
            "_COLUMN" => "Column",
            "_TYPE" => "Type",
            "_LENGTH_VALUE" => "Length/Values",
            "_DEFAULT" => "Default",
            "_NULL" => "Null",
            "_MA_MAN_HINH" => "Mã màn hình",
            "_TEN_MAN_HINH" => "Tên màn hình",
            "_URL" => "URL",
            "_TEN_WF" => "Tên WorkFlow",
            "_DN_QUY_TRINH" => "Định nghĩa quy trình",
            "_COPY_QUY_TRINH" => "Copy quy trình",

        );
        $arrFlowConst = array(
            "_TEN_BUOC_THUC_HIEN" => "Tên bước thực hiện",
            "_MO_TA" => "Mô tả bước thực hiện",
            "_THU_TU" => "Thứ tự",
            "_SO_NGAY_THUC_HIEN" => "Số ngày xử lý",
            "_LOAI_DON" => "Loại đơn",
            "_TRANG_THAI_HT" => "Trạng thái hiện tại",
            "_TRANG_THAI_CHI_TIET" => "Trạng thái chi tiết",
            "_PHONG_BAN" => "Phòng ban",
            "_DON_VI_XU_LY" => "Đơn vị xử lý",
            "_TRANG_THAI_HOAT_DONG" => "Trạng thái hoạt động",
            "_CAN_BO_XU_LY" => "Cán bộ xử lý",
            "_TRANG_THAI_XU_LY" => "Trạng thái xử lý",
            "_BUOC_KE_TIEP" => "Bước tiếp theo",
            "_THUOC_PACKET" => "Thuộc packet",
            "_THUOC_CHUC_NANG" => "Thuộc chức năng",
            "_MAN_HINH_HIEN_THI" => "Màn hình hiển thị",
            "_DAU_VIEC" => "Đầu việc",

        );
        $arrSetupPermissionConst = array(
            "_PHONG_BAN" => "Phòng ban",
            "_DON_VI_XU_LY" => "Đơn vị",
            "_TEN_CAN_BO" => "Tên cán bộ",
            "_BAN_QUYEN" => "Ban quyền",
            "_SUA_QUYEN" => "Sửa quyền",
            "_MODULE_CHUC_NANG" => "Module – Chức năng",
            "_QUYEN" => "Quyền",
            "_XEM" => "Xem",
            "_THEM" => "Thêm",
            "_SUA" => "Sửa",
            "_XOA" => "Xóa",
            "_XEM" => "Xem",
            "_QUYEN_THAO_TAC" => "Thao tác",
            "_NHOM_CHUC_VU" => "Nhóm chức vụ",
        );
        $arrResult = array();
        switch ($module) {
            case 'SYSTEM':
                $arrResult = $arrSystemConst;
                break;
            case 'WORK_FLOW':
                $arrResult = $arrFlowConst;
                break;
            case 'SETUP_PERMISSION':
                $arrResult = $arrSetupPermissionConst;
                break;
            default:
                $arrResult = array_merge($arrPublicConst, $arrSystemConst);
                break;
        }
        $arrResult = array_merge($arrPublicConst, $arrResult);
        return $arrResult;
    }
}