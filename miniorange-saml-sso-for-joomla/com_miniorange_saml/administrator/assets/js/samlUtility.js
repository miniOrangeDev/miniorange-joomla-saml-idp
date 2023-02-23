function mo_login_page() {
    jQuery('#customer_login_form').submit();
}

function moSAMLUpgrade() {
    jQuery('a[href="#licensing-plans"]').click();
    add_css_tab("#licensingtab");
}

        
function add_css_tab(element) {
    jQuery(".mo_nav_tab_active ").removeClass("mo_nav_tab_active").removeClass("active");
    jQuery(element).addClass("mo_nav_tab_active");
}

function show_proxy_form_one() {
    jQuery('#submit_proxy').show();
    jQuery('#panel1').hide();
    jQuery('#sp_proxy_setup').hide();
}

function show_proxy_form() {
    jQuery('#submit_proxy').show();
    jQuery('#cum_pro').hide();
    jQuery('#sp_proxy_setup').hide();
}

function hide_proxy_form() {
    jQuery('#submit_proxy').hide();
    jQuery('#cum_pro').show();
    jQuery('#panel1').show();
    jQuery('#sp_proxy_setup').show();
}

function moSAMLBack() {
    jQuery('#mo_saml_cancel_form').submit();
}

function moSAMLCancelForm() {
    jQuery('#cancel_form').submit();
}

function copyToClipboard(element) {
    jQuery(".selected-text").removeClass("selected-text");
    var temp = jQuery("<input>");
    jQuery("body").append(temp);
    jQuery(element).addClass("selected-text");
    temp.val(jQuery(element).text().trim()).select();
    document.execCommand("copy");
    temp.remove();
    jQuery(element).parent().siblings().children().children('.copied_text').text('Copied');
    jQuery(element).parent().parent().siblings().children().children('.copied_text').text('Copied');
    jQuery(element).siblings().children('.copied_text').text('Copied');
}

jQuery(window).click(function (e) {
    if (e.target.className === undefined || e.target.className.indexOf("fa-copy") === -1)
        jQuery(".selected-text").removeClass("selected-text");
});

jQuery(document).ready(function () {
    var basepath = window.location.href;
    basepath = basepath.substr(0, basepath.indexOf('administrator')) + 'plugins/authentication/miniorangesaml/';
    jQuery('.site-url').text(basepath);
    jQuery('.premium').click(function () {
        jQuery('.nav-tabs a[href="#attrib-licensing_plans"]').tab('show');
    });
});

var homepath = window.location.href;
var homepath = homepath.substr(0, homepath.indexOf('administrator'));
basepath = homepath + 'plugins/authentication/miniorangesaml/';
jQuery(document).ready(function () {
    jQuery('#metadata-link').attr('href', homepath + '?morequest=metadata');
});

function showmodal(){
    jQuery('#myModal').css("display","block");
}
function hidemodal(){
    jQuery('#myModal').css("display","none");
}

jQuery('#add_mapping').click(function () {
    var dropdown = jQuery("#wp_roles_list").html();
    var new_row = '<tr><td><input disabled class="mo_saml_table_textbox" type="text" placeholder="cn=group,dc=domain,dc=com" name="mapping_key_1" value="" /></td><td><select disabled name="mapping_value_1" class="mo_boot_form-control" id="role">' + dropdown + '</select></td></tr>';
    jQuery('#saml_role_mapping_table tr:last').after(new_row);
});

function show_gen_cert_form() {
    jQuery("#generate_certificate_form").show();
    jQuery("#mo_gen_cert").hide();
    jQuery("#mo_gen_tab").hide();
}

function hide_gen_cert_form() {
    jQuery("#generate_certificate_form").hide();
    jQuery("#mo_gen_cert").show();
    jQuery("#mo_gen_tab").show();
}

function validateEmail(emailField) {
    var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;

    if (reg.test(emailField.value) === false) {
        document.getElementById('email_error').style.display = "block";
        document.getElementById('submit_button').disabled = true;
    } else {
        document.getElementById('email_error').style.display = "none";
        document.getElementById('submit_button').disabled = false;
    }
}

jQuery(function () {
    jQuery("#idp_guides").change(function () {
        var selectedIdp = jQuery(this).find("option:selected").val();
        window.open(selectedIdp, "_blank");
    });
});

function showLink() {
    if (document.getElementById('login_link_check').checked)
        document.getElementById('dynamicText').style.display = 'block';
    else
        document.getElementById('dynamicText').style.display = 'none';
}

function showTestWindow() {
    var testconfigurl = window.location.href;
    testconfigurl = testconfigurl.substr(0, testconfigurl.indexOf('administrator')) + '?morequest=sso&q=test_config';
    var myWindow = window.open(testconfigurl, 'TEST SAML IDP', 'scrollbars=1 width=800, height=600');
}

function showSAMLRequest() {
    var myWindow = window.open("<?php echo mo_saml_get_saml_request_url(); ?>", "VIEW SAML REQUEST", "scrollbars=1 width=800, height=600");
}

function showSAMLResponse() {
    var myWindow = window.open("<?php echo mo_saml_get_saml_response_url(); ?>", "VIEW SAML RESPONSE", "scrollbars=1 width=800, height=600");
}

function show_metadata_form() {
    jQuery('#upload_metadata_form').show();
    jQuery('#idpdata').hide();
    jQuery('#tabhead').hide();
}

function hide_metadata_form() {
    jQuery('#upload_metadata_form').hide();
    jQuery('#idpdata').show();
    jQuery('#tabhead').show();
}

function show_import_export() {
    jQuery("#import_export_form").show();
    jQuery("#idpdata").hide();
    jQuery("#tabhead").hide();
}

function hide_import_export_form() {
    jQuery("#import_export_form").hide();
    jQuery("#idpdata").show();
    jQuery("#tabhead").show();
}
function filterFunction() {
    jQuery('.dropdown_options').css("display","block");
        var input, filter, ul, li, a, i;
        input = document.getElementById("myInput");
        filter = input.value.toUpperCase();
        div = document.getElementById("myDropdown");
        a = div.getElementsByTagName("a");
        var c=0;
        for (i = 0; i < a.length; i++) {
            jQuery('.mo_dropdown_options').css("height","auto");
            txtValue = a[i].textContent || a[i].innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
            a[i].style.display = "";
            c++;
            } else {
            a[i].style.display = "none";
            }
        }
    }


var homepath = window.location.href;
var homepath = homepath.substr(0, homepath.indexOf('administrator'));
basepath = homepath + 'plugins/authentication/miniorangesaml/';
jQuery(document).ready(function () {
    jQuery('#metadata-link').attr('href', homepath + '?morequest=metadata');
    jQuery(document).ready(function()
    {
        var inputValue = "text_cert";
        var targetBox = jQuery("." + inputValue);
        jQuery(".selectt").not(targetBox).hide();
        jQuery(targetBox).show();

        jQuery('input[type="radio"]').click(function() {
        var inputValue = jQuery(this).attr("value");
        var targetBox = jQuery("." + inputValue);
        jQuery(".selectt").not(targetBox).hide();
        jQuery(targetBox).show();
        });
    });
    
    jQuery('#select_idp').click(function(){
        jQuery('#select_idp').toggle();
        jQuery('#myInput').toggle();
        jQuery('#dropdown-test').toggle();
    });

        
    jQuery(document).ready(function(){
        jQuery(".dropdown_option").click(function(){
            var guide_name=jQuery(this).attr('id');
            jQuery('#myInput').toggle();
            jQuery('#dropdown-test').toggle();
            jQuery('#select_idp').attr('value',guide_name);
            jQuery('#select_idp').css("text-align","center");
            jQuery('#select_idp').text(guide_name);
            jQuery('#select_idp').toggle();
            jQuery( "#select_idp" ).append( "<span style='float:right!important' ><i class='fa fa-angle-down'></i></span>" );
        });

    });

    jQuery(document).on("click", function(event){
        var $trigger = jQuery(".start_dropdown");
        if($trigger !== event.target && !$trigger.has(event.target).length){
            jQuery("#myInput").slideUp("fast");
            jQuery("#dropdown-test").slideUp("fast");
            jQuery('#select_idp').show();
        }            
    });

    jQuery(".mo_sp_inclusive_plans").change(function () {
        jQuery("#plus_total_price_basic").css("display", "none");
        jQuery("#plus_total_price_pro").css("display", "none");
        jQuery("#plus_total_price_"+jQuery(this).val()).css("display", "block");
        if(jQuery(this).val()=='basic')
        {
            jQuery("#mo_pricing_list4").css("display", "none");
            jQuery("#mo_pricing_list3").css("display", "none");
            jQuery("#mo_pricing_list2").css("display", "block");
            jQuery("#mo_pricing_list1").css("display", "block");
        }else
        {
            jQuery("#mo_pricing_list4").css("display", "block");
            jQuery("#mo_pricing_list3").css("display", "block");
            jQuery("#mo_pricing_list2").css("display", "none");
            jQuery("#mo_pricing_list1").css("display", "none");
        }
    });
});

