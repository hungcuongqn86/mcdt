/*
 * Creater: ##
 * Date:##
 * Idea: ##
 */
function site_search(baseUrl, module, controller) {
    this.module = module;
    this.baseUrl = baseUrl;
    this.controller = controller;
    this.urlPath = baseUrl + '/' + module + '/' + controller;
    mysite_search = this;
    this.loadding = false;
    this.frmIndex = $('form#frm_search_index')
}