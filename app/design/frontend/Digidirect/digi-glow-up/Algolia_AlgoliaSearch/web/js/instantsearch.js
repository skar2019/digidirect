define([
    'uiComponent',
    'jquery',

    // Algolia core UI libs
    'algoliaSearchLib',
    'algoliaInstantSearchLib',

    // Algolia integration dependencies
    'algoliaCommon',
    'algoliaBase64',
    'algoliaTemplateEngine',

    // Magento core libs
    'Magento_Catalog/js/price-utils',

    'algoliaInsights',
    'algoliaHooks',
], function (Component, $, algoliasearch, instantsearch, algoliaCommon, algoliaBase64, templateEngine, priceUtils) {

    return Component.extend({
        initialize(config, element) {
            // console.log('IS initialized with', config, element);
            this.buildInstantSearch();
        },

        isStarted: false,

        /**
         * Initialize search results using Algolia's InstantSearch.js library v4
         * Docs: https://www.algolia.com/doc/api-reference/widgets/instantsearch/js/
         */
        async buildInstantSearch() {

            const templateProcessor = await templateEngine.getSelectedEngineAdapter();

            const mockAlgoliaBundle = this.mockAlgoliaBundle();

            if (!this.checkInstantSearchEnablement()) return;

            this.invokeLegacyHooks();

            this.setupWrapper(templateProcessor);

            const indexName = algoliaConfig.indexName + '_products';

            const instantsearchOptions = algoliaCommon.triggerHooks(
                'beforeInstantsearchInit',
                {
                    searchClient: algoliasearch(algoliaConfig.applicationId, algoliaConfig.apiKey),
                    indexName   : indexName,
                    routing     : algoliaCommon.routing,
                },
                mockAlgoliaBundle
            );

            const search = instantsearch(instantsearchOptions);

            search.client.addAlgoliaAgent(this.getAlgoliaAgent());

            /** Prepare sorting indices data */
            algoliaConfig.sortingIndices.unshift({
                name : indexName,
                label: algoliaConfig.translations.relevance,
            });

            // TODO: Revisit use of closures
            const currentRefinementsAttributes = this.getCurrentRefinementsAttributes();

            let allWidgetConfiguration = {
                infiniteHits: {},
                hits        : {},
                configure   : this.getSearchParameters(),
                custom      : [
                    /**
                     * Custom widget - this widget is used to refine results for search page or catalog page
                     * Docs: https://www.algolia.com/doc/guides/building-search-ui/widgets/create-your-own-widgets/js/
                     **/
                    {
                        getWidgetSearchParameters: function (searchParameters) {
                            if (
                                algoliaConfig.request.query.length > 0 &&
                                location.hash.length < 1
                            ) {
                                return searchParameters.setQuery(
                                    algoliaCommon.htmlspecialcharsDecode(algoliaConfig.request.query)
                                );
                            }
                            return searchParameters;
                        },
                        init                     : function (data) {
                            var page = data.helper.state.page;

                            if (algoliaConfig.request.refinementKey.length > 0) {
                                data.helper.toggleRefine(
                                    algoliaConfig.request.refinementKey,
                                    algoliaConfig.request.refinementValue
                                );
                            }

                            if (algoliaConfig.isCategoryPage) {
                                data.helper.addNumericRefinement('visibility_catalog', '=', 1);
                            } else {
                                data.helper.addNumericRefinement('visibility_search', '=', 1);
                            }

                            data.helper.setPage(page);
                        },
                        render                   : function (data) {
                            if (!algoliaConfig.isSearchPage) {
                                if (
                                    data.results.query.length === 0 &&
                                    data.results.nbHits === 0
                                ) {
                                    $('.algolia-instant-replaced-content').show();
                                    $('.algolia-instant-selector-results').hide();
                                } else {
                                    $('.algolia-instant-replaced-content').hide();
                                    $('.algolia-instant-selector-results').show();
                                }
                            }
                        },
                    },
                    /**
                     * Custom widget - Suggestions
                     * This widget renders suggestion queries which might be interesting for your customer
                     * Docs: https://www.algolia.com/doc/guides/building-search-ui/widgets/create-your-own-widgets/js/
                     **/
                    {
                        suggestions: [],
                        init       : function () {
                            if (algoliaConfig.showSuggestionsOnNoResultsPage) {
                                var $this = this;
                                $.each(
                                    algoliaConfig.popularQueries.slice(
                                        0,
                                        Math.min(4, algoliaConfig.popularQueries.length)
                                    ),
                                    function (i, query) {
                                        query = $('<div>').html(query).text(); //xss
                                        $this.suggestions.push(
                                            '<a href="' +
                                            algoliaConfig.baseUrl +
                                            '/catalogsearch/result/?q=' +
                                            encodeURIComponent(query) +
                                            '">' +
                                            query +
                                            '</a>'
                                        );
                                    }
                                );
                            }
                        },
                        render     : function (data) {
                            if (data.results.hits.length === 0) {
                                var content = '<div class="no-results">';
                                content +=
                                    '<div><b>' +
                                    algoliaConfig.translations.noProducts +
                                    ' "' +
                                    $('<div>').text(data.results.query).html() +
                                    '</b>"</div>';
                                content += '<div class="popular-searches">';

                                if (
                                    algoliaConfig.showSuggestionsOnNoResultsPage &&
                                    this.suggestions.length > 0
                                ) {
                                    content +=
                                        '<div>' +
                                        algoliaConfig.translations.popularQueries +
                                        '</div>' +
                                        this.suggestions.join(', ');
                                }

                                content += '</div>';
                                content +=
                                    algoliaConfig.translations.or +
                                    ' <a href="' +
                                    algoliaConfig.baseUrl +
                                    '/catalogsearch/result/?q=__empty__">' +
                                    algoliaConfig.translations.seeAll +
                                    '</a>';

                                content += '</div>';

                                $('#instant-empty-results-container').html(content);
                            } else {
                                $('#instant-empty-results-container').html('');
                            }
                        },
                    },
                ],
                /**
                 * stats
                 * Docs: https://www.algolia.com/doc/api-reference/widgets/stats/js/
                 **/
                stats: {
                    container: '#algolia-stats',
                    templates: {
                        text: function (data) {
                            data.first = data.page * data.hitsPerPage + 1;
                            data.last = Math.min(
                                data.page * data.hitsPerPage + data.hitsPerPage,
                                data.nbHits
                            );
                            data.seconds = data.processingTimeMS / 1000;
                            data.translations = window.algoliaConfig.translations;

                            // TODO: Revisit this injected jQuery logic
                            const searchParams = new URLSearchParams(window.location.search);
                            const searchQuery = searchParams.has('q') || '';
                            if (searchQuery === '' && !algoliaConfig.isSearchPage) {
                                $('.algolia-instant-replaced-content').show();
                                $('.algolia-instant-selector-results').hide();
                            } else {
                                $('.algolia-instant-replaced-content').hide();
                                $('.algolia-instant-selector-results').show();
                            }

                            const template = $('#instant-stats-template').html();
                            return templateProcessor.process(template, data);
                        },
                    },
                },
                /**
                 * sortBy
                 * Docs: https://www.algolia.com/doc/api-reference/widgets/sort-by/js/
                 **/
                sortBy: {
                    container: '#algolia-sorts',
                    items    : algoliaConfig.sortingIndices.map(function (sortingIndice) {
                        return {
                            label: sortingIndice.label,
                            value: sortingIndice.name,
                        };
                    }),
                },
                /**
                 * currentRefinements
                 * Widget displays all filters and refinements applied on query. It also let your customer to clear them one by one
                 * Docs: https://www.algolia.com/doc/api-reference/widgets/current-refinements/js/
                 **/
                currentRefinements: {
                    container: '#current-refinements',
                    // TODO: Remove this - it does nothing
                    templates         : {
                        item: $('#current-refinements-template').html(),
                    },
                    includedAttributes: currentRefinementsAttributes.map((attribute) => {
                        if (
                            attribute.name.indexOf('categories') === -1 ||
                            !algoliaConfig.isCategoryPage
                        )
                            // For category browse, requires a custom renderer to prevent removal of the root node from hierarchicalMenu widget
                            return attribute.name;
                    }),

                    transformItems: (items) => {
                        return (
                            items
                                // This filter is only applicable if categories facet is included as an attribute
                                .filter((item) => {
                                    return (
                                        !algoliaConfig.isCategoryPage ||
                                        item.refinements.filter(
                                            (refinement) =>
                                                refinement.value !== algoliaConfig.request.path
                                        ).length
                                    ); // do not expose the category root
                                })
                                .map((item) => {
                                    const attribute = currentRefinementsAttributes.filter((_attribute) => {
                                        return item.attribute === _attribute.name;
                                    })[0];
                                    if (!attribute) return item;
                                    item.label = attribute.label;
                                    item.refinements.forEach(function (refinement) {
                                        if (refinement.type !== 'hierarchical') return refinement;

                                        const levels = refinement.label.split(
                                            algoliaConfig.instant.categorySeparator
                                        );
                                        const lastLevel = levels[levels.length - 1];
                                        refinement.label = lastLevel;
                                    });
                                    return item;
                                })
                        );
                    },
                },

                /*
                 * clearRefinements
                 * Widget displays a button that lets the user clean every refinement applied to the search. You can control which attributes are impacted by the button with the options.
                 * Docs: https://www.algolia.com/doc/api-reference/widgets/clear-refinements/js/
                 **/
                clearRefinements: {
                    container         : '#clear-refinements',
                    templates         : {
                        resetLabel: algoliaConfig.translations.clearAll,
                    },
                    includedAttributes: currentRefinementsAttributes.map(function (attribute) {
                        if (
                            !(
                                algoliaConfig.isCategoryPage &&
                                attribute.name.indexOf('categories') > -1
                            )
                        ) {
                            return attribute.name;
                        }
                    }),
                    cssClasses        : {
                        button: ['action', 'primary'],
                    },
                    transformItems    : function (items) {
                        return items.map(function (item) {
                            var attribute = currentRefinementsAttributes.filter(function (_attribute) {
                                return item.attribute === _attribute.name;
                            })[0];
                            if (!attribute) return item;
                            item.label = attribute.label;
                            return item;
                        });
                    },
                },

                /*
                 * queryRuleCustomData
                 * The queryRuleCustomData widget displays custom data from Query Rules.
                 * Docs: https://www.algolia.com/doc/api-reference/widgets/query-rule-custom-data/js/
                 **/
                queryRuleCustomData: {
                    container: '#algolia-banner',
                    templates: {
                        default: '{{#items}} {{#banner}} {{{banner}}} {{/banner}} {{/items}}',
                    },
                },
            };

            if (algoliaConfig.instant.isSearchBoxEnabled) {
                /**
                 * searchBox
                 * Docs: https://www.algolia.com/doc/api-reference/widgets/search-box/js/
                 **/
                allWidgetConfiguration.searchBox = {
                    container  : '#instant-search-bar',
                    placeholder: algoliaConfig.translations.searchFor,
                    showSubmit : false,
                    queryHook  : (inputValue, search) => {
                        if (
                            algoliaConfig.isSearchPage &&
                            !algoliaConfig.request.categoryId &&
                            !algoliaConfig.request.landingPageId.length
                        ) {
                            $('.page-title-wrapper span.base').html(
                                algoliaConfig.translations.searchTitle +
                                ": '" +
                                algoliaCommon.htmlspecialcharsEncode(inputValue) +
                                "'"
                            );
                        }
                        return search(inputValue);
                    },
                };
            }

            if (algoliaConfig.instant.infiniteScrollEnabled === true) {
                /**
                 * infiniteHits
                 * This widget renders all products into result page
                 * Docs: https://www.algolia.com/doc/api-reference/widgets/infinite-hits/js/
                 **/
                allWidgetConfiguration.infiniteHits = {
                    container     : '#instant-search-results-container',
                    templates     : {
                        empty       : '',
                        item        : $('#instant-hit-template').html(),
                        showMoreText: algoliaConfig.translations.showMore,
                    },
                    cssClasses    : {
                        loadPrevious: ['action', 'primary'],
                        loadMore    : ['action', 'primary'],
                    },
                    transformItems: function (items) {
                        return items.map(function (item) {
                            item.__indexName = search.helper.lastResults.index;
                            item = algoliaCommon.transformHit(item, algoliaConfig.priceKey, search.helper);
                            item.isAddToCartEnabled = algoliaConfig.instant.isAddToCartEnabled;
                            return item;
                        });
                    },
                    showPrevious  : true,
                    escapeHits    : true,
                };

                delete allWidgetConfiguration.hits;
            } else {
                /**
                 * hits
                 * This widget renders all products into result page
                 * Docs: https://www.algolia.com/doc/api-reference/widgets/hits/js/
                 **/
                allWidgetConfiguration.hits = {
                    container     : '#instant-search-results-container',
                    templates     : {
                        empty: '',
                        item : $('#instant-hit-template').html(),
                    },
                    transformItems: function (items, {results}) {
                        if (
                            results.nbPages <= 1 &&
                            algoliaConfig.instant.hidePagination === true
                        ) {
                            document.getElementById(
                                'instant-search-pagination-container'
                            ).style.display = 'none';
                        } else {
                            document.getElementById(
                                'instant-search-pagination-container'
                            ).style.display = 'block';
                        }
                        return items.map(function (item) {
                            //console.log(item);
                            item.__indexName = search.helper.lastResults.index;
                            item = transformHit(item, algoliaConfig.priceKey, search.helper);
                            // FIXME: transformHit is a global
                            item.isAddToCartEnabled = algoliaConfig.instant.isAddToCartEnabled;

                            // Create our number formatter.
                            const formatter = new Intl.NumberFormat('en-AU', {
                                style: 'currency',
                                currency: 'AUD',
                                minimumFractionDigits: 2
                            });

                            //formatter.format(e.target.value);
                            item.hasCustomFinalPrice = false;
                            item.hasNoCustomFinalPrice = true;

                            if (item.marketplacer_seller == "digiDirect") {
                                item.isDigiMarket = false;
                                item.isDigiOnly = true;
                            } else {
                                item.isDigiMarket = true;
                                item.isDigiOnly = false;
                            }

                            if (item.preorder == "Yes") {
                                item.isPreorder = true;
                                item.isNotPreorder = false;
                            } else {
                                item.isPreorder = false;
                                item.isNotPreorder = true;
                            }


                            /*if (item.pre_order_status == "Yes") {
                                item.isPreorder = true;
                                item.isNotPreorder = false;
                            } else {
                                item.isPreorder = false;
                                item.isNotPreorder = true;
                            }*/

                            let categoryIds = item.categoryIds;
                            let digiSecondsIds = ["2564","2567","2570","2573"];

                            let hasMatch = categoryIds.some(cat => digiSecondsIds.includes(cat));
                            //console.log("hasMatch", hasMatch);

                            function decodeHtmlEntities(str) {
                                const txt = document.createElement('textarea');
                                txt.innerHTML = str;
                                return txt.value;
                            }

                            let categoriesWithoutPath = item.categories_without_path || '';
                            let firstCategory = categoriesWithoutPath.split(',')[0].trim();
                            firstCategory = decodeHtmlEntities(firstCategory) ;// ✅ decode &lt;mark&gt; → <mark>

                            item.firstCategory = firstCategory;

                            if (!hasMatch) {
                                item.isDigiSeconds = false;
                                item.isNotDigiSeconds = true;
                            } else {
                                item.isDigiSeconds = true;
                                item.isNotDigiSeconds = false;

                                if (item.item_condition == "PRELOVED") {
                                    item.digiSecondsBadge = "https://www.digidirect.com.au/media/wysiwyg/digiseconds/overlays/badge-pre-loved.png";
                                } else if (item.item_condition == "OPENBOX") {
                                    item.digiSecondsBadge = "https://www.digidirect.com.au/media/wysiwyg/digiseconds/overlays/badge-demo.png";
                                } else if (item.item_condition == "REFURB") {
                                    item.digiSecondsBadge = "https://www.digidirect.com.au/media/wysiwyg/digiseconds/overlays/badge-refurbished.png";
                                }

                                if (item.item_rating == 5) {
                                    item.digiSecondsRating= '<i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>';
                                } else if (item.item_rating == 4) {
                                    item.digiSecondsRating= '<i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i>';
                                } else if (item.item_rating == 3) {
                                    item.digiSecondsRating= '<i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>';
                                } else if (item.item_rating == 2) {
                                    item.digiSecondsRating= '<i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>';
                                } else if (item.item_rating == 1) {
                                    item.digiSecondsRating= '<i class="fas fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>';
                                } else {
                                    item.digiSecondsRating= '<i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>';
                                }
                            }
                            //ajaxFinalPrice
                            /*var ajaxFinalPrice;
                            var url = "https://www.digidirect.com.au/algoliaroute/index/finalprice";
                            $.ajax({
                                async: false,
                                url: url,
                                type: "POST",
                                data: {
                                    id: item.objectID
                                },
                                success: function(data){
                                    ajaxFinalPrice = data;
                                    console.log("ajaxFinalPrice", ajaxFinalPrice);
                                    if ((ajaxFinalPrice < item.price.AUD.default) && ajaxFinalPrice != 0) {
                                        item.ajaxFinalPrice = formatter.format(ajaxFinalPrice);
                                        item.customFinalPrice =  formatter.format(ajaxFinalPrice);
                                        item.discount = formatter.format(item.price.AUD.default - ajaxFinalPrice);
                                        item.hasCustomFinalPrice = true;
                                        item.hasNoCustomFinalPrice = false;
                                        console.log("ajaxFinalPrice!");
                                    }
                                    console.log("Success!", data);
                                },
                                    error: function(data){
                                    console.log("Error!", data);
                                }
                            });*/

                            //custom_final_price
                            /*if (item.custom_final_price) {
                                //Set price to custom_final_price
                                if ((item.custom_final_price < item.price.AUD.default) && item.custom_final_price != 0) {
                                    item.customFinalPrice =  formatter.format(item.custom_final_price);
                                    item.discount = formatter.format(item.price.AUD.default - item.custom_final_price);
                                    item.hasCustomFinalPrice = true;
                                    item.hasNoCustomFinalPrice = false;
                                }
                                //Set price to default
                                //item.customFinalPrice =  formatter.format(item.price.AUD.default);
                                //item.hasCustomFinalPrice = false;
                                //item.hasNoCustomFinalPrice = true;
                            }*/

                            if ((item.price.AUD.default < item.wiser_price) || !item.wiser_price || item.wiser_price === null || item.wiser_price === undefined) {
                                //programmed_promotion_price
                                console.log(item.price.AUD.default_original_formated);
                                if (item.price.AUD.default_original_formated && item.price.AUD.default_original_formated !== "undefined") {
                                    var defaultOriginalPrice = item.price.AUD.default_original_formated;
                                    defaultOriginalPrice = Number(defaultOriginalPrice.replace("$", "").replace(",", ""));

                                    if (defaultOriginalPrice > item.price.AUD.default) {
                                        //Set price to custom_final_price
                                        var priceDiscount = defaultOriginalPrice - item.price.AUD.default;
                                        item.defaultOriginalPrice = item.price.AUD.default_original_formated;
                                        item.customFinalPrice =  formatter.format(item.price.AUD.default);
                                        item.discount = formatter.format(priceDiscount);
                                        item.hasCustomFinalPrice = true;
                                        item.hasNoCustomFinalPrice = false;
                                    }
                                }
                            } else {
                                //wiser_price
                                if (item.wiser_price) {

                                    var basePrice;

                                    //Set price to custom_final_price
                                    var defaultOriginalPrice = item.price.AUD.default_original_formated;

                                    if (defaultOriginalPrice) {
                                        defaultOriginalPrice = Number(defaultOriginalPrice.replace("$", "").replace(",", ""));
                                        basePrice = defaultOriginalPrice;
                                    } else {
                                        basePrice = item.price.AUD.default;
                                    }

                                    var wiserDiscount = basePrice - item.wiser_price;
                                    if ((item.wiser_price < item.price.AUD.default) && item.wiser_price != 0) {
                                        item.defaultOriginalPrice = item.price.AUD.default_original_formated;
                                        item.customFinalPrice =  formatter.format(item.wiser_price);
                                        item.discount = formatter.format(wiserDiscount);
                                        item.hasCustomFinalPrice = true;
                                        item.hasNoCustomFinalPrice = false;
                                    }
                                    //Set price to default
                                    //item.customFinalPrice =  formatter.format(item.price.AUD.default);
                                    //item.hasCustomFinalPrice = false;
                                    //item.hasNoCustomFinalPrice = true;
                                }
                            }

                            item.isDigiPrint = false;
                            item.isNotDigiPrint = true;

                            if (item.is_digiprint == "Yes") {
                                item.digiPrintUrl = item.digiprint_customlink;
                                item.isDigiPrint = true;
                                item.isNotDigiPrint = false;
                            }

                            //console.log(algoliaBundle.Hogan.parse(algoliaBundle.Hogan.scan("{{sku}}")));
                            //console.log(algoliaBundle.Hogan.parse("{{sku}}"));
                            item.algoliaConfig = window.algoliaConfig;
                            return item;
                        });
                    },
                };

                /**
                 * pagination
                 * Docs: https://www.algolia.com/doc/api-reference/widgets/pagination/js/
                 **/
                allWidgetConfiguration.pagination = {
                    container   : '#instant-search-pagination-container',
                    showFirst   : false,
                    showLast    : false,
                    showNext    : true,
                    showPrevious: true,
                    totalPages  : 1000,
                    templates   : {
                        previous: algoliaConfig.translations.previousPage,
                        next    : algoliaConfig.translations.nextPage,
                    },
                };

                delete allWidgetConfiguration.infiniteHits;
            }

            /**
             * Here are specified custom attributes widgets which require special code to run properly
             * Custom widgets can be added to this object like [attribute]: function(facet, templates)
             * Function must return an array [<widget name>: string, <widget options>: object]
             **/
            const customAttributeFacet = {
                categories: function (facet, templates) {
                    const hierarchical_levels = [];
                    for (let l = 0; l < 10; l++) {
                        hierarchical_levels.push('categories.level' + l.toString());
                    }

                    const hierarchicalMenuParams = {
                        container      : facet.wrapper.appendChild(
                            algoliaCommon.createISWidgetContainer(facet.attribute)
                        ),
                        attributes     : hierarchical_levels,
                        separator      : algoliaConfig.instant.categorySeparator,
                        templates      : templates,
                        showParentLevel: true,
                        limit          : algoliaConfig.maxValuesPerFacet,
                        sortBy         : ['name:asc'],
                        transformItems(items) {
                            return algoliaConfig.isCategoryPage
                                ? items.map((item) => {
                                    return {
                                        ...item,
                                        categoryUrl: algoliaConfig.instant
                                            .isCategoryNavigationEnabled
                                            ? algoliaConfig.request.childCategories[item.value]['url']
                                            : '',
                                    };
                                })
                                : items;
                        },
                    };

                    if (algoliaConfig.isCategoryPage) {
                        hierarchicalMenuParams.rootPath = algoliaConfig.request.path;
                    }

                    hierarchicalMenuParams.templates.item =
                        '<a class="{{cssClasses.link}} {{#isRefined}}{{cssClasses.link}}--selected{{/isRefined}}" href="{{categoryUrl}}"><span class="{{cssClasses.label}}">{{label}}</span>' +
                        ' ' +
                        '<span class="{{cssClasses.count}}">{{#helpers.formatNumber}}{{count}}{{/helpers.formatNumber}}</span>' +
                        '</a>';
                    hierarchicalMenuParams.panelOptions = {
                        templates: {
                            header:
                                '<div class="name">' +
                                (facet.label ? facet.label : facet.attribute) +
                                '</div>',
                        },
                        hidden   : function ({items}) {
                            return !items.length;
                        },
                    };

                    return ['hierarchicalMenu', hierarchicalMenuParams];
                },
            };

            /** Add all facet widgets to instantsearch object **/
            var wrapper = document.getElementById('instant-search-facets-container');
            $.each(algoliaConfig.facets, (i, facet) => {
                if (facet.attribute.indexOf('price') !== -1)
                    facet.attribute = facet.attribute + algoliaConfig.priceKey;

                facet.wrapper = wrapper;

                var templates = {
                    item: $('#refinements-lists-item-template').html(),
                };

                var widgetInfo =
                    customAttributeFacet[facet.attribute] !== undefined
                        ? customAttributeFacet[facet.attribute](facet, templates)
                        : this.getFacetWidget(facet, templates);

                var widgetType = widgetInfo[0],
                    widgetConfig = widgetInfo[1];

                if (typeof allWidgetConfiguration[widgetType] === 'undefined') {
                    allWidgetConfiguration[widgetType] = [widgetConfig];
                } else {
                    allWidgetConfiguration[widgetType].push(widgetConfig);
                }
            });

            if (algoliaConfig.analytics.enabled) {
                if (typeof algoliaAnalyticsPushFunction !== 'function') {
                    var algoliaAnalyticsPushFunction = function (
                        formattedParameters,
                        state,
                        results
                    ) {
                        var trackedUrl =
                            '/catalogsearch/result/?q=' +
                            state.query +
                            '&' +
                            formattedParameters +
                            '&numberOfHits=' +
                            results.nbHits;

                        // Universal Analytics
                        if (typeof window.ga !== 'undefined') {
                            window.ga('set', 'page', trackedUrl);
                            window.ga('send', 'pageView');
                        }
                    };
                }

                allWidgetConfiguration['analytics'] = {
                    pushFunction          : algoliaAnalyticsPushFunction,
                    delay                 : algoliaConfig.analytics.delay,
                    triggerOnUIInteraction: algoliaConfig.analytics.triggerOnUiInteraction,
                    pushInitialSearch     : algoliaConfig.analytics.pushInitialSearch,
                };
            }

            allWidgetConfiguration = algoliaCommon.triggerHooks(
                'beforeWidgetInitialization',
                allWidgetConfiguration,
                mockAlgoliaBundle
            );

            $.each(allWidgetConfiguration, (widgetType, widgetConfig) => {
                if (Array.isArray(widgetConfig) === true) {
                    $.each(widgetConfig, (i, widgetConfig) => {
                        this.addWidget(search, widgetType, widgetConfig);
                    });
                } else {
                    this.addWidget(search, widgetType, widgetConfig);
                }
            });

            // Capture active redirect URL with IS facet params for add to cart from PLP
            if (algoliaConfig.instant.isAddToCartEnabled) {
                search.on('render', () => {
                    const cartForms = document.querySelectorAll(
                        '[data-role="tocart-form"]'
                    );
                    cartForms.forEach((form, i) => {
                        form.addEventListener('submit', (e) => {
                            const url = `${algoliaConfig.request.url}${window.location.search}`;
                            e.target.elements[
                                algoliaConfig.instant.addToCartParams.redirectUrlParam
                                ].value = algoliaBase64.mageEncode(url);
                        });
                    });
                });
            }

            this.startInstantSearch(search, mockAlgoliaBundle);

            this.addMobileRefinementsToggle();
        },

        /**
         * @deprecated - these hooks will be removed in a future version
         */
        invokeLegacyHooks() {
            if (typeof algoliaHookBeforeInstantsearchInit === 'function') {
                algoliaCommon.registerHook(
                    'beforeInstantsearchInit',
                    algoliaHookBeforeInstantsearchInit
                );
            }

            if (typeof algoliaHookBeforeWidgetInitialization === 'function') {
                algoliaCommon.registerHook(
                    'beforeWidgetInitialization',
                    algoliaHookBeforeWidgetInitialization
                );
            }

            if (typeof algoliaHookBeforeInstantsearchStart === 'function') {
                algoliaCommon.registerHook(
                    'beforeInstantsearchStart',
                    algoliaHookBeforeInstantsearchStart
                );
            }

            if (typeof algoliaHookAfterInstantsearchStart === 'function') {
                algoliaCommon.registerHook(
                    'afterInstantsearchStart',
                    algoliaHookAfterInstantsearchStart
                );
            }
        },

        /**
         * Pre-flight checks
         *
         * @returns {boolean} Returns true if InstantSearch is good to go
         */
        checkInstantSearchEnablement() {
            if (
                typeof algoliaConfig === 'undefined' ||
                !algoliaConfig.instant.enabled ||
                !algoliaConfig.isSearchPage
            ) {
                return false;
            }

            if (!$(algoliaConfig.instant.selector).length) {
                throw new Error(
                    `[Algolia] Invalid instant-search selector: ${algoliaConfig.instant.selector}`
                );
            }

            if (
                algoliaConfig.autocomplete.enabled &&
                $(algoliaConfig.instant.selector).find(
                    algoliaConfig.autocomplete.selector
                ).length
            ) {
                throw new Error(
                    `[Algolia] You can't have a search input matching "${algoliaConfig.autocomplete.selector}" ` +
                    `inside your instant selector "${algoliaConfig.instant.selector}"`
                );
            }

            return true;
        },

        /**
         * Handle nested Autocomplete (legacy)
         * @returns {boolean}
         */
        findAutocomplete() {
            if (algoliaConfig.autocomplete.enabled) {
                const $nestedAC = $(algoliaConfig.instant.selector).find('#algolia-autocomplete-container');
                if ($nestedAC.length) {
                    $nestedAC.remove();
                    return true;
                }
            }
            return false;
        },

        /**
         * Build wrapper DOM object to contain InstantSearch
         * @param templateProcessor
         */
        setupWrapper(templateProcessor) {
            const div = document.createElement('div');
            $(div).addClass('algolia-instant-results-wrapper');

            $(algoliaConfig.instant.selector).addClass(
                'algolia-instant-replaced-content'
            );
            $(algoliaConfig.instant.selector).wrap(div);

            $('.algolia-instant-results-wrapper').append(
                '<div class="algolia-instant-selector-results" style="display: none;"></div>'
            );

            const template = $('#instant_wrapper_template').html();
            const templateVars = {
                second_bar      : algoliaConfig.instant.enabled,
                findAutocomplete: this.findAutocomplete(),
                config          : algoliaConfig.instant,
                translations    : algoliaConfig.translations,
            };

            const wrapperHtml = templateProcessor.process(template, templateVars);
            $('.algolia-instant-selector-results').html(wrapperHtml).show();
        },

        /**
         * @returns {string[]}
         */
        getRuleContexts() {
            const ruleContexts = ['magento_filters', '']; // Empty context to keep BC for already create rules in dashboard
            if (algoliaConfig.request.categoryId.length) {
                ruleContexts.push('magento-category-' + algoliaConfig.request.categoryId);
            }

            if (algoliaConfig.request.landingPageId.length) {
                ruleContexts.push(
                    'magento-landingpage-' + algoliaConfig.request.landingPageId
                );
            }
            return ruleContexts;
        },

        /**
         * Get raw search parameters for configure widget
         * See https://www.algolia.com/doc/api-reference/widgets/configure/js/
         * @returns {*[]}
         */
        getSearchParameters() {
            const searchParameters = {
                hitsPerPage : algoliaConfig.hitsPerPage,
                ruleContexts: this.getRuleContexts()
            };

            if (
                algoliaConfig.request.path.length &&
                window.location.hash.indexOf('categories.level0') === -1
            ) {
                if (!algoliaConfig.areCategoriesInFacets) {
                    searchParameters['facetsRefinements'] = {};
                    searchParameters['facetsRefinements'][
                        'categories.level' + algoliaConfig.request.level
                    ] = [algoliaConfig.request.path];
                }
            }

            if (
                algoliaConfig.instant.isVisualMerchEnabled &&
                algoliaConfig.isCategoryPage
            ) {
                searchParameters.filters = `${
                    algoliaConfig.instant.categoryPageIdAttribute
                }:"${algoliaConfig.request.path.replace(/"/g, '\\"')}"`;
            }

            return searchParameters;
        },

        /**
         * @returns {string}
         */
        getAlgoliaAgent() {
            return 'Magento2 integration (' + algoliaConfig.extensionVersion + ')';
        },

        /**
         * Setup attributes for current refinements widget
         * @returns {*[]}
         */
        getCurrentRefinementsAttributes() {
            const attributes = [];
            $.each(algoliaConfig.facets, (i, facet) => {
                let name = facet.attribute;

                if (name === 'categories') {
                    name = 'categories.level0';
                }

                if (name === 'price') {
                    name = facet.attribute + algoliaConfig.priceKey;
                }

                attributes.push({
                    name : name,
                    label: facet.label ? facet.label : facet.attribute,
                });
            });
            return attributes;
        },

        startInstantSearch(search, mockAlgoliaBundle) {
            if (this.isStarted) {
                return;
            }
            search = algoliaCommon.triggerHooks(
                'beforeInstantsearchStart',
                search,
                mockAlgoliaBundle
            );
            search.start();
            search = algoliaCommon.triggerHooks(
                'afterInstantsearchStart',
                search,
                mockAlgoliaBundle
            );
            this.isStarted = true;
        },

        getFacetWidget(facet, templates) {
            var panelOptions = {
                templates: {
                    header:
                        '<div class="name">' +
                        (facet.label ? facet.label : facet.attribute) +
                        '</div>',
                },
                hidden: (options) => {
                    if (
                        options.results.nbPages <= 1 &&
                        algoliaConfig.instant.hidePagination === true
                    ) {
                        document.getElementById(
                            'instant-search-pagination-container'
                        ).style.display = 'none';
                    } else {
                        document.getElementById(
                            'instant-search-pagination-container'
                        ).style.display = 'block';
                    }
                    if (!options.results) return true;
                    switch (facet.type) {
                        case 'conjunctive':
                            var facetsNames = options.results.facets.map(function (f) {
                                return f.name;
                            });
                            return facetsNames.indexOf(facet.attribute) === -1;
                        case 'disjunctive':
                            var disjunctiveFacetsNames =
                                options.results.disjunctiveFacets.map(function (f) {
                                    return f.name;
                                });
                            return disjunctiveFacetsNames.indexOf(facet.attribute) === -1;
                        default:
                            return false;
                    }
                },
            };
            if (facet.type === 'priceRanges') {
                delete templates.item;

                return [
                    'rangeInput',
                    {
                        container   : facet.wrapper.appendChild(
                            algoliaCommon.createISWidgetContainer(facet.attribute)
                        ),
                        attribute   : facet.attribute,
                        templates   : $.extend(
                            {
                                separatorText: algoliaConfig.translations.to,
                                submitText   : algoliaConfig.translations.go,
                            },
                            templates
                        ),
                        cssClasses  : {
                            root: 'conjunctive',
                        },
                        panelOptions: panelOptions,
                    },
                ];
            }

            if (facet.type === 'conjunctive') {
                var refinementListOptions = {
                    container   : facet.wrapper.appendChild(
                        algoliaCommon.createISWidgetContainer(facet.attribute)
                    ),
                    attribute   : facet.attribute,
                    limit       : algoliaConfig.maxValuesPerFacet,
                    operator    : 'and',
                    templates   : templates,
                    sortBy      : ['count:desc', 'name:asc'],
                    cssClasses  : {
                        root: 'conjunctive',
                    },
                    panelOptions: panelOptions,
                };

                refinementListOptions = this.addSearchForFacetValues(
                    facet,
                    refinementListOptions
                );

                return ['refinementList', refinementListOptions];
            }

            if (facet.type === 'disjunctive') {
                var refinementListOptions = {
                    container   : facet.wrapper.appendChild(
                        algoliaCommon.createISWidgetContainer(facet.attribute)
                    ),
                    attribute   : facet.attribute,
                    limit       : algoliaConfig.maxValuesPerFacet,
                    operator    : 'or',
                    templates   : templates,
                    sortBy      : ['count:desc', 'name:asc'],
                    panelOptions: panelOptions,
                    cssClasses  : {
                        root: 'disjunctive',
                    },
                };

                refinementListOptions = this.addSearchForFacetValues(
                    facet,
                    refinementListOptions
                );

                return ['refinementList', refinementListOptions];
            }

            if (facet.type === 'slider') {
                delete templates.item;

                return [
                    'rangeSlider',
                    {
                        container   : facet.wrapper.appendChild(
                            algoliaCommon.createISWidgetContainer(facet.attribute)
                        ),
                        attribute   : facet.attribute,
                        templates   : templates,
                        pips        : false,
                        panelOptions: panelOptions,
                        tooltips    : {
                            format: function (formattedValue) {
                                return facet.attribute.match(/price/) === null
                                    ? parseInt(formattedValue)
                                    : priceUtils.formatPrice(
                                        formattedValue,
                                        algoliaConfig.priceFormat
                                    );
                            },
                        },
                    },
                ];
            }
        },

        addWidget(search, type, config) {

            if (type === 'custom') {
                search.addWidgets([config]);
                return;
            }
            var widget = instantsearch.widgets[type];
            if (config.panelOptions) {
                widget = instantsearch.widgets.panel(config.panelOptions)(
                    widget
                );
                delete config.panelOptions;
            }
            if (type === 'rangeSlider' && config.attribute.indexOf('price.') < 0) {
                config.panelOptions = {
                    hidden(options) {
                        return options.range.min === 0 && options.range.max === 0;
                    },
                };
                widget = instantsearch.widgets.panel(config.panelOptions)(
                    widget
                );
                delete config.panelOptions;
            }

            search.addWidgets([widget(config)]);

            function histogramWidget({ container, attribute, buckets = 20 }) {
                if (typeof container === 'string') {
                    container = document.querySelector(container);
                }
                if (!container) {
                    console.warn(`Histogram container not found.`);
                    return { render() {} }
                }

                return {
                    render({ results, helper }) {
                        const stats = results.getFacetStats(attribute);
                        if (!stats) return;

                        const min = stats.min;
                        const max = stats.max;

                        console.log("min", min);
                        console.log("max", max);

                        if (min === max) return;

                        const step = (max - min) / buckets;
                        const counts = Array(buckets).fill(0);

                        // ⚠️ Don’t loop through all hits (slow on large catalogs).
                        // Instead, use facet counts from Algolia:
                        const facetValues = results.getFacetValues(attribute, { sortBy: ['name:asc'] });
                        facetValues.forEach(fv => {
                            const price = parseFloat(fv.name)
                            const index = Math.min(Math.floor((price - min) / step), buckets - 1)
                            counts[index] += fv.count
                        })

                        const maxCount = Math.max(...counts)

                        container.innerHTML = `<div class="histogram">
                          ${counts.map((c, i) => {
                            const from = Math.floor(min + i * step)
                            const to = Math.floor(from + step)
                            return `<div class="bar"
                                title="${from} – ${to} (${c} products)"
                                data-from="${from}" data-to="${to}"
                                style="height:${(c / maxCount) * 100}%"></div>`
                            }).join('')}
                        </div>`;

                        // Optional: make bars clickable
                        container.querySelectorAll('.bar').forEach(bar => {
                            bar.addEventListener('click', () => {
                                helper.removeNumericRefinement(attribute)
                                helper.addNumericRefinement(attribute, '>=', +bar.dataset.from)
                                helper.addNumericRefinement(attribute, '<=', +bar.dataset.to)
                                helper.search()
                            })
                        })
                    }
                }
            }


            window.addEventListener('load', function () {

                // Add widgets (these get picked up by the already-started search)
                search.addWidgets([
                  instantsearch.widgets.hitsPerPage({
                    container: '#hits-per-page',
                    items: [
                      { label: '4', value: 4 },
                      { label: '8', value: 8 },
                      { label: '16', value: 16, default: true },
                      { label: '32', value: 32 },
                    ],
                  }),
                ]);

                // Function to recolor bars
                function updateHistogramColors(min, max) {
                    document.querySelectorAll('#price-histogram .bar').forEach(bar => {
                        const from = parseFloat(bar.dataset.from);
                        const to = parseFloat(bar.dataset.to);
                        if (to >= min && from <= max) {
                            bar.style.background = 'linear-gradient(180deg, #ffe9e4 0%, #ff9a85 100%)'; // active
                        } else {
                            bar.style.background = 'linear-gradient(180deg, #FFF 0%, #E6E6E6 25%)'; // inactive
                        }
                    });
                }

                search.on('render', () => {
                    const helper = search.helper;
                    const attribute = 'price.AUD.default';
                    const slider = document.querySelector('.ais-RangeSlider .rheostat');

                    function setSliderValues(min, max) {
                        if (!slider) return;
                        console.log("setSliderValues", min, max);
                        helper.removeNumericRefinement(attribute);
                        helper.addNumericRefinement(attribute, '>=', min);
                        helper.addNumericRefinement(attribute, '<=', max);
                        helper.search();
                    }

                    function getSliderValues() {
                      const handles = slider ? slider.querySelectorAll('.rheostat-handle') : []
                        if (handles.length < 2) return { min: 0, max: 0 }
                        return {
                            min: parseFloat(handles[0].getAttribute('aria-valuenow')),
                            max: parseFloat(handles[1].getAttribute('aria-valuenow'))
                        }
                    }

                    const priceSlider = document.querySelector('.is-widget-container-price_AUD_default');
                    const aisSlider = document.querySelector('.ais-RangeSlider');

                    // Insert histogram container before slider (only once)
                    if (priceSlider && aisSlider && !document.querySelector('#price-histogram')) {
                        const histo = document.createElement('div');
                        histo.id = 'price-histogram';
                        aisSlider.before(histo);
                    }

                    if (document.querySelector('#price-histogram') && !search.__histogramAdded) {
                        search.addWidgets([
                            histogramWidget({
                                container: '#price-histogram',
                                attribute: attribute,
                                buckets: 20,
                            }),
                        ]);
                        search.__histogramAdded = true;
                    }

                    // Insert price input boxes above slider (only once)
                    if (priceSlider && !document.querySelector('#price-inputs')) {
                        const inputWrapper = document.createElement('div');
                        inputWrapper.id = 'price-inputs';
                        inputWrapper.innerHTML = `
                          <div class="price-input-wrapper">
                            <span class="currency">$</span>
                            <input type="text" id="min-price" placeholder="Min" inputmode="numeric" />
                          </div>
                          <div class="price-input-wrapper">
                            <span class="currency">$</span>
                            <input type="text" id="max-price" placeholder="Max" inputmode="numeric" />
                          </div>
                        `;
                        aisSlider.after(inputWrapper);

                        const formatNumber = (val) => {
                            if (!val) return ''
                            const num = parseInt(val.replace(/,/g, ''), 10)
                            return isNaN(num) ? '' : num.toLocaleString()
                        }

                        const getNumericValue = (el) => {
                            const raw = el.value.replace(/,/g, '')
                            return Number(raw) || 0
                        }

                        const minInput = document.getElementById('min-price');
                        const maxInput = document.getElementById('max-price');

                        const handleInput = (inputEl, isMin) => {
                            // Reformat as user types
                            const caretPos = inputEl.selectionStart
                            const formatted = formatNumber(inputEl.value)
                            inputEl.value = formatted

                            // Update slider
                            const { min, max } = getSliderValues()
                            const newVal = getNumericValue(inputEl)
                            if (isMin) {
                                setSliderValues(newVal, max)
                            } else {
                                setSliderValues(min, newVal)
                            }
                        }

                        minInput.addEventListener('input', () => handleInput(minInput, true));
                        maxInput.addEventListener('input', () => handleInput(maxInput, false));
                    }

                    // Grab your input boxes
                    const minInput = document.querySelector('#min-price');
                    const maxInput = document.querySelector('#max-price');

                    function syncInputs() {
                        const { min, max } = getSliderValues()
                        if (min && max) {
                            if (minInput) minInput.value = min
                            if (maxInput) maxInput.value = max
                            updateHistogramColors(min, max)
                        }
                    }

                    // Sync inputs now + whenever slider changes
                    syncInputs()
                    if (slider && !slider.dataset.synced) {
                        slider.addEventListener('mouseup', syncInputs)
                        slider.addEventListener('keyup', syncInputs)
                        slider.dataset.synced = "true"
                    }
                })


                // ❌ No search.start() here → avoids double start error

                function clampHandles() {
                    const track = document.querySelector('.rheostat-background');
                    const handles = document.querySelectorAll('.rheostat-handle');

                    if (track && handles.length === 2) {
                        const trackWidth = track.offsetWidth;

                        const minAttr = parseFloat(handles[0].getAttribute('aria-valuemin'));
                        const maxAttr = parseFloat(handles[1].getAttribute('aria-valuemax'));

                        // convert 10px margins into slider values
                        const marginValue = (10 / trackWidth) * (maxAttr - minAttr);

                        // current values
                        let minNow = parseFloat(handles[0].getAttribute('aria-valuenow'));
                        let maxNow = parseFloat(handles[1].getAttribute('aria-valuenow'));

                        // clamp
                        if (minNow < minAttr + marginValue) {
                            minNow = minAttr + marginValue;
                        }
                        if (maxNow > maxAttr - marginValue) {
                            maxNow = maxAttr - marginValue;
                        }

                        // Apply clamped values back to the slider
                        // Rheostat exposes `onValuesUpdated` and `onChange` events, but since InstantSearch controls it,
                        // we can dispatch directly:
                        const event = new CustomEvent('change', {
                            detail: [minNow, maxNow],
                        });
                        document.querySelector('.ais-RangeSlider').dispatchEvent(event);
                    }
                }

                // Run clamp every time the slider updates


                function attachClamp() {
                    document.querySelector('.rheostat').addEventListener('mousemove', clampHandles);
                    document.querySelector('.rheostat').addEventListener('mouseup', clampHandles);
                }

                // run after InstantSearch render
                search.on('render', attachClamp);

            })

        },

        addSearchForFacetValues(facet, options) {
            if (facet.searchable === '1') {
                options.searchable = true;
                options.searchableIsAlwaysActive = false;
                options.searchablePlaceholder =
                    algoliaConfig.translations.searchForFacetValuesPlaceholder;
                options.templates = options.templates || {};
                options.templates.searchableNoResults =
                    '<div class="sffv-no-results">' +
                    algoliaConfig.translations.noResults +
                    '</div>';
            }

            return options;
        },

        addMobileRefinementsToggle() {
            $('#refine-toggle').on('click', function () {
                $('#instant-search-facets-container').toggleClass('hidden-sm').toggleClass('hidden-xs');
                /*if ($(this).html().trim()[0] === '+')
                    $(this).html('- ' + algoliaConfig.translations.refine);
                else
                    $(this).html('+ ' + algoliaConfig.translations.refine);*/
            });
        },

        /**
         * @deprecated algoliaBundle is going away!
         * This mock only includes libraries available to this module
         * The following have been removed:
         *  - Hogan
         *  - algoliasearchHelper
         *  - autocomplete
         *  - createAlgoliaInsightsPlugin
         *  - createLocalStorageRecentSearchesPlugin
         *  - createQuerySuggestionsPlugin
         *  - getAlgoliaResults
         * However if you've used or require any of these additional libs in your customizations,
         * you can either augment this mock as you need or include the global dependency in your module
         * and make it available to your hook.
         */
        mockAlgoliaBundle() {
            return {
                $,
                algoliasearch,
                instantsearch
            }
        }
    });

});

  const loader = document.getElementById('plp-custom-loader');
  if (loader) loader.style.display = 'block' // Show immediately

  // ─── PLP Layout Repositioning ────────────────────────────────────────────────
  // Drop-in replacement for the IIFE block at the bottom of your instantsearch JS.
  // Fixes: premature calls, observer-loops, loader never hiding on non-empty results.
  // ─────────────────────────────────────────────────────────────────────────────

  ;(function () {

    const SEARCH_BAR_ID        = '#instant-search-bar';
    const FACETS_CONTAINER_ID  = '#instant-search-facets-container';
    const RECHECK_DELAY        = 200;
    const MAX_RETRIES          = 300;

    const loader   = document.getElementById('plp-custom-loader');
    const isMobile = () => window.innerWidth <= 768;

    // ── Loader helpers ────────────────────────────────────────────────────────

    if (loader) loader.style.display = 'block';

    let loaderHidden = false;
    function hideLoader() {
      if (loaderHidden || !loader) return;
      loaderHidden = true;
      loader.style.display = 'none';
    }

    // ── Absolute loader failsafe ──────────────────────────────────────────────
    // Hides the loader no matter what after 3 s so the page is never blocked.
    setTimeout(hideLoader, 3000);

    // ── Search-bar visibility helpers ─────────────────────────────────────────

    function hideSearchBar() {
      const sb = document.querySelector(SEARCH_BAR_ID);
      if (sb) sb.style.display = 'none';
    }
    function showSearchBar() {
      const sb = document.querySelector(SEARCH_BAR_ID);
      if (sb) sb.style.display = '';
    }

    // ── moveAndInsertSearchBar ────────────────────────────────────────────────
    // Moves #instant-search-bar into #instant-search-facets-container.
    // Returns true only when the move is confirmed.

    function moveAndInsertSearchBar() {
      const searchBar       = document.querySelector(SEARCH_BAR_ID);
      const facetsContainer = document.querySelector(FACETS_CONTAINER_ID);
      if (!searchBar || !facetsContainer) return false;

      if (searchBar.parentElement !== facetsContainer) {
        facetsContainer.appendChild(searchBar);
      }

      if (!searchBar.querySelector('.search-within-label')) {
        const label       = document.createElement('span');
        label.className   = 'search-within-label';
        label.textContent = 'Search Within Results';
        searchBar.insertBefore(label, searchBar.firstChild);
      }

      return searchBar.parentElement === facetsContainer;
    }

    // ── moveElements ──────────────────────────────────────────────────────────
    // Repositions layout elements differently for mobile vs desktop.
    // Uses a "dirty" guard so the MutationObserver doesn't loop on its own moves.

    let moveBusy = false;

    function moveElements() {
      if (moveBusy) return;
      moveBusy = true;

      try {
        const mobile = isMobile();

        const sel = (q) => document.querySelector(q);

        const infos           = sel('.algolia-infos');
        const refineToggle    = sel('#refine-toggle');
        const customRefinement= sel('.algolia-custom-refinement');
        const hitsPerPage     = sel('.hits-per-page-container');
        const pagination      = sel('#instant-search-pagination-container');
        const viewToggle      = sel('.ais-ViewToggle');
        const stats           = sel('#algolia-stats');
        const facets          = sel(FACETS_CONTAINER_ID);
        const leftContainer   = sel('#algolia-left-container');

        if (!facets || !leftContainer) return;

        // ── stats ──
        if (stats) {
          if (mobile) {
            if (stats.nextElementSibling !== leftContainer) {
              leftContainer.parentNode.insertBefore(stats, leftContainer);
            }
          } else {
            if (infos && stats.parentElement !== infos) {
              infos.insertBefore(stats, infos.firstChild);
            }
          }
        }

        // ── infos bar ──
        if (infos && refineToggle && customRefinement) {
          if (mobile) {
            if (infos.parentElement !== refineToggle.parentElement ||
                infos.previousElementSibling !== refineToggle) {
              refineToggle.parentNode.insertBefore(infos, refineToggle.nextElementSibling);
            }
          } else {
            if (infos.nextElementSibling !== customRefinement) {
              customRefinement.parentNode.insertBefore(infos, customRefinement);
            }
          }
        }

        // ── hits per page ──
        if (hitsPerPage) {
          if (mobile) {
            if (pagination && hitsPerPage.nextElementSibling !== pagination) {
              pagination.parentNode.insertBefore(hitsPerPage, pagination);
            }
          } else {
            if (viewToggle && hitsPerPage.nextElementSibling !== viewToggle) {
              viewToggle.parentNode.insertBefore(hitsPerPage, viewToggle);
            }
          }
        }

        // ── facets panel ──
        if (mobile) {
          if (facets.previousElementSibling !== leftContainer) {
            leftContainer.parentNode.insertBefore(facets, leftContainer.nextElementSibling);
          }
        } else {
          if (facets.parentElement !== leftContainer) {
            leftContainer.appendChild(facets);
          }
        }

      } finally {
        // Release the guard on next tick so legitimate external mutations
        // (e.g. InstantSearch adding a widget) can still re-trigger a move.
        setTimeout(() => { moveBusy = false; }, 0);
      }
    }

    // ── ensureSearchBoxVisible ────────────────────────────────────────────────

    function ensureSearchBoxVisible() {
      const sb = document.querySelector('.ais-SearchBox');
      if (sb) { sb.style.display = 'block'; return; }

      const obs = new MutationObserver((_, o) => {
        const found = document.querySelector('.ais-SearchBox');
        if (found) { found.style.display = 'block'; o.disconnect(); }
      });
      obs.observe(document.body, { childList: true, subtree: true });
    }

    // ── clearInlineStyles ─────────────────────────────────────────────────────
    // Removes any leftover inline display:none / display:block that we set
    // during the loading phase, so CSS takes full control again.

    function clearInlineStyles() {
      const elements = [
        ...document.querySelectorAll('.algolia-instant-selector-results'),
        ...document.querySelectorAll('.hits-per-page-container'),
        ...document.querySelectorAll('.ais-ViewToggle'),
      ];
      ['refine-toggle', 'algolia-stats', 'algolia-sorts'].forEach(id => {
        const el = document.getElementById(id);
        if (el) elements.push(el);
      });
      elements.forEach(el => el.removeAttribute('style'));

      ensureSearchBoxVisible();
      hideLoader();
    }

    // ── watchForResults ───────────────────────────────────────────────────────
    // Hides the loader once InstantSearch has rendered its first result set
    // (whether there are hits or not).  Covers both empty AND non-empty cases.

    function watchForResults() {
      // InstantSearch adds one of these two classes after its first render:
      //   .ais-Hits-list   → results exist
      //   .ais-Hits--empty → no results
      const READY_SELECTORS = [
        '#instant-search-results-container .ais-Hits-list',
        '#instant-search-results-container .ais-Hits--empty',
        '#instant-search-results-container .ais-InfiniteHits-list',
        '#instant-search-results-container .ais-InfiniteHits--empty',
      ].join(', ');

      const checkReady = () => document.querySelector(READY_SELECTORS);

      if (checkReady()) { clearInlineStyles(); return; }

      const obs = new MutationObserver(() => {
        if (checkReady()) {
          clearInlineStyles();
          obs.disconnect();
        }
      });
      obs.observe(document.body, { childList: true, subtree: true });
    }

    // ── startRepositionWatcher ────────────────────────────────────────────────
    // Polls until #instant-search-bar is successfully moved, then hands off
    // to a MutationObserver for ongoing maintenance.

    function startRepositionWatcher() {
      hideSearchBar();

      let retries         = 0;
      let observerStarted = false;

      function attempt() {
        moveElements();

        const placed = moveAndInsertSearchBar();
        retries++;

        if (placed) {
          showSearchBar();

          if (!observerStarted) {
            observerStarted = true;

            const obs = new MutationObserver(() => {
              moveElements();
              if (moveAndInsertSearchBar()) showSearchBar();
              else hideSearchBar();
            });
            // Watch subtree so late-rendered widgets are caught.
            // childList only — avoids firing on every attribute tweak.
            obs.observe(document.body, { childList: true, subtree: true });

            // Layout is stable — now watch for IS render completion
            watchForResults();
          }
        } else {
          hideSearchBar();
          if (retries < MAX_RETRIES) {
            setTimeout(attempt, RECHECK_DELAY);
          } else {
            console.warn('[PLP] Repositioning retries exceeded — showing search bar anyway.');
            showSearchBar();
            watchForResults();
          }
        }
      }

      attempt();
    }

    // ── View toggle (grid / list) ─────────────────────────────────────────────

    function initViewToggle() {
      const body          = document.body;
      const toggleButtons = document.querySelectorAll('.ais-ViewToggle-button');

      const updateProductClasses = () => {
        document.querySelectorAll('.pa-product').forEach(p => {
          p.classList.toggle('list-view-col', body.classList.contains('list-view'));
        });
      };

      // Wait for .pa-product elements (InstantSearch renders them asynchronously)
      const waitForProducts = () => {
        if (document.querySelectorAll('.pa-product').length) {
          updateProductClasses();
        } else {
          setTimeout(waitForProducts, 200);
        }
      };

      const savedView = localStorage.getItem('viewMode');
      if (savedView === 'list') {
        body.classList.add('list-view');
        document.querySelector('[data-view="list"]')?.classList.add('is-active');
      } else {
        body.classList.remove('list-view');
        document.querySelector('[data-view="grid"]')?.classList.add('is-active');
      }

      waitForProducts();

      toggleButtons.forEach(button => {
        button.addEventListener('click', () => {
          const view = button.getAttribute('data-view');
          toggleButtons.forEach(btn => btn.classList.remove('is-active'));
          button.classList.add('is-active');
          body.classList.toggle('list-view', view === 'list');
          localStorage.setItem('viewMode', view);
          updateProductClasses();
        });
      });
    }

    // ── boot ──────────────────────────────────────────────────────────────────

    function boot() {
      initViewToggle();
      startRepositionWatcher();

      let resizeTimer = null;
      window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(moveElements, 120);
      });
    }

    // Run after full page load so InstantSearch widgets have had a chance to mount
    if (document.readyState === 'complete') {
      boot();
    } else {
      window.addEventListener('load', boot);
    }

  })();
