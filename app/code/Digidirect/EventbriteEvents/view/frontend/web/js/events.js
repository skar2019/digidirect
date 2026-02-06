define(['jquery'], function ($) {
    'use strict';

    return function () {

        const PROXY_URL = '/digidirect-events/api/events';

        function safeText(t){ return t == null ? '' : String(t); }

        function createCard(event){
            const card = $('<article>', {class:'event-card'});
            const media = $('<div>', {class:'media'}).append(
                $('<img>', {src:event.logo?.url || 'https://via.placeholder.com/800x450'})
            );

            const content = $('<div>', {class:'content'});
            const title = $('<h3>', {text:safeText(event.name?.text)});
            const desc = $('<p>', {text:(event.description?.text || '').slice(0,260)});
            const link = $('<a>', {class:'learn-more',href:event.url,target:'_blank',text:'Learn More'});

            content.append(title,desc,link);
            card.append(media,content);
            return card;
        }

        function createCarouselItem(event){
            return $('<a>', {
                class:'carousel-item',
                href:event.url,
                target:'_blank'
            }).append(
                $('<img>', {src:event.logo?.url}),
                $('<div>', {class:'meta'}).append(
                    $('<h4>', {text:safeText(event.name?.text)})
                )
            );
        }

        async function fetchEvents(){

            const list = $('#event-list');
            const carousel = $('#past-carousel');

            try {

                let live = await fetch(PROXY_URL + '?status=live');
                live = await live.json();

                list.empty();

                (live.events || []).slice(0,5)
                    .forEach(ev => list.append(createCard(ev)));

                let past = await fetch(PROXY_URL + '?status=ended');
                past = await past.json();

                carousel.empty();

                (past.events || [])
                    .forEach(ev => carousel.append(createCarouselItem(ev)));

            }
            catch(e){
                console.error("Event load error", e);
            }
        }

        fetchEvents();
    };
});
