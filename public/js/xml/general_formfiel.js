/**
 *
 * @constructor
 */
function Js_GeneralFormFiel() {
    //Loai doi tuong
    this.item_type = '';
    //Mang du lieu
    this.arrListValue = [];
    //ID doi tuong
    this.input_id = '';

    this.html_add = '';
    //Style voi doi tuong xd bang
    this.style = 'round_row';
    //ListObj dang thuc hien chinh sua
    this.row = 0;
}
//	Thiet lap cac gia tri
Js_GeneralFormFiel.prototype.setInput = function (oSettings) {
    this.item_type = oSettings.type;
    this.input_id = oSettings.input_id;
    this.html_add = oSettings.html_add;
    var y = document.createElement('textarea');
    y.innerHTML = '<div style="height: 15px;"></div>' + this.html_add;
    this.html_add = y.value;

    this.arrListValue = $.parseJSON(oSettings.list_value);
    this.row = oSettings.rowid;
};
// Tao doi tuong
Js_GeneralFormFiel.prototype.general = function (oSettings) {
    this.setInput(oSettings);
    if (!this.arrListValue) {
        this.addObj();
    } else {
        var i = 0;
        var row = this.row;
        for (i = 0; i < row; i++) {
            this.row = i;
            this.addObj();
            this.setValueEle(this.arrListValue[i]);
        }
    }
};

// them moi
Js_GeneralFormFiel.prototype.addObj = function () {
    var irow = this.row;
    var arrHtmladd = this.html_add.split('#row');
    var htmladd = arrHtmladd.join(irow.toString());
    $('#pane_' + this.input_id).before(htmladd);
    irow++;
    this.row = irow;
    //formfielgen
    this.setEvens();
};

Js_GeneralFormFiel.prototype.setValueEle = function (data) {
    $.each(data, function (key, val) {
        $('#'+key).val(val);
    });
};

Js_GeneralFormFiel.prototype.setEvens = function () {
    var myjs = this;
    var id = this.input_id;
    var row = this.row;
    var i = 0;
    for (i = 0; i < row; i++) {
        $('.' + id + '_formfielgen_' + i.toString()).each(function () {
            $(this).unbind('blur').bind('blur', function () {
                myjs.insert_value_to_div();
            })
        });
    }
};

//Ham lay du lieu tren table sau do day ra bien an
Js_GeneralFormFiel.prototype.insert_value_to_div = function () {
    // lay mang du lieu tu bang cha
    var arrValue = new Array();
    var id = this.input_id;
    var row = this.row;
    var i = 0;
    for (i = 0; i < row; i++) {
        var item = [];
        var check = false;
        var idname = '';
        var val = '';
        var str = '';
        str = 'item={';
        $('.' + id + '_formfielgen_' + i.toString()).each(function () {
            idname = $(this).attr('id');
            val = $(this).val();
            if (val != '') {
                check = true;
            }
            str += idname + ':\'' + val + '\',';
        });
        str += '}';
        eval(str);
        if (check) {
            arrValue.push(item);
        }
    }
    //console.log(arrValue);
    var strrJson = JSON.stringify(arrValue);
    $("#input_" + this.input_id).val(strrJson);
};

//var Js_GeneralFormFiel = new Js_GeneralFormFiel();