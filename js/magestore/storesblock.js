var Storeblock = Class.create();
Storeblock.prototype = {
    initialize: function(option) {
        this.map        = new google.maps.Map(option.map, {
            zoom: 5,
            center: new google.maps.LatLng(0, 0),
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            styles: option.mapStyle
        });
        this.url_icon   = option.url_icon;
        this.markers    = this.createMarkers(option.stores);
        this.infoPopup  = new google.maps.InfoWindow({
            content : option.storeInfo.show(),
            maxWidth: 300,
            minWidth: 250
        });
        this.geocoder = new google.maps.Geocoder();
        this.circle = new google.maps.Circle({
            map: null,
            radius: 0,
            fillColor: '#cd003a',
            fillOpacity: 0.1,
            strokeColor: "#000000",
            strokeOpacity: 0.3,
            strokeWeight: 1
        });
        this.zoom = option.zoom;
        this.radius = option.deaultRadius;
        this.circleMarker = new google.maps.Marker({
            icon: option.circleIcon
        });
        this.unit       = option.unit;
        this.baseUrl    = option.baseUrl;
        this.addControler(option.controller,option.deaultRadius);
        this.fitBounds();
        this.addCurrentPosition(option.mylocation);
    },
    addControler: function(controller,deaultRadius){
        this.map.controls[google.maps.ControlPosition.RIGHT_TOP].push(controller);
        new google.maps.places.Autocomplete(controller.down('input'));
        new PeriodicalExecuter(function(pe) {
            if ($$('#map .search-by-distance').length) {
                var array = [1];
                for (i = 1; i <= 100; i++){array.push(i);}
                $$('#map .search-by-distance').first().show();
                new Control.Slider('handle1', 'track1', {
                    range: $R(1, 100), values: array, sliderValue: deaultRadius,
                    onChange: function (v) {
                        this.changeRadius(v);
                        $('range-slider-label').update(v + this.unit.label);
                    }.bind(this),
                    onSlide: function (v) {
                        this.changeRadius(v);
                        $('range-slider-label').update(v + this.unit.label);
                    }.bind(this)
                });
                pe.stop();
            }
        }.bind(this), 1);
    },
    createMarkers: function(stores) {
        var markers = [];
        stores.each(function(store) {
            var marker = new google.maps.Marker({
                map: this.map,
                position: new google.maps.LatLng(store.latitude, store.longtitude),
                icon: (store.image_icon != null && store.image_icon != '') ? this.url_icon.replace('{id}', store.storelocator_id).replace('{icon}', store.image_icon) : null,
                options: store
            });
            markers.push(marker);
            google.maps.event.addListener(marker, 'click', function() {
                this.showPopup(marker);
            }.bind(this));
        }.bind(this));
        return markers;
    },
    fitBounds: function(){
        var bounds = new google.maps.LatLngBounds();
        this.markers.each(function(marker){
            if(marker.getMap())
                bounds.extend(marker.getPosition());
        }.bind(this));
        this.map.fitBounds(bounds);
    },
    showPopup: function(marker) {
        var content = this.infoPopup.getContent();
        content.down('a').href = this.baseUrl + marker.rewrite_request_path;
        content.down('.view-detail').update(marker.name);
        content.down('.address-store').update(marker.address + ' ' + marker.state + ' ' + marker.country);
        content.down('.phone-store').update(marker.phone);
        this.infoPopup.setContent(content);
        this.infoPopup.open(this.map, marker);
    },
    setRadius: function(location) {
        this.markers.each(function(marker) {
            marker.radius = google.maps.geometry.spherical.computeDistanceBetween(location, marker.getPosition());
        }.bind(this));
        this.markers.sort(function(a, b) {
            return b.radius - a.radius;
        });
    },
    showMarkerByRadius: function(radius){
        this.markers.each(function(marker) {
            if(marker.radius<=radius){
                if(!marker.getMap())
                    marker.setMap(this.map);
            }else if(marker.getMap()){
                marker.setMap(null);
            }else{
                return;
            }
        }.bind(this));
    },
    codeAddress: function(address) {
        if (address === '') {
            alert(storeTranslate.enterLocation);
        } else {
            this.geocoder.geocode({
                address: address
            }, function(results, status) {
                if (status === google.maps.GeocoderStatus.OK) {
                    this.map.setCenter(results[0].geometry.location);
                    this.map.setZoom(this.zoom);
//                    var radius1 = this.radius * this.unit.value;
//                    this.setRadius(results[0].geometry.location);
//                    this.drawCycle(results[0].geometry.location, radius1);
                } else {
                    alert(storeTranslate.geocodeMissuccess + status);
                }
            }.bind(this));
        }

    },
    changeRadius: function(radius) {
        if (this.circle.getMap()) {
            var radius1 = radius * this.unit.value;
            this.circle.setRadius(radius1);
            this.showMarkerByRadius(radius1);
            this.radius = radius;
            this.map.setZoom(Math.round(15 - Math.log(radius1 / this.unit.value) / Math.LN2));
        }
    },
    drawCycle: function(center, radius) {
        this.removeCycle();
        this.circleMarker.setPosition(center);
        this.circleMarker.setMap(this.map);
        this.circle.setMap(this.map);
        this.circle.setRadius(radius);
        this.circle.bindTo('center', this.circleMarker, 'position');
        this.map.setCenter(center);
        this.map.setZoom(Math.round(15 - Math.log(radius / this.unit.value) / Math.LN2));
        this.showMarkerByRadius(radius);
    },
    removeCycle: function() {
        if (this.circle.getMap()) {
            this.circleMarker.setMap(null);
            this.circle.setMap(null);
        }
    },
    reset: function(){
        this.removeCycle();
        var bounds = new google.maps.LatLngBounds();
        this.markers.each(function(marker){
            if(!marker.getMap()){
                marker.setMap(this.map);
            }
            bounds.extend(marker.getPosition());
        }.bind(this));
        this.map.fitBounds(bounds);
    },
    addCurrentPosition: function(mylocation){
        this.map.controls[google.maps.ControlPosition.RIGHT_TOP].push(mylocation);
        mylocation.observe('click',this.currentPosition.bind(this));
    },
    currentPosition: function() {
        var map = this.map;
        infoPopup = new google.maps.InfoWindow({
            content: "",
            maxWidth: 293
        });
        geocoder = new google.maps.Geocoder();
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                var latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                infoPopup.setPosition(pos);
                geocoder.geocode({
                    latLng: latlng
                }, function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        infoPopup.setContent(results[0]['formatted_address']);
                        if($('search_address'))
                            $('search_address').value = results[0]['formatted_address'];
                    }
                });
                infoPopup.setMap(this.map);
                this.map.setZoom(13);
                this.map.setCenter(pos);
            }.bind(this)),
            function() {
                infoPopup.setPosition(this.map.getCenter());
                infoPopup.setContent(true ?storeTranslate.geoLocationFailded :storeTranslate.geoLocationBrower);
            }.bind(this);
        }
    }
    
};


