/*Over all tour at very first time*/
var base_url = '<?php echo JURI::root();?>';


function restart_tabtour() {
    jQuery('a[href="#service-provider"]').click();
    tabtour.restart();
}

var tabtour = new Tour({
    name: "tabtour",
    steps: [
        {
            element: "#sptab",
            title: "Service Provider",
            content: "Configure this tab using SP information which you get from SP-Metadata XML.",
            backdrop: 'body',
            backdropPadding: '4',
            onNext: function () {
                jQuery('a[href="#identity-provider"]').click();

            },

        },
        {
            element: "#idptab",
            title: "IDP Metadata",
            content: "This tab provides details to configure with your Service Provider",
            backdrop: 'body',
            backdropPadding: '4',
            onPrev: function () {
                jQuery('a[href="#service-provider"]').click();
            },
            onNext: function () {
                jQuery('a[href="#iadvance_mapping"]').click();
            }
        },
        {
            element: "#advance_mapping_tab",
            title: "Advance Mapping",
            content: "In this tab, you can perform advanced attribute mapping.",
            backdrop: 'body',
            backdropPadding: '4',
            onPrev: function () {
                jQuery('a[href="#identity-provider"]').click();
            },
            onNext: function () {
                jQuery('a[href="#role_relay_restriciton_id"]').click();
            }
        },
        {
            element: "#rolerelay_restiction",
            title: "Role/Relay Restriction",
            content: "In this tab, you can configure role and relay restrictions.",
            backdrop: 'body',
            backdropPadding: '4',
            onPrev: function () {
                jQuery('a[href="#iadvance_mapping"]').click();
            },
            onNext: function () {
                jQuery('a[href="#signin_settings_id"]').click();
            }
        },
        {
            element: "#signin_settings_tab",
            title: "SignIn Settings ",
            content: "This tab provides a login link to user dashboard for login to your SP\n",
            backdrop: 'body',
            backdropPadding: '4',
            onPrev: function () {
                jQuery('a[href="#role_relay_restriciton_id"]').click();
            },
            onNext: function () {
                jQuery('a[href="#licensing-plans"]').click();
            }
        },
        {
            element: "#licensingtab",
            title: "Licensing Plans",
            content: " You can find premium features here and can upgrade to our premium plans.",
            backdrop: 'body',
            backdropPadding: '4',
            onNext: function () {
                jQuery('a[href="#request-demo"]').click();
            },
            onPrev: function () {
                jQuery('a[href="#signin_settings_id"]').click();
            }
        }, 
        {
            element: "#supporttab",
            title: "Trial or Demo",
            content: " You can request for trial or demo for our licensed version plugins.",
            backdrop: 'body',
            backdropPadding: '4',
            onNext: function () {
                jQuery('a[href="#help"]').click();
            },
            onPrev: function () {
                jQuery('a[href="#licensing-plans"]').click();
            },
        },
        {
            element: "#idp_sp_config_end_tour",
            title: "Tab tour option",
            content: "You can find the start tour button on each tab which will help you to configure the tab/get the information from that tab.",
            backdrop: 'body',
            backdropPadding: '4',
            onNext: function () {
                jQuery('a[href="#service-provider"]').click();
            },
            onPrev: function () {
                jQuery('a[href="#licensing-plans"]').click();
            }
        },{
            element: "#end_tab_tour",
            title: "Plugin tour option",
            content: "Click here to know what each tab does",
            backdrop: 'body',
            backdropPadding: '4',
            onPrev: function () {
                jQuery('a[href="#licensing-plans"]').click();
            }
        }
    ]
});

function restart_tourrg() {
    tourrg.restart();
}

var tourrg = new Tour({
    name: "tour",
    steps: [
        {
            element: "#idpregistration",
            title: "Register with Us",
            content: "In order to upgrade the license you will need to register with us.",
            backdrop: 'body',
            backdropPadding: '6'
        },
        {
            element: "#mo_saml_login_btn",
            title: "Login",
            content: "Click here to login if you have already registered with miniOrange.",
            backdrop: 'body',
            backdropPadding: '6'
        },
        {
            element: "#idp_support",
            title: "Support Query",
            content: "Please feel free to reach out us. We would like to help you with configuration of plugin and more.",
            backdrop: 'body',
            backdropPadding: '6'
        },
        {
            element: "#idprg_end_tour",
            title: "Tour ends",
            content: "Click here to restart tour",
            backdrop: 'body',
            backdropPadding: '6'
        }
    ]
});


function restart_toursp() {
    toursp.restart();
}

var toursp = new Tour({
    name: "tour6",
    steps: [
        {
            element: "#name",
            title: "Service Provider Name",
            content: "Give any suitable name to your Service Provider in-order to identify easily.",
            backdrop: 'body',
            backdropPadding: '6'
        },
        {
            element: "#sp_entity",
            title: "SP Entity ID/Issuer ID",
            content: "You can find the EntityID in your SP-Metadata XML file enclosed in <code>entityDescriptor</code> tag having attribute as entityID.",
            backdrop: 'body',
            backdropPadding: '6'
        },
        {
            element: "#sp_sso_url",
            title: "ACS URL",
            content: " You can find the ACS URL in Your SP-Metadata XML file enclosed in <code>AssertionConsumerService</code> tag having attribute as Location.",
            backdrop: 'body',
            backdropPadding: '6'
        },
        {
            element: "#sp_nameid_format",
            title: "Name ID Format",
            content: "You can select NameID format to send in SAML response.",
            backdrop: 'body',
            backdropPadding: '6'
        },
        {
            element: "#assertion_sign",
            title: "Assertion Sign",
            content: "Signing a SAML  assertion ensures message integrity when the response/assertion is delivered to the relying party(SP).",
            backdrop: 'body',
            backdropPadding: '6'
        },
        {
            element: "#sp_nameid_attribute",
            title: "Name ID Attribute",
            content: "Select an option to send email/name with NameID.",
            backdrop: 'body',
            backdropPadding: '6'
        },
        {
            element: "#mo_saml_support",
            title: "Support Query",
            content: "Please feel free to reach us. We would like to help you with configuration of plugin and more.",
            backdrop: 'body',
            backdropPadding: '6'
        },
        {
            element: "#idp_sp_config_end_tour",
            title: "Tour ends",
            content: "Click here to restart tour",
            backdrop: 'body',
            backdropPadding: '6'
        }

    ]
});

function restart_touridp() {
    touridp.restart();
}

var touridp = new Tour({
    name: "tour",
    steps: [
        {
            element: "#idp_metadata",
            title: "IDP Metadata",
            content: "You can use any of these three methods to configure the IdP with your SP automatically.",
            backdrop: 'body',
            backdropPadding: '6'
        },
        {
            element: "#idp_metadata_url",
            title: "Metadata URL",
            content: "You can provide this metadata URL to your Service Provider to configure the IdP with your SP automatically.",
            backdrop: 'body',
            backdropPadding: '6'
        },
        {
            element: "#idp_download_metadata",
            title: "Downlaod Metadata File",
            content: "You can provide this metadata File to your Service Provider to configure the IdP with your SP automatically.",
            backdrop: 'body',
            backdropPadding: '6'
        },

        {
            element: "#idp_saml_config",
            title: "IDP information",
            content: "Use this Identity provider information / metadata to configure your Service Provider manually.",
            backdrop: 'body',
            backdropPadding: '6'
        }, {
            element: "#mo_saml_support",
            title: "Support Query",
            content: "Please feel free to reach us. We would like to help you with configuration of plugin and more.",
            backdrop: 'body',
            backdropPadding: '6'
        },
        {
            element: "#idpconfig_end_tour",
            title: "Tour ends",
            content: "Click here to restart tour",
            backdrop: 'body',
            backdropPadding: '6'
        }
    ]
});
