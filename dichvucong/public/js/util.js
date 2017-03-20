// CHU Y: Khong duoc thay doi vi tri cua bien nay (LUON LUON dat o dong dau tien)
//************************************************************************

if (typeof(_ALLOW_EDITING_IN_MODAL_DIALOG) == "undefined")
    _ALLOW_EDITING_IN_MODAL_DIALOG = 0; // Ngam dinh khong cho phep THEM/SUA/XOA cac thong tin danh muc khi chay o mot cua so MODAL DIALOG

if (typeof(_DECIMAL_DELIMITOR) == "undefined")
    _DECIMAL_DELIMITOR = ","; // Ngam dinh su dung day PHAY (,) de phan cach hang nghin


//************************************************************************
// Ham _btn_show_all_file: goi modal dialog de hien thi danh sach cac file trong mot thu muc
// Cach dung:   p_directory      -- thu muc can lay file
//              p_typefile_list  -- danh sach cac phan mo rong cua file
//              p_obj_text       -- Doi tuong ma ten file tra lai
function _btn_show_all_file(p_directory, p_typefile_list, p_obj_text) {
    v_url = _DSP_MODAL_DIALOG_URL_PATH;
    v_url = v_url + "?goto_url=" + escape("dsp_file_in_directory.php?hdn_directory=" + p_directory + "&hdn_typefile_list=" + p_typefile_list);
    v_url = v_url + "&modal_dialog_mode=1" + "&" + randomizeNumber();
    sRtn = showModalDialog(v_url, "", "dialogWidth=400pt;dialogHeight=280pt;dialogTop=80pt;status=no;scroll=no;");
    if (!sRtn) return;
    p_obj_text.value = sRtn;
}
//************************************************************************
// Ham _save_textbox_value_to_textbox_obj duyet tat ca cac doi tuong multiple-text va luu gia tri cua cac phan tu duoc chon
// vao 1 doi tuong textbox co ten xac dinh boi thuoc tinh "xml_tag_in_db_name"
// p_textbox_obj: ten doi tuong multuple-text
function _save_textbox_value_to_textbox_obj(p_textbox_obj, the_separator) {
    var v_value;
    var list_value = the_separator;
    _save_checkbox_value_to_textbox_obj(document.getElementsByName('chk_multiple_textbox'), the_separator);
    try {
        if (!p_textbox_obj.length) {
            list_value = p_textbox_obj.value;
            //eval('document.forms[0].'+p_textbox_obj.xml_tag_in_db_name+'.value=document.forms[0].'+p_textbox_obj.xml_tag_in_db_name+'.value+"'+list_value+'"');
            document.getElementById(p_textbox_obj.getAttribute("xml_tag_in_db_name")).setAttribute("value", list_value);
        } else {
            current_chk_obj = p_textbox_obj[0].getAttribute("xml_tag_in_db_name");
            for (i = 0; i < p_textbox_obj.length; i++) {
                next_chk_obj = p_textbox_obj[i].getAttribute("xml_tag_in_db_name");
                if (current_chk_obj != next_chk_obj) {    //Neu het danh sach thi gan vao gia tri cua danh sach
                    //eval('document.forms[0].'+current_chk_obj+'.value = document.forms[0].'+current_chk_obj+'.value+"'+list_value+'"');
                    document.getElementById(current_chk_obj).setAttribute("value", list_value);
                    list_value = the_separator;
                }
                v_value = replace(p_textbox_obj[i].value, the_separator, "");
                if (v_value == "" || v_value == null) {
                    v_value = " ";
                }
                list_value = list_append(list_value, v_value, the_separator);
                if (i == p_textbox_obj.length - 1) {          //Cuoi gan gia tri vao danh sach
                    //eval('document.forms[0].'+current_chk_obj+'.value=document.forms[0].'+current_chk_obj+'.value+"'+list_value+'"');
                    document.getElementById(current_chk_obj).setAttribute("value", list_value);
                    list_value = the_separator;
                }
                current_chk_obj = next_chk_obj;
            }
        }
    } catch (e) {
        ;
    }
}
// Tao danh sach (list) chua cac the XML va danh sach gia tri tuong ung. Cac the nay duoc luu vao CSDL duoi danh chuoi XML
//Sau do luu vao 2 bien hidden.
// f: form object
// hdn_obj_tag: ten bien hidden chua danh sach cac the XML
// hdn_obj_value: ten bien hidden chua danh sach cac gia tri tuong ung voi moi the XML
// p_only_for_xml_data: neu la true thi chi luu cac the XML va gia tri cua cac form field co thuoc tinh xml_data='true'

function _save_xml_tag_and_value_list(p_form_obj, p_hdn_tag_obj, p_hdn_value_obj, p_only_for_xml_data) {
    var list_tag = "";
    var list_value = "";
    var v_temp = "";
    var v_value = "";
    _save_checkbox_value_to_textbox_obj(document.getElementsByName('chk_multiple_checkbox'), ',');
    _save_checkbox_value_to_textbox_obj(document.getElementsByName('chk_unit_id'), ',');
    _save_checkbox_value_to_textbox_obj(document.getElementsByName('chk_staff_id'), ',');
    _save_textbox_value_to_textbox_obj(document.getElementsByName('txt_multiple_textbox'), _LIST_DELIMITOR);
    f = p_form_obj;
    for (i = 0; i < f.length; i++) {
        var e = f.elements[i];
        if ((p_only_for_xml_data && e.getAttribute("xml_data") == 'true' && e.getAttribute("store_in_child_table") != 'true') || (!p_only_for_xml_data)) {
            if (e.value == "" || e.value == null) {
                v_value = " ";
            } else {
                v_value = e.value;
            }
            if (e.getAttribute("xml_tag_in_db") && e.getAttribute("store_in_child_table") != 'true' && (e.type != 'radio' && e.type != 'checkbox')) {
                list_tag = list_append(list_tag, e.getAttribute("xml_tag_in_db"), _SUB_LIST_DELIMITOR);
                list_value = list_append(list_value, v_value, _SUB_LIST_DELIMITOR);
            }
            if (e.getAttribute("xml_tag_in_db") && e.getAttribute("store_in_child_table") != 'true' && (e.type == 'radio' || e.type == 'checkbox')) {
                list_tag = list_append(list_tag, e.getAttribute("xml_tag_in_db"), _SUB_LIST_DELIMITOR);
                if (e.checked == true) {
                    v_temp = "true";
                } else {
                    v_temp = "false";
                }
                list_value = list_append(list_value, v_temp, _SUB_LIST_DELIMITOR);
            }
        }
    }
    p_hdn_tag_obj.value = list_tag;
    p_hdn_value_obj.value = list_value;
//alert(p_hdn_tag_obj.value + '\n' + p_hdn_value_obj.value);
}
// Ham _save_checkbox_value_to_textbox_obj duyet tat ca cac doi tuong multiple-checkbox va luu gia tri cua cac phan tu duoc chon
// vao 1 doi tuong textbox co ten xac dinh boi thuoc tinh "xml_tag_in_db_name"
// p_chk_obj: ten doi tuong multuple-checkbox

function _save_checkbox_value_to_textbox_obj(p_chk_obj, the_separator) {
    var ret = "";
    try {
        if (!p_chk_obj.length) {
            if (document.getElementById(p_chk_obj.getAttribute("xml_tag_in_db_name")).getAttribute("checked")) {
                ret = document.getElementById(p_chk_obj.getAttribute("xml_tag_in_db_name")).getAttribute("value");
                document.getElementById(p_chk_obj.getAttribute("xml_tag_in_db_name")).setAttribute("value", ret);
            }
        } else {
            current_chk_obj = p_chk_obj[0].getAttribute("xml_tag_in_db_name");
            for (i = 0; i < p_chk_obj.length; i++) {
                next_chk_obj = p_chk_obj[i].getAttribute("xml_tag_in_db_name");
                if (current_chk_obj != next_chk_obj) {  //Neu het danh sach thi gan vao gia tri cua danh sach
                    document.getElementById(current_chk_obj).setAttribute("value", ret);
                    ret = "";
                }
                if (p_chk_obj[i].checked) {
                    ret = list_append(ret, p_chk_obj[i].value, the_separator);
                }
                if (i == p_chk_obj.length - 1) { //Cuoi gan gia tri vao danh sach
                    document.getElementById(current_chk_obj).setAttribute("value", ret);
                    ret = "";
                }
                current_chk_obj = next_chk_obj;
            }
        }
    } catch (e) {
        ;
    }
}

//**********************************************************************************************************************
// CAC HAM LIEN QUAN DEN VIEC XU LY KHI NSD NHAN CHUOT VAO CAC O CHECKBOX TRONG 1 DANH SACH DE CHON HOAC BO CHON 1 DOI TUONG
//**********************************************************************************************************************

//**********************************************************************************************************************
// Ham show_row_selected de hien thi TAT CA cac dong DA DUOC CHON (checked)
// rad_id: la ten cua bien radio xac dinh che do HIEN THI TAT CA hay Chi HIEN THI NHUNG DOI TUONG DUOC CHON
// (vi du neu ta co <input name="rad_indicator_filter" thi rad_id="rad_indicator_filter")
// tr_name: ten cua id tuong uong voi dong chua doi tuong (vi d? neu ta co <tr id="xxxx"> thi tr_name="xxxx")
//**********************************************************************************************************************
function _show_row_selected(tablename) {
    var v_count, objtable, i, v_modul_checked = false;
    objtable = document.getElementById(tablename);
    v_count = objtable.getElementsByTagName('tr').length;
    for (i = 0; i < v_count; i++) {
        if (objtable.rows[i].getAttribute('checked') == "")
            objtable.rows[i].style.display = "none";
    }
}

//**********************************************************************************************************************
// Ham show_row_all de hien thi TAT CA cac dong (khong phan biet da DUOC CHON hay khong)
// rad_id: la ten cua bien radio xac dinh che do HIEN THI TAT CA hay Chi HIEN THI NHUNG DOI TUONG DUOC CHON
// (vi du neu ta co <input name="rad_indicator_filter" thi rad_id="rad_indicator_filter")
// tr_name: ten cua id tuong uong voi dong chua doi tuong (vi d? neu ta co <tr id="xxxx"> thi tr_name="xxxx")
//**********************************************************************************************************************
function _show_row_all(tablename) {
    var v_count, objtable, i, v_modul_checked = false;
    objtable = document.getElementById(tablename);
    v_count = objtable.getElementsByTagName('tr').length;
    for (i = 0; i < v_count; i++)
        objtable.rows[i].style.display = "";
}

// Ham change_item_checked
// Chuc nang: Xu ly khi NSD click mouse vao checkbox cua mot doi tuong trong danh sach
//  -Tim tr_name chua checkbox duoc click va thay doi trang thai cua attribute checked ("" hoac "checked")
//  -Kiem tra cac trang thai checked cua cac function trong modul de xac dinh trang thai checked cua modul
//  (Neu khong co function nao duoc chon thi checked=""; neu co thi checked="checked")
//  -Kiem tra che do hien thi (qua radio button) de refresh lai man hinh
// Tham so:
//  -chk_obj: doi tuong checkbox duoc click
//  -tr_name: ten id cua row chua checkbox (tr_function hoac tr_enduser)
//  -rad_id:  ten id cua radio button xac dinh che do hien thi cua moi loai (rad_group_enduser hoac rad_group_function)

function _change_item_checked(chk_obj, tablename) {
    var v_count, objtable, i, v_modul_checked = false;
    objtable = document.getElementById(tablename);
    v_count = objtable.getElementsByTagName('tr').length;
    for (i = 0; i < v_count; i++) {
        if (objtable.rows[i].getAttribute('value') == chk_obj.value) {
            if (chk_obj.checked)
                objtable.rows[i].setAttribute('checked', 'checked');
            else
                objtable.rows[i].setAttribute('checked', '');
            break;
        }
    }
}
//
// Ham select_unit_checkbox() cho phep danh dau cac staff cua mot phong ban
// Tham so:
// node: ten object cua doi doi tuong phong ban
function select_unit_checkbox(node) {
    var checked = 0;
    var v_count = document.all.chk_staff_id.length;
    if (node.checked) {
        for (i = 0; i < v_count; i++) {
            if (checked > 0 && document.all.chk_staff_id[i].parent_id != node.value) {
                return;
            }
            if (document.all.chk_staff_id[i].parent_id == node.value) {
                document.all.chk_staff_id[i].checked = "checked";
                checked++;
            }
        }
    } else {
        for (i = 0; i < v_count; i++) {
            if (checked > 0 && document.all.chk_staff_id[i].parent_id != node.value) {
                return;
            }
            if (document.all.chk_staff_id[i].parent_id == node.value) {
                document.all.chk_staff_id[i].checked = "";
                checked++;
            }
        }
    }
}
// Ham select_unit_checkbox_treeuser() cho phep danh dau cac staff cua mot phong ban dung cho khai bao xml
// Editer: Hoang Van Toan
// date: 11/10/2009
// Hieu chinh de duyet tren moi trinh duyet
// Tham so:
// node: ten object cua doi doi tuong phong ban
function select_unit_checkbox_treeuser(node) {
    var checked = 0;
    var v_staff = document.getElementsByName('chk_staff_id');
    var child = node.parentNode.parentNode.getElementsByTagName("INPUT");
    if (node.checked) {
        for (var r = 0; r < child.length; r++) {
            if (child[r].getAttribute('name') === 'chk_staff_id') {
                child[r].checked = true;
            }
            if (child[r].getAttribute('name') === 'chk_unit_id') {
                child[r].checked = true;
                child[r].className = '';
            }
        }
    } else {
        for (var r = 0; r < child.length; r++) {
            if (child[r].getAttribute('name') === 'chk_staff_id') {
                child[r].checked = false;
            }
            if (child[r].getAttribute('name') === 'chk_unit_id') {
                child[r].checked = false;
                child[r].className = '';
            }
        }
    }
}

// Ham select_unit_checkbox_treeuser() cho phep danh dau cac staff cua mot phong ban dung cho khai bao xml
// Tham so:
// node: ten object cua doi doi tuong phong ban
function select_staff_checkbox_treeuser(node) {
    var checked = 0;
    var unit_id = '';
    // document.getElementById(id).parentNode.removeChild(document.getElementById(chk_staff_id));
    var parentUnit = node.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.getElementsByTagName('INPUT')
    for (var i = 0; i < parentUnit.length; i++) {
        if (parentUnit[i].getAttribute('name') === 'chk_unit_id')
            unit_id = parentUnit[i];
        if (parentUnit[i].getAttribute('name') === 'chk_staff_id' && parentUnit[i].checked)
            checked++;
    }
    ;
    if (checked > 0) {
        unit_id.className = (unit_id.checked ? '' : 'dynatree_checkbox');
    } else {
        unit_id.className = '';
    }
}
function browunit() {
    chk_unit_id

}
////////////////////////////////////////////////////////////////////////
// Ham add_new_row() duoc goi khi NSD muon them mot dong
// Tham so:
// p_row_obj: ten object cua cac dong
// p_limit: so row toi da duoc them
////////////////////////////////////////////////////////////////////////////
function add_row(p_row_obj, p_limit) {
    for (var i = 0; i < p_limit; i++) {
        if (p_row_obj[i].style.display == "none") {
            p_row_obj[i].style.display = "block";
            return;
        }
    }
}
////////////////////////////////////////////////////////////////////////////
// Ham delete_row() xu ly khi NSD xoa cac file dinh kem hien co
//Chuc nang:
//  - Khong hien thi cac file bi xoa
//  - Luu ID cua cac file bi xoa vao mot doi tuong kieu hidden
//Tham so:
//  - p_row_obj: ten doi tuong (duoc dinh nghia bang thuoc tinh "id" trong tag <tr>)
//  - p_checkbox_obj: ten doi tuong checkbox tuong ung
//  - p_hdn_obj ten doi tuong hidden luu gia tri cua cac file bi xoa
////////////////////////////////////////////////////////////////////////////
function delete_row(p_row_obj, p_checkbox_obj, p_hdn_obj) {
    if (checkbox_value_to_list(p_checkbox_obj, _LIST_DELIMITOR) != "") {
        if (p_hdn_obj.value != "") {
            p_hdn_obj.value = p_hdn_obj.value + _LIST_DELIMITOR + checkbox_value_to_list(p_checkbox_obj, _LIST_DELIMITOR);
        } else {
            p_hdn_obj.value = checkbox_value_to_list(p_checkbox_obj, _LIST_DELIMITOR);
        }
    }
    try {
        for (i = 0; i < p_row_obj.length; i++) {
            if (p_checkbox_obj[i].checked) {
                p_row_obj[i].style.display = "none";
                p_checkbox_obj[i].checked = false;
            }
        }
    }
    catch (e) {
        ;
    }
}

////////////////////////////////////////////////////////////////////////
// Ham reshow_row() DAU cac dong da bi xoa truoc do
// Tham so:
// p_row_obj: ten object cua cac dong
// p_checkbox_obj: ten doi tuong checkbox tuong ung
// p_hdn_obj: ten doi tuond hidden luu thu tu cua cac row da bi xoa truoc do
////////////////////////////////////////////////////////////////////////////
function reshow_row(p_row_obj, p_checkbox_obj, p_hdn_obj) {
    if (p_hdn_obj.value == "" || p_hdn_obj.value == null)
        return;
    deleted_str = p_hdn_obj.value;
    deleted_array = deleted_str.split(",");
    for (i = 0; i < deleted_array.length; i++) {
        p_row_obj[i].style.display = "none";
        p_checkbox_obj[i].checked = false;
    }
}

// Luu cac gia tri cua cac doi tuong hidden co thuoc tinh save=1
// Tham so:
// f: ten form
// p_save_hidden_input_obj: ten doi tuong hidden dung de luu gia tri cua cac hidden khac co thuoc tinh save=1
function save_hidden_input(f, p_save_hidden_input_obj) {
    var errors = "";
    var strReturn = "";
    var stt = 1;
    for (var i = 0; i < f.length; i++) {
        var e = f.elements[i];
        if (e.type == "hidden" && e.save == "1") {
            strReturn = strReturn + e.name + _SUB_LIST_DELIMITOR + e.value;
            strReturn = strReturn + _LIST_DELIMITOR;
        }
    }
    p_save_hidden_input_obj.value = strReturn;
}
/////////////////////////// Cac ham JS duoc su dung trong modul quan tri danh muc //////////////////////
// Ham nay duoc goi khi NSD nhan chuot vao ten file dinh kem
// Ham nay mo mot cua so moi va thuc thi dsp_file_content.php
function filename_onclick(p_table, p_file_id_column, p_file_name_column, p_file_content_column, p_file_id, p_file_url) {
    url = _DSP_FILE_CONTENT_URL_PATH;
    url = url + "?file_id=" + p_file_id;
    url = url + "&table=" + p_table;
    url = url + "&file_id_column=" + p_file_id_column;
    url = url + "&file_name_column=" + p_file_name_column;
    url = url + "&file_content_column=" + p_file_content_column;
    url = url + "&file_url=" + p_file_url;
    open(url);
}
// Xu ly khi NSD nhan phim ENTER trong o textbox "Tim Kiem"
function txt_filter_keydown() {
    var keyCode = (document.layers) ? keyStroke.which : event.keyCode;
    // Phim Enter
    if (keyCode == 13) {
        document.forms[0].btn_filter.onclick();
        return;
    }
}
// ham process_hot_key() xu ly cac phim nong tren form
function process_hot_key(p_f12, p_insert, p_delete, p_esc, p_enter) {
    var keyCode = (document.layers) ? keyStroke.which : event.keyCode;
    // Phim INSERT
    if (keyCode == 45 && p_insert) {
        document.forms[0].btn_add.onclick();
        return;
    }
    // Phim Delete
    if (keyCode == 46 && p_delete) {
        document.forms[0].btn_delete.onclick();
        return;
    }
    // Phim ESC
    if (keyCode == 27 && p_esc) {
        document.forms[0].btn_back.onclick();
        return;
    }
    // Phim F12
    if (keyCode == 123 && p_esc) {
        document.forms[0].btn_save.onclick();
        return;
    }
}

// ham btn_filter_onclick() duoc goi khi NSD nhan chuot vao nut "Loc" tren man hinh hien thi mot danh sach
// p_hidden_filter_obj: doi tuong hidden dung de luu dieu kien tim kiem
// p_filter_obj: doi tuong textbox dung de nhap dieu kien tim kiem
// p_fuseaction: ten fuseaction tiep theo
function btn_filter_onclick(p_hidden_filter_obj, p_filter_obj, p_fuseaction) {
    p_hidden_filter_obj.value = p_filter_obj.value;
    document.forms[0].fuseaction.value = p_fuseaction;
    document.forms[0].submit();
}

// ham row_onclick() duoc goi khi NSD nhan chuot vao 1 dong trong danh sach. Ham nay co 3 tham so:
//  - tham so thu nhat la ten form field chua ID cua doi tuong,
//  - tham so thu hai la gia tri cua ID
//  - tham so thu ba la ten fuseaction tiep theo
function row_onclick(p_obj, p_value, p_goto_url) {
    p_obj.value = p_value;
    if (_MODAL_DIALOG_MODE == 1) {
        v_url = _DSP_MODAL_DIALOG_URL_PATH;
        v_url = v_url + "?goto_url=" + p_goto_url + "&hdn_item_id=" + p_value + "&fuseaction=" + p_fuseaction + "&modal_dialog_mode=1" + "&" + randomizeNumber();
        sRtn = showModalDialog(v_url, "", "dialogWidth=470pt;dialogHeight=210pt;status=no;scroll=no;");
        //document.forms[0].fuseaction.value = "";
        //document.forms[0].submit();
        document.getElementsByTagName('form')[0].action = p_goto_url;
        document.getElementsByTagName('form')[0].submit();
    } else {
        document.getElementsByTagName('form')[0].action = p_goto_url;
        document.getElementsByTagName('form')[0].submit();
    }
}

// ham btn_delete_onclick() duoc goi khi NSD nhan chuot vao nut "Xoa"
//  - p_checkbox_name: ten cua checckbox, vi du "chk_building_form_id"
//  - p_url: Dia chi URL de thuc thi
function btn_delete_onclick(p_checkbox_obj, p_hidden_obj, p_url) {
    if (!checkbox_value_to_list(p_checkbox_obj, ",")) {
        alert("Chưa có đối tượng nào được chọn!");
    }
    else {
        if (confirm('Bạn thực sự muốn xóa đối tượng đã chọn?')) {
            value_list = checkbox_value_to_list(p_checkbox_obj, ",");
            p_hidden_obj.value = value_list;
            actionUrl(p_url);
        }
    }
}
//Ham btn_update_onclick duoc goi khi NSD bam nut "Sua"
//- p_checkbox_obj      Ten cua checkbox
//- p_url               Dia chi URL de thuc thi
function btn_update_onclick(p_checkbox_obj, p_url) {
    v_value_list = checkbox_value_to_list(p_checkbox_obj, ",");
    if (!v_value_list) {
        alert("Chua co doi tuong nao duoc chon");
    } else {
        arr_value = v_value_list.split(",");
        if (arr_value.length > 1) {
            alert("Chi duoc chon mot doi tuong de sua")
            return;
        }
        else {
            item_onclick(v_value_list, p_url);
        }
    }
}
// ham btn_add_onclick() duoc goi khi NSD nhan chuot vao nut "Them"
//  - p_obj: ten cua object chua id duoc chon (dat bang 0)
//  - p_fuseaction: ten fuseaction tiep theo
function btn_add_onclick(p_obj, p_fuseaction, p_goto_url) {
    p_obj.value = 0;
    if (_MODAL_DIALOG_MODE == 1) {
        v_url = _DSP_MODAL_DIALOG_URL_PATH;
        v_url = v_url + "?goto_url=" + p_goto_url + "&hdn_item_id=0" + "&fuseaction=" + p_fuseaction + "&modal_dialog_mode=1" + "&" + randomizeNumber();
        sRtn = showModalDialog(v_url, "", "dialogWidth=470pt;dialogHeight=210pt;status=no;scroll=no;");
        //alert(sRtn);
        document.forms[0].fuseaction.value = "";
        document.forms[0].submit();
    } else {
        document.forms[0].fuseaction.value = p_fuseaction;
        document.forms[0].submit();
    }
}

// ham btn_select_onclick() duoc goi khi NSD nhan chuot vao nut "Chon". Ham nay tra lai gia tri cua dong duoc chon va dong cua so lai
function btn_select_onclick(p_checkbox_obj) {
    list_of_id = checkbox_value_to_list(p_checkbox_obj, ",");
    if (!list_of_id) {
        alert("Chua co doi tuong nao duoc chon");
        return;
    }
    window.returnValue = list_get_first(list_of_id, ',');
    window.close();
}

// ham btn_save_onclick() duoc goi khi NSD nhan chuot vao nut "Chap nhan"
//  - p_fuseaction: ten fuseaction tiep theo
function btn_save_onclick(p_fuseaction) {
    if (_MODAL_DIALOG_MODE == 1)
        document.forms[0].action = "index.php?modal_dialog_mode=1";
    else
        document.forms[0].action = "index.php";

    if (verify(document.forms[0])) {
        document.forms[0].fuseaction.value = p_fuseaction;
        document.forms[0].submit();
    }
}

// ham btn_single_checkbox_onclick() duoc goi khi NSD nhan chuot vao mot o Check-box
//  - p_checkbox_obj: ten doi tuong checkbox chi nhan 1 trong 2 gia tri 1 va 0
//  - p_checkbox_obj: ten doi tuong (kieu hidden) luu gia tri cua checkbox
function btn_single_checkbox_onclick(p_checkbox_obj, p_hidden_obj) {
    if (p_checkbox_obj.checked)
        p_hidden_obj.value = 1;
    else
        p_hidden_obj.value = 0;
}

// ham btn_back_onclick() duoc goi khi NSD nhan chuot vao nut "Quay lai"
//  - p_url: URL quay lai
function btn_back_onclick(p_url) {
    if (_MODAL_DIALOG_MODE == 1) {
        if (_ALLOW_EDITING_IN_MODAL_DIALOG != 1)
            window.close();
        else {
            document.getElementsByTagName('form')[0].action = p_url;
            document.getElementsByTagName('form')[0].submit();
        }
    } else {
        document.getElementsByTagName('form')[0].action = p_url;
        document.getElementsByTagName('form')[0].submit();
    }
}

//
function goto_after_update_data(p_save_and_add_new, p_fuseaction, p_return_value) {
    if (p_save_and_add_new == 1) {
        if (_MODAL_DIALOG_MODE == 1) {
            document.forms[0].action = "index.php?modal_dialog_mode=1";
        } else {
            document.forms[0].action = "index.php";
        }
        document.forms[0].fuseaction.value = p_fuseaction;
        document.forms[0].submit();
    } else {
        document.forms[0].action = "index.php";
        document.forms[0].submit();
        //}
    }
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////
// Ham nay duoc goi khi NSD click vao ten mot tap tin (de xem hoac download)
function show_file_content(p_filename) {
    window.location = p_filename;
}
// Hien thi cua so ModalDialog de chon ngay
// p_url: duong dan URL toi thu muc chua calendar.html
// p_obj: ten doi tuong form nhan gia tri ngay
function DoCal(p_url, p_obj, browerName) {
    var url = p_url + "Calendar.htm";
    var sRtn;
    if (browerName == 'Safari') {
        dataitem = window.open(url, '', 'height=230px,width=285px,toolbar=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,modal=yes');
        targetitem = p_obj;
        dataitem.targetitem = targetitem;
    } else {
        sRtn = showModalDialog(url, "", "dialogWidth=285px;dialogHeight=230px;status=no;scroll=no;dialogCenter=yes");
        if (sRtn != "") {
            p_obj.value = sRtn;
        }
    }
}
function openPopUpWindow(url) {
    open(url, '', 'height=230px,width=285px,toolbar=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,modal=yes');
}
// Quay ve trang truoc do
function goback() {
    if (_MODAL_DIALOG_MODE == 1) {
        if (_ALLOW_EDITING_IN_MODAL_DIALOG != 1)
            window.close();
        else {
            document.forms[0].fuseaction = "";
            document.forms[0].submit();
        }
    } else
        window.history.back();
}
//Chuyen toi url
function goto_url(p_url, p_open_new_win) {
    if (p_open_new_win == 3)
        open_me(p_url, 1, 1, 1, 1, 1, 0, 0, 0, 0, 450, 650, 0, 0);
    else {
        //document.parentWindow.location = p_url;
        //alert (document.location);
        if (p_open_new_win == 2)
            window.top.location = p_url;
        else {
            window.location = p_url;
        }
    }
}
// open new window with some value
function open_me(url, vStatus, vResizeable, vScrollbars, vToolbar, vLocation, vFullscreen, vTitlebar, vCentered, vHeight, vWidth, vTop, vLeft) {
    winDef = '';
    winDef = winDef.concat('status=').concat((vStatus) ? 'yes' : 'no').concat(',');
    winDef = winDef.concat('resizable=').concat((vResizeable) ? 'yes' : 'no').concat(',');
    winDef = winDef.concat('scrollbars=').concat((vScrollbars) ? 'yes' : 'no').concat(',');
    winDef = winDef.concat('toolbar=').concat((vToolbar) ? 'yes' : 'no').concat(',');
    winDef = winDef.concat('location=').concat((vLocation) ? 'yes' : 'no').concat(',');
    winDef = winDef.concat('fullscreen=').concat((vFullscreen) ? 'yes' : 'no').concat(',');
    winDef = winDef.concat('titlebar=').concat((vTitlebar) ? 'yes' : 'no').concat(',');
    winDef = winDef.concat('height=').concat(vHeight).concat(',');
    winDef = winDef.concat('width=').concat(vWidth).concat(',');

    if (vCentered) {
        winDef = winDef.concat('top=').concat((screen.height - vHeight) / 2).concat(',');
        winDef = winDef.concat('left=').concat((screen.width - vWidth) / 2);
    }
    else {
        winDef = winDef.concat('top=').concat(vTop).concat(',');
        winDef = winDef.concat('left=').concat(vLeft);
    }
    open(url, '_blank', winDef);
}
// formfield_value_to_list(the_field) converts all values of a form field to a list
// the_field is a form field that have some elements, the_field must not be a checkbox object
function formfield_value_to_list(the_field, the_separator) {
    var ret = "";
    if (!the_field.length) {
        ret = the_field.value;
    }
    else {
        for (i = 0; i < the_field.length; i++) {
            ret = list_append(ret, the_field[i].value, the_separator);
        }
    }
    return ret;
}
// checkbox_value_to_list(the_field) converts all values of a checkbox object to a list
// the_field is a checkbox object
function checkbox_value_to_list(the_field, the_separator) {
    var ret = "";
    try {
        if (!the_field.length) {
            if (the_field.checked) {
                ret = the_field.value;
            }
        }
        else {
            for (i = 0; i < the_field.length; i++) {
                if (the_field[i].checked) {
                    ret = list_append(ret, the_field[i].value, the_separator);
                }
            }
        }
    } catch (e) {
        ;
    }
    return ret;
}
// Lay phan tu dau tien cua mot danh sach
function list_get_first(the_list, the_separator) {
    if (the_list == "") return "";
    arr_value = the_list.split(the_separator);
    return arr_value[0];
}

// Kiem tra phan tu the_element co trong danh sach the_list hay khong
function list_have_element(the_list, the_element, the_separator) {
    if (the_list == "") return -1;
    arr_value = the_list.split(the_separator);
    for (var i = 0; i < arr_value.length; i++) {
        if (arr_value[i] == the_element) {
            return i;
        }
    }
    return -1;
}
function list_count_element(the_list, the_separator) {
    if (the_list == "") return -1;
    arr_value = the_list.split(the_separator);
    if (arr_value.length > 0) {
        return arr_value.length;
    }
    return -1;
}
// add a value to a list
function list_append(the_list, the_value, the_separator) {
    var list = the_list;
    if (list == "") list = the_value;
    else if (the_value != "") list = list + the_separator + the_value;
    return list;
}
// this function does nothing
function nothing() {
    return;
}
//Add p_numberofday days to  p_date
// p_date and return value are in US format
function set_date(p_date, p_numberofday) {
    dt_date = new Date(p_date);
    dt_date.setDate(p_numberofday);
    return dt_date.toLocaleString();
}
// Return number of dates from the begining of the month
function get_date(p_date) {
    dt_date = new Date(p_date);
    dt_date.getDate();
    return dt_date.getDate();
}
//Convert date from mmddyyyy format to ddmmyyyy format
function mmddyyyy_to_ddmmyyyy(theDate) {
    strSeparator = ""
    if (theDate.indexOf("/") != -1) strSeparator = "/";
    if (theDate.indexOf("-") != -1) strSeparator = "-";
    if (theDate.indexOf(".") != -1) strSeparator = ".";
    if (strSeparator == "")
        return "";
    parts = theDate.split(strSeparator);
    day = parts[1];
    month = parts[0];
    year = parts[2];
    return day + strSeparator + month + strSeparator + year.substr(0, 4);

}
//Convert date from ddmmyyyy format to mmddyyyy fromat
function ddmmyyyy_to_mmddyyyy(theDate) {
    strSeparator = ""
    if (theDate.indexOf("/") != -1) strSeparator = "/";
    if (theDate.indexOf("-") != -1) strSeparator = "-";
    if (theDate.indexOf(".") != -1) strSeparator = ".";
    if (strSeparator == "")
        return "";
    parts = theDate.split(strSeparator);
    day = parts[0];
    month = parts[1];
    year = parts[2];
    return month + strSeparator + day + strSeparator + year.substr(0, 4);

}
// ***********************************************************
// Compare date
// Format of p_str_date1 and p_str_date2 is: dd/mm/yyyy
// Return value:
//      >0 - p_str_date1>p_str_date2
//       0  - p_str_date1=p_str_date2
//      -1 - p_str_date1<p_str_date2
// ***********************************************************
function date_compare(p_str_date1, p_str_date2) {
    date1 = new Date(ddmmyyyy_to_mmddyyyy(p_str_date1));
    date2 = new Date(ddmmyyyy_to_mmddyyyy(p_str_date2));
    year1 = date1.getFullYear() * 1;
    month1 = date1.getMonth() * 1;
    day1 = date1.getDate() * 1;

    year2 = date2.getFullYear() * 1;
    month2 = date2.getMonth() * 1;
    day2 = date2.getDate() * 1;

    if (year1 > year2)
        return -1;
    else if (year1 < year2) return 1;
    else {
        if (month1 > month2) return -1
        else if (month1 < month2) return 1
        else {
            if (day1 > day2)return -1;
            else if (day1 < day2)return 1;
            else return 0;
        }
    }
}
// Valid number
function isnum(passedVal) {
    if (passedVal == "") {
        return false;
    }
    for (i = 0; i < passedVal.length; i++) {
        if (passedVal.charAt(i) < "0") {
            return false;
        }
        if (passedVal.charAt(i) > "9") {
            return false;
        }
    }
    return true;
}
// Valid double
function isdouble(passedVal) {
    if (passedVal == "") {
        return false;
    }
    // if there are more character ".", it is invalid double
    if (count_char(passedVal, '.') > 1)
        return false;
    for (i = 0; i < passedVal.length; i++) {
        if (passedVal.charAt(i) != "." && passedVal.charAt(i) < "0") {
            return false;
        }
        if (passedVal.charAt(i) != "." && passedVal.charAt(i) > "9") {
            return false;
        }
    }
    return true;
}
// Valid float
function isfloat(passedVal) {
    if (passedVal == "") {
        return false;
    }
    // if there are more character ".", it is invalid float
    if (count_char(passedVal, '.') > 1)
        return false;
    // if there are more character "-", it is invalid float
    if (count_char(passedVal, '-') > 1)
        return false;
    if (passedVal.indexOf('-') > 0)
        return false;
    passedVal = passedVal.substring(1);
    for (i = 0; i < passedVal.length; i++) {
        if (passedVal.charAt(i) != "." && passedVal.charAt(i) < "0") {
            return false;
        }
        if (passedVal.charAt(i) != "." && passedVal.charAt(i) > "9") {
            return false;
        }
    }
    return true;
}
// Get the number of times the "what_char" is in "what_str"
function count_char(what_str, what_char) {
    if (what_str == "") return 0;
    var str;
    var count;
    var pos;
    count = 0;
    str = what_str;
    while (str.indexOf(what_char, 0) >= 0) {
        count++;
        pos = str.indexOf(what_char, 0) + 1;
        str = str.substring(pos);
    }
    return count;
}
//Checking email;
function isemail(email) {
    var invalidChars = "/ :,;";

    if (email == "") {
        return false;
    }

    for (i = 0; i < invalidChars.length; i++) {
        badChar = invalidChars.charAt(i);
        if (email.indexOf(badChar, 0) > -1) {
            return false;
        }
    }
    atPos = email.indexOf("@", 1)
    if (atPos == -1) {
        return false;
    }
    if (email.indexOf("@", atPos + 1) > -1) {
        return false;
    }
    periodPos = email.indexOf(".", atPos);
    if (periodPos == -1) {
        return false;
    }
    if (periodPos + 3 > email.length) {
        return false;
    }
    return true;
}
// Check date
function isdate(the_date) {
    var strDatestyle = "EU";  //European date style
    var strDate;
    var strDateArray;
    var strDay;
    var strMonth;
    var strYear;
    var intday;
    var intMonth;
    var intYear;
    var booFound = false;
    var strSeparatorArray = new Array("-", " ", "/", ".");
    var intElementNr;
    var err = 0;
    var strMonthArray = new Array(12);

    strMonthArray[0] = "Jan";
    strMonthArray[1] = "Feb";
    strMonthArray[2] = "Mar";
    strMonthArray[3] = "Apr";
    strMonthArray[4] = "May";
    strMonthArray[5] = "Jun";
    strMonthArray[6] = "Jul";
    strMonthArray[7] = "Aug";
    strMonthArray[8] = "Sep";
    strMonthArray[9] = "Oct";
    strMonthArray[10] = "Nov";
    strMonthArray[11] = "Dec";
    strDate = the_date;
    if (strDate == "") {
        return false;
    }
    for (intElementNr = 0; intElementNr < strSeparatorArray.length; intElementNr++) {
        if (strDate.indexOf(strSeparatorArray[intElementNr]) != -1) {
            strDateArray = strDate.split(strSeparatorArray[intElementNr]);
            if (strDateArray.length != 3) {
                err = 1;
                return false;
            } else {
                strDay = strDateArray[0];
                strMonth = strDateArray[1];
                strYear = strDateArray[2];
            }
            booFound = true;
        }
    }
    if (booFound == false) {
        if (strDate.length > 5) {
            strDay = strDate.substr(0, 2);
            strMonth = strDate.substr(2, 2);
            strYear = strDate.substr(4);
        } else {
            return false;
        }
    }
    if (strYear.length == 2) {
        strYear = '20' + strYear;
    }
    // US style
    if (strDatestyle == "US") {
        strTemp = strDay;
        strDay = strMonth;
        strMonth = strTemp;
    }
    if (!isnum(strDay)) {
        err = 2;
        return false;
    }
    intday = parseInt(strDay, 10);
    if (isNaN(intday)) {
        err = 2;
        return false;
    }
    if (!isnum(strMonth)) {
        err = 3;
        return false;
    }
    intMonth = parseInt(strMonth, 10);
    if (isNaN(intMonth)) {
        for (i = 0; i < 12; i++) {
            if (strMonth.toUpperCase() == strMonthArray[i].toUpperCase()) {
                intMonth = i + 1;
                strMonth = strMonthArray[i];
                i = 12;
            }
        }
        if (isNaN(intMonth)) {
            err = 3;
            return false;
        }
    }
    if (!isnum(strYear)) {
        err = 4;
        return false;
    }
    intYear = parseInt(strYear, 10);
    if (isNaN(intYear)) {
        err = 4;
        return false;
    }
    if (intMonth > 12 || intMonth < 1) {
        err = 5;
        return false;
    }
    if ((intMonth == 1 || intMonth == 3 || intMonth == 5 || intMonth == 7 || intMonth == 8 || intMonth == 10 || intMonth == 12) && (intday > 31 || intday < 1)) {
        err = 6;
        return false;
    }
    if ((intMonth == 4 || intMonth == 6 || intMonth == 9 || intMonth == 11) && (intday > 30 || intday < 1)) {
        err = 7;
        return false;
    }
    if (intMonth == 2) {
        if (intday < 1) {
            err = 8;
            return false;
        }
        if (LeapYear(intYear) == true) {
            if (intday > 29) {
                err = 9;
                return false;
            }
        } else {
            if (intday > 28) {
                err = 10;
                return false;
            }
        }
    }
    return true;
}
//Lay ngay tiep theo cua ngay trong elTerget.value
function getNext_Date(elTarget) {
    if (isdate(elTarget.value)) {
        var theDate, strSeparator, arr, day, month, year;
        strSeparator = "";
        theDate = elTarget.value;
        if (theDate.indexOf("/") != -1) strSeparator = "/";
        if (theDate.indexOf("-") != -1) strSeparator = "-";
        if (theDate.indexOf(".") != -1) strSeparator = ".";
        if (strSeparator != "") {
            arr = theDate.split(strSeparator);
            day = new Number(arr[0]) + 1;
            month = new Number(arr[1]);
            year = new Number(arr[2]);
            if (day > 28) {
                if (((month == 1 || month == 3 || month == 5 || month == 7 || month == 8 || month == 10 || month == 12) && (day > 31))
                    || ((month == 4 || month == 6 || month == 9 || month == 11) && (day > 30)) || (month == 2 && year % 4 != 0) || (month == 2 && year % 4 == 0 && day > 29)) {
                    day = 1;
                    month = month + 1;
                }
                if (month > 12) {
                    year = year + 1;
                    month = 1;
                }

            }
            elTarget.value = day + strSeparator + month + strSeparator + year;
        }
    }

}
//Lay ngay truoc cua ngay trong elTerget.value
function getPrior_Date(elTarget) {
    if (isdate(elTarget.value)) {
        var theDate, strSeparator, arr, day, month, year, arr1;
        strSeparator = "";
        theDate = elTarget.value;
        if (theDate.indexOf("/") != -1) strSeparator = "/";
        if (theDate.indexOf("-") != -1) strSeparator = "-";
        if (theDate.indexOf(".") != -1) strSeparator = ".";
        if (strSeparator != "") {
            arr = theDate.split(strSeparator);
            day = new Number(arr[0]) - 1;
            arr1 = new Number(arr[1]);
            year = new Number(arr[2]);
            if (day == 0) {
                if ((arr1 - 1 == 1) || (arr1 - 1 == 3) || (arr1 - 1 == 5) || (arr1 - 1 == 7) || (arr1 - 1 == 8) || (arr1 - 1 == 10) || (arr1 - 1 == 12)) {
                    day = 31;
                    month = arr1 - 1;
                }
                if ((arr1 - 1 == 4) || (arr1 - 1 == 6) || (arr1 - 1 == 9) || (arr1 - 1 == 11)) {
                    day = 30;
                    month = arr1 - 1;
                }
                if ((arr1 - 1 == 2) && (year % 4 != 0)) {
                    day = 28;
                    month = arr1 - 1;
                }
                if ((arr1 - 1 == 2) && (year % 4 == 0)) {
                    day = 29;
                    month = arr1 - 1;
                }
                if (arr1 - 1 == 0) {
                    day = 31;
                    month = 12;
                    year = year - 1;
                }
            } else {
                month = arr1;
            }
            elTarget.value = day + strSeparator + month + strSeparator + year;
        }
    }

}
function LeapYear(intYear) {
    if (intYear % 100 == 0) {
        if (intYear % 400 == 0) {
            return true;
        }
    }
    else {
        if ((intYear % 4) == 0) {
            return true;
        }
    }
    return false;
}
// return true if at least one item is checked
function ischecked(f, objname) {
    var tmpchecked = false;
    var i = 0
    for (i = 0; i < f.length; i++) {
        var e1 = f.elements[i];
        if (e1.name == objname && e1.checked) {
            tmpchecked = true;
            break;
        }
    }
    return tmpchecked;
}
// return true if a string contains only white characters
function isblank(s) {
    var i;
    for (i = 0; i < s.length; i++) {
        var c = s.charAt(i);
        if ((c != " ") && (c != "\n") && (c != "\t")) return false;
    }
    return true;
}
function verify(f) {
    var errors = "";
    var i;
    var strSeparatorArray = new Array("-", " ", "/", ".");
    for (i = 0; i < f.length; i++) {
        var e = f.elements[i];
        var oE = $(e);
        //if type object is "hidden" then continue
        if (e.type == "hidden") continue;
        //get attribute
        var optional = e.getAttribute("option");
        if (optional == "" || optional == null) {
            var optional = e.getAttribute("optional");
        }
        //convert string to boolean
        if (optional == "true" || optional == null) {
            optional = true;
        } else {
            optional = false;
        }
        var message = e.getAttribute("message");
        var label = $('#' + e.getAttribute("id")).prev().html();
        label = '<label>' + label + '</label>';
        label = $("small", label).remove().end().html();
        if (message === '') {
            message = label;
        }
        ;
        if (message != null) {
            message = replace(message, label, '<label style="font-weight: bold;">' + label + '</label>');
        }
        ;
        var is_hour = e.getAttribute("ishour");
        var is_email = e.getAttribute("isemail");
        var is_date = e.getAttribute("isdate");
        var is_numeric = e.getAttribute("isnumeric");
        var is_double = e.getAttribute("isdouble");
        var is_float = e.getAttribute("isdouble");
        var min = e.getAttribute("min") * 1;
        var max = e.getAttribute("max") * 1;
        var maxlength = e.getAttribute("maxlength") * 1;
        var checkempty = e.getAttribute("checkempty");
        //alert(e.type + '--' + e.name + '--' + message + '--' + optional);
        if (e.type == "radio" && !optional) {
            if (ischecked(f, e.name) == false) {
                if (message == null) message = "At least one " + e.name + " must be checked";
                jWarning(message, 'Thông báo', function () {
                    scrollToElement(e);
                    e.focus();
                });
                return false;
            }
        }
        // If it is hour object
        if ((is_hour) && !((e.value == null) || (e.value == "") || isblank(e.value))) {
            if (IsHour(e, ':') == false) {
                if (message == null) message = "Hour is invalid";
                jWarning(message, 'Thông báo', function () {
                    scrollToElement(e);
                    e.focus();
                });
                return false;
            }
        }
        // If it is email object
        if ((is_email) && !((e.value == null) || (e.value == "") || isblank(e.value))) {
            if (isemail(e.value) == false) {
                if (message == null) message = "Email is invalid";
                jWarning(message, 'Thông báo', function () {
                    scrollToElement(e);
                    e.focus();
                });
                return false;
            }
        }
        // if it is Date object
        if (is_date && !optional) {
            if ((e.value == null) || (e.value == "")) {
                if (message == null) message = "Date is invalid";
                jWarning(message, 'Thông báo', function () {
                    scrollToElement(e);
                    e.focus();
                });
                return false;
            }
        }

        if (((is_date) && !((e.value == null) || (e.value == "") || isblank(e.value)))) {
            if (isdate(e.value) == false) {
                if (message == null) message = "Date is invalid";
                jWarning(message, 'Thông báo', function () {
                    scrollToElement(e);
                    e.focus();
                });
                return false;
            } else {
                var strSeparatorArray = new Array("-", " ", "/", ".");
                for (intElementNr = 0; intElementNr < strSeparatorArray.length; intElementNr++) {
                    if (e.value.indexOf(strSeparatorArray[intElementNr]) != -1) {
                        strDateArray = e.value.split(strSeparatorArray[intElementNr]);
                        if (strDateArray[2].length != 4) {
                            if (message == null) message = "Date is invalid";
                            jWarning(message, 'Thông báo', function () {
                                scrollToElement(e);
                                e.focus();
                            });
                            return false;
                        }
                    }
                }
            }
        }
        // if it is number object
        //if ((e.isnumeric || e.isdouble || (e.min!=null) || (e.max!=null)) && !((e.value==null) || (e.value=="") || isblank(e.value)) && !e.optional)
        if ((is_numeric || is_double || is_float || (min != 0) || (max != 0)) && !((e.value == null) || (e.value == "") || isblank(e.value))) {
            if (!_DECIMAL_DELIMITOR)
                decimal_delimitor = ",";
            else
                decimal_delimitor = _DECIMAL_DELIMITOR;
            test_value = replace(e.value, decimal_delimitor, "");
            if (is_double)
                is_number = isdouble(test_value);
            else {
                if (isfloat)
                    is_number = isfloat(test_value);
                else
                    is_number = isnum(test_value);
            }

            var v = parseFloat(test_value);
            var v_min = parseFloat(min);
            var v_max = parseFloat(max);
            if (!is_number
                || ((min != 0) && (v < v_min))
                || ((max != 0) && (v > v_max))) {
                errors += "- The field " + e.name + " must be a number";
                if (min != null)
                    errors += " that is greater than " + min;
                if (min != 0 && max != 0)
                    errors += " and less than " + max;
                else if (max != 0)
                    errors += " That is less than " + max;
                errors += ".\n";
                if (message == null) message = errors;
                jWarning(message, 'Thông báo', function () {
                    scrollToElement(e);
                    e.focus();
                });
                return false;
            }
        }
        // check maxlength
        if ((maxlength != 0) && !((e.value == null) || (e.value == "") || isblank(e.value))) {
            if (e.value.length > maxlength) {
                message = "The length of " + e.name + " must be less than " + maxlength;
                jWarning(message, 'Thông báo', function () {
                    scrollToElement(e);
                    e.focus();
                });
                return false;
            }
        }
        // check multiple selectbox must be not empty
        if (checkempty && e.type == "select-multiple" && e.length == 0) {
            if (message == null) message = e.name + " must be not empty";
            jWarning(message, 'Thông báo', function () {
                scrollToElement(e);
                e.focus();
            });
            return false;
        }
        // Check for text, textarea
        if (((e.type == "password") || (e.type == "text") || (e.type == "textarea") || (e.type == "select-one") || (e.type == "checkbox")) && !optional) {
            if ((e.value == null) || (e.value == "") || isblank(e.value)) {
                if (message == null) message = e.name + " must be not empty";
                jWarning(message, 'Thông báo', function () {
                    scrollToElement(e);
                    e.focus();
                });
                return false;
            }
        }
    }
    return true;
}
/*********************************************************************************************************************
 // Functions for moving items from a selectbox object to other one                                                  //
 //   Before call this function, you have to set value to properties "id","text" of all options of the "from_obj"    //
 //   Parameters:                                                                                                    //
 //     from_obj: is the object name of a select-multiple you want to move some it's options to ...                  //
 //     to_obj: is the object name of a select-multiple you want to add some options                                 //
 **********************************************************************************************************************/

function move_selectbox_item(from_obj, to_obj) {
    var len = 0;
    for (i = 0; i < from_obj.length; i++) {
        if (from_obj.options[i].selected) {
            if (to_obj.length == null) len = 0;
            else len = to_obj.length;
            to_obj.options[len] = new Option(from_obj.options[i].name, from_obj.options[i].id);
            to_obj.options[len].value = from_obj.options[i].value;
            to_obj.options[len].id = from_obj.options[i].id;
            to_obj.options[len].name = from_obj.options[i].name;
            from_obj.options[i].id = -1;
        }
    }
    defrag_object(from_obj);
    refresh(from_obj);
}
// Reindex all options of a select box after moving some options to another one
function defrag_object(obj) {
    var len = obj.length;
    var i = 0;
    while (i < len) {
        if (obj.options[i].id == -1) {
            if (i != (len - 1)) {
                for (j = i; j < len - 1; j++) {
                    obj.options[j].id = obj.options[j + 1].id;
                    obj.options[j].name = obj.options[j + 1].name;
                    //              obj.options[j].value = obj.options[j + 1].value;

                }
            }
            else {
                i++;
            }
            len--;
        }
        else {
            i++;
        }
    }
    obj.length = len;
}
// Refresh a select box after moving some options to another one
function refresh(obj) {
    for (i = 0; i < obj.length; i++) {
        oid = obj.options[i].id;
        oname = obj.options[i].name;
        obj.options[i] = new Option(obj.options[i].name, obj.options[i].id);
        obj.options[i].id = oid;
        obj.options[i].name = oname;
    }
}
/* Display options of the selectbox object by a foreign key value
 Parameters:
 parent_id: the key value to fillter
 child_obj: is a name of selectbox object that will be displayed it's options by parent_id
 */
function display_childselectbox_by_fk(parent_id, child_obj) {
    var k = 0;
    var len = child_obj.arr_parent_id.length;
    child_obj.length = 0;
    for (i = 0; i < len; i++) {
        if (child_obj.arr_parent_id[i] == parent_id || parent_id == "") {
            child_obj.options[k] = new Option(child_obj.arr_text[i]);
            child_obj.options[k].value = child_obj.arr_id[i];
            k = k + 1;
        }
        //if (k>0) child_obj.options[0].selected;
    }
}
/*********************************************************************************************************************
 // Display options of the selectbox object by a value. Text and value of those options will be set from 1 to "maxvalue"
 //   Parameters:
 //          child_obj: is a name of selectbox object that will be displayed it's options with text and value from 1 to "maxvalue"
 //      maxvalue:
 **********************************************************************************************************************/
function display_childselectbox_by_value(child_obj, maxvalue) {
    var k = 0;
    child_obj.length = 0;
    for (i = 1; i < maxvalue + 1; i++) {
        child_obj.options[k] = new Option(i);
        child_obj.options[k].value = i;
        k = k + 1;
    }
//if (k>0) child_obj.options[0].selected;
}
// select all checkboxelements named objname in the form
function select_all_checkbox(f, objname) {
    for (i = 0; i < f.length; i++) {
        var e = f.elements[i];
        if (e.name == objname && !e.disabled) {
            f.elements[i].checked = true;
        }
    }
    return true;
}

// deselect all elements named objname in the form
function deselect_all(f, objname) {
    for (i = 0; i < f.length; i++) {
        var e = f.elements[i];
        if (e.name == objname) {
            f.elements[i].checked = false;
        }
    }
    return true;
}
//  Function set_selected set the i option to be checked if its value equals p_value
function set_selected(p_obj, p_value) {
    for (i = 0; i < p_obj.options.length; i++) {
        if (p_obj.options[i].value == p_value) {
            p_obj.options[i].selected = true;
            break;
        }
    }
}
// function isselected(p_obj) returns true if p_obj has been selected
function isselected(p_obj) {
    for (i = 0; i < p_obj.options.length; i++) {
        if (p_obj.options[i].selected = true) {
            return true;
        }
    }
    return false;
}

/*================================================================
 function saveControl(src, dest);
 Purpose:
 - Save data from a list control into a textbox.
 Input:
 - src:  list control.
 - dest: text control.
 ================================================================*/
function saveControl(src, dest) {
    var i;
    var s = "";
    if (src.options.length > 0) {
        for (i = 0; i < src.options.length; i++) {
            s = s + src.options[i].value + "|" + src.options[i].name + ",";
        }
        dest.value = s.substring(0, s.length - 1);
    } else {
        dest.value = "";
    }
}
/*================================================================
 function restoreControl(src, dest);
 Purpose:
 - Restore data from a list control back to a textbox.
 Input:
 - src:  text control.
 - dest: list control.
 ================================================================*/
function restoreControl(src, dest) {
    var i;
    var s;
    for (i = dest.options.length - 1; i >= 0; i--) {
        dest.options.remove(dest.options[i]);
    }

    i = 0;
    s = src.value + ",";
    while (s.indexOf(",") > 0) {
        var sValue = "" + s.substring(0, s.indexOf("|"));
        var sName = "" + s.substring(s.indexOf("|") + 1, s.indexOf(","));

        dest.options[i] = new Option(sName, sValue);
        dest.options[i].id = sValue;
        dest.options[i].value = sValue;
        dest.options[i].name = sName;
        s = s.substring(s.indexOf(",") + 1, s.length);
        i++;
    }
}
//--------Chuyen focus toi doi tuong tiep theo-----------------------
// f = form name; 0 = cuurent object name
function change_focus(f, o, ev) {
    var ret1 = "";
    var j = 0;
    var i = 0;
    var b = 0;
    first_object_id = -1;
    //try{
    keyCode = ev.keyCode;
    //keyCode = (window.event)?event.keyCode:e.which;
    if (ev.keyCode == 13 && o.type != "button") {
        ev.keyCode = 9;
    }
    // Neu la phim Enter, Down, Up
    if ((keyCode == '9' && o.type == 'select-one') || (o.type != 'select-one' && (keyCode == '40' || keyCode == '38'))) {
        b = 0;
        while (i >= 0 && (i < f.length) && (j < 2)) {
            var e = f.elements[i];
            // Xac dinh ID cua field dau tien co kieu khong phai la hidden
            if (e.type != 'hidden' && first_object_id == -1) first_object_id = i;
            // Tim de vi tri cua doi tuong hien tai
            if ((b == 0) && (e.name == o.name) && (e.type != 'hidden')) {
                o.blur();
                b = 1;
                if (keyCode != '38') {
                    i = i + 1;
                    if (i == f.length) i = first_object_id;
                } else {
                    if (i == first_object_id) i = f.length - 1; else i = i - 1;
                }
                var e = f.elements[i];
            }
            if (b == 1) {
                if ((e.type != 'hidden') && (!e.readOnly) && (!e.disabled) && (e.hide != 'true')) {
                    e.focus();
                    return true;
                }
            }
            if (keyCode != '38') {
                i = i + 1;
                if (i == f.length) {
                    i = 0;
                    j = j + 1;
                }
            } else {
                i = i - 1;
                if (i == first_object_id) {
                    i = f.length - 1;
                    j = j + 1;
                }
            }
        }
    }
    return true;
//}catch(e){}
}
// Disable tat ca cac phan tu cua mot form
// f: ten form
function disable_all_elements(f) {
    var i;
    for (i = 0; i < f.length; i++) {
        if (f.elements[i].type != "hidden")
            f.elements[i].readOnly = true;
        if (f.elements[i].type == "button")
            f.elements[i].disabled = true;
        if (f.elements[i].type == "submit")
            f.elements[i].disabled = true;
    }
    return;
}
//--------Set focus at the first field of the form "f" -----------------------
function set_focus(f) {
    var i = 0;
    while (i < f.length) {
        var e = f.elements[i];
        if (((e.type == 'text') || (e.type == 'textarea')) && (!e.disabled) && (!e.readOnly)) {
            e.focus();
            return true;
        }
        i = i + 1;
    }
    return false;
}
/*********************************************************************************************************************
 //  Ham to_upper_case bien chu thuong thanh chu hoa
 //  Khi goi : onchange="JavaScript:ToUpperKey(this)"
 /*********************************************************************************************************************/
function to_upper_case(p_obj) {
    p_obj.value = p_obj.value.toUpperCase();
}
//  Ham to_lower_case bien chu hoa thanh chu thuong
//  Khi goi : onchange="JavaScript:ToLowerKey(this)"
function to_lower_case(p_obj) {
    p_obj.value = p_obj.value.toLowerCase();
}
//**********************************************************************************************************************
//Doi so xxxxxx.xxxx thanh dinh dang xxx,xxx.xxxx
function FormatCurrency(num) {
    var sign, tail;
    num = num.toString().replace(/\$|\,/g, '');
    if (isNaN(num))
        num = "0";
    sign = (num == (num = Math.abs(num)));
    var str = num.toString();
    var arr_str = str.split(".");
    if (arr_str.length > 1) {
        var tail = new String(arr_str[1])
        if (tail.length < 2) {
            tail = tail + '0';
        }
    } else {
        tail = '';
    }
    num = arr_str[0];
    for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++)
        num = num.substring(0, num.length - (4 * i + 3)) + ',' +

        num.substring(num.length - (4 * i + 3));

    if (tail == '')
        ret_value = (((sign) ? '' : '-') + num);
    else
        ret_value = (((sign) ? '' : '-') + num + '.' + tail);
    return ret_value;
}
/**********************************************************************************************************************
 //  Ham FormatMoney tu dong them dau "," vao text box khi nhap gia tri co kien la "Tien"
 //  Khi do TextBox co dang : "123,456,789"
 //  Khi goi : onkeyup="JavaScript:FormatMoney(this)"
 ***********************************************************************************************************************/
function format_money(Obj, e) {
    var theKey = e.keyCode;
    var theLen = Obj.value.length;
    var theStringNum = Obj.value;
    theSecondStringNum = "";
    // Neu ki tu dau tien la "." thi bo qua
    if (theStringNum == ".") {
        Obj.value = "";
        return;
    }
    pos = theStringNum.indexOf(".", 0)
    if (pos > 0) {
        arr_numstr = theStringNum.split(".");
        theFirstStringNum = theStringNum.substr(0, pos);
        theSecondStringNum = theStringNum.substr(pos + 1, theStringNum.length - pos);
        if (theSecondStringNum.substr(theSecondStringNum.length - 1, 1) == ".") {
            Obj.value = theStringNum.substr(0, theStringNum.length - 1);
            return;
        }
        theStringNum = theFirstStringNum;
    }
    //Chi nhan cac ky tu la so
    if ((theKey >= 48 && theKey <= 57) || (theKey >= 96 && theKey <= 105) || (theKey == 8) || (theKey == 46)) {
        var theNewString;
        var theSubString;
        var LastIndex;
        LastIndex = 0;
        theSubString = ""
        // Thay the ky tu ","
        for (var i = 0; i < theStringNum.length; i++) {
            if (theStringNum.substring(i, i + 1) == _DECIMAL_DELIMITOR)      // Tim ky tu ","
            {
                theSubString = theSubString + theStringNum.substring(LastIndex, i)
                LastIndex = i + 1;
            }
        }
        theSubString = theSubString + theStringNum.substring(LastIndex, theStringNum.length) // Lay mot doan cuoi cung (vi doan cuoi cung khong co ky tu ",")
        theStringNum = theSubString;

        theNewString = ""
        if (theStringNum.length > 3)
            while (theStringNum.length > 3) {
                theSubString = theStringNum.substring(theStringNum.length - 3, theStringNum.length);
                theStringNum = theStringNum.substring(0, theStringNum.length - 3);
                theNewString = _DECIMAL_DELIMITOR + theSubString + theNewString;
            }
        if (pos > 0)
            theNewString = theStringNum + theNewString + "." + theSecondStringNum;
        else
            theNewString = theStringNum + theNewString;

        if (theLen > 3)
            Obj.value = theNewString;
    }
}
/***********************************************************************************************************************
 Ham FormatDate dinh dang hien thi kieu thoi gian "Ngay/Thang/Nam" trong khi nhap du lieu
 Ham nay chi cho phep nhap cac ki tu dang so, neu nhap bat ky ki tu nao khac se khong nhan
 Ham nay duoc goi trong su kien onKeyUp cua cac text_box. onKeyUp="FormatDate(this,'/')"
 Tham so:
 -txt_obj (Object): doi tuong text box nhap du lieu kieu thoi gian
 -separator_char (character): dau ngan cach giua ngay, thang va nam (Vi du: dau ":", dau "/", dau "|", dau "-", ...)

 ***********************************************************************************************************************/
function FormatDate(txt_obj, separator_char) {
    //Lay gia tri ma ASCII cua phim an
    var theKey = event.keyCode;
    var theLen = txt_obj.value.length;
    //Neu an phim BackSpace, Up, Down, Left, Right, Home, End thi khong xu ly
    if (theKey == 8 || theKey == 37 || theKey == 39 || theKey == 40) {
        return 1;
    }
    //Loai bo cac ki tu khong phai ky tu so (ke ca dau phan cach ngay thang nam)
    theStr = "";
    for (var i = 0; i < theLen; i++) {
        theChar = txt_obj.value.charCodeAt(i);
        if (theChar >= 48 & theChar <= 57) {
            theStr = theStr + txt_obj.value.substring(i, i + 1);
        }
    }
    theLen = theStr.length;
    // Xu ly tao format theo dang thoi gian dd/mm/yyyy
    if (theLen >= 4) {
        theDate = theStr.substring(0, 2);
        theMonth = theStr.substring(2, 4);
        theYear = theStr.substring(4);
        txt_obj.value = theDate + separator_char + theMonth + separator_char + theYear;
    } else {
        if (theLen >= 2) {
            theDate = theStr.substring(0, 2);
            theMonth = theStr.substring(2);
            txt_obj.value = theDate + separator_char + theMonth;
        } else {
            txt_obj.value = theStr;
        }
    }
    return 1;
}
/***********************************************************************************************************************
 Ham dinh dang hien thi kieu thoi gian hh:mm trong khi nhap du lieu
 Ham nay chi cho phep nhap cac ki tu dang so, neu nhap bat ky ki tu nao khac se khong nhan
 Ham nay duoc goi trong su kien onKeyUp cua cac text_box. onKeyUp = "format_hour(this,':')"
 Tham so:
 -txt_obj (Object): doi tuong text box nhap du lieu kieu thoi gian
 -separator_char (character): dau ngan cach giua gio va phut (Vi du: dau ":", dau "/", dau "|",...)
 ***********************************************************************************************************************/
function FormatHour(txt_obj, separator_char) {
    //Lay gia tri ma ASCII cua phim an
    var theKey = event.keyCode;
    //alert(event.shiftKey);
    var theLen = txt_obj.value.length;

    //Neu an phim BackSpace, Up, Down, Left, Right, Home, End thi khong xu ly
    if (theKey == 8 || theKey == 37 || theKey == 39 || theKey == 40) {
        return 1;
    }
    //Xu ly truong hop nguoi su dung nhap dau phan cach

    //Loai bo cac ki tu khong phai ky tu so (ke ca dau phan cach thoi gian gio va phut)
    theStr = "";
    for (var i = 0; i < theLen; i++) {
        theChar = txt_obj.value.charCodeAt(i);
        if (theChar >= 48 & theChar <= 57) {
            theStr = theStr + txt_obj.value.substring(i, i + 1);
        }
    }
    //alert(theStr);
    theLen = theStr.length
    // Xu ly tao format theo dang thoi gian hh:mm
    if (theLen >= 2) {
        theHour = theStr.substring(0, 2);
        if (theHour >= 24) {
            alert("Ban nhap gio khong dung (Gio phai nho hon 24)");
            txt_obj.value = '';
            return;
        }

        theMinute = theStr.substring(2);
        if (theMinute > 59) {
            alert("Ban nhap phut khong dung (Phut phai nho hon 60)");
            txt_obj.value = '';
            return;
        }

        txt_obj.value = theHour + separator_char + theMinute;
    } else {
        txt_obj.value = theStr;
    }
    //alert(theHour);
    //if(theMinute > 59){alert("Nhap lai phut (Phut phai nho hon 60)"); txt_obj.value = '';return;}
    return 1;
}
/***********************************************************************************************************************
 Ham bo sung cac so '0' vao chuoi ky tu kieu thoi gian neu NSD nhap thieu
 Ham nay duoc goi trong su kien onBlur cua cac text_box kieu thoi gian. onBlur="AdjustHour(this, ':')"
 Tham so:
 -txt_obj (Object): doi tuong text box nhap du lieu kieu thoi gian
 -separator_char (character): dau ngan cach giua gio va phut (Vi du: dau ":", dau "/", dau "|",...)
 ***********************************************************************************************************************/
function AdjustHour(txt_obj, separator_char) {
    // Xu ly tao format theo dang thoi gian hh:mm
    var theLen = txt_obj.value.length;
    theStr = txt_obj.value;
    if (theLen == 1) {
        theHour = '0' + theStr.substring(0, 1);
        theMinute = '00';
        txt_obj.value = theHour + separator_char + theMinute;
    }
    if (theLen == 2 || theLen == 3) {
        theHour = theStr.substring(0, 2);
        theMinute = '00';
        txt_obj.value = theHour + separator_char + theMinute;
    }
    if (theLen == 4) {
        theHour = theStr.substring(0, 2);
        theMinute = '0' + theStr.substring(3, 4);
        txt_obj.value = theHour + separator_char + theMinute;
    }
}
/* Ham kiem tra gio cap nhat co hop le hay khong
 Tham so:
 -txt_obj (Object): doi tuong text box nhap du lieu kieu thoi gian
 -separator_char (character): dau ngan cach giua gio va phut (Vi du: dau ":", dau "/", dau "|",...)
 Tra ve: True neu gio la hop le
 False neu gio khong hop le
 Luu y: Ham nay duoc goi trong ham verify(f) de kiem tra cac control cap nhat kieu thoi gian la gio, phut
 */
function IsHour(txt_obj, separator_char) {
    var strLen = txt_obj.length;
    var theStr = txt_obj.value;
    var tbeHour = "";
    var theMinute = "";
    //Neu chuoi thoi gian la trang
    if (strLen == 0) {
        return false;
    }
    separator_pos = theStr.indexOf(separator_char, 1);
    if (separator_pos == 0) {
        return false;
    }
    else {
        theHour = theStr.substr(0, separator_pos);
        theMinute = theStr.substr(separator_pos + 1, separator_pos + 3);
        //alert(tbeHour + '---' + theMinute);
    }
    if (parseInt(theHour) > 24 || parseInt(theMinute) > 60) {
        return false;
    }
    return true;
}

/********************************************************************************************************************
 //  Ham AdjustYearForDate()
 //  Xu ly nam khi NSD nhap vao 1 hoac 2 hoac 3 ky tu cho nam :
 //  VD: 11/12/2 -> 11/12/2002 ; 11/12/02 -> 11/12/2002 ; 11/12/002 -> 11/12/2002
 //      11/12/97 -> 11/12/1997 ; 11/12/997 -> 11/12/1997
 // Su dung : AdjustYearForDate(fMyForm.theDate)
 *****************************************************************************************************************/
function AdjustYearForDate(Obj) {
    if (Obj.value != '') {
        DateArr = Obj.value.split("/")
        iYear = parseInt(DateArr[2]);
        if (DateArr[2].length == 1)
            if (iYear > 0 && iYear <= 50)
                Obj.value = DateArr[0] + '/' + DateArr[1] + '/' + '200' + DateArr[2];
        if (DateArr[2].length == 2)
            if (iYear > 0 && iYear <= 50)
                Obj.value = DateArr[0] + '/' + DateArr[1] + '/' + '20' + DateArr[2];
            else
                Obj.value = DateArr[0] + '/' + DateArr[1] + '/' + '19' + DateArr[2];
        if (DateArr[2].length == 3)
            if (iYear > 0 && iYear <= 50)
                Obj.value = DateArr[0] + '/' + DateArr[1] + '/' + '20' + DateArr[2].substring(1, 3);
            else
                Obj.value = DateArr[0] + '/' + DateArr[1] + '/' + '19' + DateArr[2].substring(1, 3);
    }
}
/********************************************************************************************************************
 //  Ham GetFileName lay ten file trong mot duong dan file day du VD : "C:\project\filename.txt" lay ra "filename.txt"
 //  Khi goi : onchange="GetFileName(Obj,DesObj)"
 //  Trong do : Obj : doi tuong chua duong dan file dau du
 //             DesObj : Doi tuong nhan ket qua
 *****************************************************************************************************************/
function GetFileName(Obj, DesObj) {
    strFilePath = Obj.value;
    FilePathLen = strFilePath.length;
    var strFileName = '';
    var SepChar = '';
    for (var i = FilePathLen; i >= 0; i--) {
        SepChar = Obj.value.substring(strFilePath.length - 1, strFilePath.length);
        if (SepChar != "\\") {
            strFilePath = strFilePath.substring(0, strFilePath.length - 1);
            strFileName = SepChar + strFileName;
        }
        else {
            DesObj.value = strFileName;
            return 1;
        }

    }
    if (DesObj.value == '')
        DesObj.value = Obj.value;
}
//**********************************************************************************************************************
//Ham randomizeNumber() tra lai mot so ngau nhien
//**********************************************************************************************************************
function randomizeNumber() {
    today = new Date();
    jran = today.getTime();
    number = 1000000;
    ia = 9301;
    ic = 49297;
    im = 233280;
    jran = (jran * ia + ic) % im;
    return Math.ceil((jran / (im * 1.0)) * number);
}
//**********************************************************************************************************************
// Ham get_id_from_selected
// Ham lay ID tu danh sach Select duoc sinh ra tu ham GenerateSelectOption
//**********************************************************************************************************************
function get_id_from_selected(p_select_obj, p_hdn_obj) {
    p_hdn_obj.value = p_select_obj(p_select_obj.selectedIndex).id;
}
//**********************************************************************************************************************
// Ham chuyen gia tri tuong ung tu select sang cac doi tuong Text va Hidden*/
// Cac tham so :Select_obj: Select object Co san du lieu la cac option duoc sinh tu ham GennarateSelectOption trong Publicfunction
//              Text_obj: Text object lay gia tri tuong ung
//              hdn_obj: Dung de lay ID tuong ung tu Text
//**********************************************************************************************************************
function change_text_from_selected(p_select_obj, p_text_obj, p_hdn_obj) {
    p_text_obj.value = p_select_obj.value.toUpperCase();
    p_hdn_obj.value = p_select_obj(p_select_obj.selectedIndex).id;
}
//**********************************************************************************************************************
// Ham change_selected_from_text():
// Chuc nang:
//  - Thay doi gia tri (value) hien thoi cua mot SelectBox theo gia tri cua mot textbox
//  - Luu ID hien thoi cua mot SelectBox vao mot bien hidden
//Cac tham so:
//  p_select_obj: doi tuong SelectBox duoc thay doi gia tri hien thoi
//  p_text_obj: doi tuong textbox tuong ung
//  p_hdn_obj: doi tuong hidden de luu ID hien thoi cua SelectBox
//**********************************************************************************************************************
function change_selected_from_text(p_select_obj, p_text_obj, p_hdn_obj) {
    var found_flag = false;
    for (var i = 0; i < p_select_obj.length; i++) {
        if (p_select_obj(i).value.toUpperCase() == p_text_obj.value.toUpperCase()) {
            found_flag = true;
            break;
        }
    }
    if (found_flag) {
        p_select_obj(i).selected = true;
        p_hdn_obj.value = p_select_obj(p_select_obj.selectedIndex).id;
        //alert(p_select_obj(p_select_obj.selectedIndex).id);
        p_text_obj.value = p_text_obj.value.toUpperCase();
    } else {
        p_select_obj[0].selected = true;
        p_text_obj.value = "";
    }
}
//**********************************************************************************************************************
// Cac ham JavaScrip thao tac tren TreeView
//**********************************************************************************************************************

//**********************************************************************************************************************
// Ham node_name_onclick()
// Y nghia:
// - Neu o che do cho phep THEM/SUA/XOA thi hien thi man hinh cap nhat 1 doi tuong
// - Neu o che do KHONG cho phep THEM/SUA/XOA thi tra lai 1 chuoi gom ID,CODE,NAME cua doi tuong
// Tham so:
// - node: doi tuong ma NSD nhan CHUOT
//**********************************************************************************************************************
function node_name_onclick(node, select_parent) {
    if ((select_parent == 'false') && (_MODAL_DIALOG_MODE == 1)) {
        if (node.level == 0) {
            return;
        }
    }
    if (_MODAL_DIALOG_MODE == 1) {
        // Neu la che do khong cho phep Them, Xoa, Hieu chinh thi Tra lai mot chuoi chua cac thuoc tinh cua DOI TUONG
        if (_ALLOW_EDITING_IN_MODAL_DIALOG != 1) {
            return_value = node.id + _LIST_DELIMITOR + node.value + _LIST_DELIMITOR + node.innerText;
            window.returnValue = return_value;
            //alert(return_value);
            window.close();
            return;
        } else {
            //document.forms[0].fuseaction.value="DISPLAY_SINGLE_AREA";
            document.getElementById('hdn_item_id').value = node.id;
            document.forms[0].submit();
        }
    } else {
        document.getElementById('hdn_item_id').value = node.id;
        document.forms[0].submit();
    }
}
//**********************************************************************************************************************
// Ham select_node_of_tree()
// Y nghia:
// - Tra lai 1 chuoi gom ID,CODE,NAME cua doi tuong duoc chon bang nut RDIO hoac CHECK-BOX
//**********************************************************************************************************************
function select_nodes_of_tree(p_radio_obj, p_checkbox_obj) {
    //Xac dinh Radio dang chon
    var v_count;
    return_value = "";
    f = document.all;
    try {
        v_count = p_radio_obj.length;
        if (v_count) {
            for (i = 0; i < v_count; i++) {
                if (p_radio_obj[i].checked) {
                    return_value = f.str_obj[i].item_id + _LIST_DELIMITOR + f.str_obj[i].item_code + _LIST_DELIMITOR + f.str_obj[i].item_title;
                    break;
                }
            }
        } else {
            if (p_radio_obj.checked) {
                return_value = f.str_obj.item_id + _LIST_DELIMITOR + f.str_obj.item_code + _LIST_DELIMITOR + f.str_obj.item_title;
            }
        }
    } catch (e) {
        ;
    }
    if (return_value != "") {
        window.returnValue = return_value;
        window.close();
        return;
    }
    //Xac dinh check-box dang chon
    var v_count;
    return_value = "";
    f = document.all;
    try {
        v_count = p_checkbox_obj.length;
        if (v_count) {
            for (i = 0; i < v_count; i++) {
                if (p_checkbox_obj[i].checked) {
                    return_value = f.str_obj[i].item_id + _LIST_DELIMITOR + f.str_obj[i].item_code + _LIST_DELIMITOR + f.str_obj[i].item_title;
                    break;
                }
            }
        } else {
            if (p_checkbox_obj.checked) {
                return_value = f.str_obj.item_id + _LIST_DELIMITOR + f.str_obj.item_code + _LIST_DELIMITOR + f.str_obj.item_title;
            }
        }
    } catch (e) {
        ;
    }
    if (return_value != "") {
        window.returnValue = return_value;
        window.close();
        return;
    }
}
//**********************************************************************************************************************
// Ham delete_nodes_of_tree()
// Y nghia:
// - Xoa 1 hoac nhieu doi tuong duoc chon
//**********************************************************************************************************************
function delete_nodes_of_tree(p_radio_obj, p_checkbox_obj, p_hdn_list_item_id, p_delete_fuseaction) {
    //Xac dinh Radio dang chon
    var v_count;
    var v_current_radio_id = "";
    try {
        v_count = p_radio_obj.length;
        if (v_count) {
            for (i = 0; i < v_count; i++) {
                if (p_radio_obj[i].checked) {
                    v_current_radio_id = p_radio_obj[i].value;
                    break;
                }
            }
        } else {
            if (p_radio_obj.checked) {
                v_current_radio_id = p_radio_obj.value;
            }
        }
    } catch (e) {
        ;
    }
    //Kiem tra cac staff co trong unit
    var v_count;
    v_empty_staff = true;
    try {
        v_count = p_checkbox_obj.length;
        if (v_count) {
            for (i = 0; i < v_count; i++) {
                if (p_checkbox_obj[i].parent_id == v_current_radio_id) {
                    v_empty_staff = false;
                    break;
                }
            }
        } else {
            if (p_radio_obj.parent_id == v_current_radio_id) {
                v_empty_staff = false;
            }
        }
    } catch (e) {
        ;
    }
    if (v_empty_staff) {
        // Xoa doi tuong hien thoi
        btn_delete_onclick(p_radio_obj, p_hdn_list_item_id, p_delete_fuseaction);
    } else {
        //Xoa cac doi tuong duco chon
        btn_delete_onclick(p_checkbox_obj, p_hdn_list_item_id, p_delete_fuseaction);
    }
}
//**********************************************************************************************************************
// Ham add_node_of_treeview()
// Y nghia:
// - Hien thi man hinh them doi tuong
//**********************************************************************************************************************
function add_node_of_treeview(p_radio_obj, p_hdn_item_id_obj, p_fuseaction) {
    //Xac dinh Radio dang chon
    var v_count;
    var v_current_radio_id = ""
    try {
        v_count = p_radio_obj.length;
        if (v_count) {
            for (i = 0; i < v_count; i++) {
                if (p_radio_obj[i].checked) {
                    v_current_radio_id = p_radio_obj[i].value;
                    break;
                }
            }
        } else {
            if (p_radio_obj.checked) {
                v_current_radio_id = p_radio_obj.value;
            }
        }
    } catch (e) {
        ;
    }
    p_hdn_item_id_obj.value = v_current_radio_id;
    document.forms[0].fuseaction.value = p_fuseaction;
    document.forms[0].submit();
}
//**********************************************************************************************************************
// Ham node_image_onclick()
// Editer: Hoang Van Toan
// date edit: 11/10/2009
// Muc dich: duyet duoc tren moi trinh duyet
// Y nghia:
// - Xy ly khi NSD nhan vao nut "dong/mo" trong CAY
//**********************************************************************************************************************
function node_image_onclick(node, show_control, img_open_container_str, img_close_container_str, hdn_list_parent_obj) {
    //alert(hdn_list_parent_obj.value);
    //Neu nut (anh) la mot nut dang leaf_object thi khong co tuong tac
    if (node.type == '1' || node.type == 1) {
        return;
    }
    var nextDIV = node.nextSibling;
    while (nextDIV.nodeName != "DIV") {
        nextDIV = nextDIV.nextSibling;
    }
    if (document.all) {
        var nodeItem = node.childNodes.item(0).nodeName;
        var nodeScr = node.childNodes.item(0).src;
    } else {
        var nodeItem = node.childNodes.item(1).nodeName;
        var nodeScr = node.childNodes.item(1).src;
    }
    if (nextDIV.style.display == 'none') {
        if (node.childNodes.length > 0) {
            if (nodeItem == "IMG") {
                nodeScr = img_open_container_str;
                try {
                    select_parent_radio(document.getElementsByName('rad_item_id'), document.getElementsByName('chk_item_id'), node.id);
                } catch (e) {
                    ;
                }
            }
        }
        //Mo nut hie tai dong thoi them id cua nut do vao  chuoi id can lay
        nextDIV.style.display = 'block';
        //Kiem tra xem id cua nut vua click co trong chuoi chua, Neu co roi thi thoi khong add nua
        try {
            if (hdn_list_parent_obj.value.search(node.id) < 0) {
                hdn_list_parent_obj.value = hdn_list_parent_obj.value + node.id + _LIST_DELIMITOR;
            }
        } catch (e) {
            ;
        }
    } else {
        if (node.childNodes.length > 0) {
            if (nodeItem == "IMG") {
                nodeScr = img_close_container_str;
                try {
                    select_parent_radio(document.getElementsByName('rad_item_id'), document.getElementsByName('chk_item_id'), node.id);
                } catch (e) {
                    ;
                }
            }
        }
        //Neu dong nut do lai thi bo id khoi chuoi
        nextDIV.style.display = 'none';
        try {
            hdn_list_parent_obj.value = hdn_list_parent_obj.value.replace(node.id, '');
            hdn_list_parent_obj.value = hdn_list_parent_obj.value.replace(_LIST_DELIMITOR + _LIST_DELIMITOR, _LIST_DELIMITOR);
        } catch (e) {
            ;
        }
    }
}
//**********************************************************************************************************************
// Ham unselect_checbox()
// Y nghia:
//  1. Khi bam vao Radio button cua mot Unit se bo danh dau checkbox cua cac Staff khong thuoc Unit
//  2. Giu nguyen danh danh cac Staff thuoc Unit.
//**********************************************************************************************************************
function unselect_checbox(p_radio_obj, p_checkbox_obj) {
    try {
        var v_count;
        var v_parent_id = p_radio_obj.value;
        v_count = p_checkbox_obj.length;
        if (v_count) {
            for (i = 0; i < v_count; i++) {
                if (p_checkbox_obj[i].parent_id != v_parent_id) {
                    p_checkbox_obj[i].checked = "";
                }
            }
        } else {
            if (p_checkbox_obj.parent_id != v_parent_id) {
                p_checkbox_obj.checked = "";
            }
        }
    } catch (e) {
        ;
    }
}
//**********************************************************************************************************************
// Ham select_parent_radio()
// Y nghia:
// su dung trong truong hop danh dau Check box cua mot can bo se dan toi
//  1. Danh dau Radio button cua don vi chua can bo do.
//  2. Bo danh dau cua cac Check box cua cac can bo khong cung don vi voi can bo do
//**********************************************************************************************************************
function select_parent_radio(p_radio_obj, p_checkbox_obj, v_parent_id) {
    try {
        var v_count;
        v_count = p_radio_obj.length;
        if (v_count) {
            for (i = 0; i < v_count; i++) {
                if (p_radio_obj[i].value == v_parent_id) {
                    p_radio_obj[i].checked = true;
                    break;
                } else {
                    p_radio_obj[i].checked = false;
                }
            }
        } else {
            if (p_radio_obj.value == v_parent_id) {
                p_radio_obj.checked = true;
            } else {
                p_radio_obj.checked = false;
            }
        }
        //bo selected cua cac nhanh khac
        v_count = p_checkbox_obj.length;
        if (v_count) {
            for (i = 0; i < v_count; i++) {
                if (p_checkbox_obj[i].parent_id != v_parent_id) {
                    p_checkbox_obj[i].checked = "";
                }
            }
        } else {
            if (p_checkbox_obj.parent_id != v_parent_id) {
                p_checkbox_obj.checked = "";
            }
        }
    } catch (e) {
        ;
    }
}
//**********************************************************************************************************************
// Ham set_checkbox_checked()
//**********************************************************************************************************************
function set_checkbox_checked(p_radio_obj, p_checkbox_obj, p_list_item_id) {
    try {
        var v_count;
        var v_parent_id;
        v_count = p_checkbox_obj.length;
        v_parent_id = 0;
        if (v_count) {
            for (i = 0; i < v_count; i++) {
                if (p_list_item_id.search(p_checkbox_obj[i].value) >= 0) {
                    p_checkbox_obj[i].checked = "checked";
                    v_parent_id = p_checkbox_obj[i].parent_id;
                }
            }
        } else {
            if (p_list_item_id.search(p_checkbox_obj.value) >= 0) {
                p_checkbox_obj.checked = "checked";
                v_parent_id = p_checkbox_obj.parent_id;
            }
        }
        select_parent_radio(p_radio_obj, p_checkbox_obj, v_parent_id);
    } catch (e) {
        ;
    }
}
//**********************************************************************************************************************
// Ham is_node_empty()
// Ham cho phep xac dinh node duoc click (chon) co phai la node con cuoi cung hay khong
// Ham nay chi dung cho cay duoc tao ra tu ham _built_XML_tree()
// Input:
// Output: tra lai gia tri kieu logic
//  1. True: neu no la node con cuoi cung (Khong chua cac node con khac)
//  2. False: neus nos khong phai la node con cuoi cung (Chua cac node con khacs
//**********************************************************************************************************************
function is_node_empty(node) {
    if (node.type == '1') {
        return true;
    }
    var v_count;
    v_count = document.all.str_obj.length;
    if (v_count) {
        for (var i = 0; i < v_count; i++) {
            if (document.all.str_obj[i].parent_id == node.id) {
                return false;
            }
        }
    } else {
        return true;
    }
}
//**********************************************************************************************************************
// Ham show_modal_dialog_treeview_onclick()
// Ham thuc hien chuc nang
//  hien thi modal dialog danh sach cac doi tuong hinh cay
//      input:
//          p_text_name_obj:Doi tuong chua text name cua doi tuong
//          p_text_code_obj :Doi tuong chua text code cua doi tuong
//          p_hdn_obj: Doi tuong chua id tra ve cua doi tuong
//          p_hdn_owner_id: "Gia tri id" cua doi tuong dua vao de loc khong sinh ra chinh no va cac con cua no trong cay thu muc
//                          Neu gia tri nay <0 thi co nghia la cho hien thi tat ca
//          p_height: chieu cao cua modal dialog (gia tri truyenf vao co ca chu "pt")
//          p_width: chieu rong cua modal dialog (gia tri truyenf vao co ca chu "pt")
//          p_allow_editing_in_modal_dialog: Neu co truyen tham so nay thi trong cua so modaldialog hien thi danh sach DOI TUONG se khong co cac nut "Them", "Xoa"
//          p_allow_select: Neu co truyen tham so nay thi trong cua so hien thi danh sach DOI TUONG se co nut "Chon"
//      Output: Tra lai cac gia tri tuong ung cua doi tuong duoc click
//              1. ID cua doi tuong
//              2. Ma viet tat cua doi tuong
//              3. Ten doi tuong
//**********************************************************************************************************************
function show_modal_dialog_treeview_onclick(p_goto_url, p_fuseaction, p_text_name_obj, p_text_code_obj, p_hdn_obj, p_hdn_owner_id, p_height, p_width, p_allow_editing_in_modal_dialog, p_allow_select) {
    if (!p_height) p_height = "280pt";
    if (!p_width) p_width = "450pt";
    if (!p_allow_editing_in_modal_dialog) v_allow_editing_in_modal_dialog = 0; else v_allow_editing_in_modal_dialog = p_allow_editing_in_modal_dialog;
    v_url = _DSP_MODAL_DIALOG_URL_PATH;
    v_url = v_url + "?goto_url=" + p_goto_url + "&hdn_item_id=" + p_hdn_owner_id + "&fuseaction=" + p_fuseaction + "&modal_dialog_mode=1"
    if (v_allow_editing_in_modal_dialog == 1)
        v_url = v_url + "&allow_editing_in_modal_dialog=1";

    v_url = v_url + "&" + randomizeNumber();
    sRtn = showModalDialog(v_url, "", "dialogWidth=" + p_width + ";dialogHeight=" + p_height + ";dialogTop=80pt;status=no;scroll=no;");
    if (!sRtn) return;
    arr_value = sRtn.split(_LIST_DELIMITOR);
    p_hdn_obj.value = arr_value[0];
    p_text_code_obj.value = arr_value[1];
    p_text_name_obj.value = arr_value[2];
}
//**********************************************************************************************************************
// Ham show_modal_dialog_onclick()
// Ham thuc hien chuc nang
//  Ham nay duoc goi khi NSD muon hien thi cua so modaldialog de thuc hien chuc nang quan tri mot thong tin danh muc dang BANG (khong phai dang CAY)
//  -p_goto_url: dia chi cua file index.php tuong ung voi dach muc can quan tri
//  -p_fuseaction: ten fuseaction ngam dinh
//  -p_select_obj: ten bien selectbox chua TEN danh muc
//  -p_text_obj: ten bien textbox chua CODE cua doi tuong
//  -p_hdn_obj: ten bien hidden chua ID cua doi tuong
//**********************************************************************************************************************
function show_modal_dialog_onclick(p_goto_url, p_fuseaction, p_select_obj, p_text_obj, p_hdn_obj) {
    v_url = _DSP_MODAL_DIALOG_URL_PATH;
    v_url = v_url + "?goto_url=" + p_goto_url + "&hdn_item_id=0" + "&fuseaction=" + p_fuseaction + "&modal_dialog_mode=1" + "&" + randomizeNumber();
    sRtn = showModalDialog(v_url, "", "dialogWidth=420pt;dialogHeight=370pt;dialogTop=80pt;status=no;scroll=no;");
    if (!sRtn) return;
    arr_value = sRtn.split(_LIST_DELIMITOR);
    select_obj_length = p_select_obj.length;
    p_select_obj.options[select_obj_length] = new Option(arr_value[2]);
    p_select_obj.options[select_obj_length].id = arr_value[0];
    p_select_obj.options[select_obj_length].value = arr_value[1];
    p_select_obj.options[select_obj_length].name = arr_value[2];
    p_select_obj.options[select_obj_length].selected = true;
    p_text_obj.value = arr_value[1];
    p_hdn_obj.value = arr_value[0];
}

function show_modal_dialog_change_personal_info(p_goto_url, p_staff_id) {
    v_url = _DSP_MODAL_DIALOG_URL_PATH;
    v_url = v_url + "?goto_url=" + p_goto_url + "&fuseaction=DISPLAY_DETAIL_USER&hdn_item_id=" + p_staff_id + "&modal_dialog_mode=1" + "&" + randomizeNumber();
    //alert(v_url);
    sRtn = showModalDialog(v_url, "", "dialogWidth=420pt;dialogHeight=300pt;dialogTop=80pt;status=no;scroll=no;");
}
//**********************************************************************************************************************
// function replace(string,text,by)
// Thay the ky tu trong mot chuoi
//**********************************************************************************************************************
function replace(string, text, by) {
    var strLength = string.length, txtLength = text.length;
    if ((strLength == 0) || (txtLength == 0)) return string;

    var i = string.indexOf(text);
    if ((!i) && (text != string.substring(0, txtLength))) return string;
    if (i == -1) return string;

    var newstr = string.substring(0, i) + by;

    if (i + txtLength < strLength)
        newstr += replace(string.substring(i + txtLength, strLength), text, by);

    return newstr;
}

//Ajax
function $(id) {
    return document.getElementById(id);
}
function createXMLHttpRequestObject() {
    var xmlHttp;
    if (window.ActiveXObject) {
        try {
            xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
        } catch (e) {
            xmlHttp = false;
        }
    } else {
        try {
            xmlHttp = new XMLHttpRequest();
        } catch (e) {
            xmlHttp = false;
        }
    }
    if (!xmlHttp)
        alert("Error creating the XMLHttpRequest object.");
    else
        return xmlHttp;
}
function trim(TRIM_VALUE) {
    if (TRIM_VALUE.length < 1) {
        return "";
    }
    TRIM_VALUE = rtrim(TRIM_VALUE);
    TRIM_VALUE = ltrim(TRIM_VALUE);
    if (TRIM_VALUE == "") {
        return "";
    }
    else {
        return TRIM_VALUE;
    }
} //End Function

function ltrim(VALUE) {
    var w_space = String.fromCharCode(32);
    var v_length = VALUE.length;
    var strTemp = "";
    if (v_length < 0) {
        return "";
    }
    var iTemp = v_length - 1;

    while (iTemp > -1) {
        if (VALUE.charAt(iTemp) == w_space) {
        }
        else {
            strTemp = VALUE.substring(0, iTemp + 1);
            break;
        }
        iTemp = iTemp - 1;

    } //End While
    return strTemp;

} //End Function

function rtrim(VALUE) {
    var w_space = String.fromCharCode(32);
    if (v_length < 1) {
        return "";
    }
    var v_length = VALUE.length;
    var strTemp = "";
    var iTemp = 0;
    while (iTemp < v_length) {
        if (VALUE.charAt(iTemp) == w_space) {
        }
        else {
            strTemp = VALUE.substring(iTemp, v_length);
            break;
        }
        iTemp = iTemp + 1;
    } //End While
    return strTemp;
} //End Function
///////////////////////////////////////////////////////////////
function _select_all_multiple_checkbox(p_chk_obj, obj, rdo_id, option) {
    try {
        //current_chk_obj = p_chk_obj[0].getAttribute("xml_tag_in_db_name");
        for (i = 0; i < p_chk_obj.length; i++) {
            if (rdo_id.checked && option == 0) {
                current_chk_obj = p_chk_obj[i].getAttribute("xml_tag_in_db_name");
                if (current_chk_obj == obj) {
                    p_chk_obj[i].checked = true;
                }
            } else {
                current_chk_obj = p_chk_obj[i].getAttribute("xml_tag_in_db_name");
                if (current_chk_obj == obj) {
                    p_chk_obj[i].checked = false;
                }
            }
        }
    } catch (e) {
        ;
    }
}
///////////////////////////////////////////////////////////////
/*
 Nguoi tao: Thainv
 Ngay tao: 06/10/2009
 Y nghia: Ham thuc hien action mot url: p_url
 chay chuan tren IE va Firefox
 Tham so:
 + p_url : Gia tri URL can thuc hien
 */
function actionUrl(p_url) {
    document.getElementsByTagName('form')[0].action = p_url;
    document.getElementsByTagName('form')[0].submit();
}
/*
 Creater: 
 Date: 03/02/2009
 Idea: Ham nay thuc thi khi NSD chon vao mot trang tren SelectBox (Danh sach trang tren mh danh sach)
 Parameters:
 + sel_obj : Gia tri trang hien thoi
 + pAction : Action can thuc hien (Index)
 */
function page_onchange(sel_obj, pAction) {
    _save_xml_tag_and_value_list(document.forms[0], document.getElementById('hdn_filter_xml_tag_list'), document.getElementById('hdn_filter_xml_value_list'), true);
    document.getElementById('hdn_current_page').value = sel_obj.value;
    actionUrl(pAction);
}
//Ham thay thuc thi khi NSD muon thay doi so dong quy dinh tren mot trang
function page_record_number_onchange(sel_obj, pAction) {
    _save_xml_tag_and_value_list(document.forms[0], document.getElementById('hdn_filter_xml_tag_list'), document.getElementById('hdn_filter_xml_value_list'), true);
    document.getElementById('hdn_current_page').value = 1;
    document.getElementById('hdn_record_number_page').value = sel_obj.value;
    actionUrl(pAction);
}
// Menu Control
function at_display(x) {
    var win = window.open();
    for (var i in x) win.document.write(i + ' = ' + x[i] + '<br>');
}
// ----- Show Aux -----
function at_show_aux(parent, child) {
    var p = document.getElementById(parent);
    var c = document.getElementById(child);

    var top = (c["at_position"] == "y") ? p.offsetHeight : 0;
    var left = (c["at_position"] == "x") ? p.offsetWidth + 2 : 0;

    for (; p; p = p.offsetParent) {
        top += p.offsetTop;
        left += p.offsetLeft;
    }
    c.style.position = "absolute";
    c.style.top = top + 'px';
    c.style.left = left + 'px';
    c.style.visibility = "visible";
}
// ----- Show -----
function at_show() {
    var p = document.getElementById(this["at_parent"]);
    var c = document.getElementById(this["at_child"]);
    at_show_aux(p.id, c.id);
    clearTimeout(c["at_timeout"]);
}
// ----- Hide -----
function at_hide() {
    var c = document.getElementById(this["at_child"]);
    c["at_timeout"] = setTimeout("document.getElementById('" + c.id + "').style.visibility = 'hidden'", 10);
}
// ----- Click -----
function at_click() {
    var p = document.getElementById(this["at_parent"]);
    var c = document.getElementById(this["at_child"]);

    if (c.style.visibility != "visible")
        at_show_aux(p.id, c.id);
    else c.style.visibility = "hidden";

    return false;
}
// ----- Attach -----

// PARAMETERS:
// parent   - id of visible html element
// child    - id of invisible html element that will be dropdowned
// showtype - "click" = you should click the parent to show/hide the child
//            "hover" = you should place the mouse over the parent to show
//                      the child
// position - "x" = the child is displayed to the right of the parent
//            "y" = the child is displayed below the parent
// cursor   - Omit to use default cursor or check any CSS manual for possible
//            values of this field
function at_attach(parent, child, showtype, position, cursor) {
    var p = document.getElementById(parent);
    var c = document.getElementById(child);

    p["at_parent"] = p.id;
    c["at_parent"] = p.id;
    p["at_child"] = c.id;
    c["at_child"] = c.id;
    p["at_position"] = position;
    c["at_position"] = position;

    c.style.position = "absolute";
    c.style.visibility = "hidden";

    if (cursor != undefined) p.style.cursor = cursor;

    switch (showtype) {
        case "click":
            p.onclick = at_click;
            p.onmouseout = at_hide;
            c.onmouseover = at_show;
            c.onmouseout = at_hide;
            break;
        case "hover":
            p.onmouseover = at_show;
            p.onmouseout = at_hide;
            c.onmouseover = at_show;
            c.onmouseout = at_hide;
            break;
    }
}
function list_have_date(the_list, the_element, the_separator) {
    if (the_list == "") return 0;
    arr_value = the_list.split(the_separator);
    for (i = 0; i < arr_value.length; i++) {
        if (date_compare(arr_value[i], the_element) == 0) {
            return 1;
        }
    }
    return 0;
}
/*
 Creater: Toanhv
 date: 24/10/2009
 */
function DatePrompt(v) {
    var v1 = v.value;
    var o1 = '';
    var monthdays = 0;
    var r1 = true;

    var vietDateExpr = new RegExp('^[0-3]?[0-9]/[0-1]?[0-9]/[0-9][0-9][0-9][0-9]$');
    var dateexpr1 = new RegExp('^[0-3][0-9][0-1][0-9]$');
    var dateexpr2 = new RegExp('^[0-3][0-9][0-1][0-9][0-9][0-9]$');
    var dateexpr3 = new RegExp('^[0-3]?[0-9]/[0-1]?[0-9]/[0-9][0-9]$');
    var dateexpr4 = new RegExp('^[0-3]?[0-9]/[0-1]?[0-9]$');

    if (v1.match(vietDateExpr))
        o1 = v1;
    else {
        if (v1.match(dateexpr1)) {
            var d = new Date();
            o1 = v1.substring(0, 2) + '/' + v1.substring(2, 4) + '/' + d.getFullYear();
        }
        else {
            if (v1.match(dateexpr2))
                o1 = v1.substring(0, 2) + '/' + v1.substring(2, 4) + '/20' + v1.substring(4, 6);
            else {
                if (v1.match(dateexpr3)) {
                    var i1 = v1.lastIndexOf('/');
                    o1 = v1.substring(0, i1) + '/20' + v1.substring(i1 + 1, v1.length);
                }
                else {
                    if (v1.match(dateexpr4)) {
                        var d = new Date();
                        o1 = v1 + '/' + d.getFullYear();
                    }
                }
            }
        }
    }
    if (o1 == '' && v1 != '') {
        alert('Ngay thang nam khong cung dinh dang!');
        v.focus();
        r1 = false;
    }
    // Now check date validity
    strDate1 = o1.split("/");
    if (strDate1[0].substring(0, 1) == '0') {
        strDate1[0] = strDate1[0].substring(1, 1);
    }
    if (strDate1[1].substring(0, 1) == '0') {
        strDate1[1] = strDate1[1].substring(1, 1);
    }
    if (parseInt(strDate1[1]) < 1 || parseInt(strDate1[1]) > 12) {
        alert('Thang: 1-12');
        r1 = false;
    }
    switch (parseInt(strDate1[1])) {
        case 2:
            if (parseInt(strDate1[2]) % 4 == 0)
                monthdays = 29;
            else
                monthdays = 28;
            break;
        case 4:
        case 6:
        case 9:
        case 11:
            monthdays = 30;
            break;
        default:
            monthdays = 31;
    }
    if (parseInt(strDate1[0]) < 1 || parseInt(strDate1[0]) > monthdays) {
        alert('Ngay: 1-' + monthdays);
        r1 = false;
    }
    if (r1)
        return o1;
    else
        return v1;
}

function searchFull(objName1, objName2, objView) {
    objView.style.display = "block";
    objName1.style.display = "none";
    objName2.style.display = "block";
}
function turnOff(objName1, objName2, objView) {
    objView.style.display = "none";
    objName1.style.display = "block";
    objName2.style.display = "none";
}
function searchForText(event, ObjSearch) {
    if (event.keyCode == 13) {
        document.getElementById('hdn_current_page').value = 1;
        ObjSearch.setAttribute("onclick", actionUrl(''));
    }
}
/*
 Creater: phongtd
 Date: 13/05/2010
 Idea: Ham nay thuc thi khi NSD chon vao mot trang tren danh sach cac trang dang hien thi
 Parameters: pCurrentPage : Gia tri trang hien thoi
 */
function break_page(pCurrentPage, pUrl) {
    _save_xml_tag_and_value_list(document.forms[0], document.getElementById('hdn_filter_xml_tag_list'), document.getElementById('hdn_filter_xml_value_list'), true);
    document.getElementById('hdn_current_page').value = pCurrentPage;
    //document.getElementsByTagName('form')[0].submit();
    actionUrl(pUrl);
}
/*
 Creater: phongtd
 Date: 14/05/2010
 Idea: Ham nay thuc thi khi NSD muon "Previous" hoac "Next" trang
 Parameters: psPreviousNextPage : Gia tri la "Previous" hoac "Next"
 */

function previous_next_page(psPreviousNextPage, pUrl) {
    if (psPreviousNextPage == "Previous") {
        _save_xml_tag_and_value_list(document.forms[0], document.getElementById('hdn_filter_xml_tag_list'), document.getElementById('hdn_filter_xml_value_list'), true);
        document.getElementById('hdn_current_page').value = parseInt(document.getElementById('hdn_current_page').value) - 1;
        //document.getElementsByTagName('form')[0].submit();
        actionUrl(pUrl);
    }
    if (psPreviousNextPage == "Next") {
        _save_xml_tag_and_value_list(document.forms[0], document.getElementById('hdn_filter_xml_tag_list'), document.getElementById('hdn_filter_xml_value_list'), true);
        document.getElementById('hdn_current_page').value = parseInt(document.getElementById('hdn_current_page').value) + 1;
        //document.getElementsByTagName('form')[0].submit();
        actionUrl(pUrl);
    }
}
//----------------
//set hidden man hinh danh sach van ban den
function set_hidden(obj, chk_obj, hdn_obj, value) {
    hdn_obj.value = "";
    for (i = 0; i < chk_obj.length; i++) {
        if (chk_obj[i].value == value && chk_obj[i].disabled == false) {
            chk_obj[i].checked = true;
            hdn_obj.value = value;
            $('td').parent().removeClass('selected');
            $(obj).parent().addClass('selected');
        } else {
            chk_obj[i].checked = false;
        }
    }
}
//set hidden man hinh danh sach tim kiem van ban den
function set_hidden_search(obj, hdn_obj, value) {
    //hdn_obj.value ="";
    hdn_obj.value = value;
    $('td').parent().removeClass('selected');
    $(obj).parent().addClass('selected');
}

/**
 * http://www.openjs.com/scripts/events/keyboard_shortcuts/
 * Version : 2.01.B
 * By Binny V A
 * License : BSD
 */
shortcut = {
    'all_shortcuts': {},//All the shortcuts are stored in this array
    'add': function (shortcut_combination, callback, opt) {
        //Provide a set of default options
        var default_options = {
            'type': 'keydown',
            'propagate': false,
            'disable_in_input': false,
            'target': document,
            'keycode': false
        }
        if (!opt) opt = default_options;
        else {
            for (var dfo in default_options) {
                if (typeof opt[dfo] == 'undefined') opt[dfo] = default_options[dfo];
            }
        }

        var ele = opt.target;
        if (typeof opt.target == 'string') ele = document.getElementById(opt.target);
        var ths = this;
        shortcut_combination = shortcut_combination.toLowerCase();

        //The function to be called at keypress
        var func = function (e) {
            e = e || window.event;

            if (opt['disable_in_input']) { //Don't enable shortcut keys in Input, Textarea fields
                var element;
                if (e.target) element = e.target;
                else if (e.srcElement) element = e.srcElement;
                if (element.nodeType == 3) element = element.parentNode;

                if (element.tagName == 'INPUT' || element.tagName == 'TEXTAREA') return;
            }

            //Find Which key is pressed
            if (e.keyCode) code = e.keyCode;
            else if (e.which) code = e.which;
            var character = String.fromCharCode(code).toLowerCase();

            if (code == 188) character = ","; //If the user presses , when the type is onkeydown
            if (code == 190) character = "."; //If the user presses , when the type is onkeydown

            var keys = shortcut_combination.split("+");
            //Key Pressed - counts the number of valid keypresses - if it is same as the number of keys, the shortcut function is invoked
            var kp = 0;

            //Work around for stupid Shift key bug created by using lowercase - as a result the shift+num combination was broken
            var shift_nums = {
                "`": "~",
                "1": "!",
                "2": "@",
                "3": "#",
                "4": "$",
                "5": "%",
                "6": "^",
                "7": "&",
                "8": "*",
                "9": "(",
                "0": ")",
                "-": "_",
                "=": "+",
                ";": ":",
                "'": "\"",
                ",": "<",
                ".": ">",
                "/": "?",
                "\\": "|"
            }
            //Special Keys - and their codes
            var special_keys = {
                'esc': 27,
                'escape': 27,
                'tab': 9,
                'space': 32,
                'return': 13,
                'enter': 13,
                'backspace': 8,

                'scrolllock': 145,
                'scroll_lock': 145,
                'scroll': 145,
                'capslock': 20,
                'caps_lock': 20,
                'caps': 20,
                'numlock': 144,
                'num_lock': 144,
                'num': 144,

                'pause': 19,
                'break': 19,

                'insert': 45,
                'home': 36,
                'delete': 46,
                'end': 35,

                'pageup': 33,
                'page_up': 33,
                'pu': 33,

                'pagedown': 34,
                'page_down': 34,
                'pd': 34,

                'left': 37,
                'up': 38,
                'right': 39,
                'down': 40,

                'f1': 112,
                'f2': 113,
                'f3': 114,
                'f4': 115,
                'f5': 116,
                'f6': 117,
                'f7': 118,
                'f8': 119,
                'f9': 120,
                'f10': 121,
                'f11': 122,
                'f12': 123
            }

            var modifiers = {
                shift: {
                    wanted: false,
                    pressed: false
                },
                ctrl: {
                    wanted: false,
                    pressed: false
                },
                alt: {
                    wanted: false,
                    pressed: false
                },
                meta: {
                    wanted: false,
                    pressed: false
                }   //Meta is Mac specific
            };

            if (e.ctrlKey)   modifiers.ctrl.pressed = true;
            if (e.shiftKey)  modifiers.shift.pressed = true;
            if (e.altKey)    modifiers.alt.pressed = true;
            if (e.metaKey)   modifiers.meta.pressed = true;

            for (var i = 0; k = keys[i], i < keys.length; i++) {
                //Modifiers
                if (k == 'ctrl' || k == 'control') {
                    kp++;
                    modifiers.ctrl.wanted = true;

                } else if (k == 'shift') {
                    kp++;
                    modifiers.shift.wanted = true;

                } else if (k == 'alt') {
                    kp++;
                    modifiers.alt.wanted = true;
                } else if (k == 'meta') {
                    kp++;
                    modifiers.meta.wanted = true;
                } else if (k.length > 1) { //If it is a special key
                    if (special_keys[k] == code) kp++;

                } else if (opt['keycode']) {
                    if (opt['keycode'] == code) kp++;

                } else { //The special keys did not match
                    if (character == k) kp++;
                    else {
                        if (shift_nums[character] && e.shiftKey) { //Stupid Shift key bug created by using lowercase
                            character = shift_nums[character];
                            if (character == k) kp++;
                        }
                    }
                }
            }

            if (kp == keys.length &&
                modifiers.ctrl.pressed == modifiers.ctrl.wanted &&
                modifiers.shift.pressed == modifiers.shift.wanted &&
                modifiers.alt.pressed == modifiers.alt.wanted &&
                modifiers.meta.pressed == modifiers.meta.wanted) {
                callback(e);

                if (!opt['propagate']) { //Stop the event
                    //e.cancelBubble is supported by IE - this will kill the bubbling process.
                    e.cancelBubble = true;
                    e.returnValue = false;

                    //e.stopPropagation works in Firefox.
                    if (e.stopPropagation) {
                        e.stopPropagation();
                        e.preventDefault();
                    }
                    return false;
                }
            }
        }
        this.all_shortcuts[shortcut_combination] = {
            'callback': func,
            'target': ele,
            'event': opt['type']
        };
        //Attach the function with the event
        if (ele.addEventListener) ele.addEventListener(opt['type'], func, false);
        else if (ele.attachEvent) ele.attachEvent('on' + opt['type'], func);
        else ele['on' + opt['type']] = func;
    },

    //Remove the shortcut - just specify the shortcut and I will remove the binding
    'remove': function (shortcut_combination) {
        shortcut_combination = shortcut_combination.toLowerCase();
        var binding = this.all_shortcuts[shortcut_combination];
        delete(this.all_shortcuts[shortcut_combination])
        if (!binding) return;
        var type = binding['event'];
        var ele = binding['target'];
        var callback = binding['callback'];

        if (ele.detachEvent) ele.detachEvent('on' + type, callback);
        else if (ele.removeEventListener) ele.removeEventListener(type, callback, false);
        else ele['on' + type] = false;
    }
}
//Ham checkbox all
function checkbox_all_item_id(p_chk_obj) {
    //remove class cua tat ca cac tr trong table
    $('tr').removeClass('selected');
    try {
        v_count = p_chk_obj.length;
        if (v_count) {
            if (document.forms[0].chk_all_item_id.checked == true) {
                for (i = 0; i <= v_count; i++) {
                    if (p_chk_obj[i].disabled == false && (p_chk_obj[i].value != 0)) {
                        p_chk_obj[i].checked = 'checked';
                        $(p_chk_obj[i]).parent().parent().addClass('selected');
                    }
                }
            } else {
                for (i = 0; i < p_chk_obj.length; i++) {
                    p_chk_obj[i].checked = '';
                }
            }
        } else {
            if (document.forms[0].chk_all_item_id.checked == true) {
                if (p_chk_obj.disabled == false) {
                    p_chk_obj.checked = 'checked';
                    $(p_chk_obj).parent().parent().addClass('selected');
                }
            } else {
                p_chk_obj.checked = '';
            }
        }

    } catch (e) {
        ;
    }
}
//Ham thuc hien to mau row khi nguoi su dung bam vao nut checkbox tren row
//obj   Checkbox duoc chon
function selectrow(obj) {
    if (obj.checked) 
        $(obj).parent().parent().addClass('selected');
    else
        $(obj).parent().parent().removeClass('selected');
}
//  Ham item_onclick duoc goi khi NSD click vao 1 dong trong danh sach
//  p_item_value: chua ID cua doi tuong can hieu chinh
function item_onclick(p_item_value, p_url) {
    row_onclick(document.getElementById('hdn_object_id'), p_item_value, p_url);
}
/*
 Creater ; 
 Date : 16/06/2010
 Idea : Tao ham khoi tao bien luu trong Cookie
 Paras :
 + name  : Ten Cookie
 + value : Gia tri luu trong Cookie
 + expires : Thoi gian expires
 + path : Duong dan luu Cookie
 + domain : Ten mien
 + secure : Chinh sach
 */
function Set_Cookie(name, value, expires, path, domain, secure) {
    // set time, it's in milliseconds
    var today = new Date();
    today.setTime(today.getTime());
    /*
     if the expires variable is set, make the correct
     expires time, the current script below will set
     it for x number of days, to make it for hours,
     delete * 24, for minutes, delete * 60 * 24
     */
    if (expires) {
        expires = expires * 1000 * 60 * 60 * 24;
    }

    var expires_date = new Date(today.getTime() + (expires));
    //
    document.cookie = name + "=" + escape(value) +
    ( ( expires ) ? ";expires=" + expires_date.toGMTString() : "" ) +
    ( ( path ) ? ";path=" + path : "" ) +
    ( ( domain ) ? ";domain=" + domain : "" ) +
    ( ( secure ) ? ";secure" : "" );
}

/*
 Creater ; 
 Date : 16/06/2010
 Idea : Tao ham khoi tao bien luu trong Cookie
 Paras :
 + check_name  : Ten Cookie can lay gia tri
 */

// this fixes an issue with the old method, ambiguous values
// with this test document.cookie.indexOf( name + "=" );
function Get_Cookie(check_name) {

    // first we'll split this cookie up into name/value pairs
    // note: document.cookie only returns name=value, not the other components
    var a_all_cookies = document.cookie.split(';');
    var a_temp_cookie = '';
    var cookie_name = '';
    var cookie_value = '';
    var b_cookie_found = false; // set boolean t/f default f

    for (i = 0; i < a_all_cookies.length; i++) {
        // now we'll split apart each name=value pair
        a_temp_cookie = a_all_cookies[i].split('=');

        // and trim left/right whitespace while we're at it
        cookie_name = a_temp_cookie[0].replace(/^\s+|\s+$/g, '');

        // if the extracted name matches passed check_name
        if (cookie_name == check_name) {
            b_cookie_found = true;
            // we need to handle case where cookie has no value but exists (no = sign, that is):
            if (a_temp_cookie.length > 1) {
                cookie_value = unescape(a_temp_cookie[1].replace(/^\s+|\s+$/g, ''));
            }
            // note that in cases where cookie is initialized but no value, null is returned
            return cookie_value;
            break;
        }
        a_temp_cookie = null;
        cookie_name = '';
    }

    //
    if (!b_cookie_found) {
        return null;
    }
}
//---------------------------------------------------
//ham thuc hien khi nhap du lieu vao truong ngay thang
function DateOnkeyup(d, e) {
    var pK = (e.which) ? e.which : window.event.keyCode;
    if (pK == 8) {
        return;
    }
    var dt = d.value;
    var da = dt.split('/');
    for (var a = 0; a < da.length; a++) {
        if (da[a] != +da[a]) da[a] = da[a].substr(0, da[a].length - 1);
    }
    if (da[0] > 31) {
        da[1] = da[0].substr(da[0].length - 1, 1);
        da[0] = '0' + da[0].substr(0, da[0].length - 1);
    }
    if (da[1] > 12) {
        da[2] = da[1].substr(da[1].length - 1, 1);
        da[1] = '0' + da[1].substr(0, da[1].length - 1);
    }
    if (da[2] > 9999) da[2] = da[2].substr(0, da[2].length - 1);
    if (da[0] > 28 & da[1] == 02 & da[2] > 999) {
        if ((da[2] % 4) == 0 & (da[2] % 100) == 0) {
            da[0] = 29;
        }
        else {
            da[0] = 28;
        }
    }
    if (da[0] > 30 & (da[1] == 04 || da[1] == 06 || da[1] == 09 || da[1] == 11 )) {
        da[0] = 30;
    }
    dt = da.join('/');
    if (dt.length == 2 || dt.length == 5) dt += '/';
    d.value = dt;
}
function isEdit(keycode, str) {
    if ((keycode >= 48 && keycode <= 57 || keycode >= 96) && keycode <= 105 || keycode == 111 || keycode == 191) {
        return true;
    } else {
        //alert("Chi su dung so va dau '/ ' de phan cach")
        str.value = '';
        return true;
    }
}
function btn_register_onclick(p_checkbox_obj, p_hidden_obj, p_url) {
    if (!checkbox_value_to_list(p_checkbox_obj, ",")) {
        alert("Chua co doi tuong nao duoc chon");
    }
    else {
        if (confirm('Ban thuc su muon dang ky phat hanh nhung doi tuong da chon?')) {
            p_hidden_obj.value = checkbox_value_to_list(p_checkbox_obj, ","); //Xac dinh cac phan tu duoc checked va luu vao bien hidden p_hidden_obj
            actionUrl(p_url);
        }
    }
}
//Lay ngay tiep theo cua ngay trong elTerget.value
function Next_Date(p_date) {
    if (isdate(p_date)) {
        var theDate, strSeparator, arr, day, month, year;
        strSeparator = "";
        theDate = p_date;
        if (theDate.indexOf("/") != -1) strSeparator = "/";
        if (theDate.indexOf("-") != -1) strSeparator = "-";
        if (theDate.indexOf(".") != -1) strSeparator = ".";
        if (strSeparator != "") {
            arr = theDate.split(strSeparator);
            day = new Number(arr[0]) + 1;
            month = new Number(arr[1]);
            year = new Number(arr[2]);
            if (day > 28) {
                if (((month == 1 || month == 3 || month == 5 || month == 7 || month == 8 || month == 10 || month == 12) && (day > 31))
                    || ((month == 4 || month == 6 || month == 9 || month == 11) && (day > 30)) || (month == 2 && year % 4 != 0) || (month == 2 && year % 4 == 0 && day > 29)) {
                    day = 1;
                    month = month + 1;
                }
                if (month > 12) {
                    year = year + 1;
                    month = 1;
                }
            }
            return day + strSeparator + month + strSeparator + year;
        }
    }
}
//Tinh han xu ly VB
function appointed_date(v_implementation_date, p_limit_date, p_appointed_date) {
    if (p_limit_date.value != '') {
        if (!isnum(p_limit_date.value)) {
            if (p_limit_date.message)
                alert(p_limit_date.message);
            else
                alert('Bạn phải nhập vào số nguyên dương!');
            p_limit_date.value = '';
            return;
        }
        var count = p_limit_date.value;
        //Lay thong tin Nam hien thoi
        var d = new Date();
        var p_year = d.getFullYear();
        var v_list_day_off_of_year = _LIST_DAY_OFF_OF_YEAR.split(",");
        var v_list_day = _LIST_WORK_DAY_OF_WEEK;
        var v_list_luner_date = "";
        var v_increase_and_decrease_day = parseInt(_INCREASE_AND_DECREASE_DAY);
        var v_date, v_temp_date;
        var v_next_date = "";
        //Ngay thuc hien
        var v_input_date = v_implementation_date;
        if (!isdate(v_input_date)) {
            return;
        } else {
            var arr_input_date = v_input_date.split("/");
            v_input_date = arr_input_date[0] * 1 + "/" + arr_input_date[1] * 1 + "/" + arr_input_date[2] * 1;
        }
        for (var i = 0; i < v_list_day_off_of_year.length; i++) {
            v_date = v_list_day_off_of_year[i].split("/");
            if (v_date[0] == "-") {
                v_list_luner_date = list_append(v_list_luner_date, v_date[1] + "/" + v_date[2] + "/" + p_year, ",");
            } else {
                v_temp_date = Solar2Lunar(v_date[1] + "/" + v_date[2] + "/" + p_year);
                v_list_luner_date = list_append(v_list_luner_date, v_temp_date, ",");
            }
        }
        var i = 0;
        v_next_date = v_input_date;
        //Tinh tong so ngay tru ngay duoc nghi ra
        while ((i < count - v_increase_and_decrease_day)) {
            if ((list_have_date(v_list_luner_date, Solar2Lunar(v_next_date), ",") != 1) && (Solar2DayofWeek(v_next_date) != 7) && (Solar2DayofWeek(v_next_date) != 8)) {
                i++;
                v_next_date = Next_Date(v_next_date);
            } else {
                v_next_date = Next_Date(v_next_date);
            }
        }
        //Neu gap ngay thu 7 hoac CN thi bo qua
        while ((list_have_element(v_list_day, Solar2DayofWeek(v_next_date), ",") < 0)) {
            v_next_date = Next_Date(v_next_date);
        }
        p_appointed_date.value = v_next_date;
    }
}
//Tinh han xu ly ngay gio
function appointed_datetime(v_implementation_date, v_implementation_time, p_limit_date, p_limit_time, p_appointed_date, p_appointed_time) {
    if ((p_limit_date.value != '') || (p_limit_time.value != '')) {
        var count = p_limit_date.value;
        if (!isnum(p_limit_date.value)) {
            count = 0;
        }
        //----------------------------------------------------
        var v_list_time_off_date = _LIST_WORK_TIME_OF_DAY.split(",");
        var v_begin_time = Number(v_implementation_time);
        if (v_begin_time < v_list_time_off_date[0]) {
            v_begin_time = v_list_time_off_date[0];
        } else {
            var imax = v_list_time_off_date.length - 1;
            if (v_begin_time > v_list_time_off_date[imax]) {
                count = Number(count) + Number(1);
                v_begin_time = v_list_time_off_date[0];
            } else {
                for (var i = 0; i <= imax; i++) {
                    if (v_list_time_off_date[i] == v_begin_time) {
                        break;
                    }
                    if ((v_list_time_off_date[i] < v_begin_time) && (v_list_time_off_date[Number(i) + Number(1)] > v_begin_time)) {
                        v_begin_time = v_list_time_off_date[Number(i) + Number(1)];
                        break;
                    }
                }
            }
        }
        var timemove = Number(p_limit_time.value);
        if (!isnum(p_limit_time.value)) {
            timemove = 0;
        }
        var timeadd = 0;
        while ((timemove - v_list_time_off_date.length) > v_list_time_off_date.length) {
            timeadd = Number(timeadd) + 1;
            timemove = timemove - v_list_time_off_date.length;
        }
        var i = 1;
        var v_next_time = v_begin_time;
        while (i <= timemove) {
            v_next_time = Number(v_next_time) + Number(1);
            while (list_have_element(_LIST_WORK_TIME_OF_DAY, v_next_time, ",") < 0) {
                if (v_next_time > v_list_time_off_date[imax]) {
                    count = Number(count) + Number(1);
                    v_next_time = v_list_time_off_date[0];
                } else {
                    v_next_time = Number(v_next_time) + Number(1);
                }
            }
            i++;
        }
        //----------------------------------------------------
        //Lay thong tin Nam hien thoi
        var d = new Date();
        var p_year = d.getFullYear();
        var v_list_day_off_of_year = _LIST_DAY_OFF_OF_YEAR.split(",");
        var v_list_day = _LIST_WORK_DAY_OF_WEEK;
        var v_list_luner_date = "";
        var v_increase_and_decrease_day = parseInt(_INCREASE_AND_DECREASE_DAY);
        var v_date, v_temp_date;
        var v_next_date = "";
        //Ngay thuc hien
        var v_input_date = v_implementation_date;
        if (!isdate(v_input_date)) {
            return;
        } else {
            var arr_input_date = v_input_date.split("/");
            v_input_date = arr_input_date[0] * 1 + "/" + arr_input_date[1] * 1 + "/" + arr_input_date[2] * 1;
        }
        for (var i = 0; i < v_list_day_off_of_year.length; i++) {
            v_date = v_list_day_off_of_year[i].split("/");
            if (v_date[0] == "-") {
                v_list_luner_date = list_append(v_list_luner_date, v_date[1] + "/" + v_date[2] + "/" + p_year, ",");
            } else {
                v_temp_date = Solar2Lunar(v_date[1] + "/" + v_date[2] + "/" + p_year);
                v_list_luner_date = list_append(v_list_luner_date, v_temp_date, ",");
            }
        }
        var i = 0;
        v_next_date = v_input_date;
        //Tinh tong so ngay tru ngay duoc nghi ra
        while ((i < count - v_increase_and_decrease_day)) {
            if ((list_have_date(v_list_luner_date, Solar2Lunar(v_next_date), ",") != 1) && (Solar2DayofWeek(v_next_date) != 7) && (Solar2DayofWeek(v_next_date) != 8)) {
                i++;
                v_next_date = Next_Date(v_next_date);
            } else {
                v_next_date = Next_Date(v_next_date);
            }
        }
        //Neu gap ngay thu 7 hoac CN thi bo qua
        while ((list_have_element(v_list_day, Solar2DayofWeek(v_next_date), ",") < 0)) {
            v_next_date = Next_Date(v_next_date);
        }
        p_appointed_date.value = v_next_date;
        p_appointed_time.value = v_next_time;
    }
}
//Ham kiem tra kieu ngay/thang/nam
function test_date(v_input_date) {
    var pattern = "^(?:(?:31(\\/|-|\\.)(?:0?[13578]|1[02]))\\1|(?:(?:29|30)(\\/|-|\\.)(?:0?[1,3-9]|1[0-2])\\2))(?:(?:1[6-9]|[2-9]\\d)?\\d{2})$|^(?:29(\\/|-|\\.)0?2\\3(?:(?:(?:1[6-9]|[2-9]\\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\\d|2[0-8])(\\/|-|\\.)(?:(?:0?[1-9])|(?:1[0-2]))\\4(?:(?:1[6-9]|[2-9]\\d)?\\d{2})$";
    var rx = new RegExp(pattern, "g");
    return rx.test(v_input_date);
}
//Ham mo cua so o giua man hinh
function DialogCenter(pageURL, title, w, h) {
    var left = (screen.width / 2) - (w / 2);
    var top = (screen.height / 2) - (h / 2);
    var targetWin = window.open(pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
}
function checkvalue() {
    try {
        document.getElementById('timkiem').focus();
    } catch (e) {
        ;
    }
    actionUrl('');
}
function set_upcase_value(p_obj) {
    v_value = p_obj.value;
    p_obj.value = v_value.toUpperCase();
}
//set hidden man hinh danh sach van ban den
function set_checked(chk_obj, value, tablename) {
    objtable = document.getElementById(tablename);
    v_count = objtable.getElementsByTagName('tr').length;
    for (i = 0; i < chk_obj.length; i++) {
        if (chk_obj[i].value == value && chk_obj[i].disabled == false)
            if (chk_obj[i].checked) {
                chk_obj[i].checked = false;
                try {
                    objtable.rows[i].setAttribute('checked', '');
                } catch (e) {
                    ;
                }
            }
            else {
                chk_obj[i].checked = true;
                try {
                    objtable.rows[i].setAttribute('checked', 'checked');
                } catch (e) {
                    ;
                }
            }
    }
}
//Ham thuc hien chon tat ca doi tuong
function set_check_all(chk_obj, option) {
    if (option == 1)
        for (i = 0; i < chk_obj.length; i++)
            chk_obj[i].checked = true;
    else
        for (i = 0; i < chk_obj.length; i++)
            chk_obj[i].checked = false;
}
function business_capital_onchange(p_obj) {
    numcheck = /[A-z]/;
    if (numcheck.test(p_obj.value)) {
        alert('So khong hop le');
        p_obj.focus();
        return false;
    }
    var str = numberic_to_string(replace(p_obj.value, ',', ''));
    if (str != "So khong hop le") {
        document.forms[0].business_capital_string.value = str + "đồng chẵn";
    } else {
        document.forms[0].business_capital_string.value = str;
    }

}

function numberic_to_string(so) {
    var i;
    var j;
    var kq = "";
    var l;
    var dk;
    var tmp = "";
    var check = false;
    var a = new Array(32);
    //Loai het so 0 o dau
    while (so.length > 0 && so.charAt(0) == "0") {
        so = so.substring(1, so.length);
    }
    //alert(so);
    l = so.length;
    if (l > 28) {
        return "So khong hop le";
    }

    //Load cac chu so cua so can doc
    //vao mang a
    for (var i = 1; i <= l; i++) {
        a[i] = parseInt(so.charAt(i - 1));
    }
    //Bat dau doc tu trai sang phai
    for (var i = 1; i <= l; i++) {

        if ((l - i) % 3 == 2 && a[i] == 0 && (l - i >= 2)) {
            if (a[i + 1] != 0 || a[i + 2] != 0) {
                kq = kq + "không ";
            }
        }

        if (a[i] == 2) {
            kq = kq + "hai ";
        }
        if (a[i] == 3) {
            kq = kq + "ba ";
        }
        if (a[i] == 6) {
            kq = kq + "sáu ";
        }
        if (a[i] == 7) {
            kq = kq + "bảy ";
        }
        if (a[i] == 8) {
            kq = kq + "tám ";
        }
        if (a[i] == 9) {
            kq = kq + "chín ";
        }


        //Xu ly cach doc so 4
        if (a[i] == 4) {
            if (i > 1 && (l - i) % 3 == 0) {
                if (a[i - 1] > 1) {
                    kq = kq + "tư ";
                } else {
                    kq = kq + "bốn ";
                }
            } else {
                kq = kq + "bốn ";
            }
        } //a(i)=4

        //Xu ly cach doc so 5
        if (a[i] == 5) {
            if (i > 1 && (l - i) % 3 == 0) {
                if (a[i - 1] != 0) {
                    kq = kq + "lăm ";
                } else {
                    kq = kq + "năm ";
                }
            } else {
                kq = kq + "năm ";
            }
        } //a(i)=5

        //Xu ly cach doc so 1
        if (a[i] == 1) {
            //doc la muoi neu no la hang chuc
            if ((l - i) % 3 == 1) {
                kq = kq + "mười ";  //doc la mot neu la hang don vi //va hang chuc >1
            } else {
                if ((l - i) % 3 == 0 && (i > 1)) {
                    if (a[i - 1] > 1) {
                        kq = kq + "mốt ";
                    } else {
                        kq = kq + "một ";
                    }
                } else {
                    kq = kq + "một ";
                }
            }
        } //a(i)=1


        //Doc tiep la muoi neu
        //No la so hang chuc va
        //Khac 1 va 0
        if ((l - i) % 3 == 1 && a[i] != 0 && a[i] != 1) {
            kq = kq + "mươi ";
        }

        if ((l - i) % 3 == 1 && a[i] == 0 && a[i + 1] != 0) {
            kq = kq + "linh ";
        }

        if ((l - i) % 3 == 2 && (a[i + 1] != 0 || a[i + 2] != 0)) {
            kq = kq + "trăm ";
        }

        if ((i + 2) <= l) {
            if (a[i] != 0 && (l - i) % 3 == 2) {
                if (a[i + 1] == 0 && a[i + 2] == 0) {
                    kq = kq + "trăm ";
                }
            }
        }

        if ((l - i) == 3) {
            kq = kq + "nghìn ";
        }
        if ((l - i) == 6) {
            kq = kq + "triệu ";
        }
        if ((l - i) == 9) {
            kq = kq + "tỷ ";
        }

        if ((l - i) == 12) {
            check = true;
            for (j = i + 1; i < l; i++) {
                if (a[i + 1] != 0) {
                    check = false;
                }
            }
            if (check == false) {
                kq = kq + "nghìn ";
            } else {
                kq = kq + "nghìn tỷ ";
            }
        }

        if ((l - i) == 15) {
            kq = kq + "triệu tỷ ";
        }
        if ((l - i) == 18) {
            kq = kq + "tỷ tỷ ";
        }
        if ((l - i) == 21) {
            kq = kq + "nghìn tỷ tỷ ";
        }
        if ((l - i) == 24) {
            kq = kq + "triệu tỷ tỷ ";
        }
        if ((l - i) == 27) {
            kq = kq + "tỷ tỷ tỷ ";
        }
        if ((l - i) == 30) {
            kq = kq + "nghìn tỷ tỷ ";
        }

        //Xu ly bo 3 so khong
        if (((l - i) % 3 == 2) && (a[i] == 0) && (a[i + 1] == 0) && (a[i + 2] == 0)) {
            i = i + 2;
        }

        //Xu ly tat ca so khong con lai
        if ((l - i) % 3 == 0) {
            dk = 1;
            for (j = i + 1; j <= l; j++) {
                if (a[j] != 0) {
                    dk = 0;
                }
            }
        }
        if (dk == 1) {
            break;
        }

    }

    //Viet hoa chu cai dau tien
    if (kq == "") kq = "không"
    while (kq.charAt(kq.length) == ",") {
        kq = kq.substring(0, kq.length - 1);
    }
    kq = kq.charAt(0).toUpperCase() + kq.substring(1, kq.length);
    return kq;
}
/**
 * Des: Ham thuc hien chon radio hoac checkbox khi click vao label cua no
 * @param obj
 * @param value
 * option: 'radio' or 'checkbox'
 */
function set_checked_onlabel(obj, value, option) {
    for (i = 0; i < obj.length; i++) {
        if (obj[i].value == value && obj[i].disabled == false) {
            obj[i].checked = true;
        } else {
            if (option == 'checkbox')
                obj[i].checked = false;
        }
    }
}
/**
 * Creater: Toanhv
 * Date: 27/12/2010
 * Dis: checked all Or unchecked all
 */
function _checked_all(obj_all, obj_list) {
    if (obj_all.checked) {
        for (i = 0; i < obj_list.length; i++) {
            obj_list[i].checked = true;
        }
    } else {
        for (i = 0; i < obj_list.length; i++) {
            obj_list[i].checked = false;
        }
    }
}
/**
 * Creater: Phuongtt
 * Date: 28/04/2011
 * @param string
 * @returns {Boolean}
 */
function isInteger(string) {
    var numericExpression = "^[0-9]+$";
    if (string.match(numericExpression))
        return true;
    else
        return false;
}
/**
 * Creater: Trinh Thanh PHuong
 * Date: 28/04/2011
 */
function btn_save_onclick(formId, option) {
    if (verify(document.getElementById(formId))) {
        $('#hdn_is_update').val('1');
        $('#hdn_option_new').val(option);
        document.getElementById(formId).submit();
    }
}
/**
 * Creater: Trinh Thanh PHuong
 * Date: 28/04/2011
 */
function btn_back_onclick_form(formId, url) {
    document.getElementById(formId).action = url;
    document.getElementById(formId).submit();
}
/**
 * Creater: Trinh Thanh PHuong
 * Date: 28/04/2011
 */
function left_menu_hide() {
    $('div#left-silebar').hide();
    $('div#push-left-hide').show();
    $('div#content').css({
        'margin': '0 1px 0 30px',
        'min-height': '0'
    });
    Set_Cookie('showHideMenu', 0, '', '/', '', '');
    fixTitle();
    if (typeof fixInformation == 'function')
        fixInformation();
    /*if (typeof colResizableModule == 'function')
     colResizableModule();*/
}
/**
 * Creater: Trinh Thanh PHuong
 * Date: 28/04/2011
 */
function left_menu_show() {
    $('div#left-silebar').show();
    $('div#push-left-hide').hide();
    $('div#content').css("margin", '0 1px 0 184px');
    Set_Cookie('showHideMenu', 1, '', '/', '', '');
    fixTitle();
    if (typeof fixInformation == 'function')
        fixInformation();
    /*if (typeof colResizableModule == 'function')
     colResizableModule();*/
}
/**
 * Creater: Trinh Thanh PHuong
 * Date: 28/04/2011
 */
function changeNumberRowPerPage(obj) {
    document.getElementById('hdn_record_number_page').value = obj.value;
    document.getElementsByTagName('form')[0].submit();
}
/**
 * Creater: Trinh Thanh PHuong
 * Date: 28/04/2011
 */
function gotopage(num) {
    document.getElementById('hdn_record_number_page').value = $('#cbo_nuber_record_page').val();
    document.getElementById('hdn_current_page').value = num;
    document.getElementsByTagName('form')[0].submit();
}
/**
 * Creater: Trinh Thanh PHuong
 * Date: 28/04/2011
 */
function addOnclick(url) {
    document.getElementById('hdn_object_id').value = '';
    actionUrl(url);
}
/**
 * Creater: Trinh Thanh Phuong
 * Date: 25/10/2011
 */
function setSelectRow(obj, chk_obj, value) {
    for (i = 0; i < chk_obj.length; i++) {
        if (chk_obj[i].value == value && chk_obj[i].disabled == false) {
            chk_obj[i].checked = true;
            $('td').parent().removeClass('selected');
            $(obj).parent().addClass('selected');
        } else {
            chk_obj[i].checked = false;
        }
    }
}
function setColorRow(obj, chk_obj, value) {
    for (i = 0; i < chk_obj.length; i++) {
        if (chk_obj[i].value == value && chk_obj[i].disabled == false) {
            chk_obj[i].checked = true;
            $('td').parent().removeClass('selected');
            $(obj).parent().addClass('selected');
        } else {
            chk_obj[i].checked = false;
        }
    }
}
/**
 * Creater: Trinh Thanh Phuong
 * Date: 25/10/2011
 */
function getScrollTop() {
    if (typeof pageYOffset != 'undefined') {
        //most browsers
        return pageYOffset;
    }
    else {
        var b = document.body; //IE 'quirks'
        var d = document.documentElement; //IE with doctype
        d = (d.clientHeight) ? d : b;
        return d.scrollTop;
    }
}
/**
 * Hàm giới hạn chỉ được phép nhập các chữ số
 * @param obj
 */
function inputInt(e) {
    charCode = e.which;
    string = String.fromCharCode(charCode);
    var regex = "^[0-9]+$";
    if (string.match(regex))
        return true;
    else {
        if (charCode == 0 && charCode == 118)
            return true
        else {
            return false;
        }
    }
}
/**
 * Reset lại số trang hiện tại về 1 khi thay đổi tiêu chí tìm kiếm
 */
function ResetSearch() {
    document.getElementById('hdn_current_page').value = "1";
}
function _gettimeworkofday(d, begin_time) {

    var work_time = (pm_stop_time - pm_start_time + am_stop_time - am_start_time) * 3600;
    var iTimeAm = (am_stop_time - am_start_time) * 3600;
    var iTimePm = (pm_stop_time - pm_start_time) * 3600;
    // var d = new Date('2013/07/29');
    var hours = d.getHours();
    var Day = d.getDay();
    var iTimeRestOfDayBegin = 0;
    var hours = begin_time / 3600;
    if (Day == 6) {
        if (satTime == 0) { // Nghỉ làm thứ 7
            return 0;
        } else if (satTime == 1) {
            // Ngay T7 lam buoi sang
            iStartCurrent = strtotime(startcurrentdate) + am_stop_time * 3600;
            iTimeRestOfDayBegin = (hours - am_stop_time) * 3600;
            return iTimeRestOfDayBegin;
        }
    }
    if (hours > am_stop_time && hours < pm_start_time) {
        hours = pm_start_time;
    }
    ;
    // La ngay lam viec binh thuong lam ca ngay
    if (hours >= am_start_time && hours <= am_stop_time) {
        // Buoi sang
        iTimeRestOfDayBegin = (am_stop_time - hours) * 3600 + iTimePm;
    } else if (hours >= pm_start_time && hours <= pm_stop_time) {
        // Buoi chieu
        iTimeRestOfDayBegin = (pm_stop_time - hours) * 3600;
    }
    return iTimeRestOfDayBegin;
}
var _ddmmyyyyToYYyymmdd = function (stringDate) {
    var delimitor = '';
    if (stringDate.indexOf('/') >= 0) {
        delimitor = '/';
    } else {
        delimitor = '-';
    }
    var arr = stringDate.split(delimitor);
    return arr[2] + delimitor + arr[1] + delimitor + arr[0];
}
var _getTimeDay = function (d) {
    if (typeof(d) == 'undefined') {
        d = new Date();
    }
    ;
    var iTime = 0;
    iTime = d.getHours() * 3600 + d.getMinutes() * 60 + d.getSeconds();
    return iTime;
}
/**
 Creater: Phuongtt
 Des: Hàm tính ngày, giờ hẹn trả
 begin_date : Ngày bắt đầu tính
 begin_time : Thời điểm bắt đầu tính
 number_dates : Số ngày
 number_hours : Số giờ
 Return: [d, appoint_time] Trong đó d là ngày hẹn, appoint_time là thời điểm hẹn
 */
function get_appointed_date(begin_date, begin_time, number_dates, number_hours) {
    var iTimeAm = (am_stop_time - am_start_time) * 3600;
    var iTimePm = (pm_stop_time - pm_start_time) * 3600;

    if (!test_date(begin_date) && begin_date != '') {
        alert('Ngày bắt đầu không đúng định dạng ngày tháng năm');
        return false;
    }
    if (!isInteger(number_hours) && number_hours != '' && number_hours != 0)
        number_hours = 0;
    if (begin_time == '' || begin_time == 0) {
        begin_time = am_start_time * 3600;
    }
    // Thơi lam gian con lai cua ngay hien tai
    var dconvert = new Date(_ddmmyyyyToYYyymmdd(begin_date));
    var iTimeRestOfDay = _gettimeworkofday(dconvert, begin_time);
    var work_time = (pm_stop_time - pm_start_time + am_stop_time - am_start_time) * 3600;
    var iTotalRest = work_time * number_dates - iTimeRestOfDay;
    var count_date = 0;
    number_dates = parseInt(iTotalRest / work_time);
    number_times = iTotalRest % work_time;
    var number_hours = parseInt(number_hours);
    var d, lunarDate;
    d = begin_date;
    while (count_date < number_dates) {
        count_date++;
        d = getDateOfNextDay(d, '/', 1);
        lunarDate = Solar2Lunar(d);
        if (checkDateIn(lunarDate, '/', tetHoliday, ','))
            count_date--;
        if (((Solar2DayofWeek(d) == 7 && satTime == 0) || Solar2DayofWeek(d) == 8) && !checkDateIn(lunarDate, '/', tetHoliday, ','))
            count_date--;
        if ((Solar2DayofWeek(d) == 7 && satTime == 1) && !checkDateIn(lunarDate, '/', tetHoliday, ',')) {
            //count_date--;
            number_times = number_times + iTimeAm;
        }
        if (checkDateIn(lunarDate, '/', lunarHoliday, ',') && !checkDateIn(lunarDate, '/', tetHoliday, ','))
            count_date--;
        if (checkDateIn(d, '/', solarHoliday, ',') && !checkDateIn(lunarDate, '/', tetHoliday, ','))
            count_date--;
    }
    if (number_times > 0) {
        number_dates = number_times / work_time;
        if (number_dates >= 1) {
            return get_appointed_date(d, am_start_time * 3600, number_dates, '0');
        } else {
            return setTimeAppoint(d, number_times);
        }
    }
    else {
        return [d, begin_time];
    }
}
/**
 Creater: Phuongtt
 Des: Hàm lấy ngày tiếp theo
 datestring : Ngày
 separator : Ký tự phân tách ngày của datestring
 nozero : Hiển thị thêm số 0 hay không
 Return: datestring ngày tiếp theo
 */
function getDateOfNextDay(datestring, separator, nozero) {
    if (!separator)
        separator = "-";//="yyyy-dd-mm" format
    var a_date = datestring.split(separator);
    var myday = new Date(a_date[2] + '/' + a_date[1] + '/' + a_date[0]);
    myday.setDate(myday.getDate() + 1);
    var next_day_year = myday.getFullYear();
    var next_day_month = myday.getMonth() + 1;
    var next_day_day = myday.getDate();
    if (!nozero) {
        next_day_month = (parseInt(next_day_month) < 10) ? "0" + next_day_month : next_day_month;
        next_day_day = (parseInt(next_day_day) < 10) ? "0" + next_day_day : next_day_day;
    }
    return next_day_day + separator + next_day_month + separator + next_day_year;
}
/**
 * Hàm tính số số ngày giữa 2 mốc thời gian
 * @param date1 : Từ ngày
 * @param date2 : Đến ngày
 * @returns {Boolean}
 */
function countDay(date1, date2, separator) {
    if (!test_date(date1) && date2 != '') {
        alert('Ngày không đúng định dạng ngày tháng năm');
        return false;
    }
    var count_date = 0;
    var beginD, lunarDate, endD, negative;
    var a_date = date1.split(separator);
    var objd1 = new Date(a_date[2] + '/' + a_date[1] + '/' + a_date[0]);
    a_date = date2.split(separator);
    var objd2 = new Date(a_date[2] + '/' + a_date[1] + '/' + a_date[0]);
    if (objd1 < objd2) {
        beginD = objd1;
        endD = objd2;
        negative = 1;
    }
    else if (objd2 < objd1) {
        beginD = objd2;
        endD = objd1;
        negative = 0;
    }
    else return 0;
    var next_day_year = endD.getFullYear();
    var next_day_month = endD.getMonth() + 1;
    var next_day_day = endD.getDate();
    var finishD = next_day_day + separator + next_day_month + separator + next_day_year;

    var next_day_year = beginD.getFullYear();
    var next_day_month = beginD.getMonth() + 1;
    var next_day_day = beginD.getDate();
    var d = next_day_day + separator + next_day_month + separator + next_day_year;
    while (d != finishD) {
        count_date++;
        d = getDateOfNextDay(d, '/', 1);
        lunarDate = Solar2Lunar(d);
        if (checkDateIn(lunarDate, '/', tetHoliday, ','))
            count_date--;
        if (((Solar2DayofWeek(d) == 7 && (satTime == 1 || satTime == 0)) || Solar2DayofWeek(d) == 8) && !checkDateIn(lunarDate, '/', tetHoliday, ','))
            count_date--;
        if (checkDateIn(lunarDate, '/', lunarHoliday, ',') && !checkDateIn(lunarDate, '/', tetHoliday, ','))
            count_date--;
        if (checkDateIn(d, '/', solarHoliday, ',') && !checkDateIn(lunarDate, '/', tetHoliday, ','))
            count_date--;
    }
    if (negative == 0)
        return (0 - count_date);
    else return count_date;
}
function htmlAppointedDate(appDate) {
    if (appDate != '') {
        var dates = countDay(_CURRENT_DAY, appDate, '/');
        if (dates > 0) {
            return appDate + '<br /><font><i>(Còn lại ' + dates + ' ngày)</i></font>'
        } else if (dates < 0) {
            return appDate + '<br /><font color = "red"><i><b>(Quá hạn ' + (0 - dates) + ' ngày)</i></b></font>'
        } else {
            return appDate + '<br /><font color = "blue"><b><i>(Hôm nay là hạn xử lý)</i></b></font>'
        }
        return appDate;
    } else {
        return '<font color = "red"><b><i>Không có hạn xử lý</i></b></font>'
    }
}
/**
 Creater: Phuongtt
 Des: Hàm kiểm tra 1 ngày có nằm trong một dải ngày xác định hay không
 datestring : Ngày
 separator1 : Ký tự phân tách ngày của datestring
 strCheck : Chuỗi gồm các dải ngày
 separator2: Ký tự phân tách của strCheck
 */
function checkDateIn(datestring, separator1, strCheck, separator2) {
    //split string to get day/month
    strCheck = separator2 + strCheck + separator2;
    var a_date = datestring.split(separator1);
    var s_day_month = separator2 + a_date[0] + separator1 + a_date[1] + separator2;
    if (strCheck.indexOf(s_day_month) >= 0)
        return true;
    return false;
}
/**
 Creater: Phuongtt
 Des: Hàm tính thời điểm hẹn trả
 d : Ngày hẹn trả
 number_hours : Số giờ xử lý
 begin_time : Thời điểm bắt đầu
 */
function setTimeAppoint(d, number_times) {
    var iTimeAm = (am_stop_time - am_start_time) * 3600;
    var iTimePm = (pm_stop_time - pm_start_time) * 3600;
    var option = 0;
    var iTime = '';
    //Kiểm tra ngày hôm sau có là ngày nghỉ lễ hoặc T7, CN hay không
    lunarDate = Solar2Lunar(d);
    if (Solar2DayofWeek(d) == 8 && !checkDateIn(lunarDate, '/', tetHoliday, ','))
        d = getDateOfNextDay(d, '/');
    if (checkDateIn(lunarDate, '/', lunarHoliday, ',') && !checkDateIn(lunarDate, '/', tetHoliday, ','))
        d = getDateOfNextDay(d, '/');
    if (checkDateIn(d, '/', solarHoliday, ',') && !checkDateIn(lunarDate, '/', tetHoliday, ','))
        d = getDateOfNextDay(d, '/');
    if (checkDateIn(lunarDate, '/', tetHoliday, ','))
        d = getDateOfNextDay(d, '/');
    if (Solar2DayofWeek(d) == 7 && !checkDateIn(lunarDate, '/', tetHoliday, ',')) {
        if (satTime == 0) {
            d = getDateOfNextDay(d, '/');
            d = getDateOfNextDay(d, '/');
        } else if (satTime == 1)
            option = 1;
        else {
            option = 2;
        }
    }
    iTimeRestPm = 0;
    if (number_times < iTimeAm) {
        // Buoi sang
        iTime = am_start_time * 3600 + number_times;
        // iTime = iTime/3600;
        return [d, iTime];
    } else {
        iTimeRestPm = number_times - iTimeAm;
        //
    }
    // Buoi chieu
    if (option == 1) {//Nếu ngày hẹn trả vào T7 và T7 làm buổi sáng => thời điểm hẹn trả vào thứ 2
        d = getDateOfNextDay(d, '/');
        d = getDateOfNextDay(d, '/');
        iTime = iTimeRestPm + am_start_time * 3600;
        // iTime = iTime/3600;
        return [d, iTime];
    } else {//Neu hom sau la thu 7 va T7 lam ca ngay hoặc là ngày bình thương
        iTime = iTimeRestPm + pm_start_time * 3600;
        // iTime = iTime/3600;
        return [d, pm_start_time + iTimeRestPm];
    }
}
/**
 Creater: Phuongtt
 Des: Co dinh tieu de bang
 */
function fixTitle(optionmodal, oForm) {
    return false;
    if (typeof(optionmodal) === 'undefined') {
        optionmodal = false;
    }
    ;
    if (typeof(oForm) === 'undefined') {
        oForm = $('div#table-container').closest('form');
    }
    ;
    var ctHeight = $('div.normal_title').outerHeight() + $('div.search-container').outerHeight();
    if (optionmodal) {
        $('div.searh-fixed').css({
            'width': $('div#content').width()
        });
    } else {
        $('div.searh-fixed').css({
            'width': $('div#content').width(),
            'z-index': 100
        });
    }
    // check menu
    var residual = 0;
    if ($('#main-header ul.sub_top_menu').length === 0) {
        residual = 1;
    }
    var heightsearch = $(oForm).find('div.searh-fixed').outerHeight();
    $(oForm).find('div#table-container').css('padding-top', heightsearch + 2 - residual);
    /*Get width of tds*/
    var numTd = $(oForm).find('div#table-container table#table-data tr:eq(0) td').length;
    var strInsert = '<tr id ="table-data-rowadd">';
    // $('div#table-container table#table-data tr.header td:eq(0)').css('border-left','none');
    $(oForm).find('div#table-container table#table-data tr.header td:eq(' + (numTd - 1) + ')').css('border-right', 'none');
    for (i = 0; i < numTd; i++) {
        var widthTd = 0;
        strInsert += '<td></td>';
        widthTd = $(oForm).find('div#table-container table#table-data tr:eq(0) td:eq(' + i + ')').width();

        $(oForm).find('div#table-container table#table-data tr.header td:eq(' + i + ')').css("width", widthTd);
    }
    strInsert += '</tr>';
    if ($(oForm).find('div#table-container table#table-data tr#table-data-rowadd').length == 0)
        $(oForm).find('div#table-container table#table-data tr.header').after(strInsert);
    var heightTitle = $(oForm).find('div#table-container table#table-data tr.header').height();

    $(oForm).find('div#table-container table#table-data tr#table-data-rowadd').css('height', heightTitle);

    var heightconst = $('#main-header').outerHeight() + $('#banner').outerHeight();
    $(oForm).find('div#table-container table#table-data tr.header').css({
        'position': 'fixed',
        'top': heightsearch + heightconst + residual
    })
}

/**
 Create: Truongdv
 Des: NSD Click vao Menu Head Load Ajax
 */
function selectHead(url, currentResource) {
    if (typeof(selectheadercurrent) === 'boolean') {
        return false;
    }
    ;
    selectheadercurrent = true;
    if (history.pushState) history.pushState("", "", url);
    if (typeof($("div#main-content div#content").load) === 'undefined') {
        window.location.href = url;
        location.reload();
    } else {
        showloadpage();
        $("div#main-content div#content").load(url, {}, function (string) {
            removeEventOld();
            $('li#' + currentResource).trigger('click');
            var currentState = {
                content: string
            };
            delete selectheadercurrent;
            hideloadpage();
        });
    }
    return !history.pushState;
}
/**
 Create: Truongdv
 Des: NSD Click vao Menu Left Load Ajax
 */
function selectLeft(obj, currentResource) {
    $('li#' + currentResource + ' a').trigger('click');
    return false;
}
function showloadpage() {
    if ($('#note-process #file_export').is(':visible') == false) {
        $('#note-process #modalnotify,#note-process').show()
        $('#note-process #modalnotify').css('top', window.innerHeight * 2 / 5 + 'px');
    }
}
function hideloadpage() {
    $('#note-process #modalnotify').hide()
    if ($('#note-process #file_export').is(':visible') == false) {
        $('#note-process').hide()
    }
}
/**
 Create: Truongdv
 Des: Load file Js va css
 Params: arrUrl luu danh sach url {[]['type'],[]['url']}
 */
function loadfileJsCss(data) {
    var count = data.length, src = '', ext = '';
    for (var i = 0; i < count; i++) {
        src = data[i];
        ext = src.split('.').pop();
        if (ext === 'js') {
            if ($('script[src="' + src + '"]').length == 0) {
                $('head').append('<script src="' + src + '" type="text/javascript" charset="utf-8"></script>');
            }
        } else {
            if ($('link[href="' + src + '"]').length == 0)
                $('head').append('<link rel="stylesheet" type="text/css" href="' + src + '">');
        }
    }
    ;

}
/*function loadfileJsCss(data){
 var count = data.length, src ='', ext ='';
 var arrsCript =[], arrLink=[],arr=[];
 $('head script').each(function(){
 src = $(this).attr('src');
 if(typeof(src) != 'undefined'){
 src= src.trim()
 arr = src.split('?')
 arrsCript.push(arr[0])
 }
 })
 console.log($('head').html())
 $('head link').each(function(){
 src = $(this).attr('href');
 if(typeof(src) != 'undefined'){
 src= src.trim()
 arr = src.split('?')
 arrLink.push(arr[0])
 }
 })
 for (var i = 0; i < count; i++){
 src = data[i];
 ext = src.split('.').pop();
 if(ext ==='js'){
 if(arrsCript.indexOf(src) == -1){
 $('head').append('<script src="' + src + '" type="text/javascript" charset="utf-8"></script>');}
 }else{
 if(arrLink.indexOf(src)  == -1)
 $('head').append('<link rel="stylesheet" type="text/css" href="' + src + '">');
 }
 };

 }*/
function open_modal_export_file(fileType, filePath) {
    var fileName = '', shtml = '';
    if (filePath == '' || typeof(filePath) === 'undefined') {
        jAlert('Hình thức xử lý không có mẫu in!', 'Thông báo');
        return false;
    }

    var arr_file = filePath.split("!~!");
    $('div.file_export-title').html('');
    $('a.file_export-icon').removeClass('file_export-excel').removeClass('excel').removeClass('xls').removeClass('doc');
    if(arr_file.length > 1) {
        var arr_file_length = arr_file.length;
        for (var i = 0; i < arr_file_length; i++) {
            var arrfilePath = arr_file[i].split("/");
            fileName = arrfilePath[arrfilePath.length - 1];
            shtml += '<div style="float:left; height:30px;width:280px; ">' + '<a class="file_export-icon ' + fileType + '" href="' + arr_file[i] + '" style="float:left;"></a><div style="margin-top:20px; padding-left:45px; text-align:left;"><a href="' + arr_file[i] + '" style="color:white">' + fileName + '</a></div></div>';
        }
        $('div.file_export-title').html(shtml);
    } else {
        var arrfilePath = filePath.split("/");
        var fileName = arrfilePath[arrfilePath.length - 1];
        $('div.file_export-title').html(fileName);
        $('a.file_export-icon').attr('href', filePath);
        $('a.file_export-icon').addClass('file_export-icon ' + fileType);
    }

    $('div#note-process').addClass('nq vY');
    $('div#note-process').show();
    $('div#file_export').show();
}

function Link() {
    document.getElementById("Link").href = "../public/export/phan_loai.zip";
}
function open_load_data_process() {
    $('div#note-process').addClass('nq vY');
    $('div#note-process').show();
    $('div#load_data').show();
}
function close_load_data_process() {
    $('div#note-process').removeClass('vY nq');
    $('div#note-process,#file_export,#modalnotify').hide();
    $('div#load_data').hide();
}
/*Upload nhieu file lien tuc*/
function attachFile(btnUpload, status, site_url) {
    new AjaxUpload(btnUpload, {
        action: site_url + 'main/ajax/uploadfile/',
        name: 'uploadfile',
        onSubmit: function (file, ext, sizemax) {
            if (sizemax > upload_max_filesize) {
                jAlert('Dung lượng file quá lớn, vui lòng chọn file có dung lượng nhỏ hơn ' + (upload_max_filesize / 1000) + 'Mb', 'Thông báo !')
                return false;
            }
            $('<div></div>').appendTo('#listAttach').html('Đang gửi file. Vui lòng chờ trong giây lát...').addClass('status').css('width', '55%');
        },
        onComplete: function (file, response) {
            //remove status
            $('div#listAttach div.status').remove();
            //On completion clear the status
            btnUpload.parent().prev().text('');
            var oDivFile = btnUpload.parent().parent();
            var oListAttach = $(oDivFile).find('#listAttach');
            var oFileAttach = $(oDivFile).find('#filesattach');
            var doctype = $(oListAttach).parent().attr('doctype');
            //Add uploaded file to list
            if (response !== "error") {
                file = convertVN_EN(file);
                $('<div></div>').appendTo(oListAttach).html('<input type="checkbox" doctype="' + doctype + '" id="chk_attach" checked="" value="' + response + '" /><a href="' + response + '" alt="">' + file + '</a>').addClass('success');
            } else {
                $('<div></div>').appendTo(oFileAttach).text(file).addClass('error');
            }
        }
    });
};
/*Upload file video*/
function attachVideoFile(btnUpload, status, site_url) {
    new AjaxUpload(btnUpload, {
        action: site_url + 'main/ajax/uploadfile/',
        name: 'uploadfile',
        onSubmit: function (file, ext, sizemax) {
            if (!(ext && /^(flv|avi)$/.test(ext))) {
                jAlert('Chỉ chọn các file video .flv, .avi!')
                return false;
            }
            if (sizemax > upload_max_filesize) {
                jAlert('Dung lượng file quá lớn, vui lòng chọn file có dung lượng nhỏ hơn ' + (upload_max_filesize / 1000) + 'Mb', 'Thông báo !')
                return false;
            }
            $('<div></div>').appendTo('#listAttach').html('Đang gửi file. Vui lòng chờ trong giây lát...').addClass('status').css('width', '55%');
        },
        onComplete: function (file, response) {
            //remove status
            $('div#listAttach div.status').remove();
            //On completion clear the status
            btnUpload.parent().prev().text('');
            var oDivFile = btnUpload.parent().parent();
            var oListAttach = $(oDivFile).find('#listAttach');
            var oFileAttach = $(oDivFile).find('#filesattach');
            var doctype = $(oListAttach).parent().attr('doctype');
            //Add uploaded file to list
            if (response !== "error") {
                file = convertVN_EN(file);
                $('<div></div>').appendTo(oListAttach).html('<input type="checkbox" doctype="' + doctype + '" id="chk_attach" checked="" value="' + response + '" /><a href="' + response + '" alt="">' + file + '</a>').addClass('success');
            } else {
                $('<div></div>').appendTo(oFileAttach).text(file).addClass('error');
            }
        }
    });
};
//Xoa danh sach file dinh kem
function delfiles(obj, doctype, fk_doc, site_url) {
    var list = '', delimitor = '!~@~!';
    var oDivFile = (obj).parent().parent();
    $(oDivFile).find('div#listAttach input[id="chk_attach"]:checked').each(function () {
        if (list === '') {
            list = $(this).val();
        } else {
            list += delimitor + $(this).val();
        }
    });
    if (list === '')
        return false;
    // Xoa
    jConfirm('Bạn có chắc chắn muốn xóa những file này?', 'Confirmation Dialog', function (r) {
        if (r) {
            var arrData = {
                fk_doc: fk_doc,
                list: list,
                doctype: doctype,
                delimitor: delimitor
            };
            $.ajax({
                url: site_url + '/main/ajax/deletefile/',
                type: 'POST',
                data: arrData,
                //cache: true,
                success: function (string) {
                    $(oDivFile).find('div#listAttach input[id="chk_attach"]:checked').each(function () {
                        $(this).parent().remove();
                    });
                },
                error: function () {
                    jAlert('Có lỗi xảy ra', 'Error Dialog');
                }
            });
        }
    });
}
/**
 * Creater: Truongdv
 * Date: 30/08/2012
 * Idea: Xóa file đính kèm(Xóa file gốc và xóa trong db)
 * Param: date la mang du lieu can truyen vao xu ly trong controller
 */
function cldelete(obj) {
    var delimitor = '!~@~!';
    var href = $(obj).parent().prev().find('a').attr('href');
    var oForm = $(obj).closest("form");
    var fk_doc = $(oForm).find('input#PkRecord').val();
    if (href != undefined && href != '') {
        jConfirm('Bạn có chắc chắn muốn file này?', 'Confirmation Dialog', function (r) {
            if (r) {
                var data = {
                    fk_doc: fk_doc,
                    doctype: $(obj).parent().parent().find('input.sglatach').val(),
                    list: href,
                    delimitor: delimitor
                };
                $.ajax({
                    url: baseUrl + '/main/ajax/deletefile/',
                    type: 'POST',
                    data: data,
                    // cache: true,
                    success: function (string) {
                        $(obj).parent().prev().find('div#attach').html('').removeClass('error');
                        var objdelete = $(obj).parent().parent().children().find('input[type="checkbox"]');
                        objdelete.attr('attachFile', '');
                        objdelete.attr('checked', false);
                    },
                    error: function () {
                        jAlert('Có lỗi xảy ra', 'Error Dialog');
                    }
                });
            }
        });
    }
}
/**
 Creater: Truongdv
 Des: Convert string tu VN->EN
 */
function convertVN_EN(string) {
    if (typeof(string) === 'undefined')
        return '';
    string = string.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, "a");
    string = string.replace(/À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ/g, "a");
    string = string.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ.+/g, "e");
    string = string.replace(/È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ.+/g, "e");
    string = string.replace(/ì|í|ị|ỉ|ĩ/g, "i");
    string = string.replace(/Ì|Í|Ị|Ỉ|Ĩ/g, "i");
    string = string.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ.+/g, "o");
    string = string.replace(/Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ.+/g, "o");
    string = string.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, "u");
    string = string.replace(/Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ/g, "u");
    string = string.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, "y");
    string = string.replace(/Ỳ|Ý|Ỵ|Ỷ|Ỹ/g, "y");
    string = string.replace(/đ/g, "d");
    string = string.replace(/Đ/g, "d");
    return string;
}
/**
 Creater: Truongdv
 Des: Tao modal dialog
 */
function createModalDialog(obj, functioncall) {
    if ($(obj).parent('[role="dialog"]').length === 0) {
        functioncall();
    } else {
        $(obj).parent('[role="dialog"]').find(obj).remove();
        $(obj).parent('[role="dialog"]').remove();
        functioncall();
    }
}
function _replaceBadChar(string) {
    if (typeof(string) === 'undefined')
        return '';
    string = string.replace(/&/g, "&amp;");
    string = string.replace(/"/g, "&quot;");
    string = string.replace(/'/g, "&#39;");
    string = string.replace(/</g, "&lt;");
    string = string.replace(/>/g, "&gt;");
    return string;
}
// Restore badChar
function _restoreBadChar(string) {
    if (typeof(string) === 'undefined')
        return '';
    string = string.replace(/&amp;/g, "&");
    string = string.replace(/&quot;/g, '"');
    string = string.replace(/&#39;/g, "'");
    string = string.replace(/&lt;/g, "<");
    string = string.replace(/&gt;/g, ">");
    return string;
}
function _removeBadChar(string) {
    if (typeof(string) === 'undefined')
        return '';
    string = string.replace(/&/g, "");
    string = string.replace(/"/g, "");
    string = string.replace(/'/g, "");
    string = string.replace(/</g, "");
    string = string.replace(/>/g, "");
    string = string.replace(/"/g, "");
    return string;
}
function removeEventOld() {
    if ($('.auto-list').length > 0) {
        $('.auto-list').prev().hide();
        $('.auto-list').hide();
    }
}
function menudropdown(obj) {
    var divcontent = $(obj).next();
    var width = '180px', length = 0, lentem = 0;
    if ($(divcontent).length > 0) {
        $(divcontent).find('li').each(function () {
            lentem = $(this).text().length;
            if (lentem > length) {
                length = lentem;
            }
            ;
        })
        width = length * 7.5;
        if (width < 180) {
            width = 180;
        }
        ;
        width = width + 'px';
    }
    ;
    obj.menu({
        content: obj.next().html(),
        showSpeed: 400,
        width: width
    });
}
function lddadepicker(objParrent) {
    if (typeof(objParrent) === 'undefined') {
        objParrent = $('body');
    }
    $(objParrent).find('input[date="isdate"]').each(function () {
        $(this).datepicker({
            changeMonth: true,
            gotoCurrent: true,
            minDate: new Date(1945, 1 - 1, 1),
            changeYear: true
        });
    })
}
function dateVerify(objParrent) {
    if (typeof(objParrent) === 'undefined') {
        objParrent = $('body');
    }
    $(objParrent).find('input[date="isdate"]').each(function () {
        if ($(this).val()) {
            var value_save = $(this).attr('value_save');
            if (value_save) {
                arrvalue = $(this).val().split("/");
                arrvalue_save = value_save.split(" ");
                arrvalue_save = arrvalue_save[0].split("-");
                if ((arrvalue[0] == arrvalue_save[2]) && (arrvalue[1] == arrvalue_save[1]) && (arrvalue[2] == arrvalue_save[0])) {
                    $(this).val(value_save);
                }
            }
        }
    })
}
var set_checked_process = function (obj) {
    var TDPARENT = obj.parentNode.getElementsByTagName('TD');
    var td_check = '';
    for (var i = 0; i < TDPARENT.length; i++) {
        if (TDPARENT[i] === obj) {
            td_check = TDPARENT[i - 1];
            td_check.childNodes[0].checked = (td_check.childNodes[0].checked ? false : true);
            break;
        }
    }
    ;
}
/*Cho phep  1 file*/
function singleUpload(btnUpload, status) {
    new AjaxUpload(btnUpload, {
        action: baseUrl + '/main/ajax/uploadfile/',
        name: 'uploadfile',
        onSubmit: function (file, ext, sizemax) {
            if (sizemax > upload_max_filesize) {
                jAlert('Dung lượng file quá lớn, vui lòng chọn file có dung lượng nhỏ hơn ' + (upload_max_filesize / 1000) + 'Mb', 'Thông báo !')
                return false;
            }
            var oForm = $(btnUpload).closest("form");
            var sHidden = '<input type="hidden" id="hdn_file_deleted_list" name="hdn_file_deleted_list" />';
            if ($(oForm).find('#hdn_file_deleted_list').length === 0)
                $(oForm).append(sHidden);
            var href = btnUpload.parent().prev().find('a').attr('href');
            if (href != '' && href != undefined) {
                var filelist = $('#hdn_file_deleted_list').val();
                if (filelist != '') {
                    filelist = href + '!~@~!' + filelist;
                } else {
                    filelist = href;
                }
// Luu cac file old
                $('#hdn_file_deleted_list').val(filelist);
            }
            btnUpload.parent().prev().children('div[id="attach"]').text('Uploading...');
//status.text('Uploading...');
        },
        onComplete: function (file, response) {
//On completion clear the status
            btnUpload.parent().prev().children('div[id="attach"]').text('');
            status.text('');
//Add uploaded file to list
            if (response !== "error") {
                btnUpload.parent().parent().children().find('input[type="checkbox"]').attr('checked', true);
                var strId = btnUpload.parent().parent().children().find('input[type="checkbox"]').attr('id');
                var array = strId.split("_");
                if (array.length == 3)
                    change_multiplecheckbox_fileattach(array[1], strId);
                btnUpload.parent().parent().attr('checked', true);
                btnUpload.parent().prev().children('div[id="attach"]').html('<a href="' + response + '" alt="">' + file + '</a>').addClass('success');
                btnUpload.parent().parent().find('input[type="checkbox"]').attr('attachfile', response);
            } else {
                btnUpload.parent().prev().children('div[id="attach"]').html(file).addClass('error');
            }
        }
    });
}
function loadListType(arrList, param) {
    var arr = arrList[param];
    param = parseInt(param) + 1;
    if (eval(arr.arrName).length == 0) {
        var data = {
            typelist: arr.typelist
        };
        $.ajax({
            url: baseUrl + "/main/ajax/",
            type: "POST",
            cache: true,
            data: data,
            dataType: "json",
            success: function (array) {
                eval(arr.arrName + ' = array');

                // createObject(array,arr);
                if (arr.callback != 'undefined' && arr.callback != '') {
                    eval(arr.callback);
                }
                if (param < arrList.length)
                    loadListType(arrList, param);
            }
        });
    } else {
        var array = eval(arr.arrName);
        // createObject(array,arr);
        if (arr.callback != 'undefined' && arr.callback != '') {
            eval(arr.callback);
        }
        if (param < arrList.length)
            loadListType(arrList, param);
    }
}

function scrollTop(pos) {
    document.getElementById("b1")
    $('html,body').stop().animate({
        scrollTop: pos
    }, 300, 'easeInQuart');
}
// Lay danh sach da chon tu Table
function getvaluelist(idTable, idColumn) {
    var oTable = $('table#' + idTable);
    var valueList = '';
    oTable.find('input#' + idColumn + ':checked').each(function () {
        valueList += $(this).val() + ',';
    })
    // alert(valueList);
}
var set_checked_unit = function (obj) {
    var TDPARENT = obj.parentNode.getElementsByTagName('TD');
    var td_check = '';
    for (var i = 0; i < TDPARENT.length; i++) {
        if (TDPARENT[i] === obj) {
            td_check = TDPARENT[i - 1];
            td_check.childNodes[0].checked = (td_check.childNodes[0].checked ? false : true);
            break;
        }
    }
    ;
}
var set_check_all_owners = function (obj) {
    var checked = false;
    if (obj.value === '1') {
        checked = true;
    }
    for (var i = 0; i < document.getElementsByName('chk_onwer_code').length; i++) {
        document.getElementsByName('chk_onwer_code')[i].checked = checked;
    }
    ;
}
// Fix title cho cac man hinh view
function fixTitleView() {
    return;
    if ($('#pdtview').length == 0) {
        $('<div id="pdtview"></div>').insertAfter('div.fix_title_button');
        // alert(123);
    }
    ;
    var ctHeight = jQuery('div.fix_title_button').outerHeight();
    jQuery('div.fix_title_button').css({
        'width': jQuery('div#content').width(),
        'height': ctHeight,
        'position': 'fixed',
        'background-color': '#FFFFFF',
        'border-bottom': '1px solid #DDDDDD'
        // 'margin': '5px 2px',
    });
    jQuery('div#pdtview').css('padding-top', ctHeight + 10);
}
function callbackexpand(oForm) {
    events4(oForm);
    if ($(oForm).find('div.s11').is(':hidden')) {
        //$(oForm).find('div.s11').show();
        $(oForm).find('#detailSearch input:eq(0)').focus();
        // console.log($(oForm).find('.s1').width());
        $(oForm).find('div.s11').css(
            {
                'display': 'table-cell',
                'width': $(oForm).find('.s1').width(),
                'position': 'inline-table',
                'visibility': 'visible',
                'z-index': 9999,
                'top': $(oForm).find('.s2').position().top + $(oForm).find('.s2').height() + 2
            }
        );
    }
    else {
        $(oForm).find('.s4').focus();
        $(oForm).find('div.s11').css(
            {
                'display': 'none',
                'width': $(oForm).find('.s1').width(),
                'position': 'inline-table',
                'visibility': 'visible',
                'z-index': 9999,
                'top': $(oForm).find('.s2').position().top + $(oForm).find('.s2').height() + 2
            }
        );
    }
    putdatadetail(oForm);
}
function loadsearchexpand(pathxml, oForm, callback) {
    var oCurrent = $(oForm).find('.s5');
    if (oCurrent.attr('loaded') != 'true') {
        var data = {
            pathxml: pathxml
        };
        $.ajax({
            url: baseUrl + '/main/ajax/searchexpand/',
            type: 'POST',
            data: data,
            success: function (string) {
                $(oForm).find('#detailSearch').html(string);
                oCurrent.attr('loaded', 'true');
                events4(oForm);
                lddadepicker();
                callback.apply();
            }
        });
    }
}
// Load cac su kien cho man hinh tim kiem mo rong
function searchexpand(pathxml, oForm, callback) {
    if (typeof(callback) === 'undefined') {
        callback = function () {
        };
    }
    if (typeof(oForm) === 'undefined') {
        oForm = $('.s5').closest('form');
    }
    ;
    loadsearchexpand(pathxml, oForm, callback);
    $(oForm).find('.s5,.s4,.s5_1').unbind('click');
    $(oForm).find('.s5').click(function () {
        var oCurrent = $(this);
        if (oCurrent.attr('loaded') != 'true') {
            var data = {
                pathxml: pathxml
            };
            $.ajax({
                url: baseUrl + '/main/ajax/searchexpand/',
                type: 'POST',
                data: data,
                success: function (string) {
                    $(oForm).find('#detailSearch').html(string);
                    oCurrent.attr('loaded', 'true');
                    lddadepicker();
                    callbackexpand(oForm);
                    callback.apply();
                }
            });
        } else {
            callbackexpand(oForm);
        }
    });

    $(oForm).find('.s4').click(function () {
        $(oForm).find('div.s11').hide();
        $(oForm).find('.s2').addClass('box');
    });
    $(oForm).find('.s4').focus(function () {
        $(oForm).find('.s2').addClass('box');
    });
    $(oForm).find('.s4').blur(function () {
        $(oForm).find('.s2').removeClass('box');
    });
    shortcut.add("F4", function () {
        $(oForm).find('.s5').trigger('click');
    });
    $(oForm).find('.s5_1').click(function () {
        var oS4 = $(oForm).find('input[name="s4"]');
        oS4.val('');
        oS4.trigger('change');
    });
    $(document).bind("click", function (e) {
        if ($(oForm).find('div#search_expand').has(e.target).length == 0) {
            $(oForm).find('div.s11').hide();
        }
        ;
        if ($('div.filter1').has(e.target).length == 0 && $('tr.header td:eq(0)').has(e.target).length == 0) {
            $('div.filter1').hide();
        }
        ;
    });
}

function convertdataurl(string) {
    if (typeof(string) === 'undefined')
        return '';
    string = string.replace(/%C3%A0/g, "à");
    string = string.replace(/%C3%A1/g, "á");
    string = string.replace(/%E1%BA%A1/g, "ạ");
    string = string.replace(/%E1%BA%A3/g, "ả");
    string = string.replace(/%C3%A3/g, "ã");
    string = string.replace(/%C3%A2/g, "â");
    string = string.replace(/%E1%BA%A7/g, "ầ");
    string = string.replace(/%E1%BA%A5/g, "ấ");
    string = string.replace(/%E1%BA%AD/g, "ậ");
    string = string.replace(/%E1%BA%A9/g, "ẩ");
    string = string.replace(/%E1%BA%AB/g, "ẫ");
    string = string.replace(/%E1%BA%AB/g, "ẫ");
    string = string.replace(/%C4%83/g, "ă");
    string = string.replace(/%E1%BA%B1/g, "ằ");
    string = string.replace(/%E1%BA%AF/g, "ắ");
    string = string.replace(/%E1%BA%B7/g, "ặ");
    string = string.replace(/%E1%BA%B3/g, "ẳ");
    string = string.replace(/%E1%BA%B5/g, "ẵ");
    // string = string.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g,"a");
    string = string.replace(/%C3%80/g, "À");
    string = string.replace(/%C3%81/g, "Á");
    string = string.replace(/%E1%BA%A0/g, "Ạ");
    string = string.replace(/%E1%BA%A2/g, "Ả");
    string = string.replace(/%C3%83/g, "Ã");
    string = string.replace(/%C3%82/g, "Â");
    string = string.replace(/%E1%BA%A6/g, "Ầ");
    string = string.replace(/%E1%BA%A4/g, "Ấ");
    string = string.replace(/%E1%BA%AC/g, "Ậ");
    string = string.replace(/%E1%BA%A8/g, "Ẩ");
    string = string.replace(/%E1%BA%AA/g, "Ẫ");
    string = string.replace(/%C4%82/g, "Ă");
    string = string.replace(/%E1%BA%B0/g, "Ằ");
    string = string.replace(/%E1%BA%AE/g, "Ắ");
    string = string.replace(/%E1%BA%B6/g, "Ặ");
    string = string.replace(/%E1%BA%B2/g, "Ẳ");
    string = string.replace(/%E1%BA%B4/g, "Ẵ");
    // string = string.replace(/À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ/g,"a");
    string = string.replace(/%C3%A8/g, "è");
    string = string.replace(/%C3%A9/g, "é");
    string = string.replace(/%E1%BA%B9/g, "ẹ");
    string = string.replace(/%E1%BA%BB/g, "ẻ");
    string = string.replace(/%E1%BA%BD/g, "ẽ");
    string = string.replace(/%C3%AA/g, "ê");
    string = string.replace(/%E1%BB%81/g, "ề");
    string = string.replace(/%E1%BA%BF/g, "ế");
    string = string.replace(/%E1%BB%87/g, "ệ");
    string = string.replace(/%E1%BB%83/g, "ể");
    string = string.replace(/%E1%BB%85/g, "ễ");
    // string = string.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ.+/g,"e");
    string = string.replace(/%C3%88/g, "È");
    string = string.replace(/%C3%89/g, "É");
    string = string.replace(/%E1%BA%B8/g, "Ẹ");
    string = string.replace(/%E1%BA%BA/g, "Ẻ");
    string = string.replace(/%E1%BA%BC/g, "Ẽ");
    string = string.replace(/%C3%8A/g, "Ê");
    string = string.replace(/%E1%BB%80/g, "Ề");
    string = string.replace(/%E1%BA%BE/g, "Ế");
    string = string.replace(/%E1%BB%86/g, "Ệ");
    string = string.replace(/%E1%BB%82/g, "Ể");
    string = string.replace(/%E1%BB%84/g, "Ễ");
    // string = string.replace(/È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ.+/g,"e");

    string = string.replace(/%C3%AC/g, "ì");
    string = string.replace(/%C3%AD/g, "í");
    string = string.replace(/%E1%BB%8B/g, "ị");
    string = string.replace(/%E1%BB%89/g, "ỉ");
    string = string.replace(/%C4%A9/g, "ĩ");
    // string = string.replace(/ì|í|ị|ỉ|ĩ/g,"i");
    string = string.replace(/%C3%8C/g, "Ì");
    string = string.replace(/%C3%8D/g, "Í");
    string = string.replace(/%E1%BB%8A/g, "Ị");
    string = string.replace(/%E1%BB%88/g, "Ỉ");
    string = string.replace(/%C4%A8/g, "Ĩ");
    // string = string.replace(/Ì|Í|Ị|Ỉ|Ĩ/g,"i");
    string = string.replace(/%C3%B2/g, "ò");
    string = string.replace(/%C3%B3/g, "ó");
    string = string.replace(/%E1%BB%8F/g, "ỏ");
    string = string.replace(/%E1%BB%8D/g, "ọ");
    string = string.replace(/%C3%B5/g, "õ");
    string = string.replace(/%C3%B4/g, "ô");
    string = string.replace(/%E1%BB%93/g, "ồ");
    string = string.replace(/%E1%BB%91/g, "ố");
    string = string.replace(/%E1%BB%95/g, "ổ");
    string = string.replace(/%E1%BB%99/g, "ộ");
    string = string.replace(/%E1%BB%97/g, "ỗ");

    string = string.replace(/%C6%A1/g, "ơ");
    string = string.replace(/%E1%BB%9D/g, "ờ");
    string = string.replace(/%E1%BB%9B/g, "ớ");
    string = string.replace(/%E1%BB%9F/g, "ở");
    string = string.replace(/%E1%BB%A3/g, "ợ");
    string = string.replace(/%E1%BB%A1/g, "ỡ");
    // string = string.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ.+/g,"o");
    string = string.replace(/%C3%92/g, "Ò");
    string = string.replace(/%C3%93/g, "Ó");
    string = string.replace(/%E1%BB%8E/g, "Ỏ");
    string = string.replace(/%E1%BB%8C/g, "Ọ");
    string = string.replace(/%C3%95/g, "Õ");
    string = string.replace(/%C3%94/g, "Ô");
    string = string.replace(/%E1%BB%92/g, "Ồ");
    string = string.replace(/%E1%BB%90/g, "Ố");
    string = string.replace(/%E1%BB%94/g, "Ổ");
    string = string.replace(/%E1%BB%98/g, "Ộ");
    string = string.replace(/%E1%BB%96/g, "Ỗ");
    string = string.replace(/%C6%A0/g, "Ơ");
    string = string.replace(/%E1%BB%9C/g, "Ờ");
    string = string.replace(/%E1%BB%9A/g, "Ớ");
    string = string.replace(/%E1%BB%9E/g, "Ở");
    string = string.replace(/%E1%BB%A2/g, "Ợ");
    string = string.replace(/%E1%BB%A0/g, "Ỡ");
    //        string = string.replace(/Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ.+/g,"o");

    string = string.replace(/%C3%B9/g, "ù");
    string = string.replace(/%C3%BA/g, "ú");
    string = string.replace(/%E1%BB%A7/g, "ủ");
    string = string.replace(/%E1%BB%A5/g, "ụ");
    string = string.replace(/%C5%A9/g, "ũ");
    string = string.replace(/%C6%B0/g, "ư");
    string = string.replace(/%E1%BB%AB/g, "ừ");
    string = string.replace(/%E1%BB%A9/g, "ứ");
    string = string.replace(/%E1%BB%AD/g, "ử");
    string = string.replace(/%E1%BB%B1/g, "ự");
    string = string.replace(/%E1%BB%AF/g, "ữ");
    //        string = string.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g,"u");
    string = string.replace(/%C3%99/g, "Ù");
    string = string.replace(/%C3%9A/g, "Ú");
    string = string.replace(/%E1%BB%A6/g, "Ủ");
    string = string.replace(/%E1%BB%A4/g, "Ụ");
    string = string.replace(/%C5%A8/g, "Ũ");
    string = string.replace(/%C6%AF/g, "Ư");
    string = string.replace(/%E1%BB%AA/g, "Ừ");
    string = string.replace(/%E1%BB%A8/g, "Ứ");
    string = string.replace(/%E1%BB%AC/g, "Ử");
    string = string.replace(/%E1%BB%B0/g, "Ự");
    string = string.replace(/%E1%BB%AE/g, "Ữ");
    //        string = string.replace(/Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ/g,"u");
    string = string.replace(/%E1%BB%B3/g, "ỳ");
    string = string.replace(/%C3%BD/g, "ý");
    string = string.replace(/%E1%BB%B7/g, "ỷ");
    string = string.replace(/%E1%BB%B5/g, "ỵ");
    string = string.replace(/%E1%BB%B9/g, "ỹ");
    //        string = string.replace(/ỳ|ý|ỵ|ỷ|ỹ/g,"y");
    string = string.replace(/%E1%BB%B2/g, "Ỳ");
    string = string.replace(/%C3%9D/g, "Ý");
    string = string.replace(/%E1%BB%B6/g, "Ỷ");
    string = string.replace(/%E1%BB%B4/g, "Ỵ");
    string = string.replace(/%E1%BB%B8/g, "Ỹ");
    //        string = string.replace(/Ỳ|Ý|Ỵ|Ỷ|Ỹ/g,"y");
    string = string.replace(/%C4%91/g, "đ");
    string = string.replace(/%C4%90/g, "Đ");
    string = string.replace(/\+/g, " ");
    string = string.replace(/%2F/g, "/");
    string = string.replace(/%5C/g, "\\");

    string = string.replace(/%60/g, "`");
    string = string.replace(/%40/g, "@");
    string = string.replace(/%23/g, "#");
    string = string.replace(/%24/g, "$");
    string = string.replace(/%26/g, "&");
    string = string.replace(/%3B/g, ";");
    string = string.replace(/%3F/g, "?");
    string = string.replace(/%3E/g, ">");
    string = string.replace(/%3C/g, "<");
    string = string.replace(/%7C/g, "|");
    string = string.replace(/%22/g, "\"");
    string = string.replace(/%2C/g, ",");
    string = string.replace(/%3A/g, ":");
    return string;
}
// Day du lieu search detail len thanh tim kiem
function putdatadetail(oForm) {
    events4(oForm);
    // var dataexpand = $('')
    $(oForm).find('#detailSearch div#Bottom_contentXml').change(function () {
        var oFormThis = $(this).closest('form');
        var data = $(oFormThis).find('#detailSearch :input').serialize();
        var arrData = data.split('&');
        var sID = '', sValue = '', sTemp = '', arrTemp = [];
        var sStringList = '', delimitor = '~!@#$';
        for (var i = 0; i < arrData.length; i++) {
            sTemp = arrData[i];
            arrTemp = sTemp.split('=');
            sID = arrTemp[0];
            sValue = arrTemp[1];
            if (sValue != '') {
                if ($(oFormThis).find('#' + sID).attr('column_name') === 'sDataTemp') {
                    sStringList += ' ' + convertdataurl(sValue) + ' ';
                } else {
                    if ($(oFormThis).find('#' + sID).get(0).tagName === 'SELECT')
                        sValue = getSelectTextById(sID, sValue);
                    sStringList += sID + ':{' + convertdataurl(sValue) + '} '; // + delimitor;
                }
            }
            ;
        }
        ;
        sStringList = sStringList.substr(0, sStringList.length - 1);
        $(oFormThis).find('input[name="s4"]').unbind('change');
        $(oFormThis).find('input[name="s4"]').val(sStringList);
        if (sStringList != '') $(oFormThis).find('div.s5_1').show();
        else $(oFormThis).find('div.s5_1').hide();
        events4(oFormThis);
    })
}
// Load su kien tren thanh tim kiem
function events4(oForm) {
    $(oForm).find('input[name="s4"]').unbind('change');
    $(oForm).find('input[name="s4"]').change(function () {
        if ($(this).val() != '') $(oForm).find('div.s5_1').show();
        else $(oForm).find('div.s5_1').hide();
        retoredataurl($(this).val(), oForm);
    })
    /*$('input[name="s4"]').bind('change',function(){
     retoredataurl($(this).val());
     });*/
}
// retore lai du lieu tren thanh tim kiem vao form search detail
function retoredataurl(string, oForm) {
    if (string == '') {
        $('#detailSearch option:selected').each(function () {
            if ($(this).val() != '')
                $(this).removeAttr('selected');
        })
        $('#detailSearch input').each(function () {
            $(this).val('');
        })
        return false;
    }
    ;
    var arrData = string.split('}');
    var sID = '', sValue = '', arrTemp = [], sTemp = '';
    $(oForm).find('div#detailSearch :input').val('');
    for (var i = 0; i < arrData.length; i++) {
        sTemp = arrData[i];
        sTemp = sTemp.replace(/(^[\s]+|[\s]+$)/g, '');
        arrTemp = sTemp.split(':{');
        if (arrTemp.length === 1) {
            $(oForm).find('div#detailSearch :input[column_name="sDataTemp"]').val(sTemp);
        } else {
            sID = arrTemp[0];
            sValue = arrTemp[1];
            if ($(oForm).find('#' + sID).get(0).tagName === 'SELECT')
                sValue = getSelectIDByText(sID, sValue);
            $(oForm).find('#' + sID).val(sValue);
        }
    }
    ;
}
function triggerloadlist(oForm) {
    if (typeof(oForm) === 'undefined') {
        oForm = $('.s4').closest('form');
    }
    ;
    putdatadetail(oForm);
    if ($(oForm).find('input[name="s4"]').is(':focus')) {
        $(oForm).find('input[name="s4"]').trigger('change');
    } else {
        $(oForm).find('#detailSearch div#Bottom_contentXml').trigger('change');
    }
    $(oForm).find('div.s11').hide();
    $(oForm).find('.filter1').hide();
}
/*
 //sUnFileAttachList: Xoa nhung file khong chon xoa trong csdl
 //sUnDocTypeList:

 */
function getDataAttachFile(oForm, option) {
    //Search file dinh kem
    //var doctype = $(oForm).find('div[type="ATTACHFILE"]').attr('doctype');
    var sFileAttachList = '', sUnFileAttachList = '', sDocTypeList = '', arrData = [], sDelimitor = '!~~!',
     locationList = '', doctype ='',
     item_delete ='';
    $('div[type="ATTACHFILE"]', oForm).each(function () {
        doctype = $(this).attr('doctype');
        $('p.upload_complete', this).each(function () {
            sDocTypeList += doctype + sDelimitor;
            sFileAttachList += $(this).attr('file_name') + sDelimitor;
            locationList += $(this).attr('old') + sDelimitor;

        })
        item_delete = $('#hdn_delete_file_upload', $(this)).val()
        if (item_delete) 
            sUnFileAttachList += item_delete + sDelimitor;

    })

    //Search tai lien kem theo hs
    $('div[type="ATTACHFILE_CHECKLIST"]', self.frmUpdate).each(function () {
        var elAttach = $(this),
            elCheckbox = $(this).closest('tr').find('[name="chk_multiple_checkbox"]');
        if ($(elCheckbox).is(':checked')) {
            doctype = $(elCheckbox).val();

            $('p.upload_complete', elAttach).each(function () {
                sDocTypeList += doctype + sDelimitor;
                sFileAttachList += $(this).attr('file_name') + sDelimitor;
                locationList += $(this).attr('old') + sDelimitor;

            })
        }  
        item_delete = $('input[name="hdn_delete_file_upload"]', elAttach).val();
        if (item_delete) 
            sUnFileAttachList += item_delete + sDelimitor;

    })

    sDocTypeList = sDocTypeList.substr(0, sDocTypeList.length - sDelimitor.length);
    sFileAttachList = sFileAttachList.substr(0, sFileAttachList.length - sDelimitor.length);
    sUnFileAttachList = sUnFileAttachList.substr(0, sUnFileAttachList.length - sDelimitor.length);
    locationList = locationList.substr(0, locationList.length - sDelimitor.length);

    if (option === 1) {
        var string = ''
        string += '&sFileAttachList=' + sFileAttachList;
        string += '&sDocTypeList=' + sDocTypeList;
        string += '&sUnFileAttachList=' + sUnFileAttachList;
        string += '&locationList=' + locationList;
        string += '&sDelimitor=' + sDelimitor;
        return string;
    } else {
        return arrData = {
            sFileAttachList: sFileAttachList,
            sDocTypeList: sDocTypeList,
            sUnFileAttachList: sUnFileAttachList,
            locationList: locationList,
            sDelimitor: sDelimitor
        }
    }
    //
}
var set_checked_multi = function (obj) {
    var oTable = $(obj).parent().parent().parent();
    var oCheckbox = $(obj).prev().find('input.checkvaluemark');
    if ($(oCheckbox).is(':checked')) {
        $(oCheckbox).attr('checked', false);
    } else {
        $(oCheckbox).attr('checked', true);
    }
    var id = $(oCheckbox).attr('id'), sValue = '';
    // Duyet update
    oTable.find('input#' + id + ':checked').each(function () {
        sValue += $(this).val() + ',';
    })
    sValue = sValue.substr(0, sValue.length - 1);
    $(oTable).parent().prev().find('input#' + id).val(sValue);
    // alert($(oTable).parent().prev().find('input#'+id).val());
}
var set_checked_checbox = function (obj) {
    var id = $(obj).attr('id'), sValue = '';
    var oTable = $(obj).parent().parent().parent().parent();
    // Duyet update
    oTable.find('input#' + id + ':checked').each(function () {
        sValue += $(this).val() + ',';
    })
    sValue = sValue.substr(0, sValue.length - 1);
    $(oTable).parent().prev().find('input#' + id).val(sValue);
}
var jquery_show_row_selected = function (tablename) {
    var oTable = $('table#' + tablename);
    oTable.find('td').parent().hide();
    oTable.find('td input:checked').parent().parent().show();
}
//Lay text trong option select
function getSelectedText(elementId) {
    var elt = document.getElementById(elementId);
    if (elt.selectedIndex == -1)
        return null;
    return elt.options[elt.selectedIndex].text;
}
//
function getSelectTextById(elementId, value) {
    return $('select#' + elementId).find('option#' + value).attr('name');
}
function getSelectIDByText(elementId, text) {
    return $('select#' + elementId).find('option[name="' + text + '"]').attr('id');
}
function checkbox_all_item_table(obj, nameck) {
    var oBody = $(obj).parent().parent().parent();
    if ($(obj).is(':checked')) {
        $(oBody).find('input[type="checkbox"][name="' + nameck + '"]').each(function () {
            if ($(this).is(':disabled') === false) {
                $(this).attr('checked', true);
                $(this).parent().parent().addClass('selected');
            }
            ;
        })
    } else {
        $(oBody).find('input[type="checkbox"][name="' + nameck + '"]').each(function () {
            $(this).attr('checked', false);
            $(this).parent().parent().removeClass('selected');
        })
    }
}
function fixTitleModal(oForm) {
    var oFormSearch = $(oForm).find('div.searh-fixed');
    var oTableList = $(oForm).find('tr.header')
    var ctHeight = $(oFormSearch).outerHeight();
    var top = $(oForm).position().top;
    var numTd = $(oTableList).find('td').length;
    $(oFormSearch).css({
        'width': $(oForm).width(),
        'height': ctHeight,
        'position': 'fixed'
    })
    var oTr3 = $(oForm).find('tr:eq(3)');
    for (i = 0; i < numTd; i++) {
        widthTd = $(oTr3).find('td:eq(' + i + ')').width();
        $(oTableList).find('td:eq(' + i + ')').css("width", widthTd);
    }
    var tabledatarowadd = 0, margintop = 0;
    switch ($(oForm).attr('id')) {
        case 'frm10_1003_100308':
        case 'frm04_0404_index':
            tabledatarowadd = ctHeight + $(oTableList).outerHeight();
            margintop = ctHeight;
            break;
        case 'frm10_1002_100204':
            tabledatarowadd = $(oTableList).outerHeight();
            margintop = 0;
            break;

    }
    $(oForm).find('#table-data-rowadd').css('height', tabledatarowadd);
    $(oTableList).css({
        'position': 'fixed',
        'margin-top': margintop,
        'top': 'auto',
        'width': $(oForm).width()
    })
    // $(oForm).find('.searh-fixed').css('z-index',0);
    $(oTableList).removeClass('fixTableHeaer');
    // Bottom_contentXml
    $(oForm).parent().css('padding-top', '0');
}
var scrollToElement = function (element) {
    var etop = $(element).offset().top - 138;
    $('html, body').animate({
        scrollTop: etop
    }, 300);
}
function removeClassName(elem, name) {
    var remClass = elem.className;
    var re = new RegExp('(^| )' + name + '( |$)');
    remClass = remClass.replace(re, '$1');
    remClass = remClass.replace(/ $/, '');
    elem.className = remClass;
}
/*function toggle(one)
 {
 var o=document.getElementById(one);

 o.style.display=(o.style.display=='none')?'block':'none';
 }*/
function viewtempopublic(obj, fk_record) {
    /*// Check modal_tempo
     if ($('div.modal_tempo').length == 0) {
     $(body).append('<div id="modal_tempo" class="modal_tempo"></div>');
     };*/
    var url = baseUrl + '/main/ajax/viewtempo/';
    var myClass = this;
    var oCheckbox = $(obj).parent().parent().find('input[type="checkbox"][name="chk_item_id"]');
    $(oCheckbox).attr('checked', true);
    showloadpage('Đang tải dữ liệu...');
    var data = {
        pkrecord: fk_record
    };
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        success: function (string) {
            $('div.modal_tempo').html(string);
            hideloadpage();
            shortcut.remove('Enter');
            $(".modal_tempo").dialog("open");
        }
    });
    return !history.pushState;
}
function loadListSequential() {
    var arrList = new Array;
    if (typeof(LOAI_DON) === 'undefined') {
        LOAI_DON = new Array();
    }
    if (typeof(HINH_THUC_XL_DON) === 'undefined') {
        HINH_THUC_XL_DON = new Array();
    }
    if (typeof(QUAN_HUYEN) === 'undefined') {
        QUAN_HUYEN = new Array();
    }
    if (typeof(PHUONG_XA) === 'undefined') {
        PHUONG_XA = new Array();
    }
    if (typeof(THAM_QUYEN_XU_LY) === 'undefined') {
        THAM_QUYEN_XU_LY = new Array();
    }
    if (typeof(HINH_THUC_XL_DON) === 'undefined') {
        HINH_THUC_XL_DON = new Array();
    }
    if (typeof(TRANG_THAI_XU_LY) === 'undefined') {
        TRANG_THAI_XU_LY = new Array();
    }
    if (typeof(MAU_BIEU_THEO_HTXL) === 'undefined') {
        MAU_BIEU_THEO_HTXL = new Array();
    }
    arrList['0'] = {
        typelist: 'LOAI_DON',
        arrName: 'LOAI_DON'
    };
    arrList['1'] = {
        typelist: 'HINH_THUC_XL_DON',
        arrName: 'HINH_THUC_XL_DON'
    };

    arrList['2'] = {
        typelist: 'QUAN_HUYEN',
        arrName: 'QUAN_HUYEN'
    };

    arrList['3'] = {
        typelist: 'PHUONG_XA',
        arrName: 'PHUONG_XA'
    };
    arrList['4'] = {
        typelist: 'THAM_QUYEN_XU_LY',
        arrName: 'THAM_QUYEN_XU_LY'
    };
    arrList['5'] = {
        typelist: 'HINH_THUC_XL_DON',
        arrName: 'HINH_THUC_XL_DON'
    };
    arrList['6'] = {
        typelist: 'TRANG_THAI_XU_LY',
        arrName: 'TRANG_THAI_XU_LY'
    };
    arrList['7'] = {
        typelist: 'MAU_BIEU_THEO_HTXL',
        arrName: 'MAU_BIEU_THEO_HTXL'
    };
    var count = arrList.length;
    if (count > 0)
        loadListType(arrList, 0);
}
function getIdeaClassifyByDate(idProcessDate, idAppointedDate) {
    var RW03 = parseDate($('#' + idProcessDate + '').val()).getTime();
    var R09 = parseDate($('#' + idAppointedDate + '').val()).getTime();
    if (R09 < RW03) {
        jAlert('Hạn xử lý không được nhỏ hơn ngày xử lý!', 'Error');
        $('#' + idAppointedDate + '').focus();
        $('#' + idAppointedDate + '').val('');
        return false;
    }
    getIdeaClassify(idAppointedDate);
}
var toggleradio = function (obj) {
    if ($(obj).prev().is(':checked')) {
        $(obj).prev().attr('checked', false);
    } else {
        $(obj).prev().attr('checked', true);
    }
    $(obj).prev().trigger('change');
}

function chosen_selectvalue(objname) {
    var value = $('#cssl_' + objname).val();
    $('#' + objname).val(value);
}
function selectplayvideo(objelement, sActionUrl) {
    var htmlvideo = '';
    htmlvideo = '<embed width="200" height="134" type="application/x-shockwave-flash" src="' + baseUrl + '/public/other/player.swf" name="mediaplayer" quality="high" allowfullscreen="true" flashvars="autostart=false&amp;width=180&amp;height=120&amp;file=' + sActionUrl + '&amp;image=' + baseUrl + '/public/images/media_mp3.jpg" id="mediaplayer">';
    $('#media').html(htmlvideo);
    $('.link_video').css('font-weight', 'normal');
    $('.link_video').css('color', '#001376');
    $(objelement).css('font-weight', 'bold');
    $(objelement).css('color', 'red');
}
function multiradiobutton_selectvalue(objname, value) {
    $('#' + objname).val(value);
}
//Xu ly su kien khi kick vao radio loc du lieu
function toggleBtn(objActionBtn, valueCompare, attributeName, attributeValue) {
    $('input[name="namemodeswitch"]').change(function () {
        if ($('input[name="namemodeswitch"]').is(':checked')) {
            var objChkRadio = $(this).val();
            if ($.inArray(objChkRadio, valueCompare) >= 0)
                $(objActionBtn).attr(attributeName, attributeValue);
            else
                $(objActionBtn).removeAttr(attributeName, attributeValue);
        }
    })
    if ($('input[name="namemodeswitch"]').is(':checked')) {
        var objChkRadio = $('input[name="namemodeswitch"]:checked').val();
        if ($.inArray(objChkRadio, valueCompare) >= 0)
            $(objActionBtn).attr(attributeName, attributeValue);
        else
            $(objActionBtn).removeAttr(attributeName, attributeValue);
    }
}

var togglelabel = function (obj) {
    /*  $('.normal_radio').removeAttr('style active').attr('style', 'cursor:pointer;').removeClass('active');
     $(obj).attr('style', 'color:red;').attr('active', 'true').addClass('active');
     $(obj).attr('active', 'true').trigger('change');*/
}
function toggleLabel(objActionBtn, valueCompare, attributeName, attributeValue) {
    /* $('#tab-menu li').change(function () {
     if ($(this).hasClass('active')) {
     if ($.inArray($(this).attr('v_value'), valueCompare) >= 0)
     $(objActionBtn).attr(attributeName, attributeValue);
     else
     $(objActionBtn).removeAttr(attributeName, attributeValue);
     }
     $(this).closest('.button-link-container').css('height',31)
     })
     var oSwitchData = $('#tab-menu li');
     $(oSwitchData).each(function () {
     if ($(this).hasClass('active')) {
     if ($.inArray($(this).attr('v_value'), valueCompare) >= 0)
     $(objActionBtn).attr(attributeName, attributeValue);
     else
     $(objActionBtn).removeAttr(attributeName, attributeValue);
     }
     });*/
}
// Lay gia trị cua 1 phan tu theo 1 chi so trong mang
function getItemAttrByIndex(arrIn, valueIn, indexCompare, indexOut) {
    for (var i = 0; i < arrIn.length; i++) {
        if (arrIn[i][indexCompare] == valueIn) {

            return arrIn[i][indexOut];
        }
    }
    return '';
}
// Xu ly lay gia tri khi check item multiplecheckbox_fileattach
function change_multiplecheckbox_fileattach(idvalue, iditem) {
    var _stvalue = $('#' + idvalue).val();
    var _stitemvalue = $('#' + iditem).val();
    if ($('#' + iditem).is(':checked')) {
        var contains = (_stvalue.indexOf(_stitemvalue + ',') > -1);
        if (contains == false) {
            _stvalue = _stvalue + _stitemvalue + ',';
            $('#' + idvalue).val(_stvalue);
        }
    } else {
        _stvalue = _stvalue.replace(_stitemvalue + ',', '');
        $('#' + idvalue).val(_stvalue);
    }
}
// Su kien click menu user
var _clickprofileuser = function (obj) {
    var top = jQuery(obj).position().top, left = jQuery(obj).position().left, width = jQuery('#profileuser').width(), liprofile = jQuery('li#header_profile').width();
    left = left - width + liprofile;
    jQuery('#profileuser').toggle();
    if (jQuery('#profileuser').is(':hidden') === false) {
        jQuery(obj).addClass('selected');
    }
    ;
    jQuery('#profileuser').css('position', 'fixed');
    jQuery('#profileuser').css('visibility', 'visible');
    jQuery('#profileuser').css('z-index', '110');
    jQuery('#profileuser').css('top', top + 28);
    jQuery('#profileuser').css('left', left);
}
var _clickauthozired = function (obj) {
    var top = $(obj).position().top + $('#profileuser').outerHeight() - 10, left = $('#profileuser').position().left, width = $('#infor_authozired').width(), liprofile = $('li#header_profile').width();
    left = left - width - 10;
    // $('#infor_authozired').toggle();
    if ($('#infor_authozired').is(':hidden') === false) {
        $(obj).addClass('inforselected');
    } else {
        $(obj).removeClass('inforselected');
    }
    $('#infor_authozired').css({
        'position': 'fixed',
        'visibility': 'visible',
        'z-index': 110,
        'top': top,
        'left': left
    })
}
var _setauthozireduser = function (obj) {
    var username = '';
    jQuery('#profileuser').toggle();
    // $('#infor_authozired').toggle();
    var uid = jQuery(obj).val(), url = jQuery(obj).attr('url');
    // alert(uid);return false;
    var urlredirect = baseUrl + '/' + url;
    urlredirect = urlredirect.replace(/\/\//g, "\/");
    if (uid != '') {
        Set_Cookie('uidauthorized', uid, '', '/', '', '');
        Set_Cookie('urlredirect', url, '', '/', '', '');
    } else {
        Set_Cookie('uidauthorized', '', '', '/', '', '');
        Set_Cookie('urlredirect', '', '', '/', '', '');
    }
    var data = {
        uid: uid,
        urlredirect: urlredirect
    };
    jQuery.ajax({
        url: baseUrl + '/main/ajax/setauthozired/',
        type: 'POST',
        data: data,
        success: function (string) {
            window.location.href = urlredirect;
        },
        error: function () {
            jAlert('Có lỗi xảy ra', 'Error Dialog');
        }
    });
}
var _resetauth = function (obj) {

}
var _clickwindows = function (e) {
    /*if ($('#profileuser').has(e.target).length == 0 && $('#infor_authozired').has(e.target).length == 0 && $('#header_profile').has(e.target).length == 0){
     $('#profileuser').hide();
     $('#header_profile').removeClass('selected');
     }
     if ($('#infor_authozired').has(e.target).length == 0 && $('#authozired').has(e.target).length == 0) {
     $('#infor_authozired').hide();
     $('#authozired').removeClass('selected');
     };*/

    if (!jQuery(e.target).is('#profileuser *,#infor_authozired *,#header_profile')) {
        jQuery('#profileuser').hide();
        jQuery('#header_profile').removeClass('selected');
    }
    if (!jQuery(e.target).is('#infor_authozired *,#authozired')) {
        jQuery('#infor_authozired').hide();
        jQuery('#authozired').removeClass('selected');
    }
}
var _functionauthozired = function (obj) {
    var data = {
        eventid: $(obj).attr('eventid')
    };
    var urlredirect = baseUrl + '/public/../' + $(obj).attr('url');
    $.ajax({
        url: baseUrl + '/main/ajax/functionauthozired/',
        type: 'POST',
        data: data,
        success: function (string) {
            window.location.href = urlredirect;
        },
        error: function () {
            jAlert('Có lỗi xảy ra', 'Error Dialog');
        }
    });
}
var _readyfunctionload = function () {
    var uidauthorized = Get_Cookie('uidauthorized'), urlredirect = Get_Cookie('urlredirect');
    if (uidauthorized != null) {
        jQuery('input[name="function_authozired"][value="' + uidauthorized + '"][url="' + urlredirect + '"]').attr('checked', true);
        jQuery('input[name="function_authozired"][value="' + uidauthorized + '"][url="' + urlredirect + '"]').next().attr('style', 'color:red;');
    }
    jQuery('#header-menu').disableSelection();
    jQuery('#header_profile').click(function () {
        _clickprofileuser(jQuery(this));
    })
    jQuery('#authozired').click(function () {
        _clickauthozired(jQuery(this));
    })
    //
    jQuery('input[name="function_authozired"]').change(function () {
        _setauthozireduser(jQuery(this));
    })
    jQuery('#reset_auth').click(function () {
        _setauthozireduser(jQuery(this));
    })
}
function reloadurl(url) {
    window.location.href = url;
}

// Luu su thay doi
var savechangepass = function () {
    var url = window.location.protocol + '//' + window.location.host + '/login/index.php';
    if ($('form#frmchangepass').valid()) {
        var data = {
            hdnaction: 'changepass',
            action: 'changepass',
            txtOldpass: MD5($('#txtOldpass').val()),
            txtNewpass: MD5($('#txtNewpass').val())
        };
    } else {
        return false;
    }
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function (array) {
            jAlert(array['response'], 'Thay đổi mật khẩu');
            if (array['flag']) {
                $("#modal_changepas").dialog("close");
            }
        }
    });
}
var _changepass = function (url) {
    var url = window.location.protocol + '//' + window.location.host + '/login/index.php';
    showloadpage('Đang tải dữ liệu...');
    var data = {action: 'changepass'};
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        success: function (string) {
            $("#modal_changepas").html(string);
            hideloadpage();
            $("#modal_changepas").dialog("open");
        }
    });
}

var contentchangepass = function () {
    createModalDialog("div.modal_changepas", function () {
        $("div.modal_changepas").dialog({
            autoOpen: false,
            title: 'THAY ĐỔI MẬT KHẨU',
            modal: true,
            resizable: true,
            width: 650,
            height: 250,
            autoResize: false,
            dialogClass: 'dialog-class',
            draggable: true,
            overlay: {
                opacity: 0.5,
                background: 'blue'
            },
            buttons: [
                {
                    text: "Lưu thay đổi",
                    click: function () {
                        savechangepass();
                    }
                },
                {
                    text: "Quay lại",
                    click: function () {
                        $(this).dialog("close");
                    }
                }
            ],
            open: function () {
                $('body').css('overflow', 'hidden');
                shortcut.remove('Enter');
            },
            close: function () {
                $('body').css('overflow', 'inherit');
                shortcut.remove('Enter');
                shortcut.add("Enter", function () {
                    myClass.loadList();
                });
            }
        });
    })
}
function getDateddmmyyyy() {
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //January is 0!
    var yyyy = today.getFullYear();
    if (dd < 10) {
        dd = '0' + dd
    }
    if (mm < 10) {
        mm = '0' + mm
    }
    var today = dd + '/' + mm + '/' + yyyy;
    return today;
}

var checkexit = function (arrayIn, valueIn) {
    for (var i = 0; i < arrayIn.length; i++) {
        if (arrayIn[i] === valueIn) return true;
    }
    return false;
}
function roundToTwo(num) {
    return +(Math.round(num + "e+2") + "e-2");
}
function staff_devide_by_district() {
    // alert($('select#ten_cb').val());
    $('input#list_code_update').val($('select#ten_cb').val());
    $('input#list_name_update').val($('select#ten_cb').find(':selected').attr('name'));
}
function viewtempo(obj) {

    if(!$('.modal_tempo').is(':ui-dialog')) {
        $('#main').append('<div id="modal_tempo" class="modal_tempo"></div>')

        createModalDialog(".modal_tempo", function () {
            $(".modal_tempo").dialog({
                autoOpen: false,
                title: 'THÔNG TIN ĐƠN THƯ',
                modal: true,
                resizable: true,
                width: document.documentElement.clientWidth * 0.9,
                height: document.documentElement.clientHeight * 0.8,
                autoResize: false,
                dialogClass: 'dialog-class',
                draggable: true,
                buttons: [
                    {
                        text: "Quay lại",
                        click: function () {
                            $(this).dialog("close");
                        }
                    }
                ]
            });
        })
    } 

    var url = baseUrl + '/main/ajax/viewtempo/',
        oCheckbox = $(obj).parent().parent().find('input[name="chk_item_id"]');
    $(oCheckbox).attr('checked', true)
    var pkrecord = $(oCheckbox).val()
    $.ajax({
        url: url,
        type: 'POST',
        data: {pkrecord: pkrecord},
        success: function (string) {
            $('div.modal_tempo').html(string).dialog("open");
            hideloadpage();
        },
        beforeSend: showloadpage()
    });
    return !history.pushState;
}
var dropDownDyamic = function (form, GroupSelectBox, SelectBox, listtype, TagGroup, textdefault, valueselect) {
    var url = baseUrl + '/main/ajax/dropdowndyamic/';
    data = {
        GroupSelectBox: $(form).find(' #' + GroupSelectBox).val(),
        listtype: listtype,
        TagGroup: TagGroup,
        textdefault: textdefault,
        valueselect: valueselect
    };
    $(form).find(' #' + SelectBox).load(url, data, function () {
        $(form).find(' select#' + SelectBox).trigger("liszt:updated");
    });
}
function eventDropDown(oForm) {
    var v_R30 = $(oForm).find('#noi_gui').val();
    dropDownDyamic(oForm, 'tham_quyen', 'noi_gui', 'HINH_THUC_XL_DON', 'tham_quyen', '--- Chọn Hình thức xử lý ---', v_R30);
    $('#tham_quyen').change(function () {
        dropDownDyamic(oForm, 'tham_quyen', 'noi_gui', 'HINH_THUC_XL_DON', 'tham_quyen', '--- Chọn Hình thức xử lý ---', v_R30);
    })
}
function toPascalCase(str) {
    var arr = str.split(/\s|_/);
    for (var i = 0, l = arr.length; i < l; i++) {
        arr[i] = arr[i].substr(0, 1).toUpperCase() +
        (arr[i].length > 1 ? arr[i].substr(1).toLowerCase() : "");
    }
    return arr.join(" ");
}
function printBM(obj) {
    var url = baseUrl + '/main/ajax/printBM';
    var myClass = this;
    var data = {
        kieuin: 'doc'
    };
    var count = 0;
    var oForm = $(obj).closest("form");
    // alert(codetemp);

    var pkrecord = '';
    $(obj).parent().parent().parent().parent().find('input[type="checkbox"][name="chk_item_id"]:checked').each(function () {
        pkrecord = $(this).val();
        count++;
    })
    // Chua chon cai nao
    if (count == 0) {
        jAlert('Bạn chưa chọn chọn 1 hồ sơ để in', 'Thông báo');
        return false;
    }
    ;
    // Nhieu hon 1
    if (count > 1) {
        jAlert('Bạn chỉ được chọn 1 hồ sơ để in', 'Thông báo');
        return false;
    }
    ;
    //alert(count);
    data.pkrecord = pkrecord;

    open_load_data_process();
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        success: function (string) {
            close_load_data_process();
            open_modal_export_file('doc', string);
        }
    });
    return !history.pushState;
}

/*
 *   Author: TungLX
 *   Date: 31/05/2014
 *   Cap nhat ket qua xu ly don thu
 */
function petitionProcessUpdate(obj, myClass) {
    var url = baseUrl + '/main/ajax/petitionProcessUpdate';
    // var myClass = this;
    var count = 0;
    var pkrecord = '';
    var objcur = $(obj).attr('frm_field');
    $(obj).parent().parent().parent().parent().find('input[type="checkbox"][name="chk_item_id"]:checked').each(function () {
        pkrecord = $(this).val();
        count++;
    })
    // Chua chon cai nao
    if (count == 0) {
        jAlert('Bạn chưa chọn chọn 1 hồ sơ để theo dõi xử lý', 'Thông báo');
        return false;
    }
    ;
    // Nhieu hon 1
    if (count > 1) {
        jAlert('Bạn chỉ được chọn 1 hồ sơ để theo dõi xử lý', 'Thông báo');
        return false;
    }
    ;
    //alert(count);
    var data = {pkrecord: pkrecord};
    data.pkrecord = pkrecord;
    var data = {pkrecord: pkrecord};
    showloadpage('Đang tải dữ liệu...');
    $.ajax({
        url: url, type: 'POST', data: data, success: function (string) {
            $('div#UpdateFrm').html(string);
            var oSwitchData = $('label.normal_radio');
            $(oSwitchData).each(function () {
                if ($(this).attr('active')) {
                    var objChkRadio = $(this).attr('value');
                    if (objChkRadio == 'DAGQ_10') {
                        $('div[id="id_object_3"][class="normal_label"]').hide();
                        $('input[name="chkKQXL"][type="checkbox"][id="RUT_DON_KHIEU_NAI"]').parent().attr('style', 'display:none');
                        $('div[id="id_object_3"][class="normal_label"]').attr('style', 'display:none');
                    }
                }
            });
            // alert(objcur);
            if (objcur == '030109') {
                $('input#KET_THUC_XU_LY').hide();
                $('input#KET_THUC_XU_LY').next().hide();
            }
            // else if (objcur == '031001'){
            //     $('input#KET_THUC_XU_LY').parent().hide();
            // }
            $('div#UpdateFrm').show();
            $('div#IndexFrm').hide();
            hideloadpage();
            lddadepicker();
            $('.clback').click(function () {
                myClass.loadList();
                $('#UpdateFrm').hide();
                $('#IndexFrm').show();
            })
            $('.clsave').click(function () {
                savePetitionProcess(this, myClass);
            })
            $('input[id="RUT_DON_KHIEU_NAI"][name="chkKQXL"]').prev().attr('style', 'width:15%; padding-left:5.5%');
            $('input[id="KET_THUC_XU_LY"][name="chkKQXL"]').prev().attr('style', 'width:5%; padding-left:3%');
            $('input[id="KET_THUC_XU_LY"][name="chkKQXL"]').change(function () {
                if ($(this).is(':checked')) {
                    $('input[id="RUT_DON_KHIEU_NAI"][name="chkKQXL"]').attr('disabled', 'disabled');
                }
                else {
                    $('input[id="RUT_DON_KHIEU_NAI"][name="chkKQXL"]').removeAttr('disabled');
                }
            })
            $('input[id="RUT_DON_KHIEU_NAI"][name="chkKQXL"]').change(function () {
                if ($(this).is(':checked')) {
                    $('input[id="KET_THUC_XU_LY"][name="chkKQXL"]').attr('disabled', 'disabled');
                }
                else {
                    $('input[id="KET_THUC_XU_LY"][name="chkKQXL"]').removeAttr('disabled');
                }
            })

            shortcut.remove('Enter');
        }
    });
    return !history.pushState;
}
/*
 *   Author: TungLX
 *   Date: 31/05/2014
 *   Luu ket qua xu ly don thu
 */
function savePetitionProcess(obj, myClass) {
    if (myClass.loadding) return false;
    var url = baseUrl + '/main/ajax/savepetitionprocess';

    var oForm = $("form#frm_petition_process");
    var sData = getDataAttachFile(oForm, 1);
    var id_checked = $(oForm).find("input[type='checkbox'][name='chkKQXL']:checked").attr("id");
    var pkrecord = $('#pkrecord').val();
    var actback = $(obj).attr('action');
    // alert(sData);
    var pkrecord = $('form#frm_petition_process #pkrecord').val();

    // alert(pkrecord);
    if (verify(document.forms['frm_petition_process'])) {
        var data = $(oForm).serialize();
        data += sData;
        data += '&id_checked=' + id_checked;
        data += '&pkrecord=' + pkrecord;
        myClass.loadding = true;
        $.ajax({
            url: url,
            type: "POST",
            data: data,
            // cache: true,
            //dataType: "json",
            // var work = $("form#frm_petition_process input[type='checkbox'][name='chk_item_id']:checked").val();
            success: function (string) {
                // alert(id_checked);
                myClass.loadding = false;
                if (actback == 'actback') {
                    myClass.loadList();
                    $('#UpdateFrm').hide();
                    $('#IndexFrm').show();
                }
                else if (actback != 'actback' && id_checked == 'undefined') {
                    loadlistTempo(pkrecord, 1);
                }
                else {
                    loadlistTempo(pkrecord, 0);
                }
                hideloadpage();
            }
        });
    }
}

function loadlistTempo(pkrecord, option_loadlist) {
    if (this.loadding) return false;
    var url = baseUrl + '/main/ajax/loadlistTempo';
    var data = {pkrecord: pkrecord};
    $.ajax({
        url: url,
        type: "POST",
        data: data,
        // cache: true,
        //dataType: "json",
        success: function (string) {
            $('div#wf-record-work').append(string);
            if (option_loadlist == 0) {
                $('input[type="checkbox"][name="chkKQXL"]').each(function () {
                    $(this).prop('checked', false);
                    $(this).attr('disabled', true);
                });
            }
        }
    });
}

function open_modal_export_file_trackletter(fileType, filePath) {
    var fileName = '', shtml = '';
    var arrfilePath = filePath.split("/");
    var fileName = arrfilePath[arrfilePath.length - 1];
    shtml += '<div style="float:left; height:30px;width:280px; ">' + '<a class="file_export-icon ' + fileType + '" href="' + filePath +
    '" style="float:left;"></a><div style="margin-top:20px; padding-left:45px; text-align:left;"><a href="' + filePath + '" style="color:white">' + fileName + '</a></div></div>';
    $('div.file_export-title').html(shtml);
    $('div#note-process').addClass('nq vY');
    $('div#note-process').show();
    $('div#file_export').show();

    // var arrfilePath = filePath.split("/");
    // var fileName = arrfilePath[arrfilePath.length -1];
    // $('div.file_export-title').html(fileName);
    // $('a.file_export-icon').attr('href',filePath);
    // if (fileType == 'excel')
    //     $('a.file_export-icon').attr('class','file_export-icon xls');
    // else if (fileType == 'doc')
    //     $('a.file_export-icon').attr('class','file_export-icon doc');
    // else if (fileType == 'pdf')
    //     $('a.file_export-icon').attr('class','file_export-icon pdf');
    // $('div#note-process').addClass('nq vY');
    // $('div#note-process').show();
    // $('div#file_export').show();
}
function printMeeting(obj) {
    var url = baseUrl + '/main/ajax/printmeeting';
    var myClass = this;
    var codetemp = $(obj).attr('codetemp');
    // var period = $('form#frm01_0113_index #month_meeting').val();
    // var year = $('form#frm01_0113_index #year_meeting').val();
    var pkperiod = $('form#frm_record_meeting_index #periodic_meeting').val();
    var data = {
        pkperiod: pkperiod,
        codetemp: codetemp,
        kieuin: 'doc'
    };
    var count = 0;
    var oForm = $('div.button-link-container').closest('form');
    // oForm = $('div#table-container').closest('form');

    switch (codetemp) {
        case 'BMTDDK.UBH':
            break;
        default:
            var pkrecordmeetingcitizens = '';
            $(oForm).find('input[type="checkbox"][name="chk_item_id"]:checked').each(function () {
                pkrecordmeetingcitizens += $(this).val() + ',';
                count++;
            })
            // Chua chon cai nao
            if (count == 0) {
                jAlert('Bạn chưa chọn chọn 1 lượt tiếp dân để in', 'Error');
                return false;
            }
            ;
            data.pkrecordmeetingcitizens = pkrecordmeetingcitizens;
            break;
    }

    open_load_data_process();
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        success: function (string) {
            // alert(string);
            close_load_data_process();
            open_modal_export_file('doc', string);
        }
    });
    return !history.pushState;
}

function showHideMenu() {
    var lm = Get_Cookie('lm');
    if (lm != 0) {
        lm = 0;
    } else {
        lm = 1;
    }
    Set_Cookie('lm', lm, '', '/', '', '');
}
// Clenda
/**
 * Copyright 2004 Ho Ngoc Duc [http://come.to/duc]. All Rights Reserved.<p>
 * Permission to use, copy, modify, and redistribute this software and its
 * documentation for personal, non-commercial use is hereby granted provided that
 * this copyright notice appears in all copies.
 */

var ABOUT = "\u00C2m l\u1ECBch Vi\u1EC7t Nam - Version 0.5" + "\n\u00A9 2004 H\u1ED3 Ng\u1ECDc \u0110\u1EE9c [http://come.to/duc]";
var TK19 = new Array(
    0x30baa3, 0x56ab50, 0x422ba0, 0x2cab61, 0x52a370, 0x3c51e8, 0x60d160, 0x4ae4b0, 0x376926, 0x58daa0,
    0x445b50, 0x3116d2, 0x562ae0, 0x3ea2e0, 0x28e2d2, 0x4ec950, 0x38d556, 0x5cb520, 0x46b690, 0x325da4,
    0x5855d0, 0x4225d0, 0x2ca5b3, 0x52a2b0, 0x3da8b7, 0x60a950, 0x4ab4a0, 0x35b2a5, 0x5aad50, 0x4455b0,
    0x302b74, 0x562570, 0x4052f9, 0x6452b0, 0x4e6950, 0x386d56, 0x5e5aa0, 0x46ab50, 0x3256d4, 0x584ae0,
    0x42a570, 0x2d4553, 0x50d2a0, 0x3be8a7, 0x60d550, 0x4a5aa0, 0x34ada5, 0x5a95d0, 0x464ae0, 0x2eaab4,
    0x54a4d0, 0x3ed2b8, 0x64b290, 0x4cb550, 0x385757, 0x5e2da0, 0x4895d0, 0x324d75, 0x5849b0, 0x42a4b0,
    0x2da4b3, 0x506a90, 0x3aad98, 0x606b50, 0x4c2b60, 0x359365, 0x5a9370, 0x464970, 0x306964, 0x52e4a0,
    0x3cea6a, 0x62da90, 0x4e5ad0, 0x392ad6, 0x5e2ae0, 0x4892e0, 0x32cad5, 0x56c950, 0x40d4a0, 0x2bd4a3,
    0x50b690, 0x3a57a7, 0x6055b0, 0x4c25d0, 0x3695b5, 0x5a92b0, 0x44a950, 0x2ed954, 0x54b4a0, 0x3cb550,
    0x286b52, 0x4e55b0, 0x3a2776, 0x5e2570, 0x4852b0, 0x32aaa5, 0x56e950, 0x406aa0, 0x2abaa3, 0x50ab50
);
/* Years 2000-2099 */

var TK20 = new Array(
    0x3c4bd8, 0x624ae0, 0x4ca570, 0x3854d5, 0x5cd260, 0x44d950, 0x315554, 0x5656a0, 0x409ad0, 0x2a55d2,
    0x504ae0, 0x3aa5b6, 0x60a4d0, 0x48d250, 0x33d255, 0x58b540, 0x42d6a0, 0x2cada2, 0x5295b0, 0x3f4977,
    0x644970, 0x4ca4b0, 0x36b4b5, 0x5c6a50, 0x466d40, 0x2fab54, 0x562b60, 0x409570, 0x2c52f2, 0x504970,
    0x3a6566, 0x5ed4a0, 0x48ea50, 0x336a95, 0x585ad0, 0x442b60, 0x2f86e3, 0x5292e0, 0x3dc8d7, 0x62c950,
    0x4cd4a0, 0x35d8a6, 0x5ab550, 0x4656a0, 0x31a5b4, 0x5625d0, 0x4092d0, 0x2ad2b2, 0x50a950, 0x38b557,
    0x5e6ca0, 0x48b550, 0x355355, 0x584da0, 0x42a5b0, 0x2f4573, 0x5452b0, 0x3ca9a8, 0x60e950, 0x4c6aa0,
    0x36aea6, 0x5aab50, 0x464b60, 0x30aae4, 0x56a570, 0x405260, 0x28f263, 0x4ed940, 0x38db47, 0x5cd6a0,
    0x4896d0, 0x344dd5, 0x5a4ad0, 0x42a4d0, 0x2cd4b4, 0x52b250, 0x3cd558, 0x60b540, 0x4ab5a0, 0x3755a6,
    0x5c95b0, 0x4649b0, 0x30a974, 0x56a4b0, 0x40aa50, 0x29aa52, 0x4e6d20, 0x39ad47, 0x5eab60, 0x489370,
    0x344af5, 0x5a4970, 0x4464b0, 0x2c74a3, 0x50ea50, 0x3d6a58, 0x6256a0, 0x4aaad0, 0x3696d5, 0x5c92e0
);
/* Years 1900-1999 */

var TK21 = new Array(
    0x46c960, 0x2ed954, 0x54d4a0, 0x3eda50, 0x2a7552, 0x4e56a0, 0x38a7a7, 0x5ea5d0, 0x4a92b0, 0x32aab5,
    0x58a950, 0x42b4a0, 0x2cbaa4, 0x50ad50, 0x3c55d9, 0x624ba0, 0x4ca5b0, 0x375176, 0x5c5270, 0x466930,
    0x307934, 0x546aa0, 0x3ead50, 0x2a5b52, 0x504b60, 0x38a6e6, 0x5ea4e0, 0x48d260, 0x32ea65, 0x56d520,
    0x40daa0, 0x2d56a3, 0x5256d0, 0x3c4afb, 0x6249d0, 0x4ca4d0, 0x37d0b6, 0x5ab250, 0x44b520, 0x2edd25,
    0x54b5a0, 0x3e55d0, 0x2a55b2, 0x5049b0, 0x3aa577, 0x5ea4b0, 0x48aa50, 0x33b255, 0x586d20, 0x40ad60,
    0x2d4b63, 0x525370, 0x3e49e8, 0x60c970, 0x4c54b0, 0x3768a6, 0x5ada50, 0x445aa0, 0x2fa6a4, 0x54aad0,
    0x4052e0, 0x28d2e3, 0x4ec950, 0x38d557, 0x5ed4a0, 0x46d950, 0x325d55, 0x5856a0, 0x42a6d0, 0x2c55d4,
    0x5252b0, 0x3ca9b8, 0x62a930, 0x4ab490, 0x34b6a6, 0x5aad50, 0x4655a0, 0x2eab64, 0x54a570, 0x4052b0,
    0x2ab173, 0x4e6930, 0x386b37, 0x5e6aa0, 0x48ad50, 0x332ad5, 0x582b60, 0x42a570, 0x2e52e4, 0x50d160,
    0x3ae958, 0x60d520, 0x4ada90, 0x355aa6, 0x5a56d0, 0x462ae0, 0x30a9d4, 0x54a2d0, 0x3ed150, 0x28e952
);
/* Years 2000-2099 */

var TK22 = new Array(
    0x4eb520, 0x38d727, 0x5eada0, 0x4a55b0, 0x362db5, 0x5a45b0, 0x44a2b0, 0x2eb2b4, 0x54a950, 0x3cb559,
    0x626b20, 0x4cad50, 0x385766, 0x5c5370, 0x484570, 0x326574, 0x5852b0, 0x406950, 0x2a7953, 0x505aa0,
    0x3baaa7, 0x5ea6d0, 0x4a4ae0, 0x35a2e5, 0x5aa550, 0x42d2a0, 0x2de2a4, 0x52d550, 0x3e5abb, 0x6256a0,
    0x4c96d0, 0x3949b6, 0x5e4ab0, 0x46a8d0, 0x30d4b5, 0x56b290, 0x40b550, 0x2a6d52, 0x504da0, 0x3b9567,
    0x609570, 0x4a49b0, 0x34a975, 0x5a64b0, 0x446a90, 0x2cba94, 0x526b50, 0x3e2b60, 0x28ab61, 0x4c9570,
    0x384ae6, 0x5cd160, 0x46e4a0, 0x2eed25, 0x54da90, 0x405b50, 0x2c36d3, 0x502ae0, 0x3a93d7, 0x6092d0,
    0x4ac950, 0x32d556, 0x58b4a0, 0x42b690, 0x2e5d94, 0x5255b0, 0x3e25fa, 0x6425b0, 0x4e92b0, 0x36aab6,
    0x5c6950, 0x4674a0, 0x31b2a5, 0x54ad50, 0x4055a0, 0x2aab73, 0x522570, 0x3a5377, 0x6052b0, 0x4a6950,
    0x346d56, 0x585aa0, 0x42ab50, 0x2e56d4, 0x544ae0, 0x3ca570, 0x2864d2, 0x4cd260, 0x36eaa6, 0x5ad550,
    0x465aa0, 0x30ada5, 0x5695d0, 0x404ad0, 0x2aa9b3, 0x50a4d0, 0x3ad2b7, 0x5eb250, 0x48b540, 0x33d556
);
/* Years 2100-2199 */

var CAN = new Array("Gi\341p", "\u1EA4t", "B\355nh", "\u0110inh", "M\u1EADu", "K\u1EF7", "Canh", "T\342n", "Nh\342m", "Qu\375");
var CHI = new Array("T\375", "S\u1EEDu", "D\u1EA7n", "M\343o", "Th\354n", "T\u1EF5", "Ng\u1ECD", "M\371i", "Th\342n", "D\u1EADu", "Tu\u1EA5t", "H\u1EE3i");
var TUAN = new Array("Ch\u1EE7 nh\u1EADt", "Th\u1EE9 hai", "Th\u1EE9 ba", "Th\u1EE9 t\u01B0", "Th\u1EE9 n\u0103m", "Th\u1EE9 s\341u", "Th\u1EE9 b\u1EA3y");
var GIO_HD = new Array("110100101100", "001101001011", "110011010010", "101100110100", "001011001101", "010010110011");

/* Create lunar date object, stores (lunar) date, month, year, leap month indicator, and Julian date number */
function LunarDate(dd, mm, yy, leap, jd) {
    this.day = dd;
    this.month = mm;
    this.year = yy;
    this.leap = leap;
    this.jd = jd;
}

function INT(d) {
    return Math.floor(d);
}
function jdn(dd, mm, yy) {
    var a = INT((14 - mm) / 12);
    var y = yy + 4800 - a;
    var m = mm + 12 * a - 3;
    var jd = dd + INT((153 * m + 2) / 5) + 365 * y + INT(y / 4) - INT(y / 100) + INT(y / 400) - 32045;
    return jd;
    //return 367*yy - INT(7*(yy+INT((mm+9)/12))/4) - INT(3*(INT((yy+(mm-9)/7)/100)+1)/4) + INT(275*mm/9)+dd+1721029;
}

function jdn2date(jd) {
    var Z, A, alpha, B, C, D, E, dd, mm, yyyy, F;
    Z = jd;
    if (Z < 2299161) {
        A = Z;
    } else {
        alpha = INT((Z - 1867216.25) / 36524.25);
        A = Z + 1 + alpha - INT(alpha / 4);
    }
    B = A + 1524;
    C = INT((B - 122.1) / 365.25);
    D = INT(365.25 * C);
    E = INT((B - D) / 30.6001);
    dd = INT(B - D - INT(30.6001 * E));
    if (E < 14) {
        mm = E - 1;
    } else {
        mm = E - 13;
    }
    if (mm < 3) {
        yyyy = C - 4715;
    } else {
        yyyy = C - 4716;
    }
    return new Array(dd, mm, yyyy);
}

function decodeLunarYear(yy, k) {
    var monthLengths, regularMonths, offsetOfTet, leapMonth, leapMonthLength, solarNY, currentJD, j, mm;
    var ly = new Array();
    monthLengths = new Array(29, 30);
    regularMonths = new Array(12);
    offsetOfTet = k >> 17;
    leapMonth = k & 0xf;
    leapMonthLength = monthLengths[k >> 16 & 0x1];
    solarNY = jdn(1, 1, yy);
    currentJD = solarNY + offsetOfTet;
    j = k >> 4;
    for (i = 0; i < 12; i++) {
        regularMonths[12 - i - 1] = monthLengths[j & 0x1];
        j >>= 1;
    }
    if (leapMonth == 0) {
        for (mm = 1; mm <= 12; mm++) {
            ly.push(new LunarDate(1, mm, yy, 0, currentJD));
            currentJD += regularMonths[mm - 1];
        }
    } else {
        for (mm = 1; mm <= leapMonth; mm++) {
            ly.push(new LunarDate(1, mm, yy, 0, currentJD));
            currentJD += regularMonths[mm - 1];
        }
        ly.push(new LunarDate(1, leapMonth, yy, 1, currentJD));
        currentJD += leapMonthLength;
        for (mm = leapMonth + 1; mm <= 12; mm++) {
            ly.push(new LunarDate(1, mm, yy, 0, currentJD));
            currentJD += regularMonths[mm - 1];
        }
    }
    return ly;
}

function getYearInfo(yyyy) {
    var yearCode;
    if (yyyy < 1900) {
        yearCode = TK19[yyyy - 1800];
    } else if (yyyy < 2000) {
        yearCode = TK20[yyyy - 1900];
    } else if (yyyy < 2100) {
        yearCode = TK21[yyyy - 2000];
    } else {
        yearCode = TK22[yyyy - 2100];
    }
    return decodeLunarYear(yyyy, yearCode);
}

var FIRST_DAY = jdn(25, 1, 1800); // Tet am lich 1800
var LAST_DAY = jdn(31, 12, 2199);

function findLunarDate(jd, ly) {
    if (jd > LAST_DAY || jd < FIRST_DAY || ly[0].jd > jd) {
        return new LunarDate(0, 0, 0, 0, jd);
    }
    var i = ly.length - 1;
    while (jd < ly[i].jd) {
        i--;
    }
    var off = jd - ly[i].jd;
    ret = new LunarDate(ly[i].day + off, ly[i].month, ly[i].year, ly[i].leap, jd);
    return ret;
}

function getLunarDate(dd, mm, yyyy) {
    var ly, jd;
    if (yyyy < 1800 || 2199 < yyyy) {
        //return new LunarDate(0, 0, 0, 0, 0);
    }
    ly = getYearInfo(yyyy);
    jd = jdn(dd, mm, yyyy);
    if (jd < ly[0].jd) {
        ly = getYearInfo(yyyy - 1);
    }
    return findLunarDate(jd, ly);
}

var today = new Date();
//var currentLunarYear = getYearInfo(today.getFullYear());
var currentLunarDate = getLunarDate(today.getDate(), today.getMonth() + 1, today.getFullYear());
var currentMonth = today.getMonth() + 1;
var currentYear = today.getFullYear();

function parseQuery(q) {
    var ret = new Array();
    if (q.length < 2) return ret;
    var s = q.substring(1, q.length);
    var arr = s.split("&");
    var i, j;
    for (i = 0; i < arr.length; i++) {
        var a = arr[i].split("=");
        for (j = 0; j < a.length; j++) {
            ret.push(a[j]);
        }
    }
    return ret;
}

function getSelectedMonth() {
    var query = window.location.search;
    var arr = parseQuery(query);
    var idx;
    for (idx = 0; idx < arr.length; idx++) {
        if (arr[idx] == "mm") {
            currentMonth = parseInt(arr[idx + 1]);
        } else if (arr[idx] == "yy") {
            currentYear = parseInt(arr[idx + 1]);
        }
    }
}

function getMonth(mm, yy) {
    var ly1, ly2, tet1, jd1, jd2, mm1, yy1, result, i;
    if (mm < 12) {
        mm1 = mm + 1;
        yy1 = yy;
    } else {
        mm1 = 1;
        yy1 = yy + 1;
    }
    jd1 = jdn(1, mm, yy);
    jd2 = jdn(1, mm1, yy1);
    ly1 = getYearInfo(yy);
    //alert('1/'+mm+'/'+yy+' = '+jd1+'; 1/'+mm1+'/'+yy1+' = '+jd2);
    tet1 = ly1[0].jd;
    result = new Array();
    if (tet1 <= jd1) { /* tet(yy) = tet1 < jd1 < jd2 <= 1.1.(yy+1) < tet(yy+1) */
        for (i = jd1; i < jd2; i++) {
            result.push(findLunarDate(i, ly1));
        }
    } else if (jd1 < tet1 && jd2 < tet1) { /* tet(yy-1) < jd1 < jd2 < tet1 = tet(yy) */
        ly1 = getYearInfo(yy - 1);
        for (i = jd1; i < jd2; i++) {
            result.push(findLunarDate(i, ly1));
        }
    } else if (jd1 < tet1 && tet1 <= jd2) { /* tet(yy-1) < jd1 < tet1 <= jd2 < tet(yy+1) */
        ly2 = getYearInfo(yy - 1);
        for (i = jd1; i < tet1; i++) {
            result.push(findLunarDate(i, ly2));
        }
        for (i = tet1; i < jd2; i++) {
            result.push(findLunarDate(i, ly1));
        }
    }
    return result;
}

function getDayName(lunarDate) {
    if (lunarDate.day == 0) {
        return "";
    }
    var cc = getCanChi(lunarDate);
    var s = "Ng\u00E0y " + cc[0] + ", th\341ng " + cc[1] + ", n\u0103m " + cc[2];
    return s;
}

function getYearCanChi(year) {
    return CAN[(year + 6) % 10] + " " + CHI[(year + 8) % 12];
}

function getCanChi(lunar) {
    var dayName, monthName, yearName;
    dayName = CAN[(lunar.jd + 9) % 10] + " " + CHI[(lunar.jd + 1) % 12];
    monthName = CAN[(lunar.year * 12 + lunar.month + 3) % 10] + " " + CHI[(lunar.month + 1) % 12];
    if (lunar.leap == 1) {
        monthName += " (nhu\u1EADn)";
    }
    yearName = getYearCanChi(lunar.year);
    return new Array(dayName, monthName, yearName);
}

function getDayString(lunar, solarDay, solarMonth, solarYear) {
    var s;
    var dayOfWeek = TUAN[(lunar.jd + 1) % 7];
    s = dayOfWeek + " " + solarDay + "/" + solarMonth + "/" + solarYear;
    s += " -+- ";
    s = s + "Ng\u00E0y " + lunar.day + " th\341ng " + lunar.month;
    if (lunar.leap == 1) {
        s = s + " nhu\u1EADn";
    }
    return s;
}

function getTodayString() {
    var s = getDayString(currentLunarDate, today.getDate(), today.getMonth() + 1, today.getFullYear());
    s += " n\u0103m " + getYearCanChi(currentLunarDate.year);
    return s;
}

function getCurrentTime() {
    today = new Date();
    var Std = today.getHours();
    var Min = today.getMinutes();
    var Sec = today.getSeconds();
    var s1 = ((Std < 10) ? "0" + Std : Std);
    var s2 = ((Min < 10) ? "0" + Min : Min);
    //var s3  = ((Sec < 10) ? "0" + Sec : Sec);
    //return s1 + ":" + s2 + ":" + s3;
    return s1 + ":" + s2;
}

function getGioHoangDao(jd) {
    var chiOfDay = (jd + 1) % 12;
    var gioHD = GIO_HD[chiOfDay % 6]; // same values for Ty' (1) and Ngo. (6), for Suu and Mui etc.
    var ret = "";
    var count = 0;
    for (var i = 0; i < 12; i++) {
        if (gioHD.charAt(i) == '1') {
            ret += CHI[i];
            if (count++ < 5) ret += ', ';
        }
    }
    return ret;
}

var DAYNAMES = new Array("CN", "T2", "T3", "T4", "T5", "T6", "T7");
var PRINT_OPTS = new OutputOptions();
var FONT_SIZES = new Array("9pt", "13pt", "17pt");
var TAB_WIDTHS = new Array("180px", "420px", "600px");

function OutputOptions() {
    this.fontSize = "13pt";
    this.tableWidth = "420px";
}

function setOutputSize(size) {
    var idx = 1;
    if (size == "small") {
        idx = 0;
    } else if (size == "big") {
        idx = 2;
    } else {
        idx = 1;
    }
    PRINT_OPTS.fontSize = FONT_SIZES[idx];
    PRINT_OPTS.tableWidth = TAB_WIDTHS[idx];
}

function printSelectedMonth() {
    getSelectedMonth();
    return printMonth(currentMonth, currentYear);
}

function printMonth(mm, yy) {
    var res = "";
    res += printStyle();
    res += printTable(mm, yy);
    res += printFoot();
    return res;
}

function printYear(yy) {
    var yearName = "N&#x103;m " + getYearCanChi(yy) + " " + yy;
    var res = "";
    res += printStyle();
    res += '<table align=center>\n';
    res += ('<tr><td colspan="3" class="tennam" onClick="showYearSelect();">' + yearName + '</td></tr>\n');
    for (var i = 1; i <= 12; i++) {
        if (i % 3 == 1) res += '<tr>\n';
        res += '<td>\n';
        res += printTable(i, yy);
        res += '</td>\n';
        if (i % 3 == 0) res += '</tr>\n';
    }
    res += '<table>\n';
    res += printFoot();
    return res;
}

function printSelectedYear() {
    getSelectedMonth();
    return printYear(currentYear);
}

function printStyle() {
    var fontSize = PRINT_OPTS.fontSize;
    var res = "";
    res += '<style type="text/css">\n';
    res += '<!--\n';
    //res += '  body {margin:0}\n';
    res += '  .tennam {text-align:center; font-size:150%; line-height:120%; font-weight:bold; color:#000000; background-color: #CCCCCC}\n';
    res += '  .thang {font-size: ' + fontSize + '; padding:1; line-height:100%; font-family:Tahoma,Verdana,Arial; table-layout:fixed}\n';
    res += '  .tenthang {text-align:center; font-size:125%; line-height:100%; font-weight:bold; color:#330033; background-color: #CCFFCC}\n';
    res += '  .navi-l {text-align:center; font-size:75%; line-height:100%; font-family:Verdana,Times New Roman,Arial; font-weight:bold; color:red; background-color: #CCFFCC}\n';
    res += '  .navi-r {text-align:center; font-size:75%; line-height:100%; font-family:Verdana,Arial,Times New Roman; font-weight:bold; color:#330033; background-color: #CCFFCC}\n';
    res += '  .ngaytuan {width:14%; text-align:center; font-size:125%; line-height:100%; color:#330033; background-color: #FFFFCC}\n';
    res += '  .ngaythang {background-color:#FDFDF0}\n';
    res += '  .homnay {background-color:#FFF000}\n';
    res += '  .tet {background-color:#FFCC99}\n';
    res += '  .am {text-align:right;font-size:75%;line-height:100%;color:blue}\n';
    res += '  .am2 {text-align:right;font-size:75%;line-height:100%;color:#004080}\n';
    res += '  .t2t6 {text-align:left;font-size:125%;color:black}\n';
    res += '  .t7 {text-align:left;font-size:125%;line-height:100%;color:green}\n';
    res += '  .cn {text-align:left;font-size:125%;line-height:100%;color:red}\n';
    res += '-->\n';
    res += '</style>\n';
    return res;
}

function printTable(mm, yy) {
    var i, j, k, solar, lunar, cellClass, solarClass, lunarClass;
    var currentMonth = getMonth(mm, yy);
    if (currentMonth.length == 0) return;
    var ld1 = currentMonth[0];
    var emptyCells = (ld1.jd + 1) % 7;
    var MonthHead = mm + "/" + yy;
    var LunarHead = getYearCanChi(ld1.year);
    var res = "";
    res += ('<table class="thang" border="2" cellpadding="1" cellspacing="1" width="' + PRINT_OPTS.tableWidth + '">\n');
    res += printHead(mm, yy);
    for (i = 0; i < 6; i++) {
        res += ("<tr>\n");
        for (j = 0; j < 7; j++) {
            k = 7 * i + j;
            if (k < emptyCells || k >= emptyCells + currentMonth.length) {
                res += printEmptyCell();
            } else {
                solar = k - emptyCells + 1;
                ld1 = currentMonth[k - emptyCells];
                res += printCell(ld1, solar, mm, yy);
            }
        }
        res += ("</tr>\n");
    }
    res += ('</table>\n');
    return res;
}

function getPrevMonthLink(mm, yy) {
    var mm1 = mm > 1 ? mm - 1 : 12;
    var yy1 = mm > 1 ? yy : yy - 1;
    //return '<a href="'+window.location.pathname+'?yy='+yy1+'&mm='+mm1+'"><img src="left1.gif" width=8 height=12 alt="PrevMonth" border=0></a>';
    return '<a href="' + window.location.pathname + '?yy=' + yy1 + '&mm=' + mm1 + '">&lt;</a>';
}

function getNextMonthLink(mm, yy) {
    var mm1 = mm < 12 ? mm + 1 : 1;
    var yy1 = mm < 12 ? yy : yy + 1;
    //return '<a href="'+window.location.pathname+'?yy='+yy1+'&mm='+mm1+'"><img src="right1.gif" width=8 height=12 alt="NextMonth" border=0></a>';
    return '<a href="' + window.location.pathname + '?yy=' + yy1 + '&mm=' + mm1 + '">&gt;</a>';
}

function getPrevYearLink(mm, yy) {
    //return '<a href="'+window.location.pathname+'?yy='+(yy-1)+'&mm='+mm+'"><img src="left2.gif" width=16 height=12 alt="PrevYear" border=0></a>';
    return '<a href="' + window.location.pathname + '?yy=' + (yy - 1) + '&mm=' + mm + '">&lt;&lt;</a>';
}

function getNextYearLink(mm, yy) {
    //return '<a href="'+window.location.pathname+'?yy='+(yy+1)+'&mm='+mm+'"><img src="right2.gif" width=16 height=12 alt="NextYear" border=0></a>';
    return '<a href="' + window.location.pathname + '?yy=' + (yy + 1) + '&mm=' + mm + '">&gt;&gt;</a>';
}

function printHead(mm, yy) {
    var res = "";
    var monthName = mm + "/" + yy;
    //res += ('<tr><td colspan="7" class="tenthang" onClick="showMonthSelect();">'+monthName+'</td></tr>\n');
    res += ('<tr><td colspan="2" class="navi-l">' + getPrevYearLink(mm, yy) + ' &nbsp;' + getPrevMonthLink(mm, yy) + '</td>\n');
    //res += ('<td colspan="1" class="navig"><a href="'+getPrevMonthLink(mm, yy)+'"><img src="left1.gif" alt="Prev"></a></td>\n');
    res += ('<td colspan="3" class="tenthang" onClick="showMonthSelect();">' + monthName + '</td>\n');
    //res += ('<td colspan="1" class="navi-r"><a href="'+getNextMonthLink(mm, yy)+'"><img src="right1.gif" alt="Next"></a></td>\n');
    res += ('<td colspan="2" class="navi-r">' + getNextMonthLink(mm, yy) + ' &nbsp;' + getNextYearLink(mm, yy) + '</td></tr>\n');
    //res += ('<tr><td colspan="7" class="tenthang"><a href="'+getNextMonthLink(mm, yy)+'"><img src="right.gif" alt="Next"></a></td></tr>\n');
    res += ('<tr onClick="alertAbout();">\n');
    for (var i = 0; i <= 6; i++) {
        res += ('<td class=ngaytuan>' + DAYNAMES[i] + '</td>\n');
    }
    res += ('<\/tr>\n');
    return res;
}

function printEmptyCell() {
    return '<td class=ngaythang><div class=cn>&nbsp;</div> <div class=am>&nbsp;</div></td>\n';
}

function printCell(lunarDate, solarDate, solarMonth, solarYear) {
    var cellClass, solarClass, lunarClass, solarColor;
    cellClass = "ngaythang";
    solarClass = "t2t6";
    lunarClass = "am";
    solarColor = "black";
    var dow = (lunarDate.jd + 1) % 7;
    if (dow == 0) {
        solarClass = "cn";
        solarColor = "red";
    } else if (dow == 6) {
        solarClass = "t7";
        solarColor = "green";
    }
    if (solarDate == today.getDate() && solarMonth == today.getMonth() + 1 && solarYear == today.getFullYear()) {
        cellClass = "homnay";
    }
    if (lunarDate.day == 1 && lunarDate.month == 1) {
        cellClass = "tet";
    }
    if (lunarDate.leap == 1) {
        lunarClass = "am2";
    }
    var lunar = lunarDate.day;
    if (solarDate == 1 || lunar == 1) {
        lunar = lunarDate.day + "/" + lunarDate.month;
    }
    var res = "";
    var args = lunarDate.day + "," + lunarDate.month + "," + lunarDate.year + "," + lunarDate.leap;
    args += ("," + lunarDate.jd + "," + solarDate + "," + solarMonth + "," + solarYear);
    res += ('<td class="' + cellClass + '"');
    if (lunarDate != null) res += (' title="' + getDayName(lunarDate) + '" onClick="alertDayInfo(' + args + ');"');
    res += (' <div style=color:' + solarColor + ' class="' + solarClass + '">' + solarDate + '</div> <div class="' + lunarClass + '">' + lunar + '</div></td>\n');
    return res;
}

function printFoot() {
    var res = "";
    res += '<script language="JavaScript" src="amlich-hnd.js"></script>\n';
    return res;
}

function showMonthSelect() {
    var home = "http://www.ifis.uni-luebeck.de/~duc/amlich/JavaScript/";
    window.open(home, "win2702", "menubar=yes,scrollbars=yes,status=yes,toolbar=yes,resizable=yes,location=yes");
    //window.location = home;
    //alertAbout();
}

function showYearSelect() {
    //window.open("selectyear.html", "win2702", "menubar=yes,scrollbars=yes");
    window.print();
}

function infoCellSelect(id) {
    if (id == 0) {
    }
}

function alertDayInfo(dd, mm, yy, leap, jd, sday, smonth, syear) {
    var lunar = new LunarDate(dd, mm, yy, leap, jd);
    var s = getDayString(lunar, sday, smonth, syear);
    s += " \u00E2m l\u1ECBch\n";
    s += getDayName(lunar);
    s += "\nGi\u1EDD ho\u00E0ng \u0111\u1EA1o: " + getGioHoangDao(jd);
    alert(s);
}

function alertAbout() {
    alert(ABOUT);
}

function showVietCal() {
    window.status = getCurrentTime() + " -+- " + getTodayString();
    window.window.setTimeout("showVietCal()", 5000);
}
//Ham chuyen tu ngay duong sang ngay am
function Solar2Lunar(p_date) {
    p_date = replace(p_date, "-", "/");
    var v_date = p_date.split("/");
    var date_obj = getLunarDate(parseInt(v_date[0]), parseInt(v_date[1]), parseInt(v_date[2]));
    return date_obj.day + "/" + date_obj.month + "/" + date_obj.year;
}
function Solar2DayofWeek(p_date) {
    p_date = replace(p_date, "-", "/");
    var v_date = p_date.split("/");
    var day = jdn(parseInt(v_date[0]), parseInt(v_date[1]), parseInt(v_date[2]));
    return (day % 7 + 2);
}
/**
 *
 * @param obj
 */
function createPrintDialog(obj) {
    var divcontent = $(obj).next();
    $(divcontent).dialog({
        autoOpen: false,
        title: 'DANH SÁCH MẪU IN',
        modal: true,
        resizable: true,
        width: document.documentElement.clientWidth * 0.5,
        autoResize: true,
        dialogClass: 'dialog-class',
        draggable: true,
        overlay: {
            opacity: 0.5,
            background: 'blue'
        }
    })
}
/**
 *
 * @param i
 * @returns {*}
 */
function addZeroTime(i) {
    if (i < 10) {
        i = "0" + i;
    }
    return i;
}
/**
 *
 * @param obj
 */
function selectprinttemp(obj) {
    var oLi = $(obj).parent().parent();
    var ocheck = $(oLi).find('input[name="print_checkbox"]');
    if ($(ocheck).is(':checked')) {
        $(oLi).addClass('printselected');
    } else {
        $(oLi).removeClass('printselected');
    }
}

function setHeaderUploadfile() {
    var dir = baseUrl + '/public/js/jquery_file_upload/';
    $('head').append('<link rel="stylesheet" type="text/css" href="' + dir + 'css/style.css?11">')
    // $('head').append('<link rel="stylesheet" href="'+dir+'css/jquery.fileupload.css">')
    $('head').append('<script src="' + dir + 'js/vendor/jquery.ui.widget.js" type="text/javascript"></script>')
    $('head').append('<script src="' + dir + 'js/tmpl.min.js" type="text/javascript"></script>')
    $('head').append('<script src="' + dir + 'js/load-image.min.js" type="text/javascript"></script>')
    $('head').append('<script src="' + dir + 'js/canvas-to-blob.min.js" type="text/javascript"></script>')
    $('head').append('<script src="' + dir + 'js/jquery.blueimp-gallery.min.js" type="text/javascript"></script>')
    $('head').append('<script src="' + dir + 'js/jquery.iframe-transport.js" type="text/javascript"></script>')
    $('head').append('<script src="' + dir + 'js/jquery.iframe-transport.js" type="text/javascript"></script>')
    $('head').append('<script src="' + dir + 'js/jquery.fileupload.js" type="text/javascript"></script>')
    $('head').append('<script src="' + dir + 'js/jquery.fileupload-process.js" type="text/javascript"></script>')
    $('head').append('<script src="' + dir + 'js/jquery.fileupload-image.js" type="text/javascript"></script>')
    // $('head').append('<script src="' + dir + 'js/jquery.fileupload-audio.js" type="text/javascript"></script>')
    // $('head').append('<script src="' + dir + 'js/jquery.fileupload-video.js" type="text/javascript"></script>')
    $('head').append('<script src="' + dir + 'js/jquery.fileupload-validate.js" type="text/javascript"></script>')
    $('head').append('<script src="' + dir + 'js/jquery.fileupload-ui.js" type="text/javascript"></script>')
    $('head').append('<script src="' + dir + 'js/md5.min.js" type="text/javascript"></script>')
    $('head').append('<script src="' + dir + 'js/main.js" type="text/javascript"></script>')
}

function loadtemplate(obj) {
    if (typeof $.fn.fileupload == 'undefined') {
        setHeaderUploadfile()
    }
    
    $.ajax({
        url: baseUrl + '/main/file/template',
        type: "POST",
        data: {multiple: 1},
        success: function (string) {
            $(obj).html(string);
        }
    });
}

function loadItemTemplateUpload(obj) {
    if (typeof $.fn.fileupload == 'undefined') {
        setHeaderUploadfile()
    }
    $.ajax({
        url: baseUrl + '/main/file/template',
        type: "POST",
        data: {multiple: 0},
        success: function (string) {
            $(obj).html(string);
        }
    });
}

var set_checked_process = function (obj) {
    var oCheck = $(obj).prev().find('input');
    $(oCheck).attr('checked', true);
    $(oCheck).trigger('change');
}
var checkexit = function (arrayIn, valueIn) {
    for (var i = 0; i < arrayIn.length; i++) {
        if (arrayIn[i] === valueIn) return true;
    }
    return false;
}
var checkprocess = function (obj) {
    var oCheck = $(obj).prev();
    oCheck.find('input').trigger('change');
}

var addRowEmpty = function (obj, num) {
    var numRow = $(obj).children().length - 1, numCol = $(obj).children(':first-child').children().length, shtml = '';
    if (numRow < num) {
        for (var i = 0; i < (num - numRow); i++) {
            shtml += '<tr>';
            for (var j = 0; j < numCol; j++) {
                shtml += '<td>&nbsp;</td>';
            }
            shtml += '</tr>';
        }
        $(obj).append(shtml);
    }
}

var viewDuplicate = function (obj) {
    if ($('#modal_duplicate_view').length < 1) {
        $('#main').append('<div id="modal_duplicate_view" class="modal_duplicate_view"></div>')
        createModalDialog(".modal_duplicate_view", function () {
            $(".modal_duplicate_view").dialog({
                    autoOpen: false,
                    title: 'DANH SÁCH ĐƠN TRÙNG LẶP',
                    modal: true,
                    resizable: true,
                    width: document.documentElement.clientWidth * 0.9,
                    height: document.documentElement.clientHeight * 0.8,
                    autoResize: false,
                    dialogClass: 'dialog-class',
                    draggable: true,
                    overlay: {
                        opacity: 0.5,
                        background: 'blue'
                    },
                    buttons: [
                        {
                            text: "Quay lại",
                            click: function () {
                                $(this).dialog("close");
                            }
                        }
                    ]
                })
        })
    }

    var url = baseUrl + '/main/ajax/getduplicate', 
    oCheckbox = $(obj).parent().parent().find('input[name="chk_item_id"]');
    var pkrecord = $(oCheckbox).val();
    showloadpage();
    var data = {
        pkrecord: pkrecord
    };
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        data: data,
        success: function (arrResult) {
            var htmls = '';
            htmls += '<table class="list-table-data duplicate" style="margin-top: 10px;">';
            htmls += '<col width="3%"></col><col width="6%"></col><col width="8%"></col>';
            htmls += '<col width="12%"></col><col width="25%"></col>';
            htmls += '<col width="46%"></col>';
            htmls += '<tbody><tr class="header">';
            htmls += '<td class="header" style="text-align: center;" ">STT</td>';
            htmls += '<td class="header" style="text-align: center;" >Số đến</td>';
            htmls += '<td class="header" style="text-align: center;" >Ngày nhận</td>';
            htmls += '<td class="header" style="text-align: center;" >Tổ chức/Cá nhân</td>';
            htmls += '<td class="header" style="text-align: center;" >Địa chỉ</td>';
            htmls += '<td class="header" style="text-align: center;" >Nội dung đơn</td>';
            htmls += '</tr> </tbody>';
            htmls += '</table>';
            $('div#modal_duplicate_view').html(htmls);
            $('table.duplicate tbody').append(genHtmlTable(arrResult, 'DUPLICATE'));
            hideloadpage();
            $("#modal_duplicate_view").dialog("open");
        }
    });
    return !history.pushState;
}

function genHtmlTable(arrValue, optionView) {
    var count = arrValue.length, htmls = '';
    if (optionView == 'DUPLICATE') {
        for (i = 0; i < count; i++) {
            htmls += '<tr>';
            htmls += '<td class="data" align="center"><input type="hidden" value="' + arrValue[i]['PkRecord'] + '" name="chk_item_id" onclick="{selectrow(this);}" ondblclick="">' + (i+1) + '</td>';
            htmls += '<td align = "center" class="data" onclick="{selectrow(this);}">' + arrValue[i]['sInputNumber'] + '</td>';
            htmls += '<td align = "center" class="data" onclick="{selectrow(this);}">' + arrValue[i]['dReceivedDate'] + '</td>';
            htmls += '<td align = "left" class="data" onclick="{selectrow(this);}">' + arrValue[i]['sRegistorName'] + '</td>';
            htmls += '<td align = "left" class="data" onclick="{selectrow(this);}">' + arrValue[i]['sRegistorAddress'] + '</td>';
            htmls += '<td align = "justify" class="data" onclick="{selectrow(this);}">' + arrValue[i]['sPetitionContent'] + '</td>';
            htmls += '</tr>';
        }
    }
    return htmls;
}

function remove_bracket(string) {
    string = string.replace(/\{|\}/g, "");
    return string;
}

function fSelectOwner() {
    if($('#hdn_owner_process').val() == 'KHAC') {
        $('label[for="text_owner_process"]').show()
        $('#text_owner_process').show().focus();
    } else {
        $('label[for="text_owner_process"]').hide()
        $('#text_owner_process').hide();
    }
}


function getDataTransfer(oForm) {
    //
    var oData = [];
    var sUserList = '', sUnitList = '', sOwnerList = '', sCurrentStatus = '', sDetailtStatus = '', sDistrictList = '';
    var sUserSupportList = '', sUnitSupportList = '', sOwnerSupportList = '', sStaffProcessList = '';
    var injoinsystem = '';
    var oCheckDefaut = $('input[type="radio"][name="chk_next_step"]:checked');
    if (typeof(oForm) === 'undefined') {
        oForm = $(oCheckDefaut).closest('form');
    }
    ;
    var oNextTask = $(oForm).find('input[type="radio"][name="chk_next_step"]:checked');
    var sWorkType = $(oForm).find('#hdn_worktype').val();
    var sTaskID = oNextTask.val();
    var oInforNextTask = $('.infornextstep[step="' + sTaskID + '"]');
    oInforNextTask.find('.staff_next_step:checked').each(function () {
        sStaffProcessList += $(this).val() + ',';
    })
    sStaffProcessList = sStaffProcessList.substr(0, sStaffProcessList.length - 1);

    var oTransferUser = $(oForm).find('td#transfer_staff');
    // List User
    $('input#check_user:checked').each(function () {
        sUserList += $(this).val() + ',';
    })

    sUserList = sUserList.substr(0, sUserList.length - 1);
    // Lay thong tin phong ban xu ly
    sUnitList = $('#hdn_unit_process').val();

    // Lay thong tin don vi xu ly
    sOwnerList = $('#hdn_owner_process').val();
    if(sOwnerList =='KHAC')
        sOwnerNameList = $('#text_owner_process').val();
    else sOwnerNameList = $('#owner_process').val();
        
    // List Unit
    $('input#check_district:checked').each(function () {
        sDistrictList += $(this).val() + ',';
    })
    sDistrictList = sDistrictList.substr(0, sDistrictList.length - 1);
    // Status process
    var oStatusChecked = oInforNextTask.find('input#check_status:checked');
    sCurrentStatus = oStatusChecked.val();
    sDetailtStatus = oStatusChecked.attr('detailt_status');
    if (sCurrentStatus == 'undefined' || typeof(sCurrentStatus) === 'undefined') {
        sCurrentStatus = $(oForm).find('#current_status').val();
        sDetailtStatus = $(oForm).find('#current_status').attr('detailt_status');
    }
    ;
    //
    if (sCurrentStatus != '') {
        if (oForm.length == 0) {
            $('#hdn_current_status_list').val(sCurrentStatus);
            $('#hdn_detailt_status_list').val(sDetailtStatus);
        } else {
            $(oForm).find('#hdn_current_status_list').val(sCurrentStatus);
            $(oForm).find('#hdn_detailt_status_list').val(sDetailtStatus);
        }
    }
    if (sWorkType != '') {
        $(oForm).find('#hdn_work_type').val(sWorkType);
    }

    sTaskID = (typeof(sTaskID) == 'undefined' ? '' : sTaskID)
    sUserList = (typeof(sUserList) == 'undefined' ? '' : sUserList)
    sUnitList = (typeof(sUnitList) == 'undefined' ? '' : sUnitList)
    sOwnerList = (typeof(sOwnerList) == 'undefined' ? '' : sOwnerList)
    sOwnerNameList = (typeof(sOwnerNameList) == 'undefined' ? '' : sOwnerNameList)

    sCurrentStatus = (typeof(sCurrentStatus) == 'undefined' ? '' : sCurrentStatus)
    sDetailtStatus = (typeof(sDetailtStatus) == 'undefined' ? '' : sDetailtStatus)
    sWorkType = (typeof(sWorkType) == 'undefined' ? '' : sWorkType)
    sStaffProcessList = (typeof(sStaffProcessList) == 'undefined' ? '' : sStaffProcessList)
    sDistrictList = (typeof(sDistrictList) == 'undefined' ? '' : sDistrictList)
    
    oData = {
        sTaskID: sTaskID,
        sUserList: sUserList,
        sUnitList: sUnitList,
        sOwnerList: sOwnerList,
        sOwnerNameList: sOwnerNameList,
        sCurrentStatus: sCurrentStatus,
        sDetailtStatus: sDetailtStatus,
        sWorkType: sWorkType,
        sStaffProcessList: sStaffProcessList,
        sDistrictList: sDistrictList
    };
    return oData;
}
/*
* return loi
*/
function verifyOwnerProcess(data) {
    if (data.sOwnerList == '') {
        jWarning(G_STRINGS_VN._BAN_CHUA_NOI_XU_LY, 'Lỗi');
        return true;
    }
    
    if(data.sOwnerList == 'KHAC' && data.sOwnerNameList == '') {
        jWarning(G_STRINGS_VN._BAN_CHUA_NOI_XU_LY, 'Lỗi');
        return true;
    }
    return false;
}
/*
 * return loi
 */
function verifyUnitProcess(data) {
    if (data.sUnitList == '') {
        jWarning(G_STRINGS_VN._BAN_CHUA_NOI_XU_LY, 'Lỗi');
        return true;
    }
    return false;
}
/*
 *
 */
function getNotify(callback) {
    if (typeof(callback) != 'function') 
            callback = function(){};
    $.ajax({
            url: baseUrl + '/main/reminder/getnotify',
            type: "GET",
            dataType: 'json',
            success: function (result) {
                var html = '<ul>', number_noti = 0;
                if (result.length > 0) {
                    for (var i = 0; i < result.length; i++) {  
                        html += '<li><a href="'+ result[i]['href'] +'" onclick=" $(\'#noti_box\').toggle();'+ result[i]['click'] +'">';
                        html += '<p class="title">Xử lý đơn</p>';
                        html += '<p class="text">'+ result[i]['text'] +'</p>';
                        html += '</a></li>';
                        
                        number_noti += parseInt(result[i]['number'])
                    }
                }
                html += '</ul>';
                $('.noti #noti_box ._33p .list').html(html);
                if (number_noti > 0) 
                    number_noti = '<p class="number_noti">' + number_noti + '</p>';
                else number_noti = '';
                $('.noti .icon_bell').html(number_noti);
                callback.call(this);
            }
        })
}

function ScrollTo(id, top) {
    if (top == null) top = 0;
    if (id == null || id == "")
        jQuery('html, body').animate({ scrollTop: 0 + top }, 1000);
    else
        jQuery('html, body').animate({ scrollTop: jQuery(id).offset().top + top - 100 }, 1000);
}

function business_capital_onchange(p_obj){
    numcheck = /[A-z]/;
    if(numcheck.test(p_obj.value)){
        alert('Số không hợp lệ');
        p_obj.focus();
        return false;
    }
    var str = numberic_to_string(replace(p_obj.value,',',''));
    if (str!="Số không hợp lệ")
    {
        $('#business_capital_string').val(str +"đồng chẵn");
    }else{
        $('#business_capital_string').val(str);
    }
}
