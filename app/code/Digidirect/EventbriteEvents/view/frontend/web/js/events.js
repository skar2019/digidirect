define(['jquery'], function($){
    'use strict';

    const PROXY_URL = '/digidirect-events/api/events';

    function safeText(t){ return t == null ? '' : String(t); }

    function createCard(event){
        const card = $('<article>', {class:'event-card'});
        const media = $('<div>', {class:'media'}).append(
            $('<img>', {src: event.logo?.url || 'https://via.placeholder.com/800x450?text=No+Image', alt: safeText(event.name?.text)})
        );

        const content = $('<div>', {class:'content'});
        const badge = $('<span>', {class:'event-badge', text:'🔴 Live Event'});
        const title = $('<h3>', {text: safeText(event.name?.text)});
        const desc = $('<p>', {text: (event.description?.text || '').slice(0, 260)});
        const link = $('<a>', {class:'learn-more', href:event.url, target:'_blank', html:'Learn More <i class="fa-solid fa-arrow-right"></i>'});

        content.append(badge, title, desc, link);
        card.append(media, content);
        return card;
    }

    function createCarouselItem(event){
        const item = $('<a>', {class:'carousel-item', href:event.url, target:'_blank'});
        const img = $('<img>', {src:event.logo?.url || 'https://via.placeholder.com/600x400?text=No+Image', alt:safeText(event.name?.text)});
        const meta = $('<div>', {class:'meta'});
        const title = $('<h4>', {text:safeText(event.name?.text)});
        const dateInfo = $('<p>', {class:'date-info'});
        try {
            const dt = event.start?.local ? new Date(event.start.local).toDateString() : '';
            dateInfo.html('<i class="fa-regular fa-calendar"></i> ' + dt);
        } catch(e){
            dateInfo.html('<i class="fa-regular fa-calendar"></i> Date unavailable');
        }
        meta.append(title, dateInfo);
        item.append(img, meta);
        return item;
    }

    async function fetchAllPastEvents(){
        let allEvents = [], continuation = '', hasMore = true;
        while(hasMore){
            let url = PROXY_URL + '?status=ended' + (continuation ? '&continuation=' + encodeURIComponent(continuation) : '');
            const res = await fetch(url);
            if(!res.ok) throw new Error(`HTTP error ${res.status}`);
            const data = await res.json();
            if(Array.isArray(data.events)) allEvents = allEvents.concat(data.events);
            if(data.pagination?.has_more_items && data.pagination?.continuation){
                continuation = data.pagination.continuation;
            } else hasMore = false;
        }
        return allEvents;
    }

    async function fetchEvents(){
        const list = $('#event-list');
        const carousel = $('#past-carousel');

        try{
            const liveRes = await fetch(PROXY_URL + '?status=live');
            if(!liveRes.ok) throw new Error(`HTTP error ${liveRes.status}`);
            const liveData = await liveRes.json();
            const liveEvents = Array.isArray(liveData.events) ? liveData.events : [];
            list.empty();
            if(liveEvents.length){
                liveEvents.slice(0,5).forEach(ev => list.append(createCard(ev)));
            } else list.html('<p class="muted">No live events found.</p>');

            carousel.html('<div class="loading-spinner"><div class="spinner"></div><p class="muted">Loading past events...</p></div>');
            const pastEvents = await fetchAllPastEvents();
            carousel.empty();
            if(pastEvents.length){
                pastEvents.sort((a,b)=>new Date(b.end.local)-new Date(a.end.local));
                pastEvents.forEach(ev=>carousel.append(createCarouselItem(ev)));
            } else carousel.html('<p class="muted">No past events found.</p>');

            initCarousel();
        } catch(e){
            console.error(e);
            list.html('<p class="muted">⚠️ Error loading events.</p>');
            carousel.html('<p class="muted">⚠️ Error loading past events.</p>');
        }
    }

    function initCarousel(){
        const track = $('#past-carousel');
        $('#nextBtn').click(()=> track.animate({scrollLeft:'+='+340},200));
        $('#prevBtn').click(()=> track.animate({scrollLeft:'-='+340},200));

        // drag support
        let isDown=false,startX,scrollLeft;
        track.on('mousedown touchstart', function(e){
            isDown = true;
            startX = e.pageX || e.originalEvent.touches[0].pageX;
            scrollLeft = this.scrollLeft;
        });
        track.on('mouseup mouseleave touchend',()=> isDown=false);
        track.on('mousemove touchmove', function(e){
            if(!isDown) return;
            e.preventDefault();
            const x = e.pageX || e.originalEvent.touches[0].pageX;
            const walk = (x-startX)*1.5;
            this.scrollLeft = scrollLeft - walk;
        });
    }

    $(document).ready(fetchEvents);
});
