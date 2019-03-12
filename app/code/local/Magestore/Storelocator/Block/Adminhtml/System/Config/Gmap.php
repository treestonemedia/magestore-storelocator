<?php
class Magestore_Storelocator_Block_Adminhtml_System_Config_Gmap extends Mage_Adminhtml_Block_System_Config_Form_Field {

	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
		return '
        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
        <script>
        function IsJsonString(v) {
            try {
                JSON.parse(v);
            } catch (e) {
                return false;
            }
            return true;
        }

        Validation.add("validate-json", "Invalid json data !", function(v) {
            return Validation.get("IsEmpty").test(v) || IsJsonString(v);
        });
        var map = null;
        var mapStyles = ' . Mage::helper('core')->jsonEncode(Mage::getModel('storelocator/gmap')->getMapStyle()) . '
        var library = library || {};

        library.json = {
            replacer: function(match, pIndent, pKey, pVal, pEnd) {
                var key = \'<span class=\"json-key\">\';
                var val = \'<span class=\"json-value\">\';
                var str = \'<span class=\"json-string\">\';
                var r = pIndent || \'\';
                if (pKey)
                    r = r + key + pKey.replace(/[\": ]/g, \'\') + \'</span>: \';
                if (pVal)
                    r = r + (pVal[0] == \'\"\' ? str : val) + pVal + \'</span>\';
                return r + (pEnd || \'\');
            },
            prettyPrint: function(obj) {
                var jsonLine = /^( *)(\"[\w]+\": )?(\"[^\"]*\"|[\w.+-]*)?([,[{])?$/mg;
                return JSON.stringify(obj, null, 3)
                    .replace(/&/g, \'&amp;\').replace(/\\\"/g, \'&quot;\')
                    .replace(/</g, \'&lt;\').replace(/>/g, \'&gt;\')
                    .replace(jsonLine, library.json.replacer);
            }
        };

        function customMapStyle() {
            if ($("storelocator_style_config_use_available_style").value === "1") {
                map.setOptions({styles : JSON.parse(mapStyles[$("storelocator_style_config_map_style").value])});
            } else {
                try {
                    var styles = JSON.parse($("storelocator_style_config_map_custom_style").value);
                    prettyJson();
                    map.setOptions({styles: styles});
                } catch (e) {
                    map.setOptions({styles: []});
                }
            }
        }

        function prettyJson() {
            try {
                $("pretty-json-style").innerHTML = library.json.prettyPrint(JSON.parse($("storelocator_style_config_map_custom_style").value));
            } catch (e) {
                $("pretty-json-style").innerHTML = "Invalid json data !";
            }
        }

        </script>
        <div id="map" style="width: 600px;height: 300px;"></div>
        <style>
            .pretty-json-style {
                background-color: ghostwhite;
                border: 1px solid silver;
                padding: 10px 20px;
            }
            .json-key {
                color: blue;
            }
            .json-value {
                color: navy;
            }
            .json-string {
                color: olive;
            }
        </style>
        <script>
        Event.observe(window,"load",function(){
            $("storelocator_style_config_map_custom_style").setStyle({width:"81%"});
            $("storelocator_style_config_map_custom_style").setAttribute("hidden", "1");
            $("storelocator_style_config_map_custom_style").insert({before: \'<button type="button" id="btn-toggle-json-style" style="position: relative;float: right;right: 108px;">Edit</button>\'});
            $("storelocator_style_config_map_custom_style").insert({before: \'<pre id="pretty-json-style" class="pretty-json-style" style="width: 78%;height: 250px;overflow: auto;"></pre>\'});
            $("btn-toggle-json-style").observe("click",function (){
                if ($("storelocator_style_config_map_custom_style").getAttribute("hidden")) {
                    $("storelocator_style_config_map_custom_style").removeAttribute("hidden");
                    $("pretty-json-style").setAttribute("hidden", "1");
                    this.innerHTML = "Pretty";
                } else {
                    $("storelocator_style_config_map_custom_style").setAttribute("hidden", "1");
                    $("pretty-json-style").removeAttribute("hidden");
                    this.innerHTML = "Edit";
                    prettyJson();
                }
            });

            var mapOptions = {zoom: 11,center: new google.maps.LatLng(40.6700, -73.9400),};
            var mapElement = document.getElementById("map");
            map = new google.maps.Map(mapElement, mapOptions);
            customMapStyle();
        });

        $("storelocator_style_config_map_style").observe("change",function(){
            map.setOptions({styles : JSON.parse(mapStyles[$("storelocator_style_config_map_style").value])});
        });
        $("storelocator_style_config-head").observe("click", customMapStyle);

        $("storelocator_style_config_map_custom_style").observe("keyup",customMapStyle);
        $("storelocator_style_config_use_available_style").observe("change",customMapStyle);

        </script>';
	}

}
