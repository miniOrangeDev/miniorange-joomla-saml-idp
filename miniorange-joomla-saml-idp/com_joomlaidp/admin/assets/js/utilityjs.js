function backToDefaultTab() {
    jQuery('#idp_default_form').submit();
}

jQuery('#idp_premium_upgrade').click(function () {
    jQuery('#requestOrigin').val('joomla_saml_idp_premium_plan');
    jQuery('#moidp_loginform').submit();
});

function add_css_tab(element) {
    jQuery(".mo_nav_tab_active ").removeClass("mo_nav_tab_active").removeClass("active");
    jQuery(element).addClass("mo_nav_tab_active");
}

function mo_back_btn()
{
    jQuery('#idp_cancel_form').submit();
}

function resendOTPForm()
{
    jQuery("#resend_otp_form").submit();
}

function moCancelForm()
{
    jQuery("#mo_saml_idp_cancel_form").submit();
}

jQuery(document).ready(function () {
    jQuery(".mo_idp_users_details").change(function () {
        
        jQuery(this).parent().parent().parent().parent().siblings().children().children().children().children(this).val(jQuery(this).val());

        jQuery("#mo_idp_price_slab1_100").css("display", "none");
        jQuery("#mo_idp_price_slab1_200").css("display", "none");
        jQuery("#mo_idp_price_slab1_300").css("display", "none");
        jQuery("#mo_idp_price_slab1_400").css("display", "none");
        jQuery("#mo_idp_price_slab1_500").css("display", "none");
        jQuery("#mo_idp_price_slab1_750").css("display", "none");
        jQuery("#mo_idp_price_slab1_1000").css("display", "none");
        jQuery("#mo_idp_price_slab1_2000").css("display", "none");
        jQuery("#mo_idp_price_slab1_3000").css("display", "none");
        jQuery("#mo_idp_price_slab1_4000").css("display", "none");
        jQuery("#mo_idp_price_slab1_5000").css("display", "none");
        jQuery("#mo_idp_price_slab1_5000p").css("display", "none");
        if(jQuery(this).val()=='5000p')
        {
            jQuery(".mo_idp_plans").css("display", "none");
        }
        else
        {
            jQuery(".mo_idp_plans").css("display", "block");
        }
        jQuery("#mo_idp_price_slab1_"+jQuery(this).val()).css("display", "block");

       
    });

    jQuery('#upload_metadata_file').click(function(){
        var file = document.getElementById("metadata_uploaded_file");
        var sp_name = jQuery("#sp_upload_name").val();
        if(file.files.length != 0 && sp_name!==''){
            jQuery('#IDP_metadata_form').submit();
        } 
        else if(sp_name=='')
        {
            alert("Please Enter the SP name");   
        }else {
            alert("Please upload the metadata file");
            jQuery('#metadata_url').attr('required',false);
            jQuery('#metadata_uploaded_file').attr('required',true);
        }
            
    });

    jQuery('#fetch_metadata').click(function(){
        var url = jQuery("#metadata_url").val();
        var sp_name = jQuery("#sp_upload_name").val();
        if(url!='' && sp_name!=='' )
        {
            jQuery('#IDP_metadata_form').submit(); 
        }
        else if(sp_name=='')
        {
            alert("Please Enter the SP name");   
        }
        else{
            alert("Please enter the metadata URL");
            jQuery('#metadata_uploaded_file').attr('required',false);
            jQuery('#metadata_url').attr('required',true);
        }
                
        });


        jQuery('#select_sp').click(function(){
            jQuery('#select_sp').toggle();
            jQuery('#myInput').toggle();
            jQuery('#dropdown-test').toggle();
        });

        jQuery(document).ready(function(){
            jQuery(".dropdown_option").click(function(){
                var guide_name=jQuery(this).attr('id');
                jQuery('#myInput').toggle();
                jQuery('#dropdown-test').toggle();
                jQuery('#select_sp').css("text-align","center");
                jQuery('#select_sp').text(guide_name);
                jQuery('#select_sp').toggle();
                jQuery('#select_sp').append( "<span style='float:right!important' ><i class='fa fa-angle-down'></i></span>" );
            });

        });
        

        jQuery(document).on("click", function(event){
            var $trigger = jQuery(".start_dropdown");
            if($trigger !== event.target && !$trigger.has(event.target).length){
                jQuery("#myInput").slideUp("fast");
                jQuery("#dropdown-test").slideUp("fast");
                jQuery('#select_sp').show();
            }            
        });

        jQuery('.show_extra_information').click(function()
        {
            jQuery('#mo_saml_gen_cert_info').slideToggle('fast');
        });

        jQuery('.call_date_picker').click(function(){
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0');
            var yyyy = today.getFullYear();
            today = yyyy + '-' + mm + '-' + dd;
            jQuery('.call_date_picker').attr('min',today);
        });
});

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

function mo_login_page()
{
    jQuery('#cus_login_form').submit();
}

function removeUserAccount()
{
    jQuery("#saml-sp-key-form").submit();
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
    jQuery(element).siblings().children('.copied_text').text('Copied');
}

jQuery(window).click(function (e) {
    if (e.target.className === undefined || e.target.className.indexOf("fa-copy") === -1)
        jQuery(".selected-text").removeClass("selected-text");
});
function showmodal(){
    jQuery('#myModal').css("display","block");
}
function hidemodal(){
    jQuery('#myModal').css("display","none");
}
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
window.addEventListener('DOMContentLoaded', function () {
        let supportButtons = document.getElementsByClassName('mo_saml_idp_request_quote');
        let supportForms = document.getElementsByClassName('mo_saml_idp_request_quote_form');
        for (let i = 0; i < supportButtons.length; i++) {
            supportButtons[i].addEventListener("click", function (e) {
                if (supportForms[0].style.right !== "0px") {
                    supportForms[0].style.right = "0px";
                } else {
                    supportForms[0].style.right = "-391px";
                }
            });
        }
    }
);

function guide(gvalue) {
    if (gvalue !== '1')
        window.open(gvalue);
}

function showTestWindow() {
    var issuer = jQuery('#sp_entityid').val();
    var acsUrl = jQuery('#acs_url').val();
    if (issuer === "" || acsUrl === "") {
        alert("Please provide your SP details first and then click on Test Configuration button.")
    } else {
        var url = jQuery('#idp-initiated-url').val();
        var idpInitiatedUrl = url + '&acs=' + acsUrl + '&issuer=' + issuer;
        var myWindow = window.open(idpInitiatedUrl, 'TEST SAML IDP', 'scrollbars=1 width=800, height=600');
    }
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

function moSAMLAccount() {
    jQuery('a[href="#description"]').click();
    add_css_tab("#accounttab");
}



function MyClose(){
    jQuery("#my_TC_Modal").css("display","none");
    location.reload();
}
function show_TC_modal(){
    jQuery("#my_TC_Modal").css("display","block");
}
