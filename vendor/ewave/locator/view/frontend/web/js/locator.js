/* global google, MarkerClusterer */
define([
    'jquery',
    'mage/template',
    'Ewave_Locator/js/action/set-locations',
    'Magento_Ui/js/modal/modal',
    'Magento_Ui/js/lib/core/events',
    'text!Ewave_Locator/template/info-box.html',
    'jquery/ui',
    'jquery/validate'
], function ($, mageTemplate, setLocations, modal, events, infoBoxTmpl) {
    'use strict';
    
    $.widget('ewave.locator', {
        options: {
            google: {
                key: '',
                libraries: '&libraries=places,geometry'
            },
            entityName: 'store',
            defaultAddress: 'Australia',
            defaultLocations: {},
            search: {
                form: '.locator-search .form',
                term: '.locator-search .input-search',
                radius: '.locator-search .radius',
                onLoad: false
            },
            map: {
                id: 'locator-map',
                settings: {}
            },
            mapInline: {
                enable: false,
                item: {}
            },
            marker: {
                useClustering: true,
                clusteringOptions: {
                    imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'
                },
                settings: {}
            },
            radius: {
                enable: true,
                default: 25,
                extensible: false,
                allMarkers: false,
                settings: {},
                conversionConstant: 1.609344,
                metricType: 'km'
            },
            infoBox: {
                template: infoBoxTmpl,
                extraData: {}
            },
            openInPopup: false,
            popupSelector: '[data-role=storelocator-popup]',
            popupOptions: {}
        },
        _create: function () {
            this.isLoad = false;
            this.isIgnoreRadius = false;
            this.isMarkerClusterReady = false;
            this.list = [];

            if (window.ewaveGoogleMapsUrl || this.options.google.key) {
                this._loadGoogleApi(window.ewaveGoogleMapsUrl || '//maps.googleapis.com/maps/api/js?key=' + this.options.google.key + this.options.google.libraries);
            } else {
                console.warn('Google Map hasn\'t been loaded');
            }

            this._bind();

            if (this.options.search.onLoad) {
                this._searchOnLoad();
            }
        },
        _bind: function () {
            var self = this;
            $(this.options.search.form).on('submit', function (e) {
                var $this = $(this);
                e.preventDefault();
                if ($this.valid() && !self.isLoad) {
                    self._search($this);
                }
            });

            if (this.options.openInPopup) {
                this.modal = modal(this.options.popupOptions, $(this.options.popupSelector));
                events.on('location.show', this.modal.openModal.bind(this.modal));
            }
        },
        _loadGoogleApi: function (mapUrl) {
            require([mapUrl], function () {
                this._initMap();
                this.initAutoComplete(this.options.search.term);
                this._setInlineMap();
            }.bind(this), function () {
                console.error('Failed to load Google Maps API');
                // to trigger search if necessary
                $(document).trigger('locator.map.initialized');
            });
        },
        initAutoComplete: function (field) {
            if ($(field).length) {
                this.autocomplete = new google.maps.places.Autocomplete((document.querySelector(field)), {types: ['geocode']});
            }
        },
        _initMap: function () {
            var options = $.extend({}, this.getMapSettings(), this.options.map.settings);

            if (this.options.openInPopup) {
                events.on('location.show', function (location, settings) {
                    var center = new google.maps.LatLng(location.latitude, location.longitude);

                    this.renderOnMap([location], center, settings);
                }.bind(this));
            }

            this.markers = [];
            this.map = new google.maps.Map(document.getElementById(this.options.map.id), options);
            this.geocoder = new google.maps.Geocoder();
            this.infoWindow = new google.maps.InfoWindow();
            this.bounds = new google.maps.LatLngBounds();

            this.setDefaultCountry();
            $(document).trigger('locator.map.initialized');
        },
        getMap: function () {
            return this.map;
        },
        getMapSettings: function () {
            return {
                zoom: 4,
                center: {lat: -25.271027, lng: 133.595936}
            };
        },
        setDefaultCountry: function () {
            if (!$.isEmptyObject(this.options.defaultLocations)) {
                var entity = this.options.defaultLocations[this.options.entityName],
                    items = entity.items,
                    settings = entity.settings;

                Array.prototype.push.apply(this.list, items);
                setLocations(this.list, settings);

                if (this.options.openInPopup) return;

                this.geocoder.geocode({address: this.options.defaultAddress}, function (results, status) {
                    if (status === google.maps.GeocoderStatus.OK) {
                        this.isIgnoreRadius = true;
                        this.setMarkers(items, results[0].geometry.location, settings);
                        this.isIgnoreRadius = false;
                        this.map.setCenter(results[0].geometry.location);
                        this.defaultBounds = results[0].geometry.viewport;
                        if (this.autocomplete) {
                            this.autocomplete.setBounds(this.defaultBounds);
                        }
                        this.map.fitBounds(this.defaultBounds);
                    }
                }.bind(this));
            }
        },
        _search: function ($form) {
            var term = $(this.options.search.term).val();

            this.geocoder.geocode({address: term}, function (results, status) {
                if (status === google.maps.GeocoderStatus.OK) {
                    this.sendRequest($form, term, results[0].geometry.location);
                } else {
                    this.clearLocations();
                    this.clearMap();
                    setLocations([], {});
                    console.warn(term + ' not found');
                }
            }.bind(this));
        },
        _searchOnLoad: function () {
            var self = this;
            $(this.options.search.term).val(this.options.defaultAddress);

            if (this.options.openInPopup) {
                this._search($(this.options.search.form));
                return;
            }

            $(document).on('locator.map.initialized', function () {
                self._search($(self.options.search.form));
            });
        },

        _getDefaultSearchParams: function ($form, term, location) {
            return {
                isAjax: true,
                blockName: $form.data('locator'),
                search: term, // TODO: param must be searchTerm, leave for now for backward compatibility
                latitude: location.lat(),
                longitude: location.lng(),
                radius: this.getConvertedDistance()
            };
        },

        getConvertedDistance: function (data) {
            var distance = data || $(this.options.search.radius).val();
            return this.options.radius.metricType === 'km' ? distance : distance * this.options.radius.conversionConstant;
        },

        sendRequest: function ($form, term, location) {
            var self = this,
                defaultParams = this._getDefaultSearchParams($form, term, location),
                formData = $form.serializeArray().reduce(function (res, v) {
                    if (v.name === 'radius') {
                        v.name = self.getConvertedDistance(v.value);
                    } else {
                        res[v.name] = v.value;
                    }
                    return res;
                }, defaultParams);

            self.isLoad = true;

            $.ajax({
                url: $form.attr('action'),
                type: 'GET',
                dataType: 'json',
                showLoader: true,
                data: formData,
                success: function (response) {
                    self.renderLocations(response.result[self.options.entityName], location);
                },
                error: function (xhr) {
                    console.warn('Search failed: ', xhr.statusText);
                },
                complete: function () {
                    self.isLoad = false;
                }
            });
        },
        _setInlineMap: function () {
            var item = this.options.mapInline.item;
            if (this.options.mapInline.enable && item.latitude && item.longitude) {
                this.list.push(item);
                this.initMarker(item, { lat: +item.latitude, lng: +item.longitude });
                this.fitMarkers();
            }
        },
        renderLocations: function (entity, center) {
            var items = entity.items,
                settings = entity.settings;

            this.clearLocations();
            Array.prototype.push.apply(this.list, items);
            setLocations(this.list, settings);

            // render on map explicitly
            if (this.options.openInPopup) return;

            if (this.map !== undefined) {
                this.renderOnMap(items, center, settings);
            }
        },
        renderOnMap: function (items, center, settings) {
            this.clearMap();
            this.setMarkers(items, center, settings);
            this.initCircle(center);
            this.fitLocations();
        },
        setMarkers: function (items, center, settings) {
            var self = this;

            $.each(items, function (index, item) {
                self.createMarker(item, center, settings);
                
                if (item.child_items) {
                    $.each(item.child_items, function (i, child) {
                        self.createMarker(child, center, settings);
                    });
                }
            });

            this.setMarkerClustering();
        },
        createMarker: function (item, center, settings) {
            var latlng = new google.maps.LatLng(item.latitude, item.longitude);

            if (this.options.radius.enable && !this.isIgnoreRadius) {
                if (this.options.radius.allMarkers || (this.calculateDistance(center, latlng) <= this.getRadius())) {
                    this.initMarker(item, latlng, settings);
                }
            } else {
                this.initMarker(item, latlng, settings);
            }
        },
        initMarker: function (item, latlng) {
            var self = this,
                options = $.extend({}, this.getMarkerSettings(item, latlng), this.options.marker.settings),
                marker = new google.maps.Marker(options);

            google.maps.event.addListener(marker, 'click', function () {
                self.infoWindow.setContent(self.getInfoBoxTemplate(item));
                self.infoWindow.open(self.map, marker);
            });
            this.markers.push(marker);
            this.bounds.extend(latlng);
        },
        getMarkerSettings: function (item, latlng) {
            return {
                map: this.map,
                position: latlng,
                title: item.name
            };
        },
        initCircle: function (center) {
            if (this.options.radius.enable) {
                this.extendRadius(this.markers);
                var options = $.extend({}, this.getCircleSettings(center), this.options.radius.settings);
                this.circle = new google.maps.Circle(options);
                this.markers.push(this.circle);
            }
        },
        getCircleSettings: function (center) {
            return {
                center: center,
                radius: this.getRadius(),
                map: this.map
            };
        },
        getInfoBoxTemplate: function (item, settings) {
            return mageTemplate(this.options.infoBox.template, {
                data: item,
                settings: settings,
                extra: this.options.extraData
            });
        },
        calculateDistance: function (center, latlng) {
            return google.maps.geometry.spherical.computeDistanceBetween(center, latlng);
        },
        compareDistance: function (a, b) {
            if (a.distance < b.distance) {
                return -1;
            }
            if (a.distance > b.distance) {
                return 1;
            }
            return 0;
        },
        getRadius: function () {
            var $radius = $(this.options.search.radius);
            if ($radius.length && $radius.val()) {
                return this.convertRadius($radius.val());
            }
            return this.convertRadius(this.options.radius.default);
        },
        /**
         * Convert radius to meters
         * @param radius
         * @returns {number}
         */
        convertRadius: function (radius) {
            return this.options.radius.metricType === 'km' ? radius * 1000 : radius * 1000 * this.options.radius.conversionConstant;
        },
        extendRadius: function (markers) {
            var $radius = $(this.options.search.radius),
                $nextRadius;
            if (this.options.radius.extensible && !markers.length && $radius.length) {
                $nextRadius = $radius.find('option:selected').next();
                if ($nextRadius.length) {
                    $nextRadius.attr('selected', 'selected');
                    this._search($(this.options.search.form));
                }
            }
        },
        /**
         * Fit and Pan marker(s) and circle to Bounds
         */
        fitLocations: function () {
            if (this.options.radius.enable) {
                if (this.options.radius.allMarkers) {
                    this.fitMarkers();
                } else {
                    this.map.fitBounds(this.circle.getBounds());
                    this.map.panToBounds(this.circle.getBounds());
                }
            } else if (this.markers.length) {
                this.fitMarkers();
            }
        },
        /**
         * Fit and Pan marker(s) to Bounds
         */
        fitMarkers: function () {
            this.map.fitBounds(this.bounds);
            this.map.panToBounds(this.bounds);
        },
        /**
         * Clear previously displayed locations
         */
        clearLocations: function () {
            this.list.length = 0;
        },
        clearMap: function () {
            this.infoWindow.close();
            for (var i = 0; i < this.markers.length; i++) {
                this.markers[i].setMap(null);
            }

            this.markers.length = 0;
            if (this.markerCluster) {
                this.markerCluster.clearMarkers();
            }
        },
        loadMarkerClustering: function () {
            if (this.options.marker.useClustering) {
                require(['markerClustering'], function (MarkerClusterer) {
                    this.isMarkerClusterReady = true;
                    this.clusterJS = MarkerClusterer;
                    this.initMarkerClustering(this.clusterJS);
                }.bind(this), function () {
                    console.error('Failed to load Marker Clustering');
                });
            }
        },
        initMarkerClustering: function (MarkerClusterer) {
            if (this.options.marker.useClustering) {
                this.markerCluster = new MarkerClusterer(this.map, this.markers, this.options.marker.clusteringOptions);
            }
        },
        setMarkerClustering: function () {
            if (this.isMarkerClusterReady) {
                this.initMarkerClustering(this.clusterJS);
            } else {
                this.loadMarkerClustering();
            }
        }
    });

    return $.ewave.locator;
});
