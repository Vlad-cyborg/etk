//** Compare for Joomshopping version 4.1.0 18.02.2019 (not for free) / author - Brooksus / website - http://brooksite.ru / license The MIT License (MIT); See \modules\mod_jshopping_compare\license.txt**//
jQuery(function() {
    "use strict";
    var controller = mod_compare_ajax_data.data_controller;
    var main_url = mod_compare_ajax_data.data_uri;
    var add_url = main_url + "index.php?option=com_jshopping&controller=compare&task=add";
    var remove_url = main_url + "index.php?option=com_jshopping&controller=compare&task=remove";
    var compare_url = jQuery("a.go_to_compre_mod").attr("href");
    //var len_moo = jQuery('script[src*="media/system/js/mootools-core.js"]').length;
    var cc = jQuery(".count_compare");
    var ccolor = jQuery("#jshop_module_compare").data("jscolor");
    var empty_text, maxquantity, ccq, comparemodal, str, str_c, dt, da, dg, dm, dad, dcb, dec, bsv, offcheader;
    empty_text = mod_params_compare.empty_text;
    maxquantity = mod_params_compare.compare_quantity;
    ccq = mod_params_compare.check_compare_quantity;
    comparemodal = mod_params_compare.compare_modal;
    dt = mod_compare_ajax_data.data_to,
        da = mod_compare_ajax_data.data_add,
        dg = mod_compare_ajax_data.data_go,
        dm = mod_compare_ajax_data.data_max,
        dad = mod_compare_ajax_data.data_added,
        dcb = mod_compare_ajax_data.data_cb,
        dec = mod_compare_ajax_data.data_ec,
        bsv = mod_compare_ajax_data.data_bsv,
        offcheader = mod_compare_ajax_data.data_offcheader;

    //Button click
    jQuery("button.list-btn.cp.in-compare").attr("data-original-title", dg);

    //Tooltip
    jQuery("body").tooltip({
        selector: '[data-rel="tooltip"], [rel="tooltip"]',
        trigger: "hover"
    });
    var collapse;
    if (bsv === "bs3" || bsv === "bs4") {
        collapse = "";
    } else {
        collapse = "collapse";
    }

    function createModalCompare() {
        jQuery('body').append('<div id="alert_compare" class="modal-alert-compare ' + ccolor + ' modal fade ' + collapse + '" tabindex="-1" role="dialog" aria-labelledby="alert_compareLabel" aria-hidden="true"><div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-body"><p>' + dm + '</p><div class="compare_modal_link"><p><a href="' + compare_url + '">' + dg + '</a></p></div></div></div></div>');
        jQuery('#alert_compare').modal();
    }

    function openModalCompare() {
        jQuery('body').append('<div id="modal_compare" class="modal-compare ' + ccolor + ' modal fade ' + collapse + '" tabindex="-1" role="dialog" aria-labelledby="modal-compareLabel" aria-hidden="true"><div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-body"></div></div></div></div>');
        jQuery('#modal_compare .modal-body').empty();
        jQuery('#compare_table_content').appendTo('#modal_compare .modal-body');
        jQuery('#modal_compare').modal();
        jQuery('#modal_compare').on("hidden.bs.modal", function() {
            if (ccq === "1" && cc.text() >= maxquantity) {
                createModalCompare();
                return false;
            }
        });
    }

    function qiuckView(ids) {
        jQuery("a.compare_link_to_list[data-id='comparelist_" + ids + "']").addClass("compare_dnone");
        if (bsv === "bs4") {
            jQuery("a.compare_link_to_list[data-id='comparelist_" + ids + "']").tooltip('dispose');
        } else {
            jQuery("a.compare_link_to_list[data-id='comparelist_" + ids + "']").tooltip('destroy');
        }
        jQuery("a.remove_compare_list[data-id='removelistid_" + ids + "']").removeClass("compare_dnone");
        setTimeout(function() {
            jQuery("a.remove_compare_list[data-id='removelistid_" + ids + "']").tooltip('hide');
        }, 1500);
        jQuery("a.go_to_compre_list[data-id='gotocomparelist_" + ids + "']").removeClass("compare_dnone");
        jQuery("#compare_" + ids).addClass("compare_dnone");
        jQuery("#removeid_" + ids).removeClass("compare_dnone");
        jQuery("#gotocompare_" + ids).removeClass("compare_dnone");
        jQuery("button.list-btn.cp").attr("data-original-title", dg);
    }

    function quickViewRemove(ids) {
        jQuery("a.compare_link_to_list[data-id='comparelist_" + ids + "']").removeClass("compare_dnone");
        setTimeout(function() {
            jQuery("a.compare_link_to_list[data-id='comparelist_" + ids + "']").tooltip('hide');
        }, 1500);
        jQuery("a.remove_compare_list[data-id='removelistid_" + ids + "']").addClass("compare_dnone");
        if (bsv === "bs4") {
            jQuery("a.remove_compare_list[data-id='removelistid_" + ids + "']").tooltip('dispose');
        } else {
            jQuery("a.remove_compare_list[data-id='removelistid_" + ids + "']").tooltip('destroy');
        }
        jQuery("a.go_to_compre_list[data-id='gotocomparelist_" + ids + "']").addClass("compare_dnone");
        jQuery("#compare_" + ids).removeClass("compare_dnone");
        jQuery("#removeid_" + ids).addClass("compare_dnone");
        jQuery("#gotocompare_" + ids).addClass("compare_dnone");
        jQuery(".compare_mod_" + ids).remove();
        jQuery("button.list-btn.cp").attr("data-original-title", da);
    }

    function checkQuantity() {
        if (ccq === "1" && cc.text() >= maxquantity) {
            if (comparemodal === "1") {
                createModalCompare();
            } else {
                alert(dm);
            }
            return "max";
        }
    }

    function emptyCompare() {
        if (cc.text() === "0") {
            jQuery(".go_to_compre_mod").addClass("compare_dnone");
            jQuery(".empty_compare_text").removeClass("compare_dnone");
        }
        if (controller === "compare" && jQuery(".dopcompare").length) {
            location.reload();
        }
    }

    function ajaxRequestCompare(data) {
        str = "";
        str_c = "";
        JSON.parse(data, function(key, value) {
            if (key === 'ids') {
                str += "<div class='compare_mod_" + value + " extern_row_compare' >";
            }
            if (key === 'image') {
                str += "<div class='compare_mod_img pict'><span class='pict'><img src='" + value + "'/></span></div>";
            }
            if (key === 'link') {
                str += "<div class='compare_mod_name name'><span class='name'>" + value + "</span>";
            }
            if (key === 'price') {
                str += "<div class='price'><span class='summ'>" + value + "</span></div></div>";
            }
            if (key === 'remove') {
                str += "<div class='compare_mod_remove delete'><span class='delete'>" + value + "</span></div></div>";
            }
            if (key === 'count') {
                str_c += value;
            }
        });
        //****AJAX RESAULT IF MODAL START****//
        if (comparemodal === "1") {
            jQuery(".mycompare_table").html("<div class='compare_mode_table extern_content_compare'>" + str + "</div><div id='compare_table_content'><div class='compare_modal_header'><H2>" + dad + "</H2></div><div class='compare_mode_table extern_content_compare'>" + str + "</div><div class='compare_comeback'><a href='#'>" + dcb + "</a></div><div class='compare_modal_link'><a href='" + compare_url + "'>" + dt + "</a></div></div>");
        } else {
            jQuery(".mycompare_table").html("<div class='compare_mode_table extern_content_compare'>" + str + "</div>");
        }
        //****AJAX RESULT IF MODAL END****//
        cc.empty();
        cc.html(str_c.length);
        jQuery(".go_to_compre_mod").removeClass("compare_dnone");
        jQuery(".empty_compare_text").addClass("compare_dnone");
        if (cc.text() === "0") {
            jQuery(".go_to_compre_mod").addClass("compare_dnone");
            jQuery(".empty_compare_text").removeClass("compare_dnone");
        }
        //**FOR MOD END**//
    }

    function isElementHidden() {
        var viewportWidth = jQuery(window).width(),
            viewportHeight = jQuery(window).height(),
            documentScrollTop = jQuery(document).scrollTop(),
            documentScrollLeft = jQuery(document).scrollLeft(),

            $myElement = jQuery("#jshop_module_compare"),

            elementOffset = $myElement.offset(),
            elementHeight = $myElement.height(),
            elementWidth = $myElement.width(),

            minTop = documentScrollTop,
            maxTop = documentScrollTop + viewportHeight,
            minLeft = documentScrollLeft,
            maxLeft = documentScrollLeft + viewportWidth;

        if (
            (elementOffset.top > minTop && elementOffset.top + elementHeight < maxTop) &&
            (elementOffset.left > minLeft && elementOffset.left + elementWidth < maxLeft)
        ) {
            return true;
        } else {
            return false;
        }
    }


    function animateCompare(ids, et) {

        var imgProduct, et;
        if (jQuery(".jshop_list_product").length && jQuery(".image_block img").length) {
            imgProduct = jQuery(".productitem_" + ids + " .image_block img").not(".product_label img").attr("src");
        }
        if (jQuery(".productfull").length) {
            imgProduct = jQuery("img[id^='main_image_']").eq(0).attr("src");
        }
        if (et.parents(".dop_products").length) {
            imgProduct = et.parents(".dop_products .modopprod_item, .dop_products .item").not(".dop_products.vertical .modopprod_item").find("a img").attr("src");
        }
        var ine;
        if ((jQuery(".compare-bottom-bar").length || jQuery(".wl-bottom-bar").length) && !isElementHidden()) {
            ine = jQuery(".compare-bottom-bar");
        } else {
            ine = jQuery("#jshop_module_compare");
        }

        var outepos = et.offset(),
            oute_top = outepos.top,
            oute_left = outepos.left,
            inepos = ine.offset(),
            ine_left = inepos.left,
            ine_top = inepos.top;
        if (imgProduct) {
            jQuery("body").append("<img class='new-clone-img' src='" + imgProduct + "'/>");
        }
        jQuery(".new-clone-img").css({
            "position": "absolute",
            "z-index": "9999",
            "max-width": 75,
            "max-height": 75,
            "min-width": 30,
            "min-height": 30,
            "top": oute_top - 20 + "px",
            "left": oute_left + "px"
        }).stop().animate({
            opacity: 0.6,
            top: ine_top,
            left: ine_left + 25,
            width: 0,
            height: 0,
            minWidth: 0,
            minHeight: 0,
        }, 1500, function() {
            jQuery(newFunction()).remove();
        });

        function newFunction() {
            return ".new-clone-img";
        }
    }

    var len_jshp = jQuery('script[src*="plugins/system/joomshopkit_v2/assets/js/custom_combine.js"]').length;
    if (!len_jshp) {
        jQuery("body").on("click", "button.list-btn.cp", function() {
            jQuery(".list-btn.button.cp:not('.compare_dnone')").trigger("click");
        });
    }
    if (jQuery(".btn.list-btn.button.remove_comp:not('.compare_dnone')").length) {
        jQuery("button.list-btn.cp").attr("data-original-title", "Перейти к сравнению");
    }
    if (jQuery(".btn.list-btn.button.remove_comp.compare_dnone").length) {
        jQuery("button.list-btn.cp").attr("data-original-title", "Добавить к сравнению");
    }

    //PRODUCTS START//
    jQuery("body").on("click", "a.compare_link_to_list", function(event) {
        event.preventDefault();
        //**CHECK QUANTITY START**//
        if (checkQuantity() === "max") {
            return false;
        }

        var ids = jQuery(this).attr("data-id").split("comparelist_")[1];
        var dcompare = jQuery(this).attr("data-compare");
        var et = jQuery(this);
        jQuery(this).addClass("prelative").append('<div class="ajaxloadding"></div>');
        jQuery.ajax({
            cache: false,
            url: add_url,
            data: "ids=" + ids + "&" + dcompare,
            type: 'POST',
            ifModified: true,
            success: function(data) {
                jQuery(".ajaxloadding").remove();
                jQuery(this).removeClass("prelative");
                //**FOR MOD START**//
                ajaxRequestCompare(data);

                //Animation
                if (comparemodal === "2") {
                    animateCompare(ids, et);
                }

                //FOR QUICK VIEW
                qiuckView(ids);
                //**MODAL START**//
                if (comparemodal === "1") {
                    //****CLOSE COMPARE****//
                    jQuery("body").on("click", ".compare_comeback", function(event) {
                        event.preventDefault();
                        jQuery(".modal").modal("hide");
                    });
                    openModalCompare();
                } else {
                    if (ccq === "1" && cc.text() >= maxquantity) {
                        setTimeout(function() {
                            alert(dm);
                            return false;
                        }, 1500);
                    }
                }
                //**MODAL END**//
                if (controller === "compare" && jQuery(".dopcompare").length) {
                    location.reload();
                }
            },
            error: function() {
                jQuery(".ajaxloadding").remove();
                jQuery(this).removeClass("prelative");
            }
        });
    });

    //**DELETE FROM LIST**//
    jQuery("body").on("click", "a.remove_compare_list", function(event) {
        event.preventDefault();
        var ids = jQuery(this).attr("data-id").split("removelistid_")[1];
        jQuery(this).addClass("prelative").append('<div class="ajaxloadding"></div>');
        jQuery.ajax({
            cache: false,
            url: remove_url,
            data: "removeid=" + ids,
            type: 'POST',
            ifModified: true,
            success: function() {
                jQuery(".ajaxloadding").remove();
                jQuery(this).removeClass("prelative");
                quickViewRemove(ids);
                cc.html(cc.text() - 1);
                emptyCompare();
            },
            error: function() {
                jQuery(".ajaxloadding").remove();
                jQuery(this).removeClass("prelative");
            }
        });
    });
    //PRODUCTS END//

    //************//

    //PRODUCT CARD START//	
    jQuery("body").on("click", ".comp_add", function(event) {
        event.preventDefault();
        if (bsv === "bs4") {
            jQuery(".tooltip.in").tooltip("dispose");
        } else {
            jQuery(".tooltip.in").tooltip("destroy");
        }
        if (jQuery(event.target).is("[class*='icon-']") || jQuery(event.target).is("button.list-btn.cp")) {
            jQuery(".comp_add").trigger("click");
        }
        //**CHECK QUANTITY START**//
        if (checkQuantity() === "max") {
            return false;
        }

        var ids = jQuery(this).attr("id").split("compare_")[1];
        var dcompare = jQuery(this).attr("data-compare");
        var et = jQuery(this).parent("div");
        jQuery(this).parent().addClass("prelative").append('<div class="ajaxloadding"></div>');
        jQuery.ajax({
            cache: false,
            url: add_url,
            data: "ids=" + ids + "&" + dcompare,
            type: 'POST',
            ifModified: true,
            success: function(data) {
                //console.log(data);
                jQuery(".ajaxloadding").remove();
                jQuery(this).parent().removeClass("prelative");
                //**FOR MOD START**//
                ajaxRequestCompare(data);

                //Animation
                if (comparemodal === "2") {
                    animateCompare(ids, et);
                }

                //FOR QUICK VIEW
                qiuckView(ids);
                //**MODAL START**//
                if (comparemodal === "1") {
                    //****CLOSE COMPARE****//
                    jQuery("body").on("click", ".compare_comeback", function(event) {
                        event.preventDefault();
                        jQuery(".modal").modal("hide");
                    });
                    openModalCompare();
                } else {
                    if (ccq === "1" && cc.text() >= maxquantity) {
                        setTimeout(function() {
                            alert(dm);
                            return false;
                        }, 1500);
                    }
                }
                //**MODAL END**//

            },
            error: function() {
                jQuery(".ajaxloadding").remove();
                jQuery(this).parent().removeClass("prelative");
            }
        });
    });

    //Remove from prod
    jQuery("body").on("click", ".remove_comp", function(event) {
        event.preventDefault();
        var ids = jQuery(this).attr("id").split("removeid_")[1];
        jQuery(this).parent().addClass("prelative").append('<div class="ajaxloadding"></div>');
        jQuery.ajax({
            cache: false,
            url: remove_url,
            data: "removeid=" + ids,
            type: 'POST',
            ifModified: true,
            success: function() {
                jQuery(".ajaxloadding").remove();
                jQuery(this).parent().removeClass("prelative");
                //FOR QUICK VIEW
                quickViewRemove(ids);
                cc.html(cc.text() - 1);
                if (cc.text() === "0") {
                    jQuery(".go_to_compre_mod").addClass("compare_dnone");
                    jQuery(".empty_compare_text").removeClass("compare_dnone");
                }
            },
            error: function() {
                jQuery(".ajaxloadding").remove();
                jQuery(this).parent().removeClass("prelative");
            }
        });
    });
    //PRODUCT CARD END//

    //***************//

    //COMPARE VIEW START//
    jQuery("body").on("click", "a.remove_compare_view", function(event) {
        event.preventDefault();
        var ids = jQuery(this).attr("id").split("removeviewid_")[1];
        jQuery(this).addClass("prelative").append('<div class="ajaxloadding"></div>');
        jQuery.ajax({
            cache: false,
            url: remove_url,
            data: "removeid=" + ids,
            type: 'POST',
            ifModified: true,
            success: function() {
                jQuery(".ajaxloadding").remove();
                jQuery(this).removeClass("prelative");
                jQuery(".compare_view_" + ids).remove();
                jQuery(".compare_mod_" + ids).remove();
                cc.html(cc.text() - 1);
                emptyCompare();
                if (controller === "compare" && jQuery(".dopcompare").length) {
                    location.reload();
                }
            },
            error: function() {
                jQuery(".ajaxloadding").remove();
                jQuery(this).removeClass("prelative");
            }
        });
    });
    //COMPARE VIEW END//

    //COMPARE MOD START//
    jQuery("body").on("click", "a.remove_compare_mod", function(event) {
        event.preventDefault();
        var ids = jQuery(this).attr("id").split("removemodid_")[1];
        jQuery(this).addClass("prelative").append('<div class="ajaxloadding"></div>');
        jQuery.ajax({
            cache: false,
            url: remove_url,
            data: "removeid=" + ids,
            type: 'POST',
            ifModified: true,
            success: function() {
                jQuery(".ajaxloadding").remove();
                jQuery(this).removeClass("prelative");
                quickViewRemove(ids);
                jQuery(".compare_view_" + ids).remove();
                cc.html(cc.text() - 1);
                if (cc.text() === "0") {
                    jQuery(".go_to_compre_mod, .compare_modal_header, .compare_modal_link, .compare_comeback").addClass("compare_dnone");
                    jQuery("#sbox-content .compare_mode_table").html("<tr><td style='text-align:center;'>" + dec + "</td></tr>");
                    jQuery(".empty_compare_text").removeClass("compare_dnone");
                    jQuery("#modal_compare .modal-body").html(dec);
                }
                if (controller === "compare" && jQuery(".dopcompare").length) {
                    location.reload();
                }
            },
            error: function() {
                jQuery(".ajaxloadding").remove();
                jQuery(this).removeClass("prelative");
            }
        });
    });
    //COMPARE MOD END//

    //Offcanvas
    function offcanvasCompare() {
        var ofcw;
        if (jQuery(".offcanvas-compare-wrapp").length) {
            ofcw = "";
        } else {
            ofcw = "<div class='offcanvas-compare-wrapp'></div>";
        }
        jQuery(".offcanvas-compare").toggleClass("showin_compare").append(ofcw);
        if (!jQuery(".prepend-compare-text").length) {
            jQuery(".offcanvas-compare .mycompare_content").prepend("<div class='offcanvas-compare-close'>X</div><p class='prepend-compare-text'>" + offcheader + "</p>");
        }
        jQuery("body").toggleClass("offcanvas-on");
    }

    //TOGGLE
    jQuery("body").on("click", ".click_mycompare, .offcanvas-compare-close, .offcanvas-compare-wrapp", function(e) {
        e.preventDefault();
        jQuery(".mycompare_content").fadeToggle();
        offcanvasCompare();
    });

    jQuery(".js-event-prevent .compare-bottom-bar").click(function() {
        jQuery(".offcanvas-compare .mycompare_content").fadeIn('5000');
        offcanvasCompare();
    });

    //Empty Text
    if (cc.text() === "0") {
        jQuery(".empty_compare_text").html("<span>" + empty_text + "</span>");
    } else {
        jQuery(".empty_compare_text").html("<span>" + empty_text + "</span>").addClass("compare_dnone");
    }

    if (jQuery(".min_view.compare-wrapp").length) {
        var cp = jQuery(".min_view.compare-wrapp"),
            cpc = jQuery(".mycompare_content"),
            cpw = cp.outerWidth(),
            cpcw = cpc.outerWidth(),
            cpo = cp.offset(),
            cpl = cpo.left,
            cpr = jQuery(window).outerWidth() - cpl;
        if (cpl < (cpcw / 2 - cpw) || cpr < (cpcw / 2 - cpw)) {
            cpc.css({
                "left": -cpl
            });
        } else {
            cpc.css({
                "left": -(cpcw / 2 - cpw / 2)
            });
        }
    }
});