var MapManager = Class.create();
MapManager.prototype = {
    initialize: function(option) {

        this.map = new google.maps.Map(option.map, {
            zoom: 5,
            center: new google.maps.LatLng(0, 0),
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            styles: option.mapStyle
        });

        this.markers = [];
        this.markerClusterer = new MarkerClusterer(this.map, [], {
            gridSize: 10,
            maxZoom: 15
        }); 

        this.unit = option.unit;
        this.url_icon = option.url_icon;
        this.listInfo = option.listInfo;
        this.infoPopup = new google.maps.InfoWindow({
            content: "",
            maxWidth: 450,
            minWidth: 350
        });

        this.bounds = new google.maps.LatLngBounds();
        this.boundsAllStore = new google.maps.LatLngBounds();
        this.count = 0;
        this.numAllStore = option.stores.length;
        this.countLabel = option.countLabel;

        //for search Direction
        this.dirService = new google.maps.DirectionsService();
        this.dirDisplay = new google.maps.DirectionsRenderer({
            draggable: true,
            map: this.map
        });

        //for search Street View
        this.panorama = new google.maps.StreetViewPanorama(option.map, {
            enableCloseButton: true,
            visible: false
        });

        this.streetViews = new google.maps.StreetViewService();
        //for search Ditance
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

        this.circleMarker = new google.maps.Marker({
            icon: option.circleMarkerIcont
        });

        this.directionHandleListener = this.directionHandleListener.bindAsEventListener(this);
        
        this.createMarkers(option.stores);
        this.fitBounds();
        this.map.controls[google.maps.ControlPosition.LEFT_TOP].push($('box-view'));
    },
    getDirectionDom: function() {
        if (this.directionDom) {
            return this.directionDom;
        } else {
            var directionDom = $('option-direction');
            directionDom.show();
            var autocomplete = new google.maps.places.Autocomplete(directionDom.down('.form-control.start'));

            directionDom.down('.swap-locations').observe('click', (function() {
                var start = directionDom.down('.form-control.start'),
                    end = directionDom.down('.form-control.end');

                start.insert({
                    before: end
                });

                start.removeClassName('start');
                start.addClassName('end');
                end.removeClassName('end');
                end.addClassName('start');

            }).bind(this));

            var directionHandleListener = this.directionHandleListener;

            directionDom.select('.travel').invoke('observe', 'click', function() {
                if (!this.hasClassName('active')) {
                    directionDom.select('.travel').invoke('removeClassName', 'active');
                    this.addClassName('active');
                };
                directionHandleListener();
            });

            directionDom.down('.btn-go-direction').observe('click', this.directionHandleListener);
            google.maps.event.addListener(autocomplete, 'place_changed', this.directionHandleListener);
            directionDom.down('.swap-locations').observe('click', this.directionHandleListener);

            this.directionDom = directionDom;
            return this.directionDom;
        }
    },
    directionHandleListener: function() {
        var directionDom = this.getDirectionDom(),
            start = directionDom.down('.form-control.start'),
            end = directionDom.down('.form-control.end'),
            travelMode = directionDom.down('.travel.active').getAttribute('value'),
            panelElement = directionDom.down('.directions-panel');
        var directionStart, directionEnd;

        if (start.hasAttribute('latitude')) {
            directionStart = new google.maps.LatLng(start.getAttribute('latitude'), start.getAttribute('longtitude'));
            directionEnd = end.value;
            if (end.value == '') {
                return;
            }
        } else {
            directionStart = start.value;
            directionEnd = new google.maps.LatLng(end.getAttribute('latitude'), end.getAttribute('longtitude'));
            if (start.value == '') {
                return;
            }
        }

        this.getDirection(directionStart, directionEnd, travelMode, panelElement);
    },
    createMarkers: function(stores) {
        stores.each(function(el, index) {
            var marker = new google.maps.Marker({
                map: this.map,
                position: new google.maps.LatLng(el.latitude, el.longtitude),
                icon: (el.image_icon != null && el.image_icon != '') ? this.url_icon.replace('{id}', el.storelocator_id).replace('{icon}', el.image_icon) : null,
                country: el.country,
                state: el.state,
                state_id: el.state_id,
                zipcode: el.zipcode,
                city: el.city,
                radius: null,
                isShow: true,
                element: this.listInfo[index],
                storelocator_id: el.storelocator_id
            });

            marker.element.down('.street-view').observe('click', (function() {
                this.streetView(marker);
            }).bind(this));

            var directionDom = this.getDirectionDom();
            marker.element.down('.direction').observe('click', (function() {
                if (!marker.element.down('#option-direction')) {
                    marker.element.down('.top-box').insert({
                        after: directionDom
                    });
                    this.dirDisplay.setMap(null);
                    directionDom.down('.directions-panel').update('');
                    directionDom.down('.customer-location').value = '';
                } else if (directionDom.style.display == 'none') {
                    directionDom.show();
                } else {
                    directionDom.hide();
                }
                directionDom.down('.store-location').value = marker.element.down('.address-store').innerHTML;
                directionDom.down('.store-location').setAttribute('latitude', marker.getPosition().lat());
                directionDom.down('.store-location').setAttribute('longtitude', marker.getPosition().lng());

            }).bind(this));
            marker.element.setAttribute('index', index);
            google.maps.event.addListener(marker, 'click', function() {
                this.showPopup(marker);
            }.bind(this));
            marker.element.observe('click', function() {
                this.showPopup(marker);
            }.bind(this));
            this.boundsAllStore.extend(marker.getPosition());
            this.markers.push(marker);
//            this.markerClusterer.addMarker(marker);
            this.count++;
        }.bind(this));
        this.updateCountLabel();
        this.map.fitBounds(this.boundsAllStore);
    },
    clearMarker: function() {
        this.count = 0;
//        this.markerClusterer.clearMarkers();
        this.markers.each(function(marker) {
            if (marker.isShow) {
                marker.isShow = false;
                marker.setMap(null);
                marker.element.hide();
            }
        }.bind(this));

    },
    reset: function() {
        this.removeCycle();
        this.markers.each(function(marker) {
            if (!marker.isShow) {
                this.showMarker();
            }
        }.bind(this));
    },
    resetMap: function() {
        this.removeCycle();
        this.clearMarker();
        $$('.input-location input').invoke('setValue', '');
        $$('#list-tag-ul input:checkbox').each(function(el) {
            el.checked = false;
        });
        if (this.directionDom.parentElement) {
            this.directionDom.remove();
        }

        this.markers.each(function(marker) {
            this.showMarker(marker);
        }.bind(this));
        this.map.fitBounds(this.boundsAllStore);
        this.updateNumnstore(this.numAllStore);
    },
    filterbyTag: function() {
        this.removeCycle();
        this.clearMarker();
        var storeIds = '';
        $$('#list-tag-ul input:checkbox:checked').each(function(el) {
            storeIds += ',' + el.value;
        });
        var arrayIds = storeIds.split(',');
        if (arrayIds.length == 1 && arrayIds[0] == "") {
            this.resetMap();
        } else {
            this.markers.each((function(marker) {
                if (arrayIds.indexOf(marker.storelocator_id) == -1) {
                    this.hideMarker(marker);
                } else {
                    this.showMarker(marker);
                }
            }).bind(this));
        }
        this.fitBounds();
//        this.updateNumnstore(this.markerClusterer.getTotalMarkers());
        this.updateNumnstore($$('.el-content').findAll(function(el) { return el.visible(); }).length);
    },
    filterByArea: function() {
        this.removeCycle();
        this.clearMarker();
        $$('#list-tag-ul input:checkbox').each(function(el) {
            el.checked = false;
        });
        var options = this.getOptionFilterByArea();
        this.markers.each((function(marker) {
            if (this.checkShowFilterByArea(options, marker)) {
                this.showMarker(marker);
            } else {
                this.hideMarker(marker);
            }
        }).bind(this));
        this.fitBounds();
//        this.updateNumnstore(this.markerClusterer.getTotalMarkers());
        this.updateNumnstore($$('.el-content').findAll(function(el) { return el.visible(); }).length);
    },
    checkShowFilterByArea: function(options, marker) {

        if (options['country'] && options['country'] != marker['country']) {
            return false;
        }

//        if (options['state_id'] && options['state_id'] != marker['state_id']) {
//            return false;
//        }

        if (options['state'] && marker['state'] && marker['state'].indexOf(options['state']) == -1) {
            return false;
        }

        if (options['city'] && marker['city'].indexOf(options['city']) == -1) {
            return false;
        }

        if (options['zipcode'] && marker['zipcode'].indexOf(options['zipcode']) == -1) {
            return false;
        }

        return true;
    },
    getOptionFilterByArea: function() {
        var critia = ['country', 'city', 'zipcode'],
            options = {};

        for (var i = 0; i < critia.length; i++) {
            var input = $('form-search-area').down('[searchtype=' + critia[i] + ']');
            if (input && input.value != '') {
                options[critia[i]] = input.value;
            }
        }
        var inputState = $('form-search-area').down('input[searchtype=state]'),
            selectState = $('form-search-area').down('select[searchtype=state]');

        if (inputState && selectState) {
            options['state'] = selectState.value;
        }
        return options;
    },
    fitBounds: function() {
        this.bounds = new google.maps.LatLngBounds();
        this.markers.each(function(marker) {
            if (marker.isShow)
                this.bounds.extend(marker.getPosition());
        }.bind(this));
        this.map.fitBounds(this.bounds);
        if(this.map.getZoom()>15)
            this.map.setZoom(15);
    },
    showPopup: function(marker) {
        var element = marker.element.clone(true);
        element.addClassName('inforwindow');

        if (element.down('.custom-popup')) {
            element.down('.custom-popup').remove();
        }
        element.down('.street-view').observe('click', (function() {
            this.streetView(marker);
        }).bind(this));

        var directionDom = this.getDirectionDom();
        element.down('.direction').observe('click', (function() {
            if (!element.down('#option-direction')) {
                element.down('.top-box').insert({
                    after: directionDom
                });
                this.dirDisplay.setMap(null);
                directionDom.down('.directions-panel').update('');
                directionDom.down('.customer-location').value = '';
            } else if (directionDom.style.display == 'none') {
                directionDom.show();
            } else {
                directionDom.hide();
            }
        }).bind(this));
        directionDom.down('.store-location').value = element.down('.address-store').innerHTML;
        directionDom.down('.store-location').setAttribute('latitude', marker.getPosition().lat());
        directionDom.down('.store-location').setAttribute('longtitude', marker.getPosition().lng());

        marker.setMap(this.map);
        this.infoPopup.setContent(element);
        this.infoPopup.open(this.map, marker);
    },
    showMarker: function(marker) {
        if (!marker.isShow) {
            marker.isShow = true;
            marker.setMap(this.map)
            marker.element.show();
            marker.element.up().insert({
                top: marker.element
            });
//            this.markerClusterer.addMarker(marker);
            this.count++;
        }
    },
    hideMarker: function(marker) {
        if (marker.isShow) {
            marker.setMap(null);
            marker.isShow = false;
            marker.element.hide();
//            this.markerClusterer.removeMarker(marker);
            this.count--;
        }
    },
    filterMarker: function(func) {
        this.clearMarker();
        this.markers.each(function(el) {
            if (func(el))
                this.showMarker(el);
        }.bind(this));
    },
    updateNumnstore: function(numstore) {
        var countLabel = '';
        switch (numstore) {
            case 0:
                countLabel = storeTranslate.noneStore;
                break;
            case 1:
                countLabel = storeTranslate.oneStore;
                break;
            default:
                countLabel = numstore + storeTranslate.moreStore;
                break;
        }
        this.countLabel.update(countLabel);
    },
    updateCountLabel: function() {
        var countLabel = '';
        switch (this.count) {
            case 0:
                countLabel = storeTranslate.noneStore;
                break;
            case 1:
                countLabel = storeTranslate.oneStore;
                break;
            default:
                countLabel = this.count + storeTranslate.moreStore;
                break;
        }
        this.countLabel.update(countLabel);
    },
    //search by radius
    drawCycle: function(center, radius) {
        this.removeCycle();
        this.circleMarker.setPosition(center);
        this.circleMarker.setMap(this.map);
        this.circle.setMap(this.map);
        this.circle.setRadius(radius);
        this.circle.bindTo('center', this.circleMarker, 'position');
        this.map.setCenter(center);
        this.map.setZoom(Math.round(15 - Math.log(radius / this.unit.value) / Math.LN2));
    },
    removeCycle: function() {
        if (this.circle.getMap()) {
            this.circleMarker.setMap(null);
            this.circle.setMap(null);
        }
    },
    codeAddress: function(address, radius) {
        if (address === '') {
            alert(storeTranslate.enterLocation);
        } else {
            this.geocoder.geocode({
                address: address
            }, function(results, status) {
                if (status === google.maps.GeocoderStatus.OK) {
                    this.clearMarker();
                    var radius1 = radius * this.unit.value;
                    this.setRadius(results[0].geometry.location);
                    this.drawCycle(results[0].geometry.location, radius1);
                    this.filterMarker(function(el) {
                        return el.radius <= radius1;
                    });
                    this.updateCountLabel();
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
            this.clearMarker();
            this.filterMarker(function(el) {
                return el.radius <= radius1;
            });
            this.updateCountLabel();
        }
    },
    setRadius: function(location) {
        this.markers.each(function(marker) {
            marker.radius = google.maps.geometry.spherical.computeDistanceBetween(location, marker.getPosition());
            marker.element.down('.tag-store span').update(Math.round(marker.radius / this.unit.value * 100) / 100 + ' ' + this.unit.label);
        }.bind(this));
        this.markers.sort(function(a, b) {
            return b.radius - a.radius;
        });
    },
    //street view
    streetView: function(marker) {
        this.streetViews.getPanorama({
            location: marker.position,
            radius: 50
        }, this.processSVData.bind(this));
    },
    processSVData: function processSVData(data, status) {
        this.panorama.setVisible(false);
        if (status === google.maps.StreetViewStatus.OK) {
            this.panorama.setPano(data.location.pano);
            this.panorama.setPov({
                heading: 270,
                pitch: 0
            });
            this.panorama.setVisible(true);
        } else {
            window.alert(storeTranslate.streetNotFound);
        }
    },
    getDirection: function(start, end, travelMode, panelElement) {
        this.dirDisplay.setMap(this.map);
        this.dirDisplay.setPanel(panelElement);
        this.dirService.route({
            origin: start,
            destination: end,
            travelMode: google.maps.TravelMode[travelMode],
            unitSystem: (this.unit.label == 'km') ? google.maps.UnitSystem.METRIC : google.maps.UnitSystem.IMPERIAL
        }, function(response, status) {
            if (status === google.maps.DirectionsStatus.OK) {
                this.dirDisplay.setDirections(response);
            } else {
                window.alert(storeTranslate.directionFailded + status);
            }
        }.bind(this));
        if($$('.adp-directions').length)
            $$('.adp-directions').first().up().style.width ='101px';
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
                        if($('form-search-distance'))
                            $$('#form-search-distance input.form-control').first().value = results[0]['formatted_address'];
                    }
                });
                infoPopup.setMap(this.map);
                this.map.setZoom(13);
                this.map.setCenter(pos);
            }.bind(this)),
            function() {
                infoPopup.setPosition(this.map.getCenter());
                infoPopup.setContent(true ?storeTranslate.geoLocationFailded :storeTranslate.geoLocationBrower);
            };
        }
    },
    getStateByCountry:function(country){
        var states = [];
        this.markers.each(function(marker){
            if(marker.country==country&&marker.state!=''&&states.indexOf(marker.state)==-1){
                states.push(marker.state);
            }
        });
        return states;
    }
};

function showDistance() {
    if (!$('search-distance').hasClassName('active')) {
        $('search-distance').addClassName('active');
        $('form-search-distance').removeClassName('hide');
        $('form-search-area').addClassName('hide');
        $('search-area').removeClassName('active');
    }
}

function showArea() {
    if (!$('search-area').hasClassName('active')) {
        $('search-area').addClassName('active');
        $('form-search-area').removeClassName('hide');
        $('form-search-distance').addClassName('hide');
        $('search-distance').removeClassName('active');
    }
}
