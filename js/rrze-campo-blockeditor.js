"use strict";function getCampoDataForBlockelements(e,n){var t=jQuery("input#number").val(),n=jQuery(n);t&&(n.html('<option value="">loading... </option>'),jQuery.post(campo_ajax.ajax_url,{_ajax_nonce:campo_ajax.nonce,action:"GetCampoDataForBlockelements",data:{campoOrgID:t,dataType:e}},function(e){n.html(e)}))}wp.domReady(function(){jQuery(document).ready(function(e){jQuery(document).on("change","input#number",function(){getCampoDataForBlockelements("personAll","select#campoid"),getCampoDataForBlockelements("lectureByDepartment","select#id")})})});