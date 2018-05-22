

var MYMAP = new Array();
var wpgmzaTable = new Array();

var directionsDisplay = new Array();
var directionsService = new Array();
var infoWindow = new Array();
var store_locator_marker = new Array();
var cityCircle = new Array();
var infoWindow_poly = new Array();
var polygon_center = new Array();
var WPGM_Path_Polygon = new Array();
var WPGM_Path = new Array();
var marker_array = new Array();
var marker_array2 = new Array();
var marker_sl_array = new Array();
var wpgmza_controls_active = new Array();
var wpgmza_adv_styling_json = new Array();
// TODO: Some of these should be changed, these are very generic variables names and they're on the global scope
var lazyload;
var autoplay;
var items;
var default_items;
var pagination;
var navigation;
var modern_iw_open = new Array();
var markerClusterer = new Array();
var original_iw;
var orig_fetching_directions;
var wpgmaps_map_mashup = new Array();
var focused_on_lat_lng = false;

/**
 * Variables used to focus the map on a specific LAT and LNG once the map has loaded.
 */
var focus_lat = false, focus_lng = false; 



var wpgmza_iw_Div = new Array();

var autocomplete = new Array();


var retina = window.devicePixelRatio > 1;


var click_from_list = false;

var wpgmza_user_marker = null; 
            
autoheight = true;
autoplay = 6000;
lazyload = true;
pagination = false;
navigation = true;
items = 6;

 if (typeof Array.prototype.forEach != 'function') {
    Array.prototype.forEach = function(callback){
      for (var i = 0; i < this.length; i++){
        callback.apply(this, [this[i], i, this]);
      }
    };
}

for (var entry in wpgmaps_localize) {
    modern_iw_open[entry] = false;
    if ('undefined' === typeof window.jQuery) {
        setTimeout(function(){ document.getElementById('wpgmza_map_'+wpgmaps_localize[entry]['id']).innerHTML = 'Error: In order for WP Google Maps to work, jQuery must be installed. A check was done and jQuery was not present. Please see the <a href="http://www.wpgmaps.com/documentation/troubleshooting/jquery-troubleshooting/" title="WP Google Maps - jQuery Troubleshooting">jQuery troubleshooting section of our site</a> for more information.'; }, 5000);
    }
    
    
}

/* find out if we are dealing with mashups and which maps they relate to */
if (typeof wpgmza_mashup_ids !== "undefined") {
    for (var mashup_entry in wpgmza_mashup_ids) {
        wpgmaps_map_mashup[mashup_entry] = true;
    }
}

var wpgmza_retina_width;
var wpgmza_retina_height;

if ("undefined" !== typeof wpgmaps_localize_global_settings['wpgmza_settings_retina_width']) { wpgmza_retina_width = parseInt(wpgmaps_localize_global_settings['wpgmza_settings_retina_width']); } else { wpgmza_retina_width = 31; }
if ("undefined" !== typeof wpgmaps_localize_global_settings['wpgmza_settings_retina_height']) { wpgmza_retina_height = parseInt(wpgmaps_localize_global_settings['wpgmza_settings_retina_height']); } else { wpgmza_retina_height = 45; }

function wpgmza_parse_theme_data(raw)
{
	var json;
	
	try{
		json = JSON.parse(raw);
	}catch(e) {
		try{
			json = eval(raw);
		}catch(e) {
			console.warn("Couldn't parse theme data");
			return [];
		}
	}
	
	return json;
}

var user_location;
var wpgmza_store_locator_circles_by_map_id = [];

function wpgmza_show_store_locator_radius(map_id, center, radius, distance_type, settings)
{
	var options = {
		strokeColor: '#FF0000',
		strokeOpacity: 0.25,
		strokeWeight: 2,
		fillColor: '#FF0000',
		fillOpacity: 0.15,
		map: MYMAP.map,
		center: center
	};
	
	for(var name in settings)
		options[name] = settings[name];
	
	switch(wpgmaps_localize[map_id].other_settings.wpgmza_store_locator_radius_style)
	{
		case "modern":
			if(!MYMAP.modernStoreLocatorCircle)
				MYMAP.modernStoreLocatorCircle = WPGMZA.ModernStoreLocatorCircle.createInstance(map_id);
			
			options.visible = true;
			options.radius = radius * (distance_type == 1 ? WPGMZA.KM_PER_MILE : 1);
			options.radiusString = radius;
			if(settings.strokeColor)
				options.color = settings.strokeColor;
			
			MYMAP.modernStoreLocatorCircle.setOptions(options);
			
			break;
		
		default:
			
			if(wpgmza_store_locator_circles_by_map_id[map_id])
				wpgmza_store_locator_circles_by_map_id[map_id].setMap(null);
			
			if (distance_type === "1")
				options.radius = parseInt(radius / 0.000621371);
			else
				options.radius = parseInt(radius / 0.001);

			var circle = new google.maps.Circle(options);
			wpgmza_store_locator_circles_by_map_id[map_id] = circle;
			
			break;
	}
}


function InitMap(map_id,cat_id,reinit) {
    modern_iw_open[map_id] = false /* set modern infowindow open boolean to false to reset the creation of it considering the map has been reinitialized */
    
    if ('undefined' !== typeof wpgmaps_localize_shortcode_data) {
        if (wpgmaps_localize_shortcode_data[map_id]['lat'] !== false && wpgmaps_localize_shortcode_data[map_id]['lng'] !== false) {
            wpgmaps_localize[map_id]['map_start_lat'] = wpgmaps_localize_shortcode_data[map_id]['lat'];
            wpgmaps_localize[map_id]['map_start_lng'] = wpgmaps_localize_shortcode_data[map_id]['lng'];

        }
    }
    
    
    if ('undefined' === cat_id || cat_id === '' || !cat_id || cat_id === 0 || cat_id === "0") { cat_id = 'all'; }

    
    var myLatLng = new window.google.maps.LatLng(wpgmaps_localize[map_id]['map_start_lat'],wpgmaps_localize[map_id]['map_start_lng']);
    google = window.google;
    if (reinit === false) {
        if (typeof wpgmza_override_zoom !== "undefined" && typeof wpgmza_override_zoom[map_id] !== "undefined") {
            MYMAP[map_id].init("#wpgmza_map_"+map_id, myLatLng, parseInt(wpgmza_override_zoom[map_id]), wpgmaps_localize[map_id]['type'],map_id);
        } else {
            MYMAP[map_id].init("#wpgmza_map_"+map_id, myLatLng, parseInt(wpgmaps_localize[map_id]['map_start_zoom']), wpgmaps_localize[map_id]['type'],map_id);
        }
    }
    

    UniqueCode=Math.round(Math.random()*10000);
    if ('undefined' !== typeof wpgmaps_localize_shortcode_data) {
        if (wpgmaps_localize_shortcode_data[map_id]['lat'] !== false && wpgmaps_localize_shortcode_data[map_id]['lng'] !== false) {
            /* we're using custom fields to create, only show the one marker */
            var point = new google.maps.LatLng(parseFloat(wpgmaps_localize_shortcode_data[map_id]['lat']),parseFloat(wpgmaps_localize_shortcode_data[map_id]['lng']));
            var marker = new google.maps.Marker({
                position: point,
                map: MYMAP[map_id].map
            });

        }
    } else {
        if (typeof wpgmaps_map_mashup !== "undefined" && typeof wpgmaps_map_mashup[map_id] !== "undefined" && wpgmaps_map_mashup[map_id] === true) {
            wpgmaps_localize_mashup_ids[map_id].forEach(function(entry_mashup) {
                if (typeof wpgmaps_localize[map_id]['other_settings']['store_locator_hide_before_search'] !== "undefined" && wpgmaps_localize[map_id]['other_settings']['store_locator_hide_before_search'] === 1) { 
                    /* dont show markers */
                    MYMAP[map_id].placeMarkers(wpgmaps_markerurl+entry_mashup+'markers.xml?u='+UniqueCode,map_id,cat_id,null,null,null,null,false);
                } else if (typeof wpgmaps_localize[map_id]['other_settings']['store_locator_hide_before_search'] !== "undefined" && wpgmaps_localize[map_id]['other_settings']['store_locator_hide_before_search'] === 2) { 
                    MYMAP[map_id].placeMarkers(wpgmaps_markerurl+entry_mashup+'markers.xml?u='+UniqueCode,map_id,cat_id,null,null,null,null,true);
                } else if (typeof wpgmaps_localize[map_id]['other_settings']['store_locator_hide_before_search'] === "undefined") { 
                    MYMAP[map_id].placeMarkers(wpgmaps_markerurl+entry_mashup+'markers.xml?u='+UniqueCode,map_id,cat_id,null,null,null,null,true);
                } else {
                    MYMAP[map_id].placeMarkers(wpgmaps_markerurl+entry_mashup+'markers.xml?u='+UniqueCode,map_id,cat_id,null,null,null,null,true);
                }
                
            });
        } else {
            if (typeof wpgmaps_localize[map_id]['other_settings']['store_locator_hide_before_search'] !== "undefined" && wpgmaps_localize[map_id]['other_settings']['store_locator_hide_before_search'] === 1) { 
                /* dont show markers */
                MYMAP[map_id].placeMarkers(wpgmaps_markerurl+map_id+'markers.xml?u='+UniqueCode,map_id,cat_id,null,null,null,null,false);
            } else if (typeof wpgmaps_localize[map_id]['other_settings']['store_locator_hide_before_search'] !== "undefined" && wpgmaps_localize[map_id]['other_settings']['store_locator_hide_before_search'] === 2) { 
                MYMAP[map_id].placeMarkers(wpgmaps_markerurl+map_id+'markers.xml?u='+UniqueCode,map_id,cat_id,null,null,null,null,true);
            } else if (typeof wpgmaps_localize[map_id]['other_settings']['store_locator_hide_before_search'] === "undefined") { 
                MYMAP[map_id].placeMarkers(wpgmaps_markerurl+map_id+'markers.xml?u='+UniqueCode,map_id,cat_id,null,null,null,null,true);
            } else {
                MYMAP[map_id].placeMarkers(wpgmaps_markerurl+map_id+'markers.xml?u='+UniqueCode,map_id,cat_id,null,null,null,null,true);
            }
            
        }
    }
};

function resetLocations(map_id) {
  if (typeof jQuery("#addressInput_"+map_id) === "object") { jQuery("#addressInput_"+map_id).val(''); }
  if (typeof jQuery("#nameInput_"+map_id) === "object") { jQuery("#nameInput_"+map_id).val(''); }
  reset_marker_lists(map_id);
  InitMap(map_id,'all',true);
  MYMAP[map_id].map.setZoom(parseInt(wpgmaps_localize[map_id]['map_start_zoom']));

}

function fillInAddress(mid) {
  
  var place = autocomplete[mid].getPlace();
}



for (var entry in wpgmaps_localize) {
    
    var curmid = wpgmaps_localize[entry]['id'];
    
    var elementExists = document.getElementById('addressInput_'+curmid);

    var wpgmza_input_to_exists = document.getElementById('wpgmza_input_to_'+curmid);
    var wpgmza_input_from_exists = document.getElementById('wpgmza_input_from_'+curmid);

    if (typeof google === 'object' && typeof google.maps === 'object' && typeof google.maps.places === 'object' && typeof google.maps.places.Autocomplete === 'function') {

        if (elementExists !== null) {
            if (typeof wpgmaps_localize[curmid]['other_settings']['wpgmza_store_locator_restrict'] !== "undefined" && wpgmaps_localize[curmid]['other_settings']['wpgmza_store_locator_restrict'] != "") {
                autocomplete[curmid] = new google.maps.places.Autocomplete(
                (document.getElementById('addressInput_'+curmid)),
                {types: ['geocode'], componentRestrictions: {country: wpgmaps_localize[curmid]['other_settings']['wpgmza_store_locator_restrict']} });
                google.maps.event.addListener(autocomplete[curmid], 'place_changed', function() {
                    fillInAddress(curmid);
                });
            } else {
                autocomplete[curmid] = new google.maps.places.Autocomplete(
                (document.getElementById('addressInput_'+curmid)),
                {types: ['geocode']});
                google.maps.event.addListener(autocomplete[curmid], 'place_changed', function() {
                    fillInAddress(curmid);
                });
            }
        }

        if (wpgmza_input_to_exists !== null) {
            autocomplete[curmid] = new google.maps.places.Autocomplete(
            (document.getElementById('wpgmza_input_to_'+curmid)),
            {types: ['geocode']});
            google.maps.event.addListener(autocomplete[curmid], 'place_changed', function() {
                fillInAddress(curmid);
            });
        }

        if (wpgmza_input_from_exists !== null) {
            autocomplete[curmid] = new google.maps.places.Autocomplete(
            (document.getElementById('wpgmza_input_from_'+curmid)),
            {types: ['geocode']});
            google.maps.event.addListener(autocomplete[curmid], 'place_changed', function() {
                fillInAddress(curmid);
            });
        }
        if (document.getElementById('wpgmza_ugm_add_address_'+curmid) !== null) {

            /* initialize the autocomplete form */
              autocomplete[curmid] = new google.maps.places.Autocomplete(
                  /** @type {HTMLInputElement} */(document.getElementById('wpgmza_ugm_add_address_'+curmid)),
                  { types: ['geocode'] });
              /* When the user selects an address from the dropdown,
               populate the address fields in the form. */
              google.maps.event.addListener(autocomplete[curmid], 'place_changed', function() {
                fillInAddress(curmid);
              });
          }

        
        
    }
}

  

function searchLocations(map_id) {
    if (document.getElementById("addressInput_"+map_id) === null) { var address = null; } else { var address = document.getElementById("addressInput_"+map_id).value; }
    if (document.getElementById("nameInput_"+map_id) === null) { var search_title = null; } else { var search_title = document.getElementById("nameInput_"+map_id).value; }
    
    

    checkedCatValues = 'all';
    if (jQuery(".wpgmza_cat_checkbox_"+map_id).length > 0) { 
        var checkedCatValues = jQuery('.wpgmza_checkbox:checked').map(function() { return this.value; }).get();
        if (checkedCatValues === "" || checkedCatValues.length < 1 || checkedCatValues === 0 || checkedCatValues === "0") { checkedCatValues = 'all'; }
    }  
    if (jQuery(".wpgmza_filter_select_"+map_id).length > 0) { 
        var checkedCatValues = jQuery(".wpgmza_filter_select_"+map_id).find(":selected").val();
        if (checkedCatValues === "" || checkedCatValues.length < 1 || checkedCatValues === 0 || checkedCatValues === "0") { checkedCatValues = 'all'; }
    }


    if (address === null || address === "") {
		document.getElementById("addressInput_"+map_id).focus();
		return;
		
         //var map_center = MYMAP[map_id].map.getCenter();
        //searchLocationsNear(map_id,checkedCatValues,map_center,search_title);
    } else {

        checker = address.split(",");
        var wpgm_lat = "";
        var wpgm_lng = "";
        wpgm_lat = checker[0];
        wpgm_lng = checker[1];
        checker1 = parseFloat(checker[0]);
        checker2 = parseFloat(checker[1]);

        var geocoder = new google.maps.Geocoder();

        if (typeof wpgmaps_localize[map_id]['other_settings']['wpgmza_store_locator_restrict'] !== "undefined" && wpgmaps_localize[map_id]['other_settings']['wpgmza_store_locator_restrict'] != "") {
            if ((typeof wpgm_lng !== "undefined" && wpgm_lat.match(/[a-zA-Z]/g) === null && wpgm_lng.match(/[a-zA-Z]/g) === null) && checker.length === 2 && (checker1 != NaN && (checker1 <= 90 || checker1 >= -90)) && (checker2 != NaN && (checker2 <= 90 || checker2 >= -90))) {
            
                var point = new google.maps.LatLng(parseFloat(wpgm_lat),parseFloat(wpgm_lng));
                searchLocationsNear(map_id,checkedCatValues,point,search_title);
            }
            else {
                /* is an address, must geocode */
                geocoder.geocode({address: address,componentRestrictions: {country: wpgmaps_localize[map_id]['other_settings']['wpgmza_store_locator_restrict']}}, function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        searchLocationsNear(map_id,checkedCatValues,results[0].geometry.location,search_title);
                    } else {
                        alert(address + ' not found');
                    }
                });

            }
        } else {

            if ((typeof wpgm_lng !== "undefined" && wpgm_lat.match(/[a-zA-Z]/g) === null && wpgm_lng.match(/[a-zA-Z]/g) === null) && checker.length === 2 && (checker1 != NaN && (checker1 <= 90 || checker1 >= -90)) && (checker2 != NaN && (checker2 <= 90 || checker2 >= -90))) {
                var point = new google.maps.LatLng(parseFloat(wpgm_lat),parseFloat(wpgm_lng));
                searchLocationsNear(map_id,checkedCatValues,point,search_title);
            }
            else {
                /* is an address, must geocode */
            geocoder.geocode({address: address}, function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        searchLocationsNear(map_id,checkedCatValues,results[0].geometry.location,search_title);
                    } else {
                        alert(address + ' not found');
                    }
                });

            }

        } 
                

          
    }
  }


function clearLocations() {
    infoWindow.forEach(function(entry,index) {
        infoWindow[index].close();
    });
}

function wpgmza_get_zoom_from_radius(radius, units)
{
	// With thanks to Jeff Jason http://jeffjason.com/2011/12/google-maps-radius-to-zoom/
	
	if(units == WPGMZA.UNITS_MILES)
		radius *= WPGMZA.KM_PER_MILE;
	
	return Math.round(14-Math.log(radius)/Math.LN2);
}


function searchLocationsNear(mapid,category,center_searched,search_title) {
    clearLocations();
    var distance_type = document.getElementById("wpgmza_distance_type_"+mapid).value;
    var radius = document.getElementById('radiusSelect_'+mapid).value;
    var zoomie = wpgmza_get_zoom_from_radius(radius);
	
    if (parseInt(category) === 0) { category = 'all'; }
    if (category === "0") { category = 'all'; }
    if (category === "Not found") { category = 'all'; }
    if (category === null) { category = 'all'; }
    if (category.length < 1) { category = 'all'; }

    MYMAP[mapid].map.setCenter(center_searched);
    MYMAP[mapid].map.setZoom(zoomie);
    


    
    
    if (typeof wpgmaps_map_mashup[mapid] !== "undefined" && wpgmaps_map_mashup[mapid] === true) {
        wpgmaps_localize_mashup_ids[mapid].forEach(function(entry_mashup) {

            MYMAP[mapid].placeMarkers(wpgmaps_markerurl+entry_mashup+'markers.xml?u='+UniqueCode,mapid,category,radius,center_searched,distance_type,search_title,true);
        });
    } else {
        MYMAP[mapid].placeMarkers(wpgmaps_markerurl+mapid+'markers.xml?u='+UniqueCode,mapid,category,radius,center_searched,distance_type,search_title,true);
    }
    if (jQuery("#wpgmza_marker_holder_"+mapid).length > 0) {
        /* ensure that the marker list is showing (this is if the admin has chosen to hide the markers until a store locator search is done */
        jQuery("#wpgmza_marker_holder_"+mapid).show();
    }
    if( jQuery('#wpgmza_marker_list_container_'+wpgmaps_localize[entry]['id']).length > 0 ){
        jQuery('#wpgmza_marker_list_container_'+wpgmaps_localize[entry]['id']).show();                         
    }
    
}

function toRad(Value) {
    /** Converts numeric degrees to radians */
    return Value * Math.PI / 180;
}   

function wpgmza_getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}
var wpgmza_open_marker = wpgmza_getUrlVars()["markerid"];
var wpgmza_open_marker_zoom = wpgmza_getUrlVars()["mzoom"];


            
function wpgmza_reinitialisetbl(map_id) {
    jQuery('#wpgmza_marker_holder_'+map_id).show();
    if (wpgmaps_localize[map_id]['order_markers_by'] === "1") { wpgmaps_order_by = parseInt(0); } 
    else if (wpgmaps_localize[map_id]['order_markers_by'] === "2") { wpgmaps_order_by = parseInt(2); } 
    else if (wpgmaps_localize[map_id]['order_markers_by'] === "3") { wpgmaps_order_by = parseInt(4); } 
    else if (wpgmaps_localize[map_id]['order_markers_by'] === "4") { wpgmaps_order_by = parseInt(5); } 
    else if (wpgmaps_localize[map_id]['order_markers_by'] === "5") { wpgmaps_order_by = parseInt(3); } 
    else { wpgmaps_order_by = 0; }
    if (wpgmaps_localize[map_id]['order_markers_choice'] === "1") { wpgmaps_order_by_choice = "asc"; } 
    else { wpgmaps_order_by_choice = "desc"; }
    wpgmzaTable[map_id].fnClearTable( 0 );
	
    wpgmzaTable[map_id] = jQuery('#wpgmza_table_'+map_id).DataTable({
        "bProcessing": true,"aaSorting" : [],
        responsive: true,
        "iDisplayLength": wpgmza_settings_default_items,
        "oLanguage": {
                "sLengthMenu": wpgm_dt_sLengthMenu,
                "sZeroRecords": wpgm_dt_sZeroRecords,
                "sInfo": wpgm_dt_sInfo,
                "sInfoEmpty": wpgm_dt_sInfoEmpty,
                "sInfoFiltered": wpgm_dt_sInfoFiltered,
                "sSearch": wpgm_dt_sSearch,
                "oPaginate" : {
                    "sFirst": wpgm_dt_sFirst,
                    "sLast": wpgm_dt_sLast,
                    "sNext": wpgm_dt_sNext,
                    "sPrevious": wpgm_dt_sPrevious,
                   "sSearch": wpgm_dt_sSearch
                }
        }

    });


}

// TODO: This is inefficient, we should use DataTables AJAX
// TODO: Save HTML for when custom filters change
function wpgmza_update_data_table(plain_table_html, map_id) {
	var container = jQuery("#wpgmza_marker_holder_" + map_id);
	
	container.html(plain_table_html);
	
	WPGMZA.CustomFieldFilterController.controllersByMapID[map_id].applyToAdvancedTable();
	
	if(wpgmzaTable[map_id])
		wpgmzaTable[map_id].destroy();
	
	wpgmzaTable[map_id] = jQuery('#wpgmza_table_'+map_id).DataTable({
		"bDestroy":true,
		responsive: true,
		"iDisplayLength": wpgmza_settings_default_items,
		"bProcessing": true,
		"aaSorting" : [],
		"oLanguage": {
			"sLengthMenu": wpgm_dt_sLengthMenu,
			"sZeroRecords": wpgm_dt_sZeroRecords,
			"sInfo": wpgm_dt_sInfo,
			"sInfoEmpty": wpgm_dt_sInfoEmpty,
			"sInfoFiltered": wpgm_dt_sInfoFiltered,
			"sSearch": wpgm_dt_sSearch,
			"oPaginate" : {
				"sFirst": wpgm_dt_sFirst,
				"sLast": wpgm_dt_sLast,
				"sNext": wpgm_dt_sNext,
				"sPrevious": wpgm_dt_sPrevious,
			   "sSearch": wpgm_dt_sSearch
			}
		}
	});
	
	if(MYMAP[map_id].markerListing instanceof WPGMZA.ModernMarkerListing)
	{
		var m = plain_table_html.match(/wpgmza_marker_\d+/g);
		var visible_marker_ids = [];
		for(var i = 0; i < m.length; i++)
			visible_marker_ids.push(m[i].match(/\d+/)[0]);
		MYMAP[map_id].markerListing.setVisibleListItems(visible_marker_ids);
	}
}

 function wpgmza_filter_marker_lists_by_array(map_id,marker_sl_array) {
    /* update datatables (only if using datatables) */
    if (typeof jQuery("#wpgmza_table_"+map_id) === "object") { 
        var data = {
			action: 'wpgmza_datatables_sl',
			security: wpgmaps_pro_nonce,
			map_id: map_id,
			marker_array: marker_sl_array
        };
        jQuery.post(ajaxurl, data, function(response) {
			wpgmza_update_data_table(response, map_id);
        });
    }
    if (typeof jQuery("#wpgmza_marker_list_container_"+map_id) === "object" && jQuery("#wpgmza_marker_list_container_"+map_id).length > 0) {
        if (jQuery("#wpgmza_marker_list_container_"+map_id).hasClass('wpgmza_marker_carousel')) {
            /* carousel listing */
            var data = {
                    action: 'wpgmza_sl_carousel',
                    security: wpgmaps_pro_nonce,
                    map_id: map_id,
                    marker_array: marker_sl_array
            };
            jQuery.post(ajaxurl, data, function(response) {
                    items = default_items;
                    jQuery("#wpgmza_marker_list_container_"+map_id+"").html(response);
                    if (marker_sl_array.length < items) { items = marker_sl_array.length; } else { items = default_items; }
                    if (items < 1) { items = 1; }

                    jQuery("#wpgmza_marker_list_"+map_id+"").owlCarousel({
                        autoplay: true,
                        autoplayTimeout: autoplay,
                        lazyLoad : lazyload,
                        autoHeight : autoheight,
                        dots : pagination,
                        nav : navigation,
                        items : items,
                        loop: true
                    });

            });
        }
    }
    else if (jQuery("#wpgmza_marker_list_"+map_id).length) {
		var data = {
			action: null,
			security: wpgmaps_pro_nonce,
			map_id: map_id,
			marker_array: marker_sl_array
		};
		
		function callback(response) {
			jQuery("#wpgmza_marker_list_"+map_id+"").html(response);
		}
		
        if (jQuery("#wpgmza_marker_list_"+map_id).hasClass('wpgmza_basic_list')) { 
            // We're using the basic list marker listing
			data.action = 'wpgmza_sl_basiclist';
            
            jQuery.post(ajaxurl, data, function(response) {
				items = default_items;
				callback(response);
            });
        }else{
			// We're not using the basic marker listing
            data.action = 'wpgmza_sl_basictable';
			
            jQuery.post(ajaxurl, data, function(response) {
				callback(response);
            });
		}
    }
}


function wpgmza_filter_marker_lists(wpgmza_map_id,selectedValue) {

    /* mashup support */
    if (typeof wpgmaps_localize_mashup_ids !== "undefined" && wpgmaps_localize_mashup_ids !== null) {
        if (typeof wpgmaps_localize_mashup_ids[wpgmza_map_id] !== "undefined") {
            list_mashup_ids = wpgmaps_localize_mashup_ids[wpgmza_map_id];
        } else {
            list_mashup_ids = false;
        }
    } else {
        list_mashup_ids = false;
    }



    if (typeof jQuery("#wpgmza_table_"+wpgmza_map_id) === "object") { 
        if (selectedValue === 0 || selectedValue === "All" || selectedValue === "0") {
            
            /* update datatables */
            var data = {
                    action: 'wpgmza_datatables',
                    security: wpgmaps_pro_nonce,
                    map_id: wpgmza_map_id,
                    category_data: 'all'
            };
            jQuery.post(ajaxurl, data, function(response) {
				wpgmza_update_data_table(response, wpgmza_map_id);
            });
        } else { 
            
            /* update datatables */
            var data = {
                    action: 'wpgmza_datatables',
                    security: wpgmaps_pro_nonce,
                    map_id: wpgmza_map_id,
                    category_data: selectedValue
            };
            jQuery.post(ajaxurl, data, function(response) {
                if (typeof wpgmzaTable[wpgmza_map_id] !== "undefined") {
                    wpgmzaTable[wpgmza_map_id].destroy();
                    
					wpgmza_update_data_table(response, wpgmza_map_id);
                }else if(MYMAP[wpgmza_map_id].markerListing instanceof WPGMZA.ModernMarkerListing){
					
					var temp = $(response);
					var visible_marker_ids = [];
					$("[mid]").each(function(index, el) {
						visible_marker_ids.push($(el).attr("mid"));
					});
					MYMAP[wpgmza_map_id].markerListing.setVisibleListItems(visible_marker_ids);
					
				}

            });
            
        }
		
		

    } 
    if (jQuery("#wpgmza_marker_list_"+wpgmza_map_id).length > 0) {
       
        /* check whether we are using carousel or normal marker listing */

        if (jQuery("#wpgmza_marker_list_"+wpgmza_map_id).hasClass('wpgmza_marker_carousel')) {

            if (selectedValue === 0 || selectedValue === "All" || selectedValue === "0") {
                
                var data = {
                        action: 'wpgmza_carousel_update',
                        security: wpgmaps_pro_nonce,
                        mashup_maps: list_mashup_ids,
                        map_id: wpgmza_map_id,
                        category_data: 'all'
                };
            } else {
                
                var data = {
                        action: 'wpgmza_carousel_update',
                        security: wpgmaps_pro_nonce,
                        mashup_maps: list_mashup_ids,
                        map_id: wpgmza_map_id,
                        category_data: selectedValue
                };
            }
             /* carousel listing */
            jQuery.post(ajaxurl, data, function(response) {
                    jQuery("#wpgmza_marker_list_container_"+wpgmza_map_id+"").html(response);
                    jQuery("#wpgmza_marker_list_"+wpgmza_map_id+"").owlCarousel({
                        autoplay: true,
                        autoplayTimeout: autoplay,
                        lazyLoad : lazyload,
                        autoHeight : autoheight,
                        dots : pagination,
                        nav : navigation,
                        items : items,
                        loop: true
                    });

            });
        } else if (jQuery("#wpgmza_marker_list_"+wpgmza_map_id).hasClass('wpgmza_basic_list')) { 

            /* we're using the basic list marker listing */
            if (selectedValue === 0 || selectedValue === "All" || selectedValue === "0") {
                var data = {
                        action: 'wpgmza_basiclist_update',
                        security: wpgmaps_pro_nonce,
                        map_id: wpgmza_map_id,
                        mashup_maps: list_mashup_ids,
                        category_data: 'all'
                };
            } else {
                var data = {
                        action: 'wpgmza_basiclist_update',
                        security: wpgmaps_pro_nonce,
                        map_id: wpgmza_map_id,
                        mashup_maps: list_mashup_ids,
                        category_data: selectedValue
                };
            }
             /* basic marker listing listing */
            jQuery.post(ajaxurl, data, function(response) {
                    jQuery("#wpgmza_marker_list_"+wpgmza_map_id+"").html(response);
                    

            });    

        } else { 
            /* we must be using the basic table listing */
            if (selectedValue === 0 || selectedValue === "All" || selectedValue === "0") {
                var data = {
                        action: 'wpgmza_basictable_update',
                        security: wpgmaps_pro_nonce,
                        mashup_maps: list_mashup_ids,
                        map_id: wpgmza_map_id,
                        category_data: 'all'
                };
            } else {
                var data = {
                        action: 'wpgmza_basictable_update',
                        security: wpgmaps_pro_nonce,
                        mashup_maps: list_mashup_ids,
                        map_id: wpgmza_map_id,
                        category_data: selectedValue
                };
            }
             /* basic marker listing listing */
            jQuery.post(ajaxurl, data, function(response) {
                    jQuery("#wpgmza_marker_list_"+wpgmza_map_id+"").html(response);
                    

            });                        
        }


       
    }


}




function reset_marker_lists(wpgmza_map_id) {

    if (typeof jQuery("#wpgmza_table_"+wpgmza_map_id) === "object" && jQuery("#wpgmza_table_"+wpgmza_map_id).length > 0) {
            /* update datatables */
            var data = {
                    action: 'wpgmza_datatables',
                    security: wpgmaps_pro_nonce,
                    map_id: wpgmza_map_id,
                    category_data: 'all'
            };
            jQuery.post(ajaxurl, data, function(response) {
				
                    jQuery("#wpgmza_table_"+wpgmza_map_id+"").html(response);
                    wpgmzaTable[wpgmza_map_id] = jQuery('#wpgmza_table_'+wpgmza_map_id).DataTable({
                        "bDestroy":true,
                        responsive: true,
                        "iDisplayLength": wpgmza_settings_default_items,
                        "bProcessing": true,"aaSorting" : [],
                        "oLanguage": {
                                "sLengthMenu": wpgm_dt_sLengthMenu,
                                "sZeroRecords": wpgm_dt_sZeroRecords,
                                "sInfo": wpgm_dt_sInfo,
                                "sInfoEmpty": wpgm_dt_sInfoEmpty,
                                "sInfoFiltered": wpgm_dt_sInfoFiltered,
                                "sSearch": wpgm_dt_sSearch,
                                "oPaginate" : {
                                    "sFirst": wpgm_dt_sFirst,
                                    "sLast": wpgm_dt_sLast,
                                    "sNext": wpgm_dt_sNext,
                                    "sPrevious": wpgm_dt_sPrevious,
                                   "sSearch": wpgm_dt_sSearch
                                }
                        }

                    });

            });

    } 

    if (jQuery("#wpgmza_marker_list_"+wpgmza_map_id).length > 0) {
       
        /* check whether we are using carousel or normal marker listing */

        if (jQuery("#wpgmza_marker_list_"+wpgmza_map_id).hasClass('wpgmza_marker_carousel')) {

            var data = {
                    action: 'wpgmza_carousel_update',
                    security: wpgmaps_pro_nonce,
                    map_id: wpgmza_map_id,
                    category_data: 'all'
            };
        
             /* carousel listing */
            jQuery.post(ajaxurl, data, function(response) {
                    jQuery("#wpgmza_marker_list_container_"+wpgmza_map_id+"").html(response);
                    jQuery("#wpgmza_marker_list_"+wpgmza_map_id+"").owlCarousel({
                        autoplay: true,
                        autoplayTimeout: autoplay,
                        lazyLoad : lazyload,
                        autoHeight : autoheight,
                        dots : pagination,
                        nav : navigation,
                        items : default_items,
                        loop: true
                    });

            });
        } else if (jQuery("#wpgmza_marker_list_"+wpgmza_map_id).hasClass('wpgmza_basic_list')) { 
            /* we're using the basic list marker listing */
            
            
            
            var data = {
                    action: 'wpgmza_basiclist_update',
                    security: wpgmaps_pro_nonce,
                    map_id: wpgmza_map_id,
                    category_data: 'all'
            };
        
             /* basic marker listing listing */
            jQuery.post(ajaxurl, data, function(response) {
                    jQuery("#wpgmza_marker_list_"+wpgmza_map_id+"").html(response);
                    

            });    

        } else { 
            /* we must be using the basic table listing */
            
            var data = {
                    action: 'wpgmza_basictable_update',
                    security: wpgmaps_pro_nonce,
                    map_id: wpgmza_map_id,
                    category_data: 'all'
            };
             /* basic marker listing listing */
            jQuery.post(ajaxurl, data, function(response) {
                    jQuery("#wpgmza_marker_list_"+wpgmza_map_id+"").html(response);
                    

            });                        
        }


       
    }


} 

jQuery(function() {

    jQuery(window).on("load", function(){
        jQuery(".wpgmaps_auto_get_directions").each(function() {
            var this_bliksem = jQuery(this);
            var this_bliksem_id = jQuery(this).attr('id');
            jQuery("#wpgmaps_directions_edit_"+this_bliksem_id).show( function() {
                jQuery(this_bliksem).click();
            });

        });
    });

    jQuery(document).ready(function(){
        if (typeof wpgmaps_localize_marker_data !== "undefined") { document.marker_data_array = wpgmaps_localize_marker_data; }

        for (var entry in wpgmaps_localize) {
            if (jQuery("#wpgmaps_directions_notification_"+entry).length > 0) { 
                orig_fetching_directions = jQuery("#wpgmaps_directions_notification_"+entry).html();
            }


           if ("undefined" !== typeof wpgmaps_localize[entry]['other_settings'] && "undefined" !== typeof wpgmaps_localize[entry]['other_settings']['list_markers_by'] && wpgmaps_localize[entry]['other_settings']['list_markers_by'] === "3") {
                if ("undefined" !== typeof wpgmaps_localize_global_settings['carousel_lazyload'] && wpgmaps_localize_global_settings['carousel_lazyload'] === "yes") { lazyload = true; } else { lazyload = false; }
                if ("undefined" === typeof wpgmaps_localize_global_settings['carousel_lazyload']) { lazyload = true; }

                if ("undefined" !== typeof wpgmaps_localize_global_settings['carousel_autoplay']) { autoplay = parseInt(wpgmaps_localize_global_settings['carousel_autoplay']); } else { autoplay = false; }
                if ("undefined" === typeof wpgmaps_localize_global_settings['carousel_autoplay']) { autoplay = 6000; }

                if ("undefined" !== typeof wpgmaps_localize_global_settings['carousel_autoheight'] && wpgmaps_localize_global_settings['carousel_autoheight'] === "yes") { autoheight = true; } else { autoheight = false; }
                if ("undefined" === typeof wpgmaps_localize_global_settings['carousel_autoheight']) { autoheight = true; }

                if ("undefined" !== typeof wpgmaps_localize_global_settings['carousel_pagination'] && wpgmaps_localize_global_settings['carousel_pagination'] === "yes") { pagination = true; } else { pagination = false; }
                if ("undefined" === typeof wpgmaps_localize_global_settings['carousel_pagination']) { pagination = false; }

                if ("undefined" !== typeof wpgmaps_localize_global_settings['carousel_navigation'] && wpgmaps_localize_global_settings['carousel_navigation'] === "yes") { navigation = true; } else { navigation = false; }
                if ("undefined" === typeof wpgmaps_localize_global_settings['carousel_navigation']) { navigation = true; }

                if ("undefined" !== typeof wpgmaps_localize_global_settings['carousel_items']) { items = parseInt(wpgmaps_localize_global_settings['carousel_items']); } else { items = 5; }
                if ("undefined" === typeof wpgmaps_localize_global_settings['carousel_items']) { items = 6; }
                default_items = items;

                if (wpgmaps_localize[entry]['total_markers'] < items) { items = wpgmaps_localize[entry]['total_markers']; }
                jQuery("#wpgmza_marker_list_"+wpgmaps_localize[entry]['id']).owlCarousel({
                    autoplay: true,
                    autoplayTimeout: autoplay,
                    lazyLoad : lazyload,
                    autoHeight : autoheight,
                    dots : pagination,
                    nav : navigation,
                    items : items,
                    loop: true
                });
                    
            } 
        }
        
        if (/1\.(0|1|2|3|4|5|6|7)\.(0|1|2|3|4|5|6|7|8|9)/.test(jQuery.fn.jquery)) {
            for(var entry in wpgmaps_localize) {
                document.getElementById('wpgmza_map_'+wpgmaps_localize[entry]['id']).innerHTML = 'Error: Your version of jQuery is outdated. WP Google Maps requires jQuery version 1.7+ to function correctly. Go to Maps->Settings and check the box that allows you to over-ride your current jQuery to try eliminate this problem.';
            }
        } else {


            jQuery("body").on("click", ".wpgmaps_mlist_row", function() {
                var wpgmza_markerid = jQuery(this).attr("mid");
                var wpgmza_mapid = jQuery(this).attr("mapid");
                openInfoWindow(wpgmza_markerid,wpgmza_mapid,true);

	            if (jQuery(this).parents(".wpgmza-modern-marker-listing").length < 1) {
		            jQuery('html, body').animate({
			            scrollTop: jQuery("#wpgmza_map_" + wpgmza_mapid).offset().top
		            }, 500);
	            }
            });
            jQuery("body").on("click", ".wpgmaps_blist_row", function() {
                var wpgmza_markerid = jQuery(this).attr("mid");
                var wpgmza_mapid = jQuery(this).attr("mapid");
                openInfoWindow(wpgmza_markerid,wpgmza_mapid,true);
                
            });
            jQuery("body").on("change", "#wpgmza_filter_select", function() {
                
                /* do nothing if user has enabled store locator */
                var wpgmza_map_id = jQuery(this).attr("mid");
                if (jQuery("#addressInput_"+wpgmza_map_id).length > 0) { } else {

                    var selectedValue = jQuery(this).find(":selected").val();
                    var wpgmza_map_id = jQuery(this).attr("mid");
                    InitMap(wpgmza_map_id,selectedValue);
                    wpgmza_filter_marker_lists(wpgmza_map_id,selectedValue);
                }
                

            });      
            jQuery("body").on("click", ".wpgmza_checkbox", function() {
                checkedCatValues = new Array();
                /* do nothing if user has enabled store locator */
                var wpgmza_map_id = jQuery(this).attr("mid");

                var original_click_cat = jQuery(this).attr("value");

                if (jQuery("#addressInput_"+wpgmza_map_id).length > 0) { } else {
                     var checkedCatValues = jQuery('.wpgmza_checkbox:checked').map(function() {
                        return this.value;
                    }).get();


                    /**
                     * find children categories
                     */

                    for(var tmp_cat_entry in checkedCatValues) {
                        var tmp_checker = true;
                        current_tmp_cat = parseInt(checkedCatValues[tmp_cat_entry]);


                        var counter = 0;

                        var cat_array_check_order = new Array();
                        /* set first category as 'to be checked' */
                        cat_array_check_order[current_tmp_cat] = 0;


                       

                        while (tmp_checker === true) {
                            
                            /* safety counter */
                            counter++;
                            if (counter > 1000) { break; }


                            for (current_category_to_check in cat_array_check_order) {

                                if (cat_array_check_order[current_category_to_check] === 0) {
                              
                                    if (typeof wpgmaps_localize_categories[wpgmza_map_id] !== "undefined") {
                                        
                                        var children_found = 0;
                                        for (tmp_childd in wpgmaps_localize_categories[wpgmza_map_id]) {
                                            tmp_parent = wpgmaps_localize_categories[wpgmza_map_id][tmp_childd];
                                            if (parseInt(tmp_parent) === parseInt(current_category_to_check)) {
                                                /* found a child */


                                                /* should we parse this along to the ajax request? check global settings first */
                                                if (typeof wpgmaps_localize_global_settings['wpgmza_settings_cat_logic'] === "undefined" || parseInt(wpgmaps_localize_global_settings['wpgmza_settings_cat_logic']) === 0) {
                                                    if (typeof cat_array_check_order[tmp_childd] === "undefined") {
                                                        cat_array_check_order[tmp_childd] = 0;
                                                        /* add it to the array so that we can call it from the db */
                                                        
                                                        if (jQuery.inArray(tmp_childd, checkedCatValues) === -1) { 
                                                            checkedCatValues.push(tmp_childd);
                                                        }

                                                    }
                                                    children_found++;
                                                }
                                            }
                                        }
                                    }
                                }
                                /* mark this category as 'checked' */
                                cat_array_check_order[current_category_to_check] = 1;
                            }
                            
                            /**
                             * Identify if all categories that needed to be checked were checked? i.e. they were all set to 1 
                             */
                            var tmp_continue = false;
                            for (tmp_checker2 in cat_array_check_order) {
                                if (cat_array_check_order[tmp_checker2] === 0) {
                                    tmp_continue = true;
                                }
                            }
                            if (!tmp_continue) {
                                /* we've checked everything, we can stop */
                                tmp_checker = false;
                            }

                        }



                    }

                    

                    if (checkedCatValues[0] === "0" || typeof checkedCatValues === 'undefined' || checkedCatValues.length < 1) {
                        InitMap(wpgmza_map_id,'all');
                        wpgmza_filter_marker_lists(wpgmza_map_id,'all');
                    } else {
                        InitMap(wpgmza_map_id,checkedCatValues);
                        wpgmza_filter_marker_lists(wpgmza_map_id,checkedCatValues);
                    }

                }
            });                
            
        

            jQuery("body").on("click", ".sl_use_loc", function() {
                var wpgmza_map_id = jQuery(this).attr("mid");
                jQuery('#addressInput_'+wpgmza_map_id).val(wpgmaps_lang_getting_location);

                var geocoder = new google.maps.Geocoder();
                geocoder.geocode({'latLng': user_location}, function(results, status) {
                  if (status === google.maps.GeocoderStatus.OK) {
                    if (results[0]) {
                      jQuery('#addressInput_'+wpgmza_map_id).val(results[0].formatted_address);
                    }
                  }
                });
            });       
            jQuery("body").on("click", "#wpgmza_use_my_location_from", function() {
                var wpgmza_map_id = jQuery(this).attr("mid");
                jQuery('#wpgmza_input_from_'+wpgmza_map_id).val(wpgmaps_lang_getting_location);

                var geocoder = new google.maps.Geocoder();
                geocoder.geocode({'latLng': user_location}, function(results, status) {
                  if (status === google.maps.GeocoderStatus.OK) {
                    if (results[0]) {
                      jQuery('#wpgmza_input_from_'+wpgmza_map_id).val(results[0].formatted_address);
                    }
                  }
                });
            });              
            jQuery("body").on("click", "#wpgmza_use_my_location_to", function() {
                var wpgmza_map_id = jQuery(this).attr("mid");
                jQuery('#wpgmza_input_to_'+wpgmza_map_id).val(wpgmaps_lang_getting_location);
                var geocoder = new google.maps.Geocoder();
                geocoder.geocode({'latLng': user_location}, function(results, status) {
                  if (status === google.maps.GeocoderStatus.OK) {
                    if (results[0]) {
                      jQuery('#wpgmza_input_to_'+wpgmza_map_id).val(results[0].formatted_address);
                    }
                  }
                });
            });
            


            jQuery('body').on('tabsactivate', function(event, ui) {
                for(var entry in wpgmaps_localize) {
                    InitMap(wpgmaps_localize[entry]['id'],'all',false);
                }
            });
            jQuery('body').on('tabsshow', function(event, ui) {
                for(var entry in wpgmaps_localize) {
                    InitMap(wpgmaps_localize[entry]['id'],'all',false);
                }
            });
            jQuery('body').on('accordionactivate', function(event, ui) {
                for(var entry in wpgmaps_localize) {
                    InitMap(wpgmaps_localize[entry]['id'],'all',false);
                }
            });
            
            /* tab compatibility */
            jQuery('body').on('click', '.wpb_tabs_nav li', function(event, ui) { for(var entry in wpgmaps_localize) { InitMap(wpgmaps_localize[entry]['id'],'all',false); } }); 
            jQuery('body').on('click', '.ui-tabs-nav li', function(event, ui) { for(var entry in wpgmaps_localize) { InitMap(wpgmaps_localize[entry]['id'],'all',false); } });
            jQuery('body').on('click', '.tp-tabs li a', function(event, ui) { for(var entry in wpgmaps_localize) { InitMap(wpgmaps_localize[entry]['id'],'all',false); } });
            jQuery('body').on('click', '.nav-tabs li a', function(event, ui) { for(var entry in wpgmaps_localize) { InitMap(wpgmaps_localize[entry]['id'],'all',false); } });
            jQuery('body').on('click', '.vc_tta-panel-heading', function(){ setTimeout(function(){ for(var entry in wpgmaps_localize) { InitMap(wpgmaps_localize[entry]['id'],'all',false); jQuery( jQuery.fn.dataTable.tables(true) ).DataTable().responsive.recalc(); }}, 500); });
            jQuery('body').on('click', '.ult_exp_section', function(){ setTimeout(function(){ for(var entry in wpgmaps_localize) { InitMap(wpgmaps_localize[entry]['id'],'all',false); jQuery( jQuery.fn.dataTable.tables(true) ).DataTable().responsive.recalc(); }}, 300); });
            jQuery('body').on('click', '.x-accordion-heading', function(){ setTimeout(function(){ for(var entry in wpgmaps_localize) { InitMap(wpgmaps_localize[entry]['id'],'all',false); jQuery( jQuery.fn.dataTable.tables(true) ).DataTable().responsive.recalc(); }}, 100); });
            jQuery('body').on('click', '.x-nav-tabs li', function (event, ui) { setTimeout(function () { for (var entry in wpgmaps_localize) { InitMap(wpgmaps_localize[entry]['id'], 'all', false); } }, 200); });  
            jQuery('body').on('click', '.tab-title', function (event, ui) { setTimeout(function () { for (var entry in wpgmaps_localize) { InitMap(wpgmaps_localize[entry]['id'], 'all', false); } }, 200); });  
            jQuery('body').on('click', '.tab-link', function (event, ui) { setTimeout(function () { for (var entry in wpgmaps_localize) { InitMap(wpgmaps_localize[entry]['id'], 'all', false); } }, 200); });  
            jQuery('body').on('click', '.et_pb_tabs_controls li', function (event, ui) { setTimeout(function () { for (var entry in wpgmaps_localize) { InitMap(wpgmaps_localize[entry]['id'], 'all', false); } }, 200); });  
            jQuery('body').on('click', '.fusion-tab-heading', function (event, ui) { setTimeout(function () { for (var entry in wpgmaps_localize) { InitMap(wpgmaps_localize[entry]['id'], 'all', false); } }, 200); });  
            jQuery('body').on('click', '.et_pb_tab', function (event, ui) { setTimeout(function () { for (var entry in wpgmaps_localize) { InitMap(wpgmaps_localize[entry]['id'], 'all', false); } }, 200); });  
            jQuery('body').on('click', '.gdl-tabs li', function(event, ui) { setTimeout(function () { for (var entry in wpgmaps_localize) { InitMap(wpgmaps_localize[entry]['id'], 'all', false); } }, 200); });     
            jQuery('body').on('click', '#tabnav  li', function(event, ui) { setTimeout(function () { for (var entry in wpgmaps_localize) { InitMap(wpgmaps_localize[entry]['id'], 'all', false); } }, 200); });  
            jQuery('body').on('click', '.tri-tabs-nav span', function(event, ui) { setTimeout(function () { for (var entry in wpgmaps_localize) { InitMap(wpgmaps_localize[entry]['id'], 'all', false); } }, 200); });  
            

            for(var entry in wpgmaps_localize) {
                jQuery("#wpgmza_map_"+wpgmaps_localize[entry]['id']).css({
                    height:wpgmaps_localize[entry]['map_height']+''+wpgmaps_localize[entry]['map_height_type'],
                    width:wpgmaps_localize[entry]['map_width']+''+wpgmaps_localize[entry]['map_width_type']

                });            
            }
            
    
            for(var entry in wpgmaps_localize) {
                InitMap(wpgmaps_localize[entry]['id'],wpgmaps_localize_cat_ids[wpgmaps_localize[entry]['id']],false);
            }

            for(var entry in wpgmaps_localize) {

                /*
                removed in 5.54 as we are sorting via PHP first
                 */
                /*
                if (wpgmaps_localize[entry]['order_markers_by'] === "1") { wpgmaps_order_by = parseInt(0); } 
                else if (wpgmaps_localize[entry]['order_markers_by'] === "2") { wpgmaps_order_by = parseInt(2); } 
                else if (wpgmaps_localize[entry]['order_markers_by'] === "3") { wpgmaps_order_by = parseInt(4); } 
                else if (wpgmaps_localize[entry]['order_markers_by'] === "4") { wpgmaps_order_by = parseInt(5); } 
                else if (wpgmaps_localize[entry]['order_markers_by'] === "5") { wpgmaps_order_by = parseInt(3); } 
                else { wpgmaps_order_by = 0; }
                if (wpgmaps_localize[entry]['order_markers_choice'] === "1") { wpgmaps_order_by_choice = "asc"; } 
                else { wpgmaps_order_by_choice = "desc"; }
                */
                if (wpgmaps_localize_global_settings['wpgmza_default_items'] === "" || 'undefined' === typeof wpgmaps_localize_global_settings['wpgmza_default_items']) { wpgmza_settings_default_items = 10; } else { wpgmza_settings_default_items = parseInt(wpgmaps_localize_global_settings['wpgmza_default_items']);  }
                
                if (jQuery('#wpgmza_table_'+wpgmaps_localize[entry]['id']).length === 0) { } else { 
                    
					WPGMZA.CustomFieldFilterController.dataTablesSourceHTMLByMapID[wpgmaps_localize[entry]['id']] = jQuery('#wpgmza_table_'+wpgmaps_localize[entry]['id']).parent().html();
					
                    wpgmzaTable[wpgmaps_localize[entry]['id']] = jQuery('#wpgmza_table_'+wpgmaps_localize[entry]['id']).DataTable({
                        "bProcessing": true,"aaSorting" : [],
                        "iDisplayLength": wpgmza_settings_default_items,
                        responsive: true,
                        "oLanguage": {
                            "sLengthMenu": wpgm_dt_sLengthMenu,
                            "sZeroRecords": wpgm_dt_sZeroRecords,
                            "sInfo": wpgm_dt_sInfo,
                            "sInfoEmpty": wpgm_dt_sInfoEmpty,
                            "sInfoFiltered": wpgm_dt_sInfoFiltered,
                            "sSearch": wpgm_dt_sSearch,
                            "oPaginate" : {
                                "sFirst": wpgm_dt_sFirst,
                                "sLast": wpgm_dt_sLast,
                                "sNext": wpgm_dt_sNext,
                                "sPrevious": wpgm_dt_sPrevious,
                               "sSearch": wpgm_dt_sSearch
                            }
                        }
                     });

                    
                    if (typeof wpgmza_controls_active[entry] !== 'undefined' && wpgmza_controls_active[entry]) {
                        /* hide certain elements */
                        jQuery("#wpgmza_table_"+[entry]+"_length").hide();
                    }
                     
                     if (typeof wpgmaps_localize[entry]['other_settings']['store_locator_hide_before_search'] !== "undefined" && wpgmaps_localize[entry]['other_settings']['store_locator_hide_before_search'] === 1) { 
                        jQuery('#wpgmza_marker_holder_'+wpgmaps_localize[entry]['id']).hide();
                     }
                }
                if (typeof wpgmaps_localize[entry]['other_settings']['store_locator_hide_before_search'] !== "undefined" && wpgmaps_localize[entry]['other_settings']['store_locator_hide_before_search'] === 1) { 
                    if( jQuery('#wpgmza_marker_list_container_'+wpgmaps_localize[entry]['id']).length > 0 ){
                        jQuery('#wpgmza_marker_list_container_'+wpgmaps_localize[entry]['id']).hide();                         
                    }
                }
            }

        
        }
        
        

    
    
         
        

    });
    
    
    
    
    
    
    
    
    for(var entry in wpgmaps_localize) {

    /* general directions settings and variables */
    directionsDisplay[wpgmaps_localize[entry]['id']];
    directionsService[wpgmaps_localize[entry]['id']] = new google.maps.DirectionsService();
    var currentDirections = null;
    var oldDirections = [];
    var new_gps;

    if (wpgmaps_localize[entry]['styling_json'] && wpgmaps_localize[entry]['styling_json'].length && wpgmaps_localize[entry]['styling_enabled'] === "1") {
        wpgmza_adv_styling_json[wpgmaps_localize[entry]['id']] = wpgmza_parse_theme_data(wpgmaps_localize[entry]['styling_json']);
    } else {
        wpgmza_adv_styling_json[wpgmaps_localize[entry]['id']] = "";
    }


    MYMAP[wpgmaps_localize[entry]['id']] = {
        map: null,
        bounds: null,
        mc: null
    };

    
    if (wpgmaps_localize_global_settings['wpgmza_settings_map_draggable'] === "" || 'undefined' === typeof wpgmaps_localize_global_settings['wpgmza_settings_map_draggable']) { wpgmza_settings_map_draggable = true; } else { wpgmza_settings_map_draggable = false;  }
    if (wpgmaps_localize_global_settings['wpgmza_settings_map_clickzoom'] === "" || 'undefined' === typeof wpgmaps_localize_global_settings['wpgmza_settings_map_clickzoom']) { wpgmza_settings_map_clickzoom = false; } else { wpgmza_settings_map_clickzoom = true; }
    if (wpgmaps_localize_global_settings['wpgmza_settings_map_scroll'] === "" || 'undefined' === typeof wpgmaps_localize_global_settings['wpgmza_settings_map_scroll']) { wpgmza_settings_map_scroll = true; } else { wpgmza_settings_map_scroll = false; }
    if (wpgmaps_localize_global_settings['wpgmza_settings_map_zoom'] === "" || 'undefined' === typeof wpgmaps_localize_global_settings['wpgmza_settings_map_zoom']) { wpgmza_settings_map_zoom = true; } else { wpgmza_settings_map_zoom = false; }
    if (wpgmaps_localize_global_settings['wpgmza_settings_map_pan'] === "" || 'undefined' === typeof wpgmaps_localize_global_settings['wpgmza_settings_map_pan']) { wpgmza_settings_map_pan = true; } else { wpgmza_settings_map_pan = false; }
    if (wpgmaps_localize_global_settings['wpgmza_settings_map_type'] === "" || 'undefined' === typeof wpgmaps_localize_global_settings['wpgmza_settings_map_type']) { wpgmza_settings_map_type = true; } else { wpgmza_settings_map_type = false; }
    if (wpgmaps_localize_global_settings['wpgmza_settings_map_streetview'] === "" || 'undefined' === typeof wpgmaps_localize_global_settings['wpgmza_settings_map_streetview']) { wpgmza_settings_map_streetview = true; } else { wpgmza_settings_map_streetview = false; }


    if ('undefined' === typeof wpgmaps_localize[entry]['other_settings']['map_max_zoom'] || wpgmaps_localize[entry]['other_settings']['map_max_zoom'] === "") { wpgmza_max_zoom = 0; } else { wpgmza_max_zoom = parseInt(wpgmaps_localize[entry]['other_settings']['map_max_zoom']); }
    if ('undefined' === typeof wpgmaps_localize[entry]['other_settings']['map_min_zoom'] || wpgmaps_localize[entry]['other_settings']['map_min_zoom'] === "") { wpgmza_min_zoom = 21; } else { wpgmza_min_zoom = parseInt(wpgmaps_localize[entry]['other_settings']['map_min_zoom']); }


    
    MYMAP[wpgmaps_localize[entry]['id']].init = function(selector, latLng, zoom, maptype, mapid) {
        if (typeof wpgmaps_localize_map_types !== "undefined") {
            var override_type = wpgmaps_localize_map_types[mapid];
        } else {
            var override_type = "";
        }

        var myOptions = {
                zoom:zoom,
                minZoom: wpgmza_max_zoom,
                maxZoom: wpgmza_min_zoom,
                center: latLng,
                draggable: wpgmza_settings_map_draggable,
                disableDoubleClickZoom: wpgmza_settings_map_clickzoom,
                scrollwheel: wpgmza_settings_map_scroll,
                zoomControl: wpgmza_settings_map_zoom,
                panControl: wpgmza_settings_map_pan,
                mapTypeControl: wpgmza_settings_map_type,
                streetViewControl: wpgmza_settings_map_streetview,
                mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        
        if (override_type !== "") {
            if (override_type === "ROADMAP") { myOptions.mapTypeId = google.maps.MapTypeId.ROADMAP; }
            else if (override_type === "SATELLITE") { myOptions.mapTypeId = google.maps.MapTypeId.SATELLITE; }
            else if (override_type === "HYBRID") { myOptions.mapTypeId = google.maps.MapTypeId.HYBRID; }
            else if (override_type === "TERRAIN") { myOptions.mapTypeId = google.maps.MapTypeId.TERRAIN; } 
            else { myOptions.mapTypeId = google.maps.MapTypeId.ROADMAP; }
        } else {
            if (maptype === "1") { myOptions.mapTypeId = google.maps.MapTypeId.ROADMAP; }
            else if (maptype === "2") { myOptions.mapTypeId = google.maps.MapTypeId.SATELLITE; }
            else if (maptype === "3") { myOptions.mapTypeId = google.maps.MapTypeId.HYBRID; }
            else if (maptype === "4") { myOptions.mapTypeId = google.maps.MapTypeId.TERRAIN; }
            else { myOptions.mapTypeId = google.maps.MapTypeId.ROADMAP; }
        }
        
        if (wpgmaps_localize_global_settings['wpgmza_settings_map_full_screen_control'] === "" || 'undefined' === typeof wpgmaps_localize_global_settings['wpgmza_settings_map_full_screen_control']) { 
            myOptions.fullscreenControl = true;
        } else {
            myOptions.fullscreenControl = false;
        }


        this.map = new google.maps.Map(jQuery(selector)[0], myOptions);            
		
		var themeData = wpgmaps_localize[mapid]['other_settings']['wpgmza_theme_data'];
        if (themeData && themeData.length) {
			var obj = wpgmza_parse_theme_data(themeData);
            this.map.setOptions({styles: obj});
        } 

        if (override_type === "STREETVIEW") {
            var panoramaOptions = {
                position: latLng
              };
            var panorama = new google.maps.StreetViewPanorama(jQuery(selector)[0], panoramaOptions);
            this.map.setStreetView(panorama);
        }

         

        this.bounds = new google.maps.LatLngBounds();
        jQuery( "#wpgmza_map_"+mapid).trigger( 'wpgooglemaps_loaded' );
        
        

                
                
        /* insert polygon and polyline functionality */
        if (wpgmaps_localize_heatmap_settings !== null) {
            if (typeof wpgmaps_localize_heatmap_settings[mapid] !== "undefined") {
                  for(var poly_entry in wpgmaps_localize_heatmap_settings[mapid]) {
                    add_heatmap(mapid,poly_entry);
                  }
            }
        }
        if (wpgmaps_localize_polygon_settings !== null) {
            if (typeof wpgmaps_localize_polygon_settings[mapid] !== "undefined") {
                  for(var poly_entry in wpgmaps_localize_polygon_settings[mapid]) {
                    add_polygon(mapid,poly_entry);
                  }
            }
        }
        if (wpgmaps_localize_polyline_settings !== null) {
            if (typeof wpgmaps_localize_polyline_settings[mapid] !== "undefined") {
                  for(var poly_entry in wpgmaps_localize_polyline_settings[mapid]) {
                    add_polyline(mapid,poly_entry);
                  }
            }
        }
		
		if(window.wpgmza_circle_data_array[mapid]) {
			window.circle_array = [];
			
			for(var circle_id in wpgmza_circle_data_array) {
				
				// Check that this belongs to the array itself, as opposed to its prototype, or else this will break if you add methods to the array prototype (please don't extend the native types)
				if(!wpgmza_circle_data_array[mapid].hasOwnProperty(circle_id))
					continue;
				
				add_circle(1, wpgmza_circle_data_array[mapid][circle_id]);
			}
		}
		
		if(window.wpgmza_rectangle_data_array[mapid]) {
			window.rectangle_array = [];
			
			for(var rectangle_id in wpgmza_rectangle_data_array) {
				
				// Check that this belongs to the array itself, as opposed to its prototype, or else this will break if you add methods to the array prototype (please don't extend the native types)
				if(!wpgmza_rectangle_data_array[mapid].hasOwnProperty(rectangle_id))
					continue;
				
				add_rectangle(1, wpgmza_rectangle_data_array[mapid][rectangle_id]);
				
			}
		}
		
        /*
        if (wpgmaps_localize_polyline_settings !== null) {
            if (wpgmaps_localize_polyline_settings[mapid] !== null) { 
                for(var poly_entry in wpgmaps_localize_polyline_settings[mapid]) {
                    var tmp_data = wpgmaps_localize_polyline_settings[mapid];

                    var tmp_polydata = tmp_data[poly_entry]['polydata'];
                     var WPGM_PathData = new Array();
                     for (tmp_entry2 in tmp_polydata) {
                         WPGM_PathData.push(new google.maps.LatLng(tmp_polydata[tmp_entry2][0], tmp_polydata[tmp_entry2][1]));

                     }
                    if (tmp_data[poly_entry]['lineopacity'] === null || tmp_data[poly_entry]['lineopacity'] === "") {
                        tmp_data[poly_entry]['lineopacity'] = 1;
                    }
                    
                    var WPGM_Path = new google.maps.Polyline({
                     path: WPGM_PathData,
                     strokeColor: "#"+tmp_data[poly_entry]['linecolor'],
                     strokeOpacity: tmp_data[poly_entry]['opacity'],
                     fillColor: "#"+tmp_data[poly_entry]['fillcolor'],
                     strokeWeight: tmp_data[poly_entry]['linethickness']
                   });
                   WPGM_Path.setMap(MYMAP[mapid].map);

                }
             }
        }
        */
         
		if(wpgmaps_localize[entry].other_settings)
		{
			if(wpgmaps_localize[entry].other_settings && wpgmaps_localize[entry].other_settings.list_markers_by == 6)
				MYMAP[entry].markerListing = new WPGMZA.ModernMarkerListing(entry);
			
			if(wpgmaps_localize[entry].other_settings.store_locator_style == 'modern')
				MYMAP[entry].storeLocator = new WPGMZA.ModernStoreLocator(entry);
			
			if(wpgmaps_localize[entry].other_settings.directions_box_style == 'modern')
				MYMAP[entry].directionsBox = new WPGMZA.ModernDirectionsBox(entry);
		}
		  
        if (wpgmaps_localize[entry]['bicycle'] === "1") {
            var bikeLayer = new google.maps.BicyclingLayer();
            bikeLayer.setMap(MYMAP[mapid].map);
        }        
        if (wpgmaps_localize[entry]['traffic'] === "1") {
            var trafficLayer = new google.maps.TrafficLayer();
            trafficLayer.setMap(MYMAP[mapid].map);
        }        
        if ("undefined" !== typeof wpgmaps_localize[mapid]['other_settings']['weather_layer'] && wpgmaps_localize[mapid]['other_settings']['weather_layer'] === 1) {
            if ("undefined" === typeof google.maps.weather) { } else {
                if ("undefined" !== typeof wpgmaps_localize[mapid]['other_settings']['weather_layer_temp_type'] && wpgmaps_localize[mapid]['other_settings']['weather_layer_temp_type'] === 2) {
                    var weatherLayer = new google.maps.weather.WeatherLayer({ 
                        temperatureUnits: google.maps.weather.TemperatureUnit.FAHRENHEIT
                    });
                    weatherLayer.setMap(MYMAP[mapid].map);
                } else {
                    var weatherLayer = new google.maps.weather.WeatherLayer({ 
                        temperatureUnits: google.maps.weather.TemperatureUnit.CELSIUS
                    });
                    weatherLayer.setMap(MYMAP[mapid].map);
                }
            }
        }        
        if ("undefined" !== typeof wpgmaps_localize[mapid]['other_settings']['cloud_layer'] && wpgmaps_localize[mapid]['other_settings']['cloud_layer'] === 1) {
            if ("undefined" === typeof google.maps.weather) { } else {
                var cloudLayer = new google.maps.weather.CloudLayer();
                cloudLayer.setMap(MYMAP[mapid].map);
            }
        }        
        if ("undefined" !== typeof wpgmaps_localize[mapid]['other_settings']['transport_layer'] && wpgmaps_localize[mapid]['other_settings']['transport_layer'] === 1) {
                var transitLayer = new google.maps.TransitLayer();
                transitLayer.setMap(MYMAP[mapid].map);
        }        
        if (wpgmaps_localize[entry]['kml'] !== "") {
            var wpgmaps_d = new Date();
            var wpgmaps_ms = wpgmaps_d.getTime();
            
            arr = wpgmaps_localize[mapid]['kml'].split(',');
            arr.forEach(function(entry) {
                var georssLayer = new google.maps.KmlLayer(entry+'?tstamp='+wpgmaps_ms,{preserveViewport: true});
                georssLayer.setMap(MYMAP[mapid].map);
            });


            
        }        
        if (wpgmaps_localize[mapid]['fusion'] !== "") {
            var fusionlayer = new google.maps.FusionTablesLayer(wpgmaps_localize[mapid]['fusion'], {
                  suppressInfoWindows: false
            });
            fusionlayer.setMap(MYMAP[mapid].map);
        }        



        if (typeof wpgmaps_localize[mapid]['other_settings']['push_in_map'] !== 'undefined' && wpgmaps_localize[mapid]['other_settings']['push_in_map'] === "1") {


            if (typeof wpgmaps_localize[mapid]['other_settings']['wpgmza_push_in_map_width'] !== 'undefined') {
                var wpgmza_con_width = wpgmaps_localize[mapid]['other_settings']['wpgmza_push_in_map_width'];
            } else {
                var wpgmza_con_width = "30%";
            }
            if (typeof wpgmaps_localize[mapid]['other_settings']['wpgmza_push_in_map_height'] !== 'undefined') {
                var wpgmza_con_height = wpgmaps_localize[mapid]['other_settings']['wpgmza_push_in_map_height'];
            } else {
                var wpgmza_con_height = "50%";
            }

            if (jQuery('#wpgmza_marker_holder_'+mapid).length) {
                var legend = document.getElementById('wpgmza_marker_holder_'+mapid);
                jQuery(legend).width(wpgmza_con_width);
                jQuery(legend).css('margin','15px');
                jQuery(legend).addClass('wpgmza_innermap_holder');
                jQuery(legend).addClass('wpgmza-shadow');
                jQuery('#wpgmza_table_'+mapid).addClass('');
                wpgmza_controls_active[mapid] = true;
            } else if (jQuery('#wpgmza_marker_list_container_'+mapid).length) {
                var legend_tmp = document.getElementById('wpgmza_marker_list_container_'+mapid);
                
                jQuery('#wpgmza_marker_list_container_'+mapid).wrap("<div id='wpgmza_marker_list_parent_"+mapid+"'></div>");
                var legend = document.getElementById('wpgmza_marker_list_parent_'+mapid);
                jQuery(legend).width(wpgmza_con_width);
                jQuery(legend).height(wpgmza_con_height);

                jQuery(legend).css('margin','15px');
                jQuery(legend).css('overflow','auto');

                /* check if we're using the carousel option */
                if (jQuery(legend_tmp).hasClass("wpgmza_marker_carousel")) { } else {
                    jQuery(legend).addClass('wpgmza_innermap_holder');
                    jQuery(legend).addClass('wpgmza-shadow');
                }

                jQuery('#wpgmza_marker_list_'+mapid).addClass('');
                wpgmza_controls_active[mapid] = true;

            } else if (jQuery('#wpgmza_marker_list_'+mapid).length) {
                var legend_tmp = document.getElementById('wpgmza_marker_list_'+mapid);
                
                jQuery('#wpgmza_marker_list_'+mapid).wrap("<div id='wpgmza_marker_list_parent_"+mapid+"'></div>");
                var legend = document.getElementById('wpgmza_marker_list_parent_'+mapid);
                jQuery(legend).width(wpgmza_con_width);
                jQuery(legend).height(wpgmza_con_height);

                jQuery(legend).css('margin','15px');
                jQuery(legend).css('overflow','auto');

                /* check if we're using the carousel option */
                if (jQuery(legend_tmp).hasClass("wpgmza_marker_carousel")) { } else {
                    jQuery(legend).addClass('wpgmza_innermap_holder');
                    jQuery(legend).addClass('wpgmza-shadow');
                }

                jQuery('#wpgmza_marker_list_'+mapid).addClass('');
                wpgmza_controls_active[mapid] = true;
            }
            /*
                beta - still to add options for this

            
            if (jQuery('#wpgmza_filter_'+mapid).length) {
                var legend_tmp = document.getElementById('wpgmza_filter_'+mapid);
                
                jQuery('#wpgmza_filter_'+mapid).wrap("<div id='wpgmza_filter_parent_"+mapid+"'></div>");
                var legend = document.getElementById('wpgmza_filter_parent_'+mapid);
                jQuery(legend).width(wpgmza_con_width);
                jQuery(legend).height(wpgmza_con_height);

                jQuery(legend).css('margin','15px');
                jQuery(legend).css('overflow','auto');

                
                if (jQuery(legend_tmp).hasClass("wpgmza_marker_carousel")) { } else {
                    jQuery(legend).addClass('wpgmza_innermap_holder');
                    jQuery(legend).addClass('wpgmza-shadow');
                }

                jQuery('#wpgmza_filter_'+mapid).addClass('');
                wpgmza_controls_active[mapid] = true;
            }
            */
            if (typeof legend !== 'undefined') { 
                if (typeof wpgmaps_localize[mapid]['other_settings']['push_in_map_placement'] !== 'undefined') {
                    if (wpgmaps_localize[mapid]['other_settings']['push_in_map_placement'] === "1") { MYMAP[mapid].map.controls[google.maps.ControlPosition.TOP_CENTER].push(legend); }
                    else if (wpgmaps_localize[mapid]['other_settings']['push_in_map_placement'] === "2") { MYMAP[mapid].map.controls[google.maps.ControlPosition.TOP_LEFT].push(legend); }
                    else if (wpgmaps_localize[mapid]['other_settings']['push_in_map_placement'] === "3") { MYMAP[mapid].map.controls[google.maps.ControlPosition.TOP_RIGHT].push(legend); }
                    else if (wpgmaps_localize[mapid]['other_settings']['push_in_map_placement'] === "4") { MYMAP[mapid].map.controls[google.maps.ControlPosition.LEFT_TOP].push(legend); }
                    else if (wpgmaps_localize[mapid]['other_settings']['push_in_map_placement'] === "5") { MYMAP[mapid].map.controls[google.maps.ControlPosition.RIGHT_TOP].push(legend); }
                    else if (wpgmaps_localize[mapid]['other_settings']['push_in_map_placement'] === "6") { MYMAP[mapid].map.controls[google.maps.ControlPosition.LEFT_CENTER].push(legend); }
                    else if (wpgmaps_localize[mapid]['other_settings']['push_in_map_placement'] === "7") { MYMAP[mapid].map.controls[google.maps.ControlPosition.RIGHT_CENTER].push(legend); }
                    else if (wpgmaps_localize[mapid]['other_settings']['push_in_map_placement'] === "8") { MYMAP[mapid].map.controls[google.maps.ControlPosition.LEFT_BOTTOM].push(legend); }
                    else if (wpgmaps_localize[mapid]['other_settings']['push_in_map_placement'] === "9") { MYMAP[mapid].map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(legend); }
                    else if (wpgmaps_localize[mapid]['other_settings']['push_in_map_placement'] === "10") { MYMAP[mapid].map.controls[google.maps.ControlPosition.BOTTOM_CENTER].push(legend); }
                    else if (wpgmaps_localize[mapid]['other_settings']['push_in_map_placement'] === "11") { MYMAP[mapid].map.controls[google.maps.ControlPosition.BOTTOM_LEFT].push(legend); }
                    else if (wpgmaps_localize[mapid]['other_settings']['push_in_map_placement'] === "12") { MYMAP[mapid].map.controls[google.maps.ControlPosition.BOTTOM_RIGHT].push(legend); }
                    else { MYMAP[mapid].map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(legend); }  

                } else { MYMAP[mapid].map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(legend); }  
            } 
        
        }
    };    

    




    google.maps.event.addDomListener(window, 'resize', function() {
        var myLatLng = MYMAP[wpgmaps_localize[entry]['id']].map.getCenter();
        
        if ('undefined' !== typeof MYMAP[wpgmaps_localize[entry]['id']].map) {
            MYMAP[wpgmaps_localize[entry]['id']].map.setCenter(myLatLng);
        }
    });

    jQuery(document).bind('webkitfullscreenchange mozfullscreenchange fullscreenchange', function() {
        var isFullScreen = document.fullScreen ||
            document.mozFullScreen ||
            document.webkitIsFullScreen;
        var modernMarkerButton = jQuery('.wpgmza-modern-marker-open-button');
        var modernPopoutPanel = jQuery('.wpgmza-popout-panel');
        var modernStoreLocator = jQuery('.wpgmza-modern-store-locator');
        var fullScreenMap = undefined;
        if (modernMarkerButton.length) {
            fullScreenMap = modernMarkerButton.parent('.wpgmza_map').children('div').first();
        } else if (modernPopoutPanel.length) {
            fullScreenMap = modernPopoutPanel.parent('.wpgmza_map').children('div').first();
        } else {
            fullScreenMap = modernStoreLocator.parent('.wpgmza_map').children('div').first();
        }
        if (isFullScreen && typeof fullScreenMap !== "undefined") {
            fullScreenMap.append(modernMarkerButton, modernPopoutPanel, modernStoreLocator);
        }
    });



    MYMAP[wpgmaps_localize[entry]['id']].placeMarkers = function(filename,map_id,cat_id,radius,searched_center,distance_type,search_title,show_markers) {

        var total_marker_cat_count;
        if( Object.prototype.toString.call( cat_id ) === '[object Array]' ) {
            total_marker_cat_count = Object.keys(cat_id).length;
        } else {
            total_marker_cat_count = 1;
        }

        if (typeof marker_array[map_id] !== "undefined") {
            for (var i = 0; i < marker_array[map_id].length; i++) {
                /* remove any instance of a marker first tio avoid using a full reinit which causes the map to flicker */
                if (typeof marker_array[map_id][i] !== 'undefined') { 
                    
                    marker_array[map_id][i].setMap(null);
                    /* Check which map we are working on, and only reset the correct markers. (Store locator, etc) */
                }
            }
        }

        /* reset store locator circle */
        if (typeof cityCircle[map_id] !== "undefined") {
            cityCircle[map_id].setMap(null);
        }

        /* reset store locator i` if any */
        if (typeof store_locator_marker[map_id] !== "undefined") {
            store_locator_marker[map_id].setMap(null);
        }

        marker_array[map_id] = new Array(); 
        marker_sl_array[map_id] = new Array(); 
        marker_array2[map_id] = new Array(); 
        

        if (show_markers || typeof show_markers === "undefined") { 
            
            if (typeof wpgm_g_e !== "undefined" && wpgm_g_e === '1') {
                var mcOptions = {
                    gridSize: 20,
                    maxZoom: 15,
                    styles: [{
                        height: 53,
                        url: "//ccplugins.co/markerclusterer/images/m1.png",
                        width: 53
                    },
                    {
                        height: 56,
                        url: "//ccplugins.co/markerclusterer/images/m2.png",
                        width: 56
                    },
                    {
                        height: 66,
                        url: "//ccplugins.co/markerclusterer/images/m3.png",
                        width: 66
                    },
                    {
                        height: 78,
                        url: "//ccplugins.co/markerclusterer/images/m4.png",
                        width: 78
                    },
                    {
                        height: 90,
                        url: "//ccplugins.co/markerclusterer/images/m5.png",
                        width: 90
                    }] 
                };


                if(typeof wpgmaps_custom_cluster_options !== "undefined"){
                    var customMcOptions = {};

                    if(typeof wpgmaps_custom_cluster_options['wpgmza_cluster_grid_size'] !== "undefined"){ customMcOptions['gridSize'] = parseInt(wpgmaps_custom_cluster_options['wpgmza_cluster_grid_size']); } else { customMcOptions['gridSize'] = mcOptions['gridSize']; }
                    if(typeof wpgmaps_custom_cluster_options['wpgmza_cluster_max_zoom'] !== "undefined"){ customMcOptions['maxZoom'] = parseInt(wpgmaps_custom_cluster_options['wpgmza_cluster_max_zoom']); } else { customMcOptions['maxZoom'] = mcOptions['maxZoom']; }
                    if(typeof wpgmaps_custom_cluster_options['wpgmza_cluster_min_cluster_size'] !== "undefined"){ customMcOptions['minimumClusterSize'] = parseInt(wpgmaps_custom_cluster_options['wpgmza_cluster_min_cluster_size']); } 
                    if(typeof wpgmaps_custom_cluster_options['wpgmza_cluster_zoom_click'] !== "undefined"){ customMcOptions['zoomOnClick'] = true; } else { customMcOptions['zoomOnClick'] = false; }


                    var level1 = {};
                    if(typeof wpgmaps_custom_cluster_options['wpgmza_gold_cluster_level1'] !== "undefined"){ level1['url'] = wpgmaps_custom_cluster_options['wpgmza_gold_cluster_level1'].replace(/%2F/g,"/"); }
                    if(typeof wpgmaps_custom_cluster_options['wpgmza_cluster_level1_width'] !== "undefined"){ level1['width'] = parseInt(wpgmaps_custom_cluster_options['wpgmza_cluster_level1_width']); }
                    if(typeof wpgmaps_custom_cluster_options['wpgmza_cluster_level1_height'] !== "undefined"){ level1['height'] = parseInt(wpgmaps_custom_cluster_options['wpgmza_cluster_level1_height']); }

                    var level2 = {};
                    if(typeof wpgmaps_custom_cluster_options['wpgmza_gold_cluster_level2'] !== "undefined"){ level2['url'] = wpgmaps_custom_cluster_options['wpgmza_gold_cluster_level2'].replace(/%2F/g,"/"); }
                    if(typeof wpgmaps_custom_cluster_options['wpgmza_cluster_level2_width'] !== "undefined"){ level2['width'] = parseInt(wpgmaps_custom_cluster_options['wpgmza_cluster_level2_width']); }
                    if(typeof wpgmaps_custom_cluster_options['wpgmza_cluster_level2_height'] !== "undefined"){ level2['height'] = parseInt(wpgmaps_custom_cluster_options['wpgmza_cluster_level2_height']); }

                    var level3 = {};
                    if(typeof wpgmaps_custom_cluster_options['wpgmza_gold_cluster_level3'] !== "undefined"){ level3['url'] = wpgmaps_custom_cluster_options['wpgmza_gold_cluster_level3'].replace(/%2F/g,"/"); }
                    if(typeof wpgmaps_custom_cluster_options['wpgmza_cluster_level3_width'] !== "undefined"){ level3['width'] = parseInt(wpgmaps_custom_cluster_options['wpgmza_cluster_level3_width']); }
                    if(typeof wpgmaps_custom_cluster_options['wpgmza_cluster_level3_height'] !== "undefined"){ level3['height'] = parseInt(wpgmaps_custom_cluster_options['wpgmza_cluster_level3_height']); }

                    var level4 = {};
                    if(typeof wpgmaps_custom_cluster_options['wpgmza_gold_cluster_level4'] !== "undefined"){ level4['url'] = wpgmaps_custom_cluster_options['wpgmza_gold_cluster_level4'].replace(/%2F/g,"/"); }
                    if(typeof wpgmaps_custom_cluster_options['wpgmza_cluster_level4_width'] !== "undefined"){ level4['width'] = parseInt(wpgmaps_custom_cluster_options['wpgmza_cluster_level4_width']); }
                    if(typeof wpgmaps_custom_cluster_options['wpgmza_cluster_level4_height'] !== "undefined"){ level4['height'] = parseInt(wpgmaps_custom_cluster_options['wpgmza_cluster_level4_height']); }

                    var level5 = {};
                    if(typeof wpgmaps_custom_cluster_options['wpgmza_gold_cluster_level5'] !== "undefined"){ level5['url'] = wpgmaps_custom_cluster_options['wpgmza_gold_cluster_level5'].replace(/%2F/g,"/"); }
                    if(typeof wpgmaps_custom_cluster_options['wpgmza_cluster_level5_width'] !== "undefined"){ level5['width'] = parseInt(wpgmaps_custom_cluster_options['wpgmza_cluster_level5_width']); }
                    if(typeof wpgmaps_custom_cluster_options['wpgmza_cluster_level5_height'] !== "undefined"){ level5['height'] = parseInt(wpgmaps_custom_cluster_options['wpgmza_cluster_level5_height']); }


                    if(typeof wpgmaps_custom_cluster_options['wpgmza_cluster_font_color'] !== "undefined"){
                        level1['textColor'] = wpgmaps_custom_cluster_options['wpgmza_cluster_font_color'];
                        level2['textColor'] = wpgmaps_custom_cluster_options['wpgmza_cluster_font_color'];
                        level3['textColor'] = wpgmaps_custom_cluster_options['wpgmza_cluster_font_color'];
                        level4['textColor'] = wpgmaps_custom_cluster_options['wpgmza_cluster_font_color'];
                        level5['textColor'] = wpgmaps_custom_cluster_options['wpgmza_cluster_font_color'];                       
                    }

                     if(typeof wpgmaps_custom_cluster_options['wpgmza_cluster_font_size'] !== "undefined"){
                        level1['textSize'] = parseInt(wpgmaps_custom_cluster_options['wpgmza_cluster_font_size']);
                        level2['textSize'] = parseInt(wpgmaps_custom_cluster_options['wpgmza_cluster_font_size']);
                        level3['textSize'] = parseInt(wpgmaps_custom_cluster_options['wpgmza_cluster_font_size']);
                        level4['textSize'] = parseInt(wpgmaps_custom_cluster_options['wpgmza_cluster_font_size']);
                        level5['textSize'] = parseInt(wpgmaps_custom_cluster_options['wpgmza_cluster_font_size']);                       
                    }

                    customMcOptions['styles'] = [ level1, level2, level3, level4, level5 ];

                    mcOptions = customMcOptions; //Override
                }

                if (wpgmaps_localize[entry]['mass_marker_support'] === "1" || wpgmaps_localize[entry]['mass_marker_support'] === null) { 
                    if (typeof markerClusterer[map_id] !== "undefined") { markerClusterer[map_id].clearMarkers(); }
                    markerClusterer[map_id] = new MarkerClusterer(MYMAP[map_id].map, null, mcOptions);
                }
            }

            var check1 = 0;

            if (wpgmaps_localize_global_settings['wpgmza_settings_image_width'] === "" || 'undefined' === typeof wpgmaps_localize_global_settings['wpgmza_settings_image_width']) { wpgmaps_localize_global_settings['wpgmza_settings_image_width'] = 'auto'; } else { wpgmaps_localize_global_settings['wpgmza_settings_image_width'] = wpgmaps_localize_global_settings['wpgmza_settings_image_width']+'px'; }
            if (wpgmaps_localize_global_settings['wpgmza_settings_image_height'] === "" || 'undefined' === typeof wpgmaps_localize_global_settings['wpgmza_settings_image_height']) { wpgmaps_localize_global_settings['wpgmza_settings_image_height'] = 'auto'; } else { wpgmaps_localize_global_settings['wpgmza_settings_image_height'] = wpgmaps_localize_global_settings['wpgmza_settings_image_height']+'px'; }


            if (marker_pull === '1') {

            
            
                jQuery.get(filename, function(xml){

                    jQuery(xml).find("marker").each(function(){
                        var wpgmza_def_icon = wpgmaps_localize[map_id]['default_marker'];
                        var wpmgza_map_id = jQuery(this).find('map_id').text();
                        var wpmgza_marker_id = jQuery(this).find('marker_id').text();
                        var wpmgza_title = jQuery(this).find('title').text();
                        var wpgmza_orig_title = wpmgza_title;
                        if (wpmgza_title !== "") {
                            var wpmgza_title = '<p class="wpgmza_infowindow_title">'+jQuery(this).find('title').text()+'</p>';
                        }
                        var wpmgza_address = jQuery(this).find('address').text();
                        if (wpmgza_address !== "") {
                            var wpmgza_show_address = '<p class="wpgmza_infowindow_address">'+wpmgza_address+'</p>';
                        } else {
                            var wpmgza_show_address = '';
                        }

                        var wpmgza_mapicon = jQuery(this).find('icon').text();
                        var wpmgza_image = jQuery(this).find('pic').text();
                        var wpmgza_desc  = jQuery(this).find('desc').text();
                        var wpgmza_orig_desc = wpmgza_desc;
                        if (wpmgza_desc !== "") {
                            var wpmgza_desc = '<p class="wpgmza_infowindow_description">'+jQuery(this).find('desc').text()+'</p>';
                        }
                        var wpmgza_linkd = jQuery(this).find('linkd').text();
                        var wpmgza_linkd_orig = wpmgza_linkd;
                        var wpmgza_anim  = jQuery(this).find('anim').text();
                        var wpmgza_retina  = jQuery(this).find('retina').text();
                        var wpmgza_category  = jQuery(this).find('category').text();
                        var current_lat = jQuery(this).find('lat').text();
                        var current_lng = jQuery(this).find('lng').text();
                        var show_marker_radius = true;
                        var show_marker_title_string = true;

                        val = {};
                        if (wpmgza_mapicon) { val.icon = wpmgza_mapicon; }

                        var marker_other_data = jQuery(this).find('other_data').text();
                        if (typeof marker_other_data !== "undefined"  && marker_other_data !== "") {
                            marker_other_data = JSON.parse(marker_other_data);
                            val.other_data = {};
                            val.other_data = marker_other_data;
                        } else {
                            marker_other_data = false;
                        }

                        




                        if (radius !== null) {


                            if (check1 > 0 ) { } else { 
                                var sl_stroke_color = wpgmaps_localize[map_id]['other_settings']['sl_stroke_color'];
                                if (sl_stroke_color !== "" || sl_stroke_color !== null) { } else { sl_stroke_color = 'FF0000'; }
                                var sl_stroke_opacity = wpgmaps_localize[map_id]['other_settings']['sl_stroke_opacity'];
                                if (sl_stroke_opacity !== "" || sl_stroke_opacity !== null) { } else { sl_stroke_opacity = '0.25'; }
                                var sl_fill_opacity = wpgmaps_localize[map_id]['other_settings']['sl_fill_opacity'];
                                if (sl_fill_opacity !== "" || sl_fill_opacity !== null) { } else { sl_fill_opacity = '0.15'; }
                                var sl_fill_color = wpgmaps_localize[map_id]['other_settings']['sl_fill_color'];
                                if (sl_fill_color !== "" || sl_fill_color !== null) { } else { sl_fill_color = 'FF0000'; }

                                var point = new google.maps.LatLng(parseFloat(searched_center.lat()),parseFloat(searched_center.lng()));
                                MYMAP[map_id].bounds.extend(point);
                                if (wpgmaps_localize[map_id]['other_settings']['store_locator_bounce'] === 1) {
                                    if ("undefined" !== typeof wpgmaps_localize[map_id]['other_settings']['upload_default_sl_marker']) { 
                                        store_locator_marker[map_id] = new google.maps.Marker({
                                                position: point,
                                                map: MYMAP[map_id].map,
                                                icon: wpgmaps_localize[map_id]['other_settings']['upload_default_sl_marker']
                                                
                                        });

                                    } else {
                                        store_locator_marker[map_id] = new google.maps.Marker({
                                                position: point,
                                                map: MYMAP[map_id].map
                                                
                                        });
                                    }
                                    if (typeof wpgmaps_localize[map_id]['other_settings']['wpgmza_sl_animation'] !== "undefined") {
                                        if (wpgmaps_localize[map_id]['other_settings']['wpgmza_sl_animation'] === '1') { store_locator_marker[map_id].setAnimation(google.maps.Animation.BOUNCE); }
                                        else if (wpgmaps_localize[map_id]['other_settings']['wpgmza_sl_animation'] === '2') { store_locator_marker[map_id].setAnimation(google.maps.Animation.DROP); }
                                        else {
                                            store_locator_marker[map_id].setAnimation(null);
                                        }
                                        
                                    }

                                    
                                } else {
                                    /* do nothing */
                                }
								
								var factor = (distance_type == "1" ? 0.000621371 : 0.001);
								var options = {
									strokeColor: '#'+sl_stroke_color,
									strokeOpacity: sl_stroke_opacity,
									strokeWeight: 2,
									fillColor: '#'+sl_fill_color,
									fillOpacity: sl_fill_opacity,
									map: MYMAP[map_id].map,
									center: point,
									radius: parseInt(radius / factor)
								};
								
								wpgmza_show_store_locator_radius(map_id, point, radius, distance_type, options);
								
                                check1 = check1 + 1;
                            }

                            if (distance_type === "1") {
                                R = 3958.7558657440545; /* Radius of earth in Miles  */
                            } else {
                                R = 6378.16; /* Radius of earth in kilometers  */
                            }
                            var dLat = toRad(searched_center.lat()-current_lat);
                            var dLon = toRad(searched_center.lng()-current_lng); 
                            var a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(toRad(current_lat)) * Math.cos(toRad(searched_center.lat())) * Math.sin(dLon/2) * Math.sin(dLon/2); 
                            var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
                            var d = R * c;
                            
                            if (d < radius) { show_marker_radius = true; } else { show_marker_radius = false; }


                            /* check if they have done a title search too */
                            if (search_title === null || search_title === "") { show_marker_title_string = true; }
                            else {
                                var x = wpgmza_orig_title.toLowerCase().search(search_title.toLowerCase());
                                var y = wpgmza_orig_desc.toLowerCase().search(search_title.toLowerCase());
                                if (x >= 0 || y >= 0) {
                                    show_marker_title_string = true;
                                } else {
                                    show_marker_title_string = false;
                                }

                            }



                        }
                        var cat_is_cat;
                        cat_is_cat = false;
                        if( Object.prototype.toString.call( cat_id ) === '[object Array]' ) {


                            
                            
                            if (cat_id[0] === '0') { cat_id === "all"; }
                            for (var tmp_val in cat_id) {
                                /* only one category sent through to show */
                                if(wpmgza_category.indexOf(',') === -1) {
                                    if (cat_id[tmp_val] === wpmgza_category) { 
                                        cat_is_cat = true;
                                    }
                                } else { 
                                    var array = wpmgza_category.split(/\s*,\s*/);
                                    array.forEach(function(entry) {
                                        if (parseInt(cat_id[tmp_val]) === parseInt(entry)) {
                                            cat_is_cat = true;
                                        }
                                    });
                                } 


                            }

                            /* identify if we are using AND or OR in category logic */
                            if (typeof wpgmaps_localize_global_settings['wpgmza_settings_cat_logic'] === "undefined" || parseInt(wpgmaps_localize_global_settings['wpgmza_settings_cat_logic']) === 0) {
                                /* _OR_ LOGIC */
                            } else {
                                /* _AND_ LOGIC */
                                if (cat_logic_counter >= total_marker_cat_count) {
                                    /* dispaly this marker */
                                    cat_is_cat = true;
                                } else {
                                    cat_is_cat = false;
                                }

                            }

                        } else {

                            /* only one category sent through to show */
                            if(wpmgza_category.indexOf(',') === -1) {
                                if (cat_id === wpmgza_category) {
                                    cat_is_cat = true;
                                }
                            } else { 
                                var array = wpmgza_category.split(/\s*,\s*/);
                                array.forEach(function(entry) {
                                    if (parseInt(cat_id) === parseInt(entry)) {
                                        cat_is_cat = true;
                                    }
                                });
                            } 
                        }  

                        if (cat_id === 'all' || cat_is_cat) {

                            var wpmgza_infoopen  = jQuery(this).find('infoopen').text();
                            

                            if (wpmgza_image !== "") {
                                



                                /* timthumb completely removed in 5.54 */
                                /*if (wpgmaps_localize_global_settings['wpgmza_settings_use_timthumb'] === "" || 'undefined' === typeof wpgmaps_localize_global_settings['wpgmza_settings_use_timthumb']) {
                                        wpmgza_image = "<img src=\""+wpgmaps_plugurl+"/timthumb.php?src="+wpmgza_image+"&h="+wpgmaps_localize_global_settings['wpgmza_settings_image_height']+"&w="+wpgmaps_localize_global_settings['wpgmza_settings_image_width']+"&zc=1\" title=\"\" class=\"wpgmza_infowindow_image\" width=\""+wpgmaps_localize_global_settings['wpgmza_settings_image_width']+"\" height=\""+wpgmaps_localize_global_settings['wpgmza_settings_image_height']+"\" style=\"float:right; width:"+wpgmaps_localize_global_settings['wpgmza_settings_image_width']+"px; height:"+wpgmaps_localize_global_settings['wpgmza_settings_image_height']+"px;\" />";
                                } else {*/
                                    if ('undefined' === typeof wpgmaps_localize_global_settings['wpgmza_settings_image_resizing'] || wpgmaps_localize_global_settings['wpgmza_settings_image_resizing'] === "yes") {

                                        wpmgza_image = "<img src=\""+wpmgza_image+"\" title=\"\" class=\"wpgmza_infowindow_image\" alt=\"\" style=\"float:right; width:"+wpgmaps_localize_global_settings['wpgmza_settings_image_width']+"; height:"+wpgmaps_localize_global_settings['wpgmza_settings_image_height']+"; max-width:"+wpgmaps_localize_global_settings['wpgmza_settings_infowindow_width']+"px !important;\" />";

                                    } else {
                                        wpmgza_image = "<img src=\""+wpmgza_image+"\" class=\"wpgmza_infowindow_image wpgmza_map_image\" style=\"float:right; margin:5px; max-width:"+wpgmaps_localize_global_settings['wpgmza_settings_infowindow_width']+"px !important;\" />";
                                    }
                                /*}*/


                            } else { wpmgza_image = ""; }


                            if (wpmgza_linkd !== "") {
                                if (wpgmaps_localize_global_settings['wpgmza_settings_infowindow_links'] === "yes") { wpgmza_iw_links_target = "target='_BLANK'";  }
                                else { wpgmza_iw_links_target = ''; }
                                wpmgza_linkd = "<p class=\"wpgmza_infowindow_link\"><a class=\"wpgmza_infowindow_link\" href=\""+wpmgza_linkd+"\" "+wpgmza_iw_links_target+" title=\""+wpgmaps_lang_more_details+"\">"+wpgmaps_lang_more_details+"</a></p>";
                            } else {
                                wpgmza_iw_links_target = "";
                            }

                            if (wpmgza_mapicon === "" || !wpmgza_mapicon) { if (wpgmza_def_icon !== "") { wpmgza_mapicon = wpgmaps_localize[map_id]['default_marker']; } }

                            var wpgmza_optimized = true;
                            if (wpmgza_retina === "1" && wpmgza_mapicon !== "0") {
                                wpmgza_mapicon = new google.maps.MarkerImage(wpmgza_mapicon, null, null, null, new google.maps.Size(wpgmza_retina_width,wpgmza_retina_height));
                                wpgmza_optimized = false;
                            }



                            var lat = jQuery(this).find('lat').text();
                            var lng = jQuery(this).find('lng').text();
                            var point = new google.maps.LatLng(parseFloat(lat),parseFloat(lng));
                            MYMAP[map_id].bounds.extend(point);

                            


                            if (show_marker_radius === true && show_marker_title_string === true) {
                                if (wpmgza_anim === "1") {
                                    if (wpmgza_mapicon === null || wpmgza_mapicon === "" || wpmgza_mapicon === 0 || wpmgza_mapicon === "0") {
                                        var marker = new google.maps.Marker({
                                                position: point,
                                                map: MYMAP[map_id].map,
                                                animation: google.maps.Animation.BOUNCE
                                        });
                                    } else {
                                        var marker = new google.maps.Marker({
                                                position: point,
                                                map: MYMAP[map_id].map,
                                                icon: wpmgza_mapicon,
                                                animation: google.maps.Animation.BOUNCE,
                                                optimized: wpgmza_optimized

                                        });
                                    }
                                }
                                else if (wpmgza_anim === "2") {
                                    if (wpmgza_mapicon === null || wpmgza_mapicon === "" || wpmgza_mapicon === 0 || wpmgza_mapicon === "0") {
                                        var marker = new google.maps.Marker({
                                                position: point,
                                                map: MYMAP[map_id].map,
                                                animation: google.maps.Animation.DROP
                                        });

                                    } else {

                                        var marker = new google.maps.Marker({
                                                position: point,
                                                map: MYMAP[map_id].map,
                                                icon: wpmgza_mapicon,
                                                animation: google.maps.Animation.DROP,
                                                optimized: wpgmza_optimized
                                        });
                                    }
                                }
                                else {
                                    if (wpmgza_mapicon === null || wpmgza_mapicon === "" || wpmgza_mapicon === 0 || wpmgza_mapicon === "0") {
                                        var marker = new google.maps.Marker({
                                                position: point,
                                                map: MYMAP[map_id].map,
                                                optimized: wpgmza_optimized
                                        });

                                    } else {
                                        var marker = new google.maps.Marker({
                                                position: point,
                                                map: MYMAP[map_id].map,
                                                icon: wpmgza_mapicon,
                                                optimized: wpgmza_optimized
                                        });
                                    }
                                }

                                

                                if (wpgmaps_localize_global_settings['wpgmza_settings_infowindow_address'] === "yes") {
                                    wpmgza_show_address = "";
                                }
                                if (wpgmaps_localize[map_id]['directions_enabled'] === "1") {
                                    wpmgza_dir_enabled = '<p><a href="javascript:void(0);" id="'+map_id+'" class="wpgmza_gd" wpgm_addr_field="'+wpmgza_address+'" gps="'+parseFloat(lat)+','+parseFloat(lng)+'">'+wpgmaps_lang_get_dir+'</a></p>';
                                } else {
                                    wpmgza_dir_enabled = '';
                                }
                                if (radius !== null) {                                 
                                    if (distance_type === "1") {
                                        d_string = "<p>"+Math.round(d,2)+' '+wpgmaps_lang_m_away+"</p>"; 
                                    } else {
                                        d_string = "<p>"+Math.round(d,2)+' '+wpgmaps_lang_km_away+"</p>"; 
                                    }
                                } else { d_string = ''; }

                                if (wpmgza_image !== "") {
                                        var html='<div class="wpgmza_markerbox scrollFix">'+
                                        wpmgza_image+
                                        wpmgza_title+
                                        wpmgza_show_address+
                                        wpmgza_desc+
                                        wpmgza_linkd+
                                        d_string+
                                        wpmgza_dir_enabled+
                                        '</div>';

                                } else {
                                        var html='<div class="wpgmza_markerbox scrollFix">'+                                
                                    
                                        wpmgza_image+
                                        wpmgza_title+
                                        wpmgza_show_address+
                                        wpmgza_desc+
                                        wpmgza_linkd+
                                        d_string+
                                        wpmgza_dir_enabled+
                                        '</div>';

                                }
								
								if(val.custom_fields_html)
									html += val.custom_fields_html;

                                var marker_data_object = {
                                    title: wpgmza_orig_title,
                                    address: wpmgza_address,
                                    image: jQuery(this).find('pic').text(),
                                    link: wpmgza_linkd_orig,
                                    directions: wpmgza_dir_enabled,
                                    distance: d_string,
                                    desc: wpgmza_orig_desc,
                                    gps: parseFloat(lat)+','+parseFloat(lng),
                                    link_target:wpgmza_iw_links_target
                                };
                                infoWindow[wpmgza_marker_id] = new google.maps.InfoWindow();
                                if (wpgmaps_localize_global_settings['wpgmza_settings_infowindow_width'] === "" || 'undefined' === typeof wpgmaps_localize_global_settings['wpgmza_settings_infowindow_width']) {
                                    wpgmaps_localize_global_settings['wpgmza_settings_infowindow_width'] = false;
                                }

                                if (wpmgza_infoopen === "1") {
                                    wpgmza_open_marker_func(map_id,marker,html,click_from_list,marker_data_object,wpmgza_marker_id,val);
                                }

                                /* do they want to open a marker from a GET variable? */
                                if (typeof wpgmza_open_marker !== "underfined") {
                                    if (wpgmza_open_marker === wpmgza_marker_id) { 

                                        
                                        if (wpgmaps_localize_global_settings['wpgmza_settings_infowindow_width']) { infoWindow[wpmgza_marker_id].setOptions({maxWidth:wpgmaps_localize_global_settings['wpgmza_settings_infowindow_width']}); }
                                        infoWindow[wpmgza_marker_id].setContent(html);
                                        infoWindow[wpmgza_marker_id].open(MYMAP[map_id].map, marker);
                                        MYMAP[map_id].map.setCenter(point);
                                        if (typeof wpgmza_open_marker_zoom !== "undefined") {
                                            MYMAP[map_id].map.setZoom(parseInt(wpgmza_open_marker_zoom));
                                        }
                                    }
                                }

                                if (typeof wpgmaps_localize[map_id]['other_settings']['click_open_link'] !== "undefined" && wpgmaps_localize[map_id]['other_settings']['click_open_link'] === 1 && typeof wpmgza_linkd_orig !== "undefined" && wpmgza_linkd_orig !== "") {
                                    google.maps.event.addListener(marker, 'click', function(evt) {
                                        location = wpmgza_linkd_orig;
                                   }); 
                                }
                                if (wpgmaps_localize_global_settings['wpgmza_settings_map_open_marker_by'] === "" || 'undefined' === typeof wpgmaps_localize_global_settings['wpgmza_settings_map_open_marker_by'] || wpgmaps_localize_global_settings['wpgmza_settings_map_open_marker_by'] === '1') { 
                                    google.maps.event.addListener(marker, 'click', function(evt) {

                                        wpgmza_open_marker_func(map_id,marker,html,click_from_list,marker_data_object,wpmgza_marker_id,val);
                                    }); 
                                } else {
                                    google.maps.event.addListener(marker, 'mouseover', function(evt) {
                                        wpgmza_open_marker_func(map_id,marker,html,click_from_list,marker_data_object,wpmgza_marker_id,val);
                                        
                                    }); 
                                }



                                

                                marker_array[map_id][wpmgza_marker_id] = marker;
                                marker_array[map_id][wpmgza_marker_id].default_icon = marker.icon;

                                google.maps.event.addListener(infoWindow[wpmgza_marker_id], 'closeclick', function(evt) {   
                                    if (typeof marker_array[map_id][wpmgza_marker_id].icon !== "undefined" && marker_array[map_id][wpmgza_marker_id].icon !== "") {
                                        marker_array[map_id][wpmgza_marker_id].setIcon(marker_array[map_id][wpmgza_marker_id].default_icon);
                                    } else {
                                        marker_array[map_id][wpmgza_marker_id].setIcon(null);
                                    }
                                });                            

                                marker_array2[map_id].push(marker);
                                marker_sl_array[map_id].push(wpmgza_marker_id);

                            }
                        } 

                    });
        
                    if (typeof wpgm_g_e !== "undefined" && wpgm_g_e === '1') {

                        if (wpgmaps_localize[map_id]['mass_marker_support'] === "1" || wpgmaps_localize[map_id]['mass_marker_support'] === null) { 
                            if (typeof markerClusterer[map_id] !== "undefined") { markerClusterer[map_id].addMarkers(marker_array2[map_id]); }
                        }
                    }


                    if (radius !== null) {

                        wpgmza_filter_marker_lists_by_array(map_id,marker_sl_array[map_id]);

                    }

                });
            
            } else { 
                /* DB method */
                jQuery.each(document.marker_data_array[map_id], function(i, val) {
                    
                    
                    var wpgmza_def_icon = wpgmaps_localize[map_id]['default_marker'];
                    

                    /*
                        removed due to mashup incompatibilities. If used, it tries to push the marker to the markers original ID instead of the MASHUP MAP ID.
                        var wpmgza_map_id = val.map_id;
                    
                    */ 
                    var wpmgza_map_id = map_id;
                    

                    var wpmgza_marker_id = val.marker_id;

                    var wpmgza_title = val.title;
                    var wpgmza_orig_title = wpmgza_title;
                    if (wpmgza_title !== "") {
                        var wpmgza_title = '<p class="wpgmza_infowindow_title">'+val.title+'</p>';
                    }
                    var wpmgza_address = val.address;
                    if (wpmgza_address !== "") {
                        var wpmgza_show_address = '<p class="wpgmza_infowindow_address">'+wpmgza_address+'</p>';
                    } else {
                        var wpmgza_show_address = '';
                    }
                    var wpmgza_mapicon = val.icon;
                    var wpmgza_image = val.pic;
                    var wpmgza_desc  = val.desc;
                    var wpgmza_orig_desc = wpmgza_desc;
                    if (wpmgza_desc !== "") {
                        var wpmgza_desc = '<p class="wpgmza_infowindow_description">'+val.desc;+'</p>';
                    }
                    var wpmgza_linkd = val.linkd;
                    var wpmgza_linkd_orig = wpmgza_linkd;

                    var wpmgza_anim  = val.anim;
                    var wpmgza_retina  = val.retina;
                    var wpmgza_category  = val.category;
                    var current_lat = val.lat;
                    var current_lng = val.lng;
                    var show_marker_radius = true;
                    var show_marker_title_string = true;

                    if (typeof wpgmza_override_marker !== "undefined" && typeof wpgmza_override_marker[map_id] !== "undefined") {
                        if (parseInt(wpmgza_marker_id) == parseInt(wpgmza_override_marker[map_id])) {
                            /* we have a match for the focus marker, lets save the lat and lng so we can center on it when done */
                            focus_lat = current_lat;
                            focus_lng = current_lng;
                        }
                    }



                    if (radius !== null) {


                        if (check1 > 0 ) { } else { 
                            var sl_stroke_color = wpgmaps_localize[map_id]['other_settings']['sl_stroke_color'];
                            if (sl_stroke_color !== "" || sl_stroke_color !== null) { } else { sl_stroke_color = 'FF0000'; }
                            var sl_stroke_opacity = wpgmaps_localize[map_id]['other_settings']['sl_stroke_opacity'];
                            if (sl_stroke_opacity !== "" || sl_stroke_opacity !== null) { } else { sl_stroke_opacity = '0.25'; }
                            var sl_fill_opacity = wpgmaps_localize[map_id]['other_settings']['sl_fill_opacity'];
                            if (sl_fill_opacity !== "" || sl_fill_opacity !== null) { } else { sl_fill_opacity = '0.15'; }
                            var sl_fill_color = wpgmaps_localize[map_id]['other_settings']['sl_fill_color'];
                            if (sl_fill_color !== "" || sl_fill_color !== null) { } else { sl_fill_color = 'FF0000'; }

                            var point = new google.maps.LatLng(parseFloat(searched_center.lat()),parseFloat(searched_center.lng()));
                            MYMAP[map_id].bounds.extend(point);

                            if (wpgmaps_localize[map_id]['other_settings']['store_locator_bounce'] === 1) {
                                    if ("undefined" !== typeof wpgmaps_localize[map_id]['other_settings']['upload_default_sl_marker']) { 
                                        store_locator_marker[map_id] = new google.maps.Marker({
                                                position: point,
                                                map: MYMAP[map_id].map,
                                                icon: wpgmaps_localize[map_id]['other_settings']['upload_default_sl_marker']
                                        });

                                    } else {
                                        store_locator_marker[map_id] = new google.maps.Marker({
                                                position: point,
                                                map: MYMAP[map_id].map
                                                
                                        });
                                    }
                                    if (typeof wpgmaps_localize[map_id]['other_settings']['wpgmza_sl_animation'] !== "undefined") {
                                        if (wpgmaps_localize[map_id]['other_settings']['wpgmza_sl_animation'] === '1') { store_locator_marker[map_id].setAnimation(google.maps.Animation.BOUNCE); }
                                        else if (wpgmaps_localize[map_id]['other_settings']['wpgmza_sl_animation'] === '2') { store_locator_marker[map_id].setAnimation(google.maps.Animation.DROP); }
                                        else {
                                            store_locator_marker[map_id].setAnimation(null);
                                        }
                                        
                                    }

                                
                                
                            } else {
                                /* do nothing */
                            }
                            
							var factor = (distance_type == "1" ? 0.000621371 : 0.001);
							var options = {
								strokeColor: '#'+sl_stroke_color,
								strokeOpacity: sl_stroke_opacity,
								strokeWeight: 2,
								fillColor: '#'+sl_fill_color,
								fillOpacity: sl_fill_opacity,
								map: MYMAP[map_id].map,
								center: point,
								radius: parseInt(radius / factor)
							};
							
							wpgmza_show_store_locator_radius(map_id, point, radius, distance_type, options);
							
                            check1 = check1 + 1;
                        }

                        if (distance_type === "1") {
                            R = 3958.7558657440545;
                        } else {
                            R = 6378.16;
                        }
                        var dLat = toRad(searched_center.lat()-current_lat);
                        var dLon = toRad(searched_center.lng()-current_lng); 
                        var a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(toRad(current_lat)) * Math.cos(toRad(searched_center.lat())) * Math.sin(dLon/2) * Math.sin(dLon/2); 
                        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
                        var d = R * c;
                        
                        if (d < radius) { show_marker_radius = true; } else { show_marker_radius = false; }


                        /* check if they have done a title search too */
                        if (search_title === null || search_title === "") { show_marker_title_string = true; }
                        else {
                            var x = wpgmza_orig_title.toLowerCase().search(search_title.toLowerCase());
                            var y = wpgmza_orig_desc.toLowerCase().search(search_title.toLowerCase());
                            if (x >= 0 || y >= 0) {
                                show_marker_title_string = true;
                            } else {
                                show_marker_title_string = false;
                            }

                        }



                    }

                    var cat_is_cat;
                    cat_is_cat = false;
                    cat_logic_counter = 0;
                    if( Object.prototype.toString.call( cat_id ) === '[object Array]' ) {

                        /* work with category array */
                        if (cat_id[0] === '0') { cat_id === "all"; cat_logic_counter++; }
                        for (var tmp_val in cat_id) {
                            /* only one category sent through to show */
                            if(wpmgza_category.indexOf(',') === -1) {
                                if (cat_id[tmp_val] === wpmgza_category) { 
                                    cat_is_cat = true;
                                    cat_logic_counter++;
                                }
                            } else { 
                                var array = wpmgza_category.split(/\s*,\s*/);
                                array.forEach(function(entry) {
                                    if (parseInt(cat_id[tmp_val]) === parseInt(entry)) {
                                        cat_is_cat = true;
                                        cat_logic_counter++;
                                    }
                                });
                            } 


                        }


                        /* identify if we are using AND or OR in category logic */
                        if (typeof wpgmaps_localize_global_settings['wpgmza_settings_cat_logic'] === "undefined" || parseInt(wpgmaps_localize_global_settings['wpgmza_settings_cat_logic']) === 0) {
                            /* _OR_ LOGIC */
                        } else {
                            /* _AND_ LOGIC */
                            if (cat_logic_counter >= total_marker_cat_count) {
                                /* dispaly this marker */
                                cat_is_cat = true;
                            } else {
                                cat_is_cat = false;
                            }

                        }
                    } else {

                        /* only one category sent through to show */
                       if(typeof wpmgza_category !== 'undefined') {
                           if (wpmgza_category.indexOf(',') === -1) {
                                if (cat_id === wpmgza_category) {
                                    cat_is_cat = true;
                                }
                            } else { 
                                var array = wpmgza_category.split(/\s*,\s*/);
                                array.forEach(function(entry) {
                                    if (parseInt(cat_id) === parseInt(entry)) {
                                        cat_is_cat = true;
                                    }
                                });
                            } 
                        } else {

                            
                        }
                    }  

                    if (cat_id === 'all' || cat_is_cat) {
                        
                        var wpmgza_infoopen  = val.infoopen;
                        if (wpmgza_image !== "") {

                            /* timthumb completely removed in 5.54 */
                            /*if (wpgmaps_localize_global_settings['wpgmza_settings_use_timthumb'] === "" || 'undefined' === typeof wpgmaps_localize_global_settings['wpgmza_settings_use_timthumb']) {
                                    wpmgza_image = "<img src=\""+wpgmaps_plugurl+"/timthumb.php?src="+wpmgza_image+"&h="+wpgmaps_localize_global_settings['wpgmza_settings_image_height']+"&w="+wpgmaps_localize_global_settings['wpgmza_settings_image_width']+"&zc=1\" title=\"\" class=\"wpgmza_infowindow_image\" width=\""+wpgmaps_localize_global_settings['wpgmza_settings_image_width']+"\" height=\""+wpgmaps_localize_global_settings['wpgmza_settings_image_height']+"\" style=\"float:right; width:"+wpgmaps_localize_global_settings['wpgmza_settings_image_width']+"px; height:"+wpgmaps_localize_global_settings['wpgmza_settings_image_height']+"px;\" />";
                            } else {*/
                                if ('undefined' === typeof wpgmaps_localize_global_settings['wpgmza_settings_image_resizing'] || wpgmaps_localize_global_settings['wpgmza_settings_image_resizing'] === "yes") {
                                        wpmgza_image = "<img src=\""+wpmgza_image+"\" title=\"\" class=\"wpgmza_infowindow_image\" alt=\"\" style=\"float:right; width:"+wpgmaps_localize_global_settings['wpgmza_settings_image_width']+"; height:"+wpgmaps_localize_global_settings['wpgmza_settings_image_height']+"; max-width:"+wpgmaps_localize_global_settings['wpgmza_settings_infowindow_width']+"px !important;\" />";

                                    } else {
                                        wpmgza_image = "<img src=\""+wpmgza_image+"\" class=\"wpgmza_infowindow_image wpgmza_map_image\" style=\"float:right; margin:5px; max-width:"+wpgmaps_localize_global_settings['wpgmza_settings_infowindow_width']+"px !important;\" />";
                                }
                            /*}*/
                        }

                        if (wpmgza_linkd !== "") {
                            if (wpgmaps_localize_global_settings['wpgmza_settings_infowindow_links'] === "yes") { wpgmza_iw_links_target = "target='_BLANK'";  }
                            else { wpgmza_iw_links_target = ''; }
                            wpmgza_linkd = "<p class=\"wpgmza_infowindow_link\"><a class=\"wpgmza_infowindow_link\" href=\""+wpmgza_linkd+"\" "+wpgmza_iw_links_target+" title=\""+wpgmaps_lang_more_details+"\">"+wpgmaps_lang_more_details+"</a></p>";
                        } else {
                            wpgmza_iw_links_target = "";
                        }

                        if (wpmgza_mapicon === "" || !wpmgza_mapicon) { if (wpgmza_def_icon !== "") { wpmgza_mapicon = wpgmaps_localize[map_id]['default_marker']; } }
                        var wpgmza_optimized = true;
                        if (wpmgza_retina === "1" && wpmgza_mapicon !== "0") {
                            wpmgza_mapicon = new google.maps.MarkerImage(wpmgza_mapicon, null, null, null, new google.maps.Size(wpgmza_retina_width,wpgmza_retina_height));
                            wpgmza_optimized = false;
                        }


                        var lat = val.lat;
                        var lng = val.lng;
                        var point = new google.maps.LatLng(parseFloat(lat),parseFloat(lng));
                        MYMAP[map_id].bounds.extend(point);

                        
                        

                        if (show_marker_radius === true && show_marker_title_string === true) {
                            if (wpmgza_anim === "1") {
                                if (wpmgza_mapicon === null || wpmgza_mapicon === "" || wpmgza_mapicon === 0 || wpmgza_mapicon === "0") {
                                    var marker = new google.maps.Marker({
                                            position: point,
                                            map: MYMAP[map_id].map,
                                            animation: google.maps.Animation.BOUNCE
                                    });
                                } else {
                                    var marker = new google.maps.Marker({
                                            position: point,
                                            map: MYMAP[map_id].map,
                                            icon: wpmgza_mapicon,
                                            animation: google.maps.Animation.BOUNCE,
                                            optimized: wpgmza_optimized

                                    });
                                }
                            }
                            else if (wpmgza_anim === "2") {
                                if (wpmgza_mapicon === null || wpmgza_mapicon === "" || wpmgza_mapicon === 0 || wpmgza_mapicon === "0") {
                                    var marker = new google.maps.Marker({
                                            position: point,
                                            map: MYMAP[map_id].map,
                                            animation: google.maps.Animation.DROP
                                    });

                                } else {

                                    var marker = new google.maps.Marker({
                                            position: point,
                                            map: MYMAP[map_id].map,
                                            icon: wpmgza_mapicon,
                                            animation: google.maps.Animation.DROP,
                                            optimized: wpgmza_optimized
                                    });
                                }
                            }
                            else {
                                if (wpmgza_mapicon === null || wpmgza_mapicon === "" || wpmgza_mapicon === 0 || wpmgza_mapicon === "0") {
                                    var marker = new google.maps.Marker({
                                            position: point,
                                            map: MYMAP[map_id].map,
                                            optimized: wpgmza_optimized
                                    });

                                } else {
                                    var marker = new google.maps.Marker({
                                            position: point,
                                            map: MYMAP[map_id].map,
                                            icon: wpmgza_mapicon,
                                            optimized: wpgmza_optimized
                                    });
                                }
                            }
                            

                            if (wpgmaps_localize_global_settings['wpgmza_settings_infowindow_address'] === "yes") {
                                wpmgza_show_address = "";
                            }
                            if (wpgmaps_localize[entry]['directions_enabled'] === "1") {
                                wpmgza_dir_enabled = '<p><a href="javascript:void(0);" id="'+map_id+'" class="wpgmza_gd" wpgm_addr_field="'+wpmgza_address+'" gps="'+parseFloat(lat)+','+parseFloat(lng)+'">'+wpgmaps_lang_get_dir+'</a></p>';
                            } else {
                                wpmgza_dir_enabled = '';
                            }
                            if (radius !== null) {                                 
                                if (distance_type === "1") {
                                    d_string = "<p>"+Math.round(d,2)+' '+wpgmaps_lang_m_away+"</p>"; 
                                } else {
                                    d_string = "<p>"+Math.round(d,2)+' '+wpgmaps_lang_km_away+"</p>"; 
                                }
                            } else { d_string = ''; }
                            if (wpmgza_image !== "") {
                                var html='<div class="wpgmza_markerbox scrollFix">'+
                                    wpmgza_image+
                                    wpmgza_title+
                                    wpmgza_show_address+
                                    wpmgza_desc+
                                    wpmgza_linkd+
                                    d_string+
                                    wpmgza_dir_enabled+
                                    '</div>';

                            } else {
                                var html='<div class="wpgmza_markerbox scrollFix">'+
                                    wpmgza_image+
                                    wpmgza_title+
                                    wpmgza_show_address+
                                    wpmgza_desc+
                                    wpmgza_linkd+
                                    d_string+
                                    wpmgza_dir_enabled+
                                    '</div>';

                            }
							
							if(val.custom_fields_html)
								html += val.custom_fields_html;

                            var marker_data_object = {
                                title: wpgmza_orig_title,
                                address: wpmgza_address,
                                image: val.pic,
                                link: wpmgza_linkd_orig,
                                directions: wpmgza_dir_enabled,
                                distance: d_string,
                                desc: wpgmza_orig_desc,
                                gps: parseFloat(lat)+','+parseFloat(lng),
                                link_target:wpgmza_iw_links_target
                            };


                            infoWindow[wpmgza_marker_id] = new google.maps.InfoWindow();
                            if (wpgmaps_localize_global_settings['wpgmza_settings_infowindow_width'] === "" || 'undefined' === typeof wpgmaps_localize_global_settings['wpgmza_settings_infowindow_width']) {
                                wpgmaps_localize_global_settings['wpgmza_settings_infowindow_width'] = false;
                            }
                            if (wpmgza_infoopen === "1") {
                                
                                //infoWindow[wpmgza_marker_id].setContent(html);
                                //infoWindow[wpmgza_marker_id].open(MYMAP[map_id].map, marker);
								setTimeout(function() {
									openInfoWindow(i, map_id, false);
								}, 1000);
                            }
                            /* do they want to open a marker from a GET variable? */
                            if (typeof wpgmza_open_marker !== "undefined") {
                                if (wpgmza_open_marker === wpmgza_marker_id) { 

                                    infoWindow[wpmgza_marker_id].setOptions({maxWidth:wpgmaps_localize_global_settings['wpgmza_settings_infowindow_width']});
                                    infoWindow[wpmgza_marker_id].setContent(html);
                                    infoWindow[wpmgza_marker_id].open(MYMAP[map_id].map, marker);
                                    MYMAP[map_id].map.setCenter(point);
                                    if (typeof wpgmza_open_marker_zoom !== "undefined") {
                                        MYMAP[map_id].map.setZoom(parseInt(wpgmza_open_marker_zoom));
                                    }
                                }
                            }
                            if (typeof wpgmaps_localize[map_id]['other_settings']['click_open_link'] !== "undefined" && wpgmaps_localize[map_id]['other_settings']['click_open_link'] === 1 && typeof wpmgza_linkd_orig !== "undefined" && wpmgza_linkd_orig !== "") {


                                google.maps.event.addListener(marker, 'click', function(evt) {
                                    location = wpmgza_linkd_orig;
                                }); 
                            }
                            

                            if (wpgmaps_localize_global_settings['wpgmza_settings_map_open_marker_by'] === "" || 'undefined' === typeof wpgmaps_localize_global_settings['wpgmza_settings_map_open_marker_by'] || wpgmaps_localize_global_settings['wpgmza_settings_map_open_marker_by'] === '1') { 
                                google.maps.event.addListener(marker, 'click', function(evt) {
                                    if (typeof val.other_data !== "undefined" && typeof val.other_data.icon_on_click !== "undefined" && val.other_data.icon_on_click !== "") {
                                        marker.setIcon(val.other_data.icon_on_click);
                                    }
                                    wpgmza_open_marker_func(map_id,marker,html,click_from_list,marker_data_object,wpmgza_marker_id,val);
                                }); 
                            } else {
                                google.maps.event.addListener(marker, 'mouseover', function(evt) {
                                    if (typeof val.other_data !== "undefined" && typeof val.other_data.icon_on_click !== "undefined" && val.other_data.icon_on_click !== "") {

                                        marker.setIcon(val.other_data.icon_on_click);
                                    }
                                    wpgmza_open_marker_func(map_id,marker,html,click_from_list,marker_data_object,wpmgza_marker_id,val);
                                }); 
                            }
                            marker_array[map_id][wpmgza_marker_id] = marker;
                            marker_array[map_id][wpmgza_marker_id].default_icon = marker.icon;

                            google.maps.event.addListener(infoWindow[wpmgza_marker_id], 'closeclick', function(evt) {   
                                if (typeof marker_array[map_id][wpmgza_marker_id].icon !== "undefined" && marker_array[map_id][wpmgza_marker_id].icon !== "") {
                                    marker_array[map_id][wpmgza_marker_id].setIcon(marker_array[map_id][wpmgza_marker_id].default_icon);
                                } else {
                                    marker_array[map_id][wpmgza_marker_id].setIcon(null);
                                }
                            });                            
                         
                            
                            
                            marker_array2[map_id].push(marker);
                            marker_sl_array[map_id].push(wpmgza_marker_id);

                            

                        }
                    } 

                    
                });

                if (typeof wpgm_g_e !== "undefined" && wpgm_g_e === '1') {

                    if (wpgmaps_localize[map_id]['mass_marker_support'] === "1" || wpgmaps_localize[map_id]['mass_marker_support'] === null) { 
                        if (typeof markerClusterer[map_id] !== "undefined") { markerClusterer[map_id].addMarkers(marker_array2[map_id]); }
                    }
                }

                if (radius !== null) {
                    wpgmza_filter_marker_lists_by_array(map_id,marker_sl_array[map_id]);
                }
            }
        }
        if (wpgmaps_localize[entry]['show_user_location'] === "1") {
            if(navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    user_location = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);

                    if ("undefined" !== typeof wpgmaps_localize[map_id]['other_settings']['upload_default_ul_marker']) { 
                        var marker = new google.maps.Marker({
                              position: user_location,
                              map: MYMAP[map_id].map,
                              icon: wpgmaps_localize[map_id]['other_settings']['upload_default_ul_marker'],
                              animation: google.maps.Animation.DROP
                        });    
                    } else {
                        var marker = new google.maps.Marker({
                              position: user_location,
                              map: MYMAP[map_id].map,
                              animation: google.maps.Animation.DROP
                        });    
                    } 
                    var wpmgza_marker_id = marker_array[map_id].length + 1;
                    infoWindow[wpmgza_marker_id] = new google.maps.InfoWindow();
                    google.maps.event.addListener(marker, 'click', function(evt) {
                          clearLocations();
                          
                          infoWindow[wpmgza_marker_id].setContent(wpgmaps_lang_my_location);
                          infoWindow[wpmgza_marker_id].open(MYMAP[wpgmaps_localize[entry]['id']].map, marker);
                      });

                    marker_array[map_id][wpmgza_marker_id] = marker;

                });
             } else {
              /* Browser doesn't support Geolocation */
            }       
        }

        /**
         * Identify if we need to focus on a specific LAT and LNG (focused marker)
         */
		if (focus_lat !== false && focus_lng !== false && !focused_on_lat_lng) {
			var point = new google.maps.LatLng(parseFloat(focus_lat),parseFloat(focus_lng));
			MYMAP[map_id].map.setCenter(point);
			focused_on_lat_lng = true;
		}

		var controller;
		if(controller = WPGMZA.CustomFieldFilterController.controllersByMapID[map_id])
			controller.reapplyLastResponse();
    };
    
    function wpgmza_open_marker_func(map_id,marker,html,click_from_list,marker_data,wpmgza_marker_id,val) {
        jQuery('.wpgmza_modern_infowindow').show();
        jQuery('.wpgmza_modern_infowindow').css('display', 'block');

        if (typeof val.other_data !== "undefined" && typeof val.other_data.icon_on_click !== "undefined" && val.other_data.icon_on_click !== "") {

            marker.setIcon(val.other_data.icon_on_click);
        }



        if ((typeof wpgmaps_localize_global_settings['wpgmza_iw_type'] !== 'undefined' && parseInt(wpgmaps_localize_global_settings['wpgmza_iw_type']) >= 1) || (typeof wpgmaps_localize[map_id]['other_settings']['wpgmza_iw_type'] !== "undefined" && parseInt(wpgmaps_localize[map_id]['other_settings']['wpgmza_iw_type']) >= 1)) {

            wpgmza_create_new_iw_window(map_id);
            /* set the variable to "open" */
            modern_iw_open[map_id] = true;
            

            /* see if the DOM element is there */
            if (modern_iw_open[map_id]) {
               
            } else {

            }

            /* reset the elements */
            jQuery("#wpgmza_iw_holder_"+map_id+" .wpgmza_iw_marker_image").attr("src",""); 
            jQuery("#wpgmza_iw_holder_"+map_id+" .wpgmza_iw_title").html(""); 
            jQuery("#wpgmza_iw_holder_"+map_id+" .wpgmza_iw_description").html(""); 
            jQuery("#wpgmza_iw_holder_"+map_id+" .wpgmza_iw_address_p").html(""); 


            jQuery("#wpgmza_iw_holder_"+map_id+" .wpgmza_more_info_button").attr("href","#"); 
            jQuery("#wpgmza_iw_holder_"+map_id+" .wpgmza_more_info_button").attr("target",""); 
            jQuery("#wpgmza_iw_holder_"+map_id+" .wpgmza_directions_button").attr("gps",""); 
            jQuery("#wpgmza_iw_holder_"+map_id+" .wpgmza_directions_button").attr("href","#"); 
            jQuery("#wpgmza_iw_holder_"+map_id+" .wpgmza_directions_button").attr("id",""); 
            jQuery("#wpgmza_iw_holder_"+map_id+" .wpgmza_directions_button").attr("wpgm_addr_field",""); 

            
            
            if (marker_data.image === "" && marker_data.title === "") {  
                jQuery("#wpgmza_iw_holder_"+map_id+" .wpgmza_iw_image").css("display","none"); 
            } else {
                jQuery("#wpgmza_iw_holder_"+map_id+" .wpgmza_iw_image").css("display","block"); 
            }


            if (marker_data.image !== "") { 
                jQuery("#wpgmza_iw_holder_"+map_id+" .wpgmza_iw_marker_image").css("display","block"); 
                jQuery("#wpgmza_iw_holder_"+map_id+" .wpgmza_iw_marker_image").attr("src",marker_data.image); 
                jQuery("#wpgmza_iw_holder_"+map_id+" .wpgmza_iw_title").attr("style","position: absolute !important"); 
                if (marker_data.title !== "") { jQuery("#wpgmza_iw_holder_"+map_id+" .wpgmza_iw_title").html(marker_data.title); }

            } else {
                jQuery("#wpgmza_iw_holder_"+map_id+" .wpgmza_iw_marker_image").css("display","none"); 
                jQuery("#wpgmza_iw_holder_"+map_id+" .wpgmza_iw_title").attr("style","position: relative !important"); 
                if (marker_data.title !== "") { jQuery("#wpgmza_iw_holder_"+map_id+" .wpgmza_iw_title").html(marker_data.title); }
            }

            if (marker_data.desc !== "") { 
                jQuery("#wpgmza_iw_holder_"+map_id+" .wpgmza_iw_description").css("display","block"); 
                if (typeof marker_data.desc !== "undefined" && marker_data.desc !== "") { jQuery("#wpgmza_iw_holder_"+map_id+" .wpgmza_iw_description").html(marker_data.desc); }
            } else {
                jQuery("#wpgmza_iw_holder_"+map_id+" .wpgmza_iw_description").css("display","none"); 

            }

            
            if (typeof wpgmaps_localize_global_settings['wpgmza_settings_infowindow_address'] !== 'undefined' && wpgmaps_localize_global_settings['wpgmza_settings_infowindow_address'] === "yes") {
            } else {
                if (typeof marker_data.address !== "undefined" && marker_data.address !== "") { jQuery("#wpgmza_iw_holder_"+map_id+" .wpgmza_iw_address_p").html(marker_data.address); }
            }
            

            if (typeof marker_data.link !== "undefined" && marker_data.link !== "") { 
                jQuery("#wpgmza_iw_holder_"+map_id+" .wpgmza_more_info_button").show();
                jQuery("#wpgmza_iw_holder_"+map_id+" .wpgmza_more_info_button").attr("href",marker_data.link);
                if (marker_data.link_target !== "") {
                    jQuery("#wpgmza_iw_holder_"+map_id+" .wpgmza_more_info_button").attr("target","_BLANK"); 
                }  
            } else {
                jQuery("#wpgmza_iw_holder_"+map_id+" .wpgmza_more_info_button").hide();
            }
            if (typeof marker_data.directions !== "undefined" && marker_data.directions !== "") { 
                jQuery("#wpgmza_iw_holder_"+map_id+" .wpgmza_directions_button").show();
                jQuery("#wpgmza_iw_holder_"+map_id+" .wpgmza_directions_button").attr("href","javascript:void(0);"); 
                jQuery("#wpgmza_iw_holder_"+map_id+" .wpgmza_directions_button").attr("gps",marker_data.gps); 
                jQuery("#wpgmza_iw_holder_"+map_id+" .wpgmza_directions_button").attr("wpgm_addr_field",marker_data.address); 
                jQuery("#wpgmza_iw_holder_"+map_id+" .wpgmza_directions_button").attr("id",map_id); 
                jQuery("#wpgmza_iw_holder_"+map_id+" .wpgmza_directions_button").addClass("wpgmza_gd"); 

            } else {
                jQuery("#wpgmza_iw_holder_"+map_id+" .wpgmza_directions_button").hide();
            }

            if (click_from_list) {
                MYMAP[map_id].map.panTo(marker.position);
                MYMAP[map_id].map.setZoom(13);
            } else {
	            if (MYMAP[map_id].markerListing instanceof WPGMZA.ModernMarkerListing) {
		            MYMAP[map_id].markerListing.markerView.open(wpmgza_marker_id);
	            }
            }
            click_from_list = false;

        } else {
            clearLocations();
            if (wpgmaps_localize_global_settings['wpgmza_settings_infowindow_width']) { 
                infoWindow[wpmgza_marker_id].setOptions({maxWidth:wpgmaps_localize_global_settings['wpgmza_settings_infowindow_width']});
            }
            infoWindow[wpmgza_marker_id].setContent(html);
            if (click_from_list) {
                MYMAP[map_id].map.panTo(marker.position);
                MYMAP[map_id].map.setZoom(13);
            } else {
	            if (MYMAP[map_id].markerListing instanceof WPGMZA.ModernMarkerListing) {
		            MYMAP[map_id].markerListing.markerView.open(wpmgza_marker_id);
	            }
            }
            click_from_list = false;

			if(wpgmaps_localize[map_id].other_settings && wpgmaps_localize[map_id].other_settings.list_markers_by != 6)
				infoWindow[wpmgza_marker_id].open(MYMAP[map_id].map, marker);	
            
        }

        
        
    }
    
    function wpgmza_create_new_iw_window(mapid) {
		
		if(wpgmaps_localize_global_settings.wpgmza_settings_disable_infowindows)
			return;
		
        /* handle new modern infowindow */
        if ((typeof wpgmaps_localize_global_settings['wpgmza_iw_type'] !== 'undefined' && parseInt(wpgmaps_localize_global_settings['wpgmza_iw_type']) >= 1) || (typeof wpgmaps_localize[mapid]['other_settings']['wpgmza_iw_type'] !== "undefined" && parseInt(wpgmaps_localize[mapid]['other_settings']['wpgmza_iw_type']) >= 1)) {
                if (typeof document.getElementById('wpgmza_iw_holder_'+mapid) !== "undefined") {
                    
                    if (wpgmaps_localize_global_settings['wpgmza_settings_infowindow_width'] === "" || 'undefined' === typeof wpgmaps_localize_global_settings['wpgmza_settings_infowindow_width']) {
                        wpgmaps_localize_global_settings['wpgmza_settings_infowindow_width'] = false;
                    }
                    
                    var legend = document.getElementById('wpgmza_iw_holder_'+mapid);
                    if (legend !== null) {
                        jQuery(legend).remove();
                    }

                    wpgmza_iw_Div[mapid] = document.createElement('div');
                    wpgmza_iw_Div[mapid].id = 'wpgmza_iw_holder_'+mapid;
                    wpgmza_iw_Div[mapid].style = 'display:block;';
                    document.getElementsByTagName('body')[0].appendChild(wpgmza_iw_Div[mapid]);

                    wpgmza_iw_Div_inner = document.createElement('div');
                    wpgmza_iw_Div_inner.className = 'wpgmza_modern_infowindow_inner wpgmza_modern_infowindow_inner_'+mapid;
                    wpgmza_iw_Div[mapid].appendChild(wpgmza_iw_Div_inner);

                    wpgmza_iw_Div_close = document.createElement('div');
                    wpgmza_iw_Div_close.className = 'wpgmza_modern_infowindow_close';
                    wpgmza_iw_Div_close.setAttribute('mid',mapid);

                    var t = document.createTextNode("x");
                    wpgmza_iw_Div_close.appendChild(t); 
                    wpgmza_iw_Div_inner.appendChild(wpgmza_iw_Div_close);

                    wpgmza_iw_Div_img = document.createElement('div');
                    wpgmza_iw_Div_img.className = 'wpgmza_iw_image';
                    wpgmza_iw_Div_inner.appendChild(wpgmza_iw_Div_img);

                    wpgmza_iw_img = document.createElement('img');
                    wpgmza_iw_img.className = 'wpgmza_iw_marker_image';
                    wpgmza_iw_img.src = '';
                    wpgmza_iw_img.style = 'max-width:100%;';
                    wpgmza_iw_Div_img.appendChild(wpgmza_iw_img);

                    wpgmza_iw_img_div = document.createElement('div');
                    wpgmza_iw_img_div.className = 'wpgmza_iw_title';
                    wpgmza_iw_Div_inner.appendChild(wpgmza_iw_img_div);

                    wpgmza_iw_img_div_p = document.createElement('p');
                    wpgmza_iw_img_div_p.className = 'wpgmza_iw_title_p';
                    wpgmza_iw_img_div.appendChild(wpgmza_iw_img_div_p);

                    wpgmza_iw_address_div = document.createElement('div');
                    wpgmza_iw_address_div.className = 'wpgmza_iw_address';
                    wpgmza_iw_Div_inner.appendChild(wpgmza_iw_address_div);

                    wpgmza_iw_address_p = document.createElement('p');
                    wpgmza_iw_address_p.className = 'wpgmza_iw_address_p';
                    wpgmza_iw_address_div.appendChild(wpgmza_iw_address_p);

                    wpgmza_iw_description = document.createElement('div');
                    wpgmza_iw_description.className = 'wpgmza_iw_description';
                    wpgmza_iw_Div_inner.appendChild(wpgmza_iw_description);

                    wpgmza_iw_description_p = document.createElement('p');
                    wpgmza_iw_description_p.className = 'wpgmza_iw_description_p';
                    wpgmza_iw_description.appendChild(wpgmza_iw_description_p);


                    wpgmza_iw_buttons = document.createElement('div');
                    wpgmza_iw_buttons.className = 'wpgmza_iw_buttons';
                    wpgmza_iw_Div_inner.appendChild(wpgmza_iw_buttons);

                    wpgmza_directions_button = document.createElement('a');
                    wpgmza_directions_button.className = 'wpgmza_button wpgmza_left wpgmza_directions_button';
                    wpgmza_directions_button.src = '#';
                    var t = document.createTextNode(wpgmaps_lang_directions);
                    wpgmza_directions_button.appendChild(t); 

                    wpgmza_iw_buttons.appendChild(wpgmza_directions_button);


                    wpgmza_more_info_button = document.createElement('a');
                    wpgmza_more_info_button.className = 'wpgmza_button wpgmza_right wpgmza_more_info_button';
                    wpgmza_more_info_button.src = '#';
                    var t = document.createTextNode(wpgmaps_lang_more_info);
                    wpgmza_more_info_button.appendChild(t); 

                    wpgmza_iw_buttons.appendChild(wpgmza_more_info_button);


                    var legend = document.getElementById('wpgmza_iw_holder_'+mapid);

                    jQuery(legend).css('display','block');
                    jQuery(legend).addClass('wpgmza_modern_infowindow');
                    /* jQuery(legend).css('width',wpgmaps_localize_global_settings['wpgmza_settings_infowindow_width']+'px'); */
                    jQuery(legend).addClass('wpgmza-shadow');
                    MYMAP[mapid].map.controls[google.maps.ControlPosition.RIGHT_TOP].push(legend);
                }
                
        }
    }
    
function add_heatmap(mapid,datasetid) {

        var tmp_data = wpgmaps_localize_heatmap_settings[mapid][datasetid];
        var current_poly_id = datasetid;
        var tmp_polydata = tmp_data['polydata'];
        var WPGM_PathData = new Array();
        for (tmp_entry2 in tmp_polydata) {
             if (typeof tmp_polydata[tmp_entry2][0] !== "undefined") {
                
                WPGM_PathData.push(new google.maps.LatLng(tmp_polydata[tmp_entry2][0], tmp_polydata[tmp_entry2][1]));
            }
         }
         if (tmp_data['radius'] === null || tmp_data['radius'] === "") { tmp_data['radius'] = 20; }
         if (tmp_data['gradient'] === null || tmp_data['gradient'] === "") { tmp_data['gradient'] = null; }
         if (tmp_data['opacity'] === null || tmp_data['opacity'] === "") { tmp_data['opacity'] = 0.6; }
         
         var bounds = new google.maps.LatLngBounds();
         for (i = 0; i < WPGM_PathData.length; i++) {
           bounds.extend(WPGM_PathData[i]);
         }

        WPGM_Path_Polygon[datasetid] = new google.maps.visualization.HeatmapLayer({
             data: WPGM_PathData,
             map: MYMAP[mapid].map
        });

       WPGM_Path_Polygon[datasetid].setMap(MYMAP[mapid].map);
       var gradient = JSON.parse(tmp_data['gradient']);
       WPGM_Path_Polygon[datasetid].set('radius', tmp_data['radius']);
       WPGM_Path_Polygon[datasetid].set('opacity', tmp_data['opacity']);
       WPGM_Path_Polygon[datasetid].set('gradient', gradient);


       polygon_center = bounds.getCenter();



}

    function add_polygon(mapid,polygonid) {
        var tmp_data = wpgmaps_localize_polygon_settings[mapid][polygonid];
         var current_poly_id = polygonid;
         var tmp_polydata = tmp_data['polydata'];
         var WPGM_PathData = new Array();
         for (tmp_entry2 in tmp_polydata) {
             if (typeof tmp_polydata[tmp_entry2][0] !== "undefined") {
                
                WPGM_PathData.push(new google.maps.LatLng(tmp_polydata[tmp_entry2][0], tmp_polydata[tmp_entry2][1]));
            }
         }
         if (tmp_data['lineopacity'] === null || tmp_data['lineopacity'] === "") {
             tmp_data['lineopacity'] = 1;
         }
         
         var bounds = new google.maps.LatLngBounds();
         for (i = 0; i < WPGM_PathData.length; i++) {
           bounds.extend(WPGM_PathData[i]);
         }

        WPGM_Path_Polygon[polygonid] = new google.maps.Polygon({
             path: WPGM_PathData,
             clickable: true, /* must add option for this */ 
             strokeColor: "#"+tmp_data['linecolor'],
             fillOpacity: tmp_data['opacity'],
             strokeOpacity: tmp_data['lineopacity'],
             fillColor: "#"+tmp_data['fillcolor'],
             strokeWeight: 2,
             map: MYMAP[mapid].map
       });
       WPGM_Path_Polygon[polygonid].setMap(MYMAP[mapid].map);

        polygon_center = bounds.getCenter();

        if (tmp_data['title'] !== "") {
         infoWindow_poly[polygonid] = new google.maps.InfoWindow();
         google.maps.event.addListener(WPGM_Path_Polygon[polygonid], 'click', function(event) {
             infoWindow_poly[polygonid].setPosition(event.latLng);
             content = "";
             if (tmp_data['link'] !== "") {
                 var content = "<a href='"+tmp_data['link']+"'>"+tmp_data['title']+"</a>";
             } else {
                 var content = tmp_data['title'];
             }
             infoWindow_poly[polygonid].setContent(content);
             infoWindow_poly[polygonid].open(MYMAP[mapid].map,this.position);
         }); 
        }


       google.maps.event.addListener(WPGM_Path_Polygon[polygonid], "mouseover", function(event) {
             this.setOptions({fillColor: "#"+tmp_data['ohfillcolor']});
             this.setOptions({fillOpacity: tmp_data['ohopacity']});
             this.setOptions({strokeColor: "#"+tmp_data['ohlinecolor']});
             this.setOptions({strokeWeight: 2});
             this.setOptions({strokeOpacity: 0.9});
       });
       google.maps.event.addListener(WPGM_Path_Polygon[polygonid], "click", function(event) {

             this.setOptions({fillColor: "#"+tmp_data['ohfillcolor']});
             this.setOptions({fillOpacity: tmp_data['ohopacity']});
             this.setOptions({strokeColor: "#"+tmp_data['ohlinecolor']});
             this.setOptions({strokeWeight: 2});
             this.setOptions({strokeOpacity: 0.9});
       });
       google.maps.event.addListener(WPGM_Path_Polygon[polygonid], "mouseout", function(event) {
             this.setOptions({fillColor: "#"+tmp_data['fillcolor']});
             this.setOptions({fillOpacity: tmp_data['opacity']});
             this.setOptions({strokeColor: "#"+tmp_data['linecolor']});
             this.setOptions({strokeWeight: 2});
             this.setOptions({strokeOpacity: tmp_data['lineopacity']});
       });


           
        
        
    }
    function add_polyline(mapid,polyline) {
        
        
        var tmp_data = wpgmaps_localize_polyline_settings[mapid][polyline];

        var current_poly_id = polyline;
        var tmp_polydata = tmp_data['polydata'];
        var WPGM_Polyline_PathData = new Array();
        for (tmp_entry2 in tmp_polydata) {
            if (typeof tmp_polydata[tmp_entry2][0] !== "undefined" && typeof tmp_polydata[tmp_entry2][1] !== "undefined") {
                var lat = tmp_polydata[tmp_entry2][0].replace(')', '');
                lat = lat.replace('(','');
                var lng = tmp_polydata[tmp_entry2][1].replace(')', '');
                lng = lng.replace('(','');
                WPGM_Polyline_PathData.push(new google.maps.LatLng(lat, lng));
            }
             
             
        }
         if (tmp_data['lineopacity'] === null || tmp_data['lineopacity'] === "") {
             tmp_data['lineopacity'] = 1;
         }

        WPGM_Path[polyline] = new google.maps.Polyline({
             path: WPGM_Polyline_PathData,
             strokeColor: "#"+tmp_data['linecolor'],
             strokeOpacity: tmp_data['opacity'],
             strokeWeight: tmp_data['linethickness'],
             map: MYMAP[mapid].map
       });
       WPGM_Path[polyline].setMap(MYMAP[mapid].map);
        
        
    }
	
	function add_circle(mapid, data)
	{
		data.map = MYMAP[mapid].map;
		
		if(!(data.center instanceof google.maps.LatLng)) {
			var m = data.center.match(/-?\d+(\.\d*)?/g);
			data.center = new google.maps.LatLng({
				lat: parseFloat(m[0]),
				lng: parseFloat(m[1]),
			});
		}
		
		data.radius = parseFloat(data.radius);
		data.fillColor = data.color;
		data.fillOpacity = parseFloat(data.opacity);
		
		data.strokeOpacity = 0;
		
		var circle = new google.maps.Circle(data);
		circle_array.push(circle);
	}
    
	function add_rectangle(mapid, data)
	{
		data.map = MYMAP[mapid].map;
		
		data.fillColor = data.color;
		data.fillOpacity = parseFloat(data.opacity);
		
		var northWest = data.cornerA;
		var southEast = data.cornerB;
		
		var m = northWest.match(/-?\d+(\.\d+)?/g);
		var north = parseFloat(m[0]);
		var west = parseFloat(m[1]);
		
		m = southEast.match(/-?\d+(\.\d+)?/g);
		var south = parseFloat(m[0]);
		var east = parseFloat(m[1]);
		
		data.bounds = {
			north: north,
			west: west,
			south: south,
			east: east
		};
		
		data.strokeOpacity = 0;
		
		var rectangle = new google.maps.Rectangle(data);
		rectangle_array.push(rectangle);
	}
    

}






});




function openInfoWindow(marker_id,map_id,by_list) {
    if (by_list) {
        click_from_list = true;
    } else {
        click_from_list = false;
    }

    if (wpgmaps_localize_global_settings['wpgmza_settings_map_open_marker_by'] === "" || 'undefined' === typeof wpgmaps_localize_global_settings['wpgmza_settings_map_open_marker_by'] || wpgmaps_localize_global_settings['wpgmza_settings_map_open_marker_by'] === '1') { 
        google.maps.event.trigger(marker_array[map_id][marker_id], 'click');
    } else {
        google.maps.event.trigger(marker_array[map_id][marker_id], 'mouseover');
    }
    click_from_list = false;
}






function calcRoute(start,end,mapid,travelmode,avoidtolls,avoidhighways,avoidferries,waypoints) {

	var mapElement = jQuery("#wpgmza_map_" + mapid);
 
    var request = {
        origin:start,
        destination:end,
        provideRouteAlternatives: true,
        travelMode: google.maps.DirectionsTravelMode[travelmode],
        avoidHighways: avoidhighways,
        avoidTolls: avoidtolls,
        avoidTolls: avoidferries
    };

    if(typeof waypoints !== "undefined"){
        var waypoint_array = waypoints.split("|"); //Split by pipe
        for(var i in waypoint_array){
            var the_loc = waypoint_array[i];
            waypoint_array[i] = {
                'location' : the_loc,
                'stopover' : false
            };
        }
        request['waypoints'] = waypoint_array;
    }

    dirflg = "c";

    if (travelmode === "DRIVING") { dirflg = "d"; }
    else if (travelmode === "WALKING") { dirflg = "w"; }
    else if (travelmode === "BICYCLING") { dirflg = "b"; }
    else if (travelmode === "TRANSIT") { dirflg = "t"; }
    else { dirflg = "c"; }

    directionsService[mapid] = new google.maps.DirectionsService();
    var currentDirections = null;
    var oldDirections = [];

    jQuery("#wpgmza_input_to_"+mapid).css("border","");
    jQuery("#wpgmza_input_from_"+mapid).css("border","");
    jQuery("#wpgmaps_directions_notification_"+mapid).html(orig_fetching_directions);

    directionsDisplay[mapid] = new google.maps.DirectionsRenderer({
         'map': MYMAP[mapid].map,
         'preserveViewport': true,
         'draggable': true
     });
    directionsDisplay[mapid].setPanel(document.getElementById("directions_panel_"+mapid));
    
    
    google.maps.event.addListener(directionsDisplay[mapid], 'directions_changed',
      function() {
          if (currentDirections) {
              oldDirections.push(currentDirections);
          }
          currentDirections = directionsDisplay[mapid].getDirections();
          jQuery("#directions_panel_"+mapid).show();
          jQuery("#wpgmaps_directions_notification_"+mapid).hide();
          jQuery("#wpgmaps_directions_reset_"+mapid).show();
      });


    directionsService[mapid].route(request, function(response, status) {
        if (status === google.maps.DirectionsStatus.OK) {
            directionsDisplay[mapid].setDirections(response);
        } else if (status === "ZERO_RESULTS") {
            jQuery("#wpgmaps_directions_editbox_"+mapid).show("fast");
            wpgmza_reset_directions(mapid);
            jQuery("#wpgmaps_directions_notification_"+mapid).show();
            jQuery("#wpgmaps_directions_notification_"+mapid).html("No results found.");

        } else if (status === "NOT_FOUND") {
            jQuery("#wpgmaps_directions_editbox_"+mapid).show("fast");
            wpgmza_reset_directions(mapid);
            jQuery("#wpgmaps_directions_notification_"+mapid).show();
            jQuery("#wpgmaps_directions_notification_"+mapid).html("No results found.");
            if (typeof response.geocoded_waypoints[0] !== "undefined" && typeof response.geocoded_waypoints[0].geocoder_status !== "undefined" && response.geocoded_waypoints[0].geocoder_status == "ZERO_RESULTS") {
                jQuery("#wpgmza_input_from_"+mapid).css("border","1px solid red");
            }
            if (typeof response.geocoded_waypoints[1] !== "undefined" && typeof response.geocoded_waypoints[1].geocoder_status !== "undefined" && response.geocoded_waypoints[1].geocoder_status == "ZERO_RESULTS") {
                jQuery("#wpgmza_input_to_"+mapid).css("border","1px solid red");
            }

        }
		
		if(MYMAP[mapid].directionsBox)
			jQuery(mapElement).trigger("directionsserviceresult", [response, status]);
    });

    jQuery("#wpgmaps_print_directions_"+mapid).attr('href','https://maps.google.com/maps?saddr='+encodeURIComponent(start)+'&daddr='+encodeURIComponent(end)+'&dirflg='+dirflg+'&om=1');
	
	

}

function wpgmza_show_options(wpgmzamid) {

      jQuery("#wpgmza_options_box_"+wpgmzamid).show();
      jQuery("#wpgmza_show_options_"+wpgmzamid).hide();
      jQuery("#wpgmza_hide_options_"+wpgmzamid).show();
  }
function wpgmza_hide_options(wpgmzamid) {
      jQuery("#wpgmza_options_box_"+wpgmzamid).hide();
      jQuery("#wpgmza_show_options_"+wpgmzamid).show();
      jQuery("#wpgmza_hide_options_"+wpgmzamid).hide();
  }
function wpgmza_reset_directions(wpgmzamid) {
    currentDirections = null;
    directionsDisplay[wpgmzamid].setMap(null);
    var currentDirections = null;

    jQuery("#wpgmaps_directions_editbox_"+wpgmzamid).show();
    jQuery("#directions_panel_"+wpgmzamid).hide();
    jQuery("#directions_panel_"+wpgmzamid).html('');
    jQuery("#wpgmaps_directions_notification_"+wpgmzamid).hide();
    jQuery("#wpgmaps_directions_reset_"+wpgmzamid).hide();
    jQuery("#wpgmaps_directions_notification_"+wpgmzamid).html(orig_fetching_directions);
  }

jQuery("body").on("click", ".wpgmza_gd", function() {

    var wpgmzamid = jQuery(this).attr("id");
    var end = jQuery(this).attr("wpgm_addr_field");
    var latLong = jQuery(this).attr("gps");
    /* pelicanpaul updates for mobile */
    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
        
        if( (navigator.platform.indexOf("iPhone") != -1)
        || (navigator.platform.indexOf("iPod") != -1)
        || (navigator.platform.indexOf("iPad") != -1))
        window.open("http://maps.apple.com/maps?daddr="+latLong+"&ll=");
        else
        window.open("http://maps.google.com/maps?daddr="+latLong+"&ll=");
    } else {

        jQuery("#wpgmaps_directions_edit_"+wpgmzamid).show();
        jQuery("#wpgmaps_directions_editbox_"+wpgmzamid).show();
        jQuery("#wpgmza_input_to_"+wpgmzamid).val(end.length > 0 ? end : latLong);
        jQuery("#wpgmza_input_from_"+wpgmzamid).focus().select();
    }


});

jQuery("body").on("click", ".wpgmaps_get_directions", function(event) {

	var wpgmzamid = jQuery(this).attr("id");

	var avoidtolls = jQuery('#wpgmza_tolls_'+wpgmzamid).is(':checked');
	var avoidhighways = jQuery('#wpgmza_highways_'+wpgmzamid).is(':checked');
	var avoidferries = jQuery('#wpgmza_ferries_'+wpgmzamid).is(':checked');

	var wpgmza_dir_type = jQuery("#wpgmza_dir_type_"+wpgmzamid).val();
	var wpgmaps_from = jQuery("#wpgmza_input_from_"+wpgmzamid).val();
	var wpgmaps_to = jQuery("#wpgmza_input_to_"+wpgmzamid).val();

	var wpgmaps_waypoints = jQuery("#wpgmza_input_waypoints_"+wpgmzamid).val();
	
	var waypoint_elements = jQuery("#wpgmaps_directions_edit_" + wpgmzamid + " input.wpgmaps_via");
	if(waypoint_elements.length)
	{
		var values = [];
		waypoint_elements.each(function(index, el) {
			values.push(jQuery(el).val());
		});
		wpgmaps_waypoints = values.join("|");
	}

	if (wpgmaps_from === "" || wpgmaps_to === "")
	{
		alert(wpgmaps_lang_error1);
	}
	else
	{
		calcRoute(wpgmaps_from,wpgmaps_to,wpgmzamid,wpgmza_dir_type,avoidtolls,avoidhighways,avoidferries,wpgmaps_waypoints);
		
		if(jQuery(event.target).closest(".wpgmza-modern-directions-box").length)
			return;
		
		jQuery("#wpgmaps_directions_editbox_"+wpgmzamid).hide("slow");
		jQuery("#wpgmaps_directions_notification_"+wpgmzamid).show("slow");
	}
	
});



jQuery("body").on("keypress",".addressInput", function(event) {
  if ( event.which == 13 ) {
    var mid = jQuery(this).attr("mid");
     jQuery('.wpgmza_sl_search_button_'+mid).trigger('click');
  }
});

jQuery('body').on('click', '.wpgmza_modern_infowindow_close', function(){
    var mid = jQuery(this).attr('mid');
    jQuery("#wpgmza_iw_holder_"+mid).remove();


});

if(!window.WPGMZA)
	window.WPGMZA = {};

(function($) {
	
	WPGMZA.KM_PER_MILE = 1.60934;
	WPGMZA.MILE_PER_KM = 0.621371;

	WPGMZA.UNITS_MILES = 1;
	WPGMZA.UNITS_KM = 2;
	
	WPGMZA.hexToRgba = function(hex) {
		var c;
		if(/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)){
			c= hex.substring(1).split('');
			if(c.length== 3){
				c= [c[0], c[0], c[1], c[1], c[2], c[2]];
			}
			c= '0x'+c.join('');
			
			return {
				r: (c>>16)&255,
				g: (c>>8)&255,
				b: c&255,
				a: 1
			};
		}
		
		return 0;
		
		//throw new Error('Bad Hex');
	};
	
	WPGMZA.rgbaToString = function(rgba) {
		return "rgba(" + rgba.r + ", " + rgba.g + ", " + rgba.b + ", " + rgba.a + ")";
	};
	
	/**
	 * The new modern look store locator. It takes the elements
	 * from the default look and moves them into the map, wrapping
	 * in a new element so we can apply new styles.
	 * @return Object
	 */
	WPGMZA.ModernStoreLocator = function(map_id) {
		var self = this;
		
		var original = $(".wpgmza_sl_search_button[mid='" + map_id + "']").closest(".wpgmza_sl_main_div");
		
		if(!original.length)
			return;
		
		// Build / re-arrange elements
		this.element = $("<div class='wpgmza-modern-store-locator'><div class='wpgmza-inner wpgmza-modern-hover-opaque'/></div>")[0];
		
		var inner = $(this.element).find(".wpgmza-inner");
		
		MYMAP[map_id].map.controls[google.maps.ControlPosition.TOP_CENTER].push(this.element);
		
		var titleSearch = $(original).find("[id='nameInput_" + map_id + "']");
		if(titleSearch.length)
		{
			var placeholder = wpgmaps_localize[map_id].other_settings.store_locator_name_string;
			if(placeholder && placeholder.length)
				titleSearch.attr("placeholder", placeholder);
			inner.append(titleSearch);
		}
		
		var addressInput = $(original).find(".addressInput");
		
		if(wpgmaps_localize[map_id].other_settings.store_locator_query_string && wpgmaps_localize[map_id].other_settings.store_locator_query_string.length)
			addressInput.attr("placeholder", wpgmaps_localize[map_id].other_settings.store_locator_query_string);
		
		inner.append(addressInput);
		
		
		inner.append($(original).find("select.wpgmza_sl_radius_select"));
		// inner.append($(original).find(".wpgmza_filter_select_" + map_id));
		
		// Buttons
		this.searchButton = $(original).find( ".wpgmza_sl_search_button" );
		inner.append(this.searchButton);
		
		this.resetButton = $(original).find( ".wpgmza_sl_reset_button_div" );
		inner.append(this.resetButton);
		
		this.resetButton.hide();
		
		this.searchButton.on("click", function(event) {
			if($("addressInput_" + map_id).val() == 0)
				return;
			
			self.searchButton.hide();
			self.resetButton.show();
		});
		this.resetButton.on("click", function(event) {
			self.resetButton.hide();
			self.searchButton.show();
		});
		
		// Distance type
		inner.append($("#wpgmza_distance_type_" + map_id));
		
		// Categories
		var container = $(original).find(".wpgmza_cat_checkbox_holder");
		var ul = $(container).children("ul");
		var items = $(container).find("li");
		var numCategories = 0;
		
		//$(items).find("ul").remove();
		//$(ul).append(items);
		
		var icons = [];
		
		items.each(function(index, el) {
			var id = $(el).attr("class").match(/\d+/);
			
			for(var category_id in wpgmza_category_data) {
				
				if(id == category_id) {
					var src = wpgmza_category_data[category_id].image;
					var icon = $('<div class="wpgmza-chip-icon"/>');
					
					icon.css({
						"background-image": "url('" + src + "')",
						"width": $("#wpgmza_cat_checkbox_" + category_id + " + label").height() + "px"
					});
					icons.push(icon);
					
                    if(src != null && src != ""){
					   //$(el).find("label").prepend(icon);
                       $("#wpgmza_cat_checkbox_" + category_id + " + label").prepend(icon);
                    }
					
					numCategories++;
					
					break;
				}
				
			}
		});

        $(this.element).append(container);

		
		if(numCategories) {
			this.optionsButton = $('<span class="wpgmza_store_locator_options_button"><i class="fas fa-list"></i></span>');
			$(this.searchButton).before(this.optionsButton);
		}
		
		setInterval(function() {
			
			icons.forEach(function(icon) {
				var height = $(icon).height();
				$(icon).css({"width": height + "px"});
				$(icon).closest("label").css({"padding-left": height + 8 + "px"});
			});
			
			$(container).css("width", $(self.element).find(".wpgmza-inner").outerWidth() + "px");
			
		}, 1000);
		
		$(this.element).find(".wpgmza_store_locator_options_button").on("click", function(event) {
			
			if(container.hasClass("wpgmza-open"))
				container.removeClass("wpgmza-open");
			else
				container.addClass("wpgmza-open");
			
		});
		
		// Remove original element
		$(original).remove();
		
		// Event listeners
		$(this.element).find("input, select").on("focus", function() {
			$(inner).addClass("active");
		});
		
		$(this.element).find("input, select").on("blur", function() {
			$(inner).removeClass("active");
		});
	};
	
	/**
	 * This icon will appear as an apple on Apple system and as
	 * the Google logo on other systems, based on the user agent
	 * @return Object
	 */
	WPGMZA.NativeMapsAppIcon = function() {
		if(navigator.userAgent.match(/^Apple|iPhone|iPad|iPod/))
		{
			this.type = "apple";
			this.element = $('<span><i class="fab fa-apple" aria-hidden="true"></i></span>');
		}
		else
		{
			this.type = "google";
			this.element = $('<span><i class="fab fa-google" aria-hidden="true"></i></span>');
		}
	};
	
	/**
	 * Common functionality for popout panels, which is the
	 * directions box, directions result box, and the modern
	 * style marker listing
	 * @return Object
	 */
	WPGMZA.PopoutPanel = function()
	{
		
	};
	
	/**
	 * Opens the direction box
	 * @return void
	 */
	WPGMZA.PopoutPanel.prototype.open = function() {
		$(this.element).addClass("wpgmza-open");
	};
	
	/**
	 * Closes the direction box
	 * @return void
	 */
	WPGMZA.PopoutPanel.prototype.close = function() {
		$(this.element).removeClass("wpgmza-open");
	};
	
	/**
	 * The new modern look directions box. It takes the elements
	 * from the default look and moves them into the map, wrapping
	 * in a new element so we can apply new styles.
	 * @return Object
	 */
	WPGMZA.ModernDirectionsBox = function(map_id) {
		
		WPGMZA.PopoutPanel.apply(this, arguments);
		
		var self = this;
		var original = $("div#wpgmaps_directions_edit_" + map_id);
		
		MYMAP[map_id].directionsBox = this;
		this.map_id = map_id;
		
		if(!original.length)
			return;
		
		var container = $("#wpgmza_map_" + map_id);
		this.mapElement = container;
		
		// Build element
		this.element = $("<div class='wpgmza-popout-panel wpgmza-modern-directions-box'></div>");
		
		// Add to DOM tree
		this.element.append(original);
		container.append(this.element);
		
		// Add buttons
		$(this.element).find("h2").after($("\
			<div class='wpgmza-directions-buttons'>\
				<span class='wpgmza-close'><i class='fa fa-arrow-left' aria-hidden='true'></i></span>\
			</div>\
		"));
		
		var nativeIcon = new WPGMZA.NativeMapsAppIcon();
		this.nativeMapAppIcon = nativeIcon;
		$(this.element).find(".wpgmza-directions-buttons").append(nativeIcon.element);
		$(nativeIcon.element).on("click", function(event) {
			self.onNativeMapsApp(event);
		});
		
		// Remove labels
		$(this.element).find("td:first-child").remove();
		
		// Move show options and options box to after the type select
		var row = $(this.element).find("select[name^='wpgmza_dir_type']").closest("tr");
		$(this.element).find(".wpgmaps_to_row").after(row);
		
		// Options box
		$(this.element).find("#wpgmza_options_box_" + map_id).addClass("wpgmza-directions-options");
		
		// Fancy checkboxes (This would require adding admin styles)
		//$(this.element).find("input:checkbox").addClass("postform cmn-toggle cmn-toggle-round-flat");
		
		// NB: Via waypoints is handled below to be compatible with legacy systems. Search "Waypoint JS"
		
		// Result box
		this.resultBox = new WPGMZA.ModernDirectionsResultBox(map_id, this);
		
		// Bind listeners
		$(document.body).on("click", ".wpgmza_map .wpgmza_gd", function(event) {
			self.open();
		});
		
		$(document.body).on("click", "#wpgmza_marker_list_" + map_id + " .wpgmza_gd", function(event) {
			self.open();
		});
		
		$(this.element).find(".wpgmza-close").on("click", function(event) {
			self.close();
		});
		
		$(this.element).find(".wpgmaps_get_directions").on("click", function(event) {
			if(self.from.length == 0 || self.to.length == 0)
				return;
			
			self.resultBox.open();
		});
	};
	
	WPGMZA.ModernDirectionsBox.prototype = Object.create(WPGMZA.PopoutPanel.prototype);
	WPGMZA.ModernDirectionsBox.prototype.constructor = WPGMZA.ModernDirectionsBox;
	
	Object.defineProperty(WPGMZA.ModernDirectionsBox.prototype, "from", {
		get: function() {
			return $(this.element).find("#wpgmza_input_from_" + this.map_id).val()
		},
		set: function(value) {
			return $(this.element).find("#wpgmza_input_from_" + this.map_id).val(value)
		}
	});
	
	Object.defineProperty(WPGMZA.ModernDirectionsBox.prototype, "to", {
		get: function() {
			return $(this.element).find("#wpgmza_input_to_" + this.map_id).val()
		},
		set: function(value) {
			return $(this.element).find("#wpgmza_input_to_" + this.map_id).val(value)
		}
	});
	
	/**
	 * Opens the popup and closes the results box if it's open
	 * @return void
	 */
	WPGMZA.ModernDirectionsBox.prototype.open = function() {
		WPGMZA.PopoutPanel.prototype.open.apply(this, arguments);
		this.resultBox.close();
		
		$("#wpgmaps_directions_edit_" + this.map_id).show();
	};
	
	/**
	 * Fires when the "open native map" button is clicked
	 * @return void
	 */
	WPGMZA.ModernDirectionsBox.prototype.onNativeMapsApp = function() {
		// TODO: Change this to use lat/lng
		var appleOrGoogle = this.nativeMapAppIcon.type;
		var url = "https://maps." + appleOrGoogle + ".com/?daddr=" + encodeURIComponent($(this.element.find("#wpgmza_input_to_" + this.map_id)).val());
		window.open(url, "_blank");
	};
	
	/***
	 * The second step of the directions box
	 * @return Object
	 */
	WPGMZA.ModernDirectionsResultBox = function(map_id, directionsBox)
	{
		WPGMZA.PopoutPanel.apply(this, arguments);
		
		var self = this;
		var container = $("#wpgmza_map_" + map_id);
		
		this.directionsBox = directionsBox;
		this.map_id = map_id;
		this.mapElement = container;
		
		// Build element
		this.element = $("<div class='wpgmza-popout-panel wpgmza-modern-directions-box'>\
			<h2>" + $(directionsBox.element).find("h2").html() + "</h2>\
			<div class='wpgmza-directions-buttons'>\
				<span class='wpgmza-close'><i class='fa fa-arrow-left' aria-hidden='true'></i></span>\
				<a class='wpgmza-print' style='display: none;'><i class='fa fa-print' aria-hidden='true'></i></a>\
			</div>\
			<div class='wpgmza-directions-results'>\
			</div>\
		</div>");
		
		var nativeIcon = new WPGMZA.NativeMapsAppIcon();
		this.nativeMapAppIcon = nativeIcon;
		$(this.element).find(".wpgmza-directions-buttons").append(nativeIcon.element);
		$(nativeIcon.element).on("click", function(event) {
			self.onNativeMapsApp(event);
		});
		
		// Add to DOM tree
		container.append(this.element);
		
		// Print directions link
		$(this.element).find(".wpgmza-print").attr("href", "data:text/html,<script>document.body.innerHTML += sessionStorage.wpgmzaPrintDirectionsHTML; window.print();</script>");
		
		// Event listeners
		$(this.element).find(".wpgmza-close").on("click", function(event) {
			self.close();
		});
		
		$(this.element).find(".wpgmza-print").on("click", function(event) {
			self.onPrint(event);
		});
		
		$(this.mapElement).on("directionsserviceresult", function(event, response, status) {
			self.onDirectionsChanged(event, response, status);
		});
		
		// Initial state
		this.clear();
	};
	
	WPGMZA.ModernDirectionsResultBox.prototype = Object.create(WPGMZA.PopoutPanel.prototype);
	WPGMZA.ModernDirectionsResultBox.prototype.constructor = WPGMZA.ModernDirectionsResultBox;
	
	WPGMZA.ModernDirectionsResultBox.prototype.clear = function()
	{
		$(this.element).find(".wpgmza-directions-results").html("");
		$(this.element).find("a.wpgmza-print").attr("href", "");
	};
	
	WPGMZA.ModernDirectionsResultBox.prototype.open = function()
	{
		WPGMZA.PopoutPanel.prototype.open.apply(this, arguments);
		this.showPreloader();
	};
	
	WPGMZA.ModernDirectionsResultBox.prototype.showPreloader = function()
	{
		$(this.element).find(".wpgmza-directions-results").html("<img src='" + wpgmza_ajax_loader_gif.src + "'/>");
	};
	
	WPGMZA.ModernDirectionsResultBox.prototype.onDirectionsChanged = function(event, response, status)
	{
		this.clear();
		
		switch(status)
		{
			case google.maps.DirectionsStatus.OK:
				directionsDisplay[this.map_id].setPanel(
					$(this.element).find(".wpgmza-directions-results")[0]
				);
				break;
				
			case google.maps.DirectionsStatus.NOT_FOUND:
			case google.maps.DirectionsStatus.ZERO_RESULTS:
			case google.maps.DirectionsStatus.MAX_WAYPOINTS_EXCEEDED:
			case google.maps.DirectionsStatus.MAX_ROUTE_LENGTH_EXCEEDED:
			case google.maps.DirectionsStatus.INVALID_REQUEST:
			case google.maps.DirectionsStatus.OVER_QUERY_LIMIT:
			case google.maps.DirectionsStatus.REQUEST_DENIED:
			 
				var key = status.toLowerCase();
				var message = wpgmza_localized_strings[key];
				
				$(this.element).find(".wpgmza-directions-results").html(
					'<i class="fa fa-times" aria-hidden="true"></i>' + message
				);
				
				break;
			
			default:
				
				var message = wpgmza_localized_string.unknown_error;
				
				$(this.element).find(".wpgmza-directions-results").html(
					'<i class="fa fa-times" aria-hidden="true"></i>' + message
				);
				
				break;
		}
	};
	
	WPGMZA.ModernDirectionsResultBox.prototype.onNativeMapsApp = function(event)
	{
		// TODO: Change this to use lat/lng
		var appleOrGoogle = this.nativeMapAppIcon.type;
		var params = {
			saddr: this.directionsBox.from,
			daddr: this.directionsBox.to,
			dirflg: $("#wpgmza_dir_type_" + this.map_id).val().substr(0, 1).toLowerCase(),
			om: 1
		};
		var arr = [];
		var url;
		
		for(var name in params)
			arr.push(name + "=" + encodeURIComponent(params[name]));
		
		url = "https://maps." + appleOrGoogle + ".com/?" + arr.join("&");

		window.open(url, "_blank");
	};
	
	WPGMZA.ModernDirectionsResultBox.prototype.onPrint = function(event)
	{
		var content = $(this.element).find(".wpgmza-directions-results").html();
		var doc = document.implementation.createHTMLDocument();
		var html;
		
		// sessionStorage.wpgmzaPrintDirectionsHTML = content;
	};
	
	/**
	 * The modern look and feel marker listing
	 * @return Object
	 */
	WPGMZA.ModernMarkerListing = function(map_id)
	{
		var self = this;
		
		WPGMZA.PopoutPanel.apply(this, arguments);
		
		// Build element
		var container = $("#wpgmza_map_" + map_id);
		
		this.element = $("<div class='wpgmza-popout-panel wpgmza-modern-marker-listing'>\
			<div class='wpgmza-close-container'>\
				<span class='wpgmza-close'><i class='fa fa-times' aria-hidden='true'></i></span>\
			</div>\
			<ul>\
			</ul>\
		</div>");
		
		this.map_id = map_id;
		this.mapElement = container;
		this.mapElement.append(this.element);
		
		// List items
		this.list = $(this.element).find("ul");
		
		this.markers = wpgmaps_localize_marker_data[map_id];
		
		var order = window["wpgmza_modern_marker_listing_marker_order_by_id_for_map_" + map_id];
		
		for(var index = 0; index < order.length; index++)
		{
			var marker_id = order[index];
			var marker = this.markers[marker_id];
			var li = $(WPGMZA.ModernMarkerListing.listItemHTML);
			var fields = $(li).find("[data-name]");
			
			$(li).attr("mid", marker_id);
			$(li).attr("mapid", map_id);
			
			for(var i = 0; i < fields.length; i++)
			{
				var name = $(fields[i]).attr("data-name");
				
				if(!marker[name])
					continue;
				
				$(fields[i]).html(marker[name]);
			}
			
			if(marker.pic)
				$(li).find(".wpgmza-marker-listing-pic").attr("src", marker.pic);
			
			this.list.append(li);
		}
		
		// Marker view
		this.markerView = new WPGMZA.ModernMarkerListingMarkerView(map_id);
		
		// Open button
		$(container).append('<div class="wpgmza-modern-marker-open-button wpgmza-modern-shadow wpgmza-modern-hover-opaque"><i class="fa fa-map-marker"></i> <i class="fa fa-list"></i></div>');
		$(container).find(".wpgmza-modern-marker-open-button").on("click", function(event) {
			self.open();
            $("#wpgmza_map_" + map_id + " .wpgmza-modern-store-locator").addClass("wpgmza_sl_offset");
		});
		
		// Event listeners
		$(this.element).find(".wpgmza-close-container").on("click", function(event) {
			self.close();
            $("#wpgmza_map_" + map_id + " .wpgmza-modern-store-locator").removeClass("wpgmza_sl_offset");
		});
		
		$(this.element).on("click", "li", function(event) {
			self.markerView.open($(event.currentTarget).attr("mid"));
		});
		
		$(document.body).on("click", ".wpgmza_sl_reset_button_" + map_id, function(event) {
			$(self.element).find("li[mid]").show();
		});
		
		$("select[mid='" + map_id + "'][name='wpgmza_filter_select']").on("change", function(event) {
			self.updateFilteredItems();
		});
		
		$(".wpgmza_checkbox[mid='" + map_id + "']").on("change", function(event) {
			self.updateFilteredItems();
		});
	};
	
	WPGMZA.ModernMarkerListing.prototype = Object.create(WPGMZA.PopoutPanel.prototype);
	WPGMZA.ModernMarkerListing.prototype.constructor = WPGMZA.ModernMarkerListing;
	
	WPGMZA.ModernMarkerListing.prototype.setVisibleListItems = function(marker_ids)
	{
		$(this.element).find("li").each(function(index, el) {
			
			if(!el.hasAttribute("mid"))
				return;
			
			var visible = marker_ids.indexOf( $(el).attr("mid") ) != -1;
			
			if(visible)
				$(el).show();
			else
				$(el).hide();
			
		});
	}
	
	WPGMZA.ModernMarkerListing.prototype.updateFilteredItems = function()
	{
		//var categories = this.getSelectedCategories();
	}
	
	WPGMZA.ModernMarkerListing.prototype.getSelectedCategories = function()
	{
		var select = $("select[mid='" + this.map_id + "'][name='wpgmza_filter_select']");
		var checkboxes = $(".wpgmza_checkbox[mid='" + this.map_id + "']:checked");
		var categories = [];
		
		if(select.length)
			categories.push(select.val());
		else
		{
			checkboxes.each(function(index, el) {
				categories.push($(el).val());
			});
		}
		
		return categories;
	}
	
	WPGMZA.ModernMarkerListing.listItemHTML = "\
		<li class='wpgmaps_mlist_row'>\
			<img class='wpgmza-marker-listing-pic'/>\
			<div data-name='title'/>\
			<div data-name='address'/>\
			<div data-name='desc'/>\
		</li>\
	";
	
	/**
	 * This is the 2nd step of the modern look and feel marker listing
	 * @return Object
	 */
	WPGMZA.ModernMarkerListingMarkerView = function(map_id)
	{
		var self = this;
		
		WPGMZA.PopoutPanel.apply(this, arguments);
		
		var container = $("#wpgmza_map_" + map_id);
		this.map_id = map_id;
		
		this.element = $("<div class='wpgmza-popout-panel wpgmza-modern-marker-listing-marker-view'>\
			<div class='wpgmza-close-container'>\
				<span class='wpgmza-close'><i class='fa fa-arrow-left' aria-hidden='true'></i></span>\
				<span class='wpgmza-close'><i class='fa fa-times' aria-hidden='true'></i></span>\
			</div>\
			<div data-name='title'></div>\
			<div data-name='address'></div>\
			<div data-name='category'></div>\
			<img data-name='pic'/>\
			<div data-name='desc'></div>\
			<div class='wpgmza-modern-marker-listing-buttons'>\
				<div class='wpgmza-modern-marker-listing-button wpgmza-link-button'>\
					<i class='fa fa-link' aria-hidden='true'></i>\
					<div>\
						" + wpgmza_localized_strings.link + "\
					</div>\
				</div>\
				<div class='wpgmza-modern-marker-listing-button wpgmza-directions-button'>\
					<i class='fa fa-road' aria-hidden='true'></i>\
					<div>\
						" + wpgmza_localized_strings.directions + "\
					</div>\
				</div>\
				<div class='wpgmza-modern-marker-listing-button wpgmza-zoom-button'>\
					<i class='fa fa-search-plus' aria-hidden='true'></i>\
					<div>\
						" + wpgmza_localized_strings.zoom + "\
					</div>\
				</div>\
			</div>\
		</div>");
		
		container.append(this.element);
		
		$(this.element).find(".wpgmza-close").on("click", function(event) {
			self.close();
            $("#wpgmza_map_" + map_id + " .wpgmza-modern-store-locator").removeClass("wpgmza_sl_mv_offset");
		});
		
		$(this.element).find(".wpgmza-link-button").on("click", function(event) {
			self.onLink(event);
		});
		
		$(this.element).find(".wpgmza-directions-button").on("click", function(event) {
			self.onDirections(event);
		});
		
		$(this.element).find(".wpgmza-zoom-button").on("click", function(event) {
			self.onZoom(event);
		});
	}
	
	WPGMZA.ModernMarkerListingMarkerView.prototype = Object.create(WPGMZA.PopoutPanel.prototype);
	WPGMZA.ModernMarkerListingMarkerView.prototype.constructor = WPGMZA.ModernMarkerListingMarkerView;
	
	WPGMZA.ModernMarkerListingMarkerView.prototype.open = function(marker_id)
	{
		WPGMZA.PopoutPanel.prototype.open.apply(this, arguments);
		
		var self = this;
		var marker_data = wpgmaps_localize_marker_data[this.map_id][marker_id];
		
		this.focusedMarkerData = marker_data;
		this.focusedMarker = marker_array[this.map_id][marker_id];
		
		$(this.element).find("[data-name]").each(function(index, el) {
			
			var name = $(el).attr("data-name");
			
			if(!marker_data[name])
				return;
			
			var value = marker_data[name];
			
			switch(name)
			{
				case "pic":
					$(el).attr("src", value);
					break;
				
				case "category":
					var ids = value.split(",");
					var names = [];
					
					for(var i = 0; i < ids.length; i++) {
						var id = ids[i];
						
						if(wpgmza_category_data[id])
							names.push(wpgmza_category_data[id].category_name);
					}
					
					$(el).html(names.join(", "));
					
					break;
				
				default:
					$(el).html(value);
					break;
			}
			
		});
		
		if(!marker_data["linkd"] || marker_data["linkd"].length == 0)
			$(this.element).find(".wpgmza-link-button").hide();
		else
			$(this.element).find(".wpgmza-link-button").show();

        $("#wpgmza_map_" + this.map_id + " .wpgmza-modern-store-locator").addClass("wpgmza_sl_mv_offset");
	 
		$(this.element).find("[data-custom-field-name]").remove();
		$(this.element).find(".wpgmza-modern-marker-listing-buttons").before(marker_data.custom_fields_html);
		
		$(this.element).find(".wpgmza-close").on("click", function(event) {
			self.close();
		});
	}
	
	WPGMZA.ModernMarkerListingMarkerView.prototype.onLink = function(event) {
		
		window.open(this.focusedMarkerData.linkd, "_blank");
		
	}
	
	WPGMZA.ModernMarkerListingMarkerView.prototype.onDirections = function(event) {
		
		if(MYMAP[entry].directionsBox)
			MYMAP[entry].directionsBox.open();
		else
			$("#wpgmaps_directions_edit_" + this.map_id).show();
		
		$("#wpgmza_input_to_" + this.map_id).val(this.focusedMarkerData.address);
		
	}
	
	WPGMZA.ModernMarkerListingMarkerView.prototype.onZoom = function(event) {
		
		var map = MYMAP[this.map_id].map;
		
		map.setCenter(this.focusedMarker.getPosition());
		map.setZoom(14);
		
  }
	
	/**
	 * This catches Google Maps API errors and displays them in
	 * an alert box.
	 * @return void
	 */
	WPGMZA.GoogleAPIErrorHandler = function() {
		var _error = console.error;
		
		console.error = function(message)
		{
			var m = message.match(/^Google Maps API error: (\w+) (.+)/);
			
			if(m)
			{
				var friendlyMessage = m[1].replace(/([A-Z])/g, " $1") + " - See " + m[2] + " for more information";
				alert(friendlyMessage);
			}
			
			_error.apply(this, arguments);
		}
	};
	
	WPGMZA.googleAPIErrorHandler = new WPGMZA.GoogleAPIErrorHandler();

	/**
	 * This module handles the custom field filtering logic
	 * @constructor
	 */
	WPGMZA.CustomFieldFilterController = function(map_id)
	{
		var self = this;
		
		this.map_id = map_id;
		this.widgets = [];
		this.ajaxTimeoutID = null;
		this.ajaxRequest = null;
		
		// TODO: This will break pagination (page count mismatch) when we integrate pagination for basic styles. I suggest we unify the filtering before doing so
		this.markerListingCSS = $("<style type='text/css'/>");
		$(document.body).append(this.markerListingCSS);
		
		WPGMZA.CustomFieldFilterController.controllersByMapID[map_id] = this;
		
		$("[data-wpgmza-filter-widget-class][data-map-id=" + map_id + "]").each(function(index, el) {
			self.widgets.push( WPGMZA.CustomFieldFilterWidget.createInstance(el) );
			
			$(el).on("input change", function(event) {
				self.onWidgetChanged(event);
			});
			
			$(":checkbox").on("click", function(event) {
				self.onWidgetChanged(event);
			});
		});
		
		/*var tables = $('#wpgmza_table_'+wpgmaps_localize[entry]['id']);
		if(tables.length)
			this.applyToAdvancedTable(tables[0]);*/
	};
	
	WPGMZA.CustomFieldFilterController.AJAX_DELAY = 500;
	WPGMZA.CustomFieldFilterController.controllersByMapID = {};
	WPGMZA.CustomFieldFilterController.dataTablesSourceHTMLByMapID = {};
	
	WPGMZA.CustomFieldFilterController.createInstance = function(map_id)
	{
		return new WPGMZA.CustomFieldFilterController(map_id);
	};
	
	WPGMZA.CustomFieldFilterController.prototype.getAjaxRequestData = function() {
		var self = this;
		
		var result = {
			url: ajaxurl,
			method: "POST",
			data: {
				action: "wpgmza_custom_field_filter_get_filtered_marker_ids",
				map_id: this.map_id,
				widgetData: []
			},
			success: function(response, status, xhr) {
				self.onAjaxResponse(response, status, xhr);
			}
		};
		
		this.widgets.forEach(function(widget) {
			result.data.widgetData.push(widget.getAjaxRequestData());
		});
		
		return result;
	};
	
	WPGMZA.CustomFieldFilterController.prototype.onWidgetChanged = function(event) {
		var self = this;
		
		if(this.ajaxTimeoutID)
			clearTimeout(this.ajaxTimeoutID)
		
		if(this.ajaxRequest)
			this.ajaxRequest.abort();
		
		this.ajaxTimeoutID = setTimeout(function() {
			this.ajaxTimeoutID = null;
			
			var data = self.getAjaxRequestData();
			this.ajaxRequest = $.ajax(data);
		}, WPGMZA.CustomFieldFilterController.AJAX_DELAY);
	};
	
	WPGMZA.CustomFieldFilterController.prototype.onAjaxResponse = function(response, status, xhr) {
		this.lastResponse = response;
		
		var selectors = [];
		
		for(var marker_id in marker_array[this.map_id])
		{
			var visible = (response.marker_ids.indexOf(marker_id) > -1);
			marker_array[this.map_id][marker_id].setVisible(visible);
			
			if(!visible)
				selectors.push(".wpgmaps_mlist_row[mid='" + marker_id + "']");
		}
		
		if(wpgmaps_localize[this.map_id].order_markers_by && wpgmaps_localize[this.map_id].order_markers_by == 2)
		{
			wpgmza_update_data_table(
				WPGMZA.CustomFieldFilterController.dataTablesSourceHTMLByMapID[this.map_id],
				this.map_id
			);
		}
		else
		{
			this.markerListingCSS.html( selectors.join(", ") + "{ display: none; }" );
			
			var container;
			if(this.currAdvancedTableHTML)
				container = $("#wpgmza_marker_holder_" + this.map_id);
			else
				container = $(this.currAdvancedTableHTML);
			
			this.applyToAdvancedTable(container);
		}
	};
	
	/**
	 * This function is a quick hack to re-apply the last response after the store locator
	 * has been used or marker listing filtering changes. This should be deprecated and
	 * the filtering system unified at some point.
	 * @return void
	 */
	WPGMZA.CustomFieldFilterController.prototype.reapplyLastResponse = function() {
		if(!this.lastResponse)
			return;
		
		var response = this.lastResponse;
		
		for(var marker_id in marker_array[this.map_id])
		{
			var visible = (response.marker_ids.indexOf(marker_id) > -1);
			marker_array[this.map_id][marker_id].setVisible(visible);
		}
	};
	
	WPGMZA.CustomFieldFilterController.prototype.applyToAdvancedTable = function() {
		if(!this.lastResponse)
			return;
		
		var response = this.lastResponse;
		var container = $("#wpgmza_marker_holder_" + this.map_id);
		
		$(container).find("[mid]").each(function(index, el) {
			var marker_id = $(el).attr("mid");
			if(response.marker_ids.indexOf(marker_id) == -1)
				$(el).remove();
		});
	};
	
	$(document).ready(function(event) {
		
		$(".wpgmza_map").each(function(index, el) {
			var map_id = parseInt( $(el).attr("id").match(/\d+/)[0] );
			MYMAP[map_id].customFieldFilterController = WPGMZA.CustomFieldFilterController.createInstance(map_id);

            setTimeout(function () {
                $(el).children('div').first().after($('.wpgmza-modern-marker-open-button'));
            }, 500);
		});
		
		
	});
	
	/**
	 * This is the base module for custom field filter widgets
	 * @constructor
	 */
	WPGMZA.CustomFieldFilterWidget = function(element) {
		this.element = element;
	};
	
	WPGMZA.CustomFieldFilterWidget.createInstance = function(element) {
		var widgetPHPClass = $(element).attr("data-wpgmza-filter-widget-class");
		var constructor = null;
		
		switch(widgetPHPClass)
		{
			case "WPGMZA\\CustomFieldFilterWidget\\Text":
				constructor = WPGMZA.CustomFieldFilterWidget.Text;
				break;
				
			case "WPGMZA\\CustomFieldFilterWidget\\Dropdown":
				constructor = WPGMZA.CustomFieldFilterWidget.Dropdown;
				break;
			
			case "WPGMZA\\CustomFieldFilterWidget\\Checkboxes":
				constructor = WPGMZA.CustomFieldFilterWidget.Checkboxes;
				break;
				
			default:
				throw new Error("Unknown field type '" + widgetPHPClass + "'");
				break;
		}
		
		return new constructor(element);
	};
	
	WPGMZA.CustomFieldFilterWidget.prototype.getAjaxRequestData = function() {
		var data = {
			field_id: $(this.element).attr("data-field-id"),
			value: $(this.element).val()
		};
		
		return data;
	};
	
	/**
	 * Text field custom field filter
	 * @constructor
	 */
	WPGMZA.CustomFieldFilterWidget.Text = function(element) {
		WPGMZA.CustomFieldFilterWidget.apply(this, arguments);
	};
	
	WPGMZA.CustomFieldFilterWidget.Text.prototype = Object.create(WPGMZA.CustomFieldFilterWidget.prototype);
	WPGMZA.CustomFieldFilterWidget.Text.prototype.constructor = WPGMZA.CustomFieldFilterWidget.Text;
	
	/**
	 * Dropdown field custom field filter
	 * @constructor
	 */
	WPGMZA.CustomFieldFilterWidget.Dropdown = function(element) {
		WPGMZA.CustomFieldFilterWidget.apply(this, arguments);
	};
	
	WPGMZA.CustomFieldFilterWidget.Dropdown.prototype = Object.create(WPGMZA.CustomFieldFilterWidget.prototype);
	WPGMZA.CustomFieldFilterWidget.Dropdown.prototype.constructor = WPGMZA.CustomFieldFilterWidget.Dropdown;
	
	/**
	 * Checkboxes field custom field filter
	 * @constructor
	 */
	WPGMZA.CustomFieldFilterWidget.Checkboxes = function(element) {
		WPGMZA.CustomFieldFilterWidget.apply(this, arguments);
	};
	
	WPGMZA.CustomFieldFilterWidget.Checkboxes.prototype = Object.create(WPGMZA.CustomFieldFilterWidget.prototype);
	WPGMZA.CustomFieldFilterWidget.Checkboxes.prototype.constructor = WPGMZA.CustomFieldFilterWidget.Checkboxes;
	
	WPGMZA.CustomFieldFilterWidget.Checkboxes.prototype.getAjaxRequestData = function() {
		var checked = [];
		
		$(this.element).find(":checked").each(function(index, el) {
			checked.push($(el).val());
		});
		
		return {
			field_id: $(this.element).attr("data-field-id"),
			value: checked
		}
	};
	
	/**
	 * This module is the modern store locator circle
	 * @constructor
	 */
	WPGMZA.ModernStoreLocatorCircle = function(map_id, settings) {
		var self = this;
		
		this.map_id = map_id;
		this.map = MYMAP[map_id].map;
		
		this.mapElement = $("#wpgmza_map_" + map_id);
		this.mapSize = {
			width: this.mapElement.width(),
			height: this.mapElement.height()
		};
		
		setInterval(function() {
			
			var mapSize = {
				width: self.mapElement.width(),
				height: self.mapElement.height()
			};
			
			if(mapSize.width == self.mapSize.width && mapSize.height == self.mapSize.height)
				return;
			
			self.canvasLayer.resize_();
			self.canvasLayer.draw();
			
			self.mapSize = mapSize;
			
		}, 1000);
		
		$(document).bind('webkitfullscreenchange mozfullscreenchange fullscreenchange', function() {
			
			self.canvasLayer.resize_();
			self.canvasLayer.draw();
			
		});
		
       /* this.canvasLayer = new CanvasLayer({
			map: this.map,
			resizeHandler: function(event) {
				self.onResize(event);
			},
			updateHandler: function(event) {
				self.onUpdate(event);
			},
			animate: true,
			resolutionScale: this.getResolutionScale()
        });*/
		
		this.initCanvasLayer();
		
		this.settings = {
			center: new google.maps.LatLng(0, 0),
			radius: 1,
			color: "#63AFF2",
			
			shadowColor: "white",
			shadowBlur: 4,
			
			centerRingRadius: 10,
			centerRingLineWidth: 3,

			numInnerRings: 9,
			innerRingLineWidth: 1,
			innerRingFade: true,
			
			numOuterRings: 7,
			
			ringLineWidth: 1,
			
			mainRingLineWidth: 2,
			
			numSpokes: 6,
			spokesStartAngle: Math.PI / 2,
			
			numRadiusLabels: 6,
			radiusLabelsStartAngle: Math.PI / 2,
			radiusLabelFont: "13px sans-serif",
			
			visible: false
		};
		
		if(settings)
			this.setOptions(settings);
	};
	
	WPGMZA.ModernStoreLocatorCircle.createInstance = function(map_id, settings) {
		return new WPGMZA.ModernStoreLocatorCircle(map_id, settings);
	};
	
	WPGMZA.ModernStoreLocatorCircle.prototype.initCanvasLayer = function() {
		
		var self = this;
		
		if(this.canvasLayer)
		{
			this.canvasLayer.setMap(null);
			this.canvasLayer.setAnimate(false);
		}
		
		this.canvasLayer = new CanvasLayer({
			map: this.map,
			resizeHandler: function(event) {
				self.onResize(event);
			},
			updateHandler: function(event) {
				self.onUpdate(event);
			},
			animate: true,
			resolutionScale: this.getResolutionScale()
        });
		
	}
	
	WPGMZA.ModernStoreLocatorCircle.prototype.onResize = function(event) { 
		this.draw();
	};
	
	WPGMZA.ModernStoreLocatorCircle.prototype.onUpdate = function(event) { 
		this.draw();
	};
	
	WPGMZA.ModernStoreLocatorCircle.prototype.setOptions = function(options) {
		for(var name in options)
		{
			var functionName = "set" + name.substr(0, 1).toUpperCase() + name.substr(1);
			
			if(typeof this[functionName] == "function")
				this[functionName](options[name]);
			else
				this.settings[name] = options[name];
		}
		this.canvasLayer.scheduleUpdate();
	};
	
	WPGMZA.ModernStoreLocatorCircle.prototype.getResolutionScale = function() {
		return window.devicePixelRatio || 1;
	};
	
	WPGMZA.ModernStoreLocatorCircle.prototype.getCenter = function() {
		return this.getPosition();
	};
	
	WPGMZA.ModernStoreLocatorCircle.prototype.setCenter = function(value) {
		this.setPosition(value);
	};
	
	WPGMZA.ModernStoreLocatorCircle.prototype.getPosition = function() {
		return this.settings.center;
	};
	
	WPGMZA.ModernStoreLocatorCircle.prototype.setPosition = function(position) {
		this.settings.center = position;
		this.canvasLayer.scheduleUpdate();
	};
	
	WPGMZA.ModernStoreLocatorCircle.prototype.getRadius = function() {
		return this.settings.radius;
	};
	
	WPGMZA.ModernStoreLocatorCircle.prototype.setRadius = function(radius) {
		
		if(isNaN(radius))
			throw new Error("Invalid radius");
		
		this.settings.radius = radius;
		this.canvasLayer.scheduleUpdate();
	};
	
	WPGMZA.ModernStoreLocatorCircle.prototype.getVisible = function(visible) {
		return this.settings.visible;
	};
	
	WPGMZA.ModernStoreLocatorCircle.prototype.setVisible = function(visible) {
		this.settings.visible = visible;
		this.canvasLayer.scheduleUpdate();
	};
	
	/**
	 * This function transforms a km radius into canvas space
	 * @return number
	 */
	WPGMZA.ModernStoreLocatorCircle.prototype.getTransformedRadius = function(km) {
		var multiplierAtEquator = 0.006395;
		var spherical = google.maps.geometry.spherical;
		
		var center = this.settings.center;
		var equator = new google.maps.LatLng({
			lat: 0.0,
			lng: 0.0
		});
		var latitude = new google.maps.LatLng({
			lat: center.lat(),
			lng: 0.0
		});
		
		var offsetAtEquator = spherical.computeOffset(equator, km * 1000, 90);
		var offsetAtLatitude = spherical.computeOffset(latitude, km * 1000, 90);
		
		var factor = offsetAtLatitude.lng() / offsetAtEquator.lng();
		
		return km * multiplierAtEquator * factor;
	};
	
	WPGMZA.ModernStoreLocatorCircle.prototype.draw = function() {
		// clear previous canvas contents
		var canvasLayer = this.canvasLayer;
		var settings = this.settings;
		
        var canvasWidth = canvasLayer.canvas.width;
        var canvasHeight = canvasLayer.canvas.height;
		
		var map = MYMAP[this.map_id].map;
		var resolutionScale = this.getResolutionScale();
		
		context = /*canvasLayer.canvas.getContext('webgl') ||*/ canvasLayer.canvas.getContext('2d');
		
        context.clearRect(0, 0, canvasWidth, canvasHeight);

		if(!settings.visible)
			return;
		
		context.shadowColor = settings.shadowColor;
		context.shadowBlur = settings.shadowBlur;
		
		// NB: 2018/02/13 - Left this here in case it needs to be calibrated more accurately
		/*if(!this.testCircle)
		{
			this.testCircle = new google.maps.Circle({
				strokeColor: "#ff0000",
				strokeOpacity: 0.5,
				strokeWeight: 3,
				map: this.map,
				center: this.settings.center
			});
		}
		
		this.testCircle.setCenter(settings.center);
		this.testCircle.setRadius(settings.radius * 1000);*/
		
        /* We need to scale and translate the map for current view.
         * see https://developers.google.com/maps/documentation/javascript/maptypes#MapCoordinates
         */
        var mapProjection = map.getProjection();

        /**
         * Clear transformation from last update by setting to identity matrix.
         * Could use context.resetTransform(), but most browsers don't support
         * it yet.
         */
        context.setTransform(1, 0, 0, 1, 0, 0);
        
        // scale is just 2^zoom
        // If canvasLayer is scaled (with resolutionScale), we need to scale by
        // the same amount to account for the larger canvas.
        var scale = Math.pow(2, map.zoom) * resolutionScale;
        context.scale(scale, scale);

        /* If the map was not translated, the topLeft corner would be 0,0 in
         * world coordinates. Our translation is just the vector from the
         * world coordinate of the topLeft corder to 0,0.
         */
        var offset = mapProjection.fromLatLngToPoint(canvasLayer.getTopLeft());
        context.translate(-offset.x, -offset.y);

        // project rectLatLng to world coordinates and draw
        var worldPoint = mapProjection.fromLatLngToPoint(this.settings.center);
		var rgba = WPGMZA.hexToRgba(settings.color);
		var ringSpacing = this.getTransformedRadius(settings.radius) / (settings.numInnerRings + 1);
		
		// TODO: Implement gradients for color and opacity
		
		// Inside circle (fixed?)
        context.strokeStyle = settings.color;
		context.lineWidth = (1 / scale) * settings.centerRingLineWidth;
		
		context.beginPath();
		context.arc(
			worldPoint.x, 
			worldPoint.y, 
			this.getTransformedRadius(settings.centerRingRadius) / scale, 0, 2 * Math.PI
		);
		context.stroke();
		context.closePath();
		
		// Spokes
		var radius = this.getTransformedRadius(settings.radius) + (ringSpacing * settings.numOuterRings) + 1;
		var grad = context.createRadialGradient(0, 0, 0, 0, 0, radius);
		var rgba = WPGMZA.hexToRgba(settings.color);
		var start = WPGMZA.rgbaToString(rgba), end;
		var spokeAngle;
		
		rgba.a = 0;
		end = WPGMZA.rgbaToString(rgba);
		
		grad.addColorStop(0, start);
		grad.addColorStop(1, end);
		
		context.save();
		
		context.translate(worldPoint.x, worldPoint.y);
		context.strokeStyle = grad;
		context.lineWidth = 2 / scale;
		
		for(var i = 0; i < settings.numSpokes; i++)
		{
			spokeAngle = settings.spokesStartAngle + (Math.PI * 2) * (i / settings.numSpokes);
			
			x = Math.cos(spokeAngle) * radius;
			y = Math.sin(spokeAngle) * radius;
			
			context.setLineDash([2 / scale, 15 / scale]);
			
			context.beginPath();
			context.moveTo(0, 0);
			context.lineTo(x, y);
			context.stroke();
		}
		
		context.setLineDash([]);
		
		context.restore();
		
		// Inner ringlets
		context.lineWidth = (1 / scale) * settings.innerRingLineWidth;
		
		for(var i = 1; i <= settings.numInnerRings; i++)
		{
			var radius = i * ringSpacing;
			
			if(settings.innerRingFade)
				rgba.a = 1 - (i - 1) / settings.numInnerRings;
			
			context.strokeStyle = WPGMZA.rgbaToString(rgba);
			
			context.beginPath();
			context.arc(worldPoint.x, worldPoint.y, radius, 0, 2 * Math.PI);
			context.stroke();
			context.closePath();
		}
		
		// Main circle
		context.strokeStyle = settings.color;
		context.lineWidth = (1 / scale) * settings.centerRingLineWidth;
		
		context.beginPath();
		context.arc(worldPoint.x, worldPoint.y, this.getTransformedRadius(settings.radius), 0, 2 * Math.PI);
		context.stroke();
		context.closePath();
		
		// Outer ringlets
		var radius = radius + ringSpacing;
		for(var i = 0; i < settings.numOuterRings; i++)
		{
			if(settings.innerRingFade)
				rgba.a = 1 - i / settings.numOuterRings;
			
			context.strokeStyle = WPGMZA.rgbaToString(rgba);
			
			context.beginPath();
			context.arc(worldPoint.x, worldPoint.y, radius, 0, 2 * Math.PI);
			context.stroke();
			context.closePath();
		
			radius += ringSpacing;
		}
		
		// Text
		if(settings.numRadiusLabels > 0)
		{
			var m;
			var radius = this.getTransformedRadius(settings.radius);
			var clipRadius = (12 * 1.1) / scale;
			var x, y;
			
			if(m = settings.radiusLabelFont.match(/(\d+)px/))
				clipRadius = (parseInt(m[1]) / 2 * 1.1) / scale;
			
			context.font = settings.radiusLabelFont;
			context.textAlign = "center";
			context.textBaseline = "middle";
			context.fillStyle = settings.color;
			
			context.save();
			
			context.translate(worldPoint.x, worldPoint.y)
			
			for(var i = 0; i < settings.numRadiusLabels; i++)
			{
				var spokeAngle = settings.radiusLabelsStartAngle + (Math.PI * 2) * (i / settings.numRadiusLabels);
				var textAngle = spokeAngle + Math.PI / 2;
				var text = settings.radiusString;
				var width;
				
				if(Math.sin(spokeAngle) > 0)
					textAngle -= Math.PI;
				
				x = Math.cos(spokeAngle) * radius;
				y = Math.sin(spokeAngle) * radius;
				
				context.save();
				
				context.translate(x, y);
				
				context.rotate(textAngle);
				context.scale(1 / scale, 1 / scale);
				
				width = context.measureText(text).width;
				height = width / 2;
				context.clearRect(-width, -height, 2 * width, 2 * height);
				
				context.fillText(settings.radiusString, 0, 0);
				
				context.restore();
			}
			
			context.restore();
		}
	}
	
})(jQuery);

// Waypoint JS
(function($) {
	$(document).ready(function(event) {
		
		var template = $(".wpgmaps_via.wpgmaps_template");
		template.removeClass("wpgmaps_template");
		template.remove();
		
		$(".wpgmaps_add_waypoint a").on("click", function(event) {
			
			var map_id = parseInt($(event.target).closest("[data-map-id]").attr("data-map-id"));
			var row = template.clone();
			
			$(event.target).closest(".wpgmza-form-field").before(row);
			
			var options = {
				types: ['geocode']
			};
			
			var restrict = wpgmaps_localize[map_id]['other_settings']['wpgmza_store_locator_restrict'];
			if(restrict && restrict.length)
				options.componentRestrictions = {
					country: restrict
				};
			
			new google.maps.places.Autocomplete($(row).find("input")[0], options);
			
			row.find("input").focus();
			
		});
		
		$(document.body).on("click", ".wpgmaps_remove_via", function(event) {
			$(event.target).closest(".wpgmza-form-field").remove();
		});
		
		if($("body").sortable)
			$(".wpgmaps_directions_outer_div [data-map-id]").sortable({
				items: ".wpgmza-form-field.wpgmaps_via"
			});
		
	});
})(jQuery);