## Related Product Info Settings

Option | Type | Default | Description
------ | ---- | ------- | -----------
filterable | boolean | true | Enable filter products by category
initialFilter | string | '' | Preselected filter
filterItems | string | '.related-filters > .item' | List of filter items
filterInfo | string | '.related-filters > .content' | List of filter items content (category description)
filterActiveState | string | '-active' | HTML class name of selected filter item 
gridId | string | 'related-grid' | Id of products grid
switchElement | string | '[data-role="related-details"]' | Switch listener
expandable | boolean | true | Enable expander of products
expanderElement | string | '[data-expander]' | Expander listener
expanderTemplate | string | ```'<div class="related-expander"><%= data.html %></div>'``` | Template of expander container
expanderHolder | string | '.related-expander' | Selector of expander container: main wrapper of 'expanderTemplate' 
scroll | boolean | false | Enable scroll 
scrollTo | string | 'details' | Scroll to expander ('details') or product ('item') 