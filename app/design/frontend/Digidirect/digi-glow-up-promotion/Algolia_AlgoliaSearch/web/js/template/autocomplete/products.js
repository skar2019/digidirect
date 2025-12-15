define([], function () {
    return {

        ////////////////////
        //  Template API  //
        ////////////////////

        getNoResultHtml: function ({html}) {
            return html`<p>${algoliaConfig.translations.noResults}</p>`;
        },

        getHeaderHtml: function ({html}) {
            return html`<p>Top Selling Products</p>`;
        },

        getItemHtml: function ({item, components, html}) {

            var badge = '';

            //console.log("item", JSON.stringify(item));

            if (item['price'] !== undefined && item['price']['AUD'] !== undefined) {

                let defaultOriginalPrice = 0;
                let defaultOriginalPriceStr = item['price']['AUD']['default_original_formated'];

                if (defaultOriginalPriceStr) {
                    defaultOriginalPrice = Number(defaultOriginalPriceStr.replace("$", "").replace(",", ""));
                }

                //console.log("defaultOriginalPriceStr", defaultOriginalPriceStr);

                let basePrice = defaultOriginalPrice > item['price']['AUD']['default']
                    ? defaultOriginalPrice
                    : item['price']['AUD']['default'];

                let priceDiscount = 0;
                if (defaultOriginalPrice > item['price']['AUD']['default']) {
                    priceDiscount = defaultOriginalPrice - item['price']['AUD']['default'];
                }

                let wiserDiscount = 0;
                if (item['wiser_price']) {
                    wiserDiscount = basePrice - item['wiser_price'];
                }

                //console.log(item['name'], priceDiscount, wiserDiscount);

                if (priceDiscount > 0 || wiserDiscount > 0) {
                    badge = html`<div class="ribbon-container"><div class="ribbon-digideals black-friday"><span class="digi">Boxing Day</span> Sale</div></div>`;
                } else {
                    badge = html`<div class="ribbon-digideals always-hidden"><span class="digi">digi</span>Deals</div>`;
                }
            }

            let categoryIds = item['categoryIds'] || [];
            let digiSecondsIds = ["2564","2567","2570","2573"];
            let hasMatch = categoryIds.some(cat => digiSecondsIds.includes(cat));

            if (hasMatch) {
                badge = html`<div class="ribbon-digideals digiseconds"><span style="color: #FE4C25">digi</span>Seconds</div>`;
            }

            return html`<a class="algoliasearch-autocomplete-hit"
                           href="${item.url}"
                           data-objectId="${item.objectID}"
                           data-position="${item.position}"
                           data-index="${item.__autocomplete_indexName}"
                           data-queryId="${item.__autocomplete_queryID}">${badge}<div class="thumb"><img src="${item.image_url || ''}"/></div>
                <div class="info">
                    <div class="algoliasearch-autocomplete-name">
                        ${this.safeHighlight(components, item, "name")}
                    </div>
                    <div class="algoliasearch-autocomplete-category">
                        ${this.getColorHtml(item, components, html)}
                        ${this.getCategoriesHtml(item, components, html)}
                    </div>

                    ${this.getPricingHtml(item, html)}
                </div>
            </a>`;
        },

        getFooterHtml: function ({html, ...resultDetails}) {
            return html`<div id="autocomplete-products-footer">
                ${this.getFooterSearchLinks(html, resultDetails)}
            </div>`;
        },

        ////////////////////
        // Helper methods //
        ////////////////////

        getColorHtml: function(item, components, html) {
            const highlight = this.safeHighlight(components, item, "color");

            return highlight
                ? html`<span class="color">color: ${highlight}</span>`
                : "";
        },

        getCategoriesHtml: function(item, components, html) {
            const highlight = this.safeHighlight(components, item, "categories_without_path", false);

            return highlight
                ? html`<span>in ${highlight}</span>`
                : "";
        },

        getOriginalPriceHtml: (item, html, priceGroup) => {
            if (item['price'][algoliaConfig.currencyCode][priceGroup + '_original_formated'] == null) return "";

            return html`<span class="before_special"> ${item['price'][algoliaConfig.currencyCode][priceGroup + '_original_formated']} </span>`;
        },

        getTierPriceHtml: (item, html, priceGroup) => {
            if (item['price'][algoliaConfig.currencyCode][priceGroup + '_tier_formated'] == null) return "";

            return html`<span class="tier_price"> As low as <span class="tier_value">${item['price'][algoliaConfig.currencyCode][priceGroup + '_tier_formated']}</span></span>`;
        },

        getPricingHtml: function (item, html) {
            if (!item || !item.price || !item.price.AUD) return '';

            const priceGroup = algoliaConfig.priceGroup || 'default';
            const currencyCode = algoliaConfig.currencyCode || 'AUD';
            const priceAUD = item.price.AUD;

            const formatter = new Intl.NumberFormat('en-AU', {
              style: 'currency',
              currency: 'AUD',
              minimumFractionDigits: 2,
            });

            const parsePrice = (val) => {
              if (val === undefined || val === null) return 0;
              if (typeof val === 'number') return val;
              const n = Number(String(val).replace(/\$/g, '').replace(/,/g, '').trim());
              return isNaN(n) ? 0 : n;
            };

            const defaultPrice = Number(priceAUD.default) || 0;
            const originalPrice = Number(parsePrice(priceAUD.default_original_formatted || priceAUD.default_original_formated)) || 0;
            const basePrice = originalPrice > 0 ? originalPrice : defaultPrice;

            const wiserPrice =
              item.wiser_price === undefined ||
              item.wiser_price === null ||
              item.wiser_price === '' ||
              isNaN(Number(item.wiser_price))
                ? null
                : Number(item.wiser_price);

            // discounts
            const priceDiscount = originalPrice > defaultPrice ? originalPrice - defaultPrice : 0;
            const wiserDiscount = wiserPrice !== null && basePrice > wiserPrice ? basePrice - wiserPrice : 0;

            // effective price (used when no discount)
            const effectivePrice =
              wiserDiscount > 0
                ? wiserPrice
                : priceDiscount > 0
                ? defaultPrice
                : basePrice || defaultPrice;

            // 🧾 WISER DISCOUNT (priority)
            if (wiserDiscount > 0 && wiserPrice != 0) {
              return html`<div class="algoliasearch-autocomplete-price">
                <span class="before_price promotional">${formatter.format(originalPrice || defaultPrice)}</span>
                <span class="after_special custom_final_price this-is-wiser">${formatter.format(wiserPrice)}</span>
                <div class="discount">SAVE ${formatter.format(wiserDiscount)}</div>
              </div>`;
            }

            // 💰 PROMOTION DISCOUNT
            if (priceDiscount > 0) {
              return html`<div class="algoliasearch-autocomplete-price">
                <span class="before_price promotional">${formatter.format(originalPrice || defaultPrice)}</span>
                <span class="after_special custom_final_price this-is-promotion">${formatter.format(defaultPrice)}</span>
                <div class="discount">SAVE ${formatter.format(priceDiscount)}</div>
              </div>`;
            }

            // 🧾 NO DISCOUNT — show only final price
            const formatted =
              (item.price[currencyCode] && item.price[currencyCode][`${priceGroup}_formated`]) ||
              formatter.format(effectivePrice);

            return html`<div class="algoliasearch-autocomplete-price">
              <span class="after_special custom_final_price">${formatted}</span>
            </div>`;
        }
,

        getFooterSearchCategoryLinks: (html, resultDetails) => {
            if (resultDetails.allCategories == undefined || resultDetails.allCategories.length === 0) return "";

            return html ` ${algoliaConfig.translations.orIn}
                ${resultDetails.allCategories.map((list, index) =>
                    index === 0 ? html` <span><a href="${list.url}">${list.name}</a></span>` : html`, <span><a href="${list.url}">${list.name}</a></span>`
                )}
            `;
        },

        getFooterSearchLinks: function(html, resultDetails)  {
            if (resultDetails.nbHits === 0) return "";

            return html`${algoliaConfig.translations.seeIn} <span><a href="${resultDetails.allDepartmentsUrl}">${algoliaConfig.translations.allDepartments}</a></span> (${resultDetails.nbHits})
                ${this.getFooterSearchCategoryLinks(html, resultDetails)}
            `;
        },

        // TODO: Refactor to external lib
        safeHighlight: function(components, hit, attribute, strict = true) {
            const highlightResult = hit._highlightResult[attribute];

            if (!highlightResult) return '';

            if (strict
                &&
                (
                    (Array.isArray(highlightResult)) && !highlightResult.find(hit => hit.matchLevel !== 'none')
                    ||
                    highlightResult.value === ''
                )
            ) {
                return '';
            }

            try {
                return components.Highlight({ hit, attribute });
            } catch (e) {
                return '';
            }
        }

    };
});
