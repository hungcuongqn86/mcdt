/***
*	Toanph
*   27/09/2014
*	Tao cac doi tuong tu khai bao xml
*
*
***/
function Js_GeneralDataTable(){
	//Loai doi tuong
	this.item_type = '';
	//Mang du lieu 
	this.arrListValue = new Array();	
	//ID doi tuong
	this.input_id = '';
	//Style voi doi tuong xd bang
	this.style = 'round_row';
	//ListObj dang thuc hien chinh sua
    this.row = 0;
}
//	Thiet lap cac gia tri
Js_GeneralDataTable.prototype.setInput = function(oSettings){
	this.item_type    = oSettings.type;
	this.input_id     = oSettings.input_id;
	this.arrListValue = $.parseJSON(oSettings.list_value);
    this.row          = oSettings.rowid;
}
// Tao doi tuong
Js_GeneralDataTable.prototype.general = function(oSettings){
	this.setInput(oSettings);
}
// them moi cot
Js_GeneralDataTable.prototype.update_data_list = function(){
    this.general_html_item_attach_doc();
    this.insert_value_to_div();
    //console.log(arrValue);
}
// Ham them moi 1 row
Js_GeneralDataTable.prototype.general_html_item_attach_doc = function(){
    style=this.style;
    if(style == 'round_row'){
		style = 'odd_row';
	}else{
		style = 'round_row';
	}
    var irow = this.row;
    var irow_delete = irow;
    irow_delete++;
    //alert(irow);
    shtml = '<tr class="'+style+'">';
    $('#father_'+this.input_id+' >tbody> tr:eq('+irow_delete+') input[type=textdata1]').each(function() {         
        idname = $(this).attr('id');      
        val = $(this).val();
        if(idname !==''){
           shtml += '<td class="normal_label" align="center"><input type="textdata1" id="'+idname+'" onchange="Js_GeneralDataTable_'+this.input_id+'.insert_value_to_div()" style="width:96%;" optional="true" class="normal_textbox" value="'+val+'"></td>';
          $(this).val(''); 
        } 
    })
    shtml += '<td id="td_'+irow_delete+'" class="normal_label" "><span class="delete_datatable_row" onclick="Js_GeneralDataTable_'+this.input_id+'.delete_row('+irow_delete+');"></span></td></tr>';
    $('table#father_'+ this.input_id +' tr:eq('+irow+')').after(shtml);
    this.row = irow_delete;
}
// Ham xoa 1 row
Js_GeneralDataTable.prototype.delete_row = function(row){
    var checkObj = $('#td_'+row).parent();
    checkObj.remove();
    var row1 = this.row;
    row1 = row1 - 1;
    this.row = row1;
    this.insert_value_to_div();
}
//Ham lay du lieu tren table sau do day ra bien an
Js_GeneralDataTable.prototype.insert_value_to_div = function(){
    // lay mang du lieu tu bang cha
    var arrValue = new Array();
    var id = this.input_id;
    var row = this.row;
    var i = 1;
    
    $('#father_'+id+' >tbody> tr:not(:last-child):not(:first-child)').each(function(){  
        var item = [];
        var check = false;
        var idname ='';
        var val = '';
        var str = ''; 
        str = 'item={';                             
        $('#father_'+id+' >tbody> tr:eq('+i+') input[type=textdata1]').each(function() { 
            idname = $(this).attr('id');
            val = $(this).val();
            if(val!=''){
                check = true;
            }
            str += idname + ':\''+val+'\',';
        })
        str += '}';
        eval(str);   
        if(check){
           arrValue.push(item); 
        }             
        i++;
    })
    var strrJson = JSON.stringify(arrValue);
    $("#input_"+this.input_id).val(strrJson);
}
/***
*	Tao doi tuong thuc thi
*	Js_GeneralItem la doi tuong dung chung
*
*
***/
//var Js_GeneralDataTable = new Js_GeneralDataTable();